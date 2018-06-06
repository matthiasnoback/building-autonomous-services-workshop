<?php
declare(strict_types=1);

namespace Sales;

use Common\Persistence\Database;
use Common\Render;
use Common\Stream\Stream;
use Common\Web\FlashMessage;
use Common\Web\HttpApi;
use Purchase\PurchaseOrderId;

final class SalesApplication
{
    public function __construct()
    {
        session_start();
    }

    public function createSalesOrderController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $salesOrderId = isset($_POST['salesOrderId'])
                ? SalesOrderId::fromString($_POST['salesOrderId'])
                : SalesOrderId::create();

            $salesOrder = new SalesOrder($salesOrderId, $_POST['productId'], (int)$_POST['quantity']);

            Database::persist($salesOrder);

            FlashMessage::add(FlashMessage::SUCCESS, 'Created sales order ' . $salesOrderId);

            // automatically create a purchase order for now
            $stockLevels = HttpApi::fetchDecodedJsonResponse('http://stock_web/stockLevels');
            $stockLevelForProduct = $stockLevels->{$salesOrder->productId()}->stockLevel;
            if ($stockLevelForProduct < $salesOrder->quantity()) {
                $purchaseOrderId = (string)PurchaseOrderId::create();
                $formData = [
                    'purchaseOrderId' => $purchaseOrderId,
                    'productId' => $salesOrder->productId(),
                    // we order only what we don't have in stock
                    'quantity' => $salesOrder->quantity() - $stockLevelForProduct
                ];

                HttpApi::postFormData(
                    'http://purchase_web/createPurchaseOrder',
                    $formData
                );

                FlashMessage::add(FlashMessage::SUCCESS, 'Also created purchase order ' . $purchaseOrderId);
            }

            header('Location: /listSalesOrders');
            exit;
        }

        /** @var Product[] $products */
        $products = Database::retrieveAll(Product::class);

        include __DIR__ . '/../Common/header.php';

        ?>
        <h1>Create a sales order</h1>
        <form action="/createSalesOrder" method="post">
            <div class="form-group">
                <label for="productId">
                    Product
                </label>
                <select name="productId" id="productId" class="form-control productId">
                    <?php
                    foreach ($products as $product) {
                        ?>
                        <option value="<?php echo $product->id(); ?>"><?php echo htmlspecialchars($product->name()); ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">
                    Quantity
                </label>
                <input type="text" name="quantity" id="quantity" value="" class="form-control quantity" title="Provide a quantity"/>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Order</button>
            </div>
        </form>
        <?php

        include __DIR__ . '/../Common/footer.php';
    }

    public function listSalesOrdersController(): void
    {
        $allSalesOrders = Database::retrieveAll(SalesOrder::class);

        Render::jsonOrHtml($allSalesOrders);
    }

    public function deliverSalesOrderController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var SalesOrder $salesOrder */
            $salesOrder = Database::retrieve(SalesOrder::class, $_POST['salesOrderId']);

            $salesOrder->deliver();

            FlashMessage::add(FlashMessage::SUCCESS, 'Delivered sales order ' . $_POST['salesOrderId']);

            Database::persist($salesOrder);

            Stream::produce('sales.goods_delivered', [
                'salesOrderId' => $salesOrder->id(),
                'productId' => $salesOrder->productId(),
                'quantity' => $salesOrder->quantity()
            ]);

            header('Location: /listSalesOrders');
            exit;
        }

        include __DIR__ . '/../Common/header.php';

        $salesOrders = Database::retrieveAll(SalesOrder::class);
        $openSalesOrders = array_filter($salesOrders, function (SalesOrder $salesOrder) {
            return !$salesOrder->wasDelivered();
        });

        if (\count($openSalesOrders) > 0) {
            ?>
            <form method="post" action="/deliverSalesOrder">
                <p>
                    <label for="salesOrderId">Deliver sales order: </label>
                    <select name="salesOrderId" id="salesOrderId" class="form-control">
                        <?php

                        foreach ($openSalesOrders as $salesOrder) {
                            /** @var SalesOrder $salesOrder */
                            ?>
                            <option value="<?php echo $salesOrder->id(); ?>"><?php echo $salesOrder->id(); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <button type="submit" class="btn btn-primary">Deliver</button>
                </p>
            </form>
            <?php
        } else {
            ?>
            <p>There's no open sales order, so you can't deliver anything at this moment.</p>
            <p>You could of course <a href="/createSalesOrder">Create a Sales order</a>.</p>
            <?php
        }

        include __DIR__ . '/../Common/footer.php';
    }
}

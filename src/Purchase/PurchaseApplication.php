<?php
declare(strict_types=1);

namespace Purchase;

use Common\Persistence\Database;
use Common\Render;
use Common\Web\FlashMessage;
use Common\Web\HttpApi;

final class PurchaseApplication
{
    public function bootstrap(): void
    {
        session_start();
    }

    public function createPurchaseOrderController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $purchaseOrderId = isset($_POST['purchaseOrderId'])
                ? PurchaseOrderId::fromString($_POST['purchaseOrderId'])
                : PurchaseOrderId::create();

            $purchaseOrder = new PurchaseOrder($purchaseOrderId, $_POST['productId'], (int)$_POST['quantity']);

            Database::persist($purchaseOrder);

            FlashMessage::add(FlashMessage::SUCCESS, 'Created purchase order ' . $purchaseOrderId);

            header('Location: /listPurchaseOrders');
            exit;
        }

        $products = array_values((array)HttpApi::fetchDecodedJsonResponse('http://catalog_web/listProducts'));

        include __DIR__ . '/../Common/header.php';

        ?>
        <h1>Create a purchase order</h1>
        <form action="/createPurchaseOrder" method="post">
            <div class="form-group">
                <label for="productId">
                    Product
                </label>
                <select name="productId" id="productId" class="form-control productId">
                    <?php
                    foreach ($products as $product) {
                        ?>
                        <option value="<?php echo $product->productId; ?>"><?php echo htmlspecialchars($product->name); ?></option>
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

    public function listPurchaseOrdersController(): void
    {
        $allPurchaseOrders = Database::retrieveAll(PurchaseOrder::class);

        Render::jsonOrHtml($allPurchaseOrders);
    }

    public function receiveGoodsController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = Database::retrieve(PurchaseOrder::class, $_POST['purchaseOrderId']);

            $purchaseOrder->markAsReceived();

            FlashMessage::add(FlashMessage::SUCCESS, 'Marked purchase order as received: ' . $_POST['purchaseOrderId']);

            Database::persist($purchaseOrder);

            header('Location: /listPurchaseOrders');
            exit;
        }

        include __DIR__ . '/../Common/header.php';

        $purchaseOrders = Database::retrieveAll(PurchaseOrder::class);
        $openPurchaseOrders = array_filter($purchaseOrders, function (PurchaseOrder $purchaseOrder) {
            return $purchaseOrder->isOpen();
        });

        if (\count($openPurchaseOrders) > 0) {
            ?>
            <form method="post" action="/receiveGoods">
                <p>
                    <label for="purchaseOrderId">Receive goods for order: </label>
                    <select name="purchaseOrderId" id="purchaseOrderId" class="form-control">
                        <?php

                        foreach ($openPurchaseOrders as $purchaseOrder) {
                            /** @var PurchaseOrder $purchaseOrder */
                            ?>
                            <option value="<?php echo $purchaseOrder->id(); ?>"><?php echo $purchaseOrder->id(); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <button type="submit" class="btn btn-primary">Receive</button>
                </p>
            </form>
            <?php
        } else {
            ?>
            <p>There's no open purchase order, so you can't receive anything at this moment.</p>
            <p>You could of course <a href="/createPurchaseOrder">Create a Purchase order</a>.</p>
            <?php
        }

        include __DIR__ . '/../Common/footer.php';
    }
}

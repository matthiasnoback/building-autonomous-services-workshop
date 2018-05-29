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

            $lines = [];

            foreach ($_POST['lines'] as $line) {

                if ((int)$line['quantity'] <= 0) {
                    continue;
                }

                $lines[] = new PurchaseOrderLine($line['productId'], (int)$line['quantity']);
            }

            $purchaseOrder = new PurchaseOrder($purchaseOrderId, $lines);

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
            <table class="table">
                <thead>
                <tr>
                    <th>Line</th>
                    <th>Product</th>
                    <th>Quantity</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($products as $i => $product) {
                    ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td class="product-name">
                            <input type="hidden" name="lines[<?php echo $i; ?>][productId]" value="<?php echo $product->productId; ?>" />
                            <?php echo htmlspecialchars($product->name); ?>
                        </td>
                        <td>
                            <input type="text" name="lines[<?php echo $i; ?>][quantity]" value="" class="form-control quantity" title="Provide a quantity"/>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <p>
                <button type="submit" class="btn btn-primary">Order</button>
            </p>
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

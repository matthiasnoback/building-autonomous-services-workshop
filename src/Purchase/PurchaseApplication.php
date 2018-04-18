<?php
declare(strict_types=1);

namespace Purchase;

use Common\Persistence\Database;
use Common\Render;
use Common\Web\HttpApi;

final class PurchaseApplication
{
    public function createPurchaseOrderController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allPurchaseOrders = Database::retrieveAll(PurchaseOrder::class);
            $purchaseOrderId = \count($allPurchaseOrders) + 1;

            $lines = [];

            foreach ($_POST['lines'] as $line) {

                if ((int)$line['quantity'] <= 0) {
                    continue;
                }

                $lines[] = new PurchaseOrderLine((int)$line['productId'], (int)$line['quantity']);
            }

            $purchaseOrder = new PurchaseOrder($purchaseOrderId, $lines);

            Database::persist($purchaseOrder);

            header('Location: /listPurchaseOrders');
            exit;
        }

        $products = array_values((array)HttpApi::fetchDecodedJsonResponse('http://catalog_web/listProducts'));

        include __DIR__ . '/../Common/header.html';

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
                        <td>
                            <input type="hidden" name="lines[<?php echo $i; ?>][productId]" value="<?php echo $product->productId; ?>" />
                            <?php echo $product->name; ?>
                        </td>
                        <td>
                            <input type="text" name="lines[<?php echo $i; ?>][quantity]" value="" class="form-control" title="Provide a quantity"/>
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

        include __DIR__ . '/../Common/footer.html';
    }

    public function listPurchaseOrdersController(): void
    {
        $allPurchaseOrders = Database::retrieveAll(PurchaseOrder::class);

        Render::jsonOrHtml($allPurchaseOrders);
    }

    public function selectPurchaseOrderController(): void
    {
        include __DIR__ . '/../Common/header.html';

        $purchaseOrders = Database::retrieveAll(PurchaseOrder::class);
        $openPurchaseOrders = array_filter($purchaseOrders, function (PurchaseOrder $purchaseOrder) { return $purchaseOrder->isOpen(); });

        if (count($openPurchaseOrders) > 0) {
            ?>
            <form method="get" action="/receiveGoods">
                <p>
                <label for="purchaseOrderId">Receive goods for purchase order: </label>
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
                <p><button type="submit" class="btn btn-primary">Next &raquo;</button></p>
            </form>
            <?php
        }
        else {
            ?>
            <p>There's no open purchase order, so you can't receive goods at this moment.</p>
            <p>You could of course <a href="/createPurchaseOrder">Create a Purchase order</a>.</p>
            <?php
        }

        include __DIR__ . '/../Common/footer.html';
    }

    public function receiveGoodsController($purchaseOrderId): void
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = Database::retrieve(PurchaseOrder::class, $purchaseOrderId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allReceipts = Database::retrieveAll(Receipt::class);
            $nextReceiptId = \count($allReceipts) + 1;

            $lines = [];

            foreach ($_POST['lines'] as $line) {

                if ((int)$line['quantity'] <= 0) {
                    continue;
                }

                $lines[(int)$line['productId']] = new ReceiptItem((int)$line['productId'], (int)$line['quantity']);
            }

            $receipt = new Receipt($nextReceiptId, (int)$purchaseOrderId, $lines);

            Database::persist($receipt);

            foreach ($receipt->lines() as $line) {
                $purchaseOrder->processReceipt($line->productId(), $line->quantity());
            }
            Database::persist($purchaseOrder);

            header('Location: /listReceipts');
            exit;
        }

        $products = HttpApi::fetchDecodedJsonResponse('http://catalog_web/listProducts');

        include __DIR__ . '/../Common/header.html';

        ?>
        <h1>Receive goods</h1>
        <form action="#" method="post">
            <input type="hidden" name="purchaseOrderId" value="<?php echo $purchaseOrder->id(); ?>" />
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity open</th>
                    <th>Quantity received</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($purchaseOrder->lines() as $i => $line) {
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="lines[<?php echo $i; ?>][productId]" value="<?php echo $line->productId(); ?>" />
                            <?php echo $products->{$line->productId()}->name; ?>
                        </td>
                        <td>
                            <?php echo $line->quantityOpen(); ?>
                        </td>
                        <td>
                            <input type="text" name="lines[<?php echo $i; ?>][quantity]" value="" class="form-control" title="Provide a quantity"/>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <p>
                <button type="submit" class="btn btn-primary">Save</button>
            </p>
        </form>
        <?php

        include __DIR__ . '/../Common/footer.html';
    }

    public function listReceiptsController(): void
    {
        $allReceipts = Database::retrieveAll(Receipt::class);

        Render::jsonOrHtml($allReceipts);
    }
}

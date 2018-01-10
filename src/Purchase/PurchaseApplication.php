<?php
declare(strict_types=1);

namespace Purchase;

use Common\Persistence\Database;
use NaiveSerializer\Serializer;

final class PurchaseApplication
{
    public function receiveGoodsController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allReceipts = Database::retrieveAll(GoodsReceipt::class);
            $nextReceiptId = \count($allReceipts) + 1;

            $lines = [];

            foreach ($_POST['lines'] as $line) {

                if ((int)$line['quantity'] <= 0) {
                    continue;
                }

                $lines[(int)$line['productId']] = new ReceiptItem((int)$line['productId'], (int)$line['quantity']);
            }

            $receipt = new GoodsReceipt($nextReceiptId, $lines);

            Database::persist($receipt);

            header('Location: /listReceipts');
            exit;
        }

        /** @var Product[] $products */
        $products = Database::retrieveAll(Product::class);

        include __DIR__ . '/../Common/header.html';

        ?>
        <h1>Receive goods</h1>
        <form action="/receiveGoods" method="post">
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity received</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i = 0; $i < 5; $i++) {
                    ?>
                    <tr>
                        <td>
                            <select name="lines[<?php echo $i; ?>][productId]" class="form-control" title="Select a product">
                                <?php foreach ($products as $product) { ?>
                                    <option value="<?php echo $product->productId; ?>"><?php echo $product->productId . ': ' . $product->name; ?></option>
                                <?php } ?>
                            </select>
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
        $allPurchaseOrders = Database::retrieveAll(GoodsReceipt::class);

        header('Content-Type: application/json');
        echo Serializer::serialize($allPurchaseOrders);
    }
}

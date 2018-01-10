<?php
declare(strict_types=1);

namespace Sales;

use Common\Json;
use Common\Persistence\Database;
use NaiveSerializer\Serializer;

final class SalesApplication
{
    public function createSalesOrderController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allSalesOrders = Database::retrieveAll(SalesOrder::class);
            $salesOrderId = \count($allSalesOrders) + 1;

            $lines = [];

            foreach ($_POST['lines'] as $line) {

                if ((int)$line['quantity'] <= 0) {
                    continue;
                }

                $lines[(int)$line['productId']] = new SalesOrderLine((int)$line['productId'], (int)$line['quantity']);
            }

            $salesOrder = new SalesOrder($salesOrderId, $lines);

            Database::persist($salesOrder);

            header('Location: /listSalesOrders');
            exit;
        }

        $products = Json::decodeFromRemoteUrl('http://catalog_web/listProducts');

        include __DIR__ . '/../Common/header.html';

        ?>
        <h1>Create a sales order</h1>
        <form action="/createSalesOrder" method="post">
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
                for ($i = 0; $i < 5; $i++) {
                    ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td>
                            <select name="lines[<?php echo $i; ?>][productId]" class="form-control" title="Select a product">
                                <?php foreach ($products as $product) { ?>
                                    <option value="<?php echo $product['productId']; ?>"><?php echo $product['productId'] . ': ' . $product['name']; ?></option>
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
                <button type="submit" class="btn btn-primary">Create</button>
            </p>
        </form>
        <?php

        include __DIR__ . '/../Common/footer.html';
    }

    public function listSalesOrdersController(): void
    {
        $allPurchaseOrders = Database::retrieveAll(SalesOrder::class);

        header('Content-Type: application/json');
        echo Serializer::serialize($allPurchaseOrders);
    }
}

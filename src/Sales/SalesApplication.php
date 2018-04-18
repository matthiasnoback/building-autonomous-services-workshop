<?php
declare(strict_types=1);

namespace Sales;

use Common\Persistence\Database;
use Common\Render;
use Common\Web\HttpApi;

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

                $lines[] = new SalesOrderLine((int)$line['productId'], (int)$line['quantity']);
            }

            $salesOrder = new SalesOrder($salesOrderId, $lines);

            Database::persist($salesOrder);

            header('Location: /listSalesOrders');
            exit;
        }

        $products = array_values((array)HttpApi::fetchDecodedJsonResponse('http://catalog_web/listProducts'));

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

    public function listSalesOrdersController(): void
    {
        $allSalesOrders = Database::retrieveAll(SalesOrder::class);

        Render::jsonOrHtml($allSalesOrders);
    }
}

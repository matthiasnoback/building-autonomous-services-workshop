<?php
declare(strict_types=1);

namespace Sales;

use Common\Persistence\Database;
use Common\Render;
use Common\Web\FlashMessage;
use Common\Web\HttpApi;

final class SalesApplication
{
    public function __construct()
    {
        session_start();
    }

    public function createSalesOrderController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $salesOrderId = SalesOrderId::create();

            $lines = [];

            foreach ($_POST['lines'] as $line) {

                if ((int)$line['quantity'] <= 0) {
                    continue;
                }

                $lines[] = new SalesOrderLine($line['productId'], (int)$line['quantity']);
            }

            $salesOrder = new SalesOrder($salesOrderId, $lines);

            Database::persist($salesOrder);

            FlashMessage::add(FlashMessage::SUCCESS, 'Created sales order ' . $salesOrderId);

            header('Location: /listSalesOrders');
            exit;
        }

        $products = array_values((array)HttpApi::fetchDecodedJsonResponse('http://catalog_web/listProducts'));

        include __DIR__ . '/../Common/header.php';

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
                        <td class="product-name">
                            <input type="hidden" name="lines[<?php echo $i; ?>][productId]" value="<?php echo $product->productId; ?>"/>
                            <?php echo htmlspecialchars($product->name); ?>
                        </td>
                        <td>
                            <input type="text" name="lines[<?php echo $i; ?>][quantity]" value="" class="form-control quantity"
                                   title="Provide a quantity"/>
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

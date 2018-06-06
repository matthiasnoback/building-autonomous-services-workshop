<?php
use Common\Web\FlashMessage;
?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-theme.min.css">
    <title>Building Autonomous Services Workshop</title>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Auto-Stock</a>
            </div>
            <ul class="nav navbar-nav">
                <li>
                    <a href="http://dashboard.localhost/">Dashboard</a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Catalog <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="http://catalog.localhost/listProducts">List Products</a>
                        </li>
                        <li>
                            <a href="http://catalog.localhost/createProduct">Create a Product</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Stock <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="http://stock.localhost/stockLevels">Stock levels</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sales <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="http://sales.localhost/salesOrderStatuses">Show Sales order statuses</a>
                        </li>
                        <li>
                            <a href="http://sales.localhost/listSalesOrders">List Sales orders</a>
                        </li>
                        <li>
                            <a href="http://sales.localhost/createSalesOrder">Create a Sales order</a>
                        </li>
                        <li>
                            <a href="http://sales.localhost/deliverSalesOrder">Deliver a Sales order</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Purchasing <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="http://purchase.localhost/listPurchaseOrders">List Purchase orders</a>
                        </li>
                        <li>
                            <a href="http://purchase.localhost/createPurchaseOrder">Create a Purchase order</a>
                        </li>
                        <li>
                            <a href="http://purchase.localhost/receiveGoods">Receive Goods</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <?php



    foreach (FlashMessage::types() as $type) {
        foreach (FlashMessage::get($type) as $message) {
            ?>
            <div class="alert alert-<?php echo $type; ?>" role="alert">
                <?php
                echo htmlspecialchars($message);
                ?>
            </div>
            <?php
        }
    }
    ?>

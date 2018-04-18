<?php
declare(strict_types=1);

namespace Dashboard;

use Common\Web\HttpApi;

final class DashboardApplication
{
    public function indexController(): void
    {
        include __DIR__ . '/../Common/header.html';

        ?><h1>Dashboard</h1><?php

        $allProducts = HttpApi::fetchDecodedJsonResponse('http://catalog_web/listProducts');

        $stockLevels = HttpApi::fetchDecodedJsonResponse('http://stock_web/stockLevels');

        ?><h2>List of all products</h2>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Stock level</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($allProducts as $productData) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)$productData->productId); ?></td>
                    <td><?php echo htmlspecialchars($productData->name); ?></td>
                    <td><?php echo $stockLevels->{$productData->productId} ?? 0; ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        include __DIR__ . '/../Common/footer.html';
    }
}

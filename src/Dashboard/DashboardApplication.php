<?php
declare(strict_types=1);

namespace Dashboard;

use Common\Persistence\Database;

final class DashboardApplication
{
    public function bootstrap(): void
    {
        session_start();
    }

    public function indexController(): void
    {
        $allProducts = Database::retrieveAll(Product::class);

        include __DIR__ . '/../Common/header.php';
        ?>
        <h1>Dashboard</h1>
        <h2>List of all products</h2>
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
            foreach ($allProducts as $product) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($product->id()); ?></td>
                    <td class="product-name"><?php echo htmlspecialchars($product->name()); ?></td>
                    <td class="stock-level"><?php echo $product->stockLevel(); ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        include __DIR__ . '/../Common/footer.php';
    }
}

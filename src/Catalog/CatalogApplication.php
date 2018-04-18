<?php

namespace Catalog;

use Common\Persistence\Database;
use Common\Render;

final class CatalogApplication
{
    public function createProductController(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allProducts = Database::retrieveAll(Product::class);
            $productId = \count($allProducts) + 1;
            $product = new Product(
                $productId,
                $_POST['name'],
                (int)$_POST['minimum_stock_level'] ?: null
            );
            Database::persist($product);

            header('Location: /listProducts');
            exit;
        }

        include __DIR__ . '/../Common/header.html';

        ?>
        <h1>Create a product</h1>
        <form action="/createProduct" method="post">
            <div class="form-group">
                <label for="name" class="control-label">Name:</label>
                <input type="text" name="name" id="name" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="minimum_stock_level" class="control-label">Minimum stock level:</label>
                <input type="text" name="minimum_stock_level" id="minimum_stock_level" class="form-control"/>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
        <?php

        include __DIR__ . '/../Common/footer.html';
    }

    public function listProductsController(): void
    {
        $allProducts = Database::retrieveAll(Product::class);

        Render::jsonOrHtml($allProducts);
    }
}

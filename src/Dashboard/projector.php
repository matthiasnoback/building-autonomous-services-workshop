<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Stream\Stream;
use Dashboard\Product;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

Database::deleteAll(Product::class);

Stream::consume(function(string $messageType, $data) {
    if ($messageType === 'catalog.product_created') {
        $product = new Product($data['productId'], $data['name']);
        Database::persist($product);
    }
    elseif ($messageType === 'stock.stock_level_changed') {
        /** @var Product $product */
        $product = Database::retrieve(Product::class, $data['productId']);
        $product->setStockLevel($data['stockLevel']);
        Database::persist($product);
    }
});

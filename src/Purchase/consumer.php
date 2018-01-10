<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Stream\Stream;
use Purchase\Product;

require __DIR__ . '/../../vendor/autoload.php';

Database::deleteAll(Product::class);

Stream::consume(function (string $messageType, $data) {
    if ($messageType !== 'product_created') {
        return;
    }

    $localProduct = new Product();
    $localProduct->productId = $data->productId;
    $localProduct->name = $data->name;
    Database::persist($localProduct);
});

<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Stream\Stream;
use Sales\Product;
use Symfony\Component\ErrorHandler\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

Database::deleteAll(Product::class);

Stream::consume(function (string $messageType, $data) {
    if ($messageType === 'catalog.product_created') {
        $product = new Product($data['productId'], $data['name']);
        Database::persist($product);
    }
});

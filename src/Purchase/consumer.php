<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Stream\Stream;
use Purchase\Product;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

/*
 * This is a demo consumer which consumes every message from the stream.
 * This effectively makes the consumer consume every existing message again
 * after a restart.
 *
 * The consumer prints the message to stdout. Hence, if you want to visually keep
 * track of the stream, run:
 *
 *   docker-compose logs -f consumer
 */
Stream::consume(function(string $messageType, $data) {
    if ($messageType === 'catalog.product_created') {
        $product = new Product($data->productId, $data->name);
        Database::persist($product);
    }
});

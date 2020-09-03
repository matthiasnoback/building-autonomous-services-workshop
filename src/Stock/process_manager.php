<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Stock\Balance;
use Symfony\Component\ErrorHandler\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

$startAtIndexKey = $startAtIndexKey = basename(__DIR__) . '_start_at_index';

$startAtIndex = KeyValueStore::get($startAtIndexKey) ?: 0;
echo 'Start consuming at index: ' . (string)$startAtIndex;

Stream::consume(
    function (string $messageType, $data) use ($startAtIndexKey) {
        if ($messageType === 'catalog.product_created') {
            $balance = new Balance($data['productId']);
            Database::persist($balance);
        }
        elseif ($messageType === 'purchase.goods_received') {
            $balance = Database::retrieve(Balance::class, $data['productId']);
            $balance->increase($data['quantity']);
            Database::persist($balance);

            Stream::produce('stock.stock_level_changed', [
                'productId' => $data['productId'],
                'stockLevel' => $balance->stockLevel()
            ]);
        }
        elseif ($messageType === 'sales.goods_delivered') {
            $balance = Database::retrieve(Balance::class, $data['productId']);
            $balance->decrease($data['quantity']);
            Database::persist($balance);

            Stream::produce('stock.stock_level_changed', [
                'productId' => $data['productId'],
                'stockLevel' => $balance->stockLevel()
            ]);
        }

        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Stream\Stream;
use Stock\Balance;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

Stream::consume(function(string $messageType, $data) {
    if ($messageType === 'catalog.product_created') {
        $balance = new Balance($data->productId);
        Database::persist($balance);
    }
    elseif ($messageType === 'stock.stock_level_increased') {
        /** @var Balance $balance */
        $balance = Database::retrieve(Balance::class, $data->productId);
        $balance->increase($data->quantity);
        Database::persist($balance);
    }
    elseif ($messageType === 'stock.stock_level_decreased') {
        /** @var Balance $balance */
        $balance = Database::retrieve(Balance::class, $data->productId);
        $balance->decrease($data->quantity);
        Database::persist($balance);
    }
});

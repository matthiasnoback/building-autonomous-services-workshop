<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Stock\Balance;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

$startAtIndexKey = $startAtIndexKey = basename(__DIR__) . '_start_at_index';

$startAtIndex = KeyValueStore::get($startAtIndexKey) ?: 0;
echo 'Start consuming at index: ' . (string)$startAtIndex;

Stream::consume(
    function (string $messageType, $data) use ($startAtIndexKey) {
        if ($messageType === 'catalog.product_created') {
            /** @var Balance $balance */
            $balance = new Balance($data['productId']);
            Database::persist($balance);
        } elseif ($messageType === 'purchase.goods_received') {
            /** @var Balance $balance */
            $balance = Database::retrieve(Balance::class, $data['productId']);
            $balance->increase($data['quantity']);

            $balance->tryRejectedReservations();

            $events = $balance->releaseEvents();

            Database::persist($balance);

            foreach ($events as $event) {
                list($messageType, $messageData) = $event;

                Stream::produce($messageType, $messageData);
            }
        }

        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

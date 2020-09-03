<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Common\Web\HttpApi;
use Sales\SalesOrder;
use Symfony\Component\ErrorHandler\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

$startAtIndexKey = $startAtIndexKey = basename(__DIR__) . '_start_at_index';

$startAtIndex = KeyValueStore::get($startAtIndexKey) ?: 0;
echo 'Start consuming at index: ' . (string)$startAtIndex;

Stream::consume(
    function (string $messageType, $data) use ($startAtIndexKey) {
        if ($messageType === 'sales.sales_order_created') {
            echo HttpApi::postFormData(
                'http://stock_web/makeStockReservation',
                [
                    'reservationId' => $data['salesOrderId'],
                    'productId' => $data['productId'],
                    'quantity' => $data['quantity']
                ]
            );
        } elseif ($messageType === 'stock.reservation_accepted') {
            $salesOrder = Database::retrieve(SalesOrder::class, $data['reservationId']);
            $salesOrder->markAsDeliverable();
            Database::persist($salesOrder);
        }

        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

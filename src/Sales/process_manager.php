<?php
declare(strict_types=1);

use function Common\CommandLine\line;
use function Common\CommandLine\stdout;
use Common\HttpApiExtra;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

// the key to use when storing the current message index
$startAtIndexKey = $startAtIndexKey = basename(__DIR__) . '_start_at_index';;

$startAtIndex = KeyValueStore::get($startAtIndexKey) ?: 0;
stdout(line('Start consuming at index', ':', (string)$startAtIndex));

// start consuming at the given index, and keep consuming incoming messages
Stream::consume(
    function (string $messageType, $data) use ($startAtIndexKey) {
        if ($messageType === 'sales.sales_order_created') {
            echo HttpApiExtra::postFormData(
                'http://stock_web/makeStockReservation',
                [
                    'reservationId' => $data->salesOrderId,
                    'productId' => $data->productId,
                    'quantity' => $data->quantity
                ]
            );
        } elseif ($messageType === 'stock.reservation_accepted') {
            echo HttpApiExtra::postFormData(
                'http://sales_web/deliverSalesOrder',
                [
                    'salesOrderId' => $data->reservationId
                ]
            );
        } elseif ($messageType === 'sales.goods_delivered') {
            echo HttpApiExtra::postFormData(
                'http://stock_web/commitStockReservation',
                [
                    'reservationId' => $data->salesOrderId,
                    'productId' => $data->productId
                ]
            );
        }

        // increase the "start at index" value, so we won't consume this message again
        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

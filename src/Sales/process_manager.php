<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Common\Web\HttpApi;
use Ramsey\Uuid\Uuid;
use Sales\OrderStatus;
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
            $orderStatus = new OrderStatus($data['salesOrderId']);
            Database::persist($orderStatus);

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
        } elseif ($messageType === 'stock.reservation_rejected') {
            // We can generate purchase order ID ourselves! :)
            $purchaseOrderId = Uuid::uuid4()->toString();

            $formData = [
                'purchaseOrderId' => $purchaseOrderId,
                'productId' => $data['productId'],
                'quantity' => (int)$data['quantity']
            ];

            echo HttpApi::postFormData(
                'http://purchase_web/createPurchaseOrder',
                $formData
            );

            /** @var OrderStatus $orderStatus */
            $orderStatus = Database::retrieve(OrderStatus::class, $data['reservationId']);
            $orderStatus->setPurchaseOrderId($purchaseOrderId);
            Database::persist($orderStatus);
        }

        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

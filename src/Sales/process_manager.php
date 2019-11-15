<?php
declare(strict_types=1);

use Common\Persistence\Database;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Common\Web\HttpApi;
use Ramsey\Uuid\Uuid;
use Sales\OrderStatus;
use Sales\SalesOrder;
use Symfony\Component\Debug\Debug;

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
            /** @var SalesOrder $salesOrder */
            $salesOrder = Database::retrieve(SalesOrder::class, $data['reservationId']);
            $salesOrder->markAsDeliverable();
            Database::persist($salesOrder);

            /** @var OrderStatus $orderStatus */
            $orderStatus = Database::retrieve(OrderStatus::class, $data['reservationId']);
            $orderStatus->deliverable();
            Database::persist($orderStatus);
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
            $orderStatus->waitingForGoods($purchaseOrderId);
            Database::persist($orderStatus);
        } elseif ($messageType === 'purchase.goods_received') {
            $purchaseOrderId = $data['purchaseOrderId'];
            $orderStatus = Database::findOne(OrderStatus::class, function (OrderStatus $orderStatus) use ($purchaseOrderId) {
                return $orderStatus->purchaseOrderId() === $purchaseOrderId;
            });
            if ($orderStatus instanceof OrderStatus) {
                sleep(3);
                echo "Making another reservation\n";
                echo HttpApi::postFormData(
                    'http://stock_web/makeStockReservation',
                    [
                        'reservationId' => $orderStatus->id(),
                        'productId' => $data['productId'],
                        'quantity' => $data['quantity']
                    ]
                );
            }
            echo "No order status found\n";
        } elseif ($messageType === 'sales.goods_delivered') {
            echo HttpApi::postFormData(
                'http://stock_web/commitStockReservation',
                [
                    'reservationId' => $data['salesOrderId'],
                    'productId' => $data['productId']
                ]
            );

            /** @var OrderStatus $orderStatus */
            $orderStatus = Database::retrieve(OrderStatus::class, $data['reservationId']);
            $orderStatus->delivered();
            Database::persist($orderStatus);
        }

        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

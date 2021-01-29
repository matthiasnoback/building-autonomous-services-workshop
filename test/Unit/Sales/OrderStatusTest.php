<?php
declare(strict_types=1);

namespace Sales;

use Generator;
use Purchase\PurchaseOrderId;
use Test\Integration\EntityTest;

final class OrderStatusTest extends EntityTest
{
    /**
     * @test
     */
    public function it_can_be_created_without_a_purchase_order_id(): void
    {
        $status = new OrderStatus('1eabf902-0c60-4e1f-adae-0db116c97f16');
        self::assertNull($status->purchaseOrderId());
    }

    protected function getObject(): Generator
    {
        yield new OrderStatus(SalesOrderId::create()->asString());

        $orderStatusWithPurchaseOrderId = new OrderStatus(SalesOrderId::create()->asString());
        $orderStatusWithPurchaseOrderId->setPurchaseOrderId(PurchaseOrderId::create()->asString());
        yield $orderStatusWithPurchaseOrderId;
    }
}

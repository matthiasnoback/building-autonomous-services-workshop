<?php
declare(strict_types=1);

namespace Sales;

use PHPUnit\Framework\TestCase;

final class OrderStatusTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_without_a_purchase_order_id(): void
    {
        $status = new OrderStatus('1eabf902-0c60-4e1f-adae-0db116c97f16');
        self::assertNull($status->purchaseOrderId());
    }
}

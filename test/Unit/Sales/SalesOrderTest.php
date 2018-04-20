<?php
declare(strict_types=1);

namespace Sales;

use PHPUnit\Framework\TestCase;

final class SalesOrderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_with_an_id_and_lines(): void
    {
        $salesOrder = new SalesOrder(1, [
            new SalesOrderLine(100, 10)
        ]);

        self::assertEquals(1, $salesOrder->id());
        self::assertEquals(100, $salesOrder->lines()[0]->productId());
        self::assertEquals(10, $salesOrder->lines()[0]->quantity());
    }

    /**
     * @test
     */
    public function initially_it_has_not_been_delivered_yet(): void
    {
        $salesOrder = new SalesOrder(1, [
            new SalesOrderLine(100, 10)
        ]);

        self::assertFalse($salesOrder->wasDelivered());
    }
    /**
     * @test
     */
    public function it_will_remember_if_it_was_delivered(): void
    {
        $salesOrder = new SalesOrder(1, [
            new SalesOrderLine(100, 10)
        ]);

        $salesOrder->deliver();

        self::assertTrue($salesOrder->wasDelivered());
    }
}

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
        $salesOrderId = SalesOrderId::create();
        $productId = '8513f8f0-9ed6-4096-b84c-3274dc0394d1';
        $quantity = 10;
        $salesOrder = new SalesOrder($salesOrderId, $productId, $quantity);

        self::assertEquals($salesOrderId, $salesOrder->id());
        self::assertEquals($productId, $salesOrder->productId());
        self::assertEquals($quantity, $salesOrder->quantity());
    }

    /**
     * @test
     */
    public function initially_it_has_not_been_delivered_yet(): void
    {
        $salesOrder = $this->someSalesOrder();

        self::assertFalse($salesOrder->wasDelivered());
    }

    /**
     * @test
     */
    public function it_will_remember_if_it_was_delivered(): void
    {
        $salesOrder = $this->someSalesOrder();

        $salesOrder->deliver();

        self::assertTrue($salesOrder->wasDelivered());
    }

    /**
     * @return SalesOrder
     */
    private function someSalesOrder(): SalesOrder
    {
        return new SalesOrder(SalesOrderId::create(), '8513f8f0-9ed6-4096-b84c-3274dc0394d1', 10);
    }
}

<?php
declare(strict_types=1);

namespace Purchase;

use PHPUnit\Framework\TestCase;

final class PurchaseOrderTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_an_id_and_lines(): void
    {
        $purchaseOrder = new PurchaseOrder(1, [
            new PurchaseOrderLine(100, 10)
        ]);

        self::assertEquals(1, $purchaseOrder->id());
        self::assertEquals(100, $purchaseOrder->lines()[0]->productId());
        self::assertEquals(10, $purchaseOrder->lines()[0]->quantityOrdered());
    }

    /**
     * @test
     */
    public function initially_its_status_is_open(): void
    {
        $purchaseOrder = new PurchaseOrder(1, [
            new PurchaseOrderLine(100, 10)
        ]);

        self::assertTrue($purchaseOrder->isOpen());
    }

    /**
     * @test
     */
    public function after_receiving_the_ordered_quantity_it_will_be_completed(): void
    {
        $purchaseOrder = new PurchaseOrder(1, [
            new PurchaseOrderLine(100, 10)
        ]);

        $purchaseOrder->processReceipt(100, $exactlyWhatWasOrdered = 10);

        self::assertFalse($purchaseOrder->isOpen());
    }

    /**
     * @test
     */
    public function after_receiving_less_than_the_ordered_quantity_it_will_still_be_open(): void
    {
        $purchaseOrder = new PurchaseOrder(1, [
            new PurchaseOrderLine(100, 10)
        ]);

        $purchaseOrder->processReceipt(100, $lessThanWasOrdered = 5);

        self::assertTrue($purchaseOrder->isOpen());
    }

    /**
     * @test
     */
    public function it_is_possible_to_receive_more_than_was_ordered(): void
    {
        $purchaseOrder = new PurchaseOrder(1, [
            new PurchaseOrderLine(100, 10)
        ]);

        $purchaseOrder->processReceipt(100, $moreThanWasOrdered = 15);

        self::assertFalse($purchaseOrder->isOpen());
    }
}

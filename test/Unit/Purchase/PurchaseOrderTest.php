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
        $purchaseOrderId = PurchaseOrderId::create();
        $purchaseOrder = new PurchaseOrder($purchaseOrderId, [
            new PurchaseOrderLine('100', 10)
        ]);

        self::assertEquals((string)$purchaseOrderId, $purchaseOrder->id());
        self::assertEquals('100', $purchaseOrder->lines()[0]->productId());
        self::assertEquals(10, $purchaseOrder->lines()[0]->quantity());
    }

    /**
     * @test
     */
    public function initially_its_status_is_open(): void
    {
        $purchaseOrder = new PurchaseOrder(PurchaseOrderId::create(), [
            new PurchaseOrderLine('100', 10)
        ]);

        self::assertTrue($purchaseOrder->isOpen());
    }

    /**
     * @test
     */
    public function after_receiving_its_status_is_no_longer_open(): void
    {
        $purchaseOrder = new PurchaseOrder(PurchaseOrderId::create(), [
            new PurchaseOrderLine('100', 10)
        ]);
        $purchaseOrder->markAsReceived();

        self::assertFalse($purchaseOrder->isOpen());
    }
}

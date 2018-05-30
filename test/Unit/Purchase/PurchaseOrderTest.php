<?php
declare(strict_types=1);

namespace Purchase;

use PHPUnit\Framework\TestCase;

final class PurchaseOrderTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_an_id_a_product_and_a_quantity(): void
    {
        $purchaseOrderId = PurchaseOrderId::create();
        $productId = 'f57ad8e5-e713-45dd-a623-4ff7fd3d9297';
        $quantity = 10;
        $purchaseOrder = new PurchaseOrder($purchaseOrderId, $productId, $quantity);

        self::assertEquals((string)$purchaseOrderId, $purchaseOrder->id());
        self::assertEquals($productId, $purchaseOrder->productId());
        self::assertEquals($quantity, $purchaseOrder->quantity());
    }

    /**
     * @test
     */
    public function initially_its_status_is_open(): void
    {
        $purchaseOrder = $this->somePurchaseOrder();

        self::assertTrue($purchaseOrder->isOpen());
    }

    /**
     * @test
     */
    public function after_marking_it_as_received_its_status_is_no_longer_open(): void
    {
        $purchaseOrder = $this->somePurchaseOrder();

        $purchaseOrder->markAsReceived();

        self::assertFalse($purchaseOrder->isOpen());
    }

    /**
     * @return PurchaseOrder
     */
    private function somePurchaseOrder(): PurchaseOrder
    {
        return new PurchaseOrder(
            PurchaseOrderId::create(),
            'f57ad8e5-e713-45dd-a623-4ff7fd3d9297',
            10
        );
    }
}

<?php
declare(strict_types=1);

namespace Purchase;

use PHPUnit\Framework\TestCase;

final class ReceiptTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_an_id_a_purchase_order_id_and_lines(): void
    {
        $receipt = new Receipt(1, 2, [
            new ReceiptLine(100, 10)
        ]);

        self::assertEquals(1, $receipt->id());
        self::assertEquals(2, $receipt->purchaseOrderId());
        self::assertEquals(100, $receipt->lines()[0]->productId());
        self::assertEquals(10, $receipt->lines()[0]->quantity());
    }
}

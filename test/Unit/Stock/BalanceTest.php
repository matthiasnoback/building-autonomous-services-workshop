<?php
declare(strict_types=1);

namespace Stock;

use PHPUnit\Framework\TestCase;

final class BalanceTest extends TestCase
{
    /**
     * @test
     */
    public function when_created_for_a_product_its_initial_stock_level_is_0(): void
    {
        $productId = '3257474b-09cb-4339-8e55-8b2476f493c1';
        $balance = new Balance($productId);

        self::assertEquals($productId, $balance->id());
        self::assertEquals(0, $balance->stockLevel());
    }

    /**
     * @test
     */
    public function when_processing_a_received_quantity_its_stock_level_gets_increased_by_that_quantity(): void
    {
        $balance = new Balance('3257474b-09cb-4339-8e55-8b2476f493c1');

        $balance->increase(4);

        self::assertEquals(4, $balance->stockLevel());
    }

    /**
     * @test
     */
    public function when_processing_a_delivered_quantity_its_stock_level_gets_decreased_by_that_quantity(): void
    {
        $balance = new Balance('3257474b-09cb-4339-8e55-8b2476f493c1');
        $balance->increase(4);

        $balance->decrease(1);

        self::assertEquals(3, $balance->stockLevel());
    }
}

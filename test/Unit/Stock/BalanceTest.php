<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\IdentifiableObject;
use Test\Integration\EntityTest;

final class BalanceTest extends EntityTest
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

    /**
     * @test
     */
    public function you_can_make_a_stock_reservation_for_a_given_quantity(): void
    {
        $balance = new Balance('3257474b-09cb-4339-8e55-8b2476f493c1');
        $balance->increase(4);

        $reservationId = '23bb342d-5ac1-433a-b0ae-8beb6a2490ae';
        $reservationSucceeded = $balance->makeReservation($reservationId, 3);

        self::assertTrue($reservationSucceeded);
        self::assertEquals(1, $balance->stockLevel());
        self::assertTrue($balance->hasReservation($reservationId));
    }

    /**
     * @test
     */
    public function you_cannot_make_a_stock_reservation_if_the_stock_level_is_insufficient(): void
    {
        $balance = new Balance('3257474b-09cb-4339-8e55-8b2476f493c1');
        $balance->increase(3);

        $reservationId = '23bb342d-5ac1-433a-b0ae-8beb6a2490ae';
        $reservationSucceeded = $balance->makeReservation($reservationId, 4);

        self::assertFalse($reservationSucceeded);
        self::assertEquals(3, $balance->stockLevel());
        self::assertFalse($balance->hasReservation($reservationId));
    }

    /**
     * @test
     */
    public function you_can_commit_a_reservation(): void
    {
        $balance = new Balance('3257474b-09cb-4339-8e55-8b2476f493c1');
        $balance->increase(4);
        $reservationId = '23bb342d-5ac1-433a-b0ae-8beb6a2490ae';
        $balance->makeReservation($reservationId, 3);

        $balance->commitReservation($reservationId);

        // nothing has changed about the balance
        self::assertEquals(1, $balance->stockLevel());
        self::assertFalse($balance->hasReservation($reservationId));
    }

    protected function getObject(): IdentifiableObject
    {
        $balance = new Balance('3257474b-09cb-4339-8e55-8b2476f493c1');
        $balance->increase(4);
        $balance->makeReservation('23bb342d-5ac1-433a-b0ae-8beb6a2490ae', 3);

        return $balance;
    }
}

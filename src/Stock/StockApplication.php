<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\Database;
use Common\Render;

final class StockApplication
{
    public function stockLevelsController(): void
    {
        $stockLevels = [];

        foreach (Database::retrieveAll(Balance::class) as $balance) {
            $stockLevels[$balance->id()] = $balance->stockLevel();
        }

        Render::jsonOrHtml($stockLevels);
    }

    /**
     * Note: this controller will become useful in Assignment 5
     */
    public function makeStockReservationController(): void
    {
        $balance = Database::retrieve(Balance::class, $_POST['productId']);

        $reservationWasAccepted = $balance->makeReservation($_POST['reservationId'], (int)$_POST['quantity']);
        Database::persist($balance);

        if ($reservationWasAccepted) {

            // TODO dispatch "reservation accepted" event
        } else {
            // TODO dispatch "reservation rejected" event
        }
    }
}

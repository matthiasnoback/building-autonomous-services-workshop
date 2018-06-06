<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\Database;
use Common\Render;

final class StockApplication
{
    public function stockLevelsController(): void
    {
        $stockLevels = Database::retrieveAll(Balance::class);

        Render::jsonOrHtml($stockLevels);
    }

    /**
     * Note: this controller will become useful in Assignment 5
     */
    public function makeStockReservationController(): void
    {
        /** @var Balance $balance */
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

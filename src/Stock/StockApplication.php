<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\Database;
use Common\Render;
use Common\Stream\Stream;

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
            Database::persist($balance);
            Stream::produce('stock.reservation_accepted', [
                'reservationId' => $_POST['reservationId'],
                'productId' => $_POST['productId'],
                'quantity' => (int)$_POST['quantity']
            ]);
        } else {
            Stream::produce('stock.reservation_rejected', [
                'reservationId' => $_POST['reservationId'],
                'productId' => $_POST['productId'],
                'quantity' => (int)$_POST['quantity']
            ]);
        }
    }
}

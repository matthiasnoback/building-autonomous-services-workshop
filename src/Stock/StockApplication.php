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
        $stockLevels = Database::retrieveAll(Balance::class);

        Render::jsonOrHtml($stockLevels);
    }

    public function makeStockReservationController(): void
    {
        /** @var Balance $balance */
        $balance = Database::retrieve(Balance::class, $_POST['productId']);

        $successful = $balance->makeReservation($_POST['reservationId'], (int)$_POST['quantity']);

        if ($successful) {
            Database::persist($balance);
            Stream::produce('stock.reservation_accepted', [
                'reservationId' => $_POST['reservationId'],
                'productId' => $_POST['productId'],
                'quantity' => (int)$_POST['quantity']
            ]);
            Stream::produce('stock.stock_level_decreased', [
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

    public function commitStockReservationController(): void
    {
        // TODO delete the reservation
    }
}

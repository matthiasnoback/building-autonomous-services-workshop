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

        if ($balance->stockLevel() >= (int)$_POST['quantity']) {
            $reservation = new Reservation($_POST['reservationId'], $_POST['productId'], (int)$_POST['quantity']);
            Database::persist($reservation);
            Stream::produce('stock.reservation_accepted', [
                'reservationId' => $reservation->id(),
                'productId' => $reservation->productId(),
                'quantity' => $reservation->quantity()
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

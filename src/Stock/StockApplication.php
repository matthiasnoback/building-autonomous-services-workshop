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

        $balance->makeReservation($_POST['reservationId'], (int)$_POST['quantity']);

        $events = $balance->releaseEvents();

        Database::persist($balance);

        foreach ($events as $event) {
            list($messageType, $messageData) = $event;

            Stream::produce($messageType, $messageData);
        }
    }

    public function commitStockReservationController(): void
    {
        /** @var Balance $balance */
        $balance = Database::retrieve(Balance::class, $_POST['productId']);
        $balance->commitReservation($_POST['reservationId']);
        Database::persist($balance);
    }
}

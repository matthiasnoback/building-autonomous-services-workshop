<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\Database;
use Common\Render;
use Common\Stream\Stream;
use Common\Web\HttpApi;

final class StockApplication
{
    public function stockLevelsController(): void
    {
        $stockLevels = $this->calculateStockLevels();

        Render::jsonOrHtml($stockLevels);
    }

    private function calculateStockLevels(): array
    {
        $stockLevels = [];

        $purchaseOrders = HttpApi::fetchDecodedJsonResponse('http://purchase_web/listPurchaseOrders');
        foreach ($purchaseOrders as $purchaseOrder) {
            if (!$purchaseOrder->received) {
                continue;
            }

            $stockLevels[$purchaseOrder->productId] = ($stockLevels[$purchaseOrder->productId] ?? 0) + $purchaseOrder->quantity;
        }

        $salesOrders = HttpApi::fetchDecodedJsonResponse('http://sales_web/listSalesOrders');
        foreach ($salesOrders as $salesOrder) {
            if (!$salesOrder->wasDelivered) {
                continue;
            }

            $stockLevels[$salesOrder->productId] = ($stockLevels[$salesOrder->productId] ?? 0) - $salesOrder->quantity;
        }

        return $stockLevels;
    }

    /**
     * Note: this controller will become useful in Assignment 5
     */
    public function makeStockReservationController(): void
    {
        /** @var Balance $balance */
        $balance = Database::retrieve(Balance::class, $_POST['productId']);

        if ($balance->makeReservation($_POST['reservationId'], (int)$_POST['quantity'])) {
            Database::persist($balance);

            // TODO dispatch "reservation accepted" event
        } else {
            // TODO dispatch "reservation rejected" event
        }
    }
}

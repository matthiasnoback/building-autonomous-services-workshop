<?php
declare(strict_types=1);

namespace Stock;

use Common\Render;
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

        $receipts = HttpApi::fetchDecodedJsonResponse('http://purchase_web/listReceipts');
        foreach ($receipts as $receipt) {
            foreach ($receipt->lines as $line) {
                $stockLevels[$line->productId] = ($stockLevels[$line->productId] ?? 0) + $line->quantity;
            }
        }

        $salesOrders = HttpApi::fetchDecodedJsonResponse('http://sales_web/listSalesOrders');
        foreach ($salesOrders as $salesOrder) {
            if (!$salesOrder->wasDelivered) {
                continue;
            }

            foreach ($salesOrder->lines as $line) {
                $stockLevels[$line->productId] = ($stockLevels[$line->productId] ?? 0) - $line->quantity;
            }
        }

        return $stockLevels;
    }
}

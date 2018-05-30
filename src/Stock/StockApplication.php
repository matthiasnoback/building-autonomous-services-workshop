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
}

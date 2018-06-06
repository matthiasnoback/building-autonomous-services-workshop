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
}

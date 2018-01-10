<?php
declare(strict_types=1);

namespace Sales;

final class SalesOrder
{
    /**
     * @var int
     */
    private $purchaseOrderId;

    /**
     * @var SalesOrderLine[]
     */
    private $lines;

    public function __construct(int $purchaseOrderId, array $lines)
    {
        $this->purchaseOrderId = $purchaseOrderId;
        $this->lines = $lines;
    }

    public function id(): int
    {
        return $this->purchaseOrderId;
    }

    /**
     * @return SalesOrderLine[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

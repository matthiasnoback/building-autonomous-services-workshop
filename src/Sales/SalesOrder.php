<?php
declare(strict_types=1);

namespace Sales;

final class SalesOrder
{
    /**
     * @var int
     */
    private $salesOrderId;

    /**
     * @var SalesOrderLine[]
     */
    private $lines;

    public function __construct(int $salesOrderId, array $lines)
    {
        $this->salesOrderId = $salesOrderId;
        $this->lines = $lines;
    }

    public function id(): int
    {
        return $this->salesOrderId;
    }

    /**
     * @return SalesOrderLine[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

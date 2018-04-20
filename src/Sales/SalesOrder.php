<?php
declare(strict_types=1);

namespace Sales;

use Assert\Assertion;

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

    /**
     * @var bool
     */
    private $wasDelivered;

    public function __construct(int $salesOrderId, array $lines)
    {
        Assertion::allIsInstanceOf($lines, SalesOrderLine::class);

        $this->salesOrderId = $salesOrderId;
        $this->lines = $lines;
        $this->wasDelivered = false;
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

    public function wasDelivered(): bool
    {
        return $this->wasDelivered;
    }

    public function deliver(): void
    {
        $this->wasDelivered = true;
    }
}

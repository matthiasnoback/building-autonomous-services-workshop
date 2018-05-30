<?php
declare(strict_types=1);

namespace Sales;

use Assert\Assertion;

final class SalesOrder
{
    /**
     * @var string
     */
    private $salesOrderId;

    /**
     * @var string
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var bool
     */
    private $wasDelivered;

    public function __construct(SalesOrderId $salesOrderId, string $productId, int $quantity)
    {
        $this->salesOrderId = (string)$salesOrderId;

        Assertion::uuid($productId);
        $this->productId = $productId;

        Assertion::greaterThan($quantity, 0);
        $this->quantity = $quantity;

        $this->wasDelivered = false;
    }

    public function id(): string
    {
        return $this->salesOrderId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
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

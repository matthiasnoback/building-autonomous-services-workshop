<?php
declare(strict_types=1);

namespace Sales;

final class SalesOrderLine
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(string $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

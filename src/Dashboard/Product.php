<?php
declare(strict_types=1);

namespace Dashboard;

final class Product
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $stockLevel;

    public function __construct(string $productId, string $name)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->stockLevel = 0;
    }

    public function id(): string
    {
        return $this->productId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function stockLevel(): int
    {
        return $this->stockLevel;
    }

    public function increaseStockLevel(int $quantity): void
    {
        $this->stockLevel += $quantity;
    }

    public function decreaseStockLevel(int $quantity): void
    {
        $this->stockLevel -= $quantity;
    }
}

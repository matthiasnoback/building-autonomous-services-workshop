<?php
declare(strict_types=1);

namespace Catalog;

final class Product
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int|null
     */
    private $minimumStockLevel;

    /**
     * @param int $productId
     * @param string $name
     * @param int|null $minimumStockLevel
     */
    public function __construct(int $productId, string $name, ?int $minimumStockLevel)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->minimumStockLevel = $minimumStockLevel;
    }

    public function id(): int
    {
        return  $this->productId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function minimumStockLevel(): ?int
    {
        return $this->minimumStockLevel;
    }
}

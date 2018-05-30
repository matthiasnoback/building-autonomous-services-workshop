<?php
declare(strict_types=1);

namespace Catalog;

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
     * @var int|null
     */
    private $minimumStockLevel;

    /**
     * @param ProductId $productId
     * @param string $name
     * @param int|null $minimumStockLevel
     */
    public function __construct(ProductId $productId, string $name, ?int $minimumStockLevel)
    {
        $this->productId = (string)$productId;
        $this->name = $name;
        $this->minimumStockLevel = $minimumStockLevel;
    }

    public function id(): string
    {
        return $this->productId;
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

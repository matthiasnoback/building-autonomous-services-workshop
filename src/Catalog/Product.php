<?php
declare(strict_types=1);

namespace Catalog;

/**
 * Before you can purchase or sell any product, you first need to register add
 * it to the catalog. It needs an ID and a name.
 */
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
     * @param ProductId $productId
     * @param string $name
     */
    public function __construct(ProductId $productId, string $name)
    {
        $this->productId = (string)$productId;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->productId;
    }

    public function name(): string
    {
        return $this->name;
    }
}

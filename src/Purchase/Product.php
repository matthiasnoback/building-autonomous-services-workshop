<?php
declare(strict_types=1);

namespace Purchase;

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

    public function __construct(string $productId, string $name)
    {
        $this->productId = $productId;
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

<?php
declare(strict_types=1);

namespace Purchase;

final class Product
{
    /**
     * @var int
     */
    public $productId;

    /**
     * @var string
     */
    public $name;

    public function id(): int
    {
        return $this->productId;
    }
}

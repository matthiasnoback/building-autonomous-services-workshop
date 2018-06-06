<?php
declare(strict_types=1);

namespace Purchase;

use Assert\Assertion;
use Common\Persistence\IdentifiableObject;

final class Product implements IdentifiableObject
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
        Assertion::uuid($productId);
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

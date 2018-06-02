<?php
declare(strict_types=1);

namespace Catalog;

use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_an_id_and_a_name(): void
    {
        $productId = ProductId::create();
        $name = 'Name';

        $product = new Product($productId, $name);

        self::assertEquals((string)$productId, $product->id());
        self::assertEquals($name, $product->name());
    }
}

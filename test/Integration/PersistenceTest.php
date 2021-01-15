<?php
declare(strict_types=1);

namespace Integration;

use Catalog\Product;
use Catalog\ProductId;
use Common\Persistence\Database;
use Common\Persistence\IdentifiableObject;
use Generator;
use PHPUnit\Framework\TestCase;
use Purchase\PurchaseOrder;
use Purchase\PurchaseOrderId;
use Sales\OrderStatus;
use Sales\SalesOrder;
use Sales\SalesOrderId;
use Stock\Balance;

final class PersistenceTest extends TestCase
{
    /**
     * @test
     * @dataProvider entities
     */
    public function all_entities_can_be_persisted(IdentifiableObject $entity): void
    {
        Database::persist($entity);

        $retrieved = Database::retrieve(get_class($entity), $entity->id());

        self::assertEquals($entity, $retrieved);
    }

    public function entities(): Generator
    {
        // Testing initial versions of each entity
        yield [new Product(ProductId::create(), 'Name')];

        yield [new PurchaseOrder(PurchaseOrderId::create(), ProductId::create()->asString(), 10)];

        yield [new SalesOrder(SalesOrderId::create(), ProductId::create()->asString(), 10)];

        yield [new OrderStatus(SalesOrderId::create()->asString())];

        yield [new Balance(ProductId::create()->asString())];

        // Testing modified versions of each entity
        $orderStatus = new OrderStatus(SalesOrderId::create()->asString());
        $orderStatus->setPurchaseOrderId(PurchaseOrderId::create()->asString());
        yield [$orderStatus];

        $balance = new Balance(ProductId::create()->asString());
        $balance->increase(10);
        yield [$balance];

        $balance = new Balance(ProductId::create()->asString());
        $balance->increase(10);
        $balance->decrease(5);
        yield [$balance];
    }
}

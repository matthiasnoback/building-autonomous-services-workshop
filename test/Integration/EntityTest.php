<?php
declare(strict_types=1);

namespace Test\Integration;

use Common\Persistence\Database;
use Common\Persistence\IdentifiableObject;
use PHPUnit\Framework\TestCase;
use function get_class;

abstract class EntityTest extends TestCase
{
    /**
     * @return IdentifiableObject The object that needs persisting
     */
    abstract protected function getObject(): IdentifiableObject;

    /**
     * This is an integration test to verify that an entity can be persisted using `Common\Database`.
     * Strictly, it doesn't belong inside a unit test, but we allow it for the duration of this workshop.
     *
     * @test
     */
    public function it_can_be_persisted(): void
    {
        $originalObject = $this->getObject();

        Database::persist($originalObject);

        $retrieved = Database::retrieve(get_class($originalObject), $originalObject->id());

        self::assertEquals($retrieved, $originalObject);
    }
}

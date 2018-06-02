<?php
declare(strict_types=1);

namespace Test\Integration;

use Common\Persistence\Database;
use Common\Persistence\Entity;
use PHPUnit\Framework\TestCase;

abstract class EntityTest extends TestCase
{
    /**
     * @return Entity The object that needs persisting
     */
    abstract protected function getObject(): Entity;

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

        if (!method_exists($originalObject, 'id')) {
            throw new \LogicException(sprintf(
                'Entity of class "%s" should have a method "public function id(): string"',
                \get_class($originalObject)
            ));
        }

        $retrieved = Database::retrieve(\get_class($originalObject), $originalObject->id());

        self::assertEquals($retrieved, $originalObject);
    }
}

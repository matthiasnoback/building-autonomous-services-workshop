<?php
declare(strict_types=1);

namespace Test\Integration\Common\Persistence;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Common\Persistence\Database;

class DBTest extends TestCase
{
    protected function setUp(): void
    {
        Database::deleteAll(PersistableDummy::class);
    }

    /**
     * @test
     */
    public function initially_it_will_return_an_empty_list_of_objects(): void
    {
        $this->assertEquals([], Database::retrieveAll(PersistableDummy::class));
    }

    /**
     * @test
     */
    public function it_persists_and_retrieves_objects_by_their_id(): void
    {
        $id = Uuid::uuid4();
        $object = new PersistableDummy(new DummyId((string)$id));

        Database::persist($object);

        $retrievedObject = Database::retrieve(get_class($object), (string)$id);

        $this->assertEquals($object, $retrievedObject);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_for_non_persisted_objects(): void
    {
        $this->expectException(\RuntimeException::class);

        Database::retrieve(PersistableDummy::class, (string)Uuid::uuid4());
    }

    /**
     * @test
     */
    public function you_can_find_an_object_using_a_callable_filter(): void
    {
        $object1 = new PersistableDummy(new DummyId((string)Uuid::uuid4()));
        $idOfObject2 = new DummyId((string)Uuid::uuid4());
        $object2 = new PersistableDummy($idOfObject2);
        Database::persist($object1);
        Database::persist($object2);

        $found = Database::findOne(PersistableDummy::class, function (PersistableDummy $dummy) use ($idOfObject2) {
            return (string)$dummy->id() === (string)$idOfObject2;
        });

        self::assertEquals($object2, $found);
    }

    /**
     * @test
     */
    public function if_an_object_could_not_be_found_you_get_null(): void
    {
        $found = Database::findOne(PersistableDummy::class, function () {
            return false;
        });

        self::assertEquals(null, $found);
    }

    /**
     * @test
     */
    public function you_can_find_many_objects_using_a_filter(): void
    {
        $object1 = new PersistableDummy(new DummyId((string)Uuid::uuid4()));
        $object2 = new PersistableDummy(new DummyId((string)Uuid::uuid4()));
        Database::persist($object1);
        Database::persist($object2);

        $found = Database::find(PersistableDummy::class, function () {
            return true;
        });

        self::assertEquals([$object1, $object2], $found);
    }

    /**
     * @test
     */
    public function it_retrieves_all_objects_by_classname(): void
    {
        Database::persist(new PersistableDummy(new DummyId((string)Uuid::uuid4())));
        Database::persist(new PersistableDummy(new DummyId((string)Uuid::uuid4())));

        $this->assertCount(2, Database::retrieveAll(PersistableDummy::class));
    }
}

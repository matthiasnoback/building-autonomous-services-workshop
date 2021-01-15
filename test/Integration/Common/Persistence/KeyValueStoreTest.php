<?php
declare(strict_types=1);

namespace Test\Integration\Common\Persistence;

use Common\Persistence\KeyValueStore;
use PHPUnit\Framework\TestCase;

final class KeyValueStoreTest extends TestCase
{
    /**
     * @test
     */
    public function it_stores_a_value_under_the_given_key_to_make_it_retrievable_later(): void
    {
        KeyValueStore::set('key', 'value');

        self::assertSame('value', KeyValueStore::get('key'));
    }

    /**
     * @test
     */
    public function if_there_is_no_value_stored_for_the_given_key_it_returns_null(): void
    {
        self::assertSame(null, KeyValueStore::get('unknown'));
    }

    /**
     * @test
     */
    public function using_incr_you_can_increment_an_existing_counter(): void
    {
        KeyValueStore::set('key', 10);

        KeyValueStore::incr('key');

        self::assertSame(11, KeyValueStore::get('key'));
    }

    /**
     * @test
     */
    public function if_the_existing_value_is_not_an_integer_it_will_be_casted_to_it_before_incrementing_it(): void
    {
        KeyValueStore::set('key', '1notaninteger');

        KeyValueStore::incr('key');

        self::assertSame(2, KeyValueStore::get('key'));
    }

    /**
     * @test
     */
    public function if_the_value_is_not_defined_it_will_be_assumed_0_before_incrementing_it(): void
    {
        KeyValueStore::incr('undefined');

        self::assertSame(1, KeyValueStore::get('undefined'));
    }

    /**
     * @test
     */
    public function you_can_unset_keys_explicitly(): void
    {
        KeyValueStore::set('key', 'value');

        KeyValueStore::del('key');

        self::assertSame(null, KeyValueStore::get('key'));
    }

    protected function tearDown(): void
    {
        KeyValueStore::del('key');
        KeyValueStore::del('undefined');
    }
}

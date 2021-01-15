<?php
declare(strict_types=1);

namespace Test\Integration\Common\Web;

use Common\Web\FlashMessage;
use PHPUnit\Framework\TestCase;

final class FlashMessageTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    /**
     * @test
     */
    public function you_can_add_a_flash_message_to_the_list_of_messages_of_a_certain_type(): void
    {
        FlashMessage::add('messageType', 'message');

        self::assertSame(['message'], FlashMessage::get('messageType'));
    }

    /**
     * @test
     */
    public function if_no_flash_message_has_been_aded_of_a_given_type_its_list_will_be_empty(): void
    {
        self::assertSame([], FlashMessage::get('unknown'));
    }

    /**
     * @test
     */
    public function once_retrieved_the_flash_messages_disappear(): void
    {
        FlashMessage::add('messageType', 'message');

        FlashMessage::get('messageType');

        self::assertSame([], FlashMessage::get('messageType'));
    }

    /**
     * @test
     */
    public function you_can_retrieve_all_currently_known_types_as_an_array(): void
    {
        FlashMessage::add('messageType1', 'message');
        FlashMessage::add('messageType2', 'message');

        self::assertSame(['messageType1', 'messageType2'], FlashMessage::types());
    }

    /**
     * @test
     */
    public function if_no_messages_have_been_added_the_list_of_types_is_empty(): void
    {
        self::assertSame([], FlashMessage::types());
    }
}

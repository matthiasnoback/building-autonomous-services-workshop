<?php
declare(strict_types=1);

namespace Common\Web;

/**
 * Flash messages are used to show a one-time message to the user. They usually survive one redirect, and will be forgotten after they have been retrieved once.
 */
final class FlashMessage
{
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const DANGER = 'danger';

    private const SESSION_KEY = 'flash_messages';

    /**
     * Add a flash message. Message type can be anything, but preferably use one of the constants defined in this class.
     *
     * @param string $messageType
     * @param string $message
     */
    public static function add(string $messageType, string $message): void
    {
        $_SESSION[self::SESSION_KEY][$messageType][] = $message;
    }

    /**
     * Get all the flash messages of the given type and erase them at the same time.
     *
     * @param string $messageType
     * @return array|string[]
     */
    public static function get(string $messageType): array
    {
        $messages = $_SESSION[self::SESSION_KEY][$messageType] ?? [];

        unset($_SESSION[self::SESSION_KEY][$messageType]);

        return $messages;
    }

    /**
     * Get a list of all the types for which flash messages have been added.
     *
     * @return array
     */
    public static function types(): array
    {
        return array_keys($_SESSION[self::SESSION_KEY] ?? []);
    }
}

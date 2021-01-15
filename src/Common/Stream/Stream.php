<?php
declare(strict_types=1);

namespace Common\Stream;

use Common\Persistence\Filesystem;
use function Safe\realpath;

/**
 * Using `Stream::produce()` you can write lines to a file. Using
 * `Stream::consume()` you can consume these lines one by one, starting with
 * the first line ever produced.
 */
final class Stream
{
    private const ENV_STREAM_FILE_PATH = 'STREAM_FILE_PATH';

    /**
     * @param callable $callback
     * @param int $startAtIndex
     * @return void
     * @see Consumer::consume()
     */
    public static function consume(callable $callback, int $startAtIndex = 0): void
    {
        (new Consumer(self::getStreamFilePath()))->consume($callback, $startAtIndex);
    }

    /**
     * @param string $messageType
     * @param mixed $data
     * @return void
     * @see Producer::produce()
     */
    public static function produce(string $messageType, $data): void
    {
        (new Producer(self::getStreamFilePath()))->produce($messageType, $data);
    }

    private static function getStreamFilePath(): string
    {
        $streamFilePath = getenv(self::ENV_STREAM_FILE_PATH);
        if ($streamFilePath === false) {
            throw new \RuntimeException(sprintf('Environment variable "%s" should be set', self::ENV_STREAM_FILE_PATH));
        }

        if (!is_file($streamFilePath)) {
            Filesystem::ensureFilePathIsWritable($streamFilePath);
            touch($streamFilePath);
        }

        return realpath($streamFilePath);
    }
}

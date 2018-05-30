<?php
declare(strict_types=1);

use function Common\CommandLine\line;
use function Common\CommandLine\stdout;
use Common\Persistence\KeyValueStore;
use Common\Stream\Stream;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

/*
 * This is a demo consumer which consumes every message from the stream,
 * starting at a given index. It uses the KeyValueStore to remember the
 * index at which to start consuming when the consumer has to be
 * restarted (possibly after a crash). This effectively makes this consumer
 * consume every message *only once*.
 *
 * If you want to visually keep track of the stream, run:
 *
 *   make logs
 */

// the key to use when storing the current message index
$startAtIndexKey = $startAtIndexKey = basename(__DIR__) . '_start_at_index';;

$startAtIndex = KeyValueStore::get($startAtIndexKey) ?: 0;
stdout(line('Start consuming at index', ':', (string)$startAtIndex));

// start consuming at the given index, and keep consuming incoming messages
Stream::consume(
    function(string $messageType, $data) use ($startAtIndexKey) {
        // do something with the message, or decided to ignore it based on its type
        stdout(line($messageType, ':', json_encode($data)));

        // increase the "start at index" value, so we won't consume this message again
        KeyValueStore::incr($startAtIndexKey);
    },
    $startAtIndex
);

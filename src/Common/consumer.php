<?php
declare(strict_types=1);

use Common\Stream\Stream;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../vendor/autoload.php';

Debug::enable();

/*
 * This is a demo consumer which consumes every message from the stream.
 * It prints the message to stdout. Hence, if you want to visually keep
 * track of the stream, run:
 *
 *   docker-compose logs -f consumer
 */
Stream::consume(function(string $messageType, $data) {
    echo $messageType . ': ' . json_encode($data) . "\n";
});

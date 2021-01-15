<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Common\Stream\Stream;

Stream::consume(function (string $messageType, $data) {
    echo $messageType . "\n";
}, 1);

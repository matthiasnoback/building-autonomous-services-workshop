<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Common\Stream\Stream;

sleep(2);
Stream::produce('hello_world', 'Hello, world!');

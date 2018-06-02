<?php
declare(strict_types=1);

use Common\Web\ControllerResolver;
use Stock\StockApplication;

require __DIR__ . '/../../../vendor/autoload.php';

ControllerResolver::resolve($_SERVER, $_GET, new StockApplication())();

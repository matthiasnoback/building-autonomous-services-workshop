<?php
declare(strict_types=1);

use Common\Web\ControllerResolver;
use Dashboard\DashboardApplication;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../../../vendor/autoload.php';

Debug::enable();

ControllerResolver::resolve($_SERVER, $_GET, new DashboardApplication())();

<?php

declare(strict_types=1);

namespace PHPSTORM_META;

/**
 * This set of overrides simplifies a lot IDE autocompletion in the PhpStorm
 */
override(\Common\Persistence\Database::retrieve(), type(0));
override(\Common\Persistence\Database::findOne(), type(0));
override(\Common\Persistence\Database::retrieveAll(), map(['' => '@[]']));
override(\Common\Persistence\Database::find(), map(['' => '@[]']));

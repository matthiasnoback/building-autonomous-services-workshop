<?php
declare(strict_types = 1);

namespace Common\Persistence;

interface Id
{
    public function __toString() : string;
}

<?php
declare(strict_types = 1);

namespace Common\Persistence;

interface IdentifiableObject
{
    /**
     * @return string
     */
    public function id(): string;
}

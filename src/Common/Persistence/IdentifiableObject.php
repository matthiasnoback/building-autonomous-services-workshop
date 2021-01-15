<?php
declare(strict_types = 1);

namespace Common\Persistence;

interface IdentifiableObject
{
    /**
     * @return string|object with __toString() method
     */
    public function id();
}

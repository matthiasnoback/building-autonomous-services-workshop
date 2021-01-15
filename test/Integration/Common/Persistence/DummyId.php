<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Common\Persistence\Id;

final class DummyId implements Id
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}

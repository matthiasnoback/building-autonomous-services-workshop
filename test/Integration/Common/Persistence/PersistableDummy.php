<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Common\Persistence\IdentifiableObject;

final class PersistableDummy implements IdentifiableObject
{
    private string $id;

    private string $secretValue;

    public function __construct(DummyId $id)
    {
        $this->id = (string)$id;
        $this->secretValue = uniqid('', true);
    }

    public function id() : string
    {
        return $this->id;
    }
}

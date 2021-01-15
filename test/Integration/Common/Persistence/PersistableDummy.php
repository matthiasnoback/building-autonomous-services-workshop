<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Common\Persistence\IdentifiableObject;
use Common\Persistence\Id;

final class PersistableDummy implements IdentifiableObject
{
    private DummyId $id;

    private string $secretValue;

    public function __construct(DummyId $id)
    {
        $this->id = $id;
        $this->secretValue = uniqid('', true);
    }

    public function id() : Id
    {
        return $this->id;
    }
}

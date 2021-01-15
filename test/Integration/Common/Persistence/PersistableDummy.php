<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Common\Persistence\IdentifiableObject;

final class PersistableDummy implements IdentifiableObject
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
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

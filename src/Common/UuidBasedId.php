<?php
declare(strict_types=1);

namespace Common;

use Assert\Assertion;
use Ramsey\Uuid\Uuid;

trait UuidBasedId
{
    /**
     * @var string
     */
    private $id;

    private function __construct(string $id)
    {
        Assertion::uuid($id);
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }

    /**
     * Creating the ID based on a randomly generated UUID4 string should happen inside the repository's implementation
     * of a `nextIdentity()` method of some sorts. For this workshop, creating it here is more convenient.
     *
     * @return self
     */
    public static function create(): self
    {
        return new self(Uuid::uuid4()->toString());
    }
}

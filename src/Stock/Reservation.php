<?php
declare(strict_types=1);

namespace Stock;

/**
 * Note: this class will become relevant in assignment 05
 */
final class Reservation
{
    /**
     * @var string
     */
    private $reservationId;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(string $reservationId, int $quantity)
    {
        $this->reservationId = $reservationId;
        $this->quantity = $quantity;
    }

    public function reservationId(): string
    {
        return $this->reservationId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

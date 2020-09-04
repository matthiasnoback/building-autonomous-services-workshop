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

    /**
     * @var string
     */
    private $status;

    public function __construct(string $reservationId, int $quantity)
    {
        $this->reservationId = $reservationId;
        $this->quantity = $quantity;
        $this->status = 'initial';
    }

    public function reservationId(): string
    {
        return $this->reservationId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function reject(): void
    {
        $this->status = 'rejected';
    }

    public function accept(): void
    {
        $this->status = 'accepted';
    }
}

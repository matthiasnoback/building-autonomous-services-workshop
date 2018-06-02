<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\Entity;

final class Balance implements Entity
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var int
     */
    private $stockLevel;

    /**
     * @var Reservation[]
     */
    private $reservations = [];

    public function __construct(string $productId)
    {
        $this->productId = $productId;
        $this->stockLevel = 0;
    }

    public function id(): string
    {
        return $this->productId;
    }

    public function stockLevel(): int
    {
        return $this->stockLevel;
    }

    public function increase(int $receivedQuantity): void
    {
        $this->stockLevel += $receivedQuantity;
    }

    public function decrease(int $deliveredQuantity): void
    {
        $this->stockLevel -= $deliveredQuantity;
    }

    public function makeReservation(string $reservationId, int $quantity): bool
    {
        if ($this->stockLevel >= $quantity) {
            $this->reservations[] = new Reservation($reservationId, $quantity);
            $this->decrease($quantity);
            return true;
        }

        return false;
    }

    public function commitReservation(string $reservationId): void
    {
        foreach ($this->reservations as $key => $reservation) {
            if ($reservation->reservationId() === $reservationId) {
                unset($this->reservations[$key]);
            }
        }
    }

    public function hasReservation(string $reservationId): bool
    {
        foreach ($this->reservations as $reservation) {
            if ($reservation->reservationId() === $reservationId) {
                return true;
            }
        }

        return false;
    }
}

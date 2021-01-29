<?php
declare(strict_types=1);

namespace Stock;

use Common\Persistence\IdentifiableObject;

/**
 * Note: this class will become relevant in assignment 02
 */
final class Balance implements IdentifiableObject
{
    private string $productId;

    private int $stockLevel;

    /**
     * @var Reservation[]
     */
    private array $reservations = [];

    /**
     * @var Reservation[]
     */
    private array $rejectedReservations = [];

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

    /**
     * Note this method will become relevant in assignment 05
     */
    public function makeReservation(string $reservationId, int $quantity): bool
    {
        if ($this->stockLevel >= $quantity) {
            $this->reservations[] = new Reservation($reservationId, $quantity);
            $this->decrease($quantity);
            return true;
        } else {
            $this->rejectedReservations[] = new Reservation($reservationId, $quantity);
            return false;
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

    /**
     * This will be useful in assignment 10
     */
    public function processReceivedGoodsAndRetryRejectedReservations(int $quantity): ?string
    {
        foreach ($this->rejectedReservations as $key => $rejectedReservation) {
            if ($rejectedReservation->quantity() <= $quantity) {
                unset($this->reservations[$key]);
                $this->increase($quantity - $rejectedReservation->quantity());

                return $rejectedReservation->reservationId();
            }
        }

        $this->increase($quantity);

        return null;
    }
}

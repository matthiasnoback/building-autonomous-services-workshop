<?php
declare(strict_types=1);

namespace Stock;

final class Reservation
{
    /**
     * @var string
     */
    private $reservationId;

    /**
     * @var string
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(string $reservationId, string $productId, int $quantity)
    {
        $this->reservationId = $reservationId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function id(): string
    {
        return $this->reservationId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

<?php
declare(strict_types=1);

namespace Purchase;

final class PurchaseOrderLine
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var int
     */
    private $quantityOrdered;

    /**
     * @var int
     */
    private $quantityReceived;

    public function __construct(string $productId, int $quantityOrdered)
    {
        $this->productId = $productId;
        $this->quantityOrdered = $quantityOrdered;
        $this->quantityReceived = 0;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantityOrdered(): int
    {
        return $this->quantityOrdered;
    }

    public function quantityOpen(): int
    {
        $quantityOpen = $this->quantityOrdered - $this->quantityReceived;
        if ($quantityOpen < 0) {
            $quantityOpen = 0;
        }

        return $quantityOpen;
    }

    public function processReceipt(int $receiptQuantity): void
    {
        $this->quantityReceived += $receiptQuantity;
    }

    public function isOpen(): bool
    {
        return $this->quantityOpen() > 0;
    }
}

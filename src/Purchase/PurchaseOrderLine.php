<?php
declare(strict_types=1);

namespace Purchase;

final class PurchaseOrderLine
{
    /**
     * @var int
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

    /**
     * @param int $productId
     * @param int $quantityOrdered
     */
    public function __construct(int $productId, int $quantityOrdered)
    {
        $this->productId = $productId;
        $this->quantityOrdered = $quantityOrdered;
        $this->quantityReceived = 0;
    }

    public function productId(): int
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

<?php
declare(strict_types=1);

namespace Purchase;

use Assert\Assertion;

final class PurchaseOrder
{
    /**
     * @var int
     */
    private $purchaseOrderId;

    /**
     * @var PurchaseOrderLine[]
     */
    private $lines;

    public function __construct(int $purchaseOrderId, array $lines)
    {
        Assertion::allIsInstanceOf($lines, PurchaseOrderLine::class);

        $this->purchaseOrderId = $purchaseOrderId;
        $this->lines = $lines;
    }

    public function id(): int
    {
        return $this->purchaseOrderId;
    }

    public function processReceipt(int $productId, int $receiptQuantity): void
    {
        foreach ($this->lines as $line) {
            if ($line->productId() === $productId) {
                $line->processReceipt($receiptQuantity);
            }
        }
    }

    public function isOpen(): bool
    {
        foreach ($this->lines as $line) {
            if ($line->isOpen()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PurchaseOrderLine[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

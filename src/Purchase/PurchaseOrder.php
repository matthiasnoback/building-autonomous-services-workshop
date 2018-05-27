<?php
declare(strict_types=1);

namespace Purchase;

use Assert\Assertion;

final class PurchaseOrder
{
    /**
     * @var string
     */
    private $purchaseOrderId;

    /**
     * @var PurchaseOrderLine[]
     */
    private $lines;

    public function __construct(PurchaseOrderId $purchaseOrderId, array $lines)
    {
        Assertion::allIsInstanceOf($lines, PurchaseOrderLine::class);

        $this->purchaseOrderId = (string)$purchaseOrderId;
        $this->lines = $lines;
    }

    public function id(): string
    {
        return $this->purchaseOrderId;
    }

    public function processReceipt(string $productId, int $receiptQuantity): void
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

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
     * @var string
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var bool
     */
    private $received = false;

    public function __construct(PurchaseOrderId $purchaseOrderId, string $productId, int $quantity)
    {
        $this->purchaseOrderId = (string)$purchaseOrderId;

        Assertion::uuid($productId);
        $this->productId = $productId;

        Assertion::greaterThan($quantity, 0);
        $this->quantity = $quantity;
    }

    public function id(): string
    {
        return $this->purchaseOrderId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function markAsReceived(): void
    {
        $this->received = true;
    }

    public function isOpen(): bool
    {
        return !$this->received;
    }
}

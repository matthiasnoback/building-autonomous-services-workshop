<?php
declare(strict_types=1);

namespace Purchase;

use Assert\Assertion;
use Common\Persistence\IdentifiableObject;

/**
 * A simplified version of the real-world "Purchase Order". It represents an
 * order for an external supplier. When creating a purchase order, you specify
 * a product by its ID and a desired quantity.
 *
 * When the warehouse physically receives a package with the product you ordered,
 * you can mark the purchase order as "received". Once you have received the given
 * quantity of the ordered product, the stock level of this product will be
 * increased by the same quantity that you ordered.
 */
final class PurchaseOrder implements IdentifiableObject
{
    private string $purchaseOrderId;

    private string $productId;

    private int $quantity;

    private bool $received = false;

    public function __construct(PurchaseOrderId $purchaseOrderId, string $productId, int $quantity)
    {
        $this->purchaseOrderId = $purchaseOrderId->asString();

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

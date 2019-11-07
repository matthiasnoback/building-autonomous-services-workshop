<?php
declare(strict_types=1);

namespace Sales;

use Assert\Assertion;
use Common\Persistence\IdentifiableObject;
use LogicException;


/**
 * A simplified version of a real-world "Sales Order". It represents an order
 * which you can create when a customer wants to order a certain quantity of
 * one of the products you sell.
 *
 * When the warehouse has physically prepared the order by putting the ordered
 * quantity of the product in a package that's ready for transport, you can mark
 * the corresponding sales order as "delivered". From that moment on, the stock
 * level will be decreased by the quantity of the product you delivered.
 */
final class SalesOrder implements IdentifiableObject
{
    /**
     * @var string
     */
    private $salesOrderId;

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
    private $wasDelivered;

    /**
     * @var bool
     */
    private $isDeliverable = false;

    public function __construct(SalesOrderId $salesOrderId, string $productId, int $quantity)
    {
        $this->salesOrderId = (string)$salesOrderId;

        Assertion::uuid($productId);
        $this->productId = $productId;

        Assertion::greaterThan($quantity, 0);
        $this->quantity = $quantity;

        $this->wasDelivered = false;
    }

    public function id(): string
    {
        return $this->salesOrderId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function wasDelivered(): bool
    {
        return $this->wasDelivered;
    }

    public function deliver(): void
    {
        if (!$this->isDeliverable) {
            throw new LogicException('This sales order should first be marked as deliverable before it can be delivered.');
        }
        $this->wasDelivered = true;
    }

    public function markAsDeliverable(): void
    {
        $this->isDeliverable = true;
    }

    public function isDeliverable(): bool
    {
        return $this->isDeliverable;
    }
}

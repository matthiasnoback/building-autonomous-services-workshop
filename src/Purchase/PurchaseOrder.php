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

    /**
     * @var bool
     */
    private $received = false;

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

    public function markAsReceived(): void
    {
        $this->received = true;
    }

    public function isOpen(): bool
    {
        return !$this->received;
    }

    /**
     * @return PurchaseOrderLine[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

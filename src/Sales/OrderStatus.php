<?php
declare(strict_types=1);

namespace Sales;

/**
 * Note: this class will become relevant in assignment 06
 */
final class OrderStatus
{
    /**
     * @var string
     */
    private $salesOrderId;

    /**
     * @var string
     */
    private $purchaseOrderId;

    public function __construct(string $salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
    }

    public function id(): string
    {
        return $this->salesOrderId;
    }

    public function setPurchaseOrderId(string $purchaseOrderId): void
    {
        $this->purchaseOrderId = $purchaseOrderId;
    }

    public function purchaseOrderId(): ?string
    {
        return $this->purchaseOrderId;
    }
}

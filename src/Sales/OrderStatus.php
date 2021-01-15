<?php
declare(strict_types=1);

namespace Sales;

use Common\Persistence\IdentifiableObject;

/**
 * Note: this class will become relevant in assignment 06
 */
final class OrderStatus implements IdentifiableObject
{
    /**
     * @var string
     */
    private string $salesOrderId;

    /**
     * @var string
     */
    private string $purchaseOrderId;

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

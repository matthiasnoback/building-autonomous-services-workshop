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
    private $salesOrderId;

    /**
     * @var string
     */
    private $purchaseOrderId;

    /**
     * @var string
     */
    private $status;

    public function __construct(string $salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
        $this->status = 'Created';
    }

    public function id(): string
    {
        return $this->salesOrderId;
    }

    public function awaitingGoodsReceived(string $purchaseOrderId): void
    {
        $this->purchaseOrderId = $purchaseOrderId;
        $this->status = 'Awaiting goods received';
    }

    public function purchaseOrderId(): ?string
    {
        return $this->purchaseOrderId;
    }

    public function awaitingStockReservation(): void
    {
        $this->status = 'Awaiting stock reservation';
    }

    public function deliverable(): void
    {
        $this->status = 'Deliverable';
    }

    public function delivered(): void
    {
        $this->status = 'Delivered';
    }

    public function status(): string
    {
        return $this->status;
    }
}

<?php
declare(strict_types=1);

namespace Sales;

use Assert\Assertion;
use Common\Persistence\IdentifiableObject;

/**
 * Note: this class will become relevant in assignment 06
 */
final class OrderStatus implements IdentifiableObject
{
    private const SALES_ORDER_CREATED = 'sales_order_created';
    private const AWAITING_STOCK_RESERVATION = 'awaiting_stock_reservation';
    private const AWAITING_GOODS_RECEIVED = 'awaiting_goods_received';
    private const SALES_ORDER_DELIVERED = 'sales_order_delivered';

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

    /**
     * @var int
     */
    private $lastUpdated;

    public function __construct(string $salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
        $this->setStatus(self::SALES_ORDER_CREATED, []);
    }

    public function awaitingStockReservation(): void
    {
        $this->setStatus(self::AWAITING_STOCK_RESERVATION, [self::SALES_ORDER_CREATED, self::AWAITING_GOODS_RECEIVED]);
    }

    public function awaitingGoodsReceived(string $purchaseOrderId): void
    {
        $this->purchaseOrderId = $purchaseOrderId;
        $this->setStatus(self::AWAITING_GOODS_RECEIVED, [self::AWAITING_STOCK_RESERVATION]);
    }

    public function salesOrderDelivered(): void
    {
        $this->setStatus(self::SALES_ORDER_DELIVERED, [self::AWAITING_STOCK_RESERVATION]);
    }

    public function id(): string
    {
        return $this->salesOrderId;
    }

    public function purchaseOrderId(): ?string
    {
        return $this->purchaseOrderId;
    }

    private function setStatus($status, array $allowFromPreviousStatuses): void
    {
        if (\count($allowFromPreviousStatuses) > 0) {
            Assertion::inArray($this->status, $allowFromPreviousStatuses);
        }

        $this->status = $status;
        $this->lastUpdated = time();
    }

    public function lastUpdated(): string
    {
        return date('Y-m-d H:i:s', $this->lastUpdated);
    }

    public function status(): string
    {
        return $this->status;
    }
}

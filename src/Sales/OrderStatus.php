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

    public function __construct(string $salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
        $this->status = self::SALES_ORDER_CREATED;
    }

    public function awaitingStockReservation(): void
    {
        $this->assertStatusIn([self::SALES_ORDER_CREATED, self::AWAITING_GOODS_RECEIVED]);

        $this->status = self::AWAITING_STOCK_RESERVATION;
    }

    public function awaitingGoodsReceived(string $purchaseOrderId): void
    {
        $this->assertStatusIn([self::AWAITING_STOCK_RESERVATION]);

        $this->purchaseOrderId = $purchaseOrderId;
        $this->status = self::AWAITING_GOODS_RECEIVED;
    }

    public function salesOrderDelivered(): void
    {
        $this->assertStatusIn([self::AWAITING_STOCK_RESERVATION]);

        $this->status = self::SALES_ORDER_DELIVERED;
    }

    public function id(): string
    {
        return $this->salesOrderId;
    }

    public function purchaseOrderId(): ?string
    {
        return $this->purchaseOrderId;
    }

    private function assertStatusIn(array $possibleStatuses): void
    {
        Assertion::inArray($this->status, $possibleStatuses);
    }
}

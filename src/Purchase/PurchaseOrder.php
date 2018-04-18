<?php
declare(strict_types=1);

namespace Purchase;

final class PurchaseOrder
{
    /**
     * @var int
     */
    private $purchaseOrderId;

    /**
     * @var PurchaseOrderLine[]
     */
    private $lines;

    public function __construct(int $purchaseOrderId, array $lines)
    {
        $this->purchaseOrderId = $purchaseOrderId;
        $this->lines = $lines;
    }

    public function id(): int
    {
        return $this->purchaseOrderId;
    }

    /**
     * @return PurchaseOrderLine[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

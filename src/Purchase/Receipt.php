<?php
declare(strict_types=1);

namespace Purchase;

final class Receipt
{
    /**
     * @var int
     */
    private $receiptId;

    /**
     * @var int
     */
    private $purchaseOrderId;

    /**
     * @var ReceiptItem[]
     */
    private $lines;

    public function __construct(int $receiptId, int $purchaseOrderId, array $lines)
    {
        $this->receiptId = $receiptId;
        $this->purchaseOrderId = $purchaseOrderId;
        $this->lines = $lines;
    }

    public function id(): int
    {
        return $this->receiptId;
    }

    /**
     * @return int
     */
    public function purchaseOrderId()
    {
        return $this->purchaseOrderId;
    }

    /**
     * @return ReceiptItem[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

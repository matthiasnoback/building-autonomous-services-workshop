<?php
declare(strict_types=1);

namespace Purchase;

final class GoodsReceipt
{
    /**
     * @var int
     */
    private $receiptId;

    /**
     * @var ReceiptItem[]
     */
    private $lines;

    public function __construct(int $receiptId, array $lines)
    {
        $this->receiptId = $receiptId;
        $this->lines = $lines;
    }

    public function id(): int
    {
        return $this->receiptId;
    }

    /**
     * @return ReceiptItem[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

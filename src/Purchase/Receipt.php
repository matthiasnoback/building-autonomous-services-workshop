<?php
declare(strict_types=1);

namespace Purchase;

use Assert\Assertion;

final class Receipt
{
    /**
     * @var string
     */
    private $receiptId;

    /**
     * @var int
     */
    private $purchaseOrderId;

    /**
     * @var ReceiptLine[]
     */
    private $lines;

    public function __construct(ReceiptId $receiptId, int $purchaseOrderId, array $lines)
    {
        Assertion::allIsInstanceOf($lines, ReceiptLine::class);

        $this->receiptId = (string)$receiptId;
        $this->purchaseOrderId = $purchaseOrderId;
        $this->lines = $lines;
    }

    public function id(): string
    {
        return $this->receiptId;
    }

    /**
     * @return int
     */
    public function purchaseOrderId(): int
    {
        return $this->purchaseOrderId;
    }

    /**
     * @return ReceiptLine[]
     */
    public function lines(): array
    {
        return $this->lines;
    }
}

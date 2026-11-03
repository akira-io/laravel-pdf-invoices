<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalRow
{
    private const float TAX_LINE_HEIGHT = 12.0;

    /**
     * @param  array<int, string>  $taxes
     */
    public function __construct(
        public string $code,
        public string $description,
        public string $quantity,
        public string $unitPrice,
        public array $taxes,
        public string $total,
        public string $amount,
        public float $y = 0.0,
    ) {}

    public function at(float $y): self
    {
        return new self(
            $this->code,
            $this->description,
            $this->quantity,
            $this->unitPrice,
            $this->taxes,
            $this->total,
            $this->amount,
            $y,
        );
    }

    public function taxHeight(): float
    {
        return (count($this->taxes) - 1) * self::TAX_LINE_HEIGHT;
    }
}

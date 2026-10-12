<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalRow
{
    public function __construct(
        public string $code,
        public string $description,
        public string $quantity,
        public string $unitPrice,
        public string $tax,
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
            $this->tax,
            $this->total,
            $this->amount,
            $y,
        );
    }
}

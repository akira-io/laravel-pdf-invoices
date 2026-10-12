<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalLine
{
    public function __construct(
        public string $label,
        public string $value,
        public float $y = 0.0,
        public float $height = 0.0,
    ) {}

    public function at(float $y, float $height = 0.0): self
    {
        return new self($this->label, $this->value, $y, $height);
    }
}

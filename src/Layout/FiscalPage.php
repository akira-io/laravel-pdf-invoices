<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalPage
{
    /**
     * @param  array<int, FiscalRow>  $rows
     */
    public function __construct(
        public int $number,
        public bool $continuation,
        public ?float $tableY,
        public array $rows = [],
        public ?string $carryForward = null,
        public ?FiscalSummary $summary = null,
    ) {}
}

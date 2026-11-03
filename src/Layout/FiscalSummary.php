<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalSummary
{
    /**
     * @param  array<int, FiscalLine>  $totals
     * @param  array<int, FiscalLine>  $conversion
     * @param  array<int, string>  $notes
     */
    public function __construct(
        public array $totals,
        public array $conversion,
        public ?float $conversionY,
        public array $notes,
        public ?float $notesY,
        public float $verificationY,
        public ?string $qrCode,
    ) {}
}

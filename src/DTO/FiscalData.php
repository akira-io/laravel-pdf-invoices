<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

use Akira\PdfInvoices\Enums\EmissionMode;

final readonly class FiscalData
{
    /**
     * @param  array<int, string>  $notes
     */
    public function __construct(
        public ?string $documentType = null,
        public ?string $documentTitle = null,
        public ?string $series = null,
        public ?string $number = null,
        public EmissionMode $emissionMode = EmissionMode::Online,
        public ?string $identifier = null,
        public ?string $qrPayload = null,
        public ?string $contingencyCode = null,
        public array $notes = [],
    ) {}
}

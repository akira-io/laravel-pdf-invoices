<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalParty
{
    public function __construct(
        public string $title,
        public string $name,
        public string $taxId,
        public string $address,
        public string $contact,
    ) {}
}

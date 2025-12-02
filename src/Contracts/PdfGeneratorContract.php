<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Contracts;

use Akira\PdfInvoices\DTO\InvoiceData;

interface PdfGeneratorContract
{
    public function generate(InvoiceData $invoice, string $template = 'modern'): string;

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string;
}

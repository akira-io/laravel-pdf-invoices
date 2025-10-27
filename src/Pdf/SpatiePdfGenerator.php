<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Pdf;

use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;
use Spatie\LaravelPdf\Facades\Pdf;

final class SpatiePdfGenerator implements PdfGeneratorContract
{
    public function __construct(
        private string $basePath = 'invoices',
    ) {}

    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        return Pdf::view("pdf-invoices::pdf.templates.{$template}", [
            'invoice' => $invoice,
        ])->render();
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        $fullPath = $this->basePath . '/' . $path;

        Pdf::view("pdf-invoices::pdf.templates.{$template}", [
            'invoice' => $invoice,
        ])->save($fullPath);

        return $fullPath;
    }
}
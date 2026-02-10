<?php

declare(strict_types=1);

use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\Pdf\SpatiePdfGenerator;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

it('forces the browsershot driver while using spatie generator', function (): void {
    Pdf::fake();
    config(['laravel-pdf.driver' => 'dompdf']);

    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new SpatiePdfGenerator;
    $generator->save($invoice, 'invoice.pdf');

    Pdf::assertSaved(fn (PdfBuilder $pdf, string $path): bool => $path === 'invoices/invoice.pdf'
        && (fn (): ?string => $this->driverName)->call($pdf) === 'browsershot');
});

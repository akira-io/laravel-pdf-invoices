<?php

declare(strict_types=1);

use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\Pdf\DompdfPdfGenerator;

it('generates pdf using dompdf', function (): void {
    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new DompdfPdfGenerator('invoices');
    $content = $generator->generate($invoice);

    expect($content)->toBeString();
});

it('saves pdf using dompdf', function (): void {
    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new DompdfPdfGenerator('invoices');
    $path = $generator->save($invoice, 'test.pdf');

    expect($path)->toBe('invoices/test.pdf');
});

it('handles non-string locale config in dompdf generator', function (): void {
    config(['pdf-invoices.localization.locale' => 123]);

    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new DompdfPdfGenerator('invoices');
    $content = $generator->generate($invoice);

    expect($content)->toBeString();

    $path = $generator->save($invoice, 'test-locale.pdf');
    expect($path)->toBe('invoices/test-locale.pdf');
});

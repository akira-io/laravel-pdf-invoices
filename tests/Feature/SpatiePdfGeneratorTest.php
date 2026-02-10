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

it('generates pdf using spatie generator', function (): void {
    Pdf::fake();

    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new SpatiePdfGenerator;
    $content = $generator->generate($invoice);

    expect($content)->toBe('fake-pdf-content');
});

it('handles non-string locale config in spatie generator', function (): void {
    Pdf::fake();
    config(['pdf-invoices.localization.locale' => 123]);

    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new SpatiePdfGenerator;
    $generator->save($invoice, 'invoice.pdf');

    Pdf::assertSaved(fn (PdfBuilder $pdf, string $path): bool => $path === 'invoices/invoice.pdf');
});

it('covers missing css path in spatie generator', function (): void {
    Pdf::fake();
    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    // Provide a non-existent CSS path
    $generator = new SpatiePdfGenerator(cssPath: '/non/existent/path.css');
    $generator->generate($invoice);

    expect(true)->toBeTrue();
});

it('generates pdf content using real driver branch in spatie generator', function (): void {
    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    // We mock the facade and builder instead of using Pdf::fake()
    // to hit the branch that calls base64_decode($builder->base64())
    $mockBuilder = Mockery::mock(PdfBuilder::class);
    $mockBuilder->shouldReceive('driver')->with('browsershot')->andReturnSelf();
    $mockBuilder->shouldReceive('base64')->andReturn(base64_encode('real-content'));

    Pdf::shouldReceive('view')->andReturn($mockBuilder);

    $generator = new SpatiePdfGenerator;
    $content = $generator->generate($invoice);

    expect($content)->toBe('real-content');

    Mockery::close();
});

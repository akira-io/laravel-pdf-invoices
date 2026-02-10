<?php

declare(strict_types=1);

use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\Pdf\SpatiePdfGenerator;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

it('forces the browsershot driver while using spatie generator', function (): void {
    config(['laravel-pdf.driver' => 'dompdf']);

    $builder = Mockery::mock(PdfBuilder::class);

    Pdf::shouldReceive('view')
        ->once()
        ->withArgs(function (string $view, array $data): bool {
            expect($view)->toBe('pdf-invoices::pdf.templates.modern');
            expect($data)->toHaveKeys(['invoice', 'compiledCss', 'translator']);

            return true;
        })
        ->andReturn($builder);

    $builder->shouldReceive('driver')
        ->once()
        ->with('browsershot')
        ->andReturnSelf();

    $builder->shouldReceive('save')
        ->once()
        ->with('invoices/invoice.pdf');

    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: 'Client'),
    );

    $generator = new SpatiePdfGenerator;
    $savedPath = $generator->save($invoice, 'invoice.pdf');

    expect($savedPath)->toBe('invoices/invoice.pdf');
});

<?php

declare(strict_types=1);

use Akira\PdfInvoices\Enums\EmissionMode;
use Akira\PdfInvoices\Pdf\DompdfPdfGenerator;
use Akira\PdfInvoices\Support\InvoiceTranslator;
use Akira\PdfInvoices\Tests\Support\EfaturaInvoiceFactory;

function efaturaHtml(Akira\PdfInvoices\DTO\InvoiceData $invoice, string $locale = 'pt_PT'): string
{
    return view('pdf-invoices::pdf.templates.efatura', [
        'invoice' => $invoice,
        'compiledCss' => '',
        'translator' => new InvoiceTranslator($locale),
    ])->render();
}

it('renders the e-Fatura template as a pdf with dompdf', function (): void {
    $pdf = new DompdfPdfGenerator()->generate(EfaturaInvoiceFactory::make(60, EmissionMode::Online, true), 'efatura');

    expect($pdf)->toStartWith('%PDF-')
        ->and(preg_match_all('#/Type\s*/Page\b(?!s)#', $pdf))->toBe(5);
});

it('renders every page with its header, facts and page number', function (): void {
    $html = efaturaHtml(EfaturaInvoiceFactory::make(30));

    expect(mb_substr_count($html, 'class="page'))->toBe(3)
        ->and($html)->toContain('Pág. 1/3')
        ->and($html)->toContain('Pág. 3/3')
        ->and(mb_substr_count($html, 'Fatura Eletrónica</div>'))->toBe(3)
        ->and(mb_substr_count($html, 'A transportar'))->toBe(2)
        ->and($html)->toContain('15 400$00')
        ->and($html)->toContain('Continuação')
        ->and($html)->toContain('IUD: '.EfaturaInvoiceFactory::IUD)
        ->and($html)->toContain('data:image/svg+xml;base64,')
        ->and($html)->toContain('48 300$00')
        ->and($html)->toContain('Plataforma Eletrónica da Fatura Eletrónica de Cabo Verde.');
});

it('renders the contingency band, conversion and notes', function (): void {
    $html = efaturaHtml(EfaturaInvoiceFactory::make(1, EmissionMode::Off, true, ['Nota fiscal']));

    expect($html)->toContain('EMITIDO EM CONTINGÊNCIA OFF - IUC: 2026/17 - Pendente de Autorização')
        ->and($html)->toContain('Conversão cambial')
        ->and($html)->toContain('1 EUR = 110,26500 CVE')
        ->and($html)->toContain('Nota fiscal')
        ->and($html)->not->toContain('data:image/svg+xml;base64,');
});

it('uses the european portuguese labels for the pt locale', function (): void {
    expect(efaturaHtml(EfaturaInvoiceFactory::make(), 'pt'))->toContain('Descrição')->toContain('Emissão:');
});

it('ships every efatura label in every locale', function (string $locale): void {
    $reference = array_keys((require __DIR__.'/../../resources/lang/en/invoice.php')['efatura']);
    $labels = array_keys((require __DIR__."/../../resources/lang/{$locale}/invoice.php")['efatura']);

    expect($labels)->toBe($reference);
})->with(['es', 'fr', 'it', 'pt', 'pt_BR', 'pt_PT']);

it('lets right aligned amounts overflow instead of clipping their leading digits', function (): void {
    expect(efaturaHtml(EfaturaInvoiceFactory::make()))->toContain('.right { text-align: right; overflow: visible; }');
});

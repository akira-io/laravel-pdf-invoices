<?php

declare(strict_types=1);

use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Enums\EmissionMode;
use Akira\PdfInvoices\Layout\FiscalLayout;
use Akira\PdfInvoices\Layout\FiscalLine;
use Akira\PdfInvoices\Support\InvoiceTranslator;
use Akira\PdfInvoices\Tests\Support\EfaturaInvoiceFactory;

function fiscalLayout(InvoiceData $invoice, string $locale = 'pt_PT'): Akira\PdfInvoices\Layout\FiscalDocument
{
    return new FiscalLayout(new InvoiceTranslator($locale))->build($invoice);
}

it('lays out a single line invoice on one page', function (): void {
    $document = fiscalLayout(EfaturaInvoiceFactory::make());
    $page = $document->pages[0];
    $row = $page->rows[0];

    expect($document->pages)->toHaveCount(1)
        ->and($document->title)->toBe('Fatura Eletrónica')
        ->and($document->subtitle)->toBe('DFA e-Fatura Cabo Verde')
        ->and($document->brand)->toBe('Akira Comércio, Lda')
        ->and($document->issuer->title)->toBe('Emissor')
        ->and($document->issuer->taxId)->toBe('NIF: 200123456')
        ->and($document->issuer->contact)->toBe('2601010')
        ->and($document->customer->contact)->toBe('cliente@example.cv')
        ->and(array_map(fn (FiscalLine $fact): string => $fact->label.' '.$fact->value, $document->facts))->toBe([
            'Tipo DFE: FTE', 'Série: A', 'Número: 42', 'Emissão: 12/10/2026 10:15:30', 'Modo: Online',
        ])
        ->and($document->contingency)->toBeNull()
        ->and($document->pageLabel($page))->toBe('Pág. 1/1')
        ->and($page->continuation)->toBeFalse()
        ->and($page->tableY)->toBe(350.0)
        ->and($page->carryForward)->toBeNull()
        ->and($row->y)->toBe(406.0)
        ->and([$row->code, $row->description, $row->quantity, $row->unitPrice, $row->tax, $row->total])
        ->toBe(['CIM-001', 'Cimento Portland saco 1', '2 UN', '700$00', '15%', '1 400$00'])
        ->and(array_map(fn (FiscalLine $line): string => $line->label.' '.$line->value, $page->summary->totals))->toBe([
            'Subtotal: 1 400$00', 'Descontos: 0$00', 'Encargos: 0$00', 'Imposto: 210$00', 'Total: 1 610$00',
        ])
        ->and($page->summary->totals[0]->y)->toBe(540.0)
        ->and($page->summary->verificationY)->toBe(665.0)
        ->and($page->summary->qrCode)->toStartWith('data:image/svg+xml;base64,')
        ->and($page->summary->conversion)->toBe([])
        ->and($page->summary->notesY)->toBeNull();
});

it('carries totals forward across pages for long invoices', function (): void {
    $document = fiscalLayout(EfaturaInvoiceFactory::make(60));
    $pages = $document->pages;

    expect($pages)->toHaveCount(5)
        ->and(array_map(fn (Akira\PdfInvoices\Layout\FiscalPage $page): int => count($page->rows), $pages))->toBe([11, 15, 15, 15, 4])
        ->and($pages[0]->carryForward)->toBe('15 400$00')
        ->and($pages[1]->carryForward)->toBe('36 400$00')
        ->and($pages[3]->carryForward)->toBe('78 400$00')
        ->and($pages[4]->carryForward)->toBeNull()
        ->and($pages[1]->continuation)->toBeTrue()
        ->and($pages[1]->tableY)->toBe(245.0)
        ->and($pages[1]->rows[0]->y)->toBe(301.0)
        ->and($pages[3]->summary)->toBeNull()
        ->and($pages[4]->summary->totals[4]->value)->toBe('96 600$00')
        ->and($document->pageLabel($pages[2]))->toBe('Pág. 3/5');
});

it('moves the summary to a continuation page when it does not fit', function (): void {
    $pages = fiscalLayout(EfaturaInvoiceFactory::make(11))->pages;

    expect($pages)->toHaveCount(2)
        ->and($pages[0]->summary)->toBeNull()
        ->and($pages[0]->carryForward)->toBeNull()
        ->and($pages[1]->tableY)->toBeNull()
        ->and($pages[1]->rows)->toBe([])
        ->and($pages[1]->summary->totals[0]->y)->toBe(245.0);
});

it('shows the offline contingency notice', function (): void {
    expect(fiscalLayout(EfaturaInvoiceFactory::make(mode: EmissionMode::Offline))->contingency)
        ->toBe('EMITIDO EM CONTINGÊNCIA OFFLINE - Pendente de Autorização');
});

it('hides item codes and the qr code when issued off', function (): void {
    $document = fiscalLayout(EfaturaInvoiceFactory::make(mode: EmissionMode::Off));
    $page = $document->pages[0];

    expect($document->contingency)->toBe('EMITIDO EM CONTINGÊNCIA OFF - IUC: 2026/17 - Pendente de Autorização')
        ->and($document->facts[4]->value)->toBe('Off')
        ->and($page->rows[0]->code)->toBe('')
        ->and($page->summary->qrCode)->toBeNull();
});

it('places the conversion evidence and notes below the totals', function (): void {
    $document = fiscalLayout(EfaturaInvoiceFactory::make(conversion: true, notes: ['Isento nos termos do artigo 9.º do CIVA.']));
    $summary = $document->pages[1]->summary;

    expect($document->pages)->toHaveCount(2)
        ->and(array_map(fn (FiscalLine $line): string => $line->label.' '.$line->value, $summary->conversion))->toBe([
            'Valor original: 14,60 EUR',
            'Taxa aplicada: 1 EUR = 110,26500 CVE',
            'Data da taxa: 11/10/2026',
            'Fonte: Banco de Cabo Verde',
            'Fonte online: https://www.bcv.cv/taxas-de-cambio',
        ])
        ->and($summary->totals[0]->y)->toBe(245.0)
        ->and($summary->conversionY)->toBe(364.0)
        ->and($summary->conversion[0]->y)->toBe(384.0)
        ->and($summary->conversion[0]->height)->toBe(10.0)
        ->and($summary->notes)->toBe(['Isento nos termos do artigo 9.º do CIVA.'])
        ->and($summary->notesY)->toBe(468.0)
        ->and($summary->verificationY)->toBe(665.0);
});

it('keeps the conversion evidence on the first page when it fits', function (): void {
    $summary = fiscalLayout(EfaturaInvoiceFactory::make(conversion: true))->pages[0]->summary;

    expect($summary->totals[0]->y)->toBe(457.0)
        ->and($summary->conversionY)->toBe(576.0)
        ->and($summary->verificationY)->toBe(684.0);
});

it('renders legacy invoices with derived values and placeholders', function (): void {
    $invoice = new InvoiceData(
        seller: new EntityData(name: 'Acme'),
        buyer: new EntityData(name: ' '),
        items: [
            new ItemData('Consulting', 100.0, 2, 0.15),
            new ItemData('Gift', 5.0),
            new ItemData(description: 'Mixed', unitPrice: 10.0, taxes: [
                new ItemTaxData(rate: '15'),
                new ItemTaxData(code: 'IS', amount: '1.50'),
                new ItemTaxData(exemptionCode: 'M01'),
            ]),
        ],
        invoiceNumber: 'INV-1',
        currency: 'EUR',
        notes: 'Thanks',
    );

    $document = fiscalLayout($invoice, 'en');
    $rows = $document->pages[0]->rows;

    expect($document->title)->toBe('AUXILIARY TAX DOCUMENT')
        ->and($document->identifier)->toBeNull()
        ->and($document->customer->name)->toBe('-')
        ->and($document->customer->taxId)->toBe('Tax ID: -')
        ->and($document->facts[2]->value)->toBe('INV-1')
        ->and($document->facts[3]->value)->toBe('-')
        ->and($rows[0]->tax)->toBe('15%')
        ->and($rows[0]->total)->toBe('230,00')
        ->and($rows[0]->code)->toBe('')
        ->and($rows[1]->tax)->toBe('-')
        ->and($rows[2]->tax)->toBe('15% + 1,50 + M01')
        ->and($document->pages[0]->summary->totals[4]->value)->toBe('245,00 EUR')
        ->and($document->pages[0]->summary->notes)->toBe(['Thanks'])
        ->and($document->label('unknown'))->toBe('unknown');
});

it('shows withholding and rounding only when given', function (): void {
    $invoice = EfaturaInvoiceFactory::make();
    $invoice = new InvoiceData(
        seller: $invoice->seller,
        buyer: $invoice->buyer,
        currency: 'CVE',
        totals: new Akira\PdfInvoices\DTO\InvoiceTotalsData(net: '100', payable: '90.01', withholding: '-10', rounding: '0.01'),
    );

    $labels = array_map(fn (FiscalLine $line): string => $line->label, fiscalLayout($invoice)->pages[0]->summary->totals);

    expect($labels)->toBe(['Subtotal:', 'Descontos:', 'Encargos:', 'Imposto:', 'Retenção:', 'Acerto:', 'Total:']);
});

it('raises the summary above its usual place when it is tall', function (): void {
    $invoice = EfaturaInvoiceFactory::make(0, conversion: true);

    expect(fiscalLayout($invoice)->pages[0]->summary->totals[0]->y)->toBe(438.0);
});

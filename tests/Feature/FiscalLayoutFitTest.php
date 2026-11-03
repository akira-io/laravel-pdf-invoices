<?php

declare(strict_types=1);

use Akira\PdfInvoices\DTO\CurrencyConversionData;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Layout\FiscalDocument;
use Akira\PdfInvoices\Layout\FiscalLayout;
use Akira\PdfInvoices\Support\FiscalTextMeasure;
use Akira\PdfInvoices\Support\InvoiceTranslator;
use Akira\PdfInvoices\Tests\Support\EfaturaInvoiceFactory;

function fittedLayout(InvoiceData $invoice): FiscalDocument
{
    return new FiscalLayout(new InvoiceTranslator('pt_PT'))->build($invoice);
}

it('makes room for every tax of a line', function (): void {
    $taxes = [new ItemTaxData(rate: '15'), new ItemTaxData(amount: '1.50'), new ItemTaxData(exemptionCode: 'M01')];
    $items = [
        new ItemData(description: 'Mixed', unitPrice: 10.0, taxes: $taxes),
        new ItemData(description: 'Plain', unitPrice: 10.0, taxes: [$taxes[0]]),
        new ItemData(description: 'Plain', unitPrice: 10.0, taxes: [$taxes[0]]),
    ];

    $rows = fittedLayout(new InvoiceData(seller: new EntityData('Acme'), buyer: new EntityData('Client'), items: $items))->pages[0]->rows;

    expect($rows[0]->taxHeight())->toBe(24.0)
        ->and($rows[1]->taxHeight())->toBe(0.0)
        ->and($rows[1]->y)->toBe($rows[0]->y + 27.0 + 24.0)
        ->and($rows[2]->y)->toBe($rows[1]->y + 27.0);
});

it('starts a new page when the taxes of a line do not fit', function (): void {
    $taxes = [new ItemTaxData(rate: '15'), new ItemTaxData(amount: '1.50'), new ItemTaxData(exemptionCode: 'M01')];
    $items = array_map(fn (int $index): ItemData => new ItemData(description: "Item {$index}", unitPrice: 1.0, taxes: $taxes), range(1, 7));

    $pages = fittedLayout(new InvoiceData(seller: new EntityData('Acme'), buyer: new EntityData('Client'), items: $items))->pages;

    expect(count($pages[0]->rows))->toBe(5)
        ->and(count($pages[1]->rows))->toBe(2)
        ->and($pages[1]->rows[0]->y)->toBe(301.0);
});

it('reserves enough lines for wide letters in notes and conversion sources', function (): void {
    $wide = str_repeat('WWWW MMMM ', 16);
    $invoice = EfaturaInvoiceFactory::make(notes: [$wide]);
    $invoice = new InvoiceData(
        seller: $invoice->seller,
        buyer: $invoice->buyer,
        currency: 'CVE',
        fiscal: $invoice->fiscal,
        totals: $invoice->totals,
        conversions: [
            new CurrencyConversionData(currency: 'EUR', amount: '1', rate: '110.265', source: $wide),
        ],
    );

    $summary = fittedLayout($invoice)->pages[1]->summary;
    $source = $summary->conversion[2];

    expect($source->height)->toBe(30.75)
        ->and($summary->notesY)->toBe($source->y + 30.75 + 14.0)
        ->and($summary->verificationY)->toBe(max(665.0, $summary->notesY + 20.0 + 30.0 + 18.0));
});

it('refuses notes in wide letters that would reach the DFE lookup', function (): void {
    fittedLayout(EfaturaInvoiceFactory::make(notes: [str_repeat('WWWW MMMM ', 220)]));
})->throws(LengthException::class, 'shorten the notes');

it('keeps long party details on two lines with an ellipsis', function (): void {
    $name = 'Sociedade de Construções e Obras Públicas de Cabo Verde e dos Países Africanos de Língua Oficial Portuguesa, Lda';
    $address = str_repeat('Avenida Cidade de Lisboa, Achada Santo António, ', 4);
    $invoice = new InvoiceData(
        seller: new EntityData(name: $name, address: $address, vatNumber: str_repeat('2', 80), email: str_repeat('a', 80).'@example.cv'),
        buyer: new EntityData(name: 'Sociedade de Construções e Obras Públicas, Lda'),
    );

    $document = fittedLayout($invoice);
    $measure = new FiscalTextMeasure();

    expect($document->customer->name)->toBe('Sociedade de Construções e Obras Públicas, Lda')
        ->and($document->issuer->name)->toEndWith('…')
        ->and($measure->lineCount($document->issuer->name, 10.0, 240.0))->toBe(2)
        ->and($document->issuer->address)->toEndWith('…')
        ->and($measure->lineCount($document->issuer->address, 9.0, 240.0))->toBe(2)
        ->and($document->issuer->taxId)->toEndWith('…')
        ->and($measure->lineCount($document->issuer->taxId, 10.0, 240.0))->toBe(1)
        ->and($document->issuer->contact)->toEndWith('…')
        ->and($measure->lineCount($document->issuer->contact, 9.0, 240.0))->toBe(1)
        ->and($document->brand)->toEndWith('…')
        ->and($measure->width($document->brand, 12.0))->toBeLessThanOrEqual(226.0);
});

it('refuses a line with more taxes than a page can hold', function (): void {
    $taxes = array_fill(0, 30, new ItemTaxData(rate: '15'));

    fittedLayout(new InvoiceData(
        seller: new EntityData('Acme'),
        buyer: new EntityData('Client'),
        items: [new ItemData(description: 'Mixed', unitPrice: 1.0, taxes: $taxes)],
    ));
})->throws(LengthException::class, 'too many taxes');

function renderedFrames(InvoiceData $invoice): array
{
    $dompdf = new Dompdf\Dompdf(['isFontSubsettingEnabled' => true]);
    $dompdf->loadHtml(view('pdf-invoices::pdf.templates.efatura', [
        'invoice' => $invoice,
        'compiledCss' => '',
        'translator' => new InvoiceTranslator('pt_PT'),
    ])->render());

    $frames = [];
    $dompdf->setCallbacks([[
        'event' => 'end_frame',
        'f' => function (Dompdf\Frame $frame) use (&$frames): void {
            if ($frame->get_node()->nodeName === '#text' && mb_trim($frame->get_node()->nodeValue) !== '') {
                [$x, $y] = $frame->get_position();
                $frames[] = ['text' => mb_trim($frame->get_node()->nodeValue), 'box' => ['x' => $x, 'y' => $y, 'w' => $frame->get_margin_width(), 'h' => $frame->get_margin_height()]];
            }
        },
    ]]);
    $dompdf->render();

    return $frames;
}

function frameContaining(array $frames, string $text): array
{
    return array_values(array_filter($frames, fn (array $frame): bool => str_contains($frame['text'], $text)))[0];
}

it('draws notes and conversion sources above the DFE lookup whatever the glyphs', function (string $text): void {
    $frames = renderedFrames(EfaturaInvoiceFactory::make(notes: [$text]));
    $lookup = frameContaining($frames, 'Consulta DFE')['box']['y'];
    $noteBottoms = array_map(
        fn (array $frame): float => $frame['box']['y'] + $frame['box']['h'],
        array_filter($frames, fn (array $frame): bool => str_contains($text, $frame['text'])),
    );

    expect(max($noteBottoms))->toBeLessThan($lookup);
})->with([
    'wide capitals' => [str_repeat('WWWW MMMM ', 80)],
    'symbols' => [str_repeat('• § ° ', 200)],
    'dashes' => [str_repeat('—', 400)],
    'operators' => [str_repeat('=', 500)],
    'ordinary' => [str_repeat('nota fiscal de exemplo ', 60)],
]);

it('keeps taxes clear of the unit price and the price clear of the quantity', function (): void {
    $frames = renderedFrames(new InvoiceData(
        seller: new EntityData('Acme'),
        buyer: new EntityData('Client'),
        items: [new ItemData(
            description: 'Mixed',
            unitPrice: 0.0,
            unit: 'UN',
            decimalQuantity: '1234',
            decimalUnitPrice: '1234567.12345',
            decimalTotal: '1.00',
            taxes: [new ItemTaxData(amount: '1234567.00'), new ItemTaxData(exemptionCode: 'M01')],
        )],
        currency: 'CVE',
        totals: new Akira\PdfInvoices\DTO\InvoiceTotalsData(net: '1.00', payable: '1.00'),
    ));

    $quantity = frameContaining($frames, '1234 UN')['box'];
    $price = frameContaining($frames, '1 234 567$12345')['box'];
    $tax = frameContaining($frames, '1 234 567$00')['box'];

    expect($quantity['x'] + $quantity['w'])->toBeLessThanOrEqual($price['x'])
        ->and($price['x'] + $price['w'])->toBeLessThanOrEqual($tax['x']);
});

it('reserves as much as a long party name takes when drawn', function (): void {
    $name = 'Sociedade de Construções e Obras Públicas de Cabo Verde e dos Países Africanos de Língua Oficial Portuguesa, Lda';
    $frames = renderedFrames(new InvoiceData(seller: new EntityData(name: $name, phone: '2601010'), buyer: new EntityData('Client')));

    expect(frameContaining($frames, '2601010')['box']['y'] + frameContaining($frames, '2601010')['box']['h'])
        ->toBeLessThanOrEqual(frameContaining($frames, 'Cliente')['box']['y']);
});

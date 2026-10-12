<?php

declare(strict_types=1);

use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;
use Akira\PdfInvoices\DTO\CurrencyConversionData;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\FiscalData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\InvoiceTotalsData;
use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Enums\EmissionMode;

it('labels emission modes and flags contingency', function (): void {
    expect(EmissionMode::Online->label())->toBe('Online')
        ->and(EmissionMode::Offline->label())->toBe('Offline')
        ->and(EmissionMode::Off->label())->toBe('Off')
        ->and(EmissionMode::Online->isContingency())->toBeFalse()
        ->and(EmissionMode::Offline->isContingency())->toBeTrue()
        ->and(EmissionMode::Off->isContingency())->toBeTrue();
});

it('keeps the legacy item constructor and defaults', function (): void {
    $item = new ItemData('Consulting', 100.0, 2, 0.15);

    expect($item->code)->toBeNull()
        ->and($item->unit)->toBeNull()
        ->and($item->taxes)->toBe([])
        ->and($item->exactQuantity())->toBe('2')
        ->and($item->exactUnitPrice())->toBe('100.00')
        ->and($item->exactTotal())->toBe('230.00');
});

it('prefers exact decimal values on items', function (): void {
    $tax = new ItemTaxData(rate: '15');
    $item = new ItemData(
        description: 'Cimento',
        unitPrice: 0.0,
        code: 'CIM-01',
        unit: 'KG',
        decimalQuantity: '2.5',
        decimalUnitPrice: '0.12345',
        decimalTotal: '0.31',
        taxes: [$tax],
    );

    expect($item->exactQuantity())->toBe('2.5')
        ->and($item->exactUnitPrice())->toBe('0.12345')
        ->and($item->exactTotal())->toBe('0.31')
        ->and($item->taxes)->toBe([$tax]);
});

it('rejects non decimal strings on items', function (): void {
    new ItemData(description: 'x', unitPrice: 1.0, decimalQuantity: '2,5');
})->throws(InvalidArgumentException::class, 'item quantity');

it('rejects an item tax without rate, amount or exemption', function (): void {
    new ItemTaxData();
})->throws(InvalidArgumentException::class, 'rate, an amount or an exemption code');

it('validates item tax decimals', function (): void {
    expect(new ItemTaxData(code: 'IS', amount: '10.00')->amount)->toBe('10.00')
        ->and(new ItemTaxData(exemptionCode: 'M01')->exemptionCode)->toBe('M01');

    new ItemTaxData(rate: '15%');
})->throws(InvalidArgumentException::class, 'tax rate');

it('validates item tax amounts', function (): void {
    new ItemTaxData(amount: 'ten');
})->throws(InvalidArgumentException::class, 'tax amount');

it('validates totals', function (string $field, array $arguments): void {
    new InvoiceTotalsData(...$arguments);
})->with([
    ['net total', ['net' => 'x', 'payable' => '1']],
    ['payable total', ['net' => '1', 'payable' => 'x']],
    ['discount total', ['net' => '1', 'payable' => '1', 'discounts' => 'x']],
    ['charge total', ['net' => '1', 'payable' => '1', 'charges' => 'x']],
    ['tax total', ['net' => '1', 'payable' => '1', 'tax' => 'x']],
    ['withholding total', ['net' => '1', 'payable' => '1', 'withholding' => 'x']],
    ['rounding amount', ['net' => '1', 'payable' => '1', 'rounding' => 'x']],
])->throws(InvalidArgumentException::class);

it('validates conversions', function (): void {
    new CurrencyConversionData(currency: 'EUR', amount: '1', rate: 'x');
})->throws(InvalidArgumentException::class, 'exchange rate');

it('validates conversion amounts', function (): void {
    new CurrencyConversionData(currency: 'EUR', amount: 'x', rate: '110.265');
})->throws(InvalidArgumentException::class, 'converted amount');

it('derives totals from legacy items when none are given', function (): void {
    $invoice = new InvoiceData(
        seller: new EntityData('Acme'),
        buyer: new EntityData('Client'),
        items: [new ItemData('A', 100.0, 2, 0.15, 0.1)],
    );

    $totals = $invoice->fiscalTotals();

    expect($invoice->fiscal)->toBeNull()
        ->and($invoice->totals)->toBeNull()
        ->and($invoice->conversions)->toBe([])
        ->and($totals->net)->toBe('180.00')
        ->and($totals->discounts)->toBe('20.00')
        ->and($totals->charges)->toBe('0')
        ->and($totals->tax)->toBe('27.00')
        ->and($totals->payable)->toBe('207.00')
        ->and($totals->withholding)->toBeNull();
});

it('returns caller totals untouched', function (): void {
    $totals = new InvoiceTotalsData(net: '1400.00', payable: '1610.00', tax: '210.00');
    $invoice = new InvoiceData(seller: new EntityData('Acme'), buyer: new EntityData('Client'), totals: $totals);

    expect($invoice->fiscalTotals())->toBe($totals);
});

it('exposes the party contact', function (): void {
    expect(new EntityData(name: 'A', email: 'a@b.cv', phone: '2601010')->contact())->toBe('2601010')
        ->and(new EntityData(name: 'A', email: 'a@b.cv')->contact())->toBe('a@b.cv')
        ->and(new EntityData(name: 'A')->contact())->toBeNull();
});

it('builds fiscal invoices through the builders', function (): void {
    $fiscal = new FiscalData(documentType: 'FTE', emissionMode: EmissionMode::Offline);
    $totals = new InvoiceTotalsData(net: '1400.00', payable: '1610.00');
    $conversion = new CurrencyConversionData(currency: 'EUR', amount: '14.60', rate: '110.265');
    $tax = new ItemTaxData(rate: '15');

    $item = ItemBuilder::make()
        ->description('Cimento')
        ->code('CIM-01')
        ->unit('KG')
        ->decimalQuantity('2.5')
        ->decimalUnitPrice('560.00')
        ->decimalTotal('1400.00')
        ->addTax($tax)
        ->build();

    $invoice = InvoiceBuilder::make()
        ->seller(EntityBuilder::make()->name('Acme')->phone('2601010')->build())
        ->buyer(EntityBuilder::make()->name('Client')->build())
        ->addItem($item)
        ->fiscal($fiscal)
        ->totals($totals)
        ->addConversion($conversion)
        ->build();

    expect($item->code)->toBe('CIM-01')
        ->and($item->unit)->toBe('KG')
        ->and($item->exactQuantity())->toBe('2.5')
        ->and($item->exactUnitPrice())->toBe('560.00')
        ->and($item->exactTotal())->toBe('1400.00')
        ->and($item->taxes)->toBe([$tax])
        ->and($invoice->seller->phone)->toBe('2601010')
        ->and($invoice->fiscal)->toBe($fiscal)
        ->and($invoice->totals)->toBe($totals)
        ->and($invoice->conversions)->toBe([$conversion]);
});

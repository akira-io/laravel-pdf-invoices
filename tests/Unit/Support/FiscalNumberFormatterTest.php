<?php

declare(strict_types=1);

use Akira\PdfInvoices\Support\FiscalNumberFormatter;

it('formats escudo amounts in the e-Fatura manual style', function (string $value, string $expected): void {
    expect(new FiscalNumberFormatter()->money($value, 'CVE'))->toBe($expected);
})->with([
    ['1400', '1 400$00'],
    ['1400.005', '1 400$01'],
    ['0', '0$00'],
    ['999', '999$00'],
    ['1234567.8', '1 234 567$80'],
    ['-1400.5', '-1 400$50'],
]);

it('formats other currencies with a decimal comma and the currency code', function (): void {
    $formatter = new FiscalNumberFormatter();

    expect($formatter->money('12.7', 'EUR'))->toBe('12,70 EUR')
        ->and($formatter->money('1234.5678', 'USD'))->toBe('1 234,57 USD')
        ->and($formatter->number('1234.5', 'EUR'))->toBe('1 234,50')
        ->and($formatter->number('1234.5', 'CVE'))->toBe('1 234$50');
});

it('formats unit prices with two to five decimals', function (): void {
    $formatter = new FiscalNumberFormatter();

    expect($formatter->unitPrice('1400', 'CVE'))->toBe('1 400$00')
        ->and($formatter->unitPrice('0.123456', 'CVE'))->toBe('0$12346')
        ->and($formatter->unitPrice('12.50000', 'CVE'))->toBe('12$50')
        ->and($formatter->unitPrice('12.125', 'EUR'))->toBe('12,125');
});

it('formats quantities and percentages with a decimal comma', function (): void {
    $formatter = new FiscalNumberFormatter();

    expect($formatter->quantity('2'))->toBe('2')
        ->and($formatter->quantity('2.500'))->toBe('2,5')
        ->and($formatter->percentage('15.00'))->toBe('15%')
        ->and($formatter->percentage('7.5'))->toBe('7,5%');
});

it('formats exchange rates with five decimals', function (): void {
    expect(new FiscalNumberFormatter()->rate('110.265', 'EUR', 'CVE'))->toBe('1 EUR = 110,26500 CVE')
        ->and(new FiscalNumberFormatter()->rate('1102.6', 'EUR', 'CVE'))->toBe('1 EUR = 1 102,60000 CVE');
});

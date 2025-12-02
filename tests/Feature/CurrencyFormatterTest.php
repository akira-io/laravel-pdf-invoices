<?php

declare(strict_types=1);

use Akira\PdfInvoices\Support\LaravelCurrencyFormatter;
use Akira\PdfInvoices\Support\SimpleCurrencyFormatter;

describe('LaravelCurrencyFormatter', function (): void {
    it('formats currency using Laravel number utilities', function (): void {
        $formatter = new LaravelCurrencyFormatter();
        $formatted = $formatter->format(1234.56, 'USD', 'en');

        expect($formatted)
            ->toBeString()
            ->and(str_contains($formatted, '1,234'))->toBeTrue();
    });

    it('formats without currency code', function (): void {
        $formatter = new LaravelCurrencyFormatter();
        $formatted = $formatter->format(1234.56, '', 'en');

        expect($formatted)->toBeString();
    });
});

describe('SimpleCurrencyFormatter', function (): void {
    it('formats currency with default symbol', function (): void {
        $formatter = new SimpleCurrencyFormatter();
        $formatted = $formatter->format(1234.56, 'EUR');

        expect($formatted)->toContain('1,234.56');
    });

    it('formats currency with custom symbol', function (): void {
        $formatter = new SimpleCurrencyFormatter('$');
        $formatted = $formatter->format(99.99, 'USD');

        expect($formatted)->toContain('99.99');
    });

    it('formats without currency code using symbol', function (): void {
        $formatter = new SimpleCurrencyFormatter('£');
        $formatted = $formatter->format(100.0);

        expect($formatted)
            ->toContain('£')
            ->and($formatted)->toContain('100.00');
    });
});

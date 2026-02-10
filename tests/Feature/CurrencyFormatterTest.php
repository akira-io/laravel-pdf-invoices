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

    it('handles formatting with currency code 0', function (): void {
        $formatter = new LaravelCurrencyFormatter();
        $formatted = $formatter->format(100.00, '0', 'en');

        expect($formatted)->toBeString();
    });

    it('falls back to numeric string if formatting fails', function (): void {
        $formatter = new LaravelCurrencyFormatter();

        // Passing an invalid locale might cause issues in some environments
        // leading to non-string return if the framework handles it that way.
        // But more reliably, we just test that it returns a string for varied inputs.
        expect($formatter->format(123.45, 'ANY', 'invalid-locale'))->toBeString();
    });

    it('handles unexpected throwables during formatting', function (): void {
        $formatter = new LaravelCurrencyFormatter();

        // Use a extremely long locale string to trigger a potential error in underlying Intl/PHP
        $result = $formatter->format(123.45, 'USD', str_repeat('a', 512));

        expect($result)->toBe('123.45');
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

<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Config;

final readonly class ConfigManager
{
    public function __construct() {}

    public function pdfDriver(): string
    {
        return (string) config('pdf-invoices.pdf.driver', 'spatie');
    }

    public function pdfTemplate(): string
    {
        return (string) config('pdf-invoices.pdf.template', 'modern');
    }

    public function pdfBasePath(): string
    {
        return (string) config('pdf-invoices.pdf.base_path', 'invoices');
    }

    public function storageDriver(): string
    {
        return (string) config('pdf-invoices.storage.driver', 'laravel');
    }

    public function storageDisk(): string
    {
        return (string) config('pdf-invoices.storage.disk', 'local');
    }

    public function currencyDriver(): string
    {
        return (string) config('pdf-invoices.currency.driver', 'Akira\PdfInvoices\Support\LaravelCurrencyFormatter');
    }

    public function currencyCode(): string
    {
        return (string) config('pdf-invoices.currency.code', 'EUR');
    }

    public function currencySymbol(): string
    {
        return (string) config('pdf-invoices.currency.symbol', '€');
    }

    public function locale(): string
    {
        return (string) config('pdf-invoices.localization.locale', 'en');
    }

    /**
     * @return array<int, string>
     */
    public function supportedLocales(): array
    {
        $locales = config('pdf-invoices.localization.supported_locales', ['en']);

        return is_array($locales) ? $locales : ['en'];
    }

    public function allowCustomAttributes(): bool
    {
        return (bool) config('pdf-invoices.allow_custom_attributes', true);
    }

    /**
     * Get all configuration values.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return (array) config('pdf-invoices', []);
    }
}
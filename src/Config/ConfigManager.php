<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Config;

use Akira\PdfInvoices\Support\LaravelCurrencyFormatter;

final readonly class ConfigManager
{
    public function pdfDriver(): string
    {
        $driver = config('pdf-invoices.pdf.driver', 'spatie');

        return is_string($driver) ? $driver : 'spatie';
    }

    public function pdfTemplate(): string
    {
        $template = config('pdf-invoices.pdf.template', 'modern');

        return is_string($template) ? $template : 'modern';
    }

    public function pdfBasePath(): string
    {
        $path = config('pdf-invoices.pdf.base_path', 'invoices');

        return is_string($path) ? $path : 'invoices';
    }

    public function storageDriver(): string
    {
        $driver = config('pdf-invoices.storage.driver', 'laravel');

        return is_string($driver) ? $driver : 'laravel';
    }

    public function storageDisk(): string
    {
        $disk = config('pdf-invoices.storage.disk', 'local');

        return is_string($disk) ? $disk : 'local';
    }

    public function currencyDriver(): string
    {
        $driver = config('pdf-invoices.currency.driver', LaravelCurrencyFormatter::class);

        return is_string($driver) ? $driver : LaravelCurrencyFormatter::class;
    }

    public function currencyCode(): string
    {
        $code = config('pdf-invoices.currency.code', 'EUR');

        return is_string($code) ? $code : 'EUR';
    }

    public function currencySymbol(): string
    {
        $symbol = config('pdf-invoices.currency.symbol', '€');

        return is_string($symbol) ? $symbol : '€';
    }

    public function locale(): string
    {
        $locale = config('pdf-invoices.localization.locale', 'en');

        return is_string($locale) ? $locale : 'en';
    }

    /**
     * @return array<int, string>
     */
    public function supportedLocales(): array
    {
        $locales = config('pdf-invoices.localization.supported_locales', ['en']);

        if (! is_array($locales)) {
            return ['en'];
        }

        $stringLocales = array_filter($locales, is_string(...));

        return $stringLocales === [] ? ['en'] : array_values($stringLocales);
    }

    public function allowCustomAttributes(): bool
    {
        $allow = config('pdf-invoices.allow_custom_attributes', true);

        return is_bool($allow) ? $allow : true;
    }

    /**
     * Get all configuration values.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $config = config('pdf-invoices', []);

        if (! is_array($config)) {
            return [];
        }

        return array_filter($config, is_string(...), ARRAY_FILTER_USE_KEY);
    }
}

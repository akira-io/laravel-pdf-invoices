<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Config;

final readonly class ConfigManager
{
    public function __construct() {}

    public function pdfDriver(): string
    {
        $driver = config('pdf-invoices.pdf.driver', 'spatie');
        if (!is_string($driver)) {
            return 'spatie';
        }

        return $driver;
    }

    public function pdfTemplate(): string
    {
        $template = config('pdf-invoices.pdf.template', 'modern');
        if (!is_string($template)) {
            return 'modern';
        }

        return $template;
    }

    public function pdfBasePath(): string
    {
        $path = config('pdf-invoices.pdf.base_path', 'invoices');
        if (!is_string($path)) {
            return 'invoices';
        }

        return $path;
    }

    public function storageDriver(): string
    {
        $driver = config('pdf-invoices.storage.driver', 'laravel');
        if (!is_string($driver)) {
            return 'laravel';
        }

        return $driver;
    }

    public function storageDisk(): string
    {
        $disk = config('pdf-invoices.storage.disk', 'local');
        if (!is_string($disk)) {
            return 'local';
        }

        return $disk;
    }

    public function currencyDriver(): string
    {
        $driver = config('pdf-invoices.currency.driver', 'Akira\PdfInvoices\Support\LaravelCurrencyFormatter');
        if (!is_string($driver)) {
            return 'Akira\PdfInvoices\Support\LaravelCurrencyFormatter';
        }

        return $driver;
    }

    public function currencyCode(): string
    {
        $code = config('pdf-invoices.currency.code', 'EUR');
        if (!is_string($code)) {
            return 'EUR';
        }

        return $code;
    }

    public function currencySymbol(): string
    {
        $symbol = config('pdf-invoices.currency.symbol', '€');
        if (!is_string($symbol)) {
            return '€';
        }

        return $symbol;
    }

    public function locale(): string
    {
        $locale = config('pdf-invoices.localization.locale', 'en');
        if (!is_string($locale)) {
            return 'en';
        }

        return $locale;
    }

    /**
     * @return array<int, string>
     */
    public function supportedLocales(): array
    {
        $locales = config('pdf-invoices.localization.supported_locales', ['en']);

        if (!is_array($locales)) {
            return ['en'];
        }

        $stringLocales = [];
        foreach ($locales as $locale) {
            if (is_string($locale)) {
                $stringLocales[] = $locale;
            }
        }

        return !empty($stringLocales) ? $stringLocales : ['en'];
    }

    public function allowCustomAttributes(): bool
    {
        $allow = config('pdf-invoices.allow_custom_attributes', true);
        if (!is_bool($allow)) {
            return true;
        }

        return $allow;
    }

    /**
     * Get all configuration values.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $config = config('pdf-invoices', []);
        if (!is_array($config)) {
            return [];
        }

        return $config;
    }
}
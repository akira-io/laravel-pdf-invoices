<?php

declare(strict_types=1);

use Akira\PdfInvoices\Config\ConfigManager;

describe('ConfigManager', function (): void {
    it('returns pdf driver', function (): void {
        config()->set('pdf-invoices.pdf.driver', 'dompdf');

        $manager = new ConfigManager();
        expect($manager->pdfDriver())->toBe('dompdf');
    });

    it('returns pdf driver default', function (): void {
        config()->set('pdf-invoices.pdf.driver');

        $manager = new ConfigManager();
        expect($manager->pdfDriver())->toBe('spatie');
    });

    it('returns pdf template', function (): void {
        config()->set('pdf-invoices.pdf.template', 'minimal');

        $manager = new ConfigManager();
        expect($manager->pdfTemplate())->toBe('minimal');
    });

    it('returns pdf template default', function (): void {
        config()->set('pdf-invoices.pdf.template');

        $manager = new ConfigManager();
        expect($manager->pdfTemplate())->toBe('modern');
    });

    it('returns pdf base path', function (): void {
        config()->set('pdf-invoices.pdf.base_path', 'storage/pdfs');

        $manager = new ConfigManager();
        expect($manager->pdfBasePath())->toBe('storage/pdfs');
    });

    it('returns storage disk', function (): void {
        config()->set('pdf-invoices.storage.disk', 's3');

        $manager = new ConfigManager();
        expect($manager->storageDisk())->toBe('s3');
    });

    it('returns currency code', function (): void {
        config()->set('pdf-invoices.currency.code', 'USD');

        $manager = new ConfigManager();
        expect($manager->currencyCode())->toBe('USD');
    });

    it('returns currency symbol', function (): void {
        config()->set('pdf-invoices.currency.symbol', '$');

        $manager = new ConfigManager();
        expect($manager->currencySymbol())->toBe('$');
    });

    it('returns locale', function (): void {
        config()->set('pdf-invoices.localization.locale', 'pt');

        $manager = new ConfigManager();
        expect($manager->locale())->toBe('pt');
    });

    it('returns supported locales', function (): void {
        config()->set('pdf-invoices.localization.supported_locales', ['en', 'pt', 'es']);

        $manager = new ConfigManager();
        expect($manager->supportedLocales())
            ->toBeArray()
            ->toContain('en', 'pt', 'es');
    });

    it('returns allow custom attributes', function (): void {
        config()->set('pdf-invoices.allow_custom_attributes', false);

        $manager = new ConfigManager();
        expect($manager->allowCustomAttributes())->toBeFalse();
    });

    it('returns all configuration', function (): void {
        $manager = new ConfigManager();
        $all = $manager->all();

        expect($all)->toBeArray()
            ->toHaveKey('pdf')
            ->toHaveKey('storage')
            ->toHaveKey('currency')
            ->toHaveKey('localization');
    });

    it('handles non-string config values with defaults', function (): void {
        config()->set('pdf-invoices.pdf.driver', 123);
        config()->set('pdf-invoices.pdf.template', true);
        config()->set('pdf-invoices.pdf.base_path', []);
        config()->set('pdf-invoices.storage.driver', 1.1);
        config()->set('pdf-invoices.storage.disk');
        config()->set('pdf-invoices.currency.driver', 0);
        config()->set('pdf-invoices.currency.code', []);
        config()->set('pdf-invoices.currency.symbol');
        config()->set('pdf-invoices.localization.locale', 456);
        config()->set('pdf-invoices.allow_custom_attributes', 'yes');

        $manager = new ConfigManager();
        expect($manager->pdfDriver())->toBe('spatie')
            ->and($manager->pdfTemplate())->toBe('modern')
            ->and($manager->pdfBasePath())->toBe('invoices')
            ->and($manager->storageDriver())->toBe('laravel')
            ->and($manager->storageDisk())->toBe('local')
            ->and($manager->currencyDriver())->toBe(Akira\PdfInvoices\Support\LaravelCurrencyFormatter::class)
            ->and($manager->currencyCode())->toBe('EUR')
            ->and($manager->currencySymbol())->toBe('€')
            ->and($manager->locale())->toBe('en')
            ->and($manager->allowCustomAttributes())->toBeTrue();
    });

    it('handles non-array and empty supported locales', function (): void {
        config()->set('pdf-invoices.localization.supported_locales', 'en');
        $manager = new ConfigManager();
        expect($manager->supportedLocales())->toBe(['en']);

        config()->set('pdf-invoices.localization.supported_locales', [123, []]);
        expect($manager->supportedLocales())->toBe(['en']);
    });

    it('handles invalid all() configuration', function (): void {
        config()->set('pdf-invoices', 'not-an-array');
        $manager = new ConfigManager();
        expect($manager->all())->toBe([]);
    });

    it('all() ignores non-string keys', function (): void {
        // Since Laravel config keys are usually strings, we manually override it in config repository
        config()->set('pdf-invoices', [
            'key1' => 'value1',
            123 => 'value2',
        ]);

        $manager = new ConfigManager();
        $all = $manager->all();

        expect($all)->toBeArray()
            ->toHaveKey('key1')
            ->not->toHaveKey(123)
            ->and($all['key1'])->toBe('value1');
    });
});

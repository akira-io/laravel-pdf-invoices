<?php

declare(strict_types=1);

use Akira\PdfInvoices\Config\ConfigManager;

describe('ConfigManager', function () {
    it('returns pdf driver', function () {
        config()->set('pdf-invoices.pdf.driver', 'dompdf');

        $manager = new ConfigManager();
        expect($manager->pdfDriver())->toBe('dompdf');
    });

    it('returns pdf driver default', function () {
        config()->set('pdf-invoices.pdf.driver', null);

        $manager = new ConfigManager();
        expect($manager->pdfDriver())->toBe('spatie');
    });

    it('returns pdf template', function () {
        config()->set('pdf-invoices.pdf.template', 'minimal');

        $manager = new ConfigManager();
        expect($manager->pdfTemplate())->toBe('minimal');
    });

    it('returns pdf template default', function () {
        config()->set('pdf-invoices.pdf.template', null);

        $manager = new ConfigManager();
        expect($manager->pdfTemplate())->toBe('modern');
    });

    it('returns pdf base path', function () {
        config()->set('pdf-invoices.pdf.base_path', 'storage/pdfs');

        $manager = new ConfigManager();
        expect($manager->pdfBasePath())->toBe('storage/pdfs');
    });

    it('returns storage disk', function () {
        config()->set('pdf-invoices.storage.disk', 's3');

        $manager = new ConfigManager();
        expect($manager->storageDisk())->toBe('s3');
    });

    it('returns currency code', function () {
        config()->set('pdf-invoices.currency.code', 'USD');

        $manager = new ConfigManager();
        expect($manager->currencyCode())->toBe('USD');
    });

    it('returns currency symbol', function () {
        config()->set('pdf-invoices.currency.symbol', '$');

        $manager = new ConfigManager();
        expect($manager->currencySymbol())->toBe('$');
    });

    it('returns locale', function () {
        config()->set('pdf-invoices.localization.locale', 'pt');

        $manager = new ConfigManager();
        expect($manager->locale())->toBe('pt');
    });

    it('returns supported locales', function () {
        config()->set('pdf-invoices.localization.supported_locales', ['en', 'pt', 'es']);

        $manager = new ConfigManager();
        expect($manager->supportedLocales())
            ->toBeArray()
            ->toContain('en', 'pt', 'es');
    });

    it('returns allow custom attributes', function () {
        config()->set('pdf-invoices.allow_custom_attributes', false);

        $manager = new ConfigManager();
        expect($manager->allowCustomAttributes())->toBeFalse();
    });

    it('returns all configuration', function () {
        $manager = new ConfigManager();
        $all = $manager->all();

        expect($all)->toBeArray()
            ->toHaveKey('pdf')
            ->toHaveKey('storage')
            ->toHaveKey('currency')
            ->toHaveKey('localization');
    });
});
<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | PDF Generator Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which PDF generator engine to use. Supported drivers:
    | - "spatie": Uses Spatie's Laravel PDF with Puppeteer (Node.js based)
    | - "dompdf": Uses DomPDF (pure PHP implementation)
    |
    */
    'pdf' => [
        'driver' => env('INVOICES_PDF_DRIVER', 'spatie'),

        'template' => env('INVOICES_TEMPLATE', 'modern'),

        'base_path' => env('INVOICES_PDF_PATH', 'invoices'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the filesystem disk where generated PDFs will be stored.
    |
    */
    'storage' => [
        'driver' => 'laravel',

        'disk' => env('INVOICES_STORAGE_DISK', 'local'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Formatting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure currency formatting for invoices, including currency code,
    | symbol, and formatting locale.
    |
    */
    'currency' => [
        'driver' => env('INVOICES_CURRENCY_DRIVER', \Akira\PdfInvoices\Support\LaravelCurrencyFormatter::class),

        'code' => env('INVOICES_CURRENCY_CODE', 'EUR'),

        'symbol' => env('INVOICES_CURRENCY_SYMBOL', '€'),

        'locale' => env('INVOICES_LOCALE', 'en'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Configuration
    |--------------------------------------------------------------------------
    |
    | Configure locale and supported locales for invoice translations.
    |
    */
    'localization' => [
        'locale' => env('INVOICES_LOCALE', 'en'),

        'supported_locales' => ['en', 'pt'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Attributes
    |--------------------------------------------------------------------------
    |
    | Allow custom attributes to be added to invoice entities beyond
    | the standard defined properties.
    |
    */
    'allow_custom_attributes' => env('INVOICES_ALLOW_CUSTOM_ATTRIBUTES', true),
];

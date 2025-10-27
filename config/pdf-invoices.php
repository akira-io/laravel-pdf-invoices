<?php

declare(strict_types=1);

return [
    'pdf' => [
        'driver' => 'spatie',
        'template' => env('INVOICES_TEMPLATE', 'modern'),
        'base_path' => env('INVOICES_PDF_PATH', 'invoices'),
    ],

    'storage' => [
        'driver' => 'laravel',
        'disk' => env('INVOICES_STORAGE_DISK', 'local'),
    ],

    'currency' => [
        'driver' => env('INVOICES_CURRENCY_DRIVER', \Akira\PdfInvoices\Support\LaravelCurrencyFormatter::class),
        'code' => env('INVOICES_CURRENCY_CODE', 'EUR'),
        'symbol' => env('INVOICES_CURRENCY_SYMBOL', '€'),
        'locale' => env('INVOICES_LOCALE', 'en'),
    ],

    'localization' => [
        'locale' => env('INVOICES_LOCALE', 'en'),
        'supported_locales' => ['en', 'pt'],
    ],

    'allow_custom_attributes' => env('INVOICES_ALLOW_CUSTOM_ATTRIBUTES', true),
];

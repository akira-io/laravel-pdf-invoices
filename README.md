# Akira Laravel PDF Invoices

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akira-io/laravel-pdf-invoices.svg?style=flat-square)](https://packagist.org/packages/akira-io/laravel-pdf-invoices)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/akira-io/laravel-pdf-invoices/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/akira-io/laravel-pdf-invoices/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/akira-io/laravel-pdf-invoices/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/akira-io/laravel-pdf-invoices/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/akira-io/laravel-pdf-invoices.svg?style=flat-square)](https://packagist.org/packages/akira-io/laravel-pdf-invoices)

A modern, strictly typed, and extensible invoice generator for Laravel 12+ built with PHP 8.4 syntax. This package provides a clean builder pattern API, immutable data objects, and modular design inspired by LaravelDaily/laravel-invoices but rewritten from scratch with SOLID principles and Laravel best practices.

### Key Features

- **Builder Pattern**: Chainable, fluent API for creating invoices, sellers, buyers, and items
- **Immutable DTOs**: Type-safe data transfer objects with strict types and readonly properties
- **Custom Attributes**: Extensible system for adding custom fields to any entity
- **Multiple Templates**: Built-in minimal, modern, and branded Blade templates
- **Currency Formatting**: Flexible currency system with Laravel integration and custom formatters
- **PDF Generation**: Powered by Spatie's laravel-pdf with easy customization
- **Storage Abstraction**: Pluggable storage drivers for saving invoices
- **Quality Tools**: PHPStan level max, Laravel Pint, and Rector integration
- **Well Tested**: Comprehensive PestPHP test suite
- **Release Automation**: release-it integration for semantic versioning and changelogs

### Quick Example

```php
$invoice = InvoiceBuilder::make()
    ->seller(
        EntityBuilder::make()
            ->name('Akira Corporation')
            ->address('Luxembourg')
            ->vat('LU12345678')
            ->build()
    )
    ->buyer(
        EntityBuilder::make()
            ->name('Client Name')
            ->email('client@example.com')
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Consulting Service')
            ->unitPrice(100)
            ->quantity(5)
            ->tax(0.15)
            ->discount(0.10)
            ->build()
    )
    ->notes('Payment due in 10 days.')
    ->build();

$invoice->generatePdf()->save('invoices/invoice-001.pdf');
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-pdf-invoices.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-pdf-invoices)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require akira-io/laravel-pdf-invoices
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-pdf-invoices-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-pdf-invoices-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-pdf-invoices-views"
```

## Usage

```php
$pdfInvoices = new Akira\PdfInvoices();
echo $pdfInvoices->echoPhrase('Hello, Akira!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Kidiatoliny](https://github.com/kidiatoliny)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

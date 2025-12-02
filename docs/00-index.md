# Akira Laravel PDF Invoices

A modern, strictly typed, and extensible invoice generator for Laravel 12+ built with PHP 8.4 syntax.

This package provides a clean builder pattern API, immutable data objects, and modular design inspired by LaravelDaily/laravel-invoices but rewritten from scratch with SOLID principles and Laravel best practices.

## Features

- **Builder Pattern**: Chainable, fluent API for creating invoices, sellers, buyers, and items
- **Immutable DTOs**: Type-safe data transfer objects with strict types and readonly properties
- **Custom Attributes**: Extensible system for adding custom fields to any entity
- **Multiple Templates**: Built-in minimal, modern, and branded Blade templates
- **Currency Formatting**: Flexible currency system with Laravel integration and custom formatters
- **Multiple PDF Engines**: Choose between Spatie (Puppeteer) or DomPDF generators
- **Storage Abstraction**: Pluggable storage drivers for saving invoices
- **Quality Tools**: PHPStan level max, Laravel Pint, and Rector integration
- **Well Tested**: Comprehensive PestPHP test suite
- **Release Automation**: release-it integration for semantic versioning and changelogs

## Requirements

- PHP: ^8.4
- Laravel: ^12.0

## Installation

Install the package via Composer:

```bash
composer require akira/laravel-pdf-invoices
```

Install the peer dependency (required for PDF generation):

```bash
npm install puppeteer
```

Publish the configuration and views:

```bash
php artisan vendor:publish --tag="pdf-invoices-config"
php artisan vendor:publish --tag="pdf-invoices-views"
```

## Quick Start

```php
use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;

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
    ->invoiceNumber('INV-001')
    ->notes('Payment due in 10 days.')
    ->build();
```

## Documentation

1. [Usage](./01-usage.md) - Installation and usage examples
2. [PDF Generators](./02-pdf-generators.md) - Spatie vs DomPDF comparison and configuration
3. [Builders](./03-builders.md) - Explanation of builder pattern
4. [Custom Attributes](./04-attributes.md) - Handling custom attributes
5. [Templates](./05-templates.md) - Template customization
6. [Creating Custom Templates](./06-creating-custom-templates.md) - Build your own templates
7. [Customization](./07-customization.md) - Extending and overriding services
8. [Localization](./08-localization.md) - Multi-language support
9. [CSS Compilation](./09-css-compilation.md) - CSS setup and compilation
10. [Contributing](./10-contributing.md) - Contribution guide

## Testing

Run the test suite:

```bash
composer test
```

Generate coverage report:

```bash
composer test-coverage
```

## Code Quality

```bash
composer analyse      # Run PHPStan
composer format       # Run Laravel Pint
```

## License

The MIT License (MIT). See [License File](../LICENSE.md) for more information.


---

**Next:** [01 - Usage →](./01-usage.md)

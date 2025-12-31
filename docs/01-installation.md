# Installation

This guide covers installing Laravel PDF Invoices and configuring it for your Laravel application.

## Requirements

- PHP 8.4 or higher
- Laravel 12.0 or higher
- Composer

## Installing the Package

Install via Composer:

```bash
composer require akira/laravel-pdf-invoices
```

The package uses Laravel's auto-discovery feature and will automatically register the service provider `Akira\PdfInvoices\PdfInvoicesServiceProvider`.

## Choosing a PDF Engine

The package supports two PDF generation engines. Choose the one that fits your infrastructure.

### Option 1: Spatie PDF (Default)

Spatie PDF uses Puppeteer (Chrome headless) via Node.js for rendering. This provides the most accurate PDF output and best CSS support.

**Install Puppeteer:**

```bash
npm install puppeteer
```

**Configure in .env:**

```env
INVOICES_PDF_DRIVER=spatie
```

This is the default driver. If you don't set this variable, Spatie will be used automatically.

### Option 2: DomPDF

DomPDF is a pure PHP solution requiring no external dependencies like Node.js. It's simpler to deploy but has more limited CSS support.

**No additional installation needed** - DomPDF is included as a Composer dependency.

**Configure in .env:**

```env
INVOICES_PDF_DRIVER=dompdf
```

## Publishing Configuration

Publish the package configuration file to customize defaults:

```bash
php artisan vendor:publish --tag="pdf-invoices-config"
```

This creates `config/pdf-invoices.php` where you can configure:
- PDF generation driver
- Default template
- Storage disk
- Currency settings
- Localization options

## Publishing Views (Optional)

If you want to customize invoice templates, publish the views:

```bash
php artisan vendor:publish --tag="pdf-invoices-views"
```

Views will be published to `resources/views/vendor/pdf-invoices/`.

## Publishing Translations (Optional)

To customize translations or add new languages, publish translation files:

```bash
php artisan vendor:publish --tag="pdf-invoices-translations"
```

Translations will be published to `resources/lang/vendor/pdf-invoices/`.

## Publishing All Assets

To publish everything at once:

```bash
php artisan vendor:publish --provider="Akira\PdfInvoices\PdfInvoicesServiceProvider"
```

## Verifying Installation

You can verify the installation by checking if the configuration file exists:

```bash
php artisan config:show pdf-invoices
```

Or test a simple invoice generation:

```php
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;

$invoice = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Your Company')->build())
    ->buyer(EntityBuilder::make()->name('Client')->build())
    ->addItem(ItemBuilder::make()->description('Test')->unitPrice(100)->build())
    ->build();
```

If this runs without errors, your installation is successful.

**Previous:** [Roadmap](00-roadmap.md) | **Next:** [Configuration](02-configuration.md)

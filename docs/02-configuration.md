# Configuration

Laravel PDF Invoices is configured through the `config/pdf-invoices.php` file. This document explains each configuration option.

## Publishing Configuration

If you haven't already, publish the configuration file:

```bash
php artisan vendor:publish --tag="pdf-invoices-config"
```

## Configuration Options

### PDF Generation

Configure which PDF engine to use and how PDFs are generated.

```php
'pdf' => [
    'driver' => env('INVOICES_PDF_DRIVER', 'spatie'),
    'template' => env('INVOICES_TEMPLATE', 'modern'),
    'base_path' => env('INVOICES_PDF_PATH', 'invoices'),
],
```

**driver**: PDF generation engine. Options:
- `spatie` - Uses Spatie Laravel PDF with Puppeteer (default)
- `dompdf` - Uses DomPDF pure PHP implementation

**template**: Default template for rendering invoices. Built-in options:
- `modern` - Clean, professional design (default)
- `minimal` - Simple, compact layout
- `branded` - Rich layout with prominent branding

**base_path**: Base directory path where PDFs are saved when using `save()` method. Relative to the storage disk.

### Storage Configuration

Control where generated PDFs are stored.

```php
'storage' => [
    'driver' => 'laravel',
    'disk' => env('INVOICES_STORAGE_DISK', 'local'),
],
```

**driver**: Storage driver implementation. Currently only `laravel` is supported, which uses Laravel's filesystem abstraction via `StorageDriverContract`.

**disk**: Laravel filesystem disk to use. Any disk defined in `config/filesystems.php` is valid:
- `local` - Local filesystem (default)
- `public` - Public storage
- `s3` - Amazon S3
- Custom disks you define

### Currency Formatting

Configure how currency values are formatted in invoices.

```php
'currency' => [
    'driver' => env('INVOICES_CURRENCY_DRIVER', Akira\PdfInvoices\Support\LaravelCurrencyFormatter::class),
    'code' => env('INVOICES_CURRENCY_CODE', 'EUR'),
    'symbol' => env('INVOICES_CURRENCY_SYMBOL', '€'),
    'locale' => env('INVOICES_LOCALE', 'en'),
],
```

**driver**: Currency formatter class implementing `CurrencyFormatterContract`. Options:
- `Akira\PdfInvoices\Support\LaravelCurrencyFormatter` - Uses Laravel's `Number` helper (default)
- `Akira\PdfInvoices\Support\SimpleCurrencyFormatter` - Simple number formatting
- Custom class implementing `CurrencyFormatterContract`

**code**: ISO 4217 currency code (EUR, USD, GBP, etc.)

**symbol**: Currency symbol used by SimpleCurrencyFormatter

**locale**: Locale for currency formatting (en, pt, fr, etc.)

### Localization

Configure language and translation settings.

```php
'localization' => [
    'locale' => env('INVOICES_LOCALE', 'en'),
    'supported_locales' => ['en', 'pt', 'fr'],
],
```

**locale**: Default locale for invoice translations

**supported_locales**: Array of available locale codes. Built-in support includes:
- `en` - English
- `pt` - Portuguese  
- `fr` - French

### Custom Attributes

Control whether custom attributes can be added to invoice entities.

```php
'allow_custom_attributes' => env('INVOICES_ALLOW_CUSTOM_ATTRIBUTES', true),
```

When `true`, you can add arbitrary key-value pairs to `EntityData`, `ItemData`, and `InvoiceData` via the `set()` method and `attributes` parameter.

## Environment Variables

You can override any configuration value via environment variables in your `.env` file:

```env
# PDF Engine
INVOICES_PDF_DRIVER=spatie
INVOICES_TEMPLATE=modern
INVOICES_PDF_PATH=invoices

# Storage
INVOICES_STORAGE_DISK=s3

# Currency
INVOICES_CURRENCY_CODE=USD
INVOICES_CURRENCY_SYMBOL=$
INVOICES_LOCALE=en

# Custom Attributes
INVOICES_ALLOW_CUSTOM_ATTRIBUTES=true
```

## Accessing Configuration at Runtime

The package provides a `ConfigManager` class for type-safe configuration access:

```php
use Akira\PdfInvoices\Config\ConfigManager;

$config = app(ConfigManager::class);

$driver = $config->pdfDriver();        // 'spatie' or 'dompdf'
$template = $config->pdfTemplate();    // 'modern', 'minimal', 'branded'
$disk = $config->storageDisk();        // 'local', 's3', etc.
$currency = $config->currencyCode();   // 'EUR', 'USD', etc.
$locale = $config->locale();           // 'en', 'pt', 'fr'
```

All methods return sensible defaults if configuration values are missing or invalid.

**Previous:** [Installation](01-installation.md) | **Next:** [Quick Start](03-quick-start.md)

# Usage Guide

## Installation

Install via Composer:

```bash
composer require akira/laravel-pdf-invoices
```

Publish configuration and views:

```bash
php artisan vendor:publish --tag="pdf-invoices-config"
php artisan vendor:publish --tag="pdf-invoices-views"
```

## Basic Usage

### Creating an Invoice

```php
use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;

$invoice = InvoiceBuilder::make()
    ->seller(
        EntityBuilder::make()
            ->name('Your Company')
            ->address('123 Main St, City, Country')
            ->email('contact@company.com')
            ->vat('EU123456789')
            ->build()
    )
    ->buyer(
        EntityBuilder::make()
            ->name('Customer Name')
            ->address('456 Oak Ave, City, Country')
            ->email('customer@example.com')
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Web Development Service')
            ->unitPrice(150.00)
            ->quantity(10)
            ->tax(0.20)
            ->discount(0.05)
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Maintenance & Support')
            ->unitPrice(100.00)
            ->quantity(5)
            ->tax(0.20)
            ->build()
    )
    ->invoiceNumber('INV-2024-001')
    ->issuedAt(now())
    ->dueAt(now()->addDays(30))
    ->currency('EUR')
    ->notes('Payment due within 30 days. Please include invoice number with payment.')
    ->build();
```

### Generating PDF

```php
// Generate PDF as string
$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'modern');

// Save to file
$path = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->save($invoice, 'invoices/INV-2024-001.pdf', 'modern');
```

### Choosing a PDF Generator

Choose between Spatie (Puppeteer) or DomPDF in your `.env`:

```env
# Use Spatie (default) - requires Node.js and Puppeteer
INVOICES_PDF_DRIVER=spatie

# Or use DomPDF - pure PHP, no Node.js required
INVOICES_PDF_DRIVER=dompdf
```

The code remains the same regardless of which driver you use. See [PDF Generators documentation](./pdf-generators.md) for comparison and detailed setup instructions.

### Saving to Storage

```php
$storage = app(\Akira\PdfInvoices\Contracts\StorageDriverContract::class);

$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'modern');

$storage->save('invoices/INV-2024-001.pdf', $pdfContent);
```

## Configuration

The configuration file is located at `config/pdf-invoices.php`:

```php
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

    'allow_custom_attributes' => env('INVOICES_ALLOW_CUSTOM_ATTRIBUTES', true),
];
```

### Environment Variables

```env
# PDF Generation Driver (spatie or dompdf)
INVOICES_PDF_DRIVER=spatie

# Template selection
INVOICES_TEMPLATE=modern

# PDF file path
INVOICES_PDF_PATH=invoices

# Storage configuration
INVOICES_STORAGE_DISK=local

# Currency settings
INVOICES_CURRENCY_CODE=EUR
INVOICES_CURRENCY_SYMBOL=€

# Localization
INVOICES_LOCALE=en

# Feature flags
INVOICES_ALLOW_CUSTOM_ATTRIBUTES=true
```

## Templates

Three templates are available:

- **minimal**: Clean and simple design
- **modern**: Modern design with gradient header
- **branded**: Professional branded template

Switch templates in configuration or per-invoice:

```php
$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'minimal');
```

## Invoice Totals

Access calculated totals:

```php
$invoice->getSubtotal();           // Sum of all items
$invoice->getTotalDiscount();      // Total discount amount
$invoice->getSubtotalAfterDiscount(); // Subtotal minus discounts
$invoice->getTotalTax();           // Total tax amount
$invoice->getTotal();              // Final total (subtotal - discounts + tax)
```

## Item Totals

Access item-level calculations:

```php
$item->getSubtotal();              // unitPrice * quantity
$item->getDiscountAmount();        // subtotal * discount
$item->getSubtotalAfterDiscount(); // subtotal - discount
$item->getTaxAmount();             // subtotal after discount * tax
$item->getTotal();                 // subtotal after discount + tax
```

## Custom Attributes

Add custom fields to any entity:

```php
$seller = EntityBuilder::make()
    ->name('Company')
    ->set('registration_number', 'REG-123456')
    ->set('country', 'Luxembourg')
    ->build();

$buyer = EntityBuilder::make()
    ->name('Customer')
    ->set('customer_id', 'CUST-789')
    ->build();

$invoice = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->set('po_number', 'PO-2024-001')
    ->set('project_code', 'PROJ-XYZ')
    ->build();

// Access attributes
$invoice->get('po_number');        // 'PO-2024-001'
$invoice->get('missing', 'default'); // 'default'
$invoice->has('po_number');        // true
```

## Working with Dates

```php
use DateTime;

$invoice = InvoiceBuilder::make()
    ->seller(...)
    ->buyer(...)
    ->issuedAt(new DateTime('2024-01-15'))
    ->dueAt(new DateTime('2024-02-15'))
    ->build();

// Access dates
$invoice->issuedAt;    // DateTime object
$invoice->dueAt;       // DateTime object
```

## Error Handling

The package throws exceptions for invalid states:

```php
try {
    $invoice = InvoiceBuilder::make()
        // Missing seller and buyer
        ->build();
} catch (InvalidArgumentException $e) {
    // Handle error
}
```
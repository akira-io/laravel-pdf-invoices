# Quick Start

This guide shows you how to generate your first PDF invoice in under 5 minutes.

## Basic Invoice

The simplest invoice requires a seller, buyer, and at least one item:

```php
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;

// Build the invoice data
$invoiceData = InvoiceBuilder::make()
    ->seller(
        EntityBuilder::make()
            ->name('Acme Corporation')
            ->address('123 Business Street, Tech City, TC 12345')
            ->email('billing@acme.com')
            ->vat('US123456789')
            ->build()
    )
    ->buyer(
        EntityBuilder::make()
            ->name('Client Company Ltd')
            ->address('456 Client Avenue, Customer Town, CT 67890')
            ->email('accounts@client.com')
            ->vat('GB987654321')
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Professional Services - Web Development')
            ->unitPrice(150.00)
            ->quantity(40)
            ->tax(0.19)
            ->build()
    )
    ->invoiceNumber('INV-2024-001')
    ->issuedAt(now())
    ->dueAt(now()->addDays(30))
    ->currency('EUR')
    ->build();

// Generate the PDF
$generator = app(PdfGeneratorContract::class);
$pdfContent = $generator->generate($invoiceData);

// Return as response
return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'inline; filename="invoice.pdf"',
]);
```

## Generating and Downloading

To generate a PDF and offer it as a download:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Your Company')->build())
    ->buyer(EntityBuilder::make()->name('Client')->build())
    ->addItem(ItemBuilder::make()->description('Service')->unitPrice(500)->build())
    ->invoiceNumber('INV-001')
    ->build();

$generator = app(PdfGeneratorContract::class);
$pdfContent = $generator->generate($invoiceData);

return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'attachment; filename="invoice-001.pdf"',
]);
```

## Saving to Storage

To save a PDF to your configured storage disk:

```php
use Akira\PdfInvoices\Contracts\StorageDriverContract;

$invoiceData = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Your Company')->build())
    ->buyer(EntityBuilder::make()->name('Client')->build())
    ->addItem(ItemBuilder::make()->description('Product')->unitPrice(100)->build())
    ->invoiceNumber('INV-002')
    ->build();

// Generate PDF content
$generator = app(PdfGeneratorContract::class);
$pdfContent = $generator->generate($invoiceData);

// Save to storage
$storage = app(StorageDriverContract::class);
$path = $storage->save('invoices/2024/invoice-002.pdf', $pdfContent);

// Path returned: 'invoices/2024/invoice-002.pdf'
```

## Multiple Line Items

Add multiple items with different tax rates and discounts:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Your Company')->build())
    ->buyer(EntityBuilder::make()->name('Client')->build())
    ->addItem(
        ItemBuilder::make()
            ->description('Hosting - Annual Plan')
            ->unitPrice(120.00)
            ->quantity(1)
            ->tax(0.19)
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Domain Registration')
            ->unitPrice(15.00)
            ->quantity(2)
            ->tax(0.19)
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('SSL Certificate')
            ->unitPrice(50.00)
            ->quantity(1)
            ->tax(0.19)
            ->discount(0.10) // 10% discount
            ->build()
    )
    ->invoiceNumber('INV-003')
    ->issuedAt(now())
    ->dueAt(now()->addDays(14))
    ->build();
```

## Using Different Templates

Choose from three built-in templates:

```php
$generator = app(PdfGeneratorContract::class);

// Modern template (default)
$pdf = $generator->generate($invoiceData, 'modern');

// Minimal template
$pdf = $generator->generate($invoiceData, 'minimal');

// Branded template
$pdf = $generator->generate($invoiceData, 'branded');
```

## Adding Notes

Include payment terms or additional information:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Your Company')->build())
    ->buyer(EntityBuilder::make()->name('Client')->build())
    ->addItem(ItemBuilder::make()->description('Service')->unitPrice(200)->build())
    ->invoiceNumber('INV-004')
    ->notes('Payment is due within 30 days. Please include invoice number with payment.')
    ->build();
```

## Using Carbon Dates

The package supports both `Carbon` and `CarbonImmutable` instances:

```php
use Carbon\Carbon;

$invoiceData = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Your Company')->build())
    ->buyer(EntityBuilder::make()->name('Client')->build())
    ->addItem(ItemBuilder::make()->description('Service')->unitPrice(300)->build())
    ->invoiceNumber('INV-005')
    ->issuedAt(Carbon::today())
    ->dueAt(Carbon::today()->addMonth())
    ->build();
```

## Next Steps

Now that you can generate basic invoices, explore:

- **Builder Pattern** - Detailed guide on using builders
- **Data Transfer Objects** - Understanding invoice data structures
- **Templates** - Customizing invoice appearance
- **Localization** - Multi-language invoice support

**Previous:** [Configuration](02-configuration.md) | **Next:** [Builders](04-builders.md)

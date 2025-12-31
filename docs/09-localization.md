# Localization

Laravel PDF Invoices supports multiple languages for invoice labels and terminology. The package includes English, Portuguese, and French translations.

## Available Locales

- **en** - English (default)
- **pt** - Portuguese
- **fr** - French

## Configuration

### Default Locale

Set the default locale in `config/pdf-invoices.php`:

```php
'localization' => [
    'locale' => env('INVOICES_LOCALE', 'en'),
    'supported_locales' => ['en', 'pt', 'fr'],
],
```

Or via environment variable:

```env
INVOICES_LOCALE=en
```

### Per-Invoice Locale

Override the default locale for specific invoices:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->locale('fr') // French invoice
    ->build();
```

## Translation Keys

All translation keys are defined in `resources/lang/{locale}/invoice.php`:

```php
return [
    'invoice' => 'INVOICE',
    'invoice_number' => 'Invoice Number',
    'invoice_date' => 'Invoice Date',
    'due_date' => 'Due Date',
    'invoice_from' => 'Invoice From',
    'invoice_to' => 'Invoice To',
    'bill_from' => 'Bill From',
    'bill_to' => 'Bill To',
    'from' => 'From',
    'to' => 'To',
    'description' => 'Description',
    'unit_price' => 'Unit Price',
    'quantity' => 'Quantity',
    'qty' => 'Qty',
    'tax' => 'Tax',
    'tax_rate' => 'Tax Rate',
    'amount' => 'Amount',
    'subtotal' => 'Subtotal',
    'discount' => 'Discount',
    'total' => 'Total',
    'total_due' => 'Total Due',
    'terms_and_notes' => 'Terms & Notes',
    'notes' => 'Notes',
    'thank_you' => 'Thank you for your business!',
    'all_rights_reserved' => 'All rights reserved',
    'vat_id' => 'VAT ID',
    'vat' => 'VAT',
    'issued' => 'Issued',
    'due' => 'Due',
    'page' => 'Page',
];
```

## InvoiceTranslator

The `InvoiceTranslator` class provides translation functionality in templates.

### Methods

**translate(string $key, array $replace = []): string**

Translates a key with optional replacements.

```php
$translator = new InvoiceTranslator('en');
$text = $translator->translate('invoice_number');
// Returns: "Invoice Number"
```

**__(string $key, array $replace = []): string**

Alias for `translate()` method.

```php
$text = $translator->__('total');
// Returns: "Total"
```

**getLocale(): string**

Returns the current locale.

```php
$locale = $translator->getLocale();
// Returns: "en"
```

**withLocale(string $locale): self**

Creates a new translator instance with a different locale.

```php
$frenchTranslator = $translator->withLocale('fr');
```

### Usage in Templates

Templates receive a `$translator` variable:

```blade
<h1>{{ $translator->__('invoice') }}</h1>
<p>{{ $translator->__('invoice_number') }}: {{ $invoice->invoiceNumber }}</p>

<table>
    <thead>
        <tr>
            <th>{{ $translator->__('description') }}</th>
            <th>{{ $translator->__('quantity') }}</th>
            <th>{{ $translator->__('unit_price') }}</th>
            <th>{{ $translator->__('total') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unitPrice, 2) }}</td>
                <td>{{ number_format($item->getTotal(), 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

## Adding New Languages

### Publishing Translations

Publish translation files:

```bash
php artisan vendor:publish --tag="pdf-invoices-translations"
```

Files are copied to `resources/lang/vendor/pdf-invoices/`.

### Creating a New Language

Create a new language directory and translation file:

```bash
mkdir -p resources/lang/vendor/pdf-invoices/de
touch resources/lang/vendor/pdf-invoices/de/invoice.php
```

Add translations:

```php
<?php

// resources/lang/vendor/pdf-invoices/de/invoice.php

return [
    'invoice' => 'RECHNUNG',
    'invoice_number' => 'Rechnungsnummer',
    'invoice_date' => 'Rechnungsdatum',
    'due_date' => 'Fälligkeitsdatum',
    'invoice_from' => 'Rechnung von',
    'invoice_to' => 'Rechnung an',
    'bill_from' => 'Rechnungsadresse',
    'bill_to' => 'Empfänger',
    'from' => 'Von',
    'to' => 'An',
    'description' => 'Beschreibung',
    'unit_price' => 'Einzelpreis',
    'quantity' => 'Menge',
    'qty' => 'Anz.',
    'tax' => 'Steuer',
    'tax_rate' => 'Steuersatz',
    'amount' => 'Betrag',
    'subtotal' => 'Zwischensumme',
    'discount' => 'Rabatt',
    'total' => 'Gesamt',
    'total_due' => 'Gesamtbetrag',
    'terms_and_notes' => 'Bedingungen & Hinweise',
    'notes' => 'Hinweise',
    'thank_you' => 'Vielen Dank für Ihr Geschäft!',
    'all_rights_reserved' => 'Alle Rechte vorbehalten',
    'vat_id' => 'USt-IdNr.',
    'vat' => 'MwSt.',
    'issued' => 'Ausgestellt',
    'due' => 'Fällig',
    'page' => 'Seite',
];
```

### Register the Locale

Update configuration to include the new locale:

```php
// config/pdf-invoices.php
'localization' => [
    'locale' => env('INVOICES_LOCALE', 'en'),
    'supported_locales' => ['en', 'pt', 'fr', 'de'],
],
```

### Use the New Language

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->locale('de') // German invoice
    ->build();
```

## Translation with Replacements

Use placeholders in translations:

```php
// resources/lang/vendor/pdf-invoices/en/invoice.php
return [
    'payment_due' => 'Payment due by :date',
    'items_count' => 'Total of :count items',
];
```

Use in code:

```php
$translator->translate('payment_due', ['date' => '2024-12-31']);
// Returns: "Payment due by 2024-12-31"

$translator->__('items_count', ['count' => 5]);
// Returns: "Total of 5 items"
```

## Currency Formatting by Locale

The `LaravelCurrencyFormatter` respects locale settings:

```php
use Akira\PdfInvoices\Support\LaravelCurrencyFormatter;

$formatter = new LaravelCurrencyFormatter();

// English locale
$formatter->format(1234.56, 'EUR', 'en');
// Returns: "€1,234.56"

// French locale
$formatter->format(1234.56, 'EUR', 'fr');
// Returns: "1 234,56 €"

// German locale
$formatter->format(1234.56, 'EUR', 'de');
// Returns: "1.234,56 €"
```

## Date Formatting by Locale

Use Carbon's localized date formatting:

```php
use Illuminate\Support\Facades\Date;

// Set locale
Date::setLocale('fr');

$date = Date::parse('2024-12-31');

// Localized format
$date->isoFormat('LL');
// Returns: "31 décembre 2024"

// Use in template
{{ $invoice->issuedAt?->locale($invoice->locale ?? 'en')->isoFormat('LL') }}
```

## Best Practices

### Detect User Locale

Automatically set invoice locale based on buyer:

```php
$buyerLocale = $user->locale ?? app()->getLocale();

$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->locale($buyerLocale)
    ->build();
```

### Fallback Locale

Always provide a fallback for missing translations:

```php
$translator->translate('custom_key', [], 'en') 
    ?? $translator->translate('custom_key', [], config('app.fallback_locale'));
```

### Store Locale with Invoice

When persisting invoices to database, store the locale:

```php
InvoiceModel::create([
    'invoice_number' => $invoiceData->invoiceNumber,
    'locale' => $invoiceData->locale ?? 'en',
    // ... other fields
]);
```

### Multi-Tenant Localization

Set locale per tenant:

```php
$tenant = auth()->user()->tenant;

$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->locale($tenant->default_locale)
    ->currency($tenant->default_currency)
    ->build();
```

**Previous:** [Templates](08-templates.md) | **Next:** [Currency Formatting](10-currency-formatting.md)

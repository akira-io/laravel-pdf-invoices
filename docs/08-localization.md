# Localization (i18n)

## Supported Languages

The Laravel PDF Invoices package supports the following languages:

- **English** (`en`)
- **Portuguese** (`pt`)
- **French** (`fr`)

## Configuration

### Setting the Default Locale

Set the locale in your `.env` file:

```env
INVOICES_LOCALE=en
```

Supported values: `en`, `pt`, `fr`

### Available Configuration

In `config/pdf-invoices.php`:

```php
'localization' => [
    'locale' => env('INVOICES_LOCALE', 'en'),
    'supported_locales' => ['en', 'pt', 'fr'],
],
```

## Setting Locale in InvoiceBuilder

You can set the locale directly when building an invoice using the `locale()` method:

```php
use Akira\PdfInvoices\Builder\InvoiceBuilder;

$invoice = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->locale('pt')
    ->build();
```

This allows you to generate invoices in different languages for different customers:

```php
// Portuguese invoice
$invoicePt = InvoiceBuilder::make()
    ->locale('pt')
    // ... other methods
    ->build();

// French invoice
$invoiceFr = InvoiceBuilder::make()
    ->locale('fr')
    // ... other methods
    ->build();

// English invoice (default)
$invoiceEn = InvoiceBuilder::make()
    ->locale('en')
    // ... other methods
    ->build();
```

The locale set in the builder will be used for translations in the generated PDF.

## Using the Translator

### In Blade Templates

Use the translation helper in your invoice templates:

```blade
<h1>{{ __('pdf-invoices::invoice.invoice') }}</h1>
<p>{{ __('pdf-invoices::invoice.invoice_date') }}</p>
```

### In PHP Code

Use the `InvoiceTranslator` class:

```php
use Akira\PdfInvoices\Support\InvoiceTranslator;

$translator = new InvoiceTranslator('pt');
echo $translator->translate('invoice');
echo $translator->__('subtotal');
```

### With Dynamic Locale

Create a translator with a specific locale:

```php
$translator = new InvoiceTranslator('en');
$ptTranslator = $translator->withLocale('pt');
```

## Available Translation Keys

### Invoice Labels

- `invoice` - "INVOICE" / "FATURA"
- `invoice_number` - "Invoice Number" / "Número da Fatura"
- `invoice_date` - "Invoice Date" / "Data da Fatura"
- `due_date` - "Due Date" / "Data de Vencimento"

### From/To Labels

- `invoice_from` - "Invoice From" / "Fatura De"
- `invoice_to` - "Invoice To" / "Fatura Para"
- `bill_from` - "Bill From" / "Cobrança De"
- `bill_to` - "Bill To" / "Cobrança Para"
- `from` - "From" / "De"
- `to` - "To" / "Para"

### Line Items

- `description` - "Description" / "Descrição"
- `unit_price` - "Unit Price" / "Preço Unitário"
- `quantity` - "Quantity" / "Quantidade"
- `qty` - "Qty" / "Qtd"
- `tax` - "Tax" / "Imposto"
- `tax_rate` - "Tax Rate" / "Taxa de Imposto"
- `amount` - "Amount" / "Valor"

### Totals

- `subtotal` - "Subtotal" / "Subtotal"
- `discount` - "Discount" / "Desconto"
- `total` - "Total" / "Total"
- `total_due` - "Total Due" / "Total a Pagar"

### Other Labels

- `terms_and_notes` - "Terms & Notes" / "Termos & Observações"
- `notes` - "Notes" / "Observações"
- `thank_you` - "Thank you for your business!" / "Obrigado pelo seu negócio!"
- `all_rights_reserved` - "All rights reserved" / "Todos os direitos reservados"
- `vat_id` - "VAT ID" / "NIF"
- `vat` - "VAT" / "IVA"
- `issued` - "Issued" / "Emitido"
- `due` - "Due" / "Vence"

## Adding New Languages

To add support for a new language:

1. Create a translation file in `resources/lang/{locale}/invoice.php`:

```php
<?php

declare(strict_types=1);

return [
    'invoice' => 'Your Translation',
    'subtotal' => 'Your Translation',
    // ... other keys
];
```

2. Add the locale to the supported locales in `config/pdf-invoices.php`:

```php
'localization' => [
    'locale' => env('INVOICES_LOCALE', 'en'),
    'supported_locales' => ['en', 'pt', 'es', 'fr'],
],
```

3. Use the new locale:

```php
$translator = new InvoiceTranslator('es');
echo $translator->__('invoice');
```

## Publishing Translations

To customize translations in your application, publish the language files:

```bash
php artisan vendor:publish --provider="Akira\PdfInvoices\PdfInvoicesServiceProvider" --tag="pdf-invoices-lang"
```

This will copy the translation files to `resources/lang/vendor/pdf-invoices/`.

## Laravel Locale Awareness

The package respects the Laravel application's locale setting:

```php
app()->setLocale('pt');

$translator = new InvoiceTranslator(app()->getLocale());
```

## Fallback Behavior

If a translation key is not found:

1. The translator attempts to use the specified locale
2. Falls back to the default locale (usually 'en')
3. Returns the key itself if no translation is found

---

**← Previous:** [07 - Customization](./07-customization.md) | **Next:
** [09 - CSS Compilation →](./09-css-compilation.md)

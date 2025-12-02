# Templates

This package includes three professional, production-ready templates built with Tailwind CSS v4.

## Available Templates

### 1. Minimal

A clean, simple design focused on readability.

- Minimal color usage
- Clear hierarchy
- Professional appearance
- Best for: Conservative businesses, legal documents

```php
$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'minimal');
```

### 2. Modern

A contemporary design with gradient header and color accents.

- Modern gradient header
- Color-coded sections
- Professional styling
- Best for: Tech companies, creative agencies

```php
$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'modern');
```

### 3. Branded

A professional branded template with corporate styling.

- Branded color scheme
- Detailed sections
- Corporate appearance
- Best for: Enterprise businesses, premium services

```php
$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'branded');
```

## Setting Default Template

Configure the default template in `config/pdf-invoices.php`:

```php
return [
    'pdf' => [
        'template' => env('INVOICES_TEMPLATE', 'modern'),
    ],
];
```

Or via environment variable:

```env
INVOICES_TEMPLATE=modern
```

## Template Variables

All templates have access to the invoice object and can display:

### Invoice Properties

```blade
{{ $invoice->invoiceNumber }}
{{ $invoice->currency }}
{{ $invoice->notes }}
{{ $invoice->issuedAt->format('d M Y') }}
{{ $invoice->dueAt->format('d M Y') }}
```

### Seller Information

```blade
{{ $invoice->seller->name }}
{{ $invoice->seller->address }}
{{ $invoice->seller->email }}
{{ $invoice->seller->vatNumber }}
{{ $invoice->seller->logoUrl }}
```

### Buyer Information

```blade
{{ $invoice->buyer->name }}
{{ $invoice->buyer->address }}
{{ $invoice->buyer->email }}
{{ $invoice->buyer->vatNumber }}
```

### Line Items

```blade
@foreach($invoice->items as $item)
    {{ $item->description }}
    {{ $item->unitPrice }}
    {{ $item->quantity }}
    {{ $item->tax }}
    {{ $item->discount }}
    {{ $item->getTotal() }}
@endforeach
```

### Calculations

```blade
{{ $invoice->getSubtotal() }}
{{ $invoice->getTotalDiscount() }}
{{ $invoice->getTotalTax() }}
{{ $invoice->getTotal() }}
```

### Custom Attributes

```blade
@if($invoice->has('po_number'))
    PO: {{ $invoice->get('po_number') }}
@endif

@if($invoice->seller->has('registration_number'))
    Reg: {{ $invoice->seller->get('registration_number') }}
@endif
```

## Creating Custom Templates

Create a new template in `resources/views/pdf-invoices/pdf/templates/`:

```blade
<!-- resources/views/pdf-invoices/pdf/templates/custom.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoiceNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .invoice-header {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>{{ $invoice->seller->name }}</h1>
        <h2>Invoice #{{ $invoice->invoiceNumber }}</h2>
    </div>

    <div class="addresses">
        <div>
            <h3>Bill From</h3>
            <p>{{ $invoice->seller->name }}</p>
            @if($invoice->seller->address)
                <p>{{ $invoice->seller->address }}</p>
            @endif
        </div>

        <div>
            <h3>Bill To</h3>
            <p>{{ $invoice->buyer->name }}</p>
            @if($invoice->buyer->address)
                <p>{{ $invoice->buyer->address }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->unitPrice, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->tax * 100, 0) }}%</td>
                    <td>{{ number_format($item->getTotal(), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Total: {{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</p>
    </div>

    @if($invoice->notes)
        <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
    @endif
</body>
</html>
```

Use your custom template:

```php
$pdfContent = app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'custom');
```

## Tailwind CSS

All templates use Tailwind CSS v4. The CSS is compiled and available in `resources/css/compiled.css`.

Build CSS for development:

```bash
npm run dev
```

Build for production:

```bash
npm run build
```

## HTML to PDF Conversion

Templates are converted to PDF using Spatie's laravel-pdf, which uses Chromium/Puppeteer for accurate rendering.

## Styling Guidelines

- Use Tailwind CSS utility classes
- Keep templates responsive
- Test with different invoice data
- Ensure proper page breaks for multi-page invoices
- Use standard fonts that support PDF rendering
- Avoid JavaScript (PDF conversion happens server-side)

## Customization Example

Create a template that uses your brand colors:

```blade
<style>
    :root {
        --brand-primary: #667eea;
        --brand-secondary: #764ba2;
    }

    .header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
    }
</style>
```

## Performance

- Templates render quickly with laravel-pdf
- CSS is compiled to minimize overhead
- Optimize images used in templates
- Use simple layouts for faster generation---

**← Previous:** [04 - ](./04-attributes.md) | **Next:** [06 -  →](./06-creating-custom-templates.md)

---

**← Previous:** [04 - Custom Attributes](./04-attributes.md) | **Next:** [06 - Creating Custom Templates →](./06-creating-custom-templates.md)

# Templates

Laravel PDF Invoices includes three professionally designed templates. All templates are built with Tailwind CSS and support localization.

## Available Templates

### modern (Default)

A clean, professional design with balanced visual hierarchy.

**Use Cases:**
- General business invoices
- Professional services
- B2B transactions
- When brand neutrality is desired

**Characteristics:**
- Clear section separation
- Balanced whitespace
- Professional typography
- Neutral color scheme

**Usage:**

```php
$generator->generate($invoiceData, 'modern');
```

### minimal

A compact, space-efficient layout that maximizes information density.

**Use Cases:**
- High-volume invoicing
- Simple product sales
- When paper savings matter
- Archival storage optimization

**Characteristics:**
- Dense information display
- Minimal margins and spacing
- Smaller file size
- Efficient use of page space

**Usage:**

```php
$generator->generate($invoiceData, 'minimal');
```

### branded

A rich layout emphasizing brand identity with prominent visual elements.

**Use Cases:**
- Client-facing invoices
- Marketing-conscious businesses
- Premium services
- Strong brand identity

**Characteristics:**
- Large logo placement
- Bold typography
- Colorful accents
- Prominent contact information

**Usage:**

```php
$generator->generate($invoiceData, 'branded');
```

## Template Location

All templates are Blade views located at:

```
resources/views/pdf/templates/
├── modern.blade.php
├── minimal.blade.php
└── branded.blade.php
```

## Template Data

Templates receive three variables:

**$invoice** - `InvoiceData` instance containing all invoice information

```blade
Invoice #{{ $invoice->invoiceNumber }}
Total: {{ $invoice->getTotal() }}
```

**$compiledCss** - Compiled CSS string to be inlined

```blade
<style>{!! $compiledCss !!}</style>
```

**$translator** - `InvoiceTranslator` instance for localized strings

```blade
{{ $translator->translate('invoice_number') }}
{{ $translator->__('total') }}
```

## Customizing Templates

### Publishing Templates

Publish package views to your application:

```bash
php artisan vendor:publish --tag="pdf-invoices-views"
```

Views are copied to:

```
resources/views/vendor/pdf-invoices/pdf/templates/
```

### Editing Published Templates

Modify any template in `resources/views/vendor/pdf-invoices/pdf/templates/`:

```blade
{{-- modern.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>{!! $compiledCss !!}</style>
</head>
<body>
    {{-- Your custom layout --}}
    <div class="invoice-header">
        <h1>{{ $translator->__('invoice') }}</h1>
        <p>#{{ $invoice->invoiceNumber }}</p>
    </div>
    
    {{-- Seller and Buyer --}}
    <div class="entities">
        <div class="seller">
            <strong>{{ $invoice->seller->name }}</strong>
            @if($invoice->seller->address)
                <p>{{ $invoice->seller->address }}</p>
            @endif
        </div>
        
        <div class="buyer">
            <strong>{{ $invoice->buyer->name }}</strong>
            @if($invoice->buyer->address)
                <p>{{ $invoice->buyer->address }}</p>
            @endif
        </div>
    </div>
    
    {{-- Items Table --}}
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
    
    {{-- Totals --}}
    <div class="totals">
        <div>{{ $translator->__('subtotal') }}: {{ number_format($invoice->getSubtotal(), 2) }}</div>
        <div>{{ $translator->__('tax') }}: {{ number_format($invoice->getTotalTax(), 2) }}</div>
        <div><strong>{{ $translator->__('total') }}: {{ number_format($invoice->getTotal(), 2) }}</strong></div>
    </div>
</body>
</html>
```

### Creating New Templates

Create a new Blade file in `resources/views/vendor/pdf-invoices/pdf/templates/`:

```bash
touch resources/views/vendor/pdf-invoices/pdf/templates/custom.blade.php
```

Use it:

```php
$generator->generate($invoiceData, 'custom');
```

## CSS Styling

Templates use compiled Tailwind CSS located at `resources/css/compiled.css`.

### Modifying Styles

To customize styles:

1. Edit source files in the package (advanced)
2. Override specific classes in your template
3. Add inline styles in published templates

**Inline style override example:**

```blade
<style>
    {!! $compiledCss !!}
    
    /* Your custom overrides */
    .invoice-header {
        background-color: #1e40af;
        color: white;
    }
    
    .totals {
        font-size: 1.2em;
    }
</style>
```

## Template Inheritance

Templates do not use Blade inheritance by design. Each template is self-contained for PDF generation reliability.

## Logo Handling

Display entity logos in templates:

```blade
@if($invoice->seller->logoUrl)
    <img src="{{ $invoice->seller->logoUrl }}" alt="Logo" class="logo">
@endif
```

**Logo sources:**
- Public URLs: `https://example.com/logo.png`
- Local paths: `{{ asset('images/logo.png') }}`
- Data URIs: `data:image/png;base64,...`

## Date Formatting

Format dates using Carbon:

```blade
{{-- Default format --}}
{{ $invoice->issuedAt?->format('Y-m-d') }}

{{-- Localized format --}}
{{ $invoice->issuedAt?->isoFormat('LL') }}

{{-- Custom format --}}
{{ $invoice->issuedAt?->format('F j, Y') }}
```

## Currency Formatting

Use the currency formatter:

```blade
@inject('formatter', 'Akira\PdfInvoices\Contracts\CurrencyFormatterContract')

{{ $formatter->format($invoice->getTotal(), $invoice->currency, $invoice->locale ?? 'en') }}
```

Or format manually:

```blade
{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}
```

## Conditional Content

Show content based on data presence:

```blade
@if($invoice->notes)
    <div class="notes">
        <strong>{{ $translator->__('notes') }}:</strong>
        <p>{{ $invoice->notes }}</p>
    </div>
@endif

@if($invoice->buyer->vatNumber)
    <p>{{ $translator->__('vat') }}: {{ $invoice->buyer->vatNumber }}</p>
@endif
```

## Custom Attributes

Access custom attributes in templates:

```blade
@if($invoice->has('po_number'))
    <p>PO Number: {{ $invoice->get('po_number') }}</p>
@endif

@if($item->has('sku'))
    <span class="sku">SKU: {{ $item->get('sku') }}</span>
@endif
```

## Multi-Page Support

For invoices with many items, templates automatically handle pagination when rendered by the PDF engine.

## Debugging Templates

Test template rendering without PDF generation:

```php
Route::get('/preview-invoice', function () {
    $invoiceData = InvoiceBuilder::make()
        ->seller(EntityBuilder::make()->name('Test Seller')->build())
        ->buyer(EntityBuilder::make()->name('Test Buyer')->build())
        ->addItem(ItemBuilder::make()->description('Item')->unitPrice(100)->build())
        ->build();
    
    $compiledCss = file_get_contents(base_path('vendor/akira/laravel-pdf-invoices/resources/css/compiled.css'));
    $translator = new \Akira\PdfInvoices\Support\InvoiceTranslator('en');
    
    return view('pdf-invoices::pdf.templates.modern', [
        'invoice' => $invoiceData,
        'compiledCss' => $compiledCss,
        'translator' => $translator,
    ]);
});
```

**Previous:** [Storage](07-storage.md) | **Next:** [Localization](09-localization.md)

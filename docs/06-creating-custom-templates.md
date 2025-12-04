# Creating Custom Invoice Templates

This guide explains how to create custom invoice templates for the Laravel PDF Invoices package.

## Overview

The PDF Invoices package comes with three built-in templates:

- **modern** - Clean, professional design with primary color accents
- **minimal** - Minimalist style with subtle borders
- **branded** - Executive design with gradient header and white text

You can create your own custom templates by extending the package's layout system.

## Template Structure

Each template is a Blade file located in `resources/views/pdf/templates/`. Templates receive three variables:

- `$invoice` - The `InvoiceData` DTO containing all invoice information
- `$translator` - The `InvoiceTranslator` for translating labels
- `$compiledCss` - The compiled Tailwind CSS for styling

### Invoice Data Structure

```php
$invoice->invoiceNumber      // String: Invoice number (e.g., "INV-2024-001")
$invoice->seller             // EntityData: Seller information
$invoice->buyer              // EntityData: Buyer information
$invoice->items              // Array: Array of ItemData objects
$invoice->issuedAt           // DateTime: Invoice issue date
$invoice->dueAt              // DateTime: Invoice due date
$invoice->currency           // String: Currency code (e.g., "EUR")
$invoice->notes              // String: Additional notes
$invoice->attributes()       // Array: Custom attributes

// EntityData (seller/buyer)
$invoice->seller->name       // String: Entity name
$invoice->seller->address    // String: Address
$invoice->seller->email      // String: Email
$invoice->seller->vatNumber  // String: VAT number
$invoice->seller->attributes()  // Array: Custom attributes

// ItemData
$item->description           // String: Item description
$item->quantity              // Float: Quantity
$item->unitPrice             // Float: Unit price
$item->vatRate               // Float: VAT rate (e.g., 23)
$item->getTotal()            // Float: Total amount
$item->getTax()              // Float: Tax amount

// Invoice calculations
$invoice->getSubtotal()      // Float: Subtotal without tax
$invoice->getTotalTax()      // Float: Total tax amount
$invoice->getTotal()         // Float: Total with tax
$invoice->getTotalDiscount() // Float: Total discount
```

## Creating a Custom Template

### Step 1: Create the Template File

Create a new Blade file in `resources/views/pdf/templates/` in your application:

```bash
# If you're extending the package in your app
touch resources/views/vendor/pdf-invoices/pdf/templates/custom.blade.php
```

### Step 2: Structure Your Template

Here's a minimal custom template example:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoiceNumber }}</title>
    <style>
        {!! $compiledCss !!}
    </style>
</head>
<body class="bg-white">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="px-12 py-10 border-b-4 border-indigo-600">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $invoice->seller->name }}</h1>
            <div class="text-right">
                <p class="text-xs uppercase text-gray-500 font-semibold mb-2">{{ $translator->__('invoice') }}</p>
                <p class="text-5xl font-bold text-indigo-600">#{{ $invoice->invoiceNumber }}</p>
            </div>
        </div>

        <!-- Content -->
        <div class="px-12 py-10">
            <!-- Dates -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">{{ $translator->__('issued') }}</p>
                    <p class="text-lg font-semibold">{{ $invoice->issuedAt->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">{{ $translator->__('due') }}</p>
                    <p class="text-lg font-semibold">{{ $invoice->dueAt->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Parties -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-xs uppercase font-semibold text-gray-500 mb-3">{{ $translator->__('bill_from') }}</h3>
                    <p class="font-semibold text-sm">{{ $invoice->seller->name }}</p>
                    <p class="text-xs text-gray-600 mt-1">
                        @if($invoice->seller->address)
                            {{ $invoice->seller->address }}<br>
                        @endif
                        @if($invoice->seller->email)
                            {{ $invoice->seller->email }}<br>
                        @endif
                        @if($invoice->seller->vatNumber)
                            VAT: {{ $invoice->seller->vatNumber }}
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-xs uppercase font-semibold text-gray-500 mb-3">{{ $translator->__('bill_to') }}</h3>
                    <p class="font-semibold text-sm">{{ $invoice->buyer->name }}</p>
                    <p class="text-xs text-gray-600 mt-1">
                        @if($invoice->buyer->address)
                            {{ $invoice->buyer->address }}<br>
                        @endif
                        @if($invoice->buyer->email)
                            {{ $invoice->buyer->email }}<br>
                        @endif
                        @if($invoice->buyer->vatNumber)
                            VAT: {{ $invoice->buyer->vatNumber }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full mb-8 text-sm">
                <thead class="bg-gray-50 border-b-2 border-indigo-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 uppercase">{{ $translator->__('description') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('unit_price') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('qty') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 text-gray-900">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ number_format($item->unitPrice, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($item->getTotal(), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="flex justify-end mb-8">
                <div class="w-64">
                    <div class="flex justify-between py-2 px-4 bg-gray-50 border-t-2 border-indigo-600">
                        <span class="font-semibold text-gray-900">{{ $translator->__('total') }}</span>
                        <span class="font-bold text-lg text-indigo-600">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
                <div class="bg-gray-50 p-4 rounded border-l-4 border-indigo-600 text-xs text-gray-700">
                    <strong class="text-gray-900 block mb-2">{{ $translator->__('notes') }}</strong>
                    {{ $invoice->notes }}
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-12 py-6 border-t border-gray-200 text-center text-xs text-gray-600">
            <p>{{ $translator->__('thank_you') }}</p>
        </div>
    </div>
</body>
</html>
```

## Using Your Custom Template

Once you've created your custom template, use it when generating PDFs:

```php
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;

$pdfGenerator = app(PdfGeneratorContract::class);

// Using the custom template
$pdfContent = $pdfGenerator->generate($invoiceData, 'custom');

// Or save it
$pdfGenerator->save($invoiceData, 'invoices/custom-invoice.pdf', 'custom');
```

## Available Tailwind CSS Classes

The compiled CSS includes all standard Tailwind utilities:

- **Layout**: `flex`, `grid`, `block`, `inline-block`, etc.
- **Spacing**: `px-{n}`, `py-{n}`, `mb-{n}`, `mt-{n}`, etc.
- **Typography**: `text-{size}`, `font-{weight}`, `uppercase`, `text-{color}`, etc.
- **Colors**: `bg-{color}`, `text-{color}`, `border-{color}`
- Available colors: `gray`, `indigo`, `slate`, `red`, `blue`, `green`, `purple`, etc.
- Color variants: `50`, `100`, `200`, `300`, `400`, `500`, `600`, `700`, `800`, `900`
- **Borders**: `border`, `border-{side}`, `border-{width}`, `rounded`, `rounded-{size}`
- **Effects**: `shadow`, `shadow-{size}`, `opacity-{value}`

## Tips for Custom Templates

1. **Keep it simple**: PDF rendering has limitations, so avoid complex animations or advanced CSS features.

2. **Use Tailwind utilities**: Stick to Tailwind's utility classes for consistent styling.

3. **Test thoroughly**: Always generate test PDFs to verify your design works correctly.

4. **Consider pagination**: For multi-page invoices, use page breaks with CSS:
   ```blade
   <div class="page-break">
       <!-- Content for new page -->
   </div>
   ```

5. **Use semantic HTML**: Keep your markup clean and semantic for better PDF rendering.

6. **Responsive is not needed**: PDFs have fixed dimensions, so ignore responsive design.

7. **Inline styles with caution**: Prefer Tailwind classes, but inline styles work too:
   ```blade
   <p style="color: #333; font-weight: bold;">Custom Styled Text</p>
   ```

## Translation

Use the `$translator` object to access localized labels:

```blade
{{ $translator->__('invoice') }}      <!-- "Invoice" -->
{{ $translator->__('bill_from') }}    <!-- "Bill From" -->
{{ $translator->__('bill_to') }}      <!-- "Bill To" -->
{{ $translator->__('description') }}  <!-- "Description" -->
{{ $translator->__('unit_price') }}   <!-- "Unit Price" -->
{{ $translator->__('qty') }}          <!-- "Qty" -->
{{ $translator->__('amount') }}       <!-- "Amount" -->
{{ $translator->__('total') }}        <!-- "Total" -->
{{ $translator->__('notes') }}        <!-- "Notes" -->
{{ $translator->__('thank_you') }}    <!-- "Thank You" -->
```

See the package's language files for all available translations.

## Troubleshooting

### CSS Not Applied

- Ensure you're using Tailwind utility classes
- Check that `{!! $compiledCss !!}` is included in your `<style>` tag
- Verify the compiled CSS file exists

### Layout Issues

- PDFs render with fixed dimensions, adjust padding/margins accordingly
- Test with actual data to see how content flows
- Use `overflow: hidden` on containers to prevent content overflow

### Missing Fonts

- The PDF generator uses system fonts by default
- Consider the fonts available in the rendering environment
- Stick to web-safe fonts: Arial, Helvetica, Times New Roman, etc.---

---

**← Previous:** [05 - Templates](./05-templates.md) | **Next:** [07 - Customization →](./07-customization.md)

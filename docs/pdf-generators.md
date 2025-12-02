# PDF Generators

This package supports multiple PDF generation engines. Choose the one that best fits your needs.

## Available Engines

### 1. Spatie (Puppeteer) - Default

Uses Spatie's `laravel-pdf` package with Puppeteer (Node.js) for rendering HTML to PDF.

**Pros:**
- Better rendering for complex layouts
- JavaScript execution support
- More reliable for advanced CSS features
- Better support for modern CSS

**Cons:**
- Requires Node.js and Puppeteer installation
- Higher system resource usage
- Slower for simple documents
- External dependency on Chromium

**Use case:** When you need advanced rendering features or complex layouts.

#### Setup

Install Puppeteer:

```bash
npm install puppeteer
```

Set in `.env`:

```env
INVOICES_PDF_DRIVER=spatie
```

---

### 2. DomPDF

Pure PHP solution using `barryvdh/laravel-dompdf`. No Node.js required.

**Pros:**
- Pure PHP, no external dependencies
- Lightweight and fast for simple documents
- No need for Node.js or Chromium
- Smaller memory footprint
- Great for simple invoices with basic CSS

**Cons:**
- Limited JavaScript support
- Some advanced CSS features not supported
- Slower with complex layouts
- May have rendering differences in edge cases

**Use case:** Simple invoices, lightweight deployments, or when Node.js is not available.

#### Setup

DomPDF is already included as a dependency. Just configure it:

Set in `.env`:

```env
INVOICES_PDF_DRIVER=dompdf
```

---

## Comparison Table

| Feature | Spatie (Puppeteer) | DomPDF |
|---------|-------------------|--------|
| JavaScript Support | Yes | No |
| Complex CSS | Excellent | Good |
| Performance | Medium | Fast |
| Memory Usage | High | Low |
| Setup Complexity | Medium | Easy |
| System Requirements | Node.js + Chromium | PHP only |
| License | MIT | LGPL |
| Best For | Complex layouts | Simple invoices |

---

## Switching Drivers

Switching between drivers is simple and doesn't require code changes:

```bash
# Use DomPDF
INVOICES_PDF_DRIVER=dompdf

# Use Spatie
INVOICES_PDF_DRIVER=spatie
```

Your invoice generation code remains the same:

```php
$pdf = $invoice->generatePdf();
$pdf->save('invoices/invoice-001.pdf');
```

The underlying engine will be swapped automatically based on the configuration.

---

## Configuration

All PDF generation configuration is managed in `config/pdf-invoices.php`:

```php
'pdf' => [
    'driver' => env('INVOICES_PDF_DRIVER', 'spatie'),
    'template' => env('INVOICES_TEMPLATE', 'modern'),
    'base_path' => env('INVOICES_PDF_PATH', 'invoices'),
],
```

---

## Troubleshooting

### Spatie (Puppeteer) Issues

**"Puppeteer not found"**
```bash
npm install puppeteer
```

**Memory exhausted**
- Reduce concurrent PDF generations
- Increase PHP memory limit in `php.ini`

**Timeouts**
- Increase Laravel's timeout configuration
- Check Chromium process requirements

### DomPDF Issues

**CSS not rendering correctly**
- Some CSS features are limited in DomPDF
- Test with inline styles instead
- Refer to [DomPDF documentation](https://github.com/barryvdh/laravel-dompdf)

**Font issues**
- Make sure fonts are available
- Use system fonts or specify font paths

---

## Performance Tips

### For Spatie (Puppeteer)

1. **Batch processing:** Generate multiple PDFs in parallel (with care)
2. **Caching:** Cache compiled CSS to avoid recompilation
3. **Pooling:** Use process pooling for high-volume generation

### For DomPDF

1. **Simple templates:** Keep templates clean and lightweight
2. **Inline styles:** Use inline CSS over external stylesheets when possible
3. **Optimize images:** Use optimized images to reduce file size

---

## Environment Variables

```env
# PDF Generation Driver
INVOICES_PDF_DRIVER=spatie

# Invoice Template (minimal, modern, branded)
INVOICES_TEMPLATE=modern

# Base path for saving PDFs
INVOICES_PDF_PATH=invoices

# Localization
INVOICES_LOCALE=en

# Currency Configuration
INVOICES_CURRENCY_CODE=EUR
INVOICES_CURRENCY_SYMBOL=€
```

---

## Custom PDF Generator

To create a custom PDF generator, implement the `PdfGeneratorContract`:

```php
namespace App\Invoices;

use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;

class CustomPdfGenerator implements PdfGeneratorContract
{
    public function __construct(private string $basePath = 'invoices') {}

    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        // Your custom PDF generation logic
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        // Your custom save logic
    }
}
```

Register it in your service provider:

```php
$this->app->singleton(PdfGeneratorContract::class, function ($app) {
    return new CustomPdfGenerator();
});
```

---

See the [main documentation](index.md) for more information.
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


## Configuration

All PDF generation configuration is managed in `config/pdf-invoices.php`:

```php
'pdf' => [
    'driver' => env('INVOICES_PDF_DRIVER', 'spatie'),
    'template' => env('INVOICES_TEMPLATE', 'modern'),
    'base_path' => env('INVOICES_PDF_PATH', 'invoices'),
],
```


## Performance Tips

### For Spatie (Puppeteer)

1. **Batch processing:** Generate multiple PDFs in parallel (with care)
2. **Caching:** Cache compiled CSS to avoid recompilation
3. **Pooling:** Use process pooling for high-volume generation

### For DomPDF

1. **Simple templates:** Keep templates clean and lightweight
2. **Inline styles:** Use inline CSS over external stylesheets when possible
3. **Optimize images:** Use optimized images to reduce file size


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
```---

**← Previous:** [01 - ](./01-usage.md) | **Next:** [03 -  →](./03-builders.md)

---

**← Previous:** [01 - Usage](./01-usage.md) | **Next:** [03 - Builders →](./03-builders.md)

# PDF Generation

PDF generation is handled through the `PdfGeneratorContract` interface, which abstracts the underlying PDF engine. The package includes two implementations: Spatie PDF and DomPDF.

## PdfGeneratorContract

The main interface for PDF generation.

### Methods

**generate(InvoiceData $invoice, string $template = 'modern'): string**

Generates a PDF and returns the binary content as a string.

```php
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;

$generator = app(PdfGeneratorContract::class);
$pdfContent = $generator->generate($invoiceData, 'modern');

// Return as HTTP response
return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'inline; filename="invoice.pdf"',
]);
```

**save(InvoiceData $invoice, string $path, string $template = 'modern'): string**

Generates a PDF and saves it to the filesystem. Returns the full path where the file was saved.

```php
$generator = app(PdfGeneratorContract::class);
$savedPath = $generator->save($invoiceData, 'invoice-001.pdf', 'modern');

// Returns: 'invoices/invoice-001.pdf'
```

The `path` parameter is relative to the configured `base_path` in `config/pdf-invoices.php`.

## PDF Engines

### Spatie PDF (Default)

Uses Browsershot (headless Chrome/Chromium) via Node.js for rendering. Provides the best CSS support and most accurate rendering.

**Advantages:**
- Excellent CSS3 support
- Accurate rendering of complex layouts
- JavaScript rendering capability
- Best for custom templates

**Requirements:**
- **Node.js**: Required to run Browsershot/Puppeteer.
- **Chromium or Google Chrome**: A headless browser instance.
- **Puppeteer**: Must be installed manually as a Node dependency (`npm install puppeteer`).

**Configuration:**

```env
INVOICES_PDF_DRIVER=spatie
```

**Implementation:** `Akira\PdfInvoices\Pdf\SpatiePdfGenerator`

### DomPDF

Pure PHP PDF generation using DomPDF library. No external dependencies.

**Advantages:**
- No Node.js required
- Simpler deployment
- Pure PHP stack
- Good for basic layouts

**Limitations:**
- Limited CSS support (CSS 2.1)
- No JavaScript
- Less accurate complex layouts

**Configuration:**

```env
INVOICES_PDF_DRIVER=dompdf
```

**Implementation:** `Akira\PdfInvoices\Pdf\DompdfPdfGenerator`

## Templates

The package includes three built-in templates.

### Modern (Default)

Clean, professional design with clear visual hierarchy.

```php
$pdf = $generator->generate($invoiceData, 'modern');
```

**Features:**
- Balanced layout
- Clear section separation
- Professional typography
- Suitable for most businesses

### Minimal

Compact, space-efficient layout.

```php
$pdf = $generator->generate($invoiceData, 'minimal');
```

**Features:**
- Dense information display
- Minimal whitespace
- Smaller file size
- Good for simple invoices

### Branded

Rich layout with prominent branding elements.

```php
$pdf = $generator->generate($invoiceData, 'branded');
```

**Features:**
- Large logo placement
- Bold visual identity
- Colorful accents
- Best with brand assets

## Common Usage Patterns

### Inline Display

Display PDF directly in the browser:

```php
$pdfContent = app(PdfGeneratorContract::class)->generate($invoiceData);

return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'inline; filename="invoice.pdf"',
]);
```

### Download

Trigger PDF download:

```php
$pdfContent = app(PdfGeneratorContract::class)->generate($invoiceData);

return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'attachment; filename="invoice-'.$invoiceData->invoiceNumber.'.pdf"',
]);
```

### Save to Storage

Save PDF to Laravel storage:

```php
use Akira\PdfInvoices\Contracts\StorageDriverContract;

$generator = app(PdfGeneratorContract::class);
$storage = app(StorageDriverContract::class);

// Generate PDF
$pdfContent = $generator->generate($invoiceData, 'modern');

// Save to storage
$filename = "invoice-{$invoiceData->invoiceNumber}.pdf";
$path = $storage->save("invoices/2024/{$filename}", $pdfContent);

// Get public URL if using public disk
$url = Storage::disk('public')->url($path);
```

### Email as Attachment

Attach generated PDF to Laravel mail:

```php
use Illuminate\Support\Facades\Mail;

$pdfContent = app(PdfGeneratorContract::class)->generate($invoiceData);

Mail::send('emails.invoice', ['invoice' => $invoiceData], function ($message) use ($pdfContent, $invoiceData) {
    $message->to($invoiceData->buyer->email)
        ->subject('Invoice '.$invoiceData->invoiceNumber)
        ->attachData($pdfContent, 'invoice.pdf', [
            'mime' => 'application/pdf',
        ]);
});
```

### Queue Generation

Generate PDFs in background jobs:

```php
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public InvoiceData $invoiceData,
        public string $storagePath,
    ) {}

    public function handle(PdfGeneratorContract $generator): void
    {
        $pdfContent = $generator->generate($this->invoiceData);
        
        $storage = app(StorageDriverContract::class);
        $storage->save($this->storagePath, $pdfContent);
    }
}

// Dispatch
GenerateInvoicePdf::dispatch($invoiceData, 'invoices/invoice-001.pdf');
```

## Custom PDF Engine

To implement a custom PDF engine, create a class implementing `PdfGeneratorContract`:

```php
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;

class CustomPdfGenerator implements PdfGeneratorContract
{
    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        // Your PDF generation logic
        return $pdfBinaryContent;
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        $content = $this->generate($invoice, $template);
        // Save to filesystem
        file_put_contents($path, $content);
        return $path;
    }
}
```

Register in a service provider:

```php
$this->app->singleton(PdfGeneratorContract::class, CustomPdfGenerator::class);
```

**Previous:** [Data Transfer Objects](05-data-transfer-objects.md) | **Next:** [Storage](07-storage.md)

# Troubleshooting

Common issues and solutions when working with Laravel PDF Invoices.

## PDF Generation Issues

### Spatie PDF: Puppeteer Not Found

**Problem:** PDF generation fails with "Puppeteer not found" error.

**Solution:**

Install Puppeteer via npm:

```bash
npm install puppeteer
```

Verify installation:

```bash
npx puppeteer --version
```

If still failing, ensure Node.js and npm are in your system PATH.

### DomPDF: Missing Required Libraries

**Problem:** DomPDF fails with font or library errors.

**Solution:**

Ensure PHP GD extension is installed:

```bash
# Ubuntu/Debian
sudo apt-get install php-gd

# macOS
brew install php@8.4-gd

# Verify
php -m | grep gd
```

Clear Laravel cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### PDF Content Is Empty

**Problem:** Generated PDF is empty or has no content.

**Solution:**

Check that InvoiceBuilder has required fields:

```php
$invoice = InvoiceBuilder::make()
    ->seller($seller) // Required
    ->buyer($buyer)   // Required
    ->addItem($item)  // At least one item recommended
    ->build();
```

Verify data is passed to template:

```php
// In your custom template
@if($invoice)
    {{ $invoice->invoiceNumber }}
@else
    <p>No invoice data</p>
@endif
```

### CSS Not Applied

**Problem:** Template styles are not rendering correctly.

**Solution:**

Verify compiled CSS exists:

```bash
ls vendor/akira/laravel-pdf-invoices/resources/css/compiled.css
```

In custom templates, ensure CSS is included:

```blade
<style>{!! $compiledCss !!}</style>
```

For DomPDF, use inline styles for better compatibility:

```blade
<div style="font-size: 14px; color: #333;">Content</div>
```

## Storage Issues

### File Not Saved

**Problem:** `save()` method completes but file doesn't exist.

**Solution:**

Verify disk configuration in `config/filesystems.php`:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
],
```

Check directory permissions:

```bash
chmod -R 755 storage/app
```

Verify the disk in config:

```env
INVOICES_STORAGE_DISK=local
```

### S3 Upload Fails

**Problem:** Cannot save to S3 bucket.

**Solution:**

Verify AWS credentials in `.env`:

```env
AWS_ACCESS_KEY_ID=your-key-id
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

Install AWS SDK:

```bash
composer require league/flysystem-aws-s3-v3
```

Test bucket access:

```php
Storage::disk('s3')->put('test.txt', 'test content');
```

Check S3 bucket permissions and CORS configuration.

## Template Issues

### Template Not Found

**Problem:** Error "View [pdf-invoices::pdf.templates.custom] not found".

**Solution:**

Publish package views:

```bash
php artisan vendor:publish --tag="pdf-invoices-views"
```

Create custom template in correct location:

```
resources/views/vendor/pdf-invoices/pdf/templates/custom.blade.php
```

Clear view cache:

```bash
php artisan view:clear
```

### Translation Missing

**Problem:** Template shows translation keys instead of translated text.

**Solution:**

Publish translations:

```bash
php artisan vendor:publish --tag="pdf-invoices-translations"
```

Verify translation file exists:

```
resources/lang/vendor/pdf-invoices/{locale}/invoice.php
```

Check locale is supported:

```php
// config/pdf-invoices.php
'localization' => [
    'supported_locales' => ['en', 'pt', 'fr'],
],
```

Clear translation cache:

```bash
php artisan cache:clear
```

### Blade Syntax Errors

**Problem:** Template rendering fails with syntax errors.

**Solution:**

Validate Blade syntax:

```bash
php artisan view:cache
```

Common issues:
- Unclosed tags: `@if` without `@endif`
- Undefined variables: Use `@isset` or `??` operator
- Invalid expressions: Ensure PHP syntax is valid

Debug template:

```blade
@php
    dump($invoice);
    dd($invoice->items);
@endphp
```

## Builder Issues

### Missing Required Fields

**Problem:** `InvalidArgumentException: Seller is required.`

**Solution:**

Ensure seller and buyer are set before calling `build()`:

```php
$invoice = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Seller')->build())
    ->buyer(EntityBuilder::make()->name('Buyer')->build())
    ->build();
```

### Type Errors

**Problem:** Type errors when setting values.

**Solution:**

Ensure correct types are passed:

```php
// Correct
->unitPrice(100.00)    // float
->quantity(5)          // int
->tax(0.19)            // float (decimal rate, not percentage)
->discount(0.10)       // float (decimal rate, not percentage)

// Incorrect
->unitPrice('100')     // string
->tax(19)              // percentage instead of decimal
```

## Configuration Issues

### Config Not Loading

**Problem:** Configuration changes not taking effect.

**Solution:**

Clear config cache:

```bash
php artisan config:clear
```

Republish config:

```bash
php artisan vendor:publish --tag="pdf-invoices-config" --force
```

Verify config file exists:

```bash
ls config/pdf-invoices.php
```

### Environment Variables Ignored

**Problem:** `.env` settings not applied.

**Solution:**

Clear all caches:

```bash
php artisan optimize:clear
```

Restart web server or queue workers.

Verify `.env` file is in project root and not `.env.example`.

## Memory Issues

### Out of Memory Error

**Problem:** PHP fatal error: Out of memory when generating large invoices.

**Solution:**

Increase PHP memory limit in `php.ini`:

```ini
memory_limit = 256M
```

Or temporarily in code:

```php
ini_set('memory_limit', '256M');

$pdfContent = $generator->generate($invoiceData);
```

For very large invoices, consider:
- Reducing image sizes in logos
- Simplifying templates
- Using DomPDF instead of Spatie (lower memory usage)

## Performance Issues

### Slow PDF Generation

**Problem:** PDF generation takes too long.

**Solution:**

Use DomPDF for faster generation:

```env
INVOICES_PDF_DRIVER=dompdf
```

Queue generation for large batches:

```php
GenerateInvoicePdf::dispatch($invoiceData);
```

Cache compiled CSS:

```php
// Service Provider
public function boot()
{
    $css = Cache::remember('invoice-css', 3600, function () {
        return file_get_contents(__DIR__.'/../../resources/css/compiled.css');
    });
}
```

### Queue Timeout

**Problem:** Queued jobs timeout before completion.

**Solution:**

Increase timeout in job:

```php
class GenerateInvoicePdf implements ShouldQueue
{
    public $timeout = 300; // 5 minutes
}
```

Or in queue configuration:

```php
// config/queue.php
'connections' => [
    'redis' => [
        'retry_after' => 300,
    ],
],
```

## Debugging

### Enable Debug Mode

Add logging to troubleshoot issues:

```php
use Illuminate\Support\Facades\Log;

Log::info('Generating invoice', [
    'invoice_number' => $invoiceData->invoiceNumber,
    'seller' => $invoiceData->seller->name,
    'items_count' => count($invoiceData->items),
]);

$pdfContent = $generator->generate($invoiceData);

Log::info('PDF generated', ['size' => strlen($pdfContent)]);
```

### Test in Isolation

Test components individually:

```php
// Test builder
$invoice = InvoiceBuilder::make()
    ->seller(EntityBuilder::make()->name('Test')->build())
    ->buyer(EntityBuilder::make()->name('Test')->build())
    ->addItem(ItemBuilder::make()->description('Test')->unitPrice(1)->build())
    ->build();

dump($invoice);

// Test generator
$generator = app(PdfGeneratorContract::class);
dump($generator);

// Test storage
$storage = app(StorageDriverContract::class);
dump($storage);
```

### Verify Service Container

Check service bindings:

```php
dd(app(PdfGeneratorContract::class)); // Should resolve to SpatiePdfGenerator or DompdfPdfGenerator
dd(app(StorageDriverContract::class)); // Should resolve to LaravelStorageDriver
dd(app(CurrencyFormatterContract::class)); // Should resolve to configured formatter
```

## Getting Help

If issues persist:

1. Check package version compatibility with Laravel version
2. Review the CHANGELOG for known issues
3. Search existing GitHub issues
4. Create a new issue with:
   - Laravel version
   - PHP version
   - Package version
   - Full error message and stack trace
   - Minimal reproducible example

**Previous:** [Advanced Usage](12-advanced-usage.md)

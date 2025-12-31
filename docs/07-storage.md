# Storage

Storage is handled through the `StorageDriverContract` interface, providing a consistent API for saving and retrieving generated PDFs.

## StorageDriverContract

The storage abstraction interface.

### Methods

**save(string $path, string $content): string**

Saves PDF content to storage and returns the path.

```php
use Akira\PdfInvoices\Contracts\StorageDriverContract;

$storage = app(StorageDriverContract::class);
$path = $storage->save('invoices/2024/invoice-001.pdf', $pdfContent);

// Returns: 'invoices/2024/invoice-001.pdf'
```

**exists(string $path): bool**

Checks if a file exists at the given path.

```php
if ($storage->exists('invoices/2024/invoice-001.pdf')) {
    // File exists
}
```

**get(string $path): string**

Retrieves file content from storage. Throws `RuntimeException` if file not found.

```php
try {
    $pdfContent = $storage->get('invoices/2024/invoice-001.pdf');
} catch (RuntimeException $e) {
    // File not found
}
```

**delete(string $path): bool**

Deletes a file from storage. Returns `true` on success.

```php
$deleted = $storage->delete('invoices/2024/invoice-001.pdf');
```

## Laravel Storage Driver

The default implementation uses Laravel's filesystem abstraction.

### Configuration

Configure which disk to use in `config/pdf-invoices.php`:

```php
'storage' => [
    'driver' => 'laravel',
    'disk' => env('INVOICES_STORAGE_DISK', 'local'),
],
```

Or via environment variable:

```env
INVOICES_STORAGE_DISK=local
```

### Available Disks

Any disk configured in `config/filesystems.php` can be used:

**local** - Local filesystem (default)

```env
INVOICES_STORAGE_DISK=local
```

Files stored in `storage/app/`.

**public** - Public filesystem

```env
INVOICES_STORAGE_DISK=public
```

Files stored in `storage/app/public/` and accessible via URL after `php artisan storage:link`.

**s3** - Amazon S3

```env
INVOICES_STORAGE_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

**Custom disks** - Any disk you define in `filesystems.php`

## Common Patterns

### Save and Generate URL

Generate PDF and get public URL:

```php
use Illuminate\Support\Facades\Storage;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\Contracts\StorageDriverContract;

$generator = app(PdfGeneratorContract::class);
$storage = app(StorageDriverContract::class);

// Generate PDF
$pdfContent = $generator->generate($invoiceData);

// Save to public disk
$path = $storage->save('invoices/invoice-001.pdf', $pdfContent);

// Get public URL
$url = Storage::disk('public')->url($path);

// Store URL in database or send via email
```

### Overwrite Protection

Check if file exists before saving:

```php
$path = 'invoices/invoice-001.pdf';

if ($storage->exists($path)) {
    // Handle existing file
    $path = 'invoices/invoice-001-'.time().'.pdf';
}

$storage->save($path, $pdfContent);
```

### Organize by Date

Structure invoices by year and month:

```php
$year = now()->year;
$month = now()->month;
$filename = "invoice-{$invoiceData->invoiceNumber}.pdf";
$path = "invoices/{$year}/{$month}/{$filename}";

$storage->save($path, $pdfContent);
// Saved to: invoices/2024/12/invoice-001.pdf
```

### Temporary Storage

Save temporarily, use, then delete:

```php
$tempPath = 'temp/invoice-'.uniqid().'.pdf';

try {
    $storage->save($tempPath, $pdfContent);
    
    // Process the file
    $this->processPdf($storage->get($tempPath));
    
} finally {
    $storage->delete($tempPath);
}
```

### Retrieve and Stream

Retrieve stored PDF and stream to browser:

```php
Route::get('/invoices/{number}', function (string $number) {
    $storage = app(StorageDriverContract::class);
    $path = "invoices/invoice-{$number}.pdf";
    
    if (!$storage->exists($path)) {
        abort(404);
    }
    
    $content = $storage->get($path);
    
    return response($content, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"invoice-{$number}.pdf\"",
    ]);
});
```

### Batch Operations

Process multiple invoices:

```php
$invoices = [$invoice1, $invoice2, $invoice3];
$generator = app(PdfGeneratorContract::class);
$storage = app(StorageDriverContract::class);

foreach ($invoices as $invoice) {
    $pdfContent = $generator->generate($invoice);
    $filename = "invoice-{$invoice->invoiceNumber}.pdf";
    $path = $storage->save("invoices/batch-2024/{$filename}", $pdfContent);
    
    Log::info("Saved invoice to {$path}");
}
```

## Cloud Storage

### Amazon S3

Configure S3 in `.env`:

```env
INVOICES_STORAGE_DISK=s3
AWS_ACCESS_KEY_ID=AKIA...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=my-invoices
```

Usage is identical to local storage:

```php
$storage->save('invoices/invoice-001.pdf', $pdfContent);
// Saved to S3 bucket
```

### DigitalOcean Spaces

Configure Spaces (S3-compatible):

```php
// config/filesystems.php
'spaces' => [
    'driver' => 's3',
    'key' => env('DO_SPACES_KEY'),
    'secret' => env('DO_SPACES_SECRET'),
    'endpoint' => env('DO_SPACES_ENDPOINT'),
    'region' => env('DO_SPACES_REGION'),
    'bucket' => env('DO_SPACES_BUCKET'),
],
```

```env
INVOICES_STORAGE_DISK=spaces
DO_SPACES_KEY=...
DO_SPACES_SECRET=...
DO_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DO_SPACES_REGION=nyc3
DO_SPACES_BUCKET=my-invoices
```

## Custom Storage Driver

Implement `StorageDriverContract` for custom storage backends:

```php
use Akira\PdfInvoices\Contracts\StorageDriverContract;

class FtpStorageDriver implements StorageDriverContract
{
    public function __construct(
        private FtpClient $client
    ) {}

    public function save(string $path, string $content): string
    {
        $this->client->upload($path, $content);
        return $path;
    }

    public function exists(string $path): bool
    {
        return $this->client->fileExists($path);
    }

    public function get(string $path): string
    {
        $content = $this->client->download($path);
        
        throw_if($content === null, RuntimeException::class, "File not found: {$path}");
        
        return $content;
    }

    public function delete(string $path): bool
    {
        return $this->client->delete($path);
    }
}
```

Register in service provider:

```php
$this->app->singleton(StorageDriverContract::class, function () {
    return new FtpStorageDriver(
        new FtpClient(config('storage.ftp'))
    );
});
```

**Previous:** [PDF Generation](06-pdf-generation.md) | **Next:** [Templates](08-templates.md)

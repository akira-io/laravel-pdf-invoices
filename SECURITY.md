# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | Supported          |
|---------|--------------------|
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

If you discover a security vulnerability within Laravel PDF Invoices, please send an email to:

**kidiatoliny@akira-io.com**

Include the following information in your report:

- Type of vulnerability
- Affected component or feature
- Steps to reproduce the issue
- Potential impact
- Suggested fix (if available)

### What to Expect

- **Acknowledgment:** We will acknowledge receipt of your vulnerability report within 48 hours.
- **Assessment:** We will investigate and assess the severity and impact of the vulnerability.
- **Communication:** We will keep you informed about the progress of addressing the issue.
- **Resolution:** We will work to release a fix as quickly as possible, depending on severity.
- **Credit:** We will credit you in the security advisory (unless you prefer to remain anonymous).

## Security Best Practices

When using Laravel PDF Invoices, follow these security best practices:

### 1. Validate User Input

Always validate and sanitize data before passing it to invoice builders:

```php
$validated = $request->validate([
    'buyer_name' => 'required|string|max:255',
    'buyer_email' => 'required|email',
    'items' => 'required|array',
    'items.*.description' => 'required|string|max:500',
    'items.*.unit_price' => 'required|numeric|min:0',
    'items.*.quantity' => 'required|integer|min:1',
]);
```

### 2. Sanitize Template Content

When displaying user-provided content in templates, use Blade's escaping:

```blade
{{-- Escaped automatically --}}
{{ $invoice->buyer->name }}

{{-- Raw output (use with caution) --}}
{!! $trustedHtmlContent !!}
```

### 3. Restrict File Access

When storing generated PDFs, ensure proper access controls:

```php
// Use private disk for sensitive invoices
$storage = Storage::disk('private');
$storage->put('invoices/sensitive.pdf', $pdfContent);

// Add authorization middleware
Route::middleware(['auth', 'can:view-invoice'])
    ->get('/invoices/{id}/pdf', [InvoiceController::class, 'download']);
```

### 4. Protect Storage Paths

Validate file paths to prevent directory traversal attacks:

```php
$filename = basename($request->input('filename')); // Remove directory components
$path = 'invoices/' . $filename;

if (!str_starts_with($path, 'invoices/')) {
    abort(403);
}

$storage->save($path, $pdfContent);
```

### 5. Rate Limiting

Implement rate limiting for PDF generation endpoints:

```php
Route::middleware(['throttle:10,1']) // 10 requests per minute
    ->post('/invoices/generate', [InvoiceController::class, 'generate']);
```

### 6. Validate File Uploads

If accepting logo uploads, validate file types and sizes:

```php
$request->validate([
    'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
]);
```

### 7. Environment Variables

Never commit sensitive configuration to version control:

```env
# .env (not committed)
INVOICES_PDF_DRIVER=spatie
AWS_ACCESS_KEY_ID=your-secret-key
AWS_SECRET_ACCESS_KEY=your-secret
```

### 8. Queue Security

When using queues for PDF generation, ensure jobs are authorized:

```php
class GenerateInvoicePdf implements ShouldQueue
{
    public function __construct(
        public int $invoiceId,
        public int $userId,
    ) {}

    public function handle(): void
    {
        $invoice = Invoice::findOrFail($this->invoiceId);
        
        // Verify user has access
        if ($invoice->user_id !== $this->userId) {
            throw new UnauthorizedException('User cannot access this invoice');
        }
        
        // Generate PDF...
    }
}
```

### 9. SQL Injection Prevention

Use Eloquent ORM or parameterized queries:

```php
// Secure - using Eloquent
$invoices = Invoice::where('user_id', auth()->id())
    ->where('status', 'paid')
    ->get();

// Insecure - avoid raw queries with user input
// DB::select("SELECT * FROM invoices WHERE user_id = " . $userId);
```

### 10. Cross-Site Scripting (XSS)

Blade templates automatically escape output. For custom attributes:

```blade
{{-- Secure - automatically escaped --}}
<p>{{ $invoice->get('custom_field') }}</p>

{{-- Insecure - manual escaping needed --}}
<p><?= htmlspecialchars($invoice->get('custom_field'), ENT_QUOTES, 'UTF-8') ?></p>
```

## Known Security Considerations

### PDF Generation Engines

Both Spatie PDF (Puppeteer) and DomPDF have security considerations:

**Puppeteer (Spatie PDF):**

- Executes JavaScript in headless Chrome
- Can access network resources
- Should not process untrusted HTML/JavaScript
- Keep Puppeteer and Chrome up to date

**DomPDF:**

- Can include external resources (images, CSS)
- Vulnerable to XML External Entity (XXE) attacks if processing untrusted XML
- Keep DomPDF up to date

### Mitigation

- Sanitize all user input before rendering
- Disable remote resource loading if not needed
- Use Content Security Policy headers
- Run PDF generation in isolated environment

## Disclosure Policy

When a security vulnerability is confirmed:

1. We will create a security advisory on GitHub
2. We will release a patched version as soon as possible
3. We will publish a security bulletin describing:
- The vulnerability
- Affected versions
- Severity rating
- Mitigation steps
- Fixed version

## Security Updates

Subscribe to security updates:

- Watch the GitHub repository
- Enable GitHub security alerts
- Follow release notes in CHANGELOG.md

## Contact

For security concerns:

- Email: **kidiatoliny@akira-io.com**
- PGP Key: Available upon request

For general questions:

- GitHub Issues: https://github.com/kidiatoliny/laravel-pdf-invoices/issues
- GitHub Discussions: https://github.com/kidiatoliny/laravel-pdf-invoices/discussions

Thank you for helping keep Laravel PDF Invoices secure!

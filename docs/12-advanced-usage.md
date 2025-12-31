# Advanced Usage

This guide covers advanced patterns and techniques for working with Laravel PDF Invoices.

## Calculation Methods

All calculation methods are available on `InvoiceData` and `ItemData` DTOs.

### Invoice-Level Calculations

```php
$subtotal = $invoiceData->getSubtotal();
// Sum of all item subtotals (qty × unit price)

$totalDiscount = $invoiceData->getTotalDiscount();
// Sum of all item discount amounts

$subtotalAfterDiscount = $invoiceData->getSubtotalAfterDiscount();
// Subtotal minus total discounts

$totalTax = $invoiceData->getTotalTax();
// Sum of all item tax amounts

$total = $invoiceData->getTotal();
// Final total including discounts and taxes
```

### Item-Level Calculations

```php
$lineSubtotal = $item->getSubtotal();
// Quantity × unit price

$discountAmount = $item->getDiscountAmount();
// Line subtotal × discount rate

$lineAfterDiscount = $item->getSubtotalAfterDiscount();
// Line subtotal - discount amount

$taxAmount = $item->getTaxAmount();
// Line after discount × tax rate

$lineTotal = $item->getTotal();
// Line after discount + tax amount
```

### Complex Calculations

Calculate effective tax rate:

```php
$effectiveTaxRate = $invoiceData->getTotalTax() / $invoiceData->getSubtotalAfterDiscount();
$percentage = round($effectiveTaxRate * 100, 2);
```

Calculate average item price:

```php
$itemCount = count($invoiceData->items);
$averagePrice = $itemCount > 0 
    ? $invoiceData->getSubtotal() / $itemCount 
    : 0;
```

## Queue Integration

Process invoices asynchronously using Laravel queues.

### Background Generation

```php
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class GenerateAndEmailInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public InvoiceData $invoiceData,
        public string $recipientEmail,
    ) {}

    public function handle(): void
    {
        $generator = app(PdfGeneratorContract::class);
        $pdfContent = $generator->generate($this->invoiceData);
        
        Mail::raw('Please find your invoice attached.', function ($message) use ($pdfContent) {
            $message->to($this->recipientEmail)
                ->subject('Invoice ' . $this->invoiceData->invoiceNumber)
                ->attachData($pdfContent, 'invoice.pdf', ['mime' => 'application/pdf']);
        });
    }
}

// Dispatch
GenerateAndEmailInvoice::dispatch($invoiceData, 'client@example.com');
```

### Batch Processing

```php
$invoices = Invoice::where('status', 'pending')
    ->where('due_date', '<=', now())
    ->get();

foreach ($invoices as $invoice) {
    $invoiceData = $this->buildInvoiceData($invoice);
    GenerateAndEmailInvoice::dispatch($invoiceData, $invoice->email);
}
```

## Eloquent Integration

Integrate with Eloquent models for persistence.

### Invoice Model

```php
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'seller_id',
        'buyer_id',
        'issued_at',
        'due_at',
        'currency',
        'locale',
        'notes',
        'subtotal',
        'tax',
        'total',
        'custom_attributes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'custom_attributes' => 'array',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Entity::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Entity::class, 'buyer_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function toInvoiceData(): InvoiceData
    {
        return InvoiceBuilder::make()
            ->seller($this->seller->toEntityData())
            ->buyer($this->buyer->toEntityData())
            ->items($this->items->map->toItemData()->toArray())
            ->invoiceNumber($this->invoice_number)
            ->issuedAt($this->issued_at)
            ->dueAt($this->due_at)
            ->currency($this->currency)
            ->locale($this->locale)
            ->notes($this->notes)
            ->withAttributes($this->custom_attributes ?? [])
            ->build();
    }
}
```

### Generating PDFs from Models

```php
Route::get('/invoices/{invoice}/pdf', function (Invoice $invoice) {
    $invoiceData = $invoice->toInvoiceData();
    
    $generator = app(PdfGeneratorContract::class);
    $pdfContent = $generator->generate($invoiceData);
    
    return response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"invoice-{$invoice->invoice_number}.pdf\"",
    ]);
});
```

## API Resources

Transform invoice data for API responses.

### Invoice Resource

```php
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        $invoiceData = $this->resource; // InvoiceData instance
        
        return [
            'invoice_number' => $invoiceData->invoiceNumber,
            'issued_at' => $invoiceData->issuedAt?->toIso8601String(),
            'due_at' => $invoiceData->dueAt?->toIso8601String(),
            'currency' => $invoiceData->currency,
            'locale' => $invoiceData->locale,
            'seller' => [
                'name' => $invoiceData->seller->name,
                'address' => $invoiceData->seller->address,
                'vat_number' => $invoiceData->seller->vatNumber,
                'email' => $invoiceData->seller->email,
            ],
            'buyer' => [
                'name' => $invoiceData->buyer->name,
                'address' => $invoiceData->buyer->address,
                'vat_number' => $invoiceData->buyer->vatNumber,
                'email' => $invoiceData->buyer->email,
            ],
            'items' => array_map(fn($item) => [
                'description' => $item->description,
                'unit_price' => $item->unitPrice,
                'quantity' => $item->quantity,
                'tax' => $item->tax,
                'discount' => $item->discount,
                'subtotal' => $item->getSubtotal(),
                'total' => $item->getTotal(),
            ], $invoiceData->items),
            'calculations' => [
                'subtotal' => $invoiceData->getSubtotal(),
                'total_discount' => $invoiceData->getTotalDiscount(),
                'total_tax' => $invoiceData->getTotalTax(),
                'total' => $invoiceData->getTotal(),
            ],
            'notes' => $invoiceData->notes,
            'custom_attributes' => $invoiceData->attributes(),
        ];
    }
}
```

### API Endpoint

```php
Route::get('/api/invoices/{id}', function (string $id) {
    $invoice = Invoice::findOrFail($id);
    $invoiceData = $invoice->toInvoiceData();
    
    return new InvoiceResource($invoiceData);
});
```

## Testing

Write tests for invoice generation.

### Feature Test

```php
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;

test('generates invoice pdf', function () {
    $invoiceData = InvoiceBuilder::make()
        ->seller(EntityBuilder::make()->name('Test Seller')->build())
        ->buyer(EntityBuilder::make()->name('Test Buyer')->build())
        ->addItem(ItemBuilder::make()->description('Item')->unitPrice(100)->build())
        ->invoiceNumber('TEST-001')
        ->build();
    
    $generator = app(PdfGeneratorContract::class);
    $pdfContent = $generator->generate($invoiceData);
    
    expect($pdfContent)
        ->toBeString()
        ->not->toBeEmpty()
        ->toContain('%PDF'); // PDF signature
});

test('saves invoice to storage', function () {
    $storage = app(StorageDriverContract::class);
    $pdfContent = 'test-pdf-content';
    
    $path = $storage->save('test/invoice.pdf', $pdfContent);
    
    expect($storage->exists($path))->toBeTrue();
    expect($storage->get($path))->toBe($pdfContent);
    
    $storage->delete($path);
});
```

### Unit Test

```php
test('calculates invoice totals correctly', function () {
    $item1 = ItemBuilder::make()
        ->unitPrice(100)
        ->quantity(2)
        ->tax(0.19)
        ->discount(0.10)
        ->build();
    
    $item2 = ItemBuilder::make()
        ->unitPrice(50)
        ->quantity(5)
        ->tax(0.19)
        ->build();
    
    $invoice = InvoiceBuilder::make()
        ->seller(EntityBuilder::make()->name('Seller')->build())
        ->buyer(EntityBuilder::make()->name('Buyer')->build())
        ->addItem($item1)
        ->addItem($item2)
        ->build();
    
    expect($invoice->getSubtotal())->toBe(450.0);
    expect($invoice->getTotalDiscount())->toBe(20.0);
    expect($invoice->getSubtotalAfterDiscount())->toBe(430.0);
    expect($invoice->getTotalTax())->toBe(81.70);
    expect($invoice->getTotal())->toBe(511.70);
});
```

## Event Listeners

Create custom event listeners for invoice operations.

### Define Events

```php
class InvoiceGenerated
{
    public function __construct(
        public InvoiceData $invoiceData,
        public string $pdfContent,
    ) {}
}

class InvoiceStored
{
    public function __construct(
        public InvoiceData $invoiceData,
        public string $storagePath,
    ) {}
}
```

### Dispatch Events

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->build();

$pdfContent = app(PdfGeneratorContract::class)->generate($invoiceData);

event(new InvoiceGenerated($invoiceData, $pdfContent));

$path = app(StorageDriverContract::class)->save('invoices/invoice.pdf', $pdfContent);

event(new InvoiceStored($invoiceData, $path));
```

### Register Listeners

```php
// EventServiceProvider
protected $listen = [
    InvoiceGenerated::class => [
        LogInvoiceGeneration::class,
        NotifyAccountingTeam::class,
    ],
    InvoiceStored::class => [
        UpdateInvoiceRecord::class,
        SendInvoiceEmail::class,
    ],
];
```

## Middleware Integration

Create middleware for invoice operations.

### Authorization Middleware

```php
class AuthorizeInvoiceAccess
{
    public function handle($request, Closure $next)
    {
        $invoiceId = $request->route('invoice');
        $invoice = Invoice::findOrFail($invoiceId);
        
        if ($request->user()->cannot('view', $invoice)) {
            abort(403);
        }
        
        return $next($request);
    }
}

// Route
Route::middleware(['auth', AuthorizeInvoiceAccess::class])
    ->get('/invoices/{invoice}/pdf', function (Invoice $invoice) {
        // Generate and return PDF
    });
```

## Multi-Tenancy

Support multi-tenant invoice generation.

### Tenant-Aware Generation

```php
class TenantInvoiceService
{
    public function generateInvoice(Tenant $tenant, array $invoiceData): InvoiceData
    {
        $seller = EntityBuilder::make()
            ->name($tenant->company_name)
            ->address($tenant->address)
            ->vat($tenant->vat_number)
            ->email($tenant->email)
            ->logo($tenant->logo_url)
            ->build();
        
        return InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($this->buildBuyerFromData($invoiceData['buyer']))
            ->items($this->buildItemsFromData($invoiceData['items']))
            ->currency($tenant->default_currency)
            ->locale($tenant->default_locale)
            ->build();
    }
}
```

**Previous:** [Custom Attributes](11-custom-attributes.md) | **Next:** [Troubleshooting](13-troubleshooting.md)

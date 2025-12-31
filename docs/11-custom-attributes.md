# Custom Attributes

Custom attributes allow you to extend invoice entities with arbitrary key-value data beyond the standard properties. All DTOs support custom attributes through a consistent API.

## Why Custom Attributes

Standard invoice properties cover common use cases, but businesses often need additional fields:

- Purchase order numbers
- Project codes
- Department identifiers
- Customer references
- Tracking numbers
- Internal metadata
- Integration identifiers

Custom attributes provide this flexibility without modifying the core package.

## Configuration

Enable or disable custom attributes in `config/pdf-invoices.php`:

```php
'allow_custom_attributes' => env('INVOICES_ALLOW_CUSTOM_ATTRIBUTES', true),
```

When enabled (default), all builders accept custom attributes via `set()` and `withAttributes()` methods.

## Adding Custom Attributes

### Single Attribute

Use `set()` to add individual attributes:

```php
$entity = EntityBuilder::make()
    ->name('Acme Corp')
    ->set('phone', '+1-555-0123')
    ->set('website', 'https://acme.com')
    ->build();
```

### Multiple Attributes

Use `withAttributes()` for bulk assignment:

```php
$entity = EntityBuilder::make()
    ->name('Acme Corp')
    ->withAttributes([
        'phone' => '+1-555-0123',
        'website' => 'https://acme.com',
        'industry' => 'Technology',
        'account_manager' => 'Jane Smith',
    ])
    ->build();
```

## Using Custom Attributes

### InvoiceBuilder

Add metadata to invoices:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->invoiceNumber('INV-001')
    ->set('po_number', 'PO-12345')
    ->set('project_code', 'PROJ-2024-001')
    ->set('payment_method', 'Wire Transfer')
    ->set('sales_rep', 'John Doe')
    ->withAttributes([
        'department' => 'Engineering',
        'cost_center' => 'CC-1000',
        'internal_ref' => 'REF-456',
    ])
    ->build();
```

### EntityBuilder

Add entity-specific information:

```php
$seller = EntityBuilder::make()
    ->name('Acme Corp')
    ->address('123 Main St')
    ->set('phone', '+1-555-0123')
    ->set('fax', '+1-555-0124')
    ->set('website', 'https://acme.com')
    ->set('registration_number', 'REG123456')
    ->set('bank_account', 'IBAN: GB29NWBK60161331926819')
    ->build();

$buyer = EntityBuilder::make()
    ->name('Client Corp')
    ->set('contact_person', 'Jane Smith')
    ->set('contact_phone', '+1-555-9999')
    ->set('preferred_payment', 'Bank Transfer')
    ->set('credit_limit', 50000)
    ->build();
```

### ItemBuilder

Add line item metadata:

```php
$item = ItemBuilder::make()
    ->description('Professional Services')
    ->unitPrice(150)
    ->quantity(40)
    ->set('sku', 'SRV-001')
    ->set('category', 'Consulting')
    ->set('project', 'Website Redesign')
    ->set('billable_hours', 40)
    ->set('rate_type', 'hourly')
    ->build();
```

## Accessing Custom Attributes

All DTOs provide methods to access custom attributes.

### has(string $key): bool

Check if an attribute exists:

```php
if ($invoiceData->has('po_number')) {
    echo "PO Number: " . $invoiceData->get('po_number');
}

if ($item->has('sku')) {
    echo "SKU: " . $item->get('sku');
}
```

### get(string $key, mixed $default = null): mixed

Retrieve an attribute value:

```php
$poNumber = $invoiceData->get('po_number');
$projectCode = $invoiceData->get('project_code', 'N/A');

$sku = $item->get('sku');
$category = $item->get('category', 'General');

$phone = $entity->get('phone');
$website = $entity->get('website', 'https://example.com');
```

### attributes(): array

Get all custom attributes:

```php
$allAttributes = $invoiceData->attributes();
// Returns: ['po_number' => 'PO-12345', 'project_code' => 'PROJ-2024-001', ...]

foreach ($invoiceData->attributes() as $key => $value) {
    echo "{$key}: {$value}\n";
}
```

## Using in Templates

Access custom attributes in Blade templates:

```blade
{{-- Invoice attributes --}}
@if($invoice->has('po_number'))
    <div class="po-number">
        <strong>PO Number:</strong> {{ $invoice->get('po_number') }}
    </div>
@endif

@if($invoice->has('payment_method'))
    <p>Payment Method: {{ $invoice->get('payment_method') }}</p>
@endif

{{-- Entity attributes --}}
@if($invoice->seller->has('phone'))
    <p>Phone: {{ $invoice->seller->get('phone') }}</p>
@endif

@if($invoice->seller->has('website'))
    <p>Website: <a href="{{ $invoice->seller->get('website') }}">{{ $invoice->seller->get('website') }}</a></p>
@endif

{{-- Item attributes --}}
<table>
    @foreach($invoice->items as $item)
        <tr>
            <td>
                {{ $item->description }}
                @if($item->has('sku'))
                    <br><small>SKU: {{ $item->get('sku') }}</small>
                @endif
            </td>
            <td>{{ number_format($item->getTotal(), 2) }}</td>
        </tr>
    @endforeach
</table>

{{-- Iterate all custom attributes --}}
@if(count($invoice->attributes()) > 0)
    <div class="metadata">
        <h3>Additional Information</h3>
        @foreach($invoice->attributes() as $key => $value)
            <p><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
        @endforeach
    </div>
@endif
```

## Common Use Cases

### Purchase Orders

Track purchase order numbers:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->set('po_number', 'PO-2024-12345')
    ->set('po_date', '2024-01-15')
    ->build();
```

Display in template:

```blade
@if($invoice->has('po_number'))
    <div class="po-info">
        <strong>Purchase Order:</strong> {{ $invoice->get('po_number') }}
        @if($invoice->has('po_date'))
            ({{ $invoice->get('po_date') }})
        @endif
    </div>
@endif
```

### Project Tracking

Associate invoices with projects:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->set('project_id', 'PROJ-2024-001')
    ->set('project_name', 'Website Redesign')
    ->set('project_manager', 'John Doe')
    ->build();
```

### Payment Information

Specify payment details:

```php
$seller = EntityBuilder::make()
    ->name('Acme Corp')
    ->set('bank_name', 'Bank of America')
    ->set('account_number', '****1234')
    ->set('routing_number', '123456789')
    ->set('swift_code', 'BOFAUS3N')
    ->set('iban', 'GB29NWBK60161331926819')
    ->build();
```

### Billing Codes

Track billing and accounting codes:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->set('billing_code', 'BILL-2024-001')
    ->set('cost_center', 'CC-1000')
    ->set('department', 'Engineering')
    ->set('gl_account', '4000-100')
    ->build();
```

### Product Metadata

Add detailed product information:

```php
$item = ItemBuilder::make()
    ->description('Premium Widget')
    ->unitPrice(99.99)
    ->quantity(5)
    ->set('sku', 'WIDGET-PREM-001')
    ->set('upc', '012345678901')
    ->set('manufacturer', 'Acme Manufacturing')
    ->set('warranty_months', 24)
    ->set('category', 'Electronics')
    ->set('weight_kg', 1.5)
    ->build();
```

### Customer References

Store customer-specific identifiers:

```php
$buyer = EntityBuilder::make()
    ->name('Client Corp')
    ->set('customer_id', 'CUST-12345')
    ->set('account_number', 'ACC-67890')
    ->set('contact_person', 'Jane Smith')
    ->set('credit_terms', 'Net 30')
    ->set('credit_limit', 50000)
    ->build();
```

## Type Handling

Custom attributes accept any type:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->set('is_recurring', true)              // bool
    ->set('discount_percentage', 10.5)       // float
    ->set('items_count', 5)                  // int
    ->set('tags', ['urgent', 'priority'])    // array
    ->set('metadata', ['key' => 'value'])    // associative array
    ->set('due_date', now())                 // object
    ->build();

// Access
$isRecurring = $invoiceData->get('is_recurring'); // true
$tags = $invoiceData->get('tags');                // ['urgent', 'priority']
$dueDate = $invoiceData->get('due_date');         // Carbon instance
```

## Validation

Custom attributes are not validated by the package. Implement validation in your application:

```php
$validator = Validator::make($request->all(), [
    'po_number' => 'required|string|max:50',
    'project_code' => 'nullable|string|max:20',
    'payment_method' => 'required|in:bank_transfer,credit_card,paypal',
]);

if ($validator->fails()) {
    return back()->withErrors($validator);
}

$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->withAttributes($validator->validated())
    ->build();
```

## Database Storage

When persisting invoices, store custom attributes as JSON:

```php
// Migration
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number');
    $table->json('custom_attributes')->nullable();
    $table->timestamps();
});

// Model
class Invoice extends Model
{
    protected $casts = [
        'custom_attributes' => 'array',
    ];
}

// Store
Invoice::create([
    'invoice_number' => $invoiceData->invoiceNumber,
    'custom_attributes' => $invoiceData->attributes(),
]);

// Retrieve and rebuild
$invoice = Invoice::find(1);
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->withAttributes($invoice->custom_attributes)
    ->build();
```

**Previous:** [Currency Formatting](10-currency-formatting.md) | **Next:** [Advanced Usage](12-advanced-usage.md)

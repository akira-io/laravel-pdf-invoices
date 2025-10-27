# Custom Attributes

This package provides a flexible system for adding custom fields to invoices, entities, and items without modifying the core DTOs.

## Overview

All DTOs support custom attributes:

- `EntityData` (sellers and buyers)
- `ItemData` (line items)
- `InvoiceData` (invoices)

Custom attributes are stored as an array and accessed via methods:

- `get(string $key, mixed $default = null): mixed`
- `has(string $key): bool`
- `attributes(): array`

## Adding Custom Attributes

### Using `set()`

Add one attribute at a time:

```php
$entity = EntityBuilder::make()
    ->name('Company')
    ->set('country', 'Luxembourg')
    ->set('phone', '+352 1234 5678')
    ->set('registration_number', 'ABC123')
    ->build();
```

### Using `withAttributes()`

Add multiple attributes at once:

```php
$entity = EntityBuilder::make()
    ->name('Company')
    ->withAttributes([
        'country' => 'Luxembourg',
        'phone' => '+352 1234 5678',
        'registration_number' => 'ABC123',
        'industry' => 'Technology',
        'employees' => 50,
    ])
    ->build();
```

## Accessing Custom Attributes

### Get with Default

```php
$country = $entity->get('country');           // 'Luxembourg'
$city = $entity->get('city', 'Luxembourg');  // 'Luxembourg' (default)
$missing = $entity->get('missing');          // null
```

### Check Existence

```php
if ($entity->has('country')) {
    // Process country
}
```

### Get All Attributes

```php
$all = $entity->attributes(); // array of all custom attributes
```

## Entity Custom Attributes

### Seller Example

```php
$seller = EntityBuilder::make()
    ->name('Akira Corporation')
    ->address('123 Main St')
    ->email('contact@akira.io')
    ->set('country', 'Luxembourg')
    ->set('registration_number', 'L123456789')
    ->set('bank_account', 'LU60 0569 1234 5678 9012 3456')
    ->set('swift_code', 'DEUTLUL')
    ->set('website', 'https://akira.io')
    ->withAttributes([
        'phone' => '+352 1234 5678',
        'fax' => '+352 1234 5679',
        'establishment_year' => 2020,
    ])
    ->build();
```

### In Templates

Access in Blade templates:

```blade
@if($invoice->seller->has('registration_number'))
    <p>Reg: {{ $invoice->seller->get('registration_number') }}</p>
@endif

@if($invoice->seller->has('bank_account'))
    <p>Bank: {{ $invoice->seller->get('bank_account') }}</p>
@endif

@if($invoice->seller->has('website'))
    <p><a href="{{ $invoice->seller->get('website') }}">Visit Website</a></p>
@endif
```

## Item Custom Attributes

### Example

```php
$item = ItemBuilder::make()
    ->description('Professional Services')
    ->unitPrice(150.00)
    ->quantity(10)
    ->set('sku', 'SERV-PRO-001')
    ->set('category', 'Professional Services')
    ->set('project_id', 'PROJ-2024-001')
    ->set('cost_center', 'CC-100')
    ->withAttributes([
        'department' => 'Consulting',
        'billable' => true,
        'time_tracking_id' => 'TT-12345',
    ])
    ->build();
```

### In Templates

```blade
<td>{{ $item->description }}</td>
<td>{{ $item->get('sku', 'N/A') }}</td>
<td>{{ $item->get('category', 'General') }}</td>
<td>{{ number_format($item->unitPrice, 2) }}</td>
```

## Invoice Custom Attributes

### Common Use Cases

```php
$invoice = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->invoiceNumber('INV-2024-001')
    ->set('po_number', 'PO-ABC-123')
    ->set('project_code', 'PROJ-XYZ')
    ->set('approval_status', 'approved')
    ->set('assigned_to', 'John Doe')
    ->set('internal_reference', 'INT-REF-001')
    ->withAttributes([
        'cost_center' => 'CC-100',
        'department' => 'Sales',
        'region' => 'EMEA',
        'customer_segment' => 'Enterprise',
        'payment_method' => 'Bank Transfer',
        'contract_id' => 'CTR-2024-001',
        'approval_date' => now()->toDateString(),
        'approved_by' => 'Manager Name',
        'notes_internal' => 'Follow up after payment',
    ])
    ->build();
```

### In Templates

```blade
<div class="invoice-meta">
    @if($invoice->has('po_number'))
        <p>PO Number: {{ $invoice->get('po_number') }}</p>
    @endif

    @if($invoice->has('project_code'))
        <p>Project: {{ $invoice->get('project_code') }}</p>
    @endif

    @if($invoice->has('cost_center'))
        <p>Cost Center: {{ $invoice->get('cost_center') }}</p>
    @endif
</div>
```

## Configuration

Custom attributes are enabled by default. Disable in config if needed:

```php
// config/pdf-invoices.php
return [
    'allow_custom_attributes' => false, // Set to false to disable
];
```

## Type Safety

Attributes are stored as mixed types. Cast them as needed:

```php
$count = (int) $item->get('quantity_shipped', 0);
$price = (float) $item->get('custom_price', 0.0);
$active = (bool) $item->get('is_active', false);
$tags = (array) $item->get('tags', []);
```

## Best Practices

1. **Use descriptive keys**: `po_number` instead of `po`
2. **Use snake_case**: Consistent naming convention
3. **Provide defaults**: Use the default parameter in `get()`
4. **Document attributes**: Document custom attributes in your code
5. **Validate data**: Validate data before passing to builders
6. **Keep attributes flat**: Avoid nested arrays for simplicity

## Example: Complete Invoice with Attributes

```php
$seller = EntityBuilder::make()
    ->name('Akira Corporation')
    ->address('Luxembourg')
    ->vat('LU12345678')
    ->set('country', 'Luxembourg')
    ->set('registration_number', 'L123456789')
    ->build();

$buyer = EntityBuilder::make()
    ->name('Client Inc')
    ->address('Berlin, Germany')
    ->vat('DE987654321')
    ->set('country', 'Germany')
    ->set('customer_id', 'CUST-001')
    ->build();

$invoice = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem(
        ItemBuilder::make()
            ->description('Development Services')
            ->unitPrice(150.00)
            ->quantity(40)
            ->tax(0.19)
            ->set('sku', 'DEV-HOURS')
            ->set('project_id', 'PROJ-ABC')
            ->build()
    )
    ->invoiceNumber('INV-2024-001')
    ->issuedAt(now())
    ->dueAt(now()->addDays(30))
    ->currency('EUR')
    ->set('po_number', 'PO-2024-001')
    ->set('cost_center', 'CC-100')
    ->set('approved_by', 'John Manager')
    ->build();

// Access attributes
echo $invoice->get('po_number');           // 'PO-2024-001'
echo $buyer->get('customer_id');           // 'CUST-001'
echo $invoice->items[0]->get('project_id'); // 'PROJ-ABC'
```
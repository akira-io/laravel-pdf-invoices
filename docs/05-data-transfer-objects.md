# Data Transfer Objects

Laravel PDF Invoices uses readonly Data Transfer Objects (DTOs) to represent invoice data immutably. All DTOs are in the `Akira\PdfInvoices\DTO` namespace.

## InvoiceData

The main DTO containing complete invoice information.

### Properties

```php
readonly class InvoiceData
{
    public EntityData $seller;
    public EntityData $buyer;
    public array $items;                      // array<int, ItemData>
    public CarbonInterface|DateTimeInterface|null $issuedAt;
    public CarbonInterface|DateTimeInterface|null $dueAt;
    public string $invoiceNumber;
    public string $currency;
    public ?string $locale;
    public ?string $notes;
    public array $attributes;                 // array<string, mixed>
}
```

### Calculation Methods

**getSubtotal(): float**

Returns the subtotal of all items before discounts and taxes.

```php
$subtotal = $invoiceData->getSubtotal();
// Sum of (unit price × quantity) for all items
```

**getTotalDiscount(): float**

Returns the total discount amount across all items.

```php
$discounts = $invoiceData->getTotalDiscount();
// Sum of all item discount amounts
```

**getSubtotalAfterDiscount(): float**

Returns the subtotal after applying discounts but before taxes.

```php
$afterDiscount = $invoiceData->getSubtotalAfterDiscount();
// Subtotal minus total discounts
```

**getTotalTax(): float**

Returns the total tax amount across all items.

```php
$taxes = $invoiceData->getTotalTax();
// Sum of all item tax amounts
```

**getTotal(): float**

Returns the final invoice total including all taxes and discounts.

```php
$total = $invoiceData->getTotal();
// Sum of all item totals (subtotal - discount + tax)
```

### Custom Attributes

**get(string $key, mixed $default = null): mixed**

Retrieves a custom attribute by key.

```php
$poNumber = $invoiceData->get('po_number');
$dept = $invoiceData->get('department', 'General');
```

**has(string $key): bool**

Checks if a custom attribute exists.

```php
if ($invoiceData->has('po_number')) {
    // Process PO number
}
```

**attributes(): array**

Returns all custom attributes as an associative array.

```php
$allCustomData = $invoiceData->attributes();
```

## EntityData

Represents a seller or buyer entity.

### Properties

```php
readonly class EntityData
{
    public string $name;
    public ?string $address;
    public ?string $vatNumber;
    public ?string $logoUrl;
    public ?string $email;
    public array $attributes;  // array<string, mixed>
}
```

### Custom Attributes

**get(string $key, mixed $default = null): mixed**

Retrieves a custom attribute.

```php
$phone = $entityData->get('phone');
$website = $entityData->get('website', 'https://example.com');
```

**has(string $key): bool**

Checks if a custom attribute exists.

```php
if ($entityData->has('phone')) {
    echo $entityData->get('phone');
}
```

**attributes(): array**

Returns all custom attributes.

```php
$customFields = $entityData->attributes();
```

## ItemData

Represents a single invoice line item.

### Properties

```php
readonly class ItemData
{
    public string $description;
    public float $unitPrice;
    public int $quantity;
    public float $tax;           // Tax rate as decimal (0.19 = 19%)
    public float $discount;      // Discount rate as decimal (0.10 = 10%)
    public array $attributes;    // array<string, mixed>
}
```

### Calculation Methods

**getSubtotal(): float**

Returns the line subtotal before discount and tax.

```php
$lineSubtotal = $itemData->getSubtotal();
// unit price × quantity
```

**getDiscountAmount(): float**

Returns the discount amount in currency units.

```php
$discountAmount = $itemData->getDiscountAmount();
// subtotal × discount rate
```

**getSubtotalAfterDiscount(): float**

Returns the subtotal after discount but before tax.

```php
$afterDiscount = $itemData->getSubtotalAfterDiscount();
// subtotal - discount amount
```

**getTaxAmount(): float**

Returns the tax amount in currency units.

```php
$taxAmount = $itemData->getTaxAmount();
// subtotal after discount × tax rate
```

**getTotal(): float**

Returns the final line total including discount and tax.

```php
$lineTotal = $itemData->getTotal();
// subtotal after discount + tax amount
```

### Calculation Example

For an item with:
- Unit price: €100
- Quantity: 10
- Discount: 10% (0.10)
- Tax: 19% (0.19)

```php
$item = ItemBuilder::make()
    ->unitPrice(100)
    ->quantity(10)
    ->discount(0.10)
    ->tax(0.19)
    ->build();

$item->getSubtotal();              // 1000.00
$item->getDiscountAmount();        // 100.00
$item->getSubtotalAfterDiscount(); // 900.00
$item->getTaxAmount();             // 171.00
$item->getTotal();                 // 1071.00
```

### Custom Attributes

**get(string $key, mixed $default = null): mixed**

Retrieves a custom attribute.

```php
$sku = $itemData->get('sku');
$category = $itemData->get('category', 'General');
```

**has(string $key): bool**

Checks if a custom attribute exists.

```php
if ($itemData->has('sku')) {
    echo $itemData->get('sku');
}
```

**attributes(): array**

Returns all custom attributes.

```php
$customItemData = $itemData->attributes();
```

## Immutability

All DTOs are readonly and immutable by design. Once created, their properties cannot be changed. This ensures:

- **Thread Safety**: Safe to pass between processes
- **Predictability**: Data cannot be modified unexpectedly
- **Queue Safety**: Can be serialized and queued without state corruption

To modify invoice data, create a new instance using the builders:

```php
// Cannot do this - properties are readonly
$invoiceData->currency = 'USD'; // Error

// Instead, rebuild with changes
$updatedInvoice = InvoiceBuilder::make()
    ->seller($invoiceData->seller)
    ->buyer($invoiceData->buyer)
    ->items($invoiceData->items)
    ->currency('USD') // Changed
    ->build();
```

## Type Safety

All DTOs enforce strict types:

- Numeric values are typed as `int` or `float`
- Strings are typed as `string` or nullable `string`
- Arrays have documented generic types in PHPDoc
- Dates use `CarbonInterface|DateTimeInterface` union types

This provides IDE autocomplete, static analysis support, and runtime type safety.

**Previous:** [Builders](04-builders.md) | **Next:** [PDF Generation](06-pdf-generation.md)

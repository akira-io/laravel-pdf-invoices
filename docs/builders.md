# Builder Pattern

This package uses the Builder Pattern to create invoice-related objects in a clean, fluent way.

## Overview

All builders:
- Implement the `BuilderContract`
- Provide a static `make()` factory method
- Support method chaining for fluent API
- Return immutable DTOs on `build()`
- Support custom attributes via `set()` and `withAttributes()`

## EntityBuilder

Creates seller and buyer entities.

### Basic Usage

```php
use Akira\PdfInvoices\Builder\EntityBuilder;

$seller = EntityBuilder::make()
    ->name('Akira Corporation')
    ->address('Luxembourg')
    ->vat('LU12345678')
    ->email('contact@akira.io')
    ->logo('https://example.com/logo.png')
    ->build();
```

### Methods

- `name(string $name)`: Set entity name (required)
- `address(string $address)`: Set postal address
- `vat(string $vatNumber)`: Set VAT number
- `email(string $email)`: Set email address
- `logo(string $logoUrl)`: Set logo URL
- `set(string $key, mixed $value)`: Add custom attribute
- `withAttributes(array $data)`: Add multiple custom attributes
- `build(): EntityData`: Build immutable entity

### Custom Attributes

```php
$entity = EntityBuilder::make()
    ->name('Company')
    ->set('country', 'Luxembourg')
    ->set('phone', '+352 1234 5678')
    ->set('registration_number', 'ABC123')
    ->withAttributes([
        'currency_code' => 'EUR',
        'timezone' => 'Europe/Luxembourg',
    ])
    ->build();
```

## ItemBuilder

Creates invoice line items.

### Basic Usage

```php
use Akira\PdfInvoices\Builder\ItemBuilder;

$item = ItemBuilder::make()
    ->description('Professional Services')
    ->unitPrice(150.00)
    ->quantity(10)
    ->tax(0.20)
    ->discount(0.05)
    ->build();
```

### Methods

- `description(string $description)`: Set item description (required)
- `unitPrice(float $price)`: Set unit price (required)
- `quantity(int $quantity)`: Set quantity (default: 1)
- `tax(float $tax)`: Set tax rate as decimal (default: 0.0)
- `discount(float $discount)`: Set discount rate as decimal (default: 0.0)
- `set(string $key, mixed $value)`: Add custom attribute
- `withAttributes(array $data)`: Add multiple custom attributes
- `build(): ItemData`: Build immutable item

### Calculations

The ItemBuilder automatically calculates:

```php
$item->getSubtotal();              // unitPrice * quantity
$item->getDiscountAmount();        // subtotal * discount
$item->getSubtotalAfterDiscount(); // subtotal - discount
$item->getTaxAmount();             // subtotal_after_discount * tax
$item->getTotal();                 // subtotal_after_discount + tax
```

### Custom Attributes

```php
$item = ItemBuilder::make()
    ->description('Service')
    ->unitPrice(100.00)
    ->set('sku', 'SERV-001')
    ->set('category', 'Professional')
    ->set('project_id', 'PROJ-XYZ')
    ->build();

$item->get('sku');     // 'SERV-001'
$item->get('category'); // 'Professional'
```

## InvoiceBuilder

Creates complete invoices with all details.

### Basic Usage

```php
use Akira\PdfInvoices\Builder\InvoiceBuilder;

$invoice = InvoiceBuilder::make()
    ->seller($sellerEntity)
    ->buyer($buyerEntity)
    ->addItem($lineItem1)
    ->addItem($lineItem2)
    ->invoiceNumber('INV-2024-001')
    ->currency('EUR')
    ->notes('Payment due in 30 days.')
    ->build();
```

### Methods

- `seller(EntityData $seller)`: Set seller (required)
- `buyer(EntityData $buyer)`: Set buyer (required)
- `addItem(ItemData $item)`: Add line item
- `items(array $items)`: Set all items at once
- `invoiceNumber(string $number)`: Set invoice number
- `issuedAt(DateTime $date)`: Set issue date
- `dueAt(DateTime $date)`: Set due date
- `currency(string $currency)`: Set currency code (default: 'EUR')
- `notes(string $notes)`: Set payment/delivery notes
- `set(string $key, mixed $value)`: Add custom attribute
- `withAttributes(array $data)`: Add multiple custom attributes
- `build(): InvoiceData`: Build immutable invoice

### Validations

The `build()` method requires:
- Seller entity
- Buyer entity

Throws `InvalidArgumentException` if either is missing.

### Invoice Calculations

The InvoiceBuilder automatically calculates:

```php
$invoice->getSubtotal();           // Sum of all item subtotals
$invoice->getTotalDiscount();      // Sum of all discount amounts
$invoice->getSubtotalAfterDiscount(); // Subtotal minus total discounts
$invoice->getTotalTax();           // Sum of all tax amounts
$invoice->getTotal();              // Final amount due
```

### Custom Attributes

```php
$invoice = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->set('po_number', 'PO-2024-001')
    ->set('project_code', 'PROJ-ABC')
    ->set('department', 'Sales')
    ->withAttributes([
        'approval_status' => 'approved',
        'cost_center' => 'CC-123',
    ])
    ->build();

$invoice->get('po_number');    // 'PO-2024-001'
$invoice->get('project_code');  // 'PROJ-ABC'
```

## Chaining Pattern

All builders support full method chaining:

```php
$invoice = InvoiceBuilder::make()
    ->seller(
        EntityBuilder::make()
            ->name('Company A')
            ->address('Street 123')
            ->vat('VAT123')
            ->email('contact@a.com')
            ->set('country', 'Luxembourg')
            ->build()
    )
    ->buyer(
        EntityBuilder::make()
            ->name('Company B')
            ->address('Avenue 456')
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Service A')
            ->unitPrice(100.00)
            ->quantity(5)
            ->tax(0.20)
            ->discount(0.10)
            ->set('sku', 'SERV-A')
            ->build()
    )
    ->addItem(
        ItemBuilder::make()
            ->description('Service B')
            ->unitPrice(50.00)
            ->quantity(10)
            ->tax(0.20)
            ->set('sku', 'SERV-B')
            ->build()
    )
    ->invoiceNumber('INV-2024-001')
    ->issuedAt(now())
    ->dueAt(now()->addDays(30))
    ->currency('EUR')
    ->notes('Payment terms: Net 30')
    ->set('po_number', 'PO-ABC-123')
    ->build();
```

## Data Transfer Objects (DTOs)

All builders return immutable data transfer objects:

### EntityData

```php
$entity = EntityBuilder::make()->name('Company')->build();
// Read-only properties:
// - name: string
// - address: ?string
// - vatNumber: ?string
// - logoUrl: ?string
// - email: ?string
// - attributes: array
```

### ItemData

```php
$item = ItemBuilder::make()->description('Service')->unitPrice(100)->build();
// Read-only properties:
// - description: string
// - unitPrice: float
// - quantity: int
// - tax: float
// - discount: float
// - attributes: array
```

### InvoiceData

```php
$invoice = InvoiceBuilder::make()->seller($s)->buyer($b)->build();
// Read-only properties:
// - seller: EntityData
// - buyer: EntityData
// - items: ItemData[]
// - issuedAt: ?DateTime
// - dueAt: ?DateTime
// - invoiceNumber: string
// - currency: string
// - notes: ?string
// - attributes: array
```

All DTOs are immutable and cannot be modified after creation. Create a new instance if you need different data.
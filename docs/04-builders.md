# Builders

Builders provide a fluent, chainable API for constructing invoice data. The package includes three builders: `InvoiceBuilder`, `EntityBuilder`, and `ItemBuilder`.

## InvoiceBuilder

The main builder for creating complete invoice data structures.

### Basic Usage

```php
use Akira\PdfInvoices\Builder\InvoiceBuilder;

$invoice = InvoiceBuilder::make()
    ->seller($sellerEntity)
    ->buyer($buyerEntity)
    ->addItem($item)
    ->invoiceNumber('INV-2024-001')
    ->issuedAt(now())
    ->dueAt(now()->addDays(30))
    ->currency('EUR')
    ->locale('en')
    ->notes('Payment due within 30 days')
    ->build();
```

### Methods

**seller(EntityData $seller): static**

Sets the seller entity. Required. Throws `InvalidArgumentException` if not provided before `build()`.

```php
$invoice = InvoiceBuilder::make()
    ->seller($sellerEntity)
    ->build();
```

**buyer(EntityData $buyer): static**

Sets the buyer entity. Required. Throws `InvalidArgumentException` if not provided before `build()`.

```php
$invoice = InvoiceBuilder::make()
    ->buyer($buyerEntity)
    ->build();
```

**addItem(ItemData $item): static**

Adds a single line item to the invoice. Can be called multiple times.

```php
$invoice = InvoiceBuilder::make()
    ->addItem($item1)
    ->addItem($item2)
    ->addItem($item3)
    ->build();
```

**items(array $items): static**

Sets all line items at once, replacing any previously added items.

```php
$invoice = InvoiceBuilder::make()
    ->items([$item1, $item2, $item3])
    ->build();
```

**invoiceNumber(string $number): static**

Sets the invoice number. Defaults to empty string.

```php
$invoice = InvoiceBuilder::make()
    ->invoiceNumber('INV-2024-001')
    ->build();
```

**issuedAt(CarbonInterface|DateTimeInterface $date): static**

Sets the invoice issue date. Accepts `Carbon`, `CarbonImmutable`, or any `DateTimeInterface` instance.

```php
use Carbon\Carbon;

$invoice = InvoiceBuilder::make()
    ->issuedAt(Carbon::today())
    ->build();
```

**dueAt(CarbonInterface|DateTimeInterface $date): static**

Sets the invoice due date. Accepts `Carbon`, `CarbonImmutable`, or any `DateTimeInterface` instance.

```php
$invoice = InvoiceBuilder::make()
    ->dueAt(now()->addDays(30))
    ->build();
```

**currency(string $currency): static**

Sets the currency code (EUR, USD, GBP, etc.). Defaults to `'EUR'`.

```php
$invoice = InvoiceBuilder::make()
    ->currency('USD')
    ->build();
```

**locale(string $locale): static**

Sets the locale for translations. Overrides the default from configuration.

```php
$invoice = InvoiceBuilder::make()
    ->locale('fr')
    ->build();
```

**notes(string $notes): static**

Sets invoice notes or payment terms.

```php
$invoice = InvoiceBuilder::make()
    ->notes('Payment is due within 30 days. Late payments incur 5% monthly interest.')
    ->build();
```

**set(string $key, mixed $value): static**

Adds a custom attribute to the invoice data.

```php
$invoice = InvoiceBuilder::make()
    ->set('po_number', 'PO-12345')
    ->set('payment_method', 'Bank Transfer')
    ->build();
```

**withAttributes(array $data): static**

Adds multiple custom attributes at once.

```php
$invoice = InvoiceBuilder::make()
    ->withAttributes([
        'po_number' => 'PO-12345',
        'payment_method' => 'Bank Transfer',
        'department' => 'Engineering',
    ])
    ->build();
```

**build(): InvoiceData**

Builds and returns the final `InvoiceData` DTO. Validates that seller and buyer are set.

## EntityBuilder

Builder for creating seller and buyer entities.

### Basic Usage

```php
use Akira\PdfInvoices\Builder\EntityBuilder;

$entity = EntityBuilder::make()
    ->name('Acme Corporation')
    ->address('123 Business Street, Tech City')
    ->email('billing@acme.com')
    ->vat('US123456789')
    ->logo('https://acme.com/logo.png')
    ->build();
```

### Methods

**name(string $name): static**

Sets the entity name. This is the only required field, though an empty string is allowed.

```php
$entity = EntityBuilder::make()
    ->name('Client Company Ltd')
    ->build();
```

**address(string $address): static**

Sets the entity address. Defaults to `null`.

```php
$entity = EntityBuilder::make()
    ->address('456 Oak Avenue, Suite 200, Business City, BC 12345')
    ->build();
```

**vat(string $vatNumber): static**

Sets the VAT/tax identification number. Defaults to `null`.

```php
$entity = EntityBuilder::make()
    ->vat('GB987654321')
    ->build();
```

**email(string $email): static**

Sets the entity email address. Defaults to `null`.

```php
$entity = EntityBuilder::make()
    ->email('accounts@company.com')
    ->build();
```

**logo(string $logoUrl): static**

Sets the logo URL or path. Defaults to `null`.

```php
$entity = EntityBuilder::make()
    ->logo('https://cdn.example.com/logo.png')
    ->build();
```

**set(string $key, mixed $value): static**

Adds a custom attribute.

```php
$entity = EntityBuilder::make()
    ->name('Company')
    ->set('phone', '+1-555-0123')
    ->set('website', 'https://example.com')
    ->build();
```

**withAttributes(array $data): static**

Adds multiple custom attributes.

```php
$entity = EntityBuilder::make()
    ->name('Company')
    ->withAttributes([
        'phone' => '+1-555-0123',
        'fax' => '+1-555-0124',
        'website' => 'https://example.com',
    ])
    ->build();
```

**build(): EntityData**

Builds and returns the final `EntityData` DTO.

## ItemBuilder

Builder for creating invoice line items.

### Basic Usage

```php
use Akira\PdfInvoices\Builder\ItemBuilder;

$item = ItemBuilder::make()
    ->description('Professional Services - Consulting')
    ->unitPrice(150.00)
    ->quantity(10)
    ->tax(0.19)
    ->discount(0.05)
    ->build();
```

### Methods

**description(string $description): static**

Sets the item description. Defaults to empty string.

```php
$item = ItemBuilder::make()
    ->description('Web Development Services')
    ->build();
```

**unitPrice(float $price): static**

Sets the price per unit. Defaults to `0.0`.

```php
$item = ItemBuilder::make()
    ->unitPrice(99.99)
    ->build();
```

**quantity(int $quantity): static**

Sets the item quantity. Defaults to `1`.

```php
$item = ItemBuilder::make()
    ->quantity(5)
    ->build();
```

**tax(float $tax): static**

Sets the tax rate as a decimal (0.19 for 19%). Defaults to `0.0`.

```php
$item = ItemBuilder::make()
    ->tax(0.21) // 21% tax
    ->build();
```

**discount(float $discount): static**

Sets the discount rate as a decimal (0.10 for 10%). Defaults to `0.0`.

```php
$item = ItemBuilder::make()
    ->discount(0.15) // 15% discount
    ->build();
```

**set(string $key, mixed $value): static**

Adds a custom attribute.

```php
$item = ItemBuilder::make()
    ->description('Product')
    ->set('sku', 'PROD-12345')
    ->set('category', 'Electronics')
    ->build();
```

**withAttributes(array $data): static**

Adds multiple custom attributes.

```php
$item = ItemBuilder::make()
    ->description('Product')
    ->withAttributes([
        'sku' => 'PROD-12345',
        'category' => 'Electronics',
        'warranty_months' => 24,
    ])
    ->build();
```

**build(): ItemData**

Builds and returns the final `ItemData` DTO.

## Builder Pattern Benefits

All builders follow the same contract defined by `BuilderContract`:

- **Fluent API**: Method chaining for readable code
- **Immutable Output**: Builders produce readonly DTOs
- **Type Safety**: Strict typing throughout
- **Validation**: Required fields validated at `build()` time
- **Extensibility**: Custom attributes via `set()` and `withAttributes()`

**Previous:** [Quick Start](03-quick-start.md) | **Next:** [Data Transfer Objects](05-data-transfer-objects.md)

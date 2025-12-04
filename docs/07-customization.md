# Customization Guide

This package is designed to be fully extensible. Replace any component with your own implementation.

## Service Container Bindings

The package registers services in the Laravel service container. You can override them in your application.

### Currency Formatter

Replace the default currency formatter:

```php
// In a service provider
use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;

app()->singleton(CurrencyFormatterContract::class, function () {
    return new MyCustomCurrencyFormatter();
});
```

Create your formatter:

```php
use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;

final class MyCustomCurrencyFormatter implements CurrencyFormatterContract
{
    public function format(float $amount, string $currency = '', string $locale = 'en'): string
    {
        // Your implementation
        return sprintf('%s %.2f', $currency, $amount);
    }
}
```

Or in configuration:

```php
// config/pdf-invoices.php
return [
    'currency' => [
        'driver' => MyCustomCurrencyFormatter::class,
    ],
];
```

### PDF Generator

Replace the PDF generation:

```php
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;

app()->singleton(PdfGeneratorContract::class, function () {
    return new MyCustomPdfGenerator();
});
```

Implement the contract:

```php
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;

final class MyCustomPdfGenerator implements PdfGeneratorContract
{
    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        // Your PDF generation logic
        return $pdf_content;
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        // Your save logic
        return $path;
    }
}
```

### Storage Driver

Replace the storage implementation:

```php
use Akira\PdfInvoices\Contracts\StorageDriverContract;

app()->singleton(StorageDriverContract::class, function () {
    return new MyCustomStorageDriver();
});
```

Implement the contract:

```php
use Akira\PdfInvoices\Contracts\StorageDriverContract;

final class MyCustomStorageDriver implements StorageDriverContract
{
    public function save(string $path, string $content): string
    {
        // Your storage logic
        return $path;
    }

    public function exists(string $path): bool
    {
        return true; // Your check
    }

    public function get(string $path): string
    {
        return ''; // Your retrieval
    }

    public function delete(string $path): bool
    {
        return true; // Your deletion
    }
}
```

## Custom Blade Templates

Templates are stored in `resources/views/pdf-invoices/pdf/templates/`.

Create a new template `resources/views/pdf-invoices/pdf/templates/my-template.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
</head>
<body>
    <!-- Your HTML -->
</body>
</html>
```

Use it:

```php
app(\Akira\PdfInvoices\Contracts\PdfGeneratorContract::class)
    ->generate($invoice, 'my-template');
```

## Building on the Builders

Extend builders with additional methods:

```php
use Akira\PdfInvoices\Builder\EntityBuilder as BaseEntityBuilder;

final class EntityBuilder extends BaseEntityBuilder
{
    public function fromModel(Model $model): static
    {
        return $this
            ->name($model->name)
            ->email($model->email)
            ->set('model_id', $model->id);
    }
}
```

Or create helper functions:

```php
function createInvoiceFromOrder(Order $order): InvoiceData
{
    return InvoiceBuilder::make()
        ->seller(
            EntityBuilder::make()
                ->name(config('app.name'))
                ->address(config('invoices.seller.address'))
                ->vat(config('invoices.seller.vat'))
                ->build()
        )
        ->buyer(
            EntityBuilder::make()
                ->name($order->customer_name)
                ->email($order->customer_email)
                ->set('customer_id', $order->customer_id)
                ->build()
        )
        ->addItem(
            ItemBuilder::make()
                ->description($order->description)
                ->unitPrice($order->price)
                ->quantity($order->quantity)
                ->tax($order->tax_rate)
                ->build()
        )
        ->invoiceNumber($order->invoice_number)
        ->invoiceNumber(now())
        ->dueAt(now()->addDays(30))
        ->build();
}
```

## Extending DTOs

DTOs are read-only and immutable. Create wrapper classes if you need additional functionality:

```php
final class EnhancedInvoice
{
    public function __construct(
        private InvoiceData $invoice,
        private InvoiceRepository $repository,
    ) {}

    public function save(): string
    {
        $path = "invoices/{$this->invoice->invoiceNumber}.pdf";
        return $this->repository->save($this->invoice, $path);
    }

    public function getDisplayNumber(): string
    {
        return "INV-{$this->invoice->invoiceNumber}";
    }

    public function toArray(): array
    {
        return [
            'number' => $this->invoice->invoiceNumber,
            'total' => $this->invoice->getTotal(),
            'seller' => $this->invoice->seller->name,
            'buyer' => $this->invoice->buyer->name,
        ];
    }
}
```

## Factory Classes

Create factories for common invoice types:

```php
final class QuoteFactory
{
    public static function create(Order $order): InvoiceData
    {
        return InvoiceBuilder::make()
            ->seller(/* ... */)
            ->buyer(/* ... */)
            ->addItem(/* ... */)
            ->set('document_type', 'quote')
            ->set('valid_until', now()->addDays(30)->toDateString())
            ->build();
    }
}

final class InvoiceFactory
{
    public static function create(Order $order): InvoiceData
    {
        return InvoiceBuilder::make()
            ->seller(/* ... */)
            ->buyer(/* ... */)
            ->addItem(/* ... */)
            ->set('document_type', 'invoice')
            ->build();
    }
}

// Usage
$quote = QuoteFactory::create($order);
$invoice = InvoiceFactory::create($order);
```

## Event Hooks

Create listeners for invoice lifecycle events:

```php
// You can add custom logic using service provider event listeners

app()->make(EventDispatcher::class)->listen(
    'invoice.created',
    function (InvoiceCreatedEvent $event) {
        // Log invoice creation
        // Send notifications
        // Update database
    }
);
```

## Configuration Customization

Extend configuration in your application:

```php
// config/pdf-invoices.php
return array_merge(
    config('pdf-invoices'),
    [
        'company' => [
            'name' => 'Your Company',
            'address' => '123 Main St',
            'vat' => 'EU123456789',
            'email' => 'contact@company.com',
        ],
        'taxes' => [
            'standard' => 0.20,
            'reduced' => 0.10,
            'zero' => 0.0,
        ],
    ]
);
```

## Integration Example

Complete integration with Eloquent models:

```php
use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;

final class InvoiceService
{
    public function __construct(
        private PdfGeneratorContract $pdfGenerator,
        private StorageDriverContract $storage,
    ) {}

    public function generateFromOrder(Order $order): string
    {
        $invoice = InvoiceBuilder::make()
            ->seller($this->createSellerEntity())
            ->buyer($this->createBuyerEntity($order->customer))
            ->addItem(...$this->createItems($order))
            ->invoiceNumber($order->invoice_number)
            ->issuedAt($order->created_at)
            ->dueAt($order->created_at->addDays(30))
            ->currency(config('app.currency'))
            ->set('order_id', $order->id)
            ->build();

        $pdf = $this->pdfGenerator->generate($invoice, 'modern');
        $path = "invoices/{$order->invoice_number}.pdf";

        return $this->storage->save($path, $pdf);
    }

    private function createSellerEntity(): EntityData
    {
        return EntityBuilder::make()
            ->name(config('app.name'))
            ->address(config('invoices.seller.address'))
            ->vat(config('invoices.seller.vat'))
            ->email(config('invoices.seller.email'))
            ->build();
    }

    private function createBuyerEntity(Customer $customer): EntityData
    {
        return EntityBuilder::make()
            ->name($customer->name)
            ->address($customer->full_address)
            ->email($customer->email)
            ->set('customer_id', $customer->id)
            ->build();
    }

    private function createItems(Order $order): array
    {
        return $order->items->map(function (OrderItem $item) {
            return ItemBuilder::make()
                ->description($item->product->name)
                ->unitPrice((float) $item->price)
                ->quantity($item->quantity)
                ->tax($this->getTaxRate($item->product))
                ->set('product_id', $item->product_id)
                ->build();
        })->toArray();
    }

    private function getTaxRate(Product $product): float
    {
        return config("taxes.{$product->tax_category}", 0.0);
    }
}
```

## Best Practices

1. **Use Dependency Injection**: Inject contracts, not implementations
2. **Extend, Don't Modify**: Create subclasses instead of modifying core classes
3. **Use Configurations**: Keep customizations in config files
4. **Document Custom Code**: Add PHPDoc and comments
5. **Test Your Extensions**: Add tests for custom implementations
6. **Follow SOLID Principles**: Keep code modular and maintainable---

---

**← Previous:** [06 - Creating Custom Templates](./06-creating-custom-templates.md) | **Next:
** [08 - Localization →](./08-localization.md)

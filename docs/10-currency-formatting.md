# Currency Formatting

Currency formatting is handled through the `CurrencyFormatterContract` interface. The package provides two implementations with support for custom formatters.

## CurrencyFormatterContract

The interface defining currency formatting behavior.

### Method

**format(float $amount, string $currency = '', string $locale = 'en'): string**

Formats a numeric amount as a currency string.

```php
use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;

$formatter = app(CurrencyFormatterContract::class);
$formatted = $formatter->format(1234.56, 'EUR', 'en');
// Returns: "€1,234.56" (depends on formatter implementation)
```

## LaravelCurrencyFormatter (Default)

Uses Laravel's `Number` helper for locale-aware currency formatting.

### Configuration

Set as default in `config/pdf-invoices.php`:

```php
'currency' => [
    'driver' => env('INVOICES_CURRENCY_DRIVER', Akira\PdfInvoices\Support\LaravelCurrencyFormatter::class),
    'code' => env('INVOICES_CURRENCY_CODE', 'EUR'),
    'locale' => env('INVOICES_LOCALE', 'en'),
],
```

### Formatting Examples

```php
use Akira\PdfInvoices\Support\LaravelCurrencyFormatter;

$formatter = new LaravelCurrencyFormatter();

// English locale
$formatter->format(1234.56, 'USD', 'en');
// Returns: "$1,234.56"

// French locale
$formatter->format(1234.56, 'EUR', 'fr');
// Returns: "1 234,56 €"

// German locale
$formatter->format(1234.56, 'EUR', 'de');
// Returns: "1.234,56 €"

// Portuguese locale
$formatter->format(1234.56, 'BRL', 'pt');
// Returns: "R$ 1.234,56"

// Without currency symbol (plain number)
$formatter->format(1234.56, '', 'en');
// Returns: "1,234.56"
```

### Supported Currencies

Supports all ISO 4217 currency codes:

- **USD** - US Dollar
- **EUR** - Euro
- **GBP** - British Pound
- **JPY** - Japanese Yen
- **BRL** - Brazilian Real
- **CAD** - Canadian Dollar
- **AUD** - Australian Dollar
- And 150+ more...

## SimpleCurrencyFormatter

A basic formatter using PHP's native `number_format()`.

### Usage

Configure in service provider or directly:

```php
use Akira\PdfInvoices\Support\SimpleCurrencyFormatter;

$formatter = new SimpleCurrencyFormatter(symbol: '$');

$formatter->format(1234.56, 'USD');
// Returns: "USD 1,234.56"

$formatter->format(1234.56);
// Returns: "$ 1,234.56" (uses constructor symbol)
```

### Configuration

Bind to container:

```php
$this->app->singleton(CurrencyFormatterContract::class, function () {
    return new SimpleCurrencyFormatter(symbol: '€');
});
```

Or via config:

```php
'currency' => [
    'driver' => Akira\PdfInvoices\Support\SimpleCurrencyFormatter::class,
    'symbol' => env('INVOICES_CURRENCY_SYMBOL', '€'),
],
```

## Using in Templates

Templates can inject the formatter:

```blade
@inject('formatter', 'Akira\PdfInvoices\Contracts\CurrencyFormatterContract')

<div class="totals">
    <div>{{ $translator->__('subtotal') }}: 
        {{ $formatter->format($invoice->getSubtotal(), $invoice->currency, $invoice->locale ?? 'en') }}
    </div>
    <div>{{ $translator->__('tax') }}: 
        {{ $formatter->format($invoice->getTotalTax(), $invoice->currency, $invoice->locale ?? 'en') }}
    </div>
    <div><strong>{{ $translator->__('total') }}: 
        {{ $formatter->format($invoice->getTotal(), $invoice->currency, $invoice->locale ?? 'en') }}
    </strong></div>
</div>
```

## Manual Formatting

Format currency manually without the formatter:

```blade
{{-- Simple format --}}
{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}

{{-- With thousand separator --}}
{{ number_format($invoice->getTotal(), 2, '.', ',') }} {{ $invoice->currency }}

{{-- Custom precision --}}
{{ number_format($invoice->getTotal(), 0) }} {{ $invoice->currency }}
```

## Currency Configuration

### Default Currency

Set the default currency code:

```env
INVOICES_CURRENCY_CODE=USD
```

Access via ConfigManager:

```php
use Akira\PdfInvoices\Config\ConfigManager;

$config = app(ConfigManager::class);
$defaultCurrency = $config->currencyCode(); // 'USD'
```

### Per-Invoice Currency

Override per invoice:

```php
$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item)
    ->currency('GBP') // British Pounds
    ->build();
```

## Custom Currency Formatter

Create a custom formatter by implementing `CurrencyFormatterContract`:

```php
use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;

class MoneyPhpFormatter implements CurrencyFormatterContract
{
    public function format(float $amount, string $currency = '', string $locale = 'en'): string
    {
        $money = new \Money\Money(
            (int) ($amount * 100), 
            new \Money\Currency($currency ?: 'USD')
        );
        
        $formatter = new \Money\Formatter\IntlMoneyFormatter(
            new \NumberFormatter($locale, \NumberFormatter::CURRENCY),
            new \Money\Currencies\ISOCurrencies()
        );
        
        return $formatter->format($money);
    }
}
```

Register in service provider:

```php
$this->app->singleton(CurrencyFormatterContract::class, MoneyPhpFormatter::class);
```

Or via config:

```php
'currency' => [
    'driver' => App\Services\MoneyPhpFormatter::class,
],
```

## Multi-Currency Invoices

Handle invoices with different currencies:

```php
// Store base currency
$baseCurrency = 'USD';

// Items in different currencies
$item1 = ItemBuilder::make()
    ->description('Service in EUR')
    ->unitPrice(100)
    ->set('currency', 'EUR')
    ->build();

$item2 = ItemBuilder::make()
    ->description('Service in USD')
    ->unitPrice(120)
    ->set('currency', 'USD')
    ->build();

$invoiceData = InvoiceBuilder::make()
    ->seller($seller)
    ->buyer($buyer)
    ->addItem($item1)
    ->addItem($item2)
    ->currency($baseCurrency)
    ->set('is_multi_currency', true)
    ->build();
```

Display in template:

```blade
@foreach($invoice->items as $item)
    <tr>
        <td>{{ $item->description }}</td>
        <td>
            @if($item->has('currency'))
                {{ $formatter->format($item->getTotal(), $item->get('currency'), $invoice->locale ?? 'en') }}
            @else
                {{ $formatter->format($item->getTotal(), $invoice->currency, $invoice->locale ?? 'en') }}
            @endif
        </td>
    </tr>
@endforeach
```

## Decimal Precision

Control decimal places in formatted output:

```php
// LaravelCurrencyFormatter uses locale defaults (usually 2 decimals)
$formatter->format(1234.567, 'USD', 'en');
// Returns: "$1,234.57"

// SimpleCurrencyFormatter always uses 2 decimals
$formatter->format(1234.567);
// Returns: "$ 1,234.57"

// Manual precision control
number_format($amount, 3); // 3 decimals
number_format($amount, 0); // No decimals
```

## Zero Amount Handling

Both formatters handle zero and negative amounts:

```php
$formatter->format(0, 'EUR', 'en');
// Returns: "€0.00"

$formatter->format(-100, 'USD', 'en');
// Returns: "-$100.00"
```

**Previous:** [Localization](09-localization.md) | **Next:** [Custom Attributes](11-custom-attributes.md)

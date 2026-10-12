# e-Fatura Template

The `efatura` template renders a full Cabo Verde e-Fatura invoice with its auxiliary tax document (DFA) layout: issuer and customer blocks, document facts, an items table that paginates with carried-forward totals, the fiscal totals, the currency conversion evidence, fiscal notes and the DFE lookup block with its QR code.

It is written for the `dompdf` driver. The layout uses absolutely positioned elements in points on an A4 page and plain CSS, with no Tailwind, flexbox or grid.

## Requirements

```bash
composer require barryvdh/laravel-dompdf
composer require akira/laravel-qrcode
```

```dotenv
INVOICES_PDF_DRIVER=dompdf
```

Turn on font subsetting. `barryvdh/laravel-dompdf` ships with it off, which embeds the whole DejaVu Sans font and makes every invoice weigh about 880 KB; with it on, a one-page invoice is about 30 KB:

```php
config(['dompdf.options.enable_font_subsetting' => true]);
```

Set it in a service provider, or publish `config/dompdf.php` and change `enable_font_subsetting` there.

`akira/laravel-qrcode` is a suggested dependency, not a required one: only this template needs it. When the invoice carries a QR payload and the package is missing, rendering stops with a `RuntimeException` that names the package to install. The QR code is embedded as an SVG data URI, so dompdf never needs remote access.

## Usage

```php
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\CurrencyConversionData;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\FiscalData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\InvoiceTotalsData;
use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Enums\EmissionMode;

$invoice = new InvoiceData(
    seller: new EntityData(name: 'Akira Comércio, Lda', address: 'Praia', vatNumber: '200123456', phone: '2601010'),
    buyer: new EntityData(name: 'Cliente Exemplo', vatNumber: '100987654', email: 'cliente@example.cv'),
    items: [
        new ItemData(
            description: 'Cimento Portland',
            unitPrice: 0.0,
            code: 'CIM-001',
            unit: 'UN',
            decimalQuantity: '2',
            decimalUnitPrice: '700.00',
            decimalTotal: '1400.00',
            taxes: [new ItemTaxData(code: 'IVA', rate: '15')],
        ),
    ],
    issuedAt: now(),
    currency: 'CVE',
    locale: 'pt_PT',
    fiscal: new FiscalData(
        documentType: 'FTE',
        documentTitle: 'Fatura Eletrónica',
        series: 'A',
        number: '42',
        emissionMode: EmissionMode::Online,
        identifier: $iud,
        qrPayload: "https://pe.efatura.cv/dfe/view/{$iud}",
    ),
    totals: new InvoiceTotalsData(net: '1400.00', payable: '1610.00', tax: '210.00'),
    conversions: [
        new CurrencyConversionData(currency: 'EUR', amount: '14.60', rate: '110.265', date: now(), source: 'Banco de Cabo Verde'),
    ],
);

$pdf = app(PdfGeneratorContract::class)->generate($invoice, 'efatura');
```

The builders expose the same fields: `ItemBuilder::code()`, `unit()`, `decimalQuantity()`, `decimalUnitPrice()`, `decimalTotal()`, `addTax()`; `InvoiceBuilder::fiscal()`, `totals()`, `addConversion()`; `EntityBuilder::phone()`.

## Exact values

Every amount the template prints comes from a decimal string, never from a float. Decimal strings use a dot and no grouping (`"1400.00"`, `"-12.5"`, `"0.12345"`) and hold at most 30 integer and 10 fraction digits; anything else throws an `InvalidArgumentException` when the DTO is built.

| Field | Used for |
| --- | --- |
| `ItemData::$decimalQuantity` | Quantity column, non-integer quantities allowed |
| `ItemData::$decimalUnitPrice` | Unit price column, rounded to at most 5 decimals |
| `ItemData::$decimalTotal` | Line total column and the carried-forward sum |
| `ItemData::$taxes` | VAT column: rate (`15%`), amount, or exemption code, joined with ` + ` |
| `InvoiceData::$totals` | Fiscal totals, never recalculated, only rounded to two decimals for display |

The fields are optional. Without them the template falls back to the legacy float fields, so an invoice built for the other templates still renders: the line total is unit price times quantity, the subtotal is their sum before discounts, and the discounts, tax and payable rows come from `getTotalDiscount()`, `getTotalTax()` and `getTotal()`.

An item with `decimalQuantity` or `decimalUnitPrice` must also carry `decimalTotal`, and an invoice whose items carry `decimalTotal` must pass `totals`. Both cases throw instead of printing a total computed from floats.

`InvoiceTotalsData` holds the net total, discounts, charges, tax, the optional withholding and rounding amounts, and the payable total. Withholding and rounding rows appear only when they are given.

## Number format

Escudo amounts follow the e-Fatura manual (v11): a space groups thousands and `$` separates the decimals, so `1400` prints as `1 400$00`. Unit prices keep between two and five decimals. Amounts are rounded half up to two decimals. Other currencies use a decimal comma and their code (`14,60 EUR`). Exchange rates print as `1 EUR = 110,26500 CVE`.

`Akira\PdfInvoices\Support\FiscalNumberFormatter` implements these rules and can be used on its own.

## Emission modes

`FiscalData::$emissionMode` takes an `EmissionMode` case:

| Mode | Effect |
| --- | --- |
| `Online` | No contingency notice |
| `Offline` | Notice `EMITIDO EM CONTINGÊNCIA OFFLINE - Pendente de Autorização` |
| `Off` | Notice with the IUC from `contingencyCode`, item codes hidden, no QR code even when a payload is given |

## Header title

The title at the top right is `FiscalData::$documentTitle`, so a full invoice reads "Fatura Eletrónica" (or the name of whichever document type is issued). Without it, the title falls back to the translated "DOCUMENTO FISCAL AUXILIAR".

## Pagination

Rows are split into pages in PHP before rendering, because the generator does not expose the dompdf canvas:

- The first page holds 11 rows and every continuation page 15.
- A page that overflows ends with "A transportar" and the running sum of the line totals as printed, each rounded to two decimals.
- Continuation pages repeat the header and document facts, and replace the parties with "Continuação" and the IUD.
- The totals, conversion evidence, notes and DFE lookup stay together on the last page; when they do not fit below the last row they move to a page of their own. When they do not fit on a page of their own either, because the notes are too long, rendering throws a `LengthException` rather than cut the DFE lookup off the page.
- Every page shows "Pág. x/y".

## Layout differences from node-efatura

The layout follows the PDFKit renderer of node-efatura, with three deliberate differences:

- The contingency notice sits below the document facts (x 306, y 236) rather than at x 54, y 184, where it overlapped the issuer address and contact once rendered in DejaVu Sans.
- The columns are rebalanced (Code 70, Description 150, Qty 50, Price 85, VAT 50, Total 70 plus its 8 pt gutter, from x 66 to the right margin at x 541) so a five-decimal unit price such as `1 234$56789` fits in DejaVu Sans; descriptions longer than 26 characters and codes longer than 13 end with an ellipsis.
- The summary rises above its usual place (y 540) whenever conversion evidence, notes or extra total rows would otherwise push the DFE lookup off the page.

## Translations

The labels live under the `efatura` key of `resources/lang/{locale}/invoice.php`, in every shipped locale. The `pt` locale reuses `pt_PT`.

**Previous:** [Troubleshooting](13-troubleshooting.md)

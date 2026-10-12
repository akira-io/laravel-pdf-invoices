<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Tests\Support;

use Akira\PdfInvoices\DTO\CurrencyConversionData;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\FiscalData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\InvoiceTotalsData;
use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Enums\EmissionMode;
use Carbon\CarbonImmutable;

final class EfaturaInvoiceFactory
{
    public const string IUD = 'CV1251012123456789012345678901234567890123';

    public static function make(
        int $lines = 1,
        EmissionMode $mode = EmissionMode::Online,
        bool $conversion = false,
        array $notes = [],
    ): InvoiceData {
        $items = [];

        for ($index = 1; $index <= $lines; $index++) {
            $items[] = new ItemData(
                description: "Cimento Portland saco {$index}",
                unitPrice: 0.0,
                code: sprintf('CIM-%03d', $index),
                unit: 'UN',
                decimalQuantity: '2',
                decimalUnitPrice: '700.00',
                decimalTotal: '1400.00',
                taxes: [new ItemTaxData(rate: '15')],
            );
        }

        $net = number_format(1400 * $lines, 2, '.', '');
        $tax = number_format(210 * $lines, 2, '.', '');
        $payable = number_format(1610 * $lines, 2, '.', '');

        return new InvoiceData(
            seller: new EntityData(name: 'Akira Comércio, Lda', address: 'Achada Santo António, Praia', vatNumber: '200123456', phone: '2601010'),
            buyer: new EntityData(name: 'Cliente Exemplo', address: 'Mindelo, São Vicente', vatNumber: '100987654', email: 'cliente@example.cv'),
            items: $items,
            issuedAt: CarbonImmutable::parse('2026-10-12 10:15:30'),
            invoiceNumber: '42',
            currency: 'CVE',
            locale: 'pt_PT',
            fiscal: new FiscalData(
                documentType: 'FTE',
                documentTitle: 'Fatura Eletrónica',
                series: 'A',
                number: '42',
                emissionMode: $mode,
                identifier: self::IUD,
                qrPayload: $mode === EmissionMode::Off ? null : 'https://pe.efatura.cv/dfe/view/'.self::IUD,
                contingencyCode: $mode === EmissionMode::Off ? '2026/17' : null,
                notes: $notes,
            ),
            totals: new InvoiceTotalsData(net: $net, payable: $payable, tax: $tax),
            conversions: $conversion ? [new CurrencyConversionData(
                currency: 'EUR',
                amount: number_format(1610 * $lines / 110.265, 2, '.', ''),
                rate: '110.265',
                date: CarbonImmutable::parse('2026-10-11'),
                source: 'Banco de Cabo Verde',
                sourceUrl: 'https://www.bcv.cv/taxas-de-cambio',
            )] : [],
        );
    }
}

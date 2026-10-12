<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

use Akira\PdfInvoices\DTO\CurrencyConversionData;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\FiscalData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\Enums\EmissionMode;
use Akira\PdfInvoices\Support\FiscalNumberFormatter;
use Akira\PdfInvoices\Support\InvoiceTranslator;
use Akira\PdfInvoices\Support\QrCodeImage;
use DateTimeInterface;

final readonly class FiscalLayout
{
    private const array LABELS = [
        'code', 'description', 'quantity', 'price', 'tax_column', 'total', 'carry_forward',
        'continuation', 'identifier', 'conversion', 'notes', 'verification', 'verification_text', 'platform', 'page',
    ];

    private FiscalNumberFormatter $formatter;

    public function __construct(
        private InvoiceTranslator $translator,
        private QrCodeImage $qrCode = new QrCodeImage(),
    ) {
        $this->formatter = new FiscalNumberFormatter();
    }

    public function build(InvoiceData $invoice): FiscalDocument
    {
        $fiscal = $invoice->fiscal ?? new FiscalData();
        $rows = new FiscalRowFactory($this->formatter, $invoice->currency, $fiscal->emissionMode)->rows($invoice->items);
        $summary = new FiscalSummaryBuilder(
            $this->totals($invoice),
            $this->conversion($invoice),
            array_values(array_filter([...$fiscal->notes, $invoice->notes])),
            $fiscal->qrPayload === null ? null : $this->qrCode->dataUri($fiscal->qrPayload),
        );

        return new FiscalDocument(
            title: $fiscal->documentTitle ?? $this->text('title'),
            subtitle: $this->text('subtitle'),
            brand: $invoice->seller->name,
            issuer: $this->party($this->text('issuer'), $invoice->seller),
            customer: $this->party($this->text('customer'), $invoice->buyer),
            facts: $this->facts($invoice, $fiscal),
            contingency: $this->contingency($fiscal),
            identifier: $fiscal->identifier,
            pages: new FiscalPaginator($this->formatter, $invoice->currency)->paginate($rows, $summary),
            labels: $this->labels(),
        );
    }

    private function party(string $title, EntityData $entity): FiscalParty
    {
        return new FiscalParty(
            title: $title,
            name: $this->filled($entity->name),
            taxId: $this->text('tax_id').': '.$this->filled($entity->vatNumber),
            address: $this->filled($entity->address),
            contact: $this->filled($entity->contact()),
        );
    }

    /**
     * @return array<int, FiscalLine>
     */
    private function facts(InvoiceData $invoice, FiscalData $fiscal): array
    {
        $number = $fiscal->number ?? $invoice->invoiceNumber;

        return [
            new FiscalLine($this->text('document_type'), $this->filled($fiscal->documentType)),
            new FiscalLine($this->text('series'), $this->filled($fiscal->series)),
            new FiscalLine($this->text('number'), $this->filled($number)),
            new FiscalLine($this->text('issued'), $this->filled($invoice->issuedAt?->format('d/m/Y H:i:s'))),
            new FiscalLine($this->text('mode'), $fiscal->emissionMode->label()),
        ];
    }

    private function contingency(FiscalData $fiscal): ?string
    {
        if (! $fiscal->emissionMode->isContingency()) {
            return null;
        }

        $off = $fiscal->emissionMode === EmissionMode::Off;
        $lines = [$this->text('contingency', ['mode' => $off ? 'OFF' : 'OFFLINE'])];

        if ($off && $fiscal->contingencyCode !== null) {
            $lines[] = 'IUC: '.$fiscal->contingencyCode;
        }

        $lines[] = $this->text('pending');

        return implode(' - ', $lines);
    }

    /**
     * @return array<int, FiscalLine>
     */
    private function totals(InvoiceData $invoice): array
    {
        $totals = $invoice->fiscalTotals();
        $amounts = [
            'subtotal' => $totals->net,
            'discounts' => $totals->discounts,
            'charges' => $totals->charges,
            'tax' => $totals->tax,
            'withholding' => $totals->withholding,
            'rounding' => $totals->rounding,
            'payable' => $totals->payable,
        ];

        $lines = [];

        foreach (array_filter($amounts, is_string(...)) as $key => $amount) {
            $lines[] = new FiscalLine($this->text($key), $this->formatter->money($amount, $invoice->currency));
        }

        return $lines;
    }

    /**
     * @return array<int, FiscalLine>
     */
    private function conversion(InvoiceData $invoice): array
    {
        return array_merge(...array_map(
            fn (CurrencyConversionData $conversion): array => $this->conversionLines($conversion, $invoice->currency),
            $invoice->conversions,
        ));
    }

    /**
     * @return array<int, FiscalLine>
     */
    private function conversionLines(CurrencyConversionData $conversion, string $currency): array
    {
        $lines = [
            new FiscalLine($this->text('original_amount'), $this->formatter->money($conversion->amount, $conversion->currency)),
            new FiscalLine($this->text('applied_rate'), $this->formatter->rate($conversion->rate, $conversion->currency, $currency)),
        ];

        if ($conversion->date instanceof DateTimeInterface) {
            $lines[] = new FiscalLine($this->text('rate_date'), $conversion->date->format('d/m/Y'));
        }

        if ($conversion->source !== null) {
            $lines[] = new FiscalLine($this->text('source'), $conversion->source);
        }

        if ($conversion->sourceUrl !== null) {
            $lines[] = new FiscalLine($this->text('source_url'), $conversion->sourceUrl);
        }

        return $lines;
    }

    /**
     * @return array<string, string>
     */
    private function labels(): array
    {
        $labels = [];

        foreach (self::LABELS as $key) {
            $labels[$key] = $this->text($key);
        }

        return $labels;
    }

    /**
     * @param  array<string, string>  $replace
     */
    private function text(string $key, array $replace = []): string
    {
        return $this->translator->translate('efatura.'.$key, $replace);
    }

    private function filled(?string $value): string
    {
        return $value === null || mb_trim($value) === '' ? '-' : $value;
    }
}

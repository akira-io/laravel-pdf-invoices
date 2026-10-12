<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Enums\EmissionMode;
use Akira\PdfInvoices\Support\FiscalNumberFormatter;

final readonly class FiscalRowFactory
{
    public function __construct(
        private FiscalNumberFormatter $formatter,
        private string $currency,
        private EmissionMode $mode,
    ) {}

    /**
     * @param  array<int, ItemData>  $items
     * @return array<int, FiscalRow>
     */
    public function rows(array $items): array
    {
        return array_values(array_map($this->row(...), $items));
    }

    private function row(ItemData $item): FiscalRow
    {
        $total = $item->exactTotal();

        return new FiscalRow(
            code: $this->mode === EmissionMode::Off ? '' : (string) $item->code,
            description: $item->description,
            quantity: mb_trim($this->formatter->quantity($item->exactQuantity()).' '.$item->unit),
            unitPrice: $this->formatter->unitPrice($item->exactUnitPrice(), $this->currency),
            tax: $this->tax($item),
            total: $this->formatter->number($total, $this->currency),
            amount: $total,
        );
    }

    private function tax(ItemData $item): string
    {
        if ($item->taxes === []) {
            return $item->tax > 0 ? $this->formatter->percentage(number_format($item->tax * 100, 5, '.', '')) : '-';
        }

        return implode(' + ', array_map($this->taxLabel(...), $item->taxes));
    }

    private function taxLabel(ItemTaxData $tax): string
    {
        return match (true) {
            $tax->rate !== null => $this->formatter->percentage($tax->rate),
            $tax->amount !== null => $this->formatter->number($tax->amount, $this->currency),
            default => (string) $tax->exemptionCode,
        };
    }
}

<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

use Akira\PdfInvoices\DTO\ItemData;
use Akira\PdfInvoices\DTO\ItemTaxData;
use Akira\PdfInvoices\Enums\EmissionMode;
use Akira\PdfInvoices\Support\FiscalNumberFormatter;
use Akira\PdfInvoices\Support\FiscalTextMeasure;

final readonly class FiscalRowFactory
{
    private const float TEXT_SIZE = 9.0;

    private const float DESCRIPTION_WIDTH = 128.0;

    private const float CODE_WIDTH = 62.0;

    public function __construct(
        private FiscalNumberFormatter $formatter,
        private string $currency,
        private EmissionMode $mode,
        private FiscalTextMeasure $measure = new FiscalTextMeasure(),
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
            code: $this->mode === EmissionMode::Off ? '' : $this->measure->limit((string) $item->code, self::TEXT_SIZE, self::CODE_WIDTH),
            description: $this->measure->limit($item->description, self::TEXT_SIZE, self::DESCRIPTION_WIDTH),
            quantity: mb_trim($this->formatter->quantity($item->exactQuantity()).' '.$item->unit),
            unitPrice: $this->formatter->unitPrice($item->exactUnitPrice(), $this->currency),
            taxes: $this->taxes($item),
            total: $this->formatter->number($total, $this->currency),
            amount: $total,
        );
    }

    /**
     * @return array<int, string>
     */
    private function taxes(ItemData $item): array
    {
        if ($item->taxes === []) {
            return [$item->tax > 0 ? $this->formatter->percentage(number_format($item->tax * 100, 5, '.', '')) : '-'];
        }

        return array_values(array_map($this->taxLabel(...), $item->taxes));
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

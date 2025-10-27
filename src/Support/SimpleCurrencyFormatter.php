<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;

final readonly class SimpleCurrencyFormatter implements CurrencyFormatterContract
{
    public function __construct(
        private string $symbol = '€',
    ) {}

    public function format(float $amount, string $currency = '', string $locale = 'en'): string
    {
        $formatted = number_format($amount, 2, '.', ',');
        $symbol = empty($currency) ? $this->symbol : $currency;

        return "{$symbol} {$formatted}";
    }
}
<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Illuminate\Support\Number;

final class LaravelCurrencyFormatter implements CurrencyFormatterContract
{
    public function format(float $amount, string $currency = '', string $locale = 'en'): string
    {
        if (empty($currency)) {
            return Number::format($amount, precision: 2, locale: $locale);
        }

        return Number::currency($amount, $currency, locale: $locale);
    }
}
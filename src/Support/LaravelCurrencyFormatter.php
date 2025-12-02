<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Illuminate\Support\Number;

final class LaravelCurrencyFormatter implements CurrencyFormatterContract
{
    public function format(float $amount, string $currency = '', string $locale = 'en'): string
    {
        if ($currency === '' || $currency === '0') {
            $result = Number::format($amount, precision: 2, locale: $locale);
        } else {
            $result = Number::currency($amount, $currency, locale: $locale);
        }

        if (!is_string($result)) {
            return (string) $amount;
        }

        return $result;
    }
}

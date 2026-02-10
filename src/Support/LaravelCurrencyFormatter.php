<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Illuminate\Support\Number;
use Throwable;

final class LaravelCurrencyFormatter implements CurrencyFormatterContract
{
    public function format(float $amount, string $currency = '', string $locale = 'en'): string
    {
        try {
            if ($currency === '' || $currency === '0') {
                return Number::format($amount, precision: 2, locale: $locale);
            }

            return Number::currency($amount, $currency, locale: $locale);
        } catch (Throwable) {
            return (string) $amount;
        }
    }
}

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
            $result = ($currency === '' || $currency === '0')
                ? Number::format($amount, precision: 2, locale: $locale)
                : Number::currency($amount, $currency, locale: $locale);

            return is_string($result) ? $result : (string) $amount;
        } catch (Throwable) {
            return (string) $amount;
        }
    }
}

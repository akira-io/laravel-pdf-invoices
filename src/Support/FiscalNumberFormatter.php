<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

final readonly class FiscalNumberFormatter
{
    private const string ESCUDO = 'CVE';

    private const int AMOUNT_SCALE = 2;

    private const int UNIT_PRICE_SCALE = 5;

    private const int RATE_SCALE = 5;

    public function money(string $value, string $currency): string
    {
        if ($currency === self::ESCUDO) {
            return $this->number($value, $currency);
        }

        return $this->grouped(Decimal::trim($value, self::AMOUNT_SCALE), ',').' '.$currency;
    }

    public function number(string $value, string $currency): string
    {
        return $this->grouped(Decimal::round($value, self::AMOUNT_SCALE), $this->separator($currency));
    }

    public function unitPrice(string $value, string $currency): string
    {
        $rounded = Decimal::round($value, self::UNIT_PRICE_SCALE);

        return $this->grouped(Decimal::trim($rounded, self::AMOUNT_SCALE), $this->separator($currency));
    }

    public function quantity(string $value): string
    {
        return str_replace('.', ',', Decimal::trim($value, 0));
    }

    public function percentage(string $value): string
    {
        return $this->quantity($value).'%';
    }

    public function rate(string $rate, string $from, string $to): string
    {
        return "1 {$from} = ".$this->grouped(Decimal::round($rate, self::RATE_SCALE), ',')." {$to}";
    }

    private function separator(string $currency): string
    {
        return $currency === self::ESCUDO ? '$' : ',';
    }

    private function grouped(string $value, string $separator): string
    {
        $negative = str_starts_with($value, '-');
        $parts = explode('.', mb_ltrim($value, '-'), 2);
        $integer = $parts[0];
        $fraction = $parts[1] ?? null;
        $groups = mb_str_split(strrev($integer), 3);
        $grouped = strrev(implode(' ', $groups));

        return ($negative ? '-' : '').$grouped.($fraction === null ? '' : $separator.$fraction);
    }
}

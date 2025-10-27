<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Contracts;

interface CurrencyFormatterContract
{
    public function format(float $amount, string $currency = '', string $locale = 'en'): string;
}
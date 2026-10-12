<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

use Akira\PdfInvoices\Support\Decimal;
use DateTimeInterface;

final readonly class CurrencyConversionData
{
    public function __construct(
        public string $currency,
        public string $amount,
        public string $rate,
        public ?DateTimeInterface $date = null,
        public ?string $source = null,
        public ?string $sourceUrl = null,
    ) {
        Decimal::assert($amount, 'converted amount');
        Decimal::assert($rate, 'exchange rate');
    }
}

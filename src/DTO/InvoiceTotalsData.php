<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

use Akira\PdfInvoices\Support\Decimal;

final readonly class InvoiceTotalsData
{
    public function __construct(
        public string $net,
        public string $payable,
        public string $discounts = '0',
        public string $charges = '0',
        public string $tax = '0',
        public ?string $withholding = null,
        public ?string $rounding = null,
    ) {
        Decimal::assert($net, 'net total');
        Decimal::assert($payable, 'payable total');
        Decimal::assert($discounts, 'discount total');
        Decimal::assert($charges, 'charge total');
        Decimal::assert($tax, 'tax total');
        Decimal::assertOptional($withholding, 'withholding total');
        Decimal::assertOptional($rounding, 'rounding amount');
    }
}

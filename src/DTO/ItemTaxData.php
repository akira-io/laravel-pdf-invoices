<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

use Akira\PdfInvoices\Support\Decimal;
use InvalidArgumentException;

final readonly class ItemTaxData
{
    public function __construct(
        public string $code = 'IVA',
        public ?string $rate = null,
        public ?string $amount = null,
        public ?string $exemptionCode = null,
    ) {
        throw_if(
            $rate === null && $amount === null && $exemptionCode === null,
            InvalidArgumentException::class,
            'An item tax needs a rate, an amount or an exemption code.',
        );

        Decimal::assertOptional($rate, 'tax rate');
        Decimal::assertOptional($amount, 'tax amount');
    }
}

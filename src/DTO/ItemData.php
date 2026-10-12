<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

use Akira\PdfInvoices\Support\Decimal;

final readonly class ItemData
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int, ItemTaxData>  $taxes
     */
    public function __construct(
        public string $description,
        public float $unitPrice,
        public int $quantity = 1,
        public float $tax = 0.0,
        public float $discount = 0.0,
        public array $attributes = [],
        public ?string $code = null,
        public ?string $unit = null,
        public ?string $decimalQuantity = null,
        public ?string $decimalUnitPrice = null,
        public ?string $decimalTotal = null,
        public array $taxes = [],
    ) {
        Decimal::assertOptional($decimalQuantity, 'item quantity');
        Decimal::assertOptional($decimalUnitPrice, 'item unit price');
        Decimal::assertOptional($decimalTotal, 'item total');
    }

    public function getSubtotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }

    public function getDiscountAmount(): float
    {
        return $this->getSubtotal() * $this->discount;
    }

    public function getSubtotalAfterDiscount(): float
    {
        return $this->getSubtotal() - $this->getDiscountAmount();
    }

    public function getTaxAmount(): float
    {
        return $this->getSubtotalAfterDiscount() * $this->tax;
    }

    public function getTotal(): float
    {
        return $this->getSubtotalAfterDiscount() + $this->getTaxAmount();
    }

    public function exactQuantity(): string
    {
        return $this->decimalQuantity ?? (string) $this->quantity;
    }

    public function exactUnitPrice(): string
    {
        return $this->decimalUnitPrice ?? Decimal::trim(number_format($this->unitPrice, 5, '.', ''), 2);
    }

    public function exactTotal(): string
    {
        return $this->decimalTotal ?? number_format($this->getTotal(), 2, '.', '');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
}

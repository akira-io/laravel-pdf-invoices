<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

final readonly class ItemData
{
    /**
     * @param  string  $description
     * @param  float  $unitPrice
     * @param  int  $quantity
     * @param  float  $tax
     * @param  float  $discount
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public string $description,
        public float $unitPrice,
        public int $quantity = 1,
        public float $tax = 0.0,
        public float $discount = 0.0,
        public array $attributes = [],
    ) {}

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
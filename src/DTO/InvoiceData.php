<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

use Carbon\CarbonInterface;
use DateTimeInterface;

final readonly class InvoiceData
{
    /**
     * @param  array<int, ItemData>  $items
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public EntityData $seller,
        public EntityData $buyer,
        public array $items = [],
        public CarbonInterface|DateTimeInterface|null $issuedAt = null,
        public CarbonInterface|DateTimeInterface|null $dueAt = null,
        public string $invoiceNumber = '',
        public string $currency = 'EUR',
        public ?string $locale = null,
        public ?string $notes = null,
        public array $attributes = [],
    ) {}

    public function getSubtotal(): float
    {
        return array_sum(array_map(
            static fn (ItemData $item): float => $item->getSubtotal(),
            $this->items
        )) ?: 0.0;
    }

    public function getTotalDiscount(): float
    {
        return array_sum(array_map(
            static fn (ItemData $item): float => $item->getDiscountAmount(),
            $this->items
        )) ?: 0.0;
    }

    public function getSubtotalAfterDiscount(): float
    {
        return array_sum(array_map(
            static fn (ItemData $item): float => $item->getSubtotalAfterDiscount(),
            $this->items
        )) ?: 0.0;
    }

    public function getTotalTax(): float
    {
        return array_sum(array_map(
            static fn (ItemData $item): float => $item->getTaxAmount(),
            $this->items
        )) ?: 0.0;
    }

    public function getTotal(): float
    {
        return array_sum(array_map(
            static fn (ItemData $item): float => $item->getTotal(),
            $this->items
        )) ?: 0.0;
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

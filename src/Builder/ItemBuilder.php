<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Builder;

use Akira\PdfInvoices\Contracts\BuilderContract;
use Akira\PdfInvoices\DTO\ItemData;

final class ItemBuilder implements BuilderContract
{
    private string $description = '';

    private float $unitPrice = 0.0;

    private int $quantity = 1;

    private float $tax = 0.0;

    private float $discount = 0.0;

    /** @var array<string, mixed> */
    private array $attributes = [];

    public static function make(): static
    {
        return new self();
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function unitPrice(float $price): static
    {
        $this->unitPrice = $price;

        return $this;
    }

    public function quantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function tax(float $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    public function discount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function set(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function withAttributes(array $data): static
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function build(): ItemData
    {
        return new ItemData(
            description: $this->description,
            unitPrice: $this->unitPrice,
            quantity: $this->quantity,
            tax: $this->tax,
            discount: $this->discount,
            attributes: $this->attributes,
        );
    }
}

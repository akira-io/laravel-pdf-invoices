<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Builder;

use Akira\PdfInvoices\Contracts\BuilderContract;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\ItemData;
use DateTime;

final class InvoiceBuilder implements BuilderContract
{
    private ?EntityData $seller = null;

    private ?EntityData $buyer = null;

    /** @var array<int, ItemData> */
    private array $items = [];

    private ?DateTime $issuedAt = null;

    private ?DateTime $dueAt = null;

    private string $invoiceNumber = '';

    private string $currency = 'EUR';

    private ?string $notes = null;

    /** @var array<string, mixed> */
    private array $attributes = [];

    public static function make(): static
    {
        return new static();
    }

    public function seller(EntityData $seller): static
    {
        $this->seller = $seller;

        return $this;
    }

    public function buyer(EntityData $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function addItem(ItemData $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param  array<int, ItemData>  $items
     */
    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function issuedAt(DateTime $date): static
    {
        $this->issuedAt = $date;

        return $this;
    }

    public function dueAt(DateTime $date): static
    {
        $this->dueAt = $date;

        return $this;
    }

    public function invoiceNumber(string $number): static
    {
        $this->invoiceNumber = $number;

        return $this;
    }

    public function currency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function notes(string $notes): static
    {
        $this->notes = $notes;

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

    public function build(): InvoiceData
    {
        if ($this->seller === null) {
            throw new \InvalidArgumentException('Seller is required.');
        }

        if ($this->buyer === null) {
            throw new \InvalidArgumentException('Buyer is required.');
        }

        return new InvoiceData(
            seller: $this->seller,
            buyer: $this->buyer,
            items: $this->items,
            issuedAt: $this->issuedAt,
            dueAt: $this->dueAt,
            invoiceNumber: $this->invoiceNumber,
            currency: $this->currency,
            notes: $this->notes,
            attributes: $this->attributes,
        );
    }
}
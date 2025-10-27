<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Builder;

use Akira\PdfInvoices\Contracts\BuilderContract;
use Akira\PdfInvoices\DTO\EntityData;

final class EntityBuilder implements BuilderContract
{
    private string $name = '';

    private ?string $address = null;

    private ?string $vatNumber = null;

    private ?string $logoUrl = null;

    private ?string $email = null;

    /** @var array<string, mixed> */
    private array $attributes = [];

    public static function make(): static
    {
        return new static();
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function address(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function vat(string $vatNumber): static
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    public function logo(string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function email(string $email): static
    {
        $this->email = $email;

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

    public function build(): EntityData
    {
        return new EntityData(
            name: $this->name,
            address: $this->address,
            vatNumber: $this->vatNumber,
            logoUrl: $this->logoUrl,
            email: $this->email,
            attributes: $this->attributes,
        );
    }
}
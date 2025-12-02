<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\DTO;

final readonly class EntityData
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public string $name,
        public ?string $address = null,
        public ?string $vatNumber = null,
        public ?string $logoUrl = null,
        public ?string $email = null,
        public array $attributes = [],
    ) {}

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

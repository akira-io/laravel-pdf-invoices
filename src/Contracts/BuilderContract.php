<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Contracts;

interface BuilderContract
{
    public static function make(): static;

    public function withAttributes(array $data): static;

    public function set(string $key, mixed $value): static;

    public function build(): mixed;
}

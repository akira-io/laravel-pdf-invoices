<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Contracts;

interface StorageDriverContract
{
    public function save(string $path, string $content): string;

    public function exists(string $path): bool;

    public function get(string $path): string;

    public function delete(string $path): bool;
}

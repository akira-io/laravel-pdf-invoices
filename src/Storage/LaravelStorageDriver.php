<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Storage;

use Akira\PdfInvoices\Contracts\StorageDriverContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use RuntimeException;

final readonly class LaravelStorageDriver implements StorageDriverContract
{
    public function __construct(
        private Filesystem $disk,
    ) {}

    public function save(string $path, string $content): string
    {
        $this->disk->put($path, $content);

        return $path;
    }

    public function exists(string $path): bool
    {
        return $this->disk->exists($path);
    }

    public function get(string $path): string
    {
        $content = $this->disk->get($path);
        throw_if($content === null, RuntimeException::class, "File not found at path: {$path}");

        return $content;
    }

    public function delete(string $path): bool
    {
        return $this->disk->delete($path);
    }
}

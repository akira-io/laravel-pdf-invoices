<?php

declare(strict_types=1);

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\Contracts\StorageDriverContract;

describe('Service Provider Bindings', function (): void {
    it('binds currency formatter contract', function (): void {
        $formatter = resolve(CurrencyFormatterContract::class);

        expect($formatter)->toBeInstanceOf(CurrencyFormatterContract::class);
    });

    it('binds PDF generator contract', function (): void {
        $generator = resolve(PdfGeneratorContract::class);

        expect($generator)->toBeInstanceOf(PdfGeneratorContract::class);
    });

    it('binds storage driver contract', function (): void {
        $storage = resolve(StorageDriverContract::class);

        expect($storage)->toBeInstanceOf(StorageDriverContract::class);
    });

    it('provides singleton instances', function (): void {
        $formatter1 = resolve(CurrencyFormatterContract::class);
        $formatter2 = resolve(CurrencyFormatterContract::class);

        expect($formatter1)->toBe($formatter2);
    });
});

<?php

declare(strict_types=1);

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\Contracts\StorageDriverContract;

describe('Service Provider Bindings', function () {
    it('binds currency formatter contract', function () {
        $formatter = app(CurrencyFormatterContract::class);

        expect($formatter)->toBeInstanceOf(CurrencyFormatterContract::class);
    });

    it('binds PDF generator contract', function () {
        $generator = app(PdfGeneratorContract::class);

        expect($generator)->toBeInstanceOf(PdfGeneratorContract::class);
    });

    it('binds storage driver contract', function () {
        $storage = app(StorageDriverContract::class);

        expect($storage)->toBeInstanceOf(StorageDriverContract::class);
    });

    it('provides singleton instances', function () {
        $formatter1 = app(CurrencyFormatterContract::class);
        $formatter2 = app(CurrencyFormatterContract::class);

        expect($formatter1)->toBe($formatter2);
    });
});
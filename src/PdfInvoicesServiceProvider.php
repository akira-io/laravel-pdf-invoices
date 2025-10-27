<?php

declare(strict_types=1);

namespace Akira\PdfInvoices;

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\Contracts\StorageDriverContract;
use Akira\PdfInvoices\Pdf\SpatiePdfGenerator;
use Akira\PdfInvoices\Storage\LaravelStorageDriver;
use Akira\PdfInvoices\Support\LaravelCurrencyFormatter;
use Illuminate\Contracts\Filesystem\Filesystem;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class PdfInvoicesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-pdf-invoices')
            ->hasConfigFile('pdf-invoices')
            ->hasViews('pdf-invoices');
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(CurrencyFormatterContract::class, function (mixed $app): CurrencyFormatterContract {
            $driver = config('pdf-invoices.currency.driver', LaravelCurrencyFormatter::class);

            return $app->make($driver);
        });

        $this->app->singleton(PdfGeneratorContract::class, function (mixed $app): PdfGeneratorContract {
            $basePath = config('pdf-invoices.pdf.base_path', 'invoices');

            return new SpatiePdfGenerator($basePath);
        });

        $this->app->singleton(StorageDriverContract::class, function (mixed $app): StorageDriverContract {
            $disk = config('pdf-invoices.storage.disk', 'local');

            return new LaravelStorageDriver($app['filesystem']->disk($disk));
        });
    }
}

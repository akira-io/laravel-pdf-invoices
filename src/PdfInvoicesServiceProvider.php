<?php

declare(strict_types=1);

namespace Akira\PdfInvoices;

use Akira\PdfInvoices\Contracts\CurrencyFormatterContract;
use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\Contracts\StorageDriverContract;
use Akira\PdfInvoices\Pdf\DompdfPdfGenerator;
use Akira\PdfInvoices\Pdf\SpatiePdfGenerator;
use Akira\PdfInvoices\Storage\LaravelStorageDriver;
use Akira\PdfInvoices\Support\LaravelCurrencyFormatter;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class PdfInvoicesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-pdf-invoices')
            ->hasConfigFile('pdf-invoices')
            ->hasViews('pdf-invoices')
            ->hasTranslations();
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(function (mixed $app): CurrencyFormatterContract {
            $driver = config('pdf-invoices.currency.driver', LaravelCurrencyFormatter::class);

            return $app->make($driver);
        });

        $this->app->singleton(function (mixed $app): PdfGeneratorContract {
            $driver = config('pdf-invoices.pdf.driver', 'spatie');
            $basePath = config('pdf-invoices.pdf.base_path', 'invoices');

            return match ($driver) {
                'dompdf' => new DompdfPdfGenerator($basePath),
                default => new SpatiePdfGenerator($basePath),
            };
        });

        $this->app->singleton(function (mixed $app): StorageDriverContract {
            $disk = config('pdf-invoices.storage.disk', 'local');

            return new LaravelStorageDriver($app['filesystem']->disk($disk));
        });
    }
}

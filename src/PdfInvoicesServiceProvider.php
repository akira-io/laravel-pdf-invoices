<?php

declare(strict_types=1);

namespace Akira\PdfInvoices;

use Akira\PdfInvoices\Config\ConfigManager;
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
        $this->app->singleton(ConfigManager::class);

        $this->app->singleton(CurrencyFormatterContract::class, function (): CurrencyFormatterContract {
            $configManager = $this->app->make(ConfigManager::class);
            $driver = $configManager->currencyDriver();

            return $this->app->make($driver);
        });

        $this->app->singleton(PdfGeneratorContract::class, function (): PdfGeneratorContract {
            $configManager = $this->app->make(ConfigManager::class);
            $driver = $configManager->pdfDriver();
            $basePath = $configManager->pdfBasePath();

            return match ($driver) {
                'dompdf' => new DompdfPdfGenerator($basePath),
                default => new SpatiePdfGenerator($basePath),
            };
        });

        $this->app->singleton(StorageDriverContract::class, function (): StorageDriverContract {
            $configManager = $this->app->make(ConfigManager::class);
            $disk = $configManager->storageDisk();

            return new LaravelStorageDriver($this->app['filesystem']->disk($disk));
        });
    }
}

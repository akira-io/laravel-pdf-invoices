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
use InvalidArgumentException;
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
            ->hasTranslations()
            ->hasCommand(Commands\PdfInvoicesCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(ConfigManager::class);

        $this->app->singleton(
            function (): CurrencyFormatterContract {
                $configManager = $this->app->make(ConfigManager::class);
                $driver = $configManager->currencyDriver();

                $instance = $this->app->make($driver);
                throw_unless($instance instanceof CurrencyFormatterContract, InvalidArgumentException::class, "Currency driver {$driver} must implement CurrencyFormatterContract");

                return $instance;
            }
        );

        $this->app->singleton(
            function (): PdfGeneratorContract {
                $configManager = $this->app->make(ConfigManager::class);
                $driver = $configManager->pdfDriver();
                $basePath = $configManager->pdfBasePath();

                return match ($driver) {
                    'dompdf' => new DompdfPdfGenerator($basePath),
                    default => new SpatiePdfGenerator($basePath),
                };
            }
        );

        $this->app->singleton(
            function (): StorageDriverContract {
                $configManager = $this->app->make(ConfigManager::class);
                $disk = $configManager->storageDisk();
                $filesystem = $this->app->make(\Illuminate\Contracts\Filesystem\Factory::class);

                return new LaravelStorageDriver($filesystem->disk($disk));
            }
        );
    }
}

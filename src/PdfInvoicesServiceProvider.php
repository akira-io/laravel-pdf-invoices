<?php

namespace Akira\PdfInvoices;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Akira\PdfInvoices\Commands\PdfInvoicesCommand;

class PdfInvoicesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pdf-invoices')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_pdf_invoices_table')
            ->hasCommand(PdfInvoicesCommand::class);
    }
}

<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Tests;

use Akira\Debugger\DebuggerServiceProvider;
use Akira\PdfInvoices\PdfInvoicesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        //        Factory::guessFactoryNamesUsing(
        //            fn (string $modelName) => 'Akira\\PdfInvoices\\Database\\Factories\\'.class_basename($modelName).'Factory'
        //        );
    }

    final public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }

    protected function getPackageProviders($app): array
    {
        return [
            PdfInvoicesServiceProvider::class,
            DebuggerServiceProvider::class,
        ];
    }
}

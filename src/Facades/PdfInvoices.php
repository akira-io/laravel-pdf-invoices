<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Akira\PdfInvoices\PdfInvoices
 */
final class PdfInvoices extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Akira\PdfInvoices\PdfInvoices::class;
    }
}

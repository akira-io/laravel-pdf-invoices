<?php

declare(strict_types=1);

use Akira\PdfInvoices\Facades\PdfInvoices;
use Akira\PdfInvoices\PdfInvoices as PdfInvoicesService;

it('proxies to the service', function (): void {
    expect(PdfInvoices::getFacadeRoot())->toBeInstanceOf(PdfInvoicesService::class);
});

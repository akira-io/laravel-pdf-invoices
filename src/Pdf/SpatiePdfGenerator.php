<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Pdf;

use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\Support\InvoiceTranslator;
use Spatie\LaravelPdf\Facades\Pdf;

final readonly class SpatiePdfGenerator implements PdfGeneratorContract
{
    public function __construct(
        private string $basePath = 'invoices',
    ) {}

    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_').'.pdf';
        $compiledCss = $this->getCompiledCss();
        $locale = $invoice->locale ?? config('pdf-invoices.localization.locale', 'en');
        if (! is_string($locale)) {
            $locale = 'en';
        }
        $translator = new InvoiceTranslator($locale);

        $this->saveWithBrowsershotDriver(
            $template,
            [
                'invoice' => $invoice,
                'compiledCss' => $compiledCss,
                'translator' => $translator,
            ],
            $tempFile,
        );

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return is_string($content) ? $content : '';
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        $fullPath = $this->basePath.'/'.$path;
        $compiledCss = $this->getCompiledCss();
        $locale = $invoice->locale ?? config('pdf-invoices.localization.locale', 'en');
        if (! is_string($locale)) {
            $locale = 'en';
        }
        $translator = new InvoiceTranslator($locale);

        $this->saveWithBrowsershotDriver(
            $template,
            [
                'invoice' => $invoice,
                'compiledCss' => $compiledCss,
                'translator' => $translator,
            ],
            $fullPath,
        );

        return $fullPath;
    }

    /**
     * Get compiled CSS content.
     */
    private function getCompiledCss(): string
    {
        $cssPath = __DIR__.'/../../resources/css/compiled.css';

        if (! file_exists($cssPath)) {
            return '';
        }

        $content = file_get_contents($cssPath);

        return is_string($content) ? $content : '';
    }

    /**
     * @param  array{invoice: InvoiceData, compiledCss: string, translator: InvoiceTranslator}  $data
     */
    private function saveWithBrowsershotDriver(string $template, array $data, string $path): void
    {
        $viewPath = "pdf-invoices::pdf.templates.{$template}";
        $previousDriver = config('laravel-pdf.driver');

        config(['laravel-pdf.driver' => 'browsershot']);

        try {
            Pdf::view($viewPath, $data)->save($path);
        } finally {
            config(['laravel-pdf.driver' => $previousDriver]);
        }
    }
}

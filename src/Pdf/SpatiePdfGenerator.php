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
        $data = $this->buildViewData($invoice);

        $this->saveWithBrowsershotDriver(
            $template,
            $data,
            $tempFile,
        );

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return is_string($content) ? $content : '';
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        $fullPath = $this->basePath.'/'.$path;
        $data = $this->buildViewData($invoice);

        $this->saveWithBrowsershotDriver(
            $template,
            $data,
            $fullPath,
        );

        return $fullPath;
    }

    /**
     * @return array{invoice: InvoiceData, compiledCss: string, translator: InvoiceTranslator}
     */
    private function buildViewData(InvoiceData $invoice): array
    {
        $locale = $invoice->locale ?? config('pdf-invoices.localization.locale', 'en');
        if (! is_string($locale)) {
            $locale = 'en';
        }

        return [
            'invoice' => $invoice,
            'compiledCss' => $this->getCompiledCss(),
            'translator' => new InvoiceTranslator($locale),
        ];
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

        Pdf::view($viewPath, $data)
            ->driver('browsershot')
            ->save($path);
    }
}

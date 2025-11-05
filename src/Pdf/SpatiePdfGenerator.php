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
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
        $compiledCss = $this->getCompiledCss();
        $locale = config('pdf-invoices.localization.locale', 'en');
        $translator = new InvoiceTranslator($locale);

        Pdf::view("pdf-invoices::pdf.templates.{$template}", [
            'invoice' => $invoice,
            'compiledCss' => $compiledCss,
            'translator' => $translator,
        ])->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        $fullPath = $this->basePath . '/' . $path;
        $compiledCss = $this->getCompiledCss();
        $locale = config('pdf-invoices.localization.locale', 'en');
        $translator = new InvoiceTranslator($locale);

        Pdf::view("pdf-invoices::pdf.templates.{$template}", [
            'invoice' => $invoice,
            'compiledCss' => $compiledCss,
            'translator' => $translator,
        ])->save($fullPath);

        return $fullPath;
    }

    /**
     * Get compiled CSS content from parent app's Vite build.
     *
     * @return string
     */
    private function getCompiledCss(): string
    {
        // Try to get CSS from parent app's Vite manifest
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            try {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                if (isset($manifest['resources/css/app.css']['file'])) {
                    $cssFile = public_path('build/' . $manifest['resources/css/app.css']['file']);
                    if (file_exists($cssFile)) {
                        return file_get_contents($cssFile);
                    }
                }
            } catch (\Exception) {
                // Fallback to alternative approach
            }
        }

        // Fallback: try to read CSS from resources
        $cssPath = __DIR__ . '/../../resources/css/compiled.css';
        if (file_exists($cssPath)) {
            return file_get_contents($cssPath);
        }

        return '';
    }
}
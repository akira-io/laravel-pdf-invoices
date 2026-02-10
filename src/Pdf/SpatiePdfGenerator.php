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
        private ?string $cssPath = null,
    ) {}

    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        $viewPath = "pdf-invoices::pdf.templates.{$template}";
        $data = $this->buildViewData($invoice);

        $builder = Pdf::view($viewPath, $data)
            ->driver('browsershot');

        if ($builder instanceof \Spatie\LaravelPdf\FakePdfBuilder) {
            return 'fake-pdf-content';
        }

        return base64_decode($builder->base64());
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
        $cssPath = $this->cssPath ?? __DIR__.'/../../resources/css/compiled.css';

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

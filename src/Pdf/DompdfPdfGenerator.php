<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Pdf;

use Akira\PdfInvoices\Contracts\PdfGeneratorContract;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\Support\InvoiceTranslator;
use Barryvdh\DomPDF\Facade\Pdf;

final readonly class DompdfPdfGenerator implements PdfGeneratorContract
{
    public function __construct(
        private string $basePath = 'invoices',
    ) {}

    public function generate(InvoiceData $invoice, string $template = 'modern'): string
    {
        $compiledCss = $this->getCompiledCss();
        $locale = config('pdf-invoices.localization.locale', 'en');
        if (! is_string($locale)) {
            $locale = 'en';
        }
        $translator = new InvoiceTranslator($locale);

        $viewPath = "pdf-invoices::pdf.templates.{$template}";
        /** @var string $html */
        $html = view(/** @phpstan-ignore-line */ $viewPath, [
            'invoice' => $invoice,
            'compiledCss' => $compiledCss,
            'translator' => $translator,
        ])->render();

        return Pdf::loadHTML($html)
            ->output();
    }

    public function save(InvoiceData $invoice, string $path, string $template = 'modern'): string
    {
        $fullPath = $this->basePath.'/'.$path;
        $compiledCss = $this->getCompiledCss();
        $locale = config('pdf-invoices.localization.locale', 'en');
        if (! is_string($locale)) {
            $locale = 'en';
        }
        $translator = new InvoiceTranslator($locale);

        $viewPath = "pdf-invoices::pdf.templates.{$template}";
        /** @var string $html */
        $html = view(/** @phpstan-ignore-line */ $viewPath, [
            'invoice' => $invoice,
            'compiledCss' => $compiledCss,
            'translator' => $translator,
        ])->render();

        Pdf::loadHTML($html)
            ->save($fullPath);

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
}

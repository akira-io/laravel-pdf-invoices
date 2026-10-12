<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalDocument
{
    /**
     * @param  array<int, FiscalLine>  $facts
     * @param  array<int, FiscalPage>  $pages
     * @param  array<string, string>  $labels
     */
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $brand,
        public FiscalParty $issuer,
        public FiscalParty $customer,
        public array $facts,
        public ?string $contingency,
        public ?string $identifier,
        public array $pages,
        public array $labels,
    ) {}

    public function label(string $key): string
    {
        return $this->labels[$key] ?? $key;
    }

    public function pageLabel(FiscalPage $page): string
    {
        return strtr($this->label('page'), [':current' => $page->number, ':total' => count($this->pages)]);
    }
}

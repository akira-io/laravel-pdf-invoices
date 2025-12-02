<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

/**
 * Helper class for translating invoice labels and strings.
 *
 * Provides methods to access translated invoice-related strings based on locale.
 */
final readonly class InvoiceTranslator
{
    /**
     * @param  string  $locale  The locale to use for translations (e.g., 'en', 'pt')
     */
    public function __construct(
        private string $locale = 'en',
    ) {}

    /**
     * Alias for translate() method.
     *
     * @param  string  $key  The translation key
     * @param  array<string, mixed>  $replace  Replacements for the translation
     * @return string The translated string
     */
    public function __(string $key, array $replace = []): string
    {
        return $this->translate($key, $replace);
    }

    /**
     * Translate an invoice key.
     *
     * @param  string  $key  The translation key (e.g., 'invoice', 'subtotal')
     * @param  array<string, mixed>  $replace  Replacements for the translation
     * @return string The translated string
     */
    public function translate(string $key, array $replace = []): string
    {
        /** @var array<string, bool|float|int|string|null> $transReplace */
        $transReplace = [];
        foreach ($replace as $k => $v) {
            if (is_scalar($v) || $v === null) {
                $transReplace[$k] = $v;
            } else {
                $transReplace[$k] = (string) $v;
            }
        }

        return trans("pdf-invoices::invoice.{$key}", $transReplace, $this->locale);
    }

    /**
     * Get the current locale.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Create a new instance with a different locale.
     */
    public function withLocale(string $locale): self
    {
        return new self($locale);
    }
}

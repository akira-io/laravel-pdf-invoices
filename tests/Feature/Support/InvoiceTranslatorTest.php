<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Tests\Feature\Support;

use Akira\PdfInvoices\Support\InvoiceTranslator;

it('translates a key', function (): void {
    $translator = new InvoiceTranslator('en');

    // Assuming the translation exists or we are testing the fallback/formatting
    $result = $translator->translate('invoice');
    expect($result)->toBeString();
});

it('translates with replacements', function (): void {
    $translator = new InvoiceTranslator('en');

    $result = $translator->translate('invoice', ['name' => 'John']);
    expect($result)->toBeString();
});

it('handles non-scalar replacements', function (): void {
    $translator = new InvoiceTranslator('en');

    $object = new class
    {
        public function __toString(): string
        {
            return 'ObjectString';
        }
    };

    $result = $translator->translate('invoice', ['data' => $object, 'other' => []]);
    expect($result)->toBeString();
});

it('can get and change locale', function (): void {
    $translator = new InvoiceTranslator('en');
    expect($translator->getLocale())->toBe('en');

    $newTranslator = $translator->withLocale('pt');
    expect($newTranslator->getLocale())->toBe('pt');
    expect($translator->getLocale())->toBe('en');
});

it('has an alias for translate', function (): void {
    $translator = new InvoiceTranslator('en');
    expect($translator->__('invoice'))->toBe($translator->translate('invoice'));
});

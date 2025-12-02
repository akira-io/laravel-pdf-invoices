<?php

declare(strict_types=1);

use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;

describe('Invoice Calculations', function (): void {
    it('calculates subtotal correctly', function (): void {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem(ItemBuilder::make()->description('Item')->unitPrice(100.0)->quantity(2)->build())
            ->addItem(ItemBuilder::make()->description('Item')->unitPrice(50.0)->quantity(1)->build())
            ->build();

        expect($invoice->getSubtotal())->toBe(250.0);
    });

    it('calculates discount correctly', function (): void {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem(ItemBuilder::make()
                ->description('Item')
                ->unitPrice(100.0)
                ->quantity(1)
                ->discount(0.10)
                ->build())
            ->build();

        expect($invoice->getTotalDiscount())->toBe(10.0);
    });

    it('calculates tax correctly', function (): void {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem(ItemBuilder::make()
                ->description('Item')
                ->unitPrice(100.0)
                ->quantity(1)
                ->tax(0.20)
                ->build())
            ->build();

        expect($invoice->getTotalTax())->toBe(20.0);
    });

    it('calculates complex invoice totals', function (): void {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem(ItemBuilder::make()
                ->description('Item 1')
                ->unitPrice(100.0)
                ->quantity(2)
                ->tax(0.20)
                ->discount(0.10)
                ->build())
            ->addItem(ItemBuilder::make()
                ->description('Item 2')
                ->unitPrice(50.0)
                ->quantity(3)
                ->tax(0.20)
                ->build())
            ->build();

        expect($invoice->getSubtotal())->toBe(350.0)
            ->and($invoice->getTotalDiscount())->toBe(20.0)
            ->and($invoice->getSubtotalAfterDiscount())->toBe(330.0)
            ->and($invoice->getTotalTax())->toBe(66.0)
            ->and($invoice->getTotal())->toBe(396.0);
    });

    it('handles empty item list', function (): void {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->build();

        expect($invoice->getSubtotal())->toBe(0.0)
            ->and($invoice->getTotalTax())->toBe(0.0)
            ->and($invoice->getTotalDiscount())->toBe(0.0)
            ->and($invoice->getTotal())->toBe(0.0);
    });
});

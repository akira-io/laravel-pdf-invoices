<?php

declare(strict_types=1);

use Akira\PdfInvoices\Builder\EntityBuilder;
use Akira\PdfInvoices\Builder\InvoiceBuilder;
use Akira\PdfInvoices\Builder\ItemBuilder;
use Akira\PdfInvoices\DTO\EntityData;
use Akira\PdfInvoices\DTO\InvoiceData;
use Akira\PdfInvoices\DTO\ItemData;

describe('EntityBuilder', function () {
    it('creates an entity with required data', function () {
        $entity = EntityBuilder::make()
            ->name('Acme Corp')
            ->build();

        expect($entity)
            ->toBeInstanceOf(EntityData::class)
            ->name->toBe('Acme Corp')
            ->address->toBeNull();
    });

    it('creates an entity with all data', function () {
        $entity = EntityBuilder::make()
            ->name('Acme Corp')
            ->address('123 Main St')
            ->vat('US123456789')
            ->email('contact@acme.com')
            ->logo('https://example.com/logo.png')
            ->build();

        expect($entity)
            ->name->toBe('Acme Corp')
            ->address->toBe('123 Main St')
            ->vatNumber->toBe('US123456789')
            ->email->toBe('contact@acme.com')
            ->logoUrl->toBe('https://example.com/logo.png');
    });

    it('supports custom attributes', function () {
        $entity = EntityBuilder::make()
            ->name('Acme Corp')
            ->set('country', 'USA')
            ->set('phone', '+1-555-0123')
            ->build();

        expect($entity->get('country'))->toBe('USA')
            ->and($entity->get('phone'))->toBe('+1-555-0123')
            ->and($entity->has('country'))->toBeTrue()
            ->and($entity->has('missing'))->toBeFalse();
    });

    it('supports bulk attribute assignment', function () {
        $entity = EntityBuilder::make()
            ->name('Acme Corp')
            ->withAttributes([
                'country' => 'USA',
                'phone' => '+1-555-0123',
                'industry' => 'Software',
            ])
            ->build();

        expect($entity->attributes())
            ->toHaveKey('country', 'USA')
            ->toHaveKey('phone', '+1-555-0123')
            ->toHaveKey('industry', 'Software');
    });
});

describe('ItemBuilder', function () {
    it('creates an item with required data', function () {
        $item = ItemBuilder::make()
            ->description('Consulting Service')
            ->unitPrice(100.0)
            ->build();

        expect($item)
            ->toBeInstanceOf(ItemData::class)
            ->description->toBe('Consulting Service')
            ->unitPrice->toBe(100.0)
            ->quantity->toBe(1);
    });

    it('creates an item with complete data', function () {
        $item = ItemBuilder::make()
            ->description('Consulting Service')
            ->unitPrice(100.0)
            ->quantity(5)
            ->tax(0.15)
            ->discount(0.10)
            ->build();

        expect($item)
            ->description->toBe('Consulting Service')
            ->unitPrice->toBe(100.0)
            ->quantity->toBe(5)
            ->tax->toBe(0.15)
            ->discount->toBe(0.10);
    });

    it('calculates totals correctly', function () {
        $item = ItemBuilder::make()
            ->description('Service')
            ->unitPrice(100.0)
            ->quantity(5)
            ->tax(0.15)
            ->discount(0.10)
            ->build();

        expect($item->getSubtotal())->toBe(500.0)
            ->and($item->getDiscountAmount())->toBe(50.0)
            ->and($item->getSubtotalAfterDiscount())->toBe(450.0)
            ->and($item->getTaxAmount())->toBe(67.5)
            ->and($item->getTotal())->toBe(517.5);
    });

    it('supports custom attributes', function () {
        $item = ItemBuilder::make()
            ->description('Service')
            ->unitPrice(100.0)
            ->set('sku', 'SERV-001')
            ->set('category', 'Professional')
            ->build();

        expect($item->get('sku'))->toBe('SERV-001')
            ->and($item->get('category'))->toBe('Professional');
    });
});

describe('InvoiceBuilder', function () {
    it('requires seller and buyer', function () {
        expect(function () {
            InvoiceBuilder::make()->build();
        })->toThrow(InvalidArgumentException::class);
    });

    it('requires buyer', function () {
        $seller = EntityBuilder::make()->name('Seller')->build();

        expect(function () {
            InvoiceBuilder::make()->seller($seller)->build();
        })->toThrow(InvalidArgumentException::class);
    });

    it('creates a complete invoice', function () {
        $seller = EntityBuilder::make()->name('Acme Corp')->build();
        $buyer = EntityBuilder::make()->name('Client Inc')->build();
        $item = ItemBuilder::make()
            ->description('Service')
            ->unitPrice(100.0)
            ->quantity(5)
            ->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem($item)
            ->invoiceNumber('INV-001')
            ->currency('USD')
            ->build();

        expect($invoice)
            ->toBeInstanceOf(InvoiceData::class)
            ->seller->name->toBe('Acme Corp')
            ->buyer->name->toBe('Client Inc')
            ->items->toHaveCount(1)
            ->invoiceNumber->toBe('INV-001')
            ->currency->toBe('USD');
    });

    it('adds multiple items', function () {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem(ItemBuilder::make()->description('Item 1')->unitPrice(100.0)->build())
            ->addItem(ItemBuilder::make()->description('Item 2')->unitPrice(200.0)->build())
            ->build();

        expect($invoice->items)->toHaveCount(2);
    });

    it('calculates invoice totals', function () {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->addItem(ItemBuilder::make()
                ->description('Item 1')
                ->unitPrice(100.0)
                ->quantity(2)
                ->tax(0.10)
                ->discount(0.05)
                ->build())
            ->addItem(ItemBuilder::make()
                ->description('Item 2')
                ->unitPrice(50.0)
                ->quantity(1)
                ->tax(0.10)
                ->build())
            ->build();

        expect($invoice->getSubtotal())->toBe(250.0)
            ->and($invoice->getTotalDiscount())->toBe(10.0)
            ->and($invoice->getTotalTax())->toBeCloseTo(24.0, 1);
    });

    it('supports custom attributes', function () {
        $seller = EntityBuilder::make()->name('Seller')->build();
        $buyer = EntityBuilder::make()->name('Buyer')->build();

        $invoice = InvoiceBuilder::make()
            ->seller($seller)
            ->buyer($buyer)
            ->set('po_number', 'PO-9001')
            ->set('project_code', 'PROJ-001')
            ->build();

        expect($invoice->get('po_number'))->toBe('PO-9001')
            ->and($invoice->get('project_code'))->toBe('PROJ-001');
    });
});
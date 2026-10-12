<?php

declare(strict_types=1);

use Akira\PdfInvoices\Support\Decimal;

it('accepts exact decimal strings', function (string $value): void {
    expect(Decimal::assert($value, 'amount'))->toBe($value);
})->with(['0', '1400.00', '-12.5', '0.00001', '123456789012345678901234567890.99']);

it('rejects strings that are not plain decimals', function (string $value): void {
    Decimal::assert($value, 'amount');
})->with(['', '1,5', '1e3', ' 1', '1.', '.5', '+1', 'abc', '--1'])
    ->throws(InvalidArgumentException::class, 'The amount must be a decimal string');

it('sums exact decimals of mixed scale and sign', function (): void {
    expect(Decimal::sum([]))->toBe('0')
        ->and(Decimal::sum(['0.1', '0.2']))->toBe('0.3')
        ->and(Decimal::sum(['1400.00', '-400.005']))->toBe('999.995')
        ->and(Decimal::sum(['-1.5', '-2.25']))->toBe('-3.75')
        ->and(Decimal::sum(['1.5', '-1.50']))->toBe('0.00')
        ->and(Decimal::sum(['-3', '1']))->toBe('-2')
        ->and(Decimal::sum(['99999999999999999999.99', '0.01']))->toBe('100000000000000000000.00');
});

it('rounds half away from zero to a scale', function (): void {
    expect(Decimal::round('1.005', 2))->toBe('1.01')
        ->and(Decimal::round('1.004', 2))->toBe('1.00')
        ->and(Decimal::round('-1.005', 2))->toBe('-1.01')
        ->and(Decimal::round('9.999', 2))->toBe('10.00')
        ->and(Decimal::round('7', 2))->toBe('7.00')
        ->and(Decimal::round('-0.001', 2))->toBe('0.00')
        ->and(Decimal::round('12.345', 0))->toBe('12');
});

it('reports the scale of a decimal', function (): void {
    expect(Decimal::scale('12'))->toBe(0)
        ->and(Decimal::scale('12.34000'))->toBe(5);
});

it('trims trailing fraction zeros down to a minimum scale', function (): void {
    expect(Decimal::trim('1400.12300', 2))->toBe('1400.123')
        ->and(Decimal::trim('1400.00000', 2))->toBe('1400.00')
        ->and(Decimal::trim('2.500', 0))->toBe('2.5')
        ->and(Decimal::trim('2.000', 0))->toBe('2')
        ->and(Decimal::trim('3', 2))->toBe('3.00');
});

it('accepts a missing optional decimal', function (): void {
    expect(Decimal::assertOptional(null, 'amount'))->toBeNull()
        ->and(Decimal::assertOptional('1.5', 'amount'))->toBe('1.5');
});

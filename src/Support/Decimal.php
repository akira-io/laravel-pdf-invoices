<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use InvalidArgumentException;

final class Decimal
{
    private const string PATTERN = '/\A-?\d+(\.\d+)?\z/';

    public static function assert(string $value, string $field): string
    {
        throw_unless(
            preg_match(self::PATTERN, $value) === 1,
            InvalidArgumentException::class,
            "The {$field} must be a decimal string such as \"1400.00\", got \"{$value}\".",
        );

        return $value;
    }

    public static function assertOptional(?string $value, string $field): ?string
    {
        return $value === null ? null : self::assert($value, $field);
    }

    public static function scale(string $value): int
    {
        $position = mb_strpos($value, '.');

        return $position === false ? 0 : mb_strlen($value) - $position - 1;
    }

    /**
     * @param  array<int, string>  $values
     */
    public static function sum(array $values): string
    {
        if ($values === []) {
            return '0';
        }

        $scale = max(array_map(self::scale(...), $values));
        $negative = false;
        $magnitude = '0';

        foreach ($values as $value) {
            [$valueNegative, $valueMagnitude] = self::split($value, $scale);
            [$negative, $magnitude] = self::addSigned($negative, $magnitude, $valueNegative, $valueMagnitude);
        }

        return self::compose($negative, $magnitude, $scale);
    }

    public static function round(string $value, int $scale): string
    {
        $current = self::scale($value);

        if ($current <= $scale) {
            [$negative, $magnitude] = self::split($value, $scale);

            return self::compose($negative, $magnitude, $scale);
        }

        [$negative, $magnitude] = self::split($value, $current);
        $dropped = $current - $scale;
        $padded = mb_str_pad($magnitude, $dropped + 1, '0', STR_PAD_LEFT);
        $kept = mb_substr($padded, 0, mb_strlen($padded) - $dropped);

        if ((int) mb_substr($padded, -$dropped, 1) >= 5) {
            $kept = self::addMagnitudes($kept, '1');
        }

        return self::compose($negative, self::normalize($kept), $scale);
    }

    public static function trim(string $value, int $minimumScale): string
    {
        $scale = self::scale($value);

        while ($scale > $minimumScale && str_ends_with($value, '0')) {
            $value = mb_substr($value, 0, -1);
            $scale--;
        }

        $value = mb_rtrim($value, '.');

        return self::round($value, max($minimumScale, self::scale($value)));
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private static function split(string $value, int $scale): array
    {
        $negative = str_starts_with($value, '-');
        $unsigned = mb_ltrim($value, '-');
        $fraction = mb_str_pad(self::fraction($unsigned), $scale, '0');
        $integer = explode('.', $unsigned)[0];

        return [$negative, self::normalize($integer.$fraction)];
    }

    private static function fraction(string $unsigned): string
    {
        $parts = explode('.', $unsigned);

        return $parts[1] ?? '';
    }

    private static function compose(bool $negative, string $magnitude, int $scale): string
    {
        $padded = mb_str_pad($magnitude, $scale + 1, '0', STR_PAD_LEFT);
        $integer = mb_substr($padded, 0, mb_strlen($padded) - $scale);
        $fraction = $scale > 0 ? '.'.mb_substr($padded, -$scale) : '';
        $sign = $negative && $magnitude !== '0' ? '-' : '';

        return $sign.$integer.$fraction;
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private static function addSigned(bool $leftNegative, string $left, bool $rightNegative, string $right): array
    {
        if ($leftNegative === $rightNegative) {
            return [$leftNegative, self::addMagnitudes($left, $right)];
        }

        if (self::compareMagnitudes($left, $right) >= 0) {
            return [$leftNegative, self::subtractMagnitudes($left, $right)];
        }

        return [$rightNegative, self::subtractMagnitudes($right, $left)];
    }

    private static function addMagnitudes(string $left, string $right): string
    {
        $length = max(mb_strlen($left), mb_strlen($right));
        $left = mb_str_pad($left, $length, '0', STR_PAD_LEFT);
        $right = mb_str_pad($right, $length, '0', STR_PAD_LEFT);
        $carry = 0;
        $result = '';

        for ($index = $length - 1; $index >= 0; $index--) {
            $digit = (int) $left[$index] + (int) $right[$index] + $carry;
            $result = ($digit % 10).$result;
            $carry = intdiv($digit, 10);
        }

        return self::normalize(($carry > 0 ? (string) $carry : '').$result);
    }

    private static function subtractMagnitudes(string $larger, string $smaller): string
    {
        $length = mb_strlen($larger);
        $smaller = mb_str_pad($smaller, $length, '0', STR_PAD_LEFT);
        $borrow = 0;
        $result = '';

        for ($index = $length - 1; $index >= 0; $index--) {
            $digit = (int) $larger[$index] - (int) $smaller[$index] - $borrow;
            $borrow = $digit < 0 ? 1 : 0;
            $result = ($digit + $borrow * 10).$result;
        }

        return self::normalize($result);
    }

    private static function compareMagnitudes(string $left, string $right): int
    {
        return [mb_strlen($left), $left] <=> [mb_strlen($right), $right];
    }

    private static function normalize(string $digits): string
    {
        $trimmed = mb_ltrim($digits, '0');

        return $trimmed === '' ? '0' : $trimmed;
    }
}

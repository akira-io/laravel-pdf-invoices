<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use Illuminate\Support\Str;

final readonly class FiscalTextMeasure
{
    private const string ELLIPSIS = '…';

    private const string NARROW_GLYPHS = " .,:;'!|()-/ifjlrtIJ";

    private const string MEDIUM_GLYPHS = 'abcdeghknopqsuvxyz0123456789$*?_"';

    private const string WIDE_GLYPHS = 'mwMW@%+=#<>~^`{}[]\\';

    private const string CAPITAL_GLYPHS = 'ABCDEFGHKLNOPQRSTUVXYZ&';

    private const float NARROW_WIDTH = 0.412;

    private const float MEDIUM_WIDTH = 0.64;

    private const float CAPITAL_WIDTH = 0.79;

    private const float WIDE_WIDTH = 1.0;

    private const float UNKNOWN_WIDTH = 1.45;

    public function width(string $text, float $size): float
    {
        $em = 0.0;

        foreach (mb_str_split($text) as $glyph) {
            $em += $this->glyphWidth($this->baseLetter($glyph));
        }

        return $em * $size;
    }

    public function lineCount(string $text, float $size, float $width): int
    {
        $lines = 1;
        $used = 0.0;
        $space = $this->width(' ', $size);

        foreach (preg_split('/\s+/u', mb_trim($text), flags: PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
            $wordWidth = $this->width($word, $size);

            if ($wordWidth > $width) {
                $lines += ($used > 0.0 ? 1 : 0) + (int) floor($wordWidth / $width);
                $used = fmod($wordWidth, $width);

                continue;
            }

            $needed = $used > 0.0 ? $used + $space + $wordWidth : $wordWidth;

            if ($needed > $width) {
                $lines++;
                $needed = $wordWidth;
            }

            $used = $needed;
        }

        return $lines;
    }

    public function limit(string $text, float $size, float $width, int $lines = 1): string
    {
        if ($this->lineCount($text, $size, $width) <= $lines) {
            return $text;
        }

        $cut = mb_substr($text, 0, (int) ceil($lines * $width / ($size * self::NARROW_WIDTH)));

        while ($cut !== '' && $this->lineCount(mb_rtrim($cut).self::ELLIPSIS, $size, $width) > $lines) {
            $cut = mb_substr($cut, 0, -1);
        }

        return mb_rtrim($cut).self::ELLIPSIS;
    }

    private function baseLetter(string $glyph): string
    {
        if (preg_match('/^[\x{C0}-\x{D6}\x{D8}-\x{F6}\x{F8}-\x{FF}]$/u', $glyph) !== 1) {
            return $glyph;
        }

        $ascii = Str::ascii($glyph);

        return mb_strlen($ascii) === 1 ? $ascii : $glyph;
    }

    private function glyphWidth(string $glyph): float
    {
        return match (true) {
            str_contains(self::NARROW_GLYPHS, $glyph) => self::NARROW_WIDTH,
            str_contains(self::MEDIUM_GLYPHS, $glyph) => self::MEDIUM_WIDTH,
            str_contains(self::CAPITAL_GLYPHS, $glyph) => self::CAPITAL_WIDTH,
            str_contains(self::WIDE_GLYPHS, $glyph) => self::WIDE_WIDTH,
            default => self::UNKNOWN_WIDTH,
        };
    }
}

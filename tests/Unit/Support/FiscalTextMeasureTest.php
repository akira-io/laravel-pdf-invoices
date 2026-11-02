<?php

declare(strict_types=1);

use Akira\PdfInvoices\Support\FiscalTextMeasure;

it('measures wide glyphs wider than narrow ones', function (): void {
    $measure = new FiscalTextMeasure();

    expect($measure->width('', 10.0))->toBe(0.0)
        ->and($measure->width('ill', 10.0))->toBeLessThan($measure->width('nnn', 10.0))
        ->and($measure->width('nnn', 10.0))->toBeLessThan($measure->width('NNN', 10.0))
        ->and($measure->width('NNN', 10.0))->toBeLessThan($measure->width('WWW', 10.0))
        ->and($measure->width('ç', 10.0))->toBe($measure->width('c', 10.0));
});

it('never measures below the dejavu sans advance width', function (string $text, float $points): void {
    expect(new FiscalTextMeasure()->width($text, 9.0))->toBeGreaterThanOrEqual($points);
})->with([
    'several taxes' => ['15% + 1,50 + M01', 85.8],
    'long tax amount' => ['123 456$00', 54.4],
]);

it('counts wrapped lines', function (string $text, float $width, int $lines): void {
    expect(new FiscalTextMeasure()->lineCount($text, 8.0, $width))->toBe($lines);
})->with([
    'empty' => ['', 100.0, 1],
    'fits' => ['nota fiscal', 100.0, 1],
    'wraps at spaces' => ['nota fiscal nota fiscal nota fiscal', 50.0, 3],
    'collapses whitespace' => ["nota   fiscal\nnota", 200.0, 1],
    'breaks a word wider than the box' => [str_repeat('W', 40), 100.0, 4],
    'continues after a broken word' => [str_repeat('W', 20).' nota', 100.0, 2],
]);

it('limits text to its lines with an ellipsis', function (): void {
    $measure = new FiscalTextMeasure();
    $long = 'Sociedade de Construções e Obras Públicas de Cabo Verde, Lda';
    $limited = $measure->limit($long, 10.0, 180.0, 2);

    expect($measure->limit('Akira, Lda', 10.0, 180.0))->toBe('Akira, Lda')
        ->and($limited)->toEndWith('…')
        ->and($limited)->toStartWith('Sociedade de Construções')
        ->and($measure->lineCount($limited, 10.0, 180.0))->toBeLessThanOrEqual(2)
        ->and($measure->limit($long, 10.0, 180.0))->not->toBe($limited)
        ->and($measure->limit(str_repeat('n ', 5000), 10.0, 180.0))->toEndWith('…');
});

it('gives up on a box too small for the ellipsis', function (): void {
    expect(new FiscalTextMeasure()->limit('nota fiscal', 10.0, 1.0))->toBe('…');
});

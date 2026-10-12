<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

final readonly class FiscalSummaryBuilder
{
    public const float TOTAL_ROW_HEIGHT = 21.0;

    public const float VERIFICATION_MIN_Y = 665.0;

    public const float VERIFICATION_HEIGHT = 96.0;

    public const float CONTENT_BOTTOM_Y = 790.0;

    private const float VERIFICATION_GAP = 18.0;

    private const float TEXT_LINE_HEIGHT = 10.0;

    private const int CONVERSION_CHARACTERS_PER_LINE = 80;

    private const int NOTE_CHARACTERS_PER_LINE = 100;

    /**
     * @param  array<int, FiscalLine>  $totals
     * @param  array<int, FiscalLine>  $conversion
     * @param  array<int, string>  $notes
     */
    public function __construct(
        private array $totals,
        private array $conversion,
        private array $notes,
        private ?string $qrCode,
    ) {}

    public function preferredY(float $ceiling): float
    {
        return min($ceiling, self::VERIFICATION_MIN_Y - self::VERIFICATION_GAP - $this->place(0.0)['bottom']);
    }

    public function fits(float $totalsY): bool
    {
        return $this->build($totalsY)->verificationY + self::VERIFICATION_HEIGHT <= self::CONTENT_BOTTOM_Y;
    }

    public function build(float $totalsY): FiscalSummary
    {
        $placed = $this->place($totalsY);

        return new FiscalSummary(
            $placed['totals'],
            $placed['conversion'],
            $placed['conversionY'],
            $this->notes,
            $placed['notesY'],
            max(self::VERIFICATION_MIN_Y, $placed['bottom'] + self::VERIFICATION_GAP),
            $this->qrCode,
        );
    }

    /**
     * @return array{totals: array<int, FiscalLine>, conversion: array<int, FiscalLine>, conversionY: float|null, notesY: float|null, bottom: float}
     */
    private function place(float $totalsY): array
    {
        $totals = [];

        foreach ($this->totals as $index => $line) {
            $totals[] = $line->at($totalsY + $index * self::TOTAL_ROW_HEIGHT);
        }

        $cursor = $totalsY + count($totals) * self::TOTAL_ROW_HEIGHT;
        [$conversion, $conversionY, $cursor] = $this->placeConversion($cursor);
        $notesY = $this->notes === [] ? null : $cursor + 14;

        if ($notesY !== null) {
            $cursor = $notesY + 20 + $this->notesHeight();
        }

        return ['totals' => $totals, 'conversion' => $conversion, 'conversionY' => $conversionY, 'notesY' => $notesY, 'bottom' => $cursor];
    }

    /**
     * @return array{0: array<int, FiscalLine>, 1: float|null, 2: float}
     */
    private function placeConversion(float $cursor): array
    {
        if ($this->conversion === []) {
            return [[], null, $cursor];
        }

        $top = $cursor + 14;
        $rowY = $top + 20;
        $placed = [];

        foreach ($this->conversion as $line) {
            $height = $this->lines($line->value, self::CONVERSION_CHARACTERS_PER_LINE) * self::TEXT_LINE_HEIGHT;
            $placed[] = $line->at($rowY, $height);
            $rowY += $height + 5;
        }

        return [$placed, $top, $rowY - 5];
    }

    private function notesHeight(): float
    {
        $lines = array_sum(array_map(
            fn (string $note): int => $this->lines($note, self::NOTE_CHARACTERS_PER_LINE),
            $this->notes,
        ));

        return $lines * self::TEXT_LINE_HEIGHT;
    }

    private function lines(string $text, int $perLine): int
    {
        return max(1, (int) ceil(mb_strlen($text) / $perLine));
    }
}

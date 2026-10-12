<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Layout;

use Akira\PdfInvoices\Support\Decimal;
use Akira\PdfInvoices\Support\FiscalNumberFormatter;
use LengthException;

final readonly class FiscalPaginator
{
    public const float FIRST_TABLE_Y = 350.0;

    public const float CONTINUED_TABLE_Y = 245.0;

    public const float ROW_HEIGHT = 27.0;

    public const float TABLE_BOTTOM_Y = 700.0;

    private const float TABLE_HEADER_HEIGHT = 56.0;

    private const float ROW_TEXT_HEIGHT = 18.0;

    private const float SUMMARY_LOWEST_Y = 540.0;

    public function __construct(
        private FiscalNumberFormatter $formatter,
        private string $currency,
    ) {}

    /**
     * @param  array<int, FiscalRow>  $rows
     * @return array<int, FiscalPage>
     */
    public function paginate(array $rows, FiscalSummaryBuilder $summary): array
    {
        $pages = [];
        $tableY = self::FIRST_TABLE_Y;
        $pageRows = [];
        $amounts = [];
        $rowY = $tableY + self::TABLE_HEADER_HEIGHT;

        foreach ($rows as $row) {
            if ($rowY + self::ROW_TEXT_HEIGHT > self::TABLE_BOTTOM_Y) {
                $carried = $this->formatter->money(Decimal::sum($amounts), $this->currency);
                $pages[] = new FiscalPage(count($pages) + 1, $pages !== [], $tableY, $pageRows, $carried);
                $tableY = self::CONTINUED_TABLE_Y;
                $pageRows = [];
                $rowY = $tableY + self::TABLE_HEADER_HEIGHT;
            }

            $pageRows[] = $row->at($rowY);
            $amounts[] = Decimal::round($row->amount, 2);
            $rowY += self::ROW_HEIGHT;
        }

        $totalsY = max($rowY + 24, $summary->preferredY(self::SUMMARY_LOWEST_Y));

        if ($summary->fits($totalsY)) {
            $pages[] = new FiscalPage(count($pages) + 1, $pages !== [], $tableY, $pageRows, summary: $summary->build($totalsY));

            return $pages;
        }

        throw_unless(
            $summary->fits(self::CONTINUED_TABLE_Y),
            LengthException::class,
            'The totals, conversion evidence and notes do not fit on one page of the efatura template; shorten the notes.',
        );

        $pages[] = new FiscalPage(count($pages) + 1, $pages !== [], $tableY, $pageRows);
        $pages[] = new FiscalPage(count($pages) + 1, true, null, summary: $summary->build(self::CONTINUED_TABLE_Y));

        return $pages;
    }
}

<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Commands;

use Illuminate\Console\Command;

final class PdfInvoicesCommand extends Command
{
    public $signature = 'laravel-pdf-invoices';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

<?php

namespace Akira\PdfInvoices\Commands;

use Illuminate\Console\Command;

class PdfInvoicesCommand extends Command
{
    public $signature = 'laravel-pdf-invoices';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

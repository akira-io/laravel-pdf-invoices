<?php

declare(strict_types=1);

it('can execute the command', function (): void {
    $this->artisan('laravel-pdf-invoices')
        ->expectsOutput('All done')
        ->assertSuccessful();
});

<?php

declare(strict_types=1);

use Akira\PdfInvoices\Storage\LaravelStorageDriver;
use Illuminate\Support\Facades\Storage;

it('saves content to disk', function (): void {
    Storage::fake('local');
    $driver = new LaravelStorageDriver(Storage::disk('local'));

    $path = $driver->save('test.txt', 'hello');

    expect($path)->toBe('test.txt');
    expect(Storage::disk('local')->exists('test.txt'))->toBeTrue();
});

it('checks if file exists', function (): void {
    Storage::fake('local');
    $driver = new LaravelStorageDriver(Storage::disk('local'));

    expect($driver->exists('test.txt'))->toBeFalse();

    Storage::disk('local')->put('test.txt', 'hello');
    expect($driver->exists('test.txt'))->toBeTrue();
});

it('gets file content', function (): void {
    Storage::fake('local');
    $driver = new LaravelStorageDriver(Storage::disk('local'));

    Storage::disk('local')->put('test.txt', 'hello');
    expect($driver->get('test.txt'))->toBe('hello');
});

it('throws exception if file not found', function (): void {
    Storage::fake('local');
    $driver = new LaravelStorageDriver(Storage::disk('local'));

    $driver->get('missing.txt');
})->throws(RuntimeException::class, 'File not found at path: missing.txt');

it('deletes file', function (): void {
    Storage::fake('local');
    $driver = new LaravelStorageDriver(Storage::disk('local'));

    Storage::disk('local')->put('test.txt', 'hello');
    expect($driver->delete('test.txt'))->toBeTrue();
    expect(Storage::disk('local')->exists('test.txt'))->toBeFalse();
});

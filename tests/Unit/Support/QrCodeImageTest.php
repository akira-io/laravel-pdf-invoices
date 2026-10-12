<?php

declare(strict_types=1);

use Akira\PdfInvoices\Support\QrCodeImage;

it('renders a payload as an embeddable svg data uri', function (): void {
    $uri = new QrCodeImage()->dataUri('https://pe.efatura.cv/dfe/view/CV1234');
    $svg = base64_decode(mb_substr($uri, mb_strlen('data:image/svg+xml;base64,')), true);

    expect($uri)->toStartWith('data:image/svg+xml;base64,')
        ->and($svg)->toContain('<svg');
});

it('explains how to install the qr code package when it is missing', function (): void {
    new QrCodeImage('Akira\\QrCode\\MissingGenerator')->dataUri('payload');
})->throws(RuntimeException::class, 'composer require akira/laravel-qrcode');

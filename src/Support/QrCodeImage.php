<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Support;

use Akira\QrCode\QrCode;
use RuntimeException;

final readonly class QrCodeImage
{
    /**
     * @param  class-string<QrCode>  $generator
     */
    public function __construct(
        private string $generator = QrCode::class,
    ) {}

    public function dataUri(string $payload, int $size = 144): string
    {
        $qrCode = class_exists($this->generator) ? resolve($this->generator) : null;

        throw_unless(
            $qrCode instanceof QrCode,
            RuntimeException::class,
            'The efatura template draws its QR code with akira/laravel-qrcode. Install it with "composer require akira/laravel-qrcode".',
        );

        $svg = $qrCode
            ->size($size)
            ->margin(1)
            ->errorCorrection('M')
            ->format('svg')
            ->generateRaw($payload);

        return 'data:image/svg+xml;base64,'.base64_encode((string) $svg);
    }
}

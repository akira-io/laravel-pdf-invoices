<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Enums;

enum EmissionMode: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Off = 'off';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online',
            self::Offline => 'Offline',
            self::Off => 'Off',
        };
    }

    public function isContingency(): bool
    {
        return $this !== self::Online;
    }
}

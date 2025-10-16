<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum OfferGroupLinkStatusEnum: string
{
    use TranslatableEnum;

    case LINKED = 'linked';
    case DECOUPLED = 'decoupled';
    case OUTDATED = 'outdated';

    public function color(): string
    {
        return match($this) {
            self::LINKED => 'success',
            self::DECOUPLED => 'info',
            self::OUTDATED => 'warning',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LINKED => 'heroicon-o-link',
            self::DECOUPLED => 'heroicon-o-lock-closed',
            self::OUTDATED => 'heroicon-o-exclamation-triangle',
        };
    }
}


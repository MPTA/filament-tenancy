<?php

namespace App\Enums;

enum OfferGroupLinkStatusEnum: string
{
    case LINKED = 'linked';
    case DECOUPLED = 'decoupled';
    case OUTDATED = 'outdated';

    public function label(): string
    {
        return match($this) {
            self::LINKED => 'Linked',
            self::DECOUPLED => 'Decoupled',
            self::OUTDATED => 'Outdated',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::LINKED => 'Automatically syncs with breakdown changes',
            self::DECOUPLED => 'Independent snapshot - not affected by breakdown changes',
            self::OUTDATED => 'Breakdown changed - needs review',
        };
    }

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

    public static function getOptions(): array
    {
        return [
            self::LINKED->value => self::LINKED->label(),
            self::DECOUPLED->value => self::DECOUPLED->label(),
            self::OUTDATED->value => self::OUTDATED->label(),
        ];
    }
}


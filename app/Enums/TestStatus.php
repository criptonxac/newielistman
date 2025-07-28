<?php

namespace App\Enums;

enum TestStatus: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';
    
    public function label(): string
    {
        return match($this) {
            self::IN_PROGRESS => 'Jarayonda',
            self::COMPLETED => 'Tugatilgan',
            self::ABANDONED => 'Tark etilgan',
        };
    }
    
    public static function toArray(): array
    {
        return [
            self::IN_PROGRESS->value => self::IN_PROGRESS->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::ABANDONED->value => self::ABANDONED->label(),
        ];
    }
    
    public static function values(): array
    {
        return [
            self::IN_PROGRESS->value,
            self::COMPLETED->value,
            self::ABANDONED->value,
        ];
    }
}

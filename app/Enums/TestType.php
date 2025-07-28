<?php

namespace App\Enums;

enum TestType: string
{
    case FAMILIARISATION = 'familiarisation';
    case SAMPLE = 'sample';
    case PRACTICE = 'practice';
    
    public function label(): string
    {
        return match($this) {
            self::FAMILIARISATION => 'Tanishuv',
            self::SAMPLE => 'Namuna',
            self::PRACTICE => 'Amaliyot',
        };
    }
    
    public static function toArray(): array
    {
        return [
            self::FAMILIARISATION->value => self::FAMILIARISATION->label(),
            self::SAMPLE->value => self::SAMPLE->label(),
            self::PRACTICE->value => self::PRACTICE->label(),
        ];
    }
    
    public static function values(): array
    {
        return [
            self::FAMILIARISATION->value,
            self::SAMPLE->value,
            self::PRACTICE->value,
        ];
    }
}

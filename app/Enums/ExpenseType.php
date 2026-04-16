<?php

namespace App\Enums;

enum ExpenseType: string
{
    case TREE = 'Tree';
    case TRANSPORTATION = 'Transportation';
    case WATER = 'Watering for trees';
    case TOOLS = 'Tools';
    case SNACK = 'Snack';
    case PIPE = 'Pipe';
    case OTHER = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::TREE => 'Tree (درخت)',
            self::TRANSPORTATION => 'Transportation (ترانسپورت)',
            self::WATER => 'Water for Trees (آبیاری)',
            self::TOOLS => 'Tools (وسایل)',
            self::SNACK => 'Refreshments (آب و خوراک)',
            self::PIPE => 'Pipe (پایپ)',
            self::OTHER => 'Other (سایر)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TREE => '#064e3b',
            self::TRANSPORTATION => '#d4af37',
            self::WATER => '#3b82f6',
            self::TOOLS => '#f59e0b',
            self::SNACK => '#ec4899',
            self::PIPE => '#8b5cf6',
            self::OTHER => '#6b7280',
        };
    }

    public static function options(): array
    {
        $opts = [];
        foreach (self::cases() as $case) {
            $opts[$case->value] = $case->label();
        }
        return $opts;
    }
}

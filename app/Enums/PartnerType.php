<?php

namespace App\Enums;

enum PartnerType: string
{
    case SPONSOR = 'Sponsor';
    case ADVISOR = 'Advisor';
    case COLLABORATOR = 'Collaborator';
    case SUPPORTER = 'Supporter';
    case OTHER = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::SPONSOR => 'Sponsor (حامی مالی)',
            self::ADVISOR => 'Advisor (مشاور)',
            self::COLLABORATOR => 'Collaborator (همکار)',
            self::SUPPORTER => 'Supporter (پشتیبان)',
            self::OTHER => 'Other (سایر)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SPONSOR => '#d4af37',
            self::ADVISOR => '#064e3b',
            self::COLLABORATOR => '#84cc16',
            self::SUPPORTER => '#3b82f6',
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

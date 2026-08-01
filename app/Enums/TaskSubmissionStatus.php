<?php

namespace App\Enums;

/**
 * The moderation state of one submitted attempt.
 *
 * Mirrors the `pending | approved | rejected` convention already used by
 * {@see \App\Models\Tree} and {@see \App\Models\Voice}, so reviewers see the
 * same vocabulary everywhere in the admin panel.
 */
enum TaskSubmissionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Sent back for a fix rather than refused outright.
     *
     * Distinct from REJECTED so the review queue can tell "nearly there" from
     * "no" without reading the comments — they warrant different wording to the
     * volunteer and a different push.
     */
    case NEEDS_REVISION = 'needs_revision';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::NEEDS_REVISION => 'Needs Revision',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => '#f59e0b',
            self::APPROVED => '#059669',
            self::REJECTED => '#dc2626',
            self::NEEDS_REVISION => '#f97316',
        };
    }

    /** @return array<string, string> value => label, for select inputs. */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}

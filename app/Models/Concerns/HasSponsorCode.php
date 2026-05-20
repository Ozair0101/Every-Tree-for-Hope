<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Gives a model a unique, shareable sponsor "code".
 *
 * The code is human-meaningful, built from the sponsor's own name:
 *   ETH-ABDULKARIM-01
 *   └┬┘ └────┬────┘ └┬┘
 *    │       │       └─ sequence (keeps it unique)
 *    │       └───────── the sponsor's name (so the code is recognisable)
 *    └───────────────── "Every Tree for a Hope" tag
 *
 * The code is generated on create and used by sponsors to look up
 * the events they funded.
 */
trait HasSponsorCode
{
    protected static function bootHasSponsorCode(): void
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = static::generateUniqueSponsorCode($model->sponsorCodeSource());
            }
        });
    }

    /**
     * The text the code is derived from. Models override this.
     */
    public function sponsorCodeSource(): string
    {
        return (string) ($this->full_name
            ?? $this->company_name
            ?? $this->name
            ?? 'sponsor');
    }

    public static function generateUniqueSponsorCode(string $source): string
    {
        // Name → ABDULKARIM (letters/digits only, max 12 chars)
        $slug = (string) Str::of($source)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '')
            ->substr(0, 12);

        if ($slug === '') {
            $slug = 'SPONSOR';
        }

        $i = 1;
        do {
            $code = 'ETH-' . $slug . '-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $i++;
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }

    public static function findByCode(?string $code): ?self
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        return static::query()->whereRaw('UPPER(code) = ?', [Str::upper($code)])->first();
    }
}

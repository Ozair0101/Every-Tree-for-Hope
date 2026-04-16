<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'category',
        'question',
        'answer',
        'asked_by_name',
        'asked_by_email',
        'is_verified',
        'sort_order',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true)->whereNotNull('answer');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopeUnanswered($query)
    {
        return $query->whereNull('answer')->orWhere('answer', '');
    }
}

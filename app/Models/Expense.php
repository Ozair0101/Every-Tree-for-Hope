<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'date',
        'description',
        'expense_type',
        'quantity',
        'unit_price',
        'total_cost',
        'who_paid',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('date', 'desc');
    }
}

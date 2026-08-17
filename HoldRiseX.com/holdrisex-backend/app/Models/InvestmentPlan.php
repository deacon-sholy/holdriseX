<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_amount',
        'max_amount',
        'daily_return',
        'duration_days',
        'min_profit',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'daily_return' => 'decimal:2',
            'min_profit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}

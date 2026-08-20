<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CopyTrader extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty',
        'win_rate',
        'monthly_return',
        'total_followers',
        'aum',
        'risk_level',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'win_rate' => 'decimal:2',
            'monthly_return' => 'decimal:2',
            'aum' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function copyTrades(): HasMany
    {
        return $this->hasMany(CopyTrade::class, 'trader_id');
    }
}

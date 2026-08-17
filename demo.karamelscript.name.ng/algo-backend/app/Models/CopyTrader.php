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
        'avatar',
        'description',
        'win_rate',
        'total_trades',
        'total_profit',
        'subscribers',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'win_rate' => 'decimal:2',
            'total_profit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function copyTrades(): HasMany
    {
        return $this->hasMany(CopyTrade::class);
    }
}

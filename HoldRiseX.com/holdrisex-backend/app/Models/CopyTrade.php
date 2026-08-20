<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopyTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trader_id',
        'symbol',
        'lots',
        'pnl',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'lots' => 'decimal:4',
            'pnl' => 'decimal:2',
        ];
    }

    public function copyTrader(): BelongsTo
    {
        return $this->belongsTo(CopyTrader::class, 'trader_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopyTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'copy_trader_id',
        'user_id',
        'pair',
        'amount',
        'pnl',
        'status',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'pnl' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function copyTrader(): BelongsTo
    {
        return $this->belongsTo(CopyTrader::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

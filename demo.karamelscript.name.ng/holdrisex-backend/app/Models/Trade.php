<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'type',
        'asset_type',
        'entry_price',
        'current_price',
        'lot_size',
        'pnl',
        'status',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'entry_price' => 'decimal:6',
            'current_price' => 'decimal:6',
            'lot_size' => 'decimal:4',
            'pnl' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

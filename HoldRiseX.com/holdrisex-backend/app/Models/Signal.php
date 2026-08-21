<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'direction',
        'entry_price',
        'take_profit',
        'stop_loss',
        'status',
        'risk_reward',
        'analysis',
        'analyst',
    ];

    protected function casts(): array
    {
        return [
            'entry_price' => 'decimal:2',
            'take_profit' => 'decimal:2',
            'stop_loss' => 'decimal:2',
            'risk_reward' => 'decimal:2',
        ];
    }
}

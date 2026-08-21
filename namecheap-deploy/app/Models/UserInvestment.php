<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInvestment extends Model
{
    use HasFactory;

    protected $table = 'user_investments';

    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'daily_return_earned',
        'total_earned',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'daily_return_earned' => 'decimal:2',
            'total_earned' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InvestmentPlan::class, 'plan_id');
    }
}

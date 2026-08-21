<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country',
        'balance',
        'kyc_status',
        'is_active',
        'avatar',
        'last_login_at',
        'role',
        'referral_code',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(UserInvestment::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function copyTrades(): HasMany
    {
        return $this->hasMany(CopyTrade::class);
    }

    public function kycDocuments(): HasMany
    {
        return $this->hasMany(KycDocument::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function sentTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    public function receivedTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'recipient_id');
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}

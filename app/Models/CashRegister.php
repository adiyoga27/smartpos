<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $fillable = [
        'business_id', 'location_id', 'user_id', 'status', 'closed_at',
        'closing_amount', 'total_card', 'total_bank_transfer', 'total_cheque',
        'total_cash', 'total_other', 'opening_amount', 'closing_note',
        'denom_100000', 'denom_50000', 'denom_20000', 'denom_10000',
        'denom_5000', 'denom_2000', 'denom_1000', 'denom_500', 'denom_100',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'status' => 'string',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashRegisterTransaction::class);
    }
}

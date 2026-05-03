<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterTransaction extends Model
{
    protected $fillable = [
        'cash_register_id', 'amount', 'pay_method', 'type',
        'transaction_type', 'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}

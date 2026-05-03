<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id', 'type', 'sub_type', 'amount', 'reff_no',
        'operation_date', 'created_by', 'transaction_id',
        'transaction_payment_id', 'note',
    ];

    protected $casts = [
        'operation_date' => 'datetime',
        'amount' => 'decimal:4',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionPayment(): BelongsTo
    {
        return $this->belongsTo(TransactionPayment::class);
    }
}

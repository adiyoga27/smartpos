<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionPayment extends Model
{
    protected $fillable = [
        'transaction_id', 'business_id', 'is_return', 'amount', 'method',
        'transaction_no', 'card_transaction_number', 'card_number',
        'card_type', 'card_holder_name', 'cheque_number',
        'bank_account_number', 'paid_on', 'created_by', 'is_advance',
        'payment_for', 'parent_id', 'note', 'document', 'payment_ref_no',
        'account_id',
    ];

    protected $casts = [
        'paid_on' => 'datetime',
        'is_return' => 'boolean',
        'is_advance' => 'boolean',
        'amount' => 'decimal:4',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

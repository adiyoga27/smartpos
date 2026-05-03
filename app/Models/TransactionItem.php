<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id', 'product_id', 'variation_id', 'quantity',
        'unit_price_before_discount', 'unit_price', 'line_discount_type',
        'line_discount_amount', 'unit_price_inc_tax', 'item_tax', 'tax_id',
        'item_type', 'sell_line_note', 'lot_number', 'mfg_date', 'exp_date',
        'purchase_price', 'purchase_price_inc_tax', 'quantity_returned',
        'quantity_sold', 'quantity_adjusted', 'parent_item_id', 'sub_unit_id',
    ];

    protected $casts = [
        'mfg_date' => 'date',
        'exp_date' => 'date',
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'unit_price_inc_tax' => 'decimal:4',
        'item_tax' => 'decimal:4',
        'line_discount_amount' => 'decimal:4',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class);
    }
}

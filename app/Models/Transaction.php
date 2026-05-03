<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'business_id', 'location_id', 'type', 'sub_type', 'status',
        'is_quotation', 'payment_status', 'adjustment_type', 'contact_id',
        'customer_group_id', 'invoice_no', 'ref_no', 'transaction_date',
        'total_before_tax', 'tax_id', 'tax_amount', 'discount_type',
        'discount_amount', 'round_off_amount', 'final_total',
        'additional_notes', 'staff_note', 'expense_category_id',
        'expense_for', 'is_direct_sale', 'is_suspend', 'exchange_rate',
        'transfer_parent_id', 'return_parent_id', 'opening_stock_product_id',
        'created_by', 'commission_agent', 'document', 'selling_price_group_id',
        'pay_term_number', 'pay_term_type', 'shipping_details',
        'shipping_charges', 'shipping_custom_field_1', 'shipping_custom_field_2',
        'shipping_custom_field_3', 'shipping_custom_field_4',
        'shipping_custom_field_5', 'is_recurring', 'recur_interval',
        'recur_interval_type', 'recur_repetitions', 'recur_stopped_on',
        'subscription_no', 'rp_earned', 'rp_redeemed', 'rp_redeemed_amount',
        'order_addresses', 'types_of_service_id', 'packing_charge',
        'packing_charge_type', 'service_custom_field_1',
        'service_custom_field_2', 'service_custom_field_3',
        'service_custom_field_4', 'service_custom_field_5',
        'service_custom_field_6', 'import_batch', 'import_time',
        'custom_field_1', 'custom_field_2', 'custom_field_3',
        'custom_field_4', 'invoice_token',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'is_quotation' => 'boolean',
        'is_direct_sale' => 'boolean',
        'is_suspend' => 'boolean',
        'is_recurring' => 'boolean',
        'exchange_rate' => 'decimal:3',
        'total_before_tax' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'final_total' => 'decimal:4',
        'shipping_charges' => 'decimal:4',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_id');
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'sell');
    }

    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }
}

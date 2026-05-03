<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id', 'type', 'supplier_business_name', 'prefix',
        'first_name', 'last_name', 'email', 'contact_id', 'contact_status',
        'tax_number', 'city', 'state', 'country', 'address_line_1', 'zip_code',
        'mobile', 'landline', 'alternate_number', 'credit_limit',
        'pay_term_number', 'pay_term_type', 'balance', 'total_rp',
        'total_rp_used', 'total_rp_expired', 'is_default', 'customer_group_id',
        'created_by', 'custom_field1', 'custom_field2', 'custom_field3',
        'custom_field4', 'shipping_address', 'position', 'dob',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'balance' => 'decimal:4',
        'credit_limit' => 'decimal:4',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    public function scopeCustomers($query)
    {
        return $query->whereIn('type', ['customer', 'both']);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }
}

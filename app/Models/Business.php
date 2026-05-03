<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'name', 'currency_id', 'tax_number_1', 'tax_number_2',
        'tax_label_1', 'tax_label_2', 'default_sales_tax', 'default_profit_percent',
        'owner_id', 'time_zone', 'fy_start_month', 'sell_price_tax',
        'enable_brand', 'enable_category', 'enable_sub_category', 'enable_price_tax',
        'enable_purchase_status', 'enable_lot_number', 'enable_product_expiry',
        'expiry_type', 'on_product_expiry', 'stock_expiry_alert_days',
        'keyboard_shortcuts', 'pos_settings', 'weighing_scale_setting',
        'common_settings', 'email_settings', 'sms_settings', 'ref_no_prefixes',
        'theme_color', 'enabled_modules', 'enable_rp', 'rp_name',
        'amount_for_unit_rp', 'min_order_total_for_rp', 'max_rp_per_order',
        'redeems_amount_per_unit_rp', 'min_order_total_for_redeem',
        'min_redeem_point', 'max_redeem_point', 'rp_expiry_period', 'rp_expiry_type',
        'date_format', 'time_format', 'is_active',
        'default_invoice_scheme_id', 'default_invoice_layout_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_brand' => 'boolean',
        'enable_category' => 'boolean',
        'enable_sub_category' => 'boolean',
        'enable_price_tax' => 'boolean',
        'enable_purchase_status' => 'boolean',
        'enable_lot_number' => 'boolean',
        'enable_product_expiry' => 'boolean',
        'enable_rp' => 'boolean',
        'fy_start_month' => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

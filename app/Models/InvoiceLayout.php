<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLayout extends Model
{
    protected $fillable = [
        'name', 'header_text', 'invoice_heading', 'sub_heading1',
        'sub_heading2', 'sub_heading3', 'sub_total_label', 'discount_label',
        'tax_label', 'total_label', 'total_due_label', 'paid_label',
        'balance_label', 'date_label', 'invoice_no_label', 'customer_label',
        'sales_person_label', 'show_business_name', 'show_location_name',
        'show_logo', 'show_customer', 'show_sales_person', 'show_barcode',
        'show_tax_1', 'show_tax_2', 'show_payments', 'show_sku', 'show_brand',
        'show_expiry', 'show_lot_number', 'footer_text', 'business_id',
        'is_default', 'cn_heading', 'cn_no_label', 'cn_amount_label',
        'prev_bal_label', 'change_return_label', 'highlight_color', 'design',
        'common_settings',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'show_business_name' => 'boolean',
        'show_location_name' => 'boolean',
        'show_logo' => 'boolean',
        'show_customer' => 'boolean',
        'show_sales_person' => 'boolean',
        'show_barcode' => 'boolean',
        'show_tax_1' => 'boolean',
        'show_tax_2' => 'boolean',
        'show_payments' => 'boolean',
        'show_sku' => 'boolean',
        'show_brand' => 'boolean',
        'show_expiry' => 'boolean',
        'show_lot_number' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

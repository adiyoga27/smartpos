<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessLocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id', 'location_id', 'name', 'landmark', 'country', 'state',
        'city', 'zip_code', 'address', 'mobile', 'alternate_number', 'email',
        'website', 'invoice_scheme_id', 'invoice_layout_id',
        'sale_invoice_layout_id', 'selling_price_group_id',
        'print_receipt_on_invoice', 'receipt_printer_type', 'printer_id',
        'enable_tips', 'custom_fields', 'default_payment_accounts',
    ];

    protected $casts = [
        'print_receipt_on_invoice' => 'boolean',
        'enable_tips' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function invoiceScheme(): BelongsTo
    {
        return $this->belongsTo(InvoiceScheme::class, 'invoice_scheme_id');
    }

    public function invoiceLayout(): BelongsTo
    {
        return $this->belongsTo(InvoiceLayout::class, 'invoice_layout_id');
    }
}

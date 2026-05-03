<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceScheme extends Model
{
    protected $fillable = [
        'business_id', 'name', 'scheme_type', 'prefix', 'start_number',
        'invoice_count', 'total_digits', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    protected $fillable = [
        'name', 'business_id', 'brand_id', 'category_id', 'location_id',
        'priority', 'discount_type', 'discount_amount', 'starts_at', 'ends_at',
        'is_active', 'applicable_in_spg', 'applicable_in_cg',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'applicable_in_spg' => 'boolean',
        'applicable_in_cg' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'sub_sku', 'product_id', 'default_purchase_price',
        'dpp_inc_tax', 'profit_percent', 'default_sell_price',
        'sell_price_inc_tax', 'combo_variations',
    ];

    protected $casts = [
        'combo_variations' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function locationDetails(): HasMany
    {
        return $this->hasMany(VariationLocationDetail::class);
    }

    public function groupPrices(): HasMany
    {
        return $this->hasMany(VariationGroupPrice::class);
    }
}

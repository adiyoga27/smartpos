<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name', 'business_id', 'type', 'unit_id', 'brand_id', 'category_id',
        'sub_category_id', 'tax_id', 'tax_type', 'enable_stock', 'alert_quantity',
        'sku', 'barcode_type', 'image', 'product_description', 'weight',
        'warranty_id', 'is_inactive', 'not_for_selling', 'created_by',
    ];

    protected $casts = [
        'enable_stock' => 'boolean',
        'is_inactive' => 'boolean',
        'not_for_selling' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }
}

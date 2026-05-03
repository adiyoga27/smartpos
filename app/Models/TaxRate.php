<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'amount', 'is_tax_group', 'business_id', 'created_by',
    ];

    protected $casts = [
        'is_tax_group' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

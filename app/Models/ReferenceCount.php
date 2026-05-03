<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceCount extends Model
{
    protected $fillable = ['ref_type', 'ref_count', 'business_id'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

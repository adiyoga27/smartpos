<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    protected $fillable = [
        'name', 'description', 'business_id', 'duration', 'duration_type',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    protected $fillable = [
        'business_id', 'name', 'connection_type', 'capability_profile',
        'char_per_line', 'ip_address', 'port', 'path', 'created_by',
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

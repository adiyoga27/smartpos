<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'business_id'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

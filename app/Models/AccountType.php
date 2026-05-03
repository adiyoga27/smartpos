<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountType extends Model
{
    protected $fillable = ['name', 'parent_account_type_id', 'business_id'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'parent_account_type_id');
    }
}

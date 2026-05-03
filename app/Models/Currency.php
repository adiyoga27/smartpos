<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['country', 'currency', 'code', 'symbol', 'thousand_separator', 'decimal_separator'];
}

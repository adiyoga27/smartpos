<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'business_id',
        'first_name', 'last_name', 'phone', 'address', 'language',
        'allow_login', 'status', 'is_cmmsn_agnt', 'cmmsn_percent',
        'max_sales_discount_percent', 'bank_details', 'custom_field_1',
        'custom_field_2', 'custom_field_3', 'custom_field_4',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'allow_login' => 'boolean',
            'is_cmmsn_agnt' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

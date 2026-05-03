<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username')->unique()->nullable()->after('email');
            $table->unsignedBigInteger('business_id')->nullable()->after('username');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('last_name');
            $table->string('address')->nullable()->after('phone');
            $table->string('language')->default('id')->after('address');
            $table->boolean('allow_login')->default(true)->after('language');
            $table->string('status')->default('active')->after('allow_login');
            $table->boolean('is_cmmsn_agnt')->default(false)->after('status');
            $table->decimal('cmmsn_percent', 4, 2)->default(0)->after('is_cmmsn_agnt');
            $table->decimal('max_sales_discount_percent', 5, 2)->nullable()->after('cmmsn_percent');
            $table->text('bank_details')->nullable()->after('max_sales_discount_percent');
            $table->string('custom_field_1')->nullable();
            $table->string('custom_field_2')->nullable();
            $table->string('custom_field_3')->nullable();
            $table->string('custom_field_4')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'username', 'business_id', 'first_name', 'last_name', 'phone',
                'address', 'language', 'allow_login', 'status', 'is_cmmsn_agnt',
                'cmmsn_percent', 'max_sales_discount_percent', 'bank_details',
                'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4',
                'deleted_at',
            ]);
        });
    }
};

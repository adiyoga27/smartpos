<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->double('amount', 5, 2);
            $table->string('price_calculation_type')->default('percentage');
            $table->unsignedBigInteger('selling_price_group_id')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('contacts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('type')->default('customer');
            $table->string('supplier_business_name')->nullable();
            $table->string('prefix')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_id')->nullable();
            $table->string('contact_status')->default('active');
            $table->string('tax_number')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable()->default('Indonesia');
            $table->text('address_line_1')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('mobile')->nullable();
            $table->string('landline')->nullable();
            $table->string('alternate_number')->nullable();
            $table->decimal('credit_limit', 22, 4)->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->decimal('balance', 22, 4)->default(0);
            $table->integer('total_rp')->default(0);
            $table->integer('total_rp_used')->default(0);
            $table->integer('total_rp_expired')->default(0);
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('custom_field1')->nullable();
            $table->string('custom_field2')->nullable();
            $table->string('custom_field3')->nullable();
            $table->string('custom_field4')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('position')->nullable();
            $table->date('dob')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('customer_groups');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('tax_number_1')->nullable();
            $table->string('tax_number_2')->nullable();
            $table->string('tax_label_1')->nullable()->default('GST');
            $table->string('tax_label_2')->nullable();
            $table->unsignedBigInteger('default_sales_tax')->nullable();
            $table->decimal('default_profit_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('owner_id');
            $table->string('time_zone')->default('Asia/Jakarta');
            $table->tinyInteger('fy_start_month')->default(1);
            $table->enum('sell_price_tax', ['includes', 'excludes'])->default('excludes');
            $table->boolean('enable_brand')->default(true);
            $table->boolean('enable_category')->default(true);
            $table->boolean('enable_sub_category')->default(true);
            $table->boolean('enable_price_tax')->default(true);
            $table->boolean('enable_purchase_status')->default(true);
            $table->boolean('enable_lot_number')->default(false);
            $table->boolean('enable_product_expiry')->default(false);
            $table->enum('expiry_type', ['add_expiry', 'add_manufacturing'])->nullable();
            $table->enum('on_product_expiry', ['keep_sell', 'stop_sell', 'auto_delete'])->nullable();
            $table->integer('stock_expiry_alert_days')->nullable();
            $table->text('keyboard_shortcuts')->nullable();
            $table->text('pos_settings')->nullable();
            $table->text('weighing_scale_setting')->nullable();
            $table->text('common_settings')->nullable();
            $table->text('email_settings')->nullable();
            $table->text('sms_settings')->nullable();
            $table->text('ref_no_prefixes')->nullable();
            $table->string('theme_color')->default('blue');
            $table->text('enabled_modules')->nullable();
            $table->boolean('enable_rp')->default(false);
            $table->string('rp_name')->nullable()->default('Reward Point');
            $table->decimal('amount_for_unit_rp', 5, 2)->nullable()->default(1);
            $table->decimal('min_order_total_for_rp', 22, 4)->nullable()->default(1);
            $table->integer('max_rp_per_order')->nullable();
            $table->decimal('redeems_amount_per_unit_rp', 5, 2)->nullable()->default(1);
            $table->decimal('min_order_total_for_redeem', 22, 4)->nullable()->default(1);
            $table->integer('min_redeem_point')->nullable()->default(1);
            $table->integer('max_redeem_point')->nullable();
            $table->integer('rp_expiry_period')->nullable();
            $table->enum('rp_expiry_type', ['month', 'year'])->nullable();
            $table->string('date_format')->default('d-m-Y');
            $table->enum('time_format', ['12', '24'])->default('24');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('default_invoice_scheme_id')->nullable();
            $table->unsignedBigInteger('default_invoice_layout_id')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('business_locations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('location_id')->nullable();
            $table->string('name');
            $table->string('landmark')->nullable();
            $table->string('country')->nullable()->default('Indonesia');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('alternate_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('invoice_scheme_id')->nullable();
            $table->unsignedBigInteger('invoice_layout_id')->nullable();
            $table->unsignedBigInteger('sale_invoice_layout_id')->nullable();
            $table->unsignedBigInteger('selling_price_group_id')->nullable();
            $table->boolean('print_receipt_on_invoice')->default(true);
            $table->enum('receipt_printer_type', ['browser', 'printer'])->default('browser');
            $table->unsignedBigInteger('printer_id')->nullable();
            $table->boolean('enable_tips')->default(false);
            $table->text('custom_fields')->nullable();
            $table->text('default_payment_accounts')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_locations');
        Schema::dropIfExists('businesses');
    }
};

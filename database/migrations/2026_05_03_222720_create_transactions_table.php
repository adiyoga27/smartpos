<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('type');
            $table->string('sub_type')->nullable();
            $table->string('status')->default('final');
            $table->boolean('is_quotation')->default(false);
            $table->enum('payment_status', ['paid', 'due', 'partial'])->nullable();
            $table->enum('adjustment_type', ['normal', 'abnormal'])->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->decimal('round_off_amount', 22, 4)->default(0);
            $table->decimal('final_total', 22, 4)->default(0);
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('expense_for')->nullable();
            $table->boolean('is_direct_sale')->default(false);
            $table->boolean('is_suspend')->default(false);
            $table->decimal('exchange_rate', 20, 3)->default(1);
            $table->unsignedBigInteger('transfer_parent_id')->nullable();
            $table->unsignedBigInteger('return_parent_id')->nullable();
            $table->unsignedBigInteger('opening_stock_product_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('commission_agent')->nullable();
            $table->string('document')->nullable();
            $table->unsignedBigInteger('selling_price_group_id')->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->text('shipping_details')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0);
            $table->text('shipping_custom_field_1')->nullable();
            $table->text('shipping_custom_field_2')->nullable();
            $table->text('shipping_custom_field_3')->nullable();
            $table->text('shipping_custom_field_4')->nullable();
            $table->text('shipping_custom_field_5')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->integer('recur_interval')->nullable();
            $table->enum('recur_interval_type', ['days', 'months', 'years'])->nullable();
            $table->integer('recur_repetitions')->nullable();
            $table->date('recur_stopped_on')->nullable();
            $table->string('subscription_no')->nullable();
            $table->integer('rp_earned')->default(0);
            $table->integer('rp_redeemed')->default(0);
            $table->decimal('rp_redeemed_amount', 22, 4)->default(0);
            $table->text('order_addresses')->nullable();
            $table->unsignedBigInteger('types_of_service_id')->nullable();
            $table->decimal('packing_charge', 22, 4)->nullable();
            $table->enum('packing_charge_type', ['fixed', 'percentage'])->nullable();
            $table->text('service_custom_field_1')->nullable();
            $table->text('service_custom_field_2')->nullable();
            $table->text('service_custom_field_3')->nullable();
            $table->text('service_custom_field_4')->nullable();
            $table->text('service_custom_field_5')->nullable();
            $table->text('service_custom_field_6')->nullable();
            $table->integer('import_batch')->nullable();
            $table->dateTime('import_time')->nullable();
            $table->text('custom_field_1')->nullable();
            $table->text('custom_field_2')->nullable();
            $table->text('custom_field_3')->nullable();
            $table->text('custom_field_4')->nullable();
            $table->string('invoice_token')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'type']);
            $table->index('contact_id');
            $table->index('invoice_no');
            $table->index('ref_no');
            $table->index('transaction_date');
            $table->index('payment_status');

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('set null');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('opening_stock_product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_layouts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('header_text')->nullable();
            $table->string('invoice_heading')->nullable();
            $table->string('sub_heading1')->nullable();
            $table->string('sub_heading2')->nullable();
            $table->string('sub_heading3')->nullable();
            $table->string('sub_total_label')->nullable()->default('Sub Total');
            $table->string('discount_label')->nullable()->default('Discount');
            $table->string('tax_label')->nullable()->default('Tax');
            $table->string('total_label')->nullable()->default('Total');
            $table->string('total_due_label')->nullable()->default('Total Due');
            $table->string('paid_label')->nullable()->default('Paid');
            $table->string('balance_label')->nullable()->default('Balance');
            $table->string('date_label')->nullable()->default('Date');
            $table->string('invoice_no_label')->nullable()->default('Invoice No');
            $table->string('customer_label')->nullable()->default('Customer');
            $table->string('sales_person_label')->nullable()->default('Sales Person');
            $table->boolean('show_business_name')->default(true);
            $table->boolean('show_location_name')->default(true);
            $table->boolean('show_logo')->default(false);
            $table->boolean('show_customer')->default(true);
            $table->boolean('show_sales_person')->default(false);
            $table->boolean('show_barcode')->default(false);
            $table->boolean('show_tax_1')->default(false);
            $table->boolean('show_tax_2')->default(false);
            $table->boolean('show_payments')->default(false);
            $table->boolean('show_sku')->default(false);
            $table->boolean('show_brand')->default(false);
            $table->boolean('show_expiry')->default(false);
            $table->boolean('show_lot_number')->default(false);
            $table->text('footer_text')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->boolean('is_default')->default(false);
            $table->string('cn_heading')->nullable()->default('Credit Note');
            $table->string('cn_no_label')->nullable()->default('CN No');
            $table->string('cn_amount_label')->nullable()->default('CN Amount');
            $table->string('prev_bal_label')->nullable()->default('Previous Balance');
            $table->string('change_return_label')->nullable()->default('Change Return');
            $table->string('highlight_color')->default('#000000');
            $table->string('design')->default('classic');
            $table->text('common_settings')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('invoice_schemes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name');
            $table->enum('scheme_type', ['blank', 'year']);
            $table->string('prefix')->nullable();
            $table->integer('start_number')->nullable()->default(1);
            $table->integer('invoice_count')->default(0);
            $table->integer('total_digits')->nullable()->default(4);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('reference_counts', function (Blueprint $table): void {
            $table->id();
            $table->string('ref_type');
            $table->integer('ref_count')->default(0);
            $table->unsignedBigInteger('business_id')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reference_counts');
        Schema::dropIfExists('invoice_schemes');
        Schema::dropIfExists('invoice_layouts');
    }
};

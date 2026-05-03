<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id');
            $table->decimal('quantity', 22, 4);
            $table->decimal('unit_price_before_discount', 22, 4)->default(0);
            $table->decimal('unit_price', 22, 4);
            $table->enum('line_discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('line_discount_amount', 22, 4)->default(0);
            $table->decimal('unit_price_inc_tax', 22, 4);
            $table->decimal('item_tax', 22, 4)->default(0);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->string('item_type')->default('sell');
            $table->string('sell_line_note')->nullable();
            $table->string('lot_number')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->decimal('purchase_price', 22, 4)->nullable();
            $table->decimal('purchase_price_inc_tax', 22, 4)->nullable();
            $table->decimal('quantity_returned', 22, 4)->default(0);
            $table->decimal('quantity_sold', 22, 4)->default(0);
            $table->decimal('quantity_adjusted', 22, 4)->default(0);
            $table->unsignedBigInteger('parent_item_id')->nullable();
            $table->unsignedBigInteger('sub_unit_id')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
        });

        Schema::create('transaction_payments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('business_id')->nullable();
            $table->boolean('is_return')->default(false);
            $table->decimal('amount', 22, 4);
            $table->string('method')->nullable();
            $table->string('transaction_no')->nullable();
            $table->string('card_transaction_number')->nullable();
            $table->string('card_number')->nullable();
            $table->enum('card_type', ['visa', 'mastercard', 'amex'])->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->dateTime('paid_on');
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_advance')->default(false);
            $table->unsignedBigInteger('payment_for')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('note')->nullable();
            $table->string('document')->nullable();
            $table->string('payment_ref_no')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_payments');
        Schema::dropIfExists('transaction_items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('cash_registers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['open', 'close'])->default('open');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('closing_amount', 22, 4)->default(0);
            $table->decimal('total_card', 22, 4)->default(0);
            $table->decimal('total_bank_transfer', 22, 4)->default(0);
            $table->decimal('total_cheque', 22, 4)->default(0);
            $table->decimal('total_cash', 22, 4)->default(0);
            $table->decimal('total_other', 22, 4)->default(0);
            $table->decimal('opening_amount', 22, 4)->default(0);
            $table->text('closing_note')->nullable();
            $table->integer('denom_100000')->default(0);
            $table->integer('denom_50000')->default(0);
            $table->integer('denom_20000')->default(0);
            $table->integer('denom_10000')->default(0);
            $table->integer('denom_5000')->default(0);
            $table->integer('denom_2000')->default(0);
            $table->integer('denom_1000')->default(0);
            $table->integer('denom_500')->default(0);
            $table->integer('denom_100')->default(0);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('cash_register_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('cash_register_id');
            $table->decimal('amount', 22, 4);
            $table->string('pay_method')->nullable();
            $table->enum('type', ['debit', 'credit']);
            $table->enum('transaction_type', ['initial', 'sell', 'transfer', 'refund']);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamps();

            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });

        Schema::create('document_and_notes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('notable_type');
            $table->unsignedBigInteger('notable_id');
            $table->text('heading')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_private')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_and_notes');
        Schema::dropIfExists('cash_register_transactions');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('expense_categories');
    }
};

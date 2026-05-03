<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('parent_account_type_id')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name');
            $table->string('account_number');
            $table->text('account_details')->nullable();
            $table->unsignedBigInteger('account_type_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('account_type_id')->references('id')->on('account_types')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('account_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->enum('type', ['debit', 'credit']);
            $table->enum('sub_type', ['opening_balance', 'fund_transfer', 'deposit'])->nullable();
            $table->decimal('amount', 22, 4);
            $table->string('reff_no')->nullable();
            $table->dateTime('operation_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('transaction_payment_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('account_types');
    }
};

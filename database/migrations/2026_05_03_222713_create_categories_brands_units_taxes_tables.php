<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('short_code')->nullable();
            $table->integer('parent_id')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('created_by');
            $table->string('slug')->nullable();
            $table->string('category_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->string('actual_name');
            $table->string('short_name');
            $table->boolean('allow_decimal')->default(true);
            $table->integer('base_unit_id')->nullable();
            $table->decimal('base_unit_multiplier', 20, 4)->nullable();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('tax_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->double('amount', 22, 4);
            $table->boolean('is_tax_group')->default(false);
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('group_sub_taxes', function (Blueprint $table): void {
            $table->unsignedBigInteger('group_tax_id');
            $table->unsignedBigInteger('tax_id');

            $table->foreign('group_tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_sub_taxes');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};

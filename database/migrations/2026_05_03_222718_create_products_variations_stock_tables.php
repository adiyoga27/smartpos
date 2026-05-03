<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('business_id');
            $table->enum('type', ['single', 'variable', 'combo'])->default('single');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->boolean('enable_stock')->default(false);
            $table->decimal('alert_quantity', 22, 4)->nullable();
            $table->string('sku')->nullable();
            $table->enum('barcode_type', ['C39', 'C128', 'EAN13', 'EAN8', 'UPCA', 'UPCE'])->default('C128');
            $table->string('image')->nullable();
            $table->text('product_description')->nullable();
            $table->string('weight')->nullable();
            $table->unsignedBigInteger('warranty_id')->nullable();
            $table->boolean('is_inactive')->default(false);
            $table->boolean('not_for_selling')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('variations', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->default('DUMMY');
            $table->string('sub_sku')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->decimal('default_purchase_price', 22, 4)->default(0);
            $table->decimal('dpp_inc_tax', 22, 4)->default(0);
            $table->decimal('profit_percent', 5, 2)->default(0);
            $table->decimal('default_sell_price', 22, 4)->default(0);
            $table->decimal('sell_price_inc_tax', 22, 4)->default(0);
            $table->text('combo_variations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('variation_location_details', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id');
            $table->unsignedBigInteger('location_id');
            $table->decimal('qty_available', 22, 4)->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
        });

        Schema::create('selling_price_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('variation_group_prices', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('variation_id');
            $table->unsignedBigInteger('price_group_id');
            $table->decimal('price_inc_tax', 22, 4);
            $table->timestamps();

            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('price_group_id')->references('id')->on('selling_price_groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variation_group_prices');
        Schema::dropIfExists('selling_price_groups');
        Schema::dropIfExists('variation_location_details');
        Schema::dropIfExists('variations');
        Schema::dropIfExists('products');
    }
};

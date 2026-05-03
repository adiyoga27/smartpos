<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->integer('priority')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('applicable_in_spg')->default(false);
            $table->boolean('applicable_in_cg')->default(false);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
        });

        Schema::create('discount_variations', function (Blueprint $table): void {
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('variation_id');

            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
        });

        Schema::create('printers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name');
            $table->enum('connection_type', ['network', 'windows', 'linux']);
            $table->enum('capability_profile', ['default', 'simple', 'SP2000', 'TEP-200M', 'P822D']);
            $table->string('char_per_line')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('port')->nullable();
            $table->string('path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('warranties', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->integer('duration');
            $table->enum('duration_type', ['days', 'months', 'years']);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('system_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('printers');
        Schema::dropIfExists('discount_variations');
        Schema::dropIfExists('discounts');
    }
};

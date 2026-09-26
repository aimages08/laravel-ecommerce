<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('sku');
            $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('sale_price');

            // Shipping
            $table->string('shipping_type')->default('global')->after('stock'); // global|flat|per_kg|free
            $table->decimal('shipping_fee', 10, 2)->nullable()->after('shipping_type');
            $table->decimal('shipping_rate_per_kg', 10, 2)->nullable()->after('shipping_fee');
            $table->decimal('weight', 8, 2)->nullable()->after('shipping_rate_per_kg');
            $table->decimal('length', 8, 2)->nullable()->after('weight');
            $table->decimal('width', 8, 2)->nullable()->after('length');
            $table->decimal('height', 8, 2)->nullable()->after('width');

            // Store settings
            $table->integer('low_stock_threshold')->default(5)->after('stock');
            $table->boolean('is_new')->default(true)->after('is_featured');
            $table->boolean('is_digital')->default(false)->after('is_new');
            $table->enum('status', ['draft', 'published', 'archived'])->default('published')->after('is_active');

            // SEO
            $table->string('meta_title')->nullable()->after('status');
            $table->string('meta_description', 500)->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'barcode', 'cost_price', 'tax_rate',
                'shipping_type', 'shipping_fee', 'shipping_rate_per_kg',
                'weight', 'length', 'width', 'height',
                'low_stock_threshold', 'is_new', 'is_digital', 'status',
                'meta_title', 'meta_description',
            ]);
        });
    }
};
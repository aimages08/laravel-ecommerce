<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Methods
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // "Stripe", "JazzCash"
            $table->string('code')->unique();              // "stripe", "jazzcash"
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->text('instructions')->nullable();       // shown at checkout
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Credentials (dynamic key/value per method)
        Schema::create('payment_method_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->string('key');                          // admin-defined name
            $table->text('value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['payment_method_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_settings');
        Schema::dropIfExists('payment_methods');
    }
};
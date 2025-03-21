<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new table with desired structure
        Schema::create('addresses_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->enum('type', ['billing', 'shipping'])->default('shipping');
            $table->timestamps();
        });

        // Copy data from old table to new table
        if (Schema::hasTable('addresses')) {
            DB::statement("
                INSERT INTO addresses_new (id, order_id, first_name, last_name, phone, address, city, state, zip, created_at, updated_at)
                SELECT id, order_id, first_name, last_name, phone, street_address, city, state, zip_code, created_at, updated_at
                FROM addresses
            ");
        }

        // Drop old table
        Schema::dropIfExists('addresses');

        // Rename new table to original name
        Schema::rename('addresses_new', 'addresses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create the original table structure
        Schema::create('addresses_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->longText('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->timestamps();
        });

        // Copy data back, skipping the new 'type' column
        if (Schema::hasTable('addresses')) {
            DB::statement("
                INSERT INTO addresses_old (id, order_id, first_name, last_name, phone, street_address, city, state, zip_code, created_at, updated_at)
                SELECT id, order_id, first_name, last_name, phone, address, city, state, zip, created_at, updated_at
                FROM addresses
            ");
        }

        // Drop new table
        Schema::dropIfExists('addresses');

        // Rename old table to original name
        Schema::rename('addresses_old', 'addresses');
    }
};
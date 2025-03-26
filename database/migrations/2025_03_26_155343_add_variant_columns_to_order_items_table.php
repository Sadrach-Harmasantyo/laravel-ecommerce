<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('variant_name')->nullable();
            $table->string('variant_value')->nullable();
            $table->string('sku')->nullable();

            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn([
                'product_variant_id',
                'variant_name',
                'variant_value',
                'sku'
            ]);
        });
    }
};

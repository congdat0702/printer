<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->string('sender_name')->nullable();
            $table->string('shipping_unit')->nullable();
            $table->string('cod')->nullable();
            $table->string('product_name')->nullable();
            $table->string('payer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->dropColumn(['sender_name', 'shipping_unit', 'cod', 'product_name', 'payer']);
        });
    }
};

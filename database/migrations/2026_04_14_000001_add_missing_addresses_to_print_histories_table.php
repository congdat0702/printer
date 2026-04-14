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
            if (!Schema::hasColumn('print_histories', 'recipient_address')) {
                $table->string('recipient_address')->nullable();
            }
            if (!Schema::hasColumn('print_histories', 'sender_phone')) {
                $table->string('sender_phone')->nullable();
            }
            if (!Schema::hasColumn('print_histories', 'sender_address')) {
                $table->text('sender_address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->dropColumn(['recipient_address', 'sender_phone', 'sender_address']);
        });
    }
};

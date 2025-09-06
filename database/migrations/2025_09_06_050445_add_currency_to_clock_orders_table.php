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
        Schema::table('clock_orders', function (Blueprint $table) {
            // Add currency column after total_price (adjust as needed)
            $table->string('currency', 3)->default('KES')->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clock_orders', function (Blueprint $table) {
            $table->dropColumn('currency');

        });
    }
};

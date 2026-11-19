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
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('network', 32)->nullable()->change();
            $table->string('payment_method', 32)->nullable()->after('network');
            $table->string('payment_number', 80)->nullable()->after('payment_method');
            $table->string('payer_name', 80)->nullable()->after('payment_number');
            $table->string('currency', 10)->nullable()->default('USDT')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_number', 'payer_name', 'currency']);
        });
    }
};

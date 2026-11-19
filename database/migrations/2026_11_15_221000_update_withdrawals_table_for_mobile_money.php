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
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('network', 32)->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('phone')->nullable()->after('wallet');
            $table->string('payout_method', 32)->nullable()->default('mobile_money')->after('phone');
            $table->string('account_name')->nullable()->after('payout_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['phone', 'payout_method', 'account_name']);
        });
    }
};

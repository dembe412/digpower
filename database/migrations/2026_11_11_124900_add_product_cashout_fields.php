<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('cashed_out_amount', 18, 6)->default(0)->after('daily_income');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreignId('purchase_id')->nullable()->after('user_id')->constrained('purchases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('cashed_out_amount');
        });
    }
};

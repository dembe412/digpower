<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_role', 32)->nullable()->after('is_super_admin');
        });

        // Seed existing super admins and admins
        DB::table('users')->where('is_super_admin', true)->update(['admin_role' => 'super_admin']);
        DB::table('users')->where('is_admin', true)->where('is_super_admin', false)->update(['admin_role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_role');
        });
    }
};

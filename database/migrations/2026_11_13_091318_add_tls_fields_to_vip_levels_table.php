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
        Schema::table('vip_levels', function (Blueprint $table) {
            $table->decimal('price', 18, 2)->default(0)->after('name');
            $table->decimal('recharge_requirement', 18, 2)->default(0)->after('price');
            $table->unsignedInteger('required_level_1_members')->default(0)->after('recharge_requirement');
            $table->unsignedInteger('required_level_2_members')->default(0)->after('required_level_1_members');
            $table->unsignedInteger('required_level_3_members')->default(0)->after('required_level_2_members');
            $table->decimal('monthly_salary', 18, 6)->default(0)->after('target_yield_pct');
            $table->unsignedInteger('duration_days')->default(30)->after('monthly_salary');
            $table->string('image_path')->nullable()->after('duration_days');
            $table->string('tagline', 500)->nullable()->after('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('tagline');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vip_levels', function (Blueprint $table) {
            $table->dropColumn([
                'price', 'recharge_requirement', 'required_level_1_members',
                'required_level_2_members', 'required_level_3_members', 'monthly_salary',
                'duration_days', 'image_path', 'tagline', 'sort_order', 'is_active',
            ]);
        });
    }
};

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
        Schema::create('vip_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vip_level_id')->constrained('vip_levels')->cascadeOnDelete();
            $table->enum('status', ['pending', 'active', 'rejected', 'completed'])->default('pending');
            $table->enum('payment_method', ['manual', 'wallet'])->default('manual');
            $table->string('transaction_id')->nullable();
            $table->decimal('principal', 18, 2);
            $table->decimal('monthly_salary', 18, 6);
            $table->unsignedInteger('duration_days');
            $table->decimal('recharge_requirement', 18, 2);
            $table->unsignedInteger('required_level_1_members')->default(0);
            $table->unsignedInteger('required_level_2_members')->default(0);
            $table->unsignedInteger('required_level_3_members')->default(0);
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('matures_at')->nullable();
            $table->decimal('salary_paid', 18, 6)->default(0);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['vip_level_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_purchases');
    }
};

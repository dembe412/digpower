<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stake_rounds', function (Blueprint $t) {
            $t->id();
            $t->enum('status', ['open', 'running', 'settled', 'void'])->default('open')->index();
            $t->timestamp('opens_at');
            $t->timestamp('starts_at');
            $t->unsignedBigInteger('started_ms')->nullable();
            $t->timestamp('ended_at')->nullable();
            $t->string('server_seed_hash', 64);          // committed BEFORE any stake is placed
            $t->text('server_seed');   // encrypted at rest; only exposed after settlement
            $t->string('client_seed', 64)->nullable();   // derived from player stakes at start
            $t->unsignedInteger('volatility_bp');
            $t->unsignedInteger('tick_ms');
            $t->unsignedInteger('end_tick')->nullable();
            $t->decimal('pool_total', 18, 6)->default(0);
            $t->decimal('cap_total', 18, 6)->default(0);
            $t->decimal('paid_total', 18, 6)->default(0);
            $t->decimal('house_take', 18, 6)->default(0);
            $t->unsignedInteger('players')->default(0);
            $t->string('end_reason', 24)->nullable();    // cap_reached | timeout | void_min_players
            $t->timestamps();
        });

        Schema::create('stakes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stake_round_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('side', ['usd', 'ugx']);            // usd = bet USD strengthens vs UGX; ugx = the opposite
            $t->decimal('amount', 18, 6);
            $t->enum('status', ['active', 'cashout_requested', 'cashed', 'lost', 'refunded'])->default('active');
            $t->unsignedInteger('cashout_tick')->nullable();
            $t->unsignedBigInteger('cashout_multiplier_ppm')->nullable();
            $t->decimal('payout', 18, 6)->default(0);
            $t->timestamp('cashed_at')->nullable();
            $t->timestamps();
            $t->unique(['stake_round_id', 'user_id']);
            $t->index(['user_id', 'created_at']);
        });

        Schema::create('stake_profiles', function (Blueprint $t) {
            $t->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $t->timestamp('adult_confirmed_at')->nullable();
            $t->date('date_of_birth')->nullable();
            $t->decimal('daily_limit', 18, 6);
            $t->decimal('pending_daily_limit', 18, 6)->nullable();
            $t->timestamp('pending_limit_applies_at')->nullable();
            $t->timestamp('self_excluded_until')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stake_profiles');
        Schema::dropIfExists('stakes');
        Schema::dropIfExists('stake_rounds');
    }
};

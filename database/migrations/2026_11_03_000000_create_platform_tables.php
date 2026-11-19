<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('phone')->nullable()->unique()->after('email');
            $t->string('invite_code', 12)->unique()->nullable();
            $t->foreignId('referrer_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('pin_hash')->nullable();
            $t->text('totp_secret')->nullable();
            $t->boolean('totp_enabled')->default(false);
            $t->boolean('is_admin')->default(false);
            $t->boolean('is_active')->default(true);
            $t->boolean('show_in_public_log')->default(false); // opt-in only
            $t->string('locale', 8)->default('en');
            $t->decimal('invest_balance', 18, 6)->default(0);
            $t->decimal('commission_balance', 18, 6)->default(0);
            $t->timestamp('terms_accepted_at')->nullable();
            $t->string('last_login_ip', 45)->nullable();
        });

        Schema::table('users', function (Blueprint $t) {
            $t->string('email')->nullable()->change();
        });

        Schema::create('vip_levels', function (Blueprint $t) {
            $t->id();
            $t->unsignedTinyInteger('level')->unique();
            $t->string('name');
            $t->decimal('min_deposit', 18, 2);
            $t->decimal('max_deposit', 18, 2)->nullable();
            $t->decimal('target_yield_pct', 6, 2)->default(0); // indicative only, NOT guaranteed
            $t->unsignedInteger('window_days')->nullable();    // null = no expiry
            $t->json('rebate_rates')->nullable();
            $t->timestamps();
        });

        Schema::create('transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('wallet', ['invest', 'commission']);
            $t->string('type', 32);
            $t->decimal('amount', 18, 6);        // signed
            $t->decimal('balance_after', 18, 6);
            $t->nullableMorphs('ref');
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'created_at']);
        });

        Schema::create('deposits', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('network', 12);
            $t->decimal('amount', 18, 6);
            $t->string('address')->nullable();
            $t->string('txid')->nullable()->unique();
            $t->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $t->timestamp('confirmed_at')->nullable();
            $t->timestamp('window_ends_at')->nullable();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('note')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'status']);
        });

        Schema::create('withdrawals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('wallet', ['invest', 'commission']);
            $t->string('network', 12);
            $t->string('address');
            $t->decimal('amount', 18, 6);
            $t->decimal('fee', 18, 6)->default(0);
            $t->decimal('net_amount', 18, 6);
            $t->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $t->string('txid')->nullable();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('note')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'status']);
        });

        Schema::create('yield_batches', function (Blueprint $t) {
            $t->id();
            $t->date('period_date')->unique();
            $t->decimal('gross_revenue', 18, 6);   // real revenue the operator reports for the period
            $t->decimal('fee_pct', 5, 2)->default(10);
            $t->decimal('distributed', 18, 6)->default(0);
            $t->decimal('total_hashrate', 18, 6)->default(0);
            $t->string('proof_url')->nullable();   // link to revenue statement / audit evidence
            $t->enum('status', ['draft', 'distributed'])->default('draft');
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('mining_records', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('yield_batch_id')->constrained()->cascadeOnDelete();
            $t->decimal('hashrate', 18, 6);
            $t->decimal('amount', 18, 6);
            $t->enum('status', ['to_claim', 'claimed', 'expired'])->default('to_claim');
            $t->timestamp('expires_at');
            $t->timestamp('claimed_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'yield_batch_id']);
            $t->index(['user_id', 'status']);
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->text('body')->nullable();
            $t->enum('kind', ['ticker', 'event', 'notice'])->default('ticker');
            $t->boolean('is_active')->default(true);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->timestamps();
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->string('question');
            $t->text('answer');
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->json('value')->nullable();
            $t->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('subject');
            $t->enum('status', ['open', 'answered', 'closed'])->default('open');
            $t->timestamps();
        });

        Schema::create('support_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->boolean('from_staff')->default(false);
            $t->text('body');
            $t->timestamps();
        });

        Schema::create('user_notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('body')->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 64);
            $t->nullableMorphs('subject');
            $t->json('meta')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'user_notifications', 'support_messages', 'support_tickets', 'settings', 'faqs', 'announcements', 'mining_records', 'yield_batches', 'withdrawals', 'deposits', 'transactions', 'vip_levels'] as $tbl) {
            Schema::dropIfExists($tbl);
        }
    }
};

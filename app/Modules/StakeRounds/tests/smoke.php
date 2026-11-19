<?php

// Plain-PHP smoke test (no PHPUnit needed). Usage: php app/Modules/StakeRounds/tests/smoke.php
putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';
putenv('DB_DATABASE=/tmp/smoke.sqlite');
$_ENV['DB_DATABASE'] = '/tmp/smoke.sqlite';
@unlink('/tmp/smoke.sqlite');
touch('/tmp/smoke.sqlite');

use App\Models\Deposit;
use App\Models\User;
use App\Modules\StakeRounds\Http\Controllers\StakeController;
use App\Modules\StakeRounds\Models\Stake;
use App\Modules\StakeRounds\Models\StakeProfile;
use App\Modules\StakeRounds\Models\StakeRound;
use App\Modules\StakeRounds\Services\RoundEngine;
use App\Modules\StakeRounds\Services\StakeService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

require __DIR__.'/../../../../vendor/autoload.php';
$app = require __DIR__.'/../../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

$pass = 0;
$fail = 0;
function check(string $name, bool $ok, string $extra = ''): void
{
    global $pass, $fail;
    $ok ? $pass++ : $fail++;
    echo ($ok ? '  PASS  ' : '  FAIL  ').$name.($ok || ! $extra ? '' : "  => $extra")."\n";
}
function throws(callable $f): bool
{
    try {
        $f();

        return false;
    } catch (ValidationException) {
        return true;
    }
}

$engine = app(RoundEngine::class);
$svc = app(StakeService::class);
$wallet = app(WalletService::class);
$mk = function (string $n) use ($wallet, $svc) {
    $u = User::create(['name' => $n, 'email' => "$n@t.io", 'password' => 'secret123', 'invite_code' => (string) random_int(100000, 999999)]);
    $wallet->credit($u, 'invest', '1000', 'adjustment');
    $svc->confirmAdult($u, '1990-01-01');
    StakeProfile::where('user_id', $u->id)->update(['daily_limit' => 5000]);

    return $u;
};
$bal = fn ($u) => bcadd((string) $u->fresh()->invest_balance, '0', 6);
[$A, $B, $C] = [$mk('a'), $mk('b'), $mk('c')];
$t0 = Carbon::parse('2026-11-14 12:00:00.000');
Carbon::setTestNow($t0);

echo "Round 1: normal round ending at the 90% cap\n";
$r = $engine->advance($t0);
check('round opens with committed seed hash', $r->status === 'open' && strlen($r->server_seed_hash) === 64);
check('seed hash = sha256(server_seed)', hash('sha256', $r->server_seed) === $r->server_seed_hash);
$svc->enter($A, 'usd', '100');
$svc->enter($B, 'ugx', '100');
$svc->enter($C, 'usd', '100');
check('stake debited from wallet', $bal($A) === '900.000000');
check('double stake rejected', throws(fn () => $svc->enter($A, 'ugx', '10')));
check('overdraw rejected', throws(fn () => $svc->enter($mk('z'), 'usd', '501')));
$tStart = $t0->copy()->addSeconds(16);
Carbon::setTestNow($tStart);
$r = $engine->advance($tStart);
check('round starts; pool=300, cap=270', $r->status === 'running' && (string) $r->pool_total === '300.000000' && (string) $r->cap_total === '270.000000', json_encode($r->only('status', 'pool_total', 'cap_total')));
check('staking closed after start', throws(fn () => $svc->enter($mk('late'), 'usd', '10')));

Carbon::setTestNow($tStart->copy()->addMilliseconds(600));            // tick 1
$svc->cashOut($A);
$svc->cashOut($B);
check('cash-out is NOT paid instantly (batched)', $bal($A) === '900.000000');
Carbon::setTestNow($tStart->copy()->addMilliseconds(1200));
$engine->advance();   // tick 2: batch for tick 1 closes
$m = $engine->multipliers($r->fresh(), 1);
$expA = bcdiv(bcmul('100', (string) $m['usd'], 6), '1000000', 6);
$expB = bcdiv(bcmul('100', (string) $m['ugx'], 6), '1000000', 6);
check('A paid stake x USD multiplier', (string) Stake::where('user_id', $A->id)->first()->payout === $expA, "exp $expA");
check('B paid stake x UGX multiplier', (string) Stake::where('user_id', $B->id)->first()->payout === $expB, "exp $expB");
check('round still running (cashed < 90%)', $r->fresh()->status === 'running');
Carbon::setTestNow($tStart->copy()->addMilliseconds(1300));
$svc->cashOut($C);   // tick 2
Carbon::setTestNow($tStart->copy()->addMilliseconds(1700));
$engine->advance();   // tick 3: batch breaches cap -> trimmed, round ends
$r = $r->fresh();
check('round settled by cap', $r->status === 'settled' && $r->end_reason === 'cap_reached', $r->status.'/'.$r->end_reason);
check('paid_total <= cap and within 0.00001 of it', bccomp((string) $r->paid_total, '270', 6) <= 0 && bccomp(bcsub('270', (string) $r->paid_total, 6), '0.00001', 6) <= 0, (string) $r->paid_total);
check('house keeps >= 10% of pool', bccomp((string) $r->house_take, '30', 6) >= 0, (string) $r->house_take);
$sum = bcadd(bcadd($bal($A), $bal($B), 6), $bal($C), 6);
check('solvency: players lost exactly the house take', $sum === bcsub('3000', (string) $r->house_take, 6), $sum);
check('next round opened automatically', StakeRound::where('status', 'open')->count() === 1);

echo "Provably fair: independent re-computation\n";
check('revealed seed matches published hash', hash('sha256', $r->server_seed) === $r->server_seed_hash);
$series = [1000000];
for ($k = 1; $k <= $r->end_tick; $k++) {
    $h = hash_hmac('sha256', "{$r->client_seed}:{$r->id}:$k", $r->server_seed, true);
    $x = unpack('N', substr($h, 0, 4))[1];
    $step = ($x % (2 * $r->volatility_bp + 1)) - $r->volatility_bp;
    $series[$k] = max(1000, $series[$k - 1] + intdiv($series[$k - 1] * $step, 10000));
}
$api = app(StakeController::class)->verify($r)->getData(true);
check('API series == independent series', $api['index_series_ppm'] === $series);
check('client seed derived from stakes', $r->client_seed === hash('sha256', Stake::where('stake_round_id', $r->id)->orderBy('id')->get()->map(fn ($s) => "{$s->id}:{$s->user_id}:{$s->side}:{$s->amount}")->implode('|')));

echo "Round 2: too few players -> void + refund\n";
$r2 = StakeRound::where('status', 'open')->first();
$before = $bal($A);
$svc->enter($A, 'usd', '50');
check('stake taken', $bal($A) === bcsub($before, '50', 6));
$tt = Carbon::now()->addSeconds(16);
Carbon::setTestNow($tt);
$engine->advance($tt);
check('round void, stake refunded', $r2->fresh()->status === 'void' && $bal($A) === $before, $r2->fresh()->status.' '.$bal($A));

echo "Round 3: nobody cashes out -> timeout, cap still enforced\n";
$r3 = StakeRound::where('status', 'open')->first();
$svc->enter($A, 'usd', '100');
$svc->enter($B, 'ugx', '100');
$tt = Carbon::now()->addSeconds(16);
Carbon::setTestNow($tt);
$engine->advance($tt);
Carbon::setTestNow($tt->copy()->addSeconds(121));
$engine->advance();
$r3 = $r3->fresh();
check('timed out + auto cash-out capped at 90%', $r3->status === 'settled' && $r3->end_reason === 'timeout' && bccomp((string) $r3->paid_total, '180', 6) <= 0 && bccomp((string) $r3->paid_total, '179.99', 6) >= 0, $r3->end_reason.' '.$r3->paid_total);

echo "Player protections\n";
$d = User::create(['name' => 'd', 'email' => 'd@t.io', 'password' => 'secret123', 'invite_code' => '999991']);
$wallet->credit($d, 'invest', '100', 'adjustment');
check('stake blocked until 18+ confirmed', throws(fn () => $svc->enter($d, 'usd', '10')));
check('under-18 rejected', throws(fn () => $svc->confirmAdult($d, now()->subYears(17)->toDateString())));
$p = $svc->profile($A);
StakeProfile::where('user_id', $A->id)->update(['daily_limit' => 100]);
$svc->setDailyLimit($A, '50');
check('lowering limit applies immediately', (string) $svc->profile($A)->daily_limit === '50.000000');
$svc->setDailyLimit($A, '500');
$pp = $svc->profile($A);
check('raising limit is delayed 24h', (string) $pp->daily_limit === '50.000000' && $pp->pending_limit_applies_at !== null);
$ex = $mk('ex');
$svc->selfExclude($ex, 7);
check('self-excluded user cannot stake', throws(fn () => $svc->enter($ex, 'usd', '10')));
$svc->selfExclude($ex, 1);
check('exclusion cannot be shortened', $svc->profile($ex)->self_excluded_until->gt(now()->addDays(6)));
$lock = $mk('lock');
Deposit::create(['user_id' => $lock->id, 'network' => 'TRC20', 'amount' => 1000, 'status' => 'confirmed', 'confirmed_at' => now(), 'window_ends_at' => now()->addDays(100)]);
check('locked deposit cannot be staked', throws(fn () => $svc->enter($lock, 'usd', '10')));

echo "HTTP layer (real kernel + Sanctum token)\n";
Carbon::setTestNow();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$call = function ($method, $uri, $tok = null, $body = []) use ($kernel, $app) {
    $req = Request::create($uri, $method, $body, [], [], ['HTTP_ACCEPT' => 'application/json'] + ($tok ? ['HTTP_AUTHORIZATION' => "Bearer $tok"] : []));
    $app['auth']->forgetGuards();

    return $kernel->handle($req);
};
$tok = $A->createToken('t')->plainTextToken;
check('unauthenticated -> 401', $call('GET', '/api/stake/current')->getStatusCode() === 401);
$res = $call('GET', '/api/stake/current', $tok);
$j = json_decode($res->getContent(), true);
check('GET /api/stake/current -> 200 with round', $res->getStatusCode() === 200 && isset($j['round']) && array_key_exists('notice', $j), substr($res->getContent(), 0, 200));
check('server_seed never exposed while round is live', ! str_contains($res->getContent(), '"server_seed"'));
$res = $call('POST', '/api/stake/enter', $tok, ['side' => 'sideways', 'amount' => 5]);
check('invalid side -> 422', $res->getStatusCode() === 422);
$res = $call('GET', '/api/stake/rounds', $tok);
check('GET /api/stake/rounds -> 200', $res->getStatusCode() === 200);
$res = $call('GET', "/api/stake/rounds/{$r->id}/verify", $tok);
check('GET verify -> 200 & reveals seed', $res->getStatusCode() === 200 && str_contains($res->getContent(), $r->server_seed));
$res = $call('GET', '/api/admin/stake/summary', $tok);
check('admin endpoint forbidden for normal user (403)', $res->getStatusCode() === 403);

echo "\n$pass passed, $fail failed\n";
exit($fail ? 1 : 0);

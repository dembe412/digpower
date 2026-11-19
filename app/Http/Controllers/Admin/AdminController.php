<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Deposit;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\VipLevel;
use App\Models\VipPurchase;
use App\Models\Withdrawal;
use App\Models\YieldBatch;
use App\Services\DepositService;
use App\Services\PriceService;
use App\Services\ReferralService;
use App\Services\WalletService;
use App\Services\YieldService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class AdminController extends Controller
{
    /** Solvency view: what the platform owes users vs. what it has received. */
    public function overview(Request $r)
    {
        abort_unless($r->user()->canAccessTransactions(), 403, 'Customer managers do not have access to financial overview.');

        $received = (string) (Deposit::where('status', 'confirmed')->sum('amount') ?: '0');
        $paid = (string) (Withdrawal::where('status', 'completed')->sum('net_amount') ?: '0');
        $owed = bcadd((string) User::sum('invest_balance'), (string) User::sum('commission_balance'), 6);

        return response()->json([
            'users' => User::where('is_admin', false)->count(),
            'deposits_confirmed' => $received,
            'withdrawals_completed' => $paid,
            'user_balances_owed' => $owed,
            'pending_withdrawals' => (string) (Withdrawal::whereIn('status', ['pending', 'approved'])->sum('amount') ?: '0'),
            'pending_deposits' => Deposit::where('status', 'pending')->count(),
            'net_position' => bcsub(bcsub($received, $paid, 6), $owed, 6), // negative => liabilities exceed funds held
        ]);
    }

    public function prices(PriceService $prices)
    {
        return response()->json([
            'prices' => $prices->all(),
            'overrides' => Setting::get('price_overrides', []),
        ]);
    }

    public function savePrices(Request $r, PriceService $prices)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot modify prices.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can modify prices.');

        $validated = $r->validate(['overrides' => ['required', 'array']]);
        $allowedSymbols = config('platform.prices.pairs');
        $allowedExchanges = ['binance', 'okx', 'huobi', 'coinbase'];
        $overrides = [];

        foreach ($validated['overrides'] as $symbol => $exchanges) {
            abort_unless(in_array($symbol, $allowedSymbols, true) && is_array($exchanges), 422, 'Invalid price symbol.');
            foreach ($exchanges as $exchange => $value) {
                abort_unless(in_array($exchange, $allowedExchanges, true) && is_array($value), 422, 'Invalid exchange.');
                $clean = validator($value, [
                    'enabled' => ['boolean'],
                    'price' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
                    'change_pct' => ['nullable', 'numeric', 'min:-100', 'max:1000'],
                ])->validate();
                $overrides[$symbol][$exchange] = [
                    'enabled' => (bool) ($clean['enabled'] ?? false),
                    'price' => isset($clean['price']) ? (float) $clean['price'] : null,
                    'change_pct' => isset($clean['change_pct']) ? (float) $clean['change_pct'] : 0,
                ];
                Setting::put("price_override:{$symbol}:{$exchange}", $overrides[$symbol][$exchange]);
            }
        }

        Setting::put('price_overrides', $overrides);
        $prices->clearCache();
        AuditLog::record($r->user()->id, 'prices.updated', null, ['symbols' => array_keys($overrides)]);

        return response()->json(['message' => 'Price controls saved.', 'overrides' => $overrides]);
    }

    // ---- Deposits
    public function deposits(Request $r)
    {
        abort_unless($r->user()->canAccessTransactions(), 403, 'Access denied: Customer managers do not have access to transactions.');

        return Deposit::with('user:id,email,phone')->when($r->status, fn ($q) => $q->where('status', $r->status))->latest()->paginate(30);
    }

    public function confirmDeposit(Request $r, Deposit $deposit, DepositService $svc)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot execute transactional operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can confirm deposits.');
        abort_unless($deposit->txid, 422, 'No transaction id submitted for this deposit.');

        return response()->json($svc->confirm($deposit, $r->user()->id));
    }

    public function rejectDeposit(Request $r, Deposit $deposit)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot execute transactional operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can reject deposits.');
        abort_unless($deposit->status === 'pending', 422);
        $deposit->update(['status' => 'rejected', 'reviewed_by' => $r->user()->id, 'note' => $r->input('note')]);
        AuditLog::record($r->user()->id, 'deposit.rejected', $deposit);

        return response()->json($deposit);
    }

    // ---- Withdrawals
    public function withdrawals(Request $r)
    {
        abort_unless($r->user()->canAccessTransactions(), 403, 'Access denied: Customer managers do not have access to transactions.');

        return Withdrawal::with('user:id,email,phone')->when($r->status, fn ($q) => $q->where('status', $r->status))->latest()->paginate(30);
    }

    public function approveWithdrawal(Request $r, Withdrawal $withdrawal)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot execute transactional operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can approve withdrawals.');
        abort_unless($withdrawal->status === 'pending', 422);
        $withdrawal->update(['status' => 'approved', 'reviewed_by' => $r->user()->id]);
        AuditLog::record($r->user()->id, 'withdrawal.approved', $withdrawal);

        return response()->json($withdrawal);
    }

    public function completeWithdrawal(Request $r, Withdrawal $withdrawal)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot execute transactional operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can complete withdrawals.');
        $d = $r->validate(['txid' => 'nullable|string|max:120']);
        abort_unless($withdrawal->status === 'approved', 422, 'Approve first.');
        $withdrawal->update(['status' => 'completed', 'txid' => $d['txid'] ?? null, 'reviewed_by' => $r->user()->id]);
        UserNotification::create(['user_id' => $withdrawal->user_id, 'title' => 'Withdrawal sent', 'body' => "{$withdrawal->net_amount} UGX sent."]);
        AuditLog::record($r->user()->id, 'withdrawal.completed', $withdrawal);

        return response()->json($withdrawal);
    }

    public function rejectWithdrawal(Request $r, Withdrawal $withdrawal, WalletService $wallet)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot execute transactional operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can reject withdrawals.');
        $d = $r->validate(['note' => 'required|string|max:255']);
        DB::transaction(function () use ($withdrawal, $wallet, $d, $r) {
            $w = Withdrawal::whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();
            if (! in_array($w->status, ['pending', 'approved'], true)) {
                throw new RuntimeException('Already processed');
            }
            $w->update(['status' => 'rejected', 'note' => $d['note'], 'reviewed_by' => $r->user()->id]);
            $wallet->credit($w->user, $w->wallet, (string) $w->amount, 'withdrawal_refund', $w);
            UserNotification::create(['user_id' => $w->user_id, 'title' => 'Withdrawal rejected', 'body' => $d['note']]);
        });
        AuditLog::record($r->user()->id, 'withdrawal.rejected', $withdrawal);

        return response()->json($withdrawal->refresh());
    }

    // ---- Yield (earnings)
    public function yieldBatches(Request $r)
    {
        abort_unless($r->user()->canAccessTransactions(), 403, 'Access denied: Customer managers do not have access to transactions.');

        return YieldBatch::latest('period_date')->paginate(30);
    }

    public function createYieldBatch(Request $r)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot execute operational modifications.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can create yield batches.');
        $d = $r->validate([
            'period_date' => 'required|date|unique:yield_batches,period_date',
            'gross_revenue' => 'required|numeric|min:0.000001',
            'fee_pct' => 'required|numeric|min:0|max:50',
            'proof_url' => 'required|url', // evidence of the revenue being distributed
        ]);
        $b = YieldBatch::create($d + ['created_by' => $r->user()->id]);
        AuditLog::record($r->user()->id, 'yield.created', $b);

        return response()->json($b, 201);
    }

    public function distributeYield(Request $r, YieldBatch $batch, YieldService $svc)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot execute operational modifications.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can distribute yield.');
        try {
            $b = $svc->distribute($batch);
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }
        AuditLog::record($r->user()->id, 'yield.distributed', $b);

        return response()->json($b);
    }

    // ---- Users / Clients (TLS-style client insights with product filtering)
    public function users(Request $r)
    {
        $query = User::where('is_admin', false);

        if ($r->filled('q')) {
            $q = $r->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $filter = $r->input('filter', 'all');
        if ($filter === 'with_product') {
            $query->whereHas('purchases', fn ($p) => $p->where('status', 'active'));
        } elseif ($filter === 'without_product') {
            $query->whereDoesntHave('purchases', fn ($p) => $p->where('status', 'active'));
        }

        $isCustomerManager = $r->user()->isCustomerManager();
        $totalClients = User::where('is_admin', false)->count();
        $clientsWithProduct = User::where('is_admin', false)->whereHas('purchases', fn ($p) => $p->where('status', 'active'))->count();
        $clientsWithoutProduct = max(0, $totalClients - $clientsWithProduct);
        $totalActiveValue = $isCustomerManager ? null : (string) (Purchase::where('status', 'active')->sum('principal') ?: '0');

        $paginated = $query->with(['purchases' => fn ($p) => $p->where('status', 'active')->with('product')])
            ->latest()
            ->paginate(30);

        $paginated->getCollection()->transform(function (User $client) use ($isCustomerManager) {
            $activePurchases = $client->purchases->where('status', 'active');
            $earned = $isCustomerManager ? null : $activePurchases->sum(fn ($p) => (float) $p->earnedAmount());
            $principal = $isCustomerManager ? null : $activePurchases->sum(fn ($p) => (float) $p->principal);

            return [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'is_active' => (bool) $client->is_active,
                'archived_at' => $client->archived_at?->toIso8601String(),
                'created_at' => $client->created_at?->toIso8601String(),
                'invest_balance' => $isCustomerManager ? null : (string) $client->invest_balance,
                'commission_balance' => $isCustomerManager ? null : (string) $client->commission_balance,
                'total_balance' => $isCustomerManager ? null : bcadd((string) $client->invest_balance, (string) $client->commission_balance, 2),
                'has_active_product' => $activePurchases->isNotEmpty(),
                'active_products_count' => $activePurchases->count(),
                'active_principal' => $principal !== null ? (string) $principal : null,
                'total_earned' => $earned !== null ? (string) $earned : null,
                'active_products' => $activePurchases->map(function ($p) use ($isCustomerManager) {
                    $daysLeft = $p->duration_days && $p->activated_at ? max(0, $p->duration_days - (int) $p->activated_at->diffInDays(now())) : null;

                    return [
                        'id' => $p->id,
                        'product_id' => $p->product_id,
                        'product_name' => $p->product?->name ?? 'Mining Equipment',
                        'product_code' => $p->product?->code,
                        'image_url' => $p->product?->imageUrl(),
                        'principal' => $isCustomerManager ? null : (string) $p->principal,
                        'daily_income' => (string) $p->daily_income,
                        'duration_days' => $p->duration_days,
                        'days_left' => $daysLeft,
                        'earned_so_far' => $isCustomerManager ? null : (string) $p->earnedAmount(),
                        'activated_at' => $p->activated_at?->toFormattedDateString(),
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'clients' => $paginated,
            'stats' => [
                'total' => $totalClients,
                'with_product' => $clientsWithProduct,
                'without_product' => $clientsWithoutProduct,
                'active_value' => $totalActiveValue,
            ],
            'current_filter' => $filter,
        ]);
    }

    public function assignProduct(Request $r, User $user, ReferralService $referrals)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot assign products.');
        abort_if($user->is_admin, 422, 'Cannot assign products to administrator accounts.');

        $data = $r->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        $purchase = DB::transaction(function () use ($user, $product, $r, $referrals) {
            $purchase = $user->purchases()->create([
                'product_id' => $product->id,
                'status' => 'active',
                'payment_method' => 'admin_assigned',
                'transaction_id' => 'ADM-'.strtoupper(Str::random(10)),
                'principal' => $product->cost_price,
                'daily_income' => $product->daily_income,
                'duration_days' => $product->duration_days,
                'requested_at' => now(),
                'activated_at' => now(),
                'matures_at' => $product->duration_days ? now()->addDays($product->duration_days) : null,
                'reviewed_by' => $r->user()->id,
                'note' => 'Assigned by staff: '.($r->user()->name ?: 'Admin'),
            ]);

            $referrals->payPurchaseRewards($purchase);

            return $purchase;
        });

        AuditLog::record($r->user()->id, 'client.product_assigned', $purchase, [
            'client_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => "{$product->name} assigned to {$user->name}.",
            'purchase' => $purchase->load('product'),
        ], 201);
    }

    public function admins()
    {
        return response()->json(User::query()
            ->where('is_admin', true)
            ->select(['id', 'name', 'email', 'phone', 'is_admin', 'is_active', 'is_super_admin', 'admin_role', 'created_at', 'last_login_ip'])
            ->latest()
            ->get()
            ->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'phone' => $admin->phone,
                    'is_active' => (bool) $admin->is_active,
                    'is_super_admin' => (bool) $admin->is_super_admin,
                    'admin_role' => $admin->effectiveAdminRole(),
                    'role_label' => match ($admin->effectiveAdminRole()) {
                        'super_admin' => 'Super Administrator',
                        'admin' => 'Operations Admin',
                        'manager' => 'Customer Manager',
                        default => 'Staff',
                    },
                    'created_at' => $admin->created_at?->toIso8601String(),
                ];
            }));
    }

    public function createAdmin(Request $r)
    {
        $d = $r->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190', 'required_without:phone', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'required_without:email', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'in:admin,manager'],
        ]);

        $role = $d['role'] ?? 'admin';

        $admin = User::create([
            'name' => $d['name'],
            'email' => $d['email'] ?? null,
            'phone' => $d['phone'] ?? null,
            'password' => Hash::make($d['password']),
            'invite_code' => $this->uniqueInviteCode(),
            'terms_accepted_at' => now(),
            'is_active' => true,
        ]);
        $admin->forceFill([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role' => $role,
        ])->save();
        AuditLog::record($r->user()->id, 'admin.created', $admin, [
            'created_by' => $r->user()->id,
            'role' => $role,
        ]);

        return response()->json([
            'message' => 'Administrator account created successfully.',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'is_admin' => true,
                'is_super_admin' => false,
                'admin_role' => $admin->admin_role,
                'is_active' => $admin->is_active,
                'invite_code' => $admin->invite_code,
            ],
        ], 201);
    }

    public function updateAdmin(Request $r, User $admin)
    {
        abort_unless($admin->is_admin, 404, 'User is not an administrator.');
        abort_if($admin->is_super_admin && $admin->id !== $r->user()->id, 403, 'Another super administrator account cannot be edited.');

        $d = $r->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => ['sometimes', 'nullable', 'email', 'max:190', "unique:users,email,{$admin->id}"],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', "unique:users,phone,{$admin->id}"],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'nullable', 'in:admin,manager'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $updates = [];
        if (array_key_exists('name', $d)) {
            $updates['name'] = $d['name'];
        }
        if (array_key_exists('email', $d)) {
            $updates['email'] = $d['email'];
        }
        if (array_key_exists('phone', $d)) {
            $updates['phone'] = $d['phone'];
        }
        if (! empty($d['password'])) {
            $updates['password'] = Hash::make($d['password']);
        }
        if (isset($d['is_active']) && ! $admin->is_super_admin) {
            $updates['is_active'] = (bool) $d['is_active'];
        }

        $admin->update($updates);
        if (! $admin->is_super_admin && ! empty($d['role'])) {
            $admin->forceFill(['admin_role' => $d['role']])->save();
        }
        AuditLog::record($r->user()->id, 'admin.updated', $admin);

        return response()->json([
            'message' => 'Administrator account updated.',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'is_active' => $admin->is_active,
                'admin_role' => $admin->effectiveAdminRole(),
            ],
        ]);
    }

    public function toggleAdmin(Request $r, User $admin)
    {
        abort_unless($admin->is_admin, 404, 'User is not an administrator.');
        abort_if($admin->is_super_admin, 403, 'Super administrator accounts cannot be deactivated.');

        $admin->update(['is_active' => ! $admin->is_active]);
        if (! $admin->is_active) {
            $admin->tokens()->delete();
        }
        AuditLog::record($r->user()->id, 'admin.toggled', $admin, ['active' => $admin->is_active]);

        return response()->json([
            'message' => 'Admin status updated.',
            'is_active' => $admin->is_active,
        ]);
    }

    private function uniqueInviteCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (User::where('invite_code', $code)->exists());

        return $code;
    }

    public function vipLevels()
    {
        return response()->json(VipLevel::withCount('purchases')->orderBy('level')->get());
    }

    public function products()
    {
        return response()->json(Product::withCount([
            'purchases',
            'purchases as pending_purchases_count' => fn ($query) => $query->where('status', 'pending'),
        ])->orderBy('sort_order')->orderBy('cost_price')->get());
    }

    public function saveProduct(Request $r, ?Product $product = null)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot modify products.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can modify products.');

        $productId = $product?->id;
        $d = $r->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($productId)],
            'name' => 'required|string|max:120',
            'cost_price' => 'required|numeric|min:0.01',
            'daily_income' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1|max:3650',
            'image_path' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'boolean',
        ]);

        if (empty($d['image_path'])) {
            $d['image_path'] = '/images/products/mining/bronze.webp';
        }

        $saved = $product ? tap($product)->update($d) : Product::create($d);
        AuditLog::record($r->user()->id, $product ? 'product.updated' : 'product.created', $saved);

        return response()->json($saved->fresh(), $product ? 200 : 201);
    }

    public function deleteProduct(Request $r, Product $product)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators have overarching oversight and cannot modify products.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can modify products.');

        if ($product->purchases()->exists()) {
            $product->update(['is_active' => false]);
            AuditLog::record($r->user()->id, 'product.deactivated', $product);

            return response()->json([
                'status' => 'deactivated',
                'message' => 'Product has existing user purchases and was deactivated instead of permanently deleted.',
                'product' => $product->fresh(),
            ]);
        }

        $product->delete();
        AuditLog::record($r->user()->id, 'product.deleted', $product);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function purchases(Request $r)
    {
        abort_unless($r->user()->canAccessTransactions(), 403, 'Access denied: Customer managers do not have access to transactions.');

        return Purchase::with(['user:id,email,phone', 'product:id,code,name'])
            ->when($r->status, fn ($q) => $q->where('status', $r->status))
            ->latest()
            ->paginate(30);
    }

    public function activatePurchase(Request $r, Purchase $purchase, ReferralService $referrals)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot execute purchase operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can activate purchases.');

        $purchase = DB::transaction(function () use ($r, $purchase, $referrals) {
            $lockedPurchase = Purchase::with('user')
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($lockedPurchase->status === 'pending', 422, 'Purchase is no longer pending.');
            $lockedPurchase->update([
                'status' => 'active',
                'activated_at' => now(),
                'matures_at' => $lockedPurchase->duration_days ? now()->addDays($lockedPurchase->duration_days) : null,
                'reviewed_by' => $r->user()->id,
                'note' => $r->input('note'),
            ]);
            $referrals->payPurchaseRewards($lockedPurchase);

            return $lockedPurchase;
        });
        AuditLog::record($r->user()->id, 'purchase.activated', $purchase);

        return response()->json($purchase->fresh('product'));
    }

    public function rejectPurchase(Request $r, Purchase $purchase)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot execute purchase operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can reject purchases.');

        $d = $r->validate(['note' => 'required|string|max:255']);
        abort_unless($purchase->status === 'pending', 422, 'Purchase is no longer pending.');
        $purchase->update(['status' => 'rejected', 'reviewed_by' => $r->user()->id, 'note' => $d['note']]);
        AuditLog::record($r->user()->id, 'purchase.rejected', $purchase);

        return response()->json($purchase->fresh('product'));
    }

    public function toggleUser(Request $r, User $user)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot modify client accounts.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can modify client accounts.');
        abort_if($user->is_admin, 422);
        abort_if($user->archived_at, 422, 'Archived clients cannot be reactivated.');
        $user->update(['is_active' => ! $user->is_active]);
        if (! $user->is_active) {
            $user->tokens()->delete();
        }
        AuditLog::record($r->user()->id, 'user.toggled', $user, ['active' => $user->is_active]);

        return response()->json($user);
    }

    public function archiveUser(Request $r, User $user)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot modify client accounts.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can delete client accounts.');
        abort_if($user->is_admin, 422, 'Administrator accounts cannot be archived.');
        abort_if($user->archived_at, 422, 'Client is already archived.');

        DB::transaction(function () use ($user, $r) {
            $user->forceFill([
                'name' => 'Archived client',
                'email' => null,
                'phone' => null,
                'invite_code' => null,
                'is_active' => false,
                'archived_at' => now(),
            ])->save();
            $user->tokens()->delete();
            AuditLog::record($r->user()->id, 'user.archived', $user);
        });

        return response()->json(['message' => 'Client archived and anonymized.']);
    }

    // ---- Content
    public function saveAnnouncement(Request $r, ?Announcement $announcement = null)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot edit announcements.');
        $d = $r->validate(['title' => 'required|string|max:255', 'body' => 'nullable|string', 'kind' => 'required|in:ticker,event,notice', 'is_active' => 'boolean', 'starts_at' => 'nullable|date', 'ends_at' => 'nullable|date']);

        return response()->json(Announcement::updateOrCreate(['id' => $announcement?->id], $d));
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        $announcement->delete();

        return response()->noContent();
    }

    public function saveFaq(Request $r, ?Faq $faq = null)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot edit FAQs.');
        $d = $r->validate(['question' => 'required|string|max:255', 'answer' => 'required|string', 'sort' => 'integer', 'is_active' => 'boolean']);

        return response()->json(Faq::updateOrCreate(['id' => $faq?->id], $d));
    }

    public function deleteFaq(Faq $faq)
    {
        $faq->delete();

        return response()->noContent();
    }

    public function saveSettings(Request $r)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot edit platform settings.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can edit platform settings.');

        $d = $r->validate(['about' => 'sometimes|array', 'video_intro_url' => 'sometimes|nullable|url', 'app_download_url' => 'sometimes|nullable|url', 'support_contact' => 'sometimes|array']);
        foreach ($d as $k => $v) {
            Setting::put($k, $v);
        }

        return response()->json(['message' => 'Saved']);
    }

    public function saveVipLevel(Request $r, VipLevel $level)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot edit VIP levels or rebates.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can edit VIP levels.');

        $d = $r->validate([
            'min_deposit' => 'numeric|min:0', 'max_deposit' => 'nullable|numeric',
            'target_yield_pct' => 'numeric|min:0|max:100', 'window_days' => 'nullable|integer|min:1',
            'price' => 'numeric|min:0', 'recharge_requirement' => 'numeric|min:0',
            'required_level_1_members' => 'integer|min:0', 'required_level_2_members' => 'integer|min:0',
            'required_level_3_members' => 'integer|min:0', 'monthly_salary' => 'numeric|min:0',
            'duration_days' => 'integer|min:1', 'tagline' => 'nullable|string|max:500',
            'image_path' => 'nullable|string|max:255', 'sort_order' => 'integer|min:0|max:65535',
            'is_active' => 'boolean',
        ]);
        $level->update($d);

        return response()->json($level);
    }

    public function vipPurchases(Request $r)
    {
        abort_unless($r->user()->canAccessTransactions(), 403, 'Access denied: Customer managers do not have access to transactions.');

        return VipPurchase::with(['user:id,email,phone', 'vipLevel:id,name,level'])
            ->when($r->status, fn ($q) => $q->where('status', $r->status))
            ->latest()->paginate(30);
    }

    public function activateVipPurchase(Request $r, VipPurchase $vipPurchase)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot execute VIP purchase operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can activate VIP purchases.');

        $purchase = DB::transaction(function () use ($r, $vipPurchase) {
            $locked = VipPurchase::whereKey($vipPurchase->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'pending', 422, 'VIP purchase is no longer pending.');
            $locked->update([
                'status' => 'active', 'activated_at' => now(),
                'matures_at' => now()->addDays($locked->duration_days),
                'reviewed_by' => $r->user()->id, 'note' => $r->input('note'),
            ]);

            return $locked;
        });
        AuditLog::record($r->user()->id, 'vip_purchase.activated', $purchase);

        return response()->json($purchase->fresh('vipLevel'));
    }

    public function rejectVipPurchase(Request $r, VipPurchase $vipPurchase)
    {
        abort_if($r->user()->isSuperAdmin(), 403, 'Super administrators cannot execute VIP purchase operations.');
        abort_unless($r->user()->isOperationsAdmin(), 403, 'Only operational administrators can reject VIP purchases.');

        $d = $r->validate(['note' => 'required|string|max:255']);
        abort_unless($vipPurchase->status === 'pending', 422, 'VIP purchase is no longer pending.');
        $vipPurchase->update(['status' => 'rejected', 'reviewed_by' => $r->user()->id, 'note' => $d['note']]);
        AuditLog::record($r->user()->id, 'vip_purchase.rejected', $vipPurchase);

        return response()->json($vipPurchase->fresh('vipLevel'));
    }

    // ---- Support
    public function tickets()
    {
        return SupportTicket::with('user:id,email,phone')->latest()->paginate(30);
    }

    public function replyTicket(Request $r, SupportTicket $ticket)
    {
        $d = $r->validate(['body' => 'required|string|max:4000', 'close' => 'boolean']);
        $ticket->messages()->create(['user_id' => $r->user()->id, 'from_staff' => true, 'body' => $d['body']]);
        $ticket->update(['status' => ($d['close'] ?? false) ? 'closed' : 'answered']);
        UserNotification::create(['user_id' => $ticket->user_id, 'title' => 'Support replied', 'body' => $ticket->subject]);

        return response()->json($ticket->load('messages'));
    }

    public function auditLog()
    {
        return AuditLog::latest()->paginate(50);
    }
}

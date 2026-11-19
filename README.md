# DigiPowerX – Laravel back end (transparent edition)

REST API (Laravel 11, Sanctum) covering every screen in the original design:
Home, VIP, Mining, Team, Me (Deposit, Withdraw, Statements, Transfer, FAQ, Password,
Security PIN, Google Authenticator, Support, About, Log out), language, notifications.

> **Status:** written without a PHP runtime available, so it has NOT been executed or linted.
> Run `php artisan test` and review before deploying. Treat it as a solid first draft.

## Install (overlay onto a fresh Laravel 11 app)

```bash
composer create-project laravel/laravel digipowerx
cd digipowerx
php artisan install:api            # Sanctum
# copy this archive's folders over the project (app/, bootstrap/app.php, config/, database/, routes/api.php, tests/)
cat .env.platform.example >> .env  # then fill in deposit addresses, secrets, DB settings
php artisan migrate --seed
php artisan schedule:work          # runs mining:expire hourly
php artisan test
```
Default admin: `ADMIN_EMAIL` / `ADMIN_PASSWORD` from `.env` (change immediately).

## What was kept, and what was deliberately changed

| Original screen / feature | Here |
|---|---|
| Invest + Commission wallets, Total assets | Same, backed by an append-only ledger (`transactions`) |
| 8 VIP tiers + "No equipment", deposit ranges, 188-day window | Same ranges/windows, editable by admin |
| "Mining income 25–27%" | Kept as `target_yield_pct`, labelled **indicative, not guaranteed**. Nothing is paid at a fixed rate |
| Hashrate, daily, To claim / Claimed / Expired | Same. Earnings come from admin-posted **real revenue batches** (with a required proof URL), split pro-rata by hashrate. No batch = no earnings. Daily figure is an estimate from the last real batch |
| Deposit rebate Lev1 11% / Lev2 4% / Lev3 3% | 3-level team view kept; reward rates configurable, default **5% on level 1 only**, paid once per confirmed deposit from the platform margin |
| Team size, commissions, deposits, withdrawals, date filter, LEV1-3 count/valid/details | Same |
| Total users / Total output banner | **Real** counts from the DB (users, total paid out) |
| Scrolling "Withdraw log" | Shows only **real completed withdrawals** from members who opted in; identities masked |
| Live prices (Binance, OKX, Huobi, Coinbase) | Live from each exchange's public API, 30 s cache, unavailable = null |
| Deposit | Address per network, user submits txid, admin or signed webhook confirms (idempotent) |
| Withdraw | PIN (+ authenticator if enabled), fee + minimum, admin approve → complete, rejection refunds. **Deposits stay locked during the plan window**; earnings/rewards withdrawable |
| Transfer | Between own wallets (locked principal excluded) |
| Statements | Filterable ledger |
| Security PIN, Google Authenticator, Password | Hashed PIN with lockout, RFC 6238 TOTP, token revocation on password change |
| Support, FAQ, About, Events, App link | Tickets with staff replies, admin-managed content |

Added for legitimacy: registration requires accepting terms **and** a risk acknowledgement; risk
disclaimers in API responses; admin **solvency overview** (funds received − paid − owed to users);
audit log of every admin/financial action; no fabricated statistics anywhere.

## Endpoints
Public: `POST auth/register|login|forgot-password|reset-password`, `GET faqs|about|app|prices|support/contact`, `POST webhooks/deposit` (HMAC `X-Signature`).
Member (Bearer token): `me, home, vip, events, deposit/options, deposits, deposits/{id}/txid, withdraw/options, withdrawals, transfer, statements, mining, mining/records?status=, mining/claim, team, team/level/{1-3}, team/commissions, account/*, notifications, support/tickets*`.
Admin (`/api/admin/*`): overview, deposits, withdrawals, yield-batches (+distribute), users, announcements, faqs, settings, vip-levels, tickets, audit-log.

## Before going live
This handles customers' money. Get legal advice on licensing/securities rules in the jurisdictions you
serve, keep funds in custody you can prove, publish real revenue evidence for every batch, and don't
market returns as guaranteed. Add email verification, KYC, and a real chain/payment-processor integration.

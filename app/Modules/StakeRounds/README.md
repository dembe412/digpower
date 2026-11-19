
## Plugging into an existing platform
1. Implement `Contracts\WalletGateway` (spendable / debit / credit) against your wallet + ledger, set `wallet_gateway` in `config/stake_rounds.php`.
2. Set `STAKE_WALLET` (default `invest`) and `admin_middleware` (e.g. `['admin']` or `['admin','super.admin']`).
3. To make the Mining tab open this module, set `STAKE_ROUTE_PREFIXES=stake,mining` and remove the old `mining/*` routes so they don't collide.
4. Keep the ledgers separate: do not pay referral rebates on stakes, count stakes toward VIP qualification, or present results as product earnings.

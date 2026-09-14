# Telegram Account Ownership

HORTON separates dashboard administrators from Telegram end users.

- `users` is reserved for dashboard / Filament authentication.
- `telegram_accounts` owns Telegram end-user identity and lifecycle.
- Business records belonging to a Telegram user reference `telegram_account_id`.
- Product/catalog records are not user-owned.
- Business models must not use `App\Models\User` for Telegram ownership.

The database transition is intentionally manual because `horton.sql` is the schema source of truth. The SQL transition script must be reviewed and executed in phpMyAdmin after application code has been pulled.

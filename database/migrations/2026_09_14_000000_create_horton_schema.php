<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $path = base_path('horton.sql');
        if (! is_file($path)) {
            throw new RuntimeException('HORTON schema source not found: horton.sql');
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException('HORTON schema source is empty: horton.sql');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach ($this->extractCreateTables($sql) as [$table, $statement]) {
                if ($table === 'migrations') {
                    continue;
                }

                DB::unprepared($this->transform($table, $statement));
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach (DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()") as $row) {
                if ($row->table_name !== 'migrations') {
                    DB::statement('DROP TABLE IF EXISTS `'.$row->table_name.'`');
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function extractCreateTables(string $sql): array
    {
        preg_match_all('/CREATE TABLE `([^`]+)`\\s*\\(.*?\\)\\s*ENGINE=.*?;/s', $sql, $matches, PREG_SET_ORDER);
        return array_map(static fn (array $m) => [$m[1], $m[0]], $matches);
    }

    private function transform(string $table, string $sql): string
    {
        $owners = [
            'broadcast_recipients', 'cashback_accounts', 'cashback_transactions',
            'discount_usages', 'gift_code_redemptions', 'notifications', 'orders',
            'payments', 'referral_accounts', 'services', 'support_tickets',
            'wallets', 'wallet_transactions',
        ];

        if (in_array($table, $owners, true)) {
            $sql = str_replace('`user_id`', '`telegram_account_id`', $sql);
        }

        if ($table === 'referrals') {
            $sql = str_replace('`referrer_user_id`', '`referrer_telegram_account_id`', $sql);
            $sql = str_replace('`referred_user_id`', '`referred_telegram_account_id`', $sql);
        }

        if ($table === 'telegram_accounts') {
            $sql = preg_replace('/^\\s*`user_id`\\s+[^,]+,\\s*$/m', '', $sql) ?? $sql;
        }

        return $sql;
    }
};

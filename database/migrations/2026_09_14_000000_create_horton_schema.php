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

        $tables = $this->extractCreateTables($sql);
        if (count($tables) !== 57) {
            throw new RuntimeException('Expected 57 HORTON tables, found '.count($tables).'.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach ($tables as [$table, $statement]) {
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
        $pattern = '/CREATE\\s+TABLE\\s+(?:IF\\s+NOT\\s+EXISTS\\s+)?`([^`]+)`\\s*\\(/i';
        preg_match_all($pattern, $sql, $matches, PREG_OFFSET_CAPTURE);

        $tables = [];
        foreach ($matches[1] as $index => $match) {
            $table = $match[0];
            $start = $matches[0][$index][1];
            $semicolon = $this->findStatementEnd($sql, $start);
            if ($semicolon === null) {
                throw new RuntimeException("Could not parse CREATE TABLE for {$table}.");
            }

            $tables[] = [$table, substr($sql, $start, $semicolon - $start + 1)];
        }

        return $tables;
    }

    private function findStatementEnd(string $sql, int $start): ?int
    {
        $length = strlen($sql);
        $quote = null;
        $escaped = false;

        for ($i = $start; $i < $length; $i++) {
            $char = $sql[$i];

            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }
                if ($char === '\\\\') {
                    $escaped = true;
                    continue;
                }
                if ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
                continue;
            }

            if ($char === ';') {
                return $i;
            }
        }

        return null;
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
            $sql = preg_replace('/REFERENCES\\s+`users`/i', 'REFERENCES `telegram_accounts`', $sql) ?? $sql;
        }

        if ($table === 'referrals') {
            $sql = str_replace('`referrer_user_id`', '`referrer_telegram_account_id`', $sql);
            $sql = str_replace('`referred_user_id`', '`referred_telegram_account_id`', $sql);
            $sql = preg_replace('/REFERENCES\\s+`users`/i', 'REFERENCES `telegram_accounts`', $sql) ?? $sql;
        }

        if ($table === 'telegram_accounts') {
            $sql = preg_replace('/^\\s*`user_id`\\s+[^,]+,\\s*$/m', '', $sql) ?? $sql;
            $sql = preg_replace('/^\\s*CONSTRAINT.*`user_id`.*$/mi', '', $sql) ?? $sql;
        }

        return $sql;
    }
};

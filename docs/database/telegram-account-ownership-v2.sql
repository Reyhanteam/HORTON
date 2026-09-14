-- HORTON Telegram-account ownership migration (corrected v2)
-- MySQL 8.4 / phpMyAdmin
-- BACKUP FIRST. Run the preflight and verify every result is zero before continuing.
-- Do not delete users: users remains the dashboard/Filament identity table.
-- sessions, passkeys and user_profiles intentionally remain user-owned.

-- 1) Preflight: a referenced user must have exactly one Telegram account.
SELECT ta.user_id, COUNT(*) AS account_count
FROM telegram_accounts ta
GROUP BY ta.user_id
HAVING COUNT(*) <> 1;

SELECT x.table_name, x.user_id
FROM (
    SELECT 'orders' table_name, user_id FROM orders
    UNION ALL SELECT 'payments', user_id FROM payments
    UNION ALL SELECT 'wallets', user_id FROM wallets
    UNION ALL SELECT 'cashback_accounts', user_id FROM cashback_accounts
    UNION ALL SELECT 'cashback_transactions', user_id FROM cashback_transactions
    UNION ALL SELECT 'discount_usages', user_id FROM discount_usages
    UNION ALL SELECT 'gift_code_redemptions', user_id FROM gift_code_redemptions
    UNION ALL SELECT 'notifications', user_id FROM notifications
    UNION ALL SELECT 'support_tickets', user_id FROM support_tickets
    UNION ALL SELECT 'wallet_transactions', user_id FROM wallet_transactions
    UNION ALL SELECT 'broadcast_recipients', user_id FROM broadcast_recipients
    UNION ALL SELECT 'services', user_id FROM services
    UNION ALL SELECT 'referral_accounts', user_id FROM referral_accounts
) x
LEFT JOIN telegram_accounts ta ON ta.user_id = x.user_id
WHERE ta.id IS NULL;

SELECT r.id, r.referrer_user_id, r.referred_user_id
FROM referrals r
LEFT JOIN telegram_accounts a ON a.user_id = r.referrer_user_id
LEFT JOIN telegram_accounts b ON b.user_id = r.referred_user_id
WHERE a.id IS NULL OR b.id IS NULL;

-- If any preflight query returns rows, STOP. Resolve the mapping manually.

-- 2) Add target columns.
ALTER TABLE orders ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE payments ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE wallets ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE cashback_accounts ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE cashback_transactions ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE discount_usages ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE gift_code_redemptions ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE notifications ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE support_tickets ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE wallet_transactions ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE broadcast_recipients ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE services ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE referral_accounts ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE referrals ADD COLUMN referrer_telegram_account_id BIGINT UNSIGNED NULL AFTER referrer_user_id;
ALTER TABLE referrals ADD COLUMN referred_telegram_account_id BIGINT UNSIGNED NULL AFTER referred_user_id;

-- 3) Backfill.
UPDATE orders o JOIN telegram_accounts ta ON ta.user_id=o.user_id SET o.telegram_account_id=ta.id;
UPDATE payments p JOIN telegram_accounts ta ON ta.user_id=p.user_id SET p.telegram_account_id=ta.id;
UPDATE wallets w JOIN telegram_accounts ta ON ta.user_id=w.user_id SET w.telegram_account_id=ta.id;
UPDATE cashback_accounts c JOIN telegram_accounts ta ON ta.user_id=c.user_id SET c.telegram_account_id=ta.id;
UPDATE cashback_transactions c JOIN telegram_accounts ta ON ta.user_id=c.user_id SET c.telegram_account_id=ta.id;
UPDATE discount_usages d JOIN telegram_accounts ta ON ta.user_id=d.user_id SET d.telegram_account_id=ta.id;
UPDATE gift_code_redemptions g JOIN telegram_accounts ta ON ta.user_id=g.user_id SET g.telegram_account_id=ta.id;
UPDATE notifications n JOIN telegram_accounts ta ON ta.user_id=n.user_id SET n.telegram_account_id=ta.id;
UPDATE support_tickets s JOIN telegram_accounts ta ON ta.user_id=s.user_id SET s.telegram_account_id=ta.id;
UPDATE wallet_transactions w JOIN telegram_accounts ta ON ta.user_id=w.user_id SET w.telegram_account_id=ta.id;
UPDATE broadcast_recipients b JOIN telegram_accounts ta ON ta.user_id=b.user_id SET b.telegram_account_id=ta.id;
UPDATE services s JOIN telegram_accounts ta ON ta.user_id=s.user_id SET s.telegram_account_id=ta.id;
UPDATE referral_accounts r JOIN telegram_accounts ta ON ta.user_id=r.user_id SET r.telegram_account_id=ta.id;
UPDATE referrals r
JOIN telegram_accounts a ON a.user_id=r.referrer_user_id
JOIN telegram_accounts b ON b.user_id=r.referred_user_id
SET r.referrer_telegram_account_id=a.id, r.referred_telegram_account_id=b.id;

-- 4) Backfill verification. ALL counts must be zero.
SELECT 'orders' t,COUNT(*) n FROM orders WHERE telegram_account_id IS NULL
UNION ALL SELECT 'payments',COUNT(*) FROM payments WHERE telegram_account_id IS NULL
UNION ALL SELECT 'wallets',COUNT(*) FROM wallets WHERE telegram_account_id IS NULL
UNION ALL SELECT 'cashback_accounts',COUNT(*) FROM cashback_accounts WHERE telegram_account_id IS NULL
UNION ALL SELECT 'cashback_transactions',COUNT(*) FROM cashback_transactions WHERE telegram_account_id IS NULL
UNION ALL SELECT 'discount_usages',COUNT(*) FROM discount_usages WHERE telegram_account_id IS NULL
UNION ALL SELECT 'gift_code_redemptions',COUNT(*) FROM gift_code_redemptions WHERE telegram_account_id IS NULL
UNION ALL SELECT 'notifications',COUNT(*) FROM notifications WHERE telegram_account_id IS NULL
UNION ALL SELECT 'support_tickets',COUNT(*) FROM support_tickets WHERE telegram_account_id IS NULL
UNION ALL SELECT 'wallet_transactions',COUNT(*) FROM wallet_transactions WHERE telegram_account_id IS NULL
UNION ALL SELECT 'broadcast_recipients',COUNT(*) FROM broadcast_recipients WHERE telegram_account_id IS NULL
UNION ALL SELECT 'services',COUNT(*) FROM services WHERE telegram_account_id IS NULL
UNION ALL SELECT 'referral_accounts',COUNT(*) FROM referral_accounts WHERE telegram_account_id IS NULL
UNION ALL SELECT 'referrals',COUNT(*) FROM referrals WHERE referrer_telegram_account_id IS NULL OR referred_telegram_account_id IS NULL;

-- 5) Drop old FK constraints and indexes.
ALTER TABLE orders DROP FOREIGN KEY orders_user_id_foreign, DROP INDEX orders_user_id_status_created_at_index;
ALTER TABLE payments DROP FOREIGN KEY payments_user_id_foreign, DROP INDEX payments_user_id_foreign;
ALTER TABLE wallets DROP FOREIGN KEY wallets_user_id_foreign, DROP INDEX wallets_user_id_currency_unique;
ALTER TABLE cashback_accounts DROP FOREIGN KEY cashback_accounts_user_id_foreign, DROP INDEX cashback_accounts_user_id_currency_unique;
ALTER TABLE cashback_transactions DROP FOREIGN KEY cashback_transactions_user_id_foreign, DROP INDEX cashback_transactions_user_id_created_at_index;
ALTER TABLE discount_usages DROP FOREIGN KEY discount_usages_user_id_foreign, DROP INDEX discount_usages_user_id_foreign, DROP INDEX discount_usages_discount_code_id_user_id_index;
ALTER TABLE gift_code_redemptions DROP FOREIGN KEY gift_code_redemptions_user_id_foreign, DROP INDEX gift_code_redemptions_gift_code_id_user_id_unique, DROP INDEX gift_code_redemptions_user_id_foreign;
ALTER TABLE notifications DROP FOREIGN KEY notifications_user_id_foreign, DROP INDEX notifications_user_id_read_at_created_at_index;
ALTER TABLE support_tickets DROP FOREIGN KEY support_tickets_user_id_foreign, DROP INDEX support_tickets_user_id_foreign;
ALTER TABLE wallet_transactions DROP FOREIGN KEY wallet_transactions_user_id_foreign, DROP INDEX wallet_transactions_user_id_created_at_index;
ALTER TABLE broadcast_recipients DROP FOREIGN KEY broadcast_recipients_user_id_foreign, DROP INDEX broadcast_recipients_user_id_foreign;
ALTER TABLE services DROP FOREIGN KEY services_user_id_foreign, DROP INDEX services_user_id_status_expires_at_index;
ALTER TABLE referral_accounts DROP FOREIGN KEY referral_accounts_user_id_foreign, DROP INDEX referral_accounts_user_id_unique;
ALTER TABLE referrals DROP FOREIGN KEY referrals_referrer_user_id_foreign, DROP FOREIGN KEY referrals_referred_user_id_foreign, DROP INDEX referrals_referrer_user_id_status_index, DROP INDEX referrals_referred_user_id_unique;

-- 6) Remove old ownership columns and make new ones required.
ALTER TABLE orders DROP COLUMN user_id;
ALTER TABLE payments DROP COLUMN user_id;
ALTER TABLE wallets DROP COLUMN user_id;
ALTER TABLE cashback_accounts DROP COLUMN user_id;
ALTER TABLE cashback_transactions DROP COLUMN user_id;
ALTER TABLE discount_usages DROP COLUMN user_id;
ALTER TABLE gift_code_redemptions DROP COLUMN user_id;
ALTER TABLE notifications DROP COLUMN user_id;
ALTER TABLE support_tickets DROP COLUMN user_id;
ALTER TABLE wallet_transactions DROP COLUMN user_id;
ALTER TABLE broadcast_recipients DROP COLUMN user_id;
ALTER TABLE services DROP COLUMN user_id;
ALTER TABLE referral_accounts DROP COLUMN user_id;
ALTER TABLE referrals DROP COLUMN referrer_user_id, DROP COLUMN referred_user_id;

ALTER TABLE orders MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE payments MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE wallets MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE cashback_accounts MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE cashback_transactions MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE discount_usages MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE gift_code_redemptions MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE notifications MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE support_tickets MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE wallet_transactions MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE broadcast_recipients MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE services MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE referral_accounts MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE referrals MODIFY referrer_telegram_account_id BIGINT UNSIGNED NOT NULL, MODIFY referred_telegram_account_id BIGINT UNSIGNED NOT NULL;

-- 7) Add new indexes/FKs.
ALTER TABLE orders ADD KEY orders_telegram_account_id_status_created_at_index (telegram_account_id,status,created_at), ADD CONSTRAINT orders_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE payments ADD KEY payments_telegram_account_id_index (telegram_account_id), ADD CONSTRAINT payments_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE wallets ADD UNIQUE KEY wallets_telegram_account_id_currency_unique (telegram_account_id,currency), ADD CONSTRAINT wallets_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE cashback_accounts ADD UNIQUE KEY cashback_accounts_telegram_account_id_currency_unique (telegram_account_id,currency), ADD CONSTRAINT cashback_accounts_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE cashback_transactions ADD KEY cashback_transactions_telegram_account_id_created_at_index (telegram_account_id,created_at), ADD CONSTRAINT cashback_transactions_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE discount_usages ADD KEY discount_usages_telegram_account_id_foreign (telegram_account_id), ADD KEY discount_usages_discount_code_id_telegram_account_id_index (discount_code_id,telegram_account_id), ADD CONSTRAINT discount_usages_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE gift_code_redemptions ADD UNIQUE KEY gift_code_redemptions_gift_code_id_telegram_account_id_unique (gift_code_id,telegram_account_id), ADD KEY gift_code_redemptions_telegram_account_id_foreign (telegram_account_id), ADD CONSTRAINT gift_code_redemptions_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE notifications ADD KEY notifications_telegram_account_id_read_at_created_at_index (telegram_account_id,read_at,created_at), ADD CONSTRAINT notifications_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE CASCADE;
ALTER TABLE support_tickets ADD KEY support_tickets_telegram_account_id_foreign (telegram_account_id), ADD CONSTRAINT support_tickets_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE wallet_transactions ADD KEY wallet_transactions_telegram_account_id_created_at_index (telegram_account_id,created_at), ADD CONSTRAINT wallet_transactions_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE broadcast_recipients ADD KEY broadcast_recipients_telegram_account_id_foreign (telegram_account_id), ADD CONSTRAINT broadcast_recipients_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE CASCADE;
ALTER TABLE services ADD KEY services_telegram_account_id_status_expires_at_index (telegram_account_id,status,expires_at), ADD CONSTRAINT services_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;
ALTER TABLE referral_accounts ADD UNIQUE KEY referral_accounts_telegram_account_id_unique (telegram_account_id), ADD CONSTRAINT referral_accounts_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE CASCADE;
ALTER TABLE referrals ADD UNIQUE KEY referrals_referred_telegram_account_id_unique (referred_telegram_account_id), ADD KEY referrals_referrer_telegram_account_id_status_index (referrer_telegram_account_id,status), ADD CONSTRAINT referrals_referrer_telegram_account_id_foreign FOREIGN KEY (referrer_telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT, ADD CONSTRAINT referrals_referred_telegram_account_id_foreign FOREIGN KEY (referred_telegram_account_id) REFERENCES telegram_accounts(id) ON DELETE RESTRICT;

-- 8) Detach Telegram accounts from dashboard users.
ALTER TABLE telegram_accounts DROP FOREIGN KEY telegram_accounts_user_id_foreign, DROP INDEX telegram_accounts_user_id_is_active_index;
ALTER TABLE telegram_accounts DROP COLUMN user_id;

-- 9) These remain user-owned by design:
-- user_profiles.user_id
-- sessions.user_id
-- passkeys.user_id

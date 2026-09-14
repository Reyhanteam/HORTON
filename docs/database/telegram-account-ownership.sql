-- HORTON: migrate Telegram-user business ownership from users to telegram_accounts.
-- Source schema: horton.sql (MySQL 8.4).
-- IMPORTANT: take a database backup before running this script.
-- Do NOT delete rows from users: that table remains the dashboard/Filament identity table.
-- sessions, passkeys and user_profiles intentionally remain attached to users.

-- ================================================================
-- 0) PRE-FLIGHT: every referenced Telegram user must map to exactly
--    one telegram_accounts row. Run these checks before continuing.
-- ================================================================

SELECT ta.user_id, COUNT(*) AS telegram_account_count
FROM telegram_accounts ta
GROUP BY ta.user_id
HAVING COUNT(*) <> 1;

SELECT 'orders' AS table_name, COUNT(*) AS unmapped_rows
FROM orders o LEFT JOIN telegram_accounts ta ON ta.user_id = o.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'payments', COUNT(*)
FROM payments p LEFT JOIN telegram_accounts ta ON ta.user_id = p.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'wallets', COUNT(*)
FROM wallets w LEFT JOIN telegram_accounts ta ON ta.user_id = w.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'cashback_accounts', COUNT(*)
FROM cashback_accounts ca LEFT JOIN telegram_accounts ta ON ta.user_id = ca.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'cashback_transactions', COUNT(*)
FROM cashback_transactions ct LEFT JOIN telegram_accounts ta ON ta.user_id = ct.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'discount_usages', COUNT(*)
FROM discount_usages du LEFT JOIN telegram_accounts ta ON ta.user_id = du.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'gift_code_redemptions', COUNT(*)
FROM gift_code_redemptions gr LEFT JOIN telegram_accounts ta ON ta.user_id = gr.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'notifications', COUNT(*)
FROM notifications n LEFT JOIN telegram_accounts ta ON ta.user_id = n.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'support_tickets', COUNT(*)
FROM support_tickets st LEFT JOIN telegram_accounts ta ON ta.user_id = st.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'wallet_transactions', COUNT(*)
FROM wallet_transactions wt LEFT JOIN telegram_accounts ta ON ta.user_id = wt.user_id
WHERE ta.id IS NULL
UNION ALL
SELECT 'broadcast_recipients', COUNT(*)
FROM broadcast_recipients br LEFT JOIN telegram_accounts ta ON ta.user_id = br.user_id
WHERE ta.id IS NULL;

-- Referrals need both sides mapped.
SELECT r.id, r.referrer_user_id, r.referred_user_id
FROM referrals r
LEFT JOIN telegram_accounts referrer_ta ON referrer_ta.user_id = r.referrer_user_id
LEFT JOIN telegram_accounts referred_ta ON referred_ta.user_id = r.referred_user_id
WHERE referrer_ta.id IS NULL OR referred_ta.id IS NULL;

-- If ANY pre-flight result is non-zero or a user has more than one Telegram
-- account, STOP and resolve the mapping manually. Never assign arbitrarily.

-- ================================================================
-- 1) Add target columns. They are nullable temporarily for backfill.
-- ================================================================

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
ALTER TABLE referrals ADD COLUMN referrer_telegram_account_id BIGINT UNSIGNED NULL AFTER referrer_user_id;
ALTER TABLE referrals ADD COLUMN referred_telegram_account_id BIGINT UNSIGNED NULL AFTER referred_user_id;
ALTER TABLE referral_accounts ADD COLUMN telegram_account_id BIGINT UNSIGNED NULL AFTER user_id;

-- ================================================================
-- 2) Backfill through the existing users -> telegram_accounts mapping.
-- ================================================================

UPDATE orders o
JOIN telegram_accounts ta ON ta.user_id = o.user_id
SET o.telegram_account_id = ta.id;

UPDATE payments p
JOIN telegram_accounts ta ON ta.user_id = p.user_id
SET p.telegram_account_id = ta.id;

UPDATE wallets w
JOIN telegram_accounts ta ON ta.user_id = w.user_id
SET w.telegram_account_id = ta.id;

UPDATE cashback_accounts ca
JOIN telegram_accounts ta ON ta.user_id = ca.user_id
SET ca.telegram_account_id = ta.id;

UPDATE cashback_transactions ct
JOIN telegram_accounts ta ON ta.user_id = ct.user_id
SET ct.telegram_account_id = ta.id;

UPDATE discount_usages du
JOIN telegram_accounts ta ON ta.user_id = du.user_id
SET du.telegram_account_id = ta.id;

UPDATE gift_code_redemptions gr
JOIN telegram_accounts ta ON ta.user_id = gr.user_id
SET gr.telegram_account_id = ta.id;

UPDATE notifications n
JOIN telegram_accounts ta ON ta.user_id = n.user_id
SET n.telegram_account_id = ta.id;

UPDATE support_tickets st
JOIN telegram_accounts ta ON ta.user_id = st.user_id
SET st.telegram_account_id = ta.id;

UPDATE wallet_transactions wt
JOIN telegram_accounts ta ON ta.user_id = wt.user_id
SET wt.telegram_account_id = ta.id;

UPDATE broadcast_recipients br
JOIN telegram_accounts ta ON ta.user_id = br.user_id
SET br.telegram_account_id = ta.id;

UPDATE referrals r
JOIN telegram_accounts referrer_ta ON referrer_ta.user_id = r.referrer_user_id
JOIN telegram_accounts referred_ta ON referred_ta.user_id = r.referred_user_id
SET r.referrer_telegram_account_id = referrer_ta.id,
    r.referred_telegram_account_id = referred_ta.id;

UPDATE referral_accounts ra
JOIN telegram_accounts ta ON ta.user_id = ra.user_id
SET ra.telegram_account_id = ta.id;

-- ================================================================
-- 3) Verify backfill before destructive changes.
--    Every count below MUST be zero.
-- ================================================================

SELECT 'orders' AS table_name, COUNT(*) AS remaining
FROM orders WHERE telegram_account_id IS NULL
UNION ALL SELECT 'payments', COUNT(*) FROM payments WHERE telegram_account_id IS NULL
UNION ALL SELECT 'wallets', COUNT(*) FROM wallets WHERE telegram_account_id IS NULL
UNION ALL SELECT 'cashback_accounts', COUNT(*) FROM cashback_accounts WHERE telegram_account_id IS NULL
UNION ALL SELECT 'cashback_transactions', COUNT(*) FROM cashback_transactions WHERE telegram_account_id IS NULL
UNION ALL SELECT 'discount_usages', COUNT(*) FROM discount_usages WHERE telegram_account_id IS NULL
UNION ALL SELECT 'gift_code_redemptions', COUNT(*) FROM gift_code_redemptions WHERE telegram_account_id IS NULL
UNION ALL SELECT 'notifications', COUNT(*) FROM notifications WHERE telegram_account_id IS NULL
UNION ALL SELECT 'support_tickets', COUNT(*) FROM support_tickets WHERE telegram_account_id IS NULL
UNION ALL SELECT 'wallet_transactions', COUNT(*) FROM wallet_transactions WHERE telegram_account_id IS NULL
UNION ALL SELECT 'broadcast_recipients', COUNT(*) FROM broadcast_recipients WHERE telegram_account_id IS NULL;

SELECT 'referrals' AS table_name, COUNT(*) AS remaining
FROM referrals
WHERE referrer_telegram_account_id IS NULL OR referred_telegram_account_id IS NULL
UNION ALL
SELECT 'referral_accounts', COUNT(*)
FROM referral_accounts
WHERE telegram_account_id IS NULL;

-- STOP if any result above is non-zero.

-- ================================================================
-- 4) Remove old users foreign keys and user-based indexes.
-- ================================================================

ALTER TABLE orders DROP FOREIGN KEY orders_user_id_foreign;
ALTER TABLE orders DROP INDEX orders_user_id_status_created_at_index;

ALTER TABLE payments DROP FOREIGN KEY payments_user_id_foreign;
ALTER TABLE payments DROP INDEX payments_user_id_foreign;

ALTER TABLE wallets DROP FOREIGN KEY wallets_user_id_foreign;
ALTER TABLE wallets DROP INDEX wallets_user_id_foreign;

ALTER TABLE cashback_accounts DROP FOREIGN KEY cashback_accounts_user_id_foreign;
ALTER TABLE cashback_accounts DROP INDEX cashback_accounts_user_id_currency_unique;

ALTER TABLE cashback_transactions DROP FOREIGN KEY cashback_transactions_user_id_foreign;
ALTER TABLE cashback_transactions DROP INDEX cashback_transactions_user_id_created_at_index;

ALTER TABLE discount_usages DROP FOREIGN KEY discount_usages_user_id_foreign;
ALTER TABLE discount_usages DROP INDEX discount_usages_user_id_foreign;
ALTER TABLE discount_usages DROP INDEX discount_usages_discount_code_id_user_id_index;

ALTER TABLE gift_code_redemptions DROP FOREIGN KEY gift_code_redemptions_user_id_foreign;
ALTER TABLE gift_code_redemptions DROP INDEX gift_code_redemptions_gift_code_id_user_id_unique;
ALTER TABLE gift_code_redemptions DROP INDEX gift_code_redemptions_user_id_foreign;

ALTER TABLE notifications DROP FOREIGN KEY notifications_user_id_foreign;
ALTER TABLE notifications DROP INDEX notifications_user_id_read_at_created_at_index;

ALTER TABLE support_tickets DROP FOREIGN KEY support_tickets_user_id_foreign;
ALTER TABLE support_tickets DROP INDEX support_tickets_user_id_foreign;

ALTER TABLE wallet_transactions DROP FOREIGN KEY wallet_transactions_user_id_foreign;
ALTER TABLE wallet_transactions DROP INDEX wallet_transactions_user_id_foreign;

ALTER TABLE broadcast_recipients DROP FOREIGN KEY broadcast_recipients_user_id_foreign;
ALTER TABLE broadcast_recipients DROP INDEX broadcast_recipients_user_id_foreign;

ALTER TABLE referrals DROP FOREIGN KEY referrals_referrer_user_id_foreign;
ALTER TABLE referrals DROP FOREIGN KEY referrals_referred_user_id_foreign;
ALTER TABLE referrals DROP INDEX referrals_referrer_user_id_status_index;
ALTER TABLE referrals DROP INDEX referrals_referred_user_id_unique;

ALTER TABLE referral_accounts DROP FOREIGN KEY referral_accounts_user_id_foreign;
ALTER TABLE referral_accounts DROP INDEX referral_accounts_user_id_unique;

-- ================================================================
-- 5) Replace old columns with Telegram-account ownership columns.
-- ================================================================

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
ALTER TABLE referrals DROP COLUMN referrer_user_id;
ALTER TABLE referrals DROP COLUMN referred_user_id;
ALTER TABLE referral_accounts DROP COLUMN user_id;

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
ALTER TABLE referrals MODIFY referrer_telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE referrals MODIFY referred_telegram_account_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE referral_accounts MODIFY telegram_account_id BIGINT UNSIGNED NOT NULL;

-- ================================================================
-- 6) Add replacement indexes and foreign keys.
-- ================================================================

ALTER TABLE orders
  ADD KEY orders_telegram_account_id_status_created_at_index (telegram_account_id, status, created_at),
  ADD CONSTRAINT orders_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE payments
  ADD KEY payments_telegram_account_id_index (telegram_account_id),
  ADD CONSTRAINT payments_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE wallets
  ADD UNIQUE KEY wallets_telegram_account_id_currency_unique (telegram_account_id, currency),
  ADD CONSTRAINT wallets_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE cashback_accounts
  ADD UNIQUE KEY cashback_accounts_telegram_account_id_currency_unique (telegram_account_id, currency),
  ADD CONSTRAINT cashback_accounts_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE cashback_transactions
  ADD KEY cashback_transactions_telegram_account_id_created_at_index (telegram_account_id, created_at),
  ADD CONSTRAINT cashback_transactions_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE discount_usages
  ADD KEY discount_usages_telegram_account_id_foreign (telegram_account_id),
  ADD KEY discount_usages_discount_code_id_telegram_account_id_index (discount_code_id, telegram_account_id),
  ADD CONSTRAINT discount_usages_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE gift_code_redemptions
  ADD UNIQUE KEY gift_code_redemptions_gift_code_id_telegram_account_id_unique (gift_code_id, telegram_account_id),
  ADD KEY gift_code_redemptions_telegram_account_id_foreign (telegram_account_id),
  ADD CONSTRAINT gift_code_redemptions_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE notifications
  ADD KEY notifications_telegram_account_id_read_at_created_at_index (telegram_account_id, read_at, created_at),
  ADD CONSTRAINT notifications_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE CASCADE;

ALTER TABLE support_tickets
  ADD KEY support_tickets_telegram_account_id_foreign (telegram_account_id),
  ADD CONSTRAINT support_tickets_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE wallet_transactions
  ADD KEY wallet_transactions_telegram_account_id_foreign (telegram_account_id),
  ADD CONSTRAINT wallet_transactions_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE broadcast_recipients
  ADD KEY broadcast_recipients_telegram_account_id_foreign (telegram_account_id),
  ADD CONSTRAINT broadcast_recipients_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE CASCADE;

ALTER TABLE referrals
  ADD UNIQUE KEY referrals_referred_telegram_account_id_unique (referred_telegram_account_id),
  ADD KEY referrals_referrer_telegram_account_id_status_index (referrer_telegram_account_id, status),
  ADD CONSTRAINT referrals_referrer_telegram_account_id_foreign FOREIGN KEY (referrer_telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT,
  ADD CONSTRAINT referrals_referred_telegram_account_id_foreign KEY (referred_telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE RESTRICT;

ALTER TABLE referral_accounts
  ADD UNIQUE KEY referral_accounts_telegram_account_id_unique (telegram_account_id),
  ADD CONSTRAINT referral_accounts_telegram_account_id_foreign FOREIGN KEY (telegram_account_id) REFERENCES telegram_accounts (id) ON DELETE CASCADE;

-- ================================================================
-- 7) Telegram accounts are no longer linked to dashboard users.
-- ================================================================

ALTER TABLE telegram_accounts DROP FOREIGN KEY telegram_accounts_user_id_foreign;
ALTER TABLE telegram_accounts DROP COLUMN user_id;

-- ================================================================
-- 8) Final integrity checks.
-- ================================================================

SELECT COUNT(*) AS telegram_accounts_without_business_identity
FROM telegram_accounts;

SHOW CREATE TABLE orders;
SHOW CREATE TABLE payments;
SHOW CREATE TABLE wallets;
SHOW CREATE TABLE services;
SHOW CREATE TABLE telegram_accounts;

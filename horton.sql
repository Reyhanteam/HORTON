-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Sep 14, 2026 at 10:09 AM
-- Server version: 8.4.11
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `horton`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `status`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Horton Admin', 'jookeremasst@gmail.com', '$2y$12$dBs7ICWEmbfhAWCrCXKLTuzn8BVtcIKUmHCBGMywvvi91Pvi3JFpe', 'active', NULL, NULL, '2026-09-13 15:11:32', '2026-09-13 15:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `admin_user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bot_channels`
--

CREATE TABLE `bot_channels` (
  `id` bigint UNSIGNED NOT NULL,
  `telegram_chat_id` bigint NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'channel',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bot_messages`
--

CREATE TABLE `bot_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa',
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'message',
  `button_type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bot_messages`
--

INSERT INTO `bot_messages` (`id`, `key`, `locale`, `text`, `type`, `button_type`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'support.faq_gate', 'fa', 'قبل از ارسال درخواست پشتیبانی، بهتر است ابتدا سوالات متداول را مطالعه کنید. آیا سوالات متداول را مطالعه کرده‌اید؟', 'message', NULL, 'Support entry: ask whether FAQ was reviewed', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(2, 'support.faq_title', 'fa', 'سوالات متداول', 'message', NULL, 'FAQ heading', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(3, 'support.faq_empty', 'fa', 'در حال حاضر سوال متداولی ثبت نشده است. می‌توانید درخواست پشتیبانی خود را ارسال کنید.', 'message', NULL, 'Empty FAQ fallback', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(4, 'support.choose_department', 'fa', 'لطفاً بخش موردنظر برای پیگیری درخواست خود را انتخاب کنید.', 'message', NULL, 'Department selection prompt', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(5, 'support.choose_sensitivity', 'fa', 'میزان حساسیت درخواست را مشخص کنید.', 'message', NULL, 'Sensitivity selection prompt', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(6, 'support.write_message', 'fa', 'حالا پیام خود را ارسال کنید. پیام می‌تواند شامل متن، عکس، ویدیو یا فایل باشد.', 'message', NULL, 'Support message prompt', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(7, 'support.created', 'fa', 'درخواست پشتیبانی شما با شماره #{ticket_id} ثبت شد. پاسخ تیم پشتیبانی پس از بررسی برای شما ارسال می‌شود.', 'message', NULL, 'Ticket created confirmation', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(8, 'support.no_departments', 'fa', 'در حال حاضر هیچ بخش پشتیبانی فعالی وجود ندارد. لطفاً بعداً دوباره تلاش کنید.', 'message', NULL, 'No active support departments', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(9, 'support.no_tickets', 'fa', 'هنوز درخواست پشتیبانی ثبت نکرده‌اید.', 'message', NULL, 'No tickets', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(10, 'support.ticket_list', 'fa', 'درخواست‌های پشتیبانی شما:\n{tickets}', 'message', NULL, 'Ticket list', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(11, 'support.faq_not_read', 'fa', 'خیر، مطالعه نکردم', 'button', NULL, 'FAQ gate negative button', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(12, 'support.faq_read', 'fa', 'بله، مطالعه کردم', 'button', NULL, 'FAQ gate positive button', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(13, 'support.continue', 'fa', 'ادامه و ارسال درخواست', 'button', NULL, 'Continue to support after FAQ', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(14, 'support.sensitivity_low', 'fa', 'کم', 'button', NULL, 'Low sensitivity', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(15, 'support.sensitivity_normal', 'fa', 'عادی', 'button', NULL, 'Normal sensitivity', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(16, 'support.sensitivity_high', 'fa', 'زیاد', 'button', NULL, 'High sensitivity', 1, '2026-09-13 15:57:57', '2026-09-13 15:57:57'),
(17, 'notifications.account.registration_success.title', 'fa', 'ثبت‌نام با موفقیت انجام شد', 'message', NULL, 'Notification title: registration success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(18, 'notifications.account.registration_success.body', 'fa', 'سلام {name} 🌷 حساب شما با موفقیت فعال شد و می‌توانید از ربات استفاده کنید.', 'message', NULL, 'Notification body: registration success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(19, 'notifications.account.phone_verification_success.title', 'fa', 'تأیید شماره تلفن', 'message', NULL, 'Notification title: phone verification success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(20, 'notifications.account.phone_verification_success.body', 'fa', 'شماره تلفن شما با موفقیت تأیید شد.', 'message', NULL, 'Notification body: phone verification success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(21, 'notifications.account.status_changed.title', 'fa', 'تغییر وضعیت حساب', 'message', NULL, 'Notification title: account status changed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(22, 'notifications.account.status_changed.body', 'fa', 'وضعیت حساب شما به «{status}» تغییر کرد.', 'message', NULL, 'Notification body: account status changed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(23, 'notifications.payment.success.title', 'fa', 'پرداخت موفق', 'message', NULL, 'Notification title: payment success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(24, 'notifications.payment.success.body', 'fa', 'پرداخت شما به مبلغ {amount} {currency} با موفقیت انجام شد. شماره پرداخت: {payment_id}', 'message', NULL, 'Notification body: payment success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(25, 'notifications.payment.failed.title', 'fa', 'پرداخت ناموفق', 'message', NULL, 'Notification title: payment failed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(26, 'notifications.payment.failed.body', 'fa', 'پرداخت شما برای سفارش {order_id} ناموفق بود. مبلغ: {amount} {currency}', 'message', NULL, 'Notification body: payment failed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(27, 'notifications.payment.pending.title', 'fa', 'پرداخت در انتظار بررسی', 'message', NULL, 'Notification title: manual payment pending', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(28, 'notifications.payment.pending.body', 'fa', 'رسید پرداخت دستی شما ثبت شد و در انتظار بررسی است. سفارش: {order_id} — مبلغ: {amount} {currency}', 'message', NULL, 'Notification body: manual payment pending', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(29, 'notifications.payment.manual_approved.title', 'fa', 'تأیید پرداخت دستی', 'message', NULL, 'Notification title: manual payment approved', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(30, 'notifications.payment.manual_approved.body', 'fa', 'پرداخت دستی شما تأیید شد و سفارش {order_id} وارد فرایند انجام شد.', 'message', NULL, 'Notification body: manual payment approved', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(31, 'notifications.payment.manual_rejected.title', 'fa', 'رد پرداخت دستی', 'message', NULL, 'Notification title: manual payment rejected', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(32, 'notifications.payment.manual_rejected.body', 'fa', 'پرداخت دستی شما تأیید نشد. در صورت نیاز دوباره پرداخت را انجام دهید یا با پشتیبانی تماس بگیرید.', 'message', NULL, 'Notification body: manual payment rejected', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(33, 'notifications.wallet.credited.title', 'fa', 'افزایش موجودی کیف پول', 'message', NULL, 'Notification title: wallet credited', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(34, 'notifications.wallet.credited.body', 'fa', 'مبلغ {amount} به کیف پول شما اضافه شد. موجودی جدید: {balance_after}', 'message', NULL, 'Notification body: wallet credited', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(35, 'notifications.wallet.debited.title', 'fa', 'کاهش موجودی کیف پول', 'message', NULL, 'Notification title: wallet debited', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(36, 'notifications.wallet.debited.body', 'fa', 'مبلغ {amount} از کیف پول شما کسر شد. موجودی جدید: {balance_after}', 'message', NULL, 'Notification body: wallet debited', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(37, 'notifications.order.created.title', 'fa', 'ثبت سفارش', 'message', NULL, 'Notification title: order created', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(38, 'notifications.order.created.body', 'fa', 'سفارش شما با شماره {order_id} ثبت شد. مبلغ سفارش: {amount} {currency}', 'message', NULL, 'Notification body: order created', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(39, 'notifications.order.processing.title', 'fa', 'در حال پردازش سفارش', 'message', NULL, 'Notification title: order processing', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(40, 'notifications.order.processing.body', 'fa', 'سفارش {order_id} در حال پردازش و آماده‌سازی است.', 'message', NULL, 'Notification body: order processing', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(41, 'notifications.order.completed.title', 'fa', 'تکمیل سفارش', 'message', NULL, 'Notification title: order completed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(42, 'notifications.order.completed.body', 'fa', 'سفارش {order_id} با موفقیت تکمیل شد.', 'message', NULL, 'Notification body: order completed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(43, 'notifications.order.failed.title', 'fa', 'خطا در سفارش', 'message', NULL, 'Notification title: order failed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(44, 'notifications.order.failed.body', 'fa', 'در پردازش سفارش {order_id} خطایی رخ داد. وضعیت سفارش: ناموفق.', 'message', NULL, 'Notification body: order failed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(45, 'notifications.service.expiring_3_days.title', 'fa', 'یادآوری پایان سرویس', 'message', NULL, 'Notification title: service expires in three days', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(46, 'notifications.service.expiring_3_days.body', 'fa', 'سرویس شما کمتر از سه روز دیگر به پایان می‌رسد. زمان پایان: {expires_at}', 'message', NULL, 'Notification body: service expires in three days', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(47, 'notifications.service.expiring_24_hours.title', 'fa', '⚠️ هشدار پایان سرویس', 'message', NULL, 'Notification title: service expires in 24 hours', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(48, 'notifications.service.expiring_24_hours.body', 'fa', 'سرویس شما کمتر از ۲۴ ساعت دیگر به پایان می‌رسد. برای جلوگیری از قطع سرویس، نسبت به تمدید آن اقدام کنید. زمان پایان: {expires_at}', 'message', NULL, 'Notification body: service expires in 24 hours', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(49, 'notifications.service.expired.title', 'fa', 'پایان سرویس', 'message', NULL, 'Notification title: service expired', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(50, 'notifications.service.expired.body', 'fa', 'سرویس شما در تاریخ {expires_at} به پایان رسید.', 'message', NULL, 'Notification body: service expired', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(51, 'notifications.service.renewal_success.title', 'fa', 'تمدید موفق سرویس', 'message', NULL, 'Notification title: renewal success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(52, 'notifications.service.renewal_success.body', 'fa', 'سرویس شما با موفقیت تمدید شد. زمان پایان جدید: {expires_at}', 'message', NULL, 'Notification body: renewal success', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(53, 'notifications.service.renewal_failed.title', 'fa', 'تمدید ناموفق سرویس', 'message', NULL, 'Notification title: renewal failed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(54, 'notifications.service.renewal_failed.body', 'fa', 'تمدید سرویس شما ناموفق بود. لطفاً دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.', 'message', NULL, 'Notification body: renewal failed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(55, 'notifications.service.created.title', 'fa', 'سرویس آماده شد', 'message', NULL, 'Notification title: service created', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(56, 'notifications.service.created.body', 'fa', 'سرویس شما با موفقیت ایجاد و فعال شد. زمان پایان: {expires_at}', 'message', NULL, 'Notification body: service created', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(57, 'notifications.service.provisioning.title', 'fa', 'در حال ساخت سرویس', 'message', NULL, 'Notification title: provisioning in progress', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(58, 'notifications.service.provisioning.body', 'fa', 'درخواست ساخت سرویس شما ثبت شد و سرویس در حال آماده‌سازی است.', 'message', NULL, 'Notification body: provisioning in progress', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(59, 'notifications.service.capacity_increased.title', 'fa', 'افزایش ظرفیت سرویس', 'message', NULL, 'Notification title: capacity increased', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(60, 'notifications.service.capacity_increased.body', 'fa', 'ظرفیت سرویس شما با موفقیت افزایش یافت. ظرفیت جدید: {capacity}', 'message', NULL, 'Notification body: capacity increased', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(61, 'notifications.service.disabled.title', 'fa', 'غیرفعال شدن سرویس', 'message', NULL, 'Notification title: service disabled', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(62, 'notifications.service.disabled.body', 'fa', 'سرویس شما غیرفعال شد. در صورت نیاز برای بررسی علت با پشتیبانی تماس بگیرید.', 'message', NULL, 'Notification body: service disabled', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(63, 'notifications.service.reactivated.title', 'fa', 'فعال شدن دوباره سرویس', 'message', NULL, 'Notification title: service reactivated', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(64, 'notifications.service.reactivated.body', 'fa', 'سرویس شما دوباره فعال شد و قابل استفاده است.', 'message', NULL, 'Notification body: service reactivated', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(65, 'notifications.marketing.discount_applied.title', 'fa', 'اعمال تخفیف', 'message', NULL, 'Notification title: discount applied', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(66, 'notifications.marketing.discount_applied.body', 'fa', 'کد تخفیف {discount_code} با موفقیت روی سفارش {order_id} اعمال شد. مبلغ تخفیف: {discount_amount}', 'message', NULL, 'Notification body: discount applied', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(67, 'notifications.marketing.gift_redeemed.title', 'fa', 'هدیه دریافت شد', 'message', NULL, 'Notification title: gift redeemed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(68, 'notifications.marketing.gift_redeemed.body', 'fa', 'کد هدیه {gift_code} با موفقیت استفاده شد. ارزش هدیه: {gift_value}', 'message', NULL, 'Notification body: gift redeemed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(69, 'notifications.marketing.referral_reward.title', 'fa', 'پاداش دعوت', 'message', NULL, 'Notification title: referral reward', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(70, 'notifications.marketing.referral_reward.body', 'fa', 'دعوت شما با موفقیت واجد شرایط شد و پاداش ارجاع برای حساب شما ثبت شد.', 'message', NULL, 'Notification body: referral reward', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(71, 'notifications.marketing.cashback_credited.title', 'fa', 'دریافت کش‌بک', 'message', NULL, 'Notification title: cashback credited', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(72, 'notifications.marketing.cashback_credited.body', 'fa', 'مبلغ {amount} کش‌بک به حساب شما اضافه شد.', 'message', NULL, 'Notification body: cashback credited', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(73, 'notifications.support.ticket_replied.title', 'fa', 'پاسخ پشتیبانی', 'message', NULL, 'Notification title: support ticket replied', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(74, 'notifications.support.ticket_replied.body', 'fa', 'برای تیکت پشتیبانی شماره {ticket_id} پاسخ جدید ثبت شده است.', 'message', NULL, 'Notification body: support ticket replied', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(75, 'notifications.support.ticket_status_changed.title', 'fa', 'تغییر وضعیت تیکت', 'message', NULL, 'Notification title: support ticket status changed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(76, 'notifications.support.ticket_status_changed.body', 'fa', 'وضعیت تیکت پشتیبانی شماره {ticket_id} به «{status}» تغییر کرد.', 'message', NULL, 'Notification body: support ticket status changed', 1, '2026-09-13 15:58:57', '2026-09-13 15:58:57'),
(77, 'registration.rules', 'fa', 'تایید قوانین', 'message', NULL, NULL, 1, NULL, NULL),
(78, 'registration.accept_button', 'fa', 'بله', 'button', 'inline', NULL, 1, NULL, NULL),
(79, 'registration.decline_button', 'fa', 'خیر', 'button', 'inline', NULL, 1, NULL, NULL),
(80, 'registration.phone_prompt', 'fa', 'شماره تفن را وارد کن', 'message', NULL, NULL, 1, NULL, NULL),
(81, 'registration.share_phone_button', 'fa', 'شماره تفن را وارد کن', 'button', 'reply', NULL, 1, NULL, NULL),
(84, 'menu.home', 'fa', 'سلام {name} 🌷\n\nشناسه کاربری: {username}\nشماره تلفن: {phone}\nموجودی کیف پول: {balance}\n\nاز منوی زیر یکی از گزینه‌ها را انتخاب کنید:', 'message', NULL, 'متن منوی اصلی کاربر', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(85, 'menu.coming_soon', 'fa', 'این بخش به‌زودی فعال خواهد شد.', 'message', NULL, 'پیام موقت بخش‌های در حال توسعه', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(86, 'menu.back_button', 'fa', '🔙 بازگشت', 'button', 'inline', 'دکمه بازگشت به منوی اصلی', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(87, 'menu.renew', 'fa', '🔄 تمدید سرویس', 'button', 'inline', 'دکمه تمدید سرویس', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(88, 'menu.shop', 'fa', '🛍 فروشگاه', 'button', 'inline', 'دکمه فروشگاه', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(89, 'menu.test_account', 'fa', '🧪 اکانت تست', 'button', 'inline', 'دکمه اکانت تست', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(90, 'menu.wallet', 'fa', '💰 کیف پول', 'button', 'inline', 'دکمه کیف پول', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(91, 'menu.services', 'fa', '📦 سرویس‌های من', 'button', 'inline', 'دکمه سرویس‌های کاربر', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(92, 'menu.plans', 'fa', '📋 پلن‌ها', 'button', 'inline', 'دکمه مشاهده پلن‌ها', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(93, 'menu.referral', 'fa', '👥 دعوت دوستان', 'button', 'inline', 'دکمه دعوت و معرفی دوستان', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(94, 'menu.tutorials', 'fa', '📚 آموزش‌ها', 'button', 'inline', 'دکمه آموزش‌ها', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(95, 'menu.support', 'fa', '🎧 پشتیبانی', 'button', 'inline', 'دکمه پشتیبانی', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(96, 'menu.representative', 'fa', '👤 نمایندگی', 'button', 'inline', 'دکمه نمایندگی', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(97, 'menu.currency_irr', 'fa', 'تومان', 'message', NULL, 'واحد نمایش موجودی کیف پول', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(98, 'menu.no_username', 'fa', 'ثبت نشده', 'message', NULL, 'Fallback قدیمی برای username؛ نباید مانع اجرای منو شود', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14'),
(99, 'menu.phone_not_registered', 'fa', 'ثبت نشده', 'message', NULL, 'Fallback برای شماره تلفن ثبت‌نشده', 1, '2026-09-13 16:51:14', '2026-09-13 16:51:14');

-- --------------------------------------------------------

--
-- Table structure for table `bot_rules`
--

CREATE TABLE `bot_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bot_settings`
--

CREATE TABLE `bot_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bot_settings`
--

INSERT INTO `bot_settings` (`id`, `key`, `value`, `type`, `group`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'features.phone_verification', 'true', 'boolean', NULL, 0, '2026-09-13 15:11:32', '2026-09-13 15:11:32'),
(2, 'features.channel_membership', 'true', 'boolean', NULL, 0, '2026-09-13 15:11:32', '2026-09-13 15:11:32'),
(3, 'notifications.enabled', 'true', 'boolean', 'notifications', 0, '2026-09-13 15:58:43', '2026-09-13 15:58:43'),
(4, 'notifications.service_expiry.3_days.enabled', 'true', 'boolean', 'notifications', 0, '2026-09-13 15:58:43', '2026-09-13 15:58:43'),
(5, 'notifications.service_expiry.3_days.hours', '72', 'integer', 'notifications', 0, '2026-09-13 15:58:43', '2026-09-13 15:58:43'),
(6, 'notifications.service_expiry.24_hours.enabled', 'true', 'boolean', 'notifications', 0, '2026-09-13 15:58:43', '2026-09-13 15:58:43'),
(7, 'notifications.service_expiry.24_hours.hours', '24', 'integer', 'notifications', 0, '2026-09-13 15:58:43', '2026-09-13 15:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `broadcasts`
--

CREATE TABLE `broadcasts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `media` json DEFAULT NULL,
  `keyboard` json DEFAULT NULL,
  `targeting` json DEFAULT NULL,
  `batch_size` int UNSIGNED NOT NULL DEFAULT '100',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `total_recipients` bigint UNSIGNED NOT NULL DEFAULT '0',
  `sent_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `failed_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_recipients`
--

CREATE TABLE `broadcast_recipients` (
  `id` bigint UNSIGNED NOT NULL,
  `broadcast_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `sent_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('horton-cache-horton.settings.all', 'a:7:{s:27:\"features.phone_verification\";a:2:{s:5:\"value\";b:1;s:9:\"is_public\";b:0;}s:27:\"features.channel_membership\";a:2:{s:5:\"value\";b:1;s:9:\"is_public\";b:0;}s:21:\"notifications.enabled\";a:2:{s:5:\"value\";b:1;s:9:\"is_public\";b:0;}s:43:\"notifications.service_expiry.3_days.enabled\";a:2:{s:5:\"value\";b:1;s:9:\"is_public\";b:0;}s:41:\"notifications.service_expiry.3_days.hours\";a:2:{s:5:\"value\";i:72;s:9:\"is_public\";b:0;}s:45:\"notifications.service_expiry.24_hours.enabled\";a:2:{s:5:\"value\";b:1;s:9:\"is_public\";b:0;}s:43:\"notifications.service_expiry.24_hours.hours\";a:2:{s:5:\"value\";i:24;s:9:\"is_public\";b:0;}}', 2104681990),
('horton-cache-telegram_bot_router.conversation.6034058150.6034058150', 'a:7:{s:4:\"name\";s:12:\"registration\";s:4:\"step\";i:1;s:4:\"data\";a:1:{s:8:\"accepted\";b:1;}s:3:\"ttl\";i:3600;s:10:\"middleware\";a:0:{}s:11:\"cache_store\";N;s:10:\"expires_at\";i:1789325591;}', 1789325595);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache_locks`
--

INSERT INTO `cache_locks` (`key`, `owner`, `expiration`) VALUES
('horton-cache-laravel_unique_job:App\\Jobs\\SendTelegramNotificationJob:telegram-notification:1', '1XzIcXD2XX1HFyEw', 1789403931),
('horton-cache-laravel_unique_job:App\\Jobs\\SendTelegramNotificationJob:telegram-notification:2', 'whYuh7iADNs3Dzuz', 1789404161),
('horton-cache-laravel_unique_job:App\\Jobs\\SendTelegramNotificationJob:telegram-notification:3', '95G3uYjNsn6kxfp6', 1789404725),
('horton-cache-laravel_unique_job:App\\Jobs\\SendTelegramNotificationJob:telegram-notification:4', 'jcMrGtWjemCkgW4K', 1789404851),
('horton-cache-laravel_unique_job:App\\Jobs\\SendTelegramNotificationJob:telegram-notification:5', 'j8ajOXh5kUeRev4H', 1789404936);

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `usage_limit` int UNSIGNED DEFAULT NULL,
  `used_count` int UNSIGNED NOT NULL DEFAULT '0',
  `configuration` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashback_accounts`
--

CREATE TABLE `cashback_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` bigint UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IRR',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashback_transactions`
--

CREATE TABLE `cashback_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `cashback_account_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint UNSIGNED NOT NULL,
  `reference_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idempotency_key` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` bigint UNSIGNED NOT NULL,
  `minimum_order_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `maximum_discount_amount` bigint UNSIGNED DEFAULT NULL,
  `usage_limit` int UNSIGNED DEFAULT NULL,
  `usage_limit_per_user` int UNSIGNED DEFAULT NULL,
  `used_count` int UNSIGNED NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_usages`
--

CREATE TABLE `discount_usages` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `amount` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gift_codes`
--

CREATE TABLE `gift_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` bigint UNSIGNED NOT NULL,
  `usage_limit` int UNSIGNED DEFAULT NULL,
  `used_count` int UNSIGNED NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gift_code_redemptions`
--

CREATE TABLE `gift_code_redemptions` (
  `id` bigint UNSIGNED NOT NULL,
  `gift_code_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `value` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'issued',
  `subtotal` bigint UNSIGNED NOT NULL DEFAULT '0',
  `discount_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `total_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IRR',
  `issued_at` timestamp NULL DEFAULT NULL,
  `due_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"9dbd8ee3-092f-4880-9606-41b402babec7\",\"displayName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,90\",\"timeout\":120,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\SendTelegramNotificationJob\\\":8:{s:9:\\\"uniqueFor\\\";i:86400;s:14:\\\"notificationId\\\";i:1;s:5:\\\"tries\\\";i:3;s:7:\\\"timeout\\\";i:120;s:7:\\\"backoff\\\";a:3:{i:0;i:10;i:1;i:30;i:2;i:90;}s:5:\\\"queue\\\";s:7:\\\"default\\\";s:15:\\\"uniqueLockOwner\\\";s:16:\\\"1XzIcXD2XX1HFyEw\\\";s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1789317532,\"illuminate:log:context\":{\"data\":[],\"hidden\":{\"laravel_unique_job_cache_store\":\"s:8:\\\"database\\\";\",\"laravel_unique_job_key\":\"s:79:\\\"laravel_unique_job:App\\\\Jobs\\\\SendTelegramNotificationJob:telegram-notification:1\\\";\",\"laravel_unique_job_lock_owner\":\"s:16:\\\"1XzIcXD2XX1HFyEw\\\";\"}},\"delay\":null}', 0, NULL, 1789317532, 1789317532),
(2, 'default', '{\"uuid\":\"d44ae94d-8041-47da-9477-c537a85ec8df\",\"displayName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,90\",\"timeout\":120,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\SendTelegramNotificationJob\\\":8:{s:9:\\\"uniqueFor\\\";i:86400;s:14:\\\"notificationId\\\";i:2;s:5:\\\"tries\\\";i:3;s:7:\\\"timeout\\\";i:120;s:7:\\\"backoff\\\";a:3:{i:0;i:10;i:1;i:30;i:2;i:90;}s:5:\\\"queue\\\";s:7:\\\"default\\\";s:15:\\\"uniqueLockOwner\\\";s:16:\\\"whYuh7iADNs3Dzuz\\\";s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1789317762,\"illuminate:log:context\":{\"data\":[],\"hidden\":{\"laravel_unique_job_cache_store\":\"s:8:\\\"database\\\";\",\"laravel_unique_job_key\":\"s:79:\\\"laravel_unique_job:App\\\\Jobs\\\\SendTelegramNotificationJob:telegram-notification:2\\\";\",\"laravel_unique_job_lock_owner\":\"s:16:\\\"whYuh7iADNs3Dzuz\\\";\"}},\"delay\":null}', 0, NULL, 1789317762, 1789317762),
(3, 'default', '{\"uuid\":\"27011c19-41de-42ed-82c1-78904028985f\",\"displayName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,90\",\"timeout\":120,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\SendTelegramNotificationJob\\\":8:{s:9:\\\"uniqueFor\\\";i:86400;s:14:\\\"notificationId\\\";i:3;s:5:\\\"tries\\\";i:3;s:7:\\\"timeout\\\";i:120;s:7:\\\"backoff\\\";a:3:{i:0;i:10;i:1;i:30;i:2;i:90;}s:5:\\\"queue\\\";s:7:\\\"default\\\";s:15:\\\"uniqueLockOwner\\\";s:16:\\\"95G3uYjNsn6kxfp6\\\";s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1789318325,\"illuminate:log:context\":{\"data\":[],\"hidden\":{\"laravel_unique_job_cache_store\":\"s:8:\\\"database\\\";\",\"laravel_unique_job_key\":\"s:79:\\\"laravel_unique_job:App\\\\Jobs\\\\SendTelegramNotificationJob:telegram-notification:3\\\";\",\"laravel_unique_job_lock_owner\":\"s:16:\\\"95G3uYjNsn6kxfp6\\\";\"}},\"delay\":null}', 0, NULL, 1789318325, 1789318325),
(4, 'default', '{\"uuid\":\"b81ded03-5de6-4e4b-b601-2106f23d9f0f\",\"displayName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,90\",\"timeout\":120,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\SendTelegramNotificationJob\\\":8:{s:9:\\\"uniqueFor\\\";i:86400;s:14:\\\"notificationId\\\";i:4;s:5:\\\"tries\\\";i:3;s:7:\\\"timeout\\\";i:120;s:7:\\\"backoff\\\";a:3:{i:0;i:10;i:1;i:30;i:2;i:90;}s:5:\\\"queue\\\";s:7:\\\"default\\\";s:15:\\\"uniqueLockOwner\\\";s:16:\\\"jcMrGtWjemCkgW4K\\\";s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1789318452,\"illuminate:log:context\":{\"data\":[],\"hidden\":{\"laravel_unique_job_cache_store\":\"s:8:\\\"database\\\";\",\"laravel_unique_job_key\":\"s:79:\\\"laravel_unique_job:App\\\\Jobs\\\\SendTelegramNotificationJob:telegram-notification:4\\\";\",\"laravel_unique_job_lock_owner\":\"s:16:\\\"jcMrGtWjemCkgW4K\\\";\"}},\"delay\":null}', 0, NULL, 1789318452, 1789318452),
(5, 'default', '{\"uuid\":\"a24f294a-7529-4f0c-a618-49257d53c7c0\",\"displayName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,90\",\"timeout\":120,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendTelegramNotificationJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\SendTelegramNotificationJob\\\":8:{s:9:\\\"uniqueFor\\\";i:86400;s:14:\\\"notificationId\\\";i:5;s:5:\\\"tries\\\";i:3;s:7:\\\"timeout\\\";i:120;s:7:\\\"backoff\\\";a:3:{i:0;i:10;i:1;i:30;i:2;i:90;}s:5:\\\"queue\\\";s:7:\\\"default\\\";s:15:\\\"uniqueLockOwner\\\";s:16:\\\"j8ajOXh5kUeRev4H\\\";s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1789318536,\"illuminate:log:context\":{\"data\":[],\"hidden\":{\"laravel_unique_job_cache_store\":\"s:8:\\\"database\\\";\",\"laravel_unique_job_key\":\"s:79:\\\"laravel_unique_job:App\\\\Jobs\\\\SendTelegramNotificationJob:telegram-notification:5\\\";\",\"laravel_unique_job_lock_owner\":\"s:16:\\\"j8ajOXh5kUeRev4H\\\";\"}},\"delay\":null}', 0, NULL, 1789318536, 1789318536);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_12_000001_create_horton_domain_schema', 1),
(5, '2026_09_12_000002_add_idempotency_key_to_payments_table', 1),
(6, '2026_09_12_000003_add_ledger_integrity_to_wallet_transactions', 1),
(7, '2026_09_12_000004_add_order_item_snapshots', 1),
(8, '2026_09_13_000002_refactor_bot_messages_content', 1),
(9, '2026_09_13_000003_create_telegram_bot_message_states_table', 1),
(10, '2026_09_13_000004_add_notification_deduplication_and_broadcast_recipients', 1),
(11, '2026_09_13_000005_add_support_departments_and_content', 1),
(12, '2026_09_13_135322_add_two_factor_columns_to_users_table', 1),
(13, '2026_09_13_135323_create_passkeys_table', 1),
(14, '2026_09_13_135710_create_personal_access_tokens_table', 1),
(15, '2026_09_13_000005_add_status_to_users_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `deduplication_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_deliveries`
--

CREATE TABLE `notification_deliveries` (
  `id` bigint UNSIGNED NOT NULL,
  `notification_id` bigint UNSIGNED NOT NULL,
  `channel` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `sent_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` bigint UNSIGNED NOT NULL DEFAULT '0',
  `discount_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `cashback_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `wallet_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `total_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IRR',
  `discount_code_id` bigint UNSIGNED DEFAULT NULL,
  `gift_code_id` bigint UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `idempotency_key` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_id` bigint UNSIGNED DEFAULT NULL,
  `plan_name_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `unit_price` bigint UNSIGNED NOT NULL DEFAULT '0',
  `discount_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `total_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `duration_value_snapshot` int UNSIGNED DEFAULT NULL,
  `duration_unit_snapshot` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity_value_snapshot` bigint UNSIGNED DEFAULT NULL,
  `capacity_unit_snapshot` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_trial_snapshot` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passkeys`
--

CREATE TABLE `passkeys` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential` json NOT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` bigint UNSIGNED NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IRR',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_attempts`
--

CREATE TABLE `payment_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_id` bigint UNSIGNED NOT NULL,
  `gateway` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_reference` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount` bigint UNSIGNED NOT NULL,
  `response_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_callbacks`
--

CREATE TABLE `payment_callbacks` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_id` bigint UNSIGNED NOT NULL,
  `gateway` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received',
  `payload` json NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'users.view', 'users.view', 'HORTON dashboard permission: users.view', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(2, 'users.manage', 'users.manage', 'HORTON dashboard permission: users.manage', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(3, 'wallet.view', 'wallet.view', 'HORTON dashboard permission: wallet.view', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(4, 'wallet.credit', 'wallet.credit', 'HORTON dashboard permission: wallet.credit', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(5, 'wallet.debit', 'wallet.debit', 'HORTON dashboard permission: wallet.debit', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(6, 'wallet.manage', 'wallet.manage', 'HORTON dashboard permission: wallet.manage', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(7, 'support.departments.view', 'support.departments.view', 'HORTON dashboard permission: support.departments.view', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(8, 'support.departments.manage', 'support.departments.manage', 'HORTON dashboard permission: support.departments.manage', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(9, 'support.content.view', 'support.content.view', 'HORTON dashboard permission: support.content.view', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(10, 'support.content.manage', 'support.content.manage', 'HORTON dashboard permission: support.content.manage', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(11, 'support.tickets.view', 'support.tickets.view', 'HORTON dashboard permission: support.tickets.view', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(12, 'support.tickets.reply', 'support.tickets.reply', 'HORTON dashboard permission: support.tickets.reply', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(13, 'support.tickets.manage', 'support.tickets.manage', 'HORTON dashboard permission: support.tickets.manage', '2026-09-13 15:10:06', '2026-09-13 15:10:06'),
(14, 'View notifications', 'notifications.view', 'View generated notifications and delivery state.', '2026-09-13 15:59:10', '2026-09-13 15:59:10'),
(15, 'View broadcasts', 'broadcasts.view', 'View broadcast definitions and delivery counters.', '2026-09-13 15:59:10', '2026-09-13 15:59:10'),
(16, 'Create broadcasts', 'broadcasts.create', 'Create and schedule broadcasts.', '2026-09-13 15:59:10', '2026-09-13 15:59:10'),
(17, 'Send broadcasts', 'broadcasts.send', 'Queue a broadcast for delivery.', '2026-09-13 15:59:10', '2026-09-13 15:59:10'),
(18, 'Cancel broadcasts', 'broadcasts.cancel', 'Cancel a queued or sending broadcast.', '2026-09-13 15:59:10', '2026-09-13 15:59:10');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` bigint UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IRR',
  `duration_value` int UNSIGNED NOT NULL DEFAULT '0',
  `duration_unit` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'day',
  `capacity_value` bigint UNSIGNED DEFAULT NULL,
  `capacity_unit` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_trial` tinyint(1) NOT NULL DEFAULT '0',
  `trial_duration_value` int UNSIGNED DEFAULT NULL,
  `trial_duration_unit` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_prices`
--

CREATE TABLE `plan_prices` (
  `id` bigint UNSIGNED NOT NULL,
  `plan_id` bigint UNSIGNED NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` bigint UNSIGNED NOT NULL,
  `referrer_user_id` bigint UNSIGNED NOT NULL,
  `referred_user_id` bigint UNSIGNED NOT NULL,
  `source` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registered',
  `registered_at` timestamp NULL DEFAULT NULL,
  `qualified_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_accounts`
--

CREATE TABLE `referral_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_rate` decimal(5,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `cashback_rate` decimal(5,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Support & User Manager', 'support-manager', 'Manage users, wallets and support.', '2026-09-13 15:10:06', '2026-09-13 15:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `role_id` bigint UNSIGNED NOT NULL,
  `admin_user_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`role_id`, `admin_user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `order_item_id` bigint UNSIGNED DEFAULT NULL,
  `plan_id` bigint UNSIGNED NOT NULL,
  `service_provider_id` bigint UNSIGNED NOT NULL,
  `provider_account_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `capacity` bigint UNSIGNED DEFAULT NULL,
  `used_capacity` bigint UNSIGNED NOT NULL DEFAULT '0',
  `is_trial` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_operations`
--

CREATE TABLE `service_operations` (
  `id` bigint UNSIGNED NOT NULL,
  `service_id` bigint UNSIGNED NOT NULL,
  `operation` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `request_metadata` json DEFAULT NULL,
  `response_metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `configuration` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_provider_accounts`
--

CREATE TABLE `service_provider_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `service_provider_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credentials` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `priority` int UNSIGNED NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1SUZl4VhVpmWPz9ain30Ic3EIPFw8YBtcGe0P2Ws', NULL, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJkU3ZSWFdOQVNYOW1GU00zNnRCN1c0UzZvZzR3YnhQMkRrQlBhMVRMIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1789313932),
('A1kSgCR52joUchjR0gyVbOizyJiOILqQsFLSuqOq', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJFbVVndTlzRTV6UjNVVTVaOE5BUklKWHBXYzZKQ1R4TjhDdjNkWkxyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvbG9naW4iLCJyb3V0ZSI6ImxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1789309185),
('JaffP5z2QYW7pctZayA2JClXgjrPYlbWzYm7Abxf', 1, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5QlF1OWNzVzdIZHIzbjRXSkhrVElKVDFWT0FjOGlDcXZmQjVDcmVwIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC91c2Vycz9xPVRhdGFsb29fb29yZyZzdGF0dXM9Iiwicm91dGUiOiJhZG1pbi51c2Vycy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1789314786),
('uNeZ9HxwB9XxMJi1UvmqbTHEP6InwiubSQkaDL0s', NULL, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJXazBvRHROa0x6aG1OcE5aWkZ4QktTbGE1ckVEM0xDMlZ1OUppOGsyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1789375725);

-- --------------------------------------------------------

--
-- Table structure for table `support_contents`
--

CREATE TABLE `support_contents` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'faq',
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_departments`
--

CREATE TABLE `support_departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `sender_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `priority` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `sensitivity` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `assigned_admin_id` bigint UNSIGNED DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_accounts`
--

CREATE TABLE `telegram_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `telegram_user_id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_bot` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `telegram_accounts`
--

INSERT INTO `telegram_accounts` (`id`, `user_id`, `telegram_user_id`, `username`, `first_name`, `last_name`, `phone`, `language_code`, `is_bot`, `is_active`, `last_seen_at`, `created_at`, `updated_at`) VALUES
(19, 22, 1577775109, 'hossein3776', 'Hossein', 'Akbari', NULL, 'en', 0, 1, '2026-09-13 17:34:45', '2026-09-13 17:34:35', '2026-09-13 17:34:45'),
(20, 23, 6034058150, 'Mr_x37_76', '.', NULL, NULL, 'en', 0, 1, '2026-09-13 17:53:17', '2026-09-13 17:53:11', '2026-09-13 17:53:17');

-- --------------------------------------------------------

--
-- Table structure for table `telegram_bot_message_states`
--

CREATE TABLE `telegram_bot_message_states` (
  `id` bigint UNSIGNED NOT NULL,
  `chat_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `telegram_bot_message_states`
--

INSERT INTO `telegram_bot_message_states` (`id`, `chat_id`, `message_id`, `created_at`, `updated_at`) VALUES
(7, '1577775109', 1133, '2026-09-13 17:34:42', '2026-09-13 17:34:42'),
(8, '6034058150', 1137, '2026-09-13 17:53:14', '2026-09-13 17:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `status`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Horton Admin', 'jookeremasst@gmail.com', NULL, '$2y$12$/cRtbnoQuO7rVN6KL2HNoOST8Ege9cfhvgx/rhaFB5x0/URH74Zse', 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-13 14:24:55', '2026-09-13 14:24:55'),
(2, 'Tataloo_oorg', 'jookeremasst3@gmail.com', NULL, '$2y$12$ib/qzdD6yWyBcI68OXUUM..dZ1Fs.mmLk.Rdzqh24onbnzT5amFRu', 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-13 14:29:37', '2026-09-13 14:29:37'),
(22, 'Hossein Akbari', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-13 17:34:35', '2026-09-13 17:34:35'),
(23, '.', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-13 17:53:10', '2026-09-13 17:53:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `avatar`, `metadata`, `created_at`, `updated_at`) VALUES
(19, 22, NULL, '[]', '2026-09-13 17:34:35', '2026-09-13 17:34:35'),
(20, 23, NULL, '[]', '2026-09-13 17:53:11', '2026-09-13 17:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` bigint UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IRR',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `currency`, `status`, `created_at`, `updated_at`) VALUES
(20, 22, 0, 'IRR', 'active', '2026-09-13 17:34:35', '2026-09-13 17:34:35'),
(21, 23, 0, 'IRR', 'active', '2026-09-13 17:53:11', '2026-09-13 17:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint UNSIGNED NOT NULL,
  `balance_before` bigint UNSIGNED NOT NULL,
  `balance_after` bigint UNSIGNED NOT NULL,
  `reference_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idempotency_key` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ledger_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_email_unique` (`email`),
  ADD KEY `admin_users_status_index` (`status`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `audit_logs_admin_user_id_created_at_index` (`admin_user_id`,`created_at`),
  ADD KEY `audit_logs_action_created_at_index` (`action`,`created_at`);

--
-- Indexes for table `bot_channels`
--
ALTER TABLE `bot_channels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bot_channels_telegram_chat_id_unique` (`telegram_chat_id`),
  ADD KEY `bot_channels_username_index` (`username`),
  ADD KEY `bot_channels_is_required_index` (`is_required`),
  ADD KEY `bot_channels_is_active_index` (`is_active`);

--
-- Indexes for table `bot_messages`
--
ALTER TABLE `bot_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bot_messages_key_locale_unique` (`key`,`locale`),
  ADD KEY `bot_messages_is_active_index` (`is_active`);

--
-- Indexes for table `bot_rules`
--
ALTER TABLE `bot_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bot_rules_key_unique` (`key`),
  ADD KEY `bot_rules_is_active_index` (`is_active`);

--
-- Indexes for table `bot_settings`
--
ALTER TABLE `bot_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bot_settings_key_unique` (`key`),
  ADD KEY `bot_settings_group_index` (`group`);

--
-- Indexes for table `broadcasts`
--
ALTER TABLE `broadcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broadcasts_status_index` (`status`),
  ADD KEY `broadcasts_scheduled_at_index` (`scheduled_at`);

--
-- Indexes for table `broadcast_recipients`
--
ALTER TABLE `broadcast_recipients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `broadcast_recipients_broadcast_id_user_id_unique` (`broadcast_id`,`user_id`),
  ADD KEY `broadcast_recipients_broadcast_id_status_index` (`broadcast_id`,`status`),
  ADD KEY `broadcast_recipients_user_id_status_index` (`user_id`,`status`),
  ADD KEY `broadcast_recipients_status_index` (`status`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaigns_status_index` (`status`),
  ADD KEY `campaigns_starts_at_index` (`starts_at`),
  ADD KEY `campaigns_ends_at_index` (`ends_at`);

--
-- Indexes for table `cashback_accounts`
--
ALTER TABLE `cashback_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cashback_accounts_user_id_currency_unique` (`user_id`,`currency`);

--
-- Indexes for table `cashback_transactions`
--
ALTER TABLE `cashback_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cashback_transactions_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `cashback_transactions_cashback_account_id_created_at_index` (`cashback_account_id`,`created_at`),
  ADD KEY `cashback_transactions_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `cashback_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_status_sort_order_index` (`parent_id`,`status`,`sort_order`),
  ADD KEY `categories_status_index` (`status`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_codes_code_unique` (`code`),
  ADD KEY `discount_codes_starts_at_index` (`starts_at`),
  ADD KEY `discount_codes_expires_at_index` (`expires_at`),
  ADD KEY `discount_codes_is_active_index` (`is_active`);

--
-- Indexes for table `discount_usages`
--
ALTER TABLE `discount_usages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_usages_discount_code_id_order_id_unique` (`discount_code_id`,`order_id`),
  ADD KEY `discount_usages_user_id_foreign` (`user_id`),
  ADD KEY `discount_usages_order_id_foreign` (`order_id`),
  ADD KEY `discount_usages_discount_code_id_user_id_index` (`discount_code_id`,`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `gift_codes`
--
ALTER TABLE `gift_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gift_codes_code_unique` (`code`),
  ADD KEY `gift_codes_starts_at_index` (`starts_at`),
  ADD KEY `gift_codes_expires_at_index` (`expires_at`),
  ADD KEY `gift_codes_is_active_index` (`is_active`);

--
-- Indexes for table `gift_code_redemptions`
--
ALTER TABLE `gift_code_redemptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gift_code_redemptions_gift_code_id_user_id_unique` (`gift_code_id`,`user_id`),
  ADD KEY `gift_code_redemptions_user_id_foreign` (`user_id`),
  ADD KEY `gift_code_redemptions_order_id_foreign` (`order_id`),
  ADD KEY `gift_code_redemptions_gift_code_id_order_id_index` (`gift_code_id`,`order_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_order_id_unique` (`order_id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_status_index` (`status`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notifications_deduplication_key_unique` (`deduplication_key`),
  ADD KEY `notifications_user_id_read_at_created_at_index` (`user_id`,`read_at`,`created_at`),
  ADD KEY `notifications_read_at_index` (`read_at`);

--
-- Indexes for table `notification_deliveries`
--
ALTER TABLE `notification_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_deliveries_notification_id_channel_unique` (`notification_id`,`channel`),
  ADD KEY `notification_deliveries_channel_status_index` (`channel`,`status`),
  ADD KEY `notification_deliveries_status_index` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `orders_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `orders_user_id_status_created_at_index` (`user_id`,`status`,`created_at`),
  ADD KEY `orders_discount_code_id_index` (`discount_code_id`),
  ADD KEY `orders_gift_code_id_index` (`gift_code_id`),
  ADD KEY `orders_status_index` (`status`),
  ADD KEY `orders_paid_at_index` (`paid_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_plan_id_foreign` (`plan_id`),
  ADD KEY `order_items_order_id_product_id_plan_id_index` (`order_id`,`product_id`,`plan_id`);

--
-- Indexes for table `passkeys`
--
ALTER TABLE `passkeys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  ADD KEY `passkeys_user_id_index` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `payments_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `payments_user_id_foreign` (`user_id`),
  ADD KEY `payments_order_id_status_index` (`order_id`,`status`),
  ADD KEY `payments_gateway_transaction_id_index` (`gateway`,`transaction_id`),
  ADD KEY `payments_gateway_reference_id_index` (`gateway`,`reference_id`),
  ADD KEY `payments_status_index` (`status`);

--
-- Indexes for table `payment_attempts`
--
ALTER TABLE `payment_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_attempts_payment_id_status_index` (`payment_id`,`status`),
  ADD KEY `payment_attempts_request_id_index` (`request_id`),
  ADD KEY `payment_attempts_status_index` (`status`);

--
-- Indexes for table `payment_callbacks`
--
ALTER TABLE `payment_callbacks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_callbacks_callback_id_unique` (`callback_id`),
  ADD KEY `payment_callbacks_payment_id_status_index` (`payment_id`,`status`),
  ADD KEY `payment_callbacks_status_index` (`status`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plans_product_id_slug_unique` (`product_id`,`slug`),
  ADD KEY `plans_product_id_status_sort_order_index` (`product_id`,`status`,`sort_order`),
  ADD KEY `plans_is_trial_index` (`is_trial`),
  ADD KEY `plans_status_index` (`status`);

--
-- Indexes for table `plan_prices`
--
ALTER TABLE `plan_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_prices_plan_id_currency_starts_at_ends_at_index` (`plan_id`,`currency`,`starts_at`,`ends_at`),
  ADD KEY `plan_prices_is_default_index` (`is_default`),
  ADD KEY `plan_prices_starts_at_index` (`starts_at`),
  ADD KEY `plan_prices_ends_at_index` (`ends_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_category_id_status_sort_order_index` (`category_id`,`status`,`sort_order`),
  ADD KEY `products_status_index` (`status`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referrals_referred_user_id_unique` (`referred_user_id`),
  ADD KEY `referrals_referrer_user_id_status_index` (`referrer_user_id`,`status`),
  ADD KEY `referrals_status_index` (`status`);

--
-- Indexes for table `referral_accounts`
--
ALTER TABLE `referral_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral_accounts_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `referral_accounts_code_unique` (`code`),
  ADD KEY `referral_accounts_status_index` (`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`role_id`,`admin_user_id`),
  ADD KEY `role_user_admin_user_id_foreign` (`admin_user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `services_service_provider_id_external_id_unique` (`service_provider_id`,`external_id`),
  ADD KEY `services_order_id_foreign` (`order_id`),
  ADD KEY `services_order_item_id_foreign` (`order_item_id`),
  ADD KEY `services_plan_id_foreign` (`plan_id`),
  ADD KEY `services_provider_account_id_foreign` (`provider_account_id`),
  ADD KEY `services_user_id_status_expires_at_index` (`user_id`,`status`,`expires_at`),
  ADD KEY `services_service_provider_id_status_index` (`service_provider_id`,`status`),
  ADD KEY `services_status_index` (`status`),
  ADD KEY `services_expires_at_index` (`expires_at`),
  ADD KEY `services_is_trial_index` (`is_trial`);

--
-- Indexes for table `service_operations`
--
ALTER TABLE `service_operations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_operations_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `service_operations_service_id_operation_status_index` (`service_id`,`operation`,`status`),
  ADD KEY `service_operations_status_index` (`status`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_providers_slug_unique` (`slug`),
  ADD KEY `service_providers_status_index` (`status`);

--
-- Indexes for table `service_provider_accounts`
--
ALTER TABLE `service_provider_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spa_provider_status_priority_idx` (`service_provider_id`,`status`,`priority`),
  ADD KEY `service_provider_accounts_identifier_index` (`identifier`),
  ADD KEY `service_provider_accounts_status_index` (`status`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `support_contents`
--
ALTER TABLE `support_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_contents_department_id_foreign` (`department_id`),
  ADD KEY `support_contents_type_is_active_sort_order_index` (`type`,`is_active`,`sort_order`),
  ADD KEY `support_contents_type_index` (`type`),
  ADD KEY `support_contents_category_index` (`category`),
  ADD KEY `support_contents_is_active_index` (`is_active`);

--
-- Indexes for table `support_departments`
--
ALTER TABLE `support_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_departments_slug_unique` (`slug`),
  ADD KEY `support_departments_is_active_sort_order_index` (`is_active`,`sort_order`),
  ADD KEY `support_departments_is_active_index` (`is_active`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_messages_ticket_id_created_at_index` (`ticket_id`,`created_at`),
  ADD KEY `support_messages_sender_type_sender_id_index` (`sender_type`,`sender_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_tickets_uuid_unique` (`uuid`),
  ADD KEY `support_tickets_assigned_admin_id_foreign` (`assigned_admin_id`),
  ADD KEY `support_tickets_user_id_status_index` (`user_id`,`status`),
  ADD KEY `support_tickets_status_index` (`status`),
  ADD KEY `support_tickets_priority_index` (`priority`),
  ADD KEY `support_tickets_last_message_at_index` (`last_message_at`),
  ADD KEY `support_tickets_department_id_status_sensitivity_index` (`department_id`,`status`,`sensitivity`),
  ADD KEY `support_tickets_sensitivity_index` (`sensitivity`);

--
-- Indexes for table `telegram_accounts`
--
ALTER TABLE `telegram_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_accounts_telegram_user_id_unique` (`telegram_user_id`),
  ADD KEY `telegram_accounts_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `telegram_accounts_username_index` (`username`),
  ADD KEY `telegram_accounts_is_active_index` (`is_active`),
  ADD KEY `telegram_accounts_last_seen_at_index` (`last_seen_at`);

--
-- Indexes for table `telegram_bot_message_states`
--
ALTER TABLE `telegram_bot_message_states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_bot_message_states_chat_id_unique` (`chat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_status_index` (`status`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_profiles_user_id_unique` (`user_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallets_user_id_currency_unique` (`user_id`,`currency`),
  ADD KEY `wallets_status_index` (`status`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallet_transactions_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `wallet_transactions_wallet_id_created_at_index` (`wallet_id`,`created_at`),
  ADD KEY `wallet_transactions_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `wallet_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `wallet_transactions_wallet_id_ledger_hash_index` (`wallet_id`,`ledger_hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bot_channels`
--
ALTER TABLE `bot_channels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bot_messages`
--
ALTER TABLE `bot_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `bot_rules`
--
ALTER TABLE `bot_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bot_settings`
--
ALTER TABLE `bot_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `broadcasts`
--
ALTER TABLE `broadcasts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `broadcast_recipients`
--
ALTER TABLE `broadcast_recipients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cashback_accounts`
--
ALTER TABLE `cashback_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cashback_transactions`
--
ALTER TABLE `cashback_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_usages`
--
ALTER TABLE `discount_usages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gift_codes`
--
ALTER TABLE `gift_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gift_code_redemptions`
--
ALTER TABLE `gift_code_redemptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notification_deliveries`
--
ALTER TABLE `notification_deliveries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `passkeys`
--
ALTER TABLE `passkeys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_attempts`
--
ALTER TABLE `payment_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_callbacks`
--
ALTER TABLE `payment_callbacks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_prices`
--
ALTER TABLE `plan_prices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_accounts`
--
ALTER TABLE `referral_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_operations`
--
ALTER TABLE `service_operations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_provider_accounts`
--
ALTER TABLE `service_provider_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_contents`
--
ALTER TABLE `support_contents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_departments`
--
ALTER TABLE `support_departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telegram_accounts`
--
ALTER TABLE `telegram_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `telegram_bot_message_states`
--
ALTER TABLE `telegram_bot_message_states`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `broadcast_recipients`
--
ALTER TABLE `broadcast_recipients`
  ADD CONSTRAINT `broadcast_recipients_broadcast_id_foreign` FOREIGN KEY (`broadcast_id`) REFERENCES `broadcasts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `broadcast_recipients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cashback_accounts`
--
ALTER TABLE `cashback_accounts`
  ADD CONSTRAINT `cashback_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `cashback_transactions`
--
ALTER TABLE `cashback_transactions`
  ADD CONSTRAINT `cashback_transactions_cashback_account_id_foreign` FOREIGN KEY (`cashback_account_id`) REFERENCES `cashback_accounts` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `cashback_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `discount_usages`
--
ALTER TABLE `discount_usages`
  ADD CONSTRAINT `discount_usages_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `discount_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `discount_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `gift_code_redemptions`
--
ALTER TABLE `gift_code_redemptions`
  ADD CONSTRAINT `gift_code_redemptions_gift_code_id_foreign` FOREIGN KEY (`gift_code_id`) REFERENCES `gift_codes` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `gift_code_redemptions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gift_code_redemptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_deliveries`
--
ALTER TABLE `notification_deliveries`
  ADD CONSTRAINT `notification_deliveries_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_gift_code_id_foreign` FOREIGN KEY (`gift_code_id`) REFERENCES `gift_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `passkeys`
--
ALTER TABLE `passkeys`
  ADD CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `payment_attempts`
--
ALTER TABLE `payment_attempts`
  ADD CONSTRAINT `payment_attempts_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_callbacks`
--
ALTER TABLE `payment_callbacks`
  ADD CONSTRAINT `payment_callbacks_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `plans_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `plan_prices`
--
ALTER TABLE `plan_prices`
  ADD CONSTRAINT `plan_prices_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_referred_user_id_foreign` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `referrals_referrer_user_id_foreign` FOREIGN KEY (`referrer_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `referral_accounts`
--
ALTER TABLE `referral_accounts`
  ADD CONSTRAINT `referral_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `services_provider_account_id_foreign` FOREIGN KEY (`provider_account_id`) REFERENCES `service_provider_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `service_operations`
--
ALTER TABLE `service_operations`
  ADD CONSTRAINT `service_operations_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_provider_accounts`
--
ALTER TABLE `service_provider_accounts`
  ADD CONSTRAINT `service_provider_accounts_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_contents`
--
ALTER TABLE `support_contents`
  ADD CONSTRAINT `support_contents_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `support_departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_assigned_admin_id_foreign` FOREIGN KEY (`assigned_admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `support_departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `telegram_accounts`
--
ALTER TABLE `telegram_accounts`
  ADD CONSTRAINT `telegram_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

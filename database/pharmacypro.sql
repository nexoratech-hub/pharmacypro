-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 03:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pharmacypro`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target` enum('all','trial','active','suspended','specific') NOT NULL DEFAULT 'all',
  `specific_pharmacy_id` int(10) UNSIGNED DEFAULT NULL,
  `priority` enum('low','normal','high') NOT NULL DEFAULT 'normal',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_name` varchar(200) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `pharmacy_id`, `branch_id`, `user_id`, `user_name`, `action`, `module`, `description`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, NULL, 1, 'BismartTech Admin', 'system_init', 'system', 'Database initialized with demo pharmacy Admin1', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(200) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `alt_phone` varchar(30) DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_headquarters` tinyint(1) NOT NULL DEFAULT 0,
  `manager_name` varchar(200) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `pharmacy_id`, `branch_code`, `branch_name`, `phone`, `alt_phone`, `whatsapp`, `email`, `region`, `district`, `address`, `logo`, `is_headquarters`, `manager_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'HQ', 'PharmacyPro Demo - HQ', '+255712345678', '+255712345679', '+255712345678', 'hq@pharmacypro.co.tz', 'Dar es Salaam', 'Kinondoni', 'Mikocheni, Dar es Salaam', NULL, 1, 'Admin1 Owner', 'active', '2026-11-11 14:41:10', '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `module_code` varchar(50) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `category` enum('core','operations','finance','reports','advanced') NOT NULL DEFAULT 'core',
  `is_core` tinyint(1) NOT NULL DEFAULT 0,
  `default_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `module_code`, `module_name`, `description`, `icon`, `category`, `is_core`, `default_enabled`, `sort_order`, `created_at`) VALUES
(1, 'dashboard', 'Dashboard', 'Main overview dashboard', 'fa-chart-pie', 'core', 1, 1, 10, '2026-11-11 14:41:10'),
(2, 'pos', 'Point of Sale', 'Sell products fast via POS', 'fa-cash-register', 'operations', 1, 1, 20, '2026-11-11 14:41:10'),
(3, 'products', 'Products', 'Product catalog management', 'fa-box', 'operations', 1, 1, 30, '2026-11-11 14:41:10'),
(4, 'inventory', 'Inventory', 'Stock levels and batches', 'fa-warehouse', 'operations', 1, 1, 40, '2026-11-11 14:41:10'),
(5, 'sales', 'Sales', 'Sales history and receipts', 'fa-receipt', 'operations', 1, 1, 50, '2026-11-11 14:41:10'),
(6, 'customers', 'Customers', 'Customer records', 'fa-users', 'operations', 0, 1, 60, '2026-11-11 14:41:10'),
(7, 'suppliers', 'Suppliers', 'Supplier records', 'fa-truck', 'operations', 0, 1, 70, '2026-11-11 14:41:10'),
(8, 'purchases', 'Purchases', 'Purchase orders and stock-in', 'fa-shopping-bag', 'operations', 0, 1, 80, '2026-11-11 14:41:10'),
(9, 'debts', 'Debts', 'Credit sales and debt collection', 'fa-file-invoice', 'finance', 0, 1, 90, '2026-11-11 14:41:10'),
(10, 'expenses', 'Expenses', 'Business expenses tracking', 'fa-money-bill', 'finance', 0, 1, 100, '2026-11-11 14:41:10'),
(11, 'reports', 'Reports', 'Sales, stock, profit reports', 'fa-chart-line', 'reports', 0, 1, 110, '2026-11-11 14:41:10'),
(12, 'reports_advanced', 'Advanced Reports', 'Advanced analytics and exports', 'fa-chart-bar', 'reports', 0, 0, 120, '2026-11-11 14:41:10'),
(13, 'multibranch', 'Multi-Branch', 'Manage multiple pharmacy branches', 'fa-code-branch', 'advanced', 0, 0, 130, '2026-11-11 14:41:10'),
(14, 'employees', 'Employees', 'Employee management', 'fa-user-tie', 'advanced', 0, 1, 140, '2026-11-11 14:41:10'),
(15, 'notifications', 'Notifications', 'In-app notifications', 'fa-bell', 'core', 1, 1, 150, '2026-11-11 14:41:10'),
(16, 'audit_logs', 'Audit Logs', 'Activity history', 'fa-history', 'advanced', 0, 0, 160, '2026-11-11 14:41:10'),
(17, 'api_access', 'API Access', 'REST API for integrations', 'fa-plug', 'advanced', 0, 0, 170, '2026-11-11 14:41:10'),
(18, 'settings', 'Settings', 'Pharmacy and module settings', 'fa-cog', 'core', 1, 1, 999, '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `module_settings`
--

CREATE TABLE `module_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `data_type` enum('string','integer','decimal','boolean','json') NOT NULL DEFAULT 'string',
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `module_settings`
--

INSERT INTO `module_settings` (`id`, `pharmacy_id`, `module_id`, `setting_key`, `setting_value`, `data_type`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'vat_enabled', '1', 'boolean', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(2, 1, 2, 'vat_percentage', '18', 'decimal', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(3, 1, 2, 'receipt_footer', 'Asante kwa kununua!', 'string', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(4, 1, 2, 'currency_symbol', 'TSh', 'string', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(5, 1, 2, 'allow_credit_sales', '1', 'boolean', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(6, 1, 2, 'auto_print_receipt', '0', 'boolean', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(8, 1, 4, 'low_stock_threshold', '10', 'integer', 0, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(9, 1, 4, 'expiry_warning_days', '30', 'integer', 0, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(10, 1, 4, 'require_batch_number', '1', 'boolean', 0, '2026-11-11 14:41:10', '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `pharmacy_id`, `branch_id`, `user_id`, `type`, `title`, `message`, `link`, `priority`, `is_read`, `read_at`, `created_at`) VALUES
(1, 1, NULL, 2, 'system', 'Karibu PharmacyPro!', 'Karibu kwenye PharmacyPro! Trial yako imeanza. Badilisha password yako mara moja.', NULL, 'high', 0, NULL, '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `permission_code` varchar(100) NOT NULL,
  `permission_name` varchar(150) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_code`, `permission_name`, `module`, `description`, `created_at`) VALUES
(1, 'pos.access', 'Access POS', 'pos', NULL, '2026-11-11 14:41:10'),
(2, 'pos.sale.create', 'Create sale', 'pos', NULL, '2026-11-11 14:41:10'),
(3, 'pos.sale.discount', 'Apply discount', 'pos', NULL, '2026-11-11 14:41:10'),
(4, 'pos.sale.refund', 'Process refund', 'pos', NULL, '2026-11-11 14:41:10'),
(5, 'products.view', 'View products', 'products', NULL, '2026-11-11 14:41:10'),
(6, 'products.create', 'Add products', 'products', NULL, '2026-11-11 14:41:10'),
(7, 'products.edit', 'Edit products', 'products', NULL, '2026-11-11 14:41:10'),
(8, 'products.delete', 'Archive products', 'products', NULL, '2026-11-11 14:41:10'),
(9, 'inventory.view', 'View inventory', 'inventory', NULL, '2026-11-11 14:41:10'),
(10, 'inventory.adjust', 'Adjust stock', 'inventory', NULL, '2026-11-11 14:41:10'),
(11, 'inventory.purchase', 'Record purchases', 'inventory', NULL, '2026-11-11 14:41:10'),
(12, 'sales.view', 'View sales', 'sales', NULL, '2026-11-11 14:41:10'),
(13, 'sales.view_all', 'View all sales', 'sales', NULL, '2026-11-11 14:41:10'),
(14, 'sales.export', 'Export sales', 'sales', NULL, '2026-11-11 14:41:10'),
(15, 'customers.view', 'View customers', 'customers', NULL, '2026-11-11 14:41:10'),
(16, 'customers.create', 'Add customers', 'customers', NULL, '2026-11-11 14:41:10'),
(17, 'customers.edit', 'Edit customers', 'customers', NULL, '2026-11-11 14:41:10'),
(18, 'debts.view', 'View debts', 'debts', NULL, '2026-11-11 14:41:10'),
(19, 'debts.collect', 'Collect debt payments', 'debts', NULL, '2026-11-11 14:41:10'),
(20, 'expenses.view', 'View expenses', 'expenses', NULL, '2026-11-11 14:41:10'),
(21, 'expenses.create', 'Record expenses', 'expenses', NULL, '2026-11-11 14:41:10'),
(22, 'employees.view', 'View employees', 'employees', NULL, '2026-11-11 14:41:10'),
(23, 'employees.create', 'Add employees', 'employees', NULL, '2026-11-11 14:41:10'),
(24, 'employees.edit', 'Edit employees', 'employees', NULL, '2026-11-11 14:41:10'),
(25, 'employees.delete', 'Deactivate employees', 'employees', NULL, '2026-11-11 14:41:10'),
(26, 'reports.view', 'View reports', 'reports', NULL, '2026-11-11 14:41:10'),
(27, 'reports.financial', 'View financial reports', 'reports', NULL, '2026-11-11 14:41:10'),
(28, 'reports.export', 'Export reports', 'reports', NULL, '2026-11-11 14:41:10'),
(29, 'reports.advanced', 'View advanced reports', 'reports', NULL, '2026-11-11 14:41:10'),
(30, 'settings.view', 'View settings', 'settings', NULL, '2026-11-11 14:41:10'),
(31, 'settings.edit', 'Edit settings', 'settings', NULL, '2026-11-11 14:41:10'),
(32, 'settings.subscription', 'Manage subscription', 'settings', NULL, '2026-11-11 14:41:10'),
(33, 'settings.modules', 'Manage modules', 'settings', NULL, '2026-11-11 14:41:10'),
(34, 'settings.branches', 'Manage branches', 'settings', NULL, '2026-11-11 14:41:10'),
(35, 'branches.view', 'View branches', 'branches', NULL, '2026-11-11 14:41:10'),
(36, 'branches.create', 'Create branch', 'branches', NULL, '2026-11-11 14:41:10'),
(37, 'branches.edit', 'Edit branch', 'branches', NULL, '2026-11-11 14:41:10'),
(38, 'branches.delete', 'Delete branch', 'branches', NULL, '2026-11-11 14:41:10'),
(39, 'platform.pharmacies', 'Manage pharmacies', 'platform', NULL, '2026-11-11 14:41:10'),
(40, 'platform.subscriptions', 'Manage subscriptions', 'platform', NULL, '2026-11-11 14:41:10'),
(41, 'platform.payments', 'Verify payments', 'platform', NULL, '2026-11-11 14:41:10'),
(42, 'platform.modules', 'Manage modules', 'platform', NULL, '2026-11-11 14:41:10'),
(43, 'platform.audit_logs', 'View audit logs', 'platform', NULL, '2026-11-11 14:41:10'),
(44, 'platform.system_settings', 'Manage system settings', 'platform', NULL, '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacies`
--

CREATE TABLE `pharmacies` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_code` varchar(50) NOT NULL,
  `pharmacy_name` varchar(200) NOT NULL,
  `owner_name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `alt_phone` varchar(30) DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('trial','active','suspended','expired','cancelled') NOT NULL DEFAULT 'trial',
  `trial_ends_at` datetime DEFAULT NULL,
  `subscription_plan_id` int(10) UNSIGNED DEFAULT NULL,
  `subscription_starts_at` datetime DEFAULT NULL,
  `subscription_ends_at` datetime DEFAULT NULL,
  `last_payment_at` datetime DEFAULT NULL,
  `total_paid` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacies`
--

INSERT INTO `pharmacies` (`id`, `pharmacy_code`, `pharmacy_name`, `owner_name`, `email`, `phone`, `alt_phone`, `whatsapp`, `website`, `region`, `district`, `address`, `logo`, `status`, `trial_ends_at`, `subscription_plan_id`, `subscription_starts_at`, `subscription_ends_at`, `last_payment_at`, `total_paid`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, 'PHARM-001', 'PharmacyPro Demo', 'Admin1 Owner', 'admin1@pharmacypro.co.tz', '+255712345678', '+255712345679', '+255712345678', 'https://pharmacypro.co.tz', 'Dar es Salaam', 'Kinondoni', 'Mikocheni, Dar es Salaam', NULL, 'trial', '2026-11-25 17:41:10', 3, NULL, NULL, NULL, 0.00, 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_modules`
--

CREATE TABLE `pharmacy_modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `enabled_at` datetime DEFAULT NULL,
  `disabled_at` datetime DEFAULT NULL,
  `enabled_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_modules`
--

INSERT INTO `pharmacy_modules` (`id`, `pharmacy_id`, `module_id`, `is_enabled`, `enabled_at`, `disabled_at`, `enabled_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(2, 1, 2, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(3, 1, 3, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(4, 1, 4, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(5, 1, 5, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(6, 1, 6, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(7, 1, 7, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(8, 1, 8, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(9, 1, 9, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(10, 1, 10, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(11, 1, 11, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(12, 1, 12, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(13, 1, 13, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(14, 1, 14, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(15, 1, 15, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(16, 1, 16, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(17, 1, 18, 1, '2026-11-11 17:41:10', NULL, 1, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_settings`
--

CREATE TABLE `pharmacy_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `data_type` enum('string','integer','decimal','boolean','json') NOT NULL DEFAULT 'string',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_settings`
--

INSERT INTO `pharmacy_settings` (`id`, `pharmacy_id`, `setting_key`, `setting_value`, `data_type`, `created_at`, `updated_at`) VALUES
(1, 1, 'sidebar_logo', NULL, 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(2, 1, 'sidebar_logo_size', '40', 'integer', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(3, 1, 'company_phone', '+255712345678', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(4, 1, 'company_alt_phone', '+255712345679', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(5, 1, 'company_whatsapp', '+255712345678', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(6, 1, 'company_website', 'https://pharmacypro.co.tz', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(7, 1, 'receipt_header', 'PharmacyPro Demo', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(8, 1, 'receipt_footer', 'Asante kwa kununua!', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(9, 1, 'invoice_prefix', 'INV', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(10, 1, 'timezone', 'Africa/Dar_es_Salaam', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(11, 1, 'date_format', 'd/m/Y', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(12, 1, 'language', 'sw', 'string', '2026-11-11 14:41:10', '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `plan_modules`
--

CREATE TABLE `plan_modules` (
  `plan_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plan_modules`
--

INSERT INTO `plan_modules` (`plan_id`, `module_id`, `is_enabled`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1),
(1, 8, 1),
(1, 9, 1),
(1, 10, 1),
(1, 11, 1),
(1, 14, 1),
(1, 15, 1),
(1, 18, 1),
(2, 1, 1),
(2, 2, 1),
(2, 3, 1),
(2, 4, 1),
(2, 5, 1),
(2, 6, 1),
(2, 7, 1),
(2, 8, 1),
(2, 9, 1),
(2, 10, 1),
(2, 11, 1),
(2, 14, 1),
(2, 15, 1),
(2, 18, 1),
(3, 1, 1),
(3, 2, 1),
(3, 3, 1),
(3, 4, 1),
(3, 5, 1),
(3, 6, 1),
(3, 7, 1),
(3, 8, 1),
(3, 9, 1),
(3, 10, 1),
(3, 11, 1),
(3, 12, 1),
(3, 13, 1),
(3, 14, 1),
(3, 15, 1),
(3, 16, 1),
(3, 18, 1),
(4, 1, 1),
(4, 2, 1),
(4, 3, 1),
(4, 4, 1),
(4, 5, 1),
(4, 6, 1),
(4, 7, 1),
(4, 8, 1),
(4, 9, 1),
(4, 10, 1),
(4, 11, 1),
(4, 12, 1),
(4, 13, 1),
(4, 14, 1),
(4, 15, 1),
(4, 16, 1),
(4, 18, 1),
(5, 1, 1),
(5, 2, 1),
(5, 3, 1),
(5, 4, 1),
(5, 5, 1),
(5, 6, 1),
(5, 7, 1),
(5, 8, 1),
(5, 9, 1),
(5, 10, 1),
(5, 11, 1),
(5, 12, 1),
(5, 13, 1),
(5, 14, 1),
(5, 15, 1),
(5, 16, 1),
(5, 17, 1),
(5, 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_code` varchar(50) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` enum('system','pharmacy','employee') NOT NULL DEFAULT 'pharmacy',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_code`, `role_name`, `description`, `level`, `is_system`, `created_at`) VALUES
(1, 'super_admin', 'BismartTech Super Admin', 'Platform owner', 'system', 1, '2026-11-11 14:41:10'),
(2, 'pharmacy_owner', 'Pharmacy Owner', 'Pharmacy owner/manager', 'pharmacy', 1, '2026-11-11 14:41:10'),
(3, 'pharmacy_admin', 'Pharmacy Admin', 'Pharmacy administrator', 'pharmacy', 1, '2026-11-11 14:41:10'),
(4, 'cashier', 'Cashier', 'POS operator', 'employee', 1, '2026-11-11 14:41:10'),
(5, 'store_keeper', 'Store Keeper', 'Inventory manager', 'employee', 1, '2026-11-11 14:41:10'),
(6, 'accountant', 'Accountant', 'Financial officer', 'employee', 1, '2026-11-11 14:41:10'),
(7, 'branch_manager', 'Branch Manager', 'Manages a specific branch', 'employee', 1, '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 38),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 33),
(3, 34),
(3, 35),
(3, 36),
(3, 37),
(4, 1),
(4, 2),
(4, 5),
(4, 12),
(4, 15),
(4, 16),
(4, 19),
(5, 5),
(5, 6),
(5, 7),
(5, 9),
(5, 10),
(5, 11),
(6, 12),
(6, 13),
(6, 14),
(6, 18),
(6, 19),
(6, 20),
(6, 21),
(6, 26),
(6, 27),
(6, 28),
(7, 1),
(7, 2),
(7, 3),
(7, 5),
(7, 6),
(7, 7),
(7, 9),
(7, 10),
(7, 11),
(7, 12),
(7, 13),
(7, 14),
(7, 15),
(7, 16),
(7, 17),
(7, 18),
(7, 19),
(7, 20),
(7, 21),
(7, 22),
(7, 23),
(7, 24),
(7, 26),
(7, 27),
(7, 28),
(7, 35);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `billing_cycle` enum('monthly','annual') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` enum('mobile_money','bank_transfer','cash','manual') DEFAULT NULL,
  `payment_reference` varchar(150) DEFAULT NULL,
  `period_start` datetime NOT NULL,
  `period_end` datetime NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `verified_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `plan_code` varchar(50) NOT NULL,
  `billing_cycle` enum('monthly','annual') NOT NULL DEFAULT 'monthly',
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `trial_days` int(10) UNSIGNED NOT NULL DEFAULT 14,
  `grace_days` int(10) UNSIGNED NOT NULL DEFAULT 7,
  `max_employees` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `max_products` int(10) UNSIGNED NOT NULL DEFAULT 1000,
  `max_branches` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `plan_name`, `plan_code`, `billing_cycle`, `price`, `trial_days`, `grace_days`, `max_employees`, `max_products`, `max_branches`, `features`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Starter Monthly', 'STARTER-M', 'monthly', 20000.00, 14, 7, 5, 500, 1, '{\"desc\":\"Single branch, core modules\",\"support\":\"email\"}', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(2, 'Starter Annual', 'STARTER-A', 'annual', 180000.00, 14, 7, 5, 500, 1, '{\"desc\":\"Single branch, core modules\",\"support\":\"email\"}', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(3, 'Business Monthly', 'BUSINESS-M', 'monthly', 45000.00, 14, 7, 20, 5000, 3, '{\"desc\":\"Up to 3 branches, advanced reports\",\"support\":\"priority\"}', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(4, 'Business Annual', 'BUSINESS-A', 'annual', 450000.00, 14, 7, 20, 5000, 3, '{\"desc\":\"Up to 3 branches, advanced reports\",\"support\":\"priority\"}', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(5, 'Enterprise Monthly', 'ENTERPRISE-M', 'monthly', 120000.00, 14, 7, 100, 50000, 10, '{\"desc\":\"Unlimited branches, API access, dedicated support\",\"support\":\"dedicated\"}', 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `pharmacy_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `description` text DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'platform_name', 'PharmacyPro', 'general', NULL, NULL, '2026-11-11 14:41:10'),
(2, 'platform_owner', 'BismartTech', 'general', NULL, NULL, '2026-11-11 14:41:10'),
(3, 'platform_version', '1.0.0', 'general', NULL, NULL, '2026-11-11 14:41:10'),
(4, 'platform_email', 'support@bismarttech.co.tz', 'general', NULL, NULL, '2026-11-11 14:41:10'),
(5, 'platform_phone', '+255700000000', 'general', NULL, NULL, '2026-11-11 14:41:10'),
(6, 'currency', 'TSh', 'regional', NULL, NULL, '2026-11-11 14:41:10'),
(7, 'timezone', 'Africa/Dar_es_Salaam', 'regional', NULL, NULL, '2026-11-11 14:41:10'),
(8, 'date_format', 'd/m/Y', 'regional', NULL, NULL, '2026-11-11 14:41:10'),
(9, 'session_timeout_minutes', '120', 'security', NULL, NULL, '2026-11-11 14:41:10'),
(10, 'max_login_attempts', '5', 'security', NULL, NULL, '2026-11-11 14:41:10'),
(11, 'lockout_duration_minutes', '30', 'security', NULL, NULL, '2026-11-11 14:41:10'),
(12, 'trial_days_default', '14', 'subscription', NULL, NULL, '2026-11-11 14:41:10'),
(13, 'grace_days_default', '7', 'subscription', NULL, NULL, '2026-11-11 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `pharmacy_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = Super Admin',
  `branch_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = Owner (all branches)',
  `role_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `last_online` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `failed_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `pharmacy_id`, `branch_id`, `role_id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `profile_pic`, `status`, `must_change_password`, `is_online`, `last_online`, `last_login_at`, `last_login_ip`, `failed_attempts`, `locked_until`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 1, 'BismartTech Admin', 'BismartTech Admin', 'admin@bismarttech.co.tz', '+255700000000', '$2y$10$PLACEHOLDER_SUPER_ADMIN_HASH', NULL, 'active', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(2, 1, NULL, 2, 'Admin1 Owner', 'Admin1', 'admin1@pharmacypro.co.tz', '+255712345678', '$2y$10$PLACEHOLDER_ADMIN1_HASH', NULL, 'active', 1, 0, NULL, NULL, NULL, 0, NULL, 1, '2026-11-11 14:41:10', '2026-11-11 14:41:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_target` (`target`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_branch` (`branch_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pharmacy_branch_code` (`pharmacy_id`,`branch_code`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_hq` (`is_headquarters`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `module_code` (`module_code`),
  ADD KEY `idx_code` (`module_code`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `module_settings`
--
ALTER TABLE `module_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pharmacy_module_setting` (`pharmacy_id`,`module_id`,`setting_key`),
  ADD KEY `idx_pharmacy_module` (`pharmacy_id`,`module_id`),
  ADD KEY `fk_modset_module` (`module_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_code` (`permission_code`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pharmacy_code` (`pharmacy_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_plan` (`subscription_plan_id`),
  ADD KEY `idx_region` (`region`);

--
-- Indexes for table `pharmacy_modules`
--
ALTER TABLE `pharmacy_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pharmacy_module` (`pharmacy_id`,`module_id`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_enabled` (`is_enabled`),
  ADD KEY `fk_phmod_module` (`module_id`);

--
-- Indexes for table `pharmacy_settings`
--
ALTER TABLE `pharmacy_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pharmacy_setting` (`pharmacy_id`,`setting_key`);

--
-- Indexes for table `plan_modules`
--
ALTER TABLE `plan_modules`
  ADD PRIMARY KEY (`plan_id`,`module_id`),
  ADD KEY `fk_pm_module` (`module_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_code` (`role_code`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_permission` (`permission_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_period` (`period_start`,`period_end`),
  ADD KEY `fk_sub_plan` (`plan_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_code` (`plan_code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_group` (`setting_group`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_username_pharmacy` (`username`,`pharmacy_id`),
  ADD UNIQUE KEY `uk_email_pharmacy` (`email`,`pharmacy_id`),
  ADD KEY `idx_pharmacy` (`pharmacy_id`),
  ADD KEY `idx_branch` (`branch_id`),
  ADD KEY `idx_role` (`role_id`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `module_settings`
--
ALTER TABLE `module_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `pharmacies`
--
ALTER TABLE `pharmacies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pharmacy_modules`
--
ALTER TABLE `pharmacy_modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `pharmacy_settings`
--
ALTER TABLE `pharmacy_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `fk_branch_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `module_settings`
--
ALTER TABLE `module_settings`
  ADD CONSTRAINT `fk_modset_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_modset_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD CONSTRAINT `fk_pharmacy_plan` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_modules`
--
ALTER TABLE `pharmacy_modules`
  ADD CONSTRAINT `fk_phmod_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_phmod_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_settings`
--
ALTER TABLE `pharmacy_settings`
  ADD CONSTRAINT `fk_settings_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_modules`
--
ALTER TABLE `plan_modules`
  ADD CONSTRAINT `fk_pm_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pm_plan` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_sub_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sub_plan` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

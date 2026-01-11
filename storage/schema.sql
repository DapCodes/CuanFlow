/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `additional_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `additional_costs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `cost_type` enum('fixed','per_unit','percentage') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `additional_costs_recipe_id_foreign` (`recipe_id`),
  CONSTRAINT `additional_costs_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ai_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ai_chat_session_id` bigint(20) unsigned NOT NULL,
  `role` enum('user','assistant','system') NOT NULL,
  `content` text NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `tokens_used` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_chat_messages_ai_chat_session_id_foreign` (`ai_chat_session_id`),
  CONSTRAINT `ai_chat_messages_ai_chat_session_id_foreign` FOREIGN KEY (`ai_chat_session_id`) REFERENCES `ai_chat_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ai_chat_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_chat_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_chat_sessions_user_id_foreign` (`user_id`),
  KEY `ai_chat_sessions_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `ai_chat_sessions_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_chat_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ai_insights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_insights` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `type` enum('sales_trend','stock_prediction','product_recommendation','anomaly','general') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `severity` enum('info','warning','critical') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_dismissed` tinyint(1) NOT NULL DEFAULT 0,
  `insight_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_insights_outlet_id_created_at_index` (`outlet_id`,`created_at`),
  KEY `ai_insights_outlet_id_is_read_is_dismissed_index` (`outlet_id`,`is_read`,`is_dismissed`),
  KEY `ai_insights_insight_date_index` (`insight_date`),
  CONSTRAINT `ai_insights_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `backup_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backup_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'local',
  `path` varchar(255) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `type` enum('full','database','files') NOT NULL,
  `status` enum('completed','failed') NOT NULL,
  `error_message` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `backup_logs_created_by_foreign` (`created_by`),
  CONSTRAINT `backup_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_registers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `opening_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `closing_amount` decimal(15,2) DEFAULT NULL,
  `expected_amount` decimal(15,2) DEFAULT NULL,
  `difference` decimal(15,2) DEFAULT NULL,
  `total_transactions` int(11) NOT NULL DEFAULT 0,
  `total_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cash` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_qris` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_transfer` decimal(15,2) NOT NULL DEFAULT 0.00,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_registers_outlet_id_foreign` (`outlet_id`),
  KEY `cash_registers_user_id_foreign` (`user_id`),
  CONSTRAINT `cash_registers_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_registers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` enum('raw_material','product') NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_debts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_debts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `sale_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(15,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_debts_customer_id_foreign` (`customer_id`),
  KEY `customer_debts_sale_id_foreign` (`sale_id`),
  KEY `customer_debts_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `customer_debts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_debts_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_debts_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `type` enum('regular','reseller','vip') NOT NULL DEFAULT 'regular',
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_debt` decimal(15,2) NOT NULL DEFAULT 0.00,
  `points` int(11) NOT NULL DEFAULT 0,
  `birth_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_code_unique` (`code`),
  KEY `customers_phone_index` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_summaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `summary_date` date NOT NULL,
  `total_transactions` int(11) NOT NULL DEFAULT 0,
  `total_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cash_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qris_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `transfer_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `debt_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_hpp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `gross_profit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_expenses` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_profit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `products_sold` int(11) NOT NULL DEFAULT 0,
  `top_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`top_products`)),
  `hourly_sales` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hourly_sales`)),
  `total_customers` int(11) NOT NULL DEFAULT 0,
  `new_customers` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_summaries_outlet_id_summary_date_unique` (`outlet_id`,`summary_date`),
  CONSTRAINT `daily_summaries_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `debt_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `debt_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_debt_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','qris','transfer') NOT NULL,
  `outlet_payment_link_id` bigint(20) unsigned DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `received_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `debt_payments_customer_debt_id_foreign` (`customer_debt_id`),
  KEY `debt_payments_received_by_foreign` (`received_by`),
  KEY `debt_payments_outlet_payment_link_id_foreign` (`outlet_payment_link_id`),
  CONSTRAINT `debt_payments_customer_debt_id_foreign` FOREIGN KEY (`customer_debt_id`) REFERENCES `customer_debts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `debt_payments_outlet_payment_link_id_foreign` FOREIGN KEY (`outlet_payment_link_id`) REFERENCES `outlet_payment_links` (`id`) ON DELETE SET NULL,
  CONSTRAINT `debt_payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('percentage','fixed','buy_x_get_y') NOT NULL,
  `value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `min_purchase` decimal(15,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(15,2) DEFAULT NULL,
  `buy_quantity` int(11) DEFAULT NULL,
  `get_quantity` int(11) DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_voucher` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discounts_code_unique` (`code`),
  KEY `discounts_product_id_foreign` (`product_id`),
  KEY `discounts_category_id_foreign` (`category_id`),
  KEY `discounts_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `discounts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `discounts_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_number` varchar(50) NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `expense_category_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `receipt_image` varchar(255) DEFAULT NULL,
  `payment_method` enum('cash','transfer','card') NOT NULL DEFAULT 'cash',
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_outlet_id_foreign` (`outlet_id`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_approved_by_foreign` (`approved_by`),
  CONSTRAINT `expenses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `type` enum('general','pos','product','finance','report','account') NOT NULL DEFAULT 'general',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `not_helpful_count` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faqs_outlet_id_type_is_active_index` (`outlet_id`,`type`,`is_active`),
  CONSTRAINT `faqs_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hpp_calculations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hpp_calculations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `recipe_id` bigint(20) unsigned DEFAULT NULL,
  `raw_material_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `additional_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_hpp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `hpp_per_unit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `output_quantity` decimal(15,4) NOT NULL DEFAULT 1.0000,
  `calculation_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`calculation_details`)),
  `notes` text DEFAULT NULL,
  `calculated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hpp_calculations_product_id_foreign` (`product_id`),
  KEY `hpp_calculations_recipe_id_foreign` (`recipe_id`),
  KEY `hpp_calculations_calculated_by_foreign` (`calculated_by`),
  CONSTRAINT `hpp_calculations_calculated_by_foreign` FOREIGN KEY (`calculated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hpp_calculations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hpp_calculations_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `landing_page_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_page_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `landing_page_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `visited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landing_page_visits_landing_page_id_foreign` (`landing_page_id`),
  CONSTRAINT `landing_page_visits_landing_page_id_foreign` FOREIGN KEY (`landing_page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `landing_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL DEFAULT 1,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `hero_title` varchar(255) DEFAULT 'Selamat Datang',
  `hero_subtitle` varchar(255) DEFAULT 'Ke Toko Kami',
  `hero_image` varchar(255) DEFAULT NULL,
  `about_image` varchar(255) DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#4F46E5',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#1F2937',
  `font_heading` varchar(255) NOT NULL DEFAULT 'Inter',
  `font_body` varchar(255) NOT NULL DEFAULT 'Inter',
  `about_text` text DEFAULT NULL,
  `vision_text` text DEFAULT NULL,
  `mission_text` text DEFAULT NULL,
  `tagline_text` text DEFAULT NULL,
  `services_section` text DEFAULT NULL,
  `testimonials_section` text DEFAULT NULL,
  `selected_testimonial_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_testimonial_ids`)),
  `gallery_images` text DEFAULT NULL,
  `cta_text` text DEFAULT NULL,
  `cta_button_text` text DEFAULT NULL,
  `selected_product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_product_ids`)),
  `social_media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_media`)),
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `footer_text` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landing_pages_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `landing_pages_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offline_sync_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offline_sync_queues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `local_id` varchar(255) DEFAULT NULL,
  `synced_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offline_sync_queues_outlet_id_foreign` (`outlet_id`),
  KEY `offline_sync_queues_user_id_foreign` (`user_id`),
  CONSTRAINT `offline_sync_queues_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offline_sync_queues_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `outlet_payment_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlet_payment_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `payment_method_id` bigint(20) unsigned NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `qr_image` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outlet_payment_links_outlet_id_payment_method_id_unique` (`outlet_id`,`payment_method_id`),
  KEY `outlet_payment_links_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `outlet_payment_links_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `outlet_payment_links_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `outlet_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlet_policies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outlet_policies_outlet_id_foreign` (`outlet_id`),
  KEY `outlet_policies_created_by_foreign` (`created_by`),
  CONSTRAINT `outlet_policies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `outlet_policies_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `outlets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `has_table_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indicates if outlet uses table management system (e.g., coffee shop)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outlets_code_unique` (`code`),
  KEY `outlets_owner_id_foreign` (`owner_id`),
  CONSTRAINT `outlets_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_methods_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permission_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `permission_category_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_permission_category_id_foreign` (`permission_category_id`),
  CONSTRAINT `permissions_permission_category_id_foreign` FOREIGN KEY (`permission_category_id`) REFERENCES `permission_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_sales_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_sales_targets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `monthly_target_revenue` decimal(15,2) NOT NULL,
  `hpp_per_unit` decimal(15,2) NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `daily_sales_target` int(11) NOT NULL,
  `monthly_sales_target` int(11) NOT NULL,
  `daily_revenue_target` decimal(15,2) NOT NULL,
  `profit_per_unit` decimal(15,2) NOT NULL,
  `monthly_profit_target` decimal(15,2) NOT NULL,
  `sales_pattern` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sales_pattern`)),
  `target_start_date` date NOT NULL,
  `target_end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_sales_targets_product_id_foreign` (`product_id`),
  KEY `product_sales_targets_outlet_id_foreign` (`outlet_id`),
  KEY `product_sales_targets_created_by_foreign` (`created_by`),
  CONSTRAINT `product_sales_targets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `product_sales_targets_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_sales_targets_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_stocks_product_id_outlet_id_unique` (`product_id`,`outlet_id`),
  KEY `product_stocks_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `product_stocks_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `production_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `production_id` bigint(20) unsigned NOT NULL,
  `raw_material_id` bigint(20) unsigned NOT NULL,
  `planned_quantity` decimal(15,6) NOT NULL,
  `actual_quantity` decimal(15,6) DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_items_production_id_foreign` (`production_id`),
  KEY `production_items_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `production_items_production_id_foreign` FOREIGN KEY (`production_id`) REFERENCES `productions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=657 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_number` varchar(255) NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `recipe_id` bigint(20) unsigned DEFAULT NULL,
  `planned_quantity` decimal(15,4) NOT NULL,
  `actual_quantity` decimal(15,4) DEFAULT NULL,
  `waste_quantity` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `status` enum('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned',
  `is_disposed` tinyint(1) NOT NULL DEFAULT 0,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `expired_at` date DEFAULT NULL,
  `total_material_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_additional_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productions_batch_number_unique` (`batch_number`),
  KEY `productions_outlet_id_foreign` (`outlet_id`),
  KEY `productions_product_id_foreign` (`product_id`),
  KEY `productions_recipe_id_foreign` (`recipe_id`),
  KEY `productions_created_by_foreign` (`created_by`),
  KEY `productions_completed_by_foreign` (`completed_by`),
  CONSTRAINT `productions_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productions_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `productions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `productions_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `hpp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reseller_price` decimal(15,2) DEFAULT NULL,
  `promo_price` decimal(15,2) DEFAULT NULL,
  `margin_percent` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `min_stock` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `shelf_life_days` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_sellable` tinyint(1) NOT NULL DEFAULT 1,
  `track_stock` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_unit_id_foreign` (`unit_id`),
  KEY `products_outlet_id_foreign` (`outlet_id`),
  KEY `products_barcode_index` (`barcode`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`),
  CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `raw_material_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `received_quantity` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `remaining_quantity` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `is_disposed` tinyint(1) NOT NULL DEFAULT 0,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `expired_at` date DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_number` varchar(50) NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `status` enum('draft','ordered','received','partial_received','cancelled') NOT NULL DEFAULT 'draft',
  `purchase_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `purchases_outlet_id_foreign` (`outlet_id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_created_by_foreign` (`created_by`),
  CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `raw_material_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `raw_material_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `raw_material_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `avg_purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `raw_material_stocks_raw_material_id_outlet_id_unique` (`raw_material_id`,`outlet_id`),
  KEY `raw_material_stocks_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `raw_material_stocks_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `raw_material_stocks_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `raw_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `raw_materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `min_stock` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `shelf_life_days` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `raw_materials_code_unique` (`code`),
  KEY `raw_materials_category_id_foreign` (`category_id`),
  KEY `raw_materials_outlet_id_foreign` (`outlet_id`),
  KEY `raw_materials_unit_id_foreign` (`unit_id`),
  KEY `raw_materials_supplier_id_foreign` (`supplier_id`),
  KEY `raw_materials_barcode_index` (`barcode`),
  CONSTRAINT `raw_materials_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `raw_materials_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `raw_materials_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `raw_materials_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recipe_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `raw_material_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,6) NOT NULL,
  `notes` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_items_recipe_id_foreign` (`recipe_id`),
  KEY `recipe_items_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `recipe_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_items_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `output_quantity` decimal(15,4) NOT NULL DEFAULT 1.0000,
  `instructions` text DEFAULT NULL,
  `estimated_time_minutes` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipes_product_id_foreign` (`product_id`),
  CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `report_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `report_type` enum('daily_sales','weekly_sales','monthly_sales','stock_report','profit_report','custom') NOT NULL,
  `frequency` enum('daily','weekly','monthly') NOT NULL,
  `day_of_week` varchar(255) DEFAULT NULL,
  `day_of_month` int(11) DEFAULT NULL,
  `send_time` time NOT NULL DEFAULT '08:00:00',
  `recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`recipients`)),
  `format` enum('pdf','excel') NOT NULL DEFAULT 'pdf',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_schedules_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `report_schedules_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reseller_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reseller_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `description` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_applications_user_id_foreign` (`user_id`),
  KEY `supplier_applications_outlet_id_foreign` (`outlet_id`),
  KEY `supplier_applications_processed_by_foreign` (`processed_by`),
  CONSTRAINT `supplier_applications_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_applications_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `hpp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `profit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sale_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('cash','qris','transfer','card') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `received_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_payments_sale_id_foreign` (`sale_id`),
  KEY `sale_payments_received_by_foreign` (`received_by`),
  CONSTRAINT `sale_payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `cashier_id` bigint(20) unsigned NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','qris','transfer','card','debt','split') NOT NULL DEFAULT 'cash',
  `service_type` enum('dine_in','take_away') NOT NULL DEFAULT 'take_away',
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `outlet_payment_link_id` bigint(20) unsigned DEFAULT NULL,
  `payment_status` enum('pending','paid','partial','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `midtrans_payment_type` varchar(255) DEFAULT NULL,
  `midtrans_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`midtrans_response`)),
  `notes` text DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `status` enum('draft','completed','cancelled','refunded') NOT NULL DEFAULT 'draft',
  `is_synced` tinyint(1) NOT NULL DEFAULT 1,
  `completed_at` datetime DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `is_reported` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoice_number_unique` (`invoice_number`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_cashier_id_foreign` (`cashier_id`),
  KEY `sales_outlet_id_created_at_index` (`outlet_id`,`created_at`),
  KEY `sales_payment_status_status_index` (`payment_status`,`status`),
  KEY `sales_midtrans_order_id_index` (`midtrans_order_id`),
  KEY `sales_outlet_payment_link_id_foreign` (`outlet_payment_link_id`),
  KEY `sales_table_id_foreign` (`table_id`),
  CONSTRAINT `sales_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_outlet_payment_link_id_foreign` FOREIGN KEY (`outlet_payment_link_id`) REFERENCES `outlet_payment_links` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `group` varchar(50) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_outlet_id_group_key_unique` (`outlet_id`,`group`,`key`),
  CONSTRAINT `settings_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `stockable_type` varchar(255) NOT NULL,
  `stockable_id` bigint(20) unsigned NOT NULL,
  `type` enum('in','out','adjustment','transfer','production','sale','waste','return') NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `quantity_before` decimal(15,4) NOT NULL,
  `quantity_after` decimal(15,4) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `expired_at` date DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_outlet_id_foreign` (`outlet_id`),
  KEY `stock_movements_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  KEY `stock_movements_created_by_foreign` (`created_by`),
  KEY `stock_movements_stockable_type_stockable_id_outlet_id_index` (`stockable_type`,`stockable_id`,`outlet_id`),
  KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=947 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `stockable_type` varchar(255) NOT NULL,
  `stockable_id` bigint(20) unsigned NOT NULL,
  `type` enum('low_stock','out_of_stock','expiring_soon','expired') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `current_stock` decimal(15,4) DEFAULT NULL,
  `min_stock` decimal(15,4) DEFAULT NULL,
  `days_until_expiry` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_sent_email` tinyint(1) NOT NULL DEFAULT 0,
  `is_sent_wa` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_notifications_outlet_id_foreign` (`outlet_id`),
  KEY `stock_notifications_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  CONSTRAINT `stock_notifications_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_opname_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_opname_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_opname_id` bigint(20) unsigned NOT NULL,
  `stockable_type` varchar(255) NOT NULL,
  `stockable_id` bigint(20) unsigned NOT NULL,
  `system_quantity` decimal(15,4) NOT NULL,
  `physical_quantity` decimal(15,4) DEFAULT NULL,
  `difference` decimal(15,4) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_opname_items_stock_opname_id_foreign` (`stock_opname_id`),
  KEY `stock_opname_items_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  CONSTRAINT `stock_opname_items_stock_opname_id_foreign` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_opnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_opnames` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `opname_number` varchar(50) NOT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `type` enum('raw_material','product','all') NOT NULL,
  `status` enum('draft','in_progress','completed','cancelled') NOT NULL DEFAULT 'draft',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_opnames_opname_number_unique` (`opname_number`),
  KEY `stock_opnames_outlet_id_foreign` (`outlet_id`),
  KEY `stock_opnames_created_by_foreign` (`created_by`),
  KEY `stock_opnames_approved_by_foreign` (`approved_by`),
  CONSTRAINT `stock_opnames_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_opnames_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_opnames_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfer_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` bigint(20) unsigned NOT NULL,
  `stockable_type` varchar(255) NOT NULL,
  `stockable_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `received_quantity` decimal(15,4) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `stock_transfer_items_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  CONSTRAINT `stock_transfer_items_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(50) NOT NULL,
  `from_outlet_id` bigint(20) unsigned NOT NULL,
  `to_outlet_id` bigint(20) unsigned NOT NULL,
  `status` enum('draft','pending','in_transit','received','cancelled') NOT NULL DEFAULT 'draft',
  `sent_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `received_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_transfer_number_unique` (`transfer_number`),
  KEY `stock_transfers_from_outlet_id_foreign` (`from_outlet_id`),
  KEY `stock_transfers_to_outlet_id_foreign` (`to_outlet_id`),
  KEY `stock_transfers_created_by_foreign` (`created_by`),
  KEY `stock_transfers_received_by_foreign` (`received_by`),
  CONSTRAINT `stock_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_from_outlet_id_foreign` FOREIGN KEY (`from_outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_to_outlet_id_foreign` FOREIGN KEY (`to_outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `table_number` varchar(20) NOT NULL COMMENT 'Nomor meja, e.g., 1, 2, A1, VIP-01',
  `code` varchar(50) DEFAULT NULL COMMENT 'Kode unik untuk QR atau referensi',
  `name` varchar(255) DEFAULT NULL COMMENT 'Nama meja (opsional), e.g., Meja Teras, Meja VIP',
  `capacity` int(11) NOT NULL DEFAULT 4 COMMENT 'Kapasitas orang per meja',
  `location` varchar(255) DEFAULT NULL COMMENT 'Lokasi meja, e.g., Indoor, Outdoor, Lantai 2',
  `status` enum('available','occupied','reserved','maintenance') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tables_outlet_id_table_number_unique` (`outlet_id`,`table_number`),
  UNIQUE KEY `tables_outlet_id_code_unique` (`outlet_id`,`code`),
  CONSTRAINT `tables_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `field` varchar(255) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_activities_task_id_index` (`task_id`),
  KEY `task_activities_user_id_index` (`user_id`),
  KEY `task_activities_action_index` (`action`),
  KEY `task_activities_created_at_index` (`created_at`),
  CONSTRAINT `task_activities_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_assignments_task_id_user_id_unique` (`task_id`,`user_id`),
  KEY `task_assignments_task_id_index` (`task_id`),
  KEY `task_assignments_user_id_index` (`user_id`),
  CONSTRAINT `task_assignments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_label_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_label_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `label_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_label_assignments_task_id_label_id_unique` (`task_id`,`label_id`),
  KEY `task_label_assignments_task_id_index` (`task_id`),
  KEY `task_label_assignments_label_id_index` (`label_id`),
  CONSTRAINT `task_label_assignments_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `task_labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_label_assignments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_labels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#6B7280',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#6B7280',
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_statuses_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `deadline` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_status_id_index` (`status_id`),
  KEY `tasks_priority_index` (`priority`),
  KEY `tasks_deadline_index` (`deadline`),
  KEY `tasks_created_by_index` (`created_by`),
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `task_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `testimonials_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `testimonials_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(10) NOT NULL,
  `base_unit_id` bigint(20) unsigned DEFAULT NULL,
  `conversion_factor` decimal(15,6) NOT NULL DEFAULT 1.000000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_base_unit_id_foreign` (`base_unit_id`),
  CONSTRAINT `units_base_unit_id_foreign` FOREIGN KEY (`base_unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_withdraw_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_withdraw_locks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_withdraw_locks_user_id_unique` (`user_id`),
  CONSTRAINT `user_withdraw_locks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `daily_chat_quota` int(11) NOT NULL DEFAULT 3,
  `last_chat_reset_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `users_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `payment_method_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(15,2) NOT NULL COMMENT 'Amount after tax deduction',
  `payment_method` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','paid','cancelled') NOT NULL DEFAULT 'pending',
  `proof_image` varchar(255) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawals_processed_by_foreign` (`processed_by`),
  KEY `withdrawals_user_id_status_index` (`user_id`,`status`),
  KEY `withdrawals_outlet_id_created_at_index` (`outlet_id`,`created_at`),
  KEY `withdrawals_status_index` (`status`),
  KEY `withdrawals_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `withdrawals_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `withdrawals_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  CONSTRAINT `withdrawals_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

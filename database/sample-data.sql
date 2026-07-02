-- Beyond Plus CMS — sanitized sample data
-- ---------------------------------------------------------------------------
-- Full schema for every table + seed data for generic CMS infrastructure
-- (modules, permissions, languages, options) plus a few neutral demo posts.
-- Contains NO production PII: users/customers/activity_log carry no real data.
-- A demo admin is provided at the bottom: admin@example.com / password
-- ---------------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS=0;
/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `batch_uuid` char(36) CHARACTER SET ascii DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=578 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_access` (
  `access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `usertype` int(11) DEFAULT 4,
  `canshow` tinyint(1) NOT NULL,
  `cancreate` tinyint(1) NOT NULL,
  `canedit` tinyint(1) NOT NULL,
  `candelete` tinyint(1) NOT NULL,
  PRIMARY KEY (`access_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `block_url` text NOT NULL,
  `block_type` varchar(10) DEFAULT NULL,
  `block_active` varchar(3) NOT NULL DEFAULT 'yes',
  `translate_id` int(11) NOT NULL DEFAULT 0,
  `lang` int(11) NOT NULL DEFAULT 1,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `active` varchar(3) NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_customs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_customs` (
  `custom_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `custom_name` varchar(255) NOT NULL,
  `custom_link` varchar(255) NOT NULL,
  `custom_blade` varchar(255) NOT NULL,
  `custom_weight` int(11) NOT NULL DEFAULT 1,
  `custom_icon` varchar(255) NOT NULL DEFAULT 'fa fa-edit',
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`custom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_iso` varchar(255) NOT NULL,
  `language_value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_media` (
  `media_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `media_name` varchar(255) NOT NULL,
  `media_link` varchar(255) NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `department_type` int(11) NOT NULL DEFAULT 0,
  `media_weight` int(11) NOT NULL DEFAULT 0,
  `media_description` text DEFAULT NULL,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_menus` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) NOT NULL,
  `menu_link` text NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `menu_weight` int(11) NOT NULL DEFAULT 0,
  `menu_icon` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `menu_type` varchar(255) NOT NULL DEFAULT 'default',
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `lang` int(11) NOT NULL DEFAULT 1,
  `translate_id` varchar(255) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `message_value` text NOT NULL,
  `message_active` varchar(3) NOT NULL,
  `message_type` varchar(7) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_modules` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) NOT NULL,
  `module_name_mm` varchar(255) NOT NULL,
  `module_link` varchar(255) NOT NULL,
  `module_weight` int(11) NOT NULL DEFAULT 1,
  `module_icon` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `section` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_options` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(35) NOT NULL,
  `option_value` text NOT NULL,
  `autoload` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `featured` int(11) NOT NULL DEFAULT 0,
  `featured_img` varchar(100) NOT NULL DEFAULT 'default.jpg',
  `post_link` text NOT NULL,
  `post_type` varchar(255) NOT NULL,
  `post_template` varchar(255) NOT NULL DEFAULT 'default',
  `post_weight` int(11) NOT NULL DEFAULT 0,
  `post_view` int(11) NOT NULL DEFAULT 0,
  `post_active` varchar(3) NOT NULL DEFAULT 'yes',
  `translate_id` int(11) NOT NULL DEFAULT 0,
  `lang` int(11) NOT NULL DEFAULT 1,
  `event_at` date DEFAULT NULL,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `event_color` varchar(100) NOT NULL DEFAULT '#3A87AD',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_relationships` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_id` text NOT NULL,
  `post_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_sliders` (
  `slider_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slider_name` varchar(255) NOT NULL,
  `slider_link` varchar(255) NOT NULL,
  `slider_type` varchar(255) NOT NULL,
  `slider_weight` int(11) NOT NULL,
  `slider_description` text NOT NULL,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`slider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_taxes` (
  `tax_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `tax_name` varchar(255) NOT NULL,
  `tax_link` varchar(255) NOT NULL,
  `tax_icon` varchar(255) NOT NULL DEFAULT 'fa fa-list',
  `tax_lan` int(11) NOT NULL DEFAULT 1,
  `tax_type` varchar(255) NOT NULL,
  `tax_active` varchar(3) NOT NULL DEFAULT 'yes',
  `translate_id` int(11) NOT NULL DEFAULT 0,
  `lang` int(11) NOT NULL DEFAULT 1,
  `staff_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bp_usertype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bp_usertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(100) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `discount_amount` varchar(10) DEFAULT NULL,
  `total_spend_amount` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_types_id` int(10) unsigned NOT NULL,
  `first_name` varchar(200) DEFAULT NULL,
  `last_name` varchar(200) DEFAULT NULL,
  `gender` varchar(30) DEFAULT NULL,
  `date_of_birth` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `subscribed_to_news_letter` tinyint(4) DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT NULL,
  `profile_photo` varchar(200) DEFAULT NULL,
  `total_reward_points` int(11) DEFAULT NULL,
  `total_subtotal_amount` int(11) NOT NULL DEFAULT 0,
  `wallets` int(11) DEFAULT 0,
  `reward_expiry_date` date DEFAULT NULL,
  `activation_code` varchar(300) DEFAULT NULL,
  `otpcode` int(11) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `api_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_customer_types_id_foreign` (`customer_types_id`),
  KEY `customers_api_token_index` (`api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1,
  `department_type` int(11) NOT NULL DEFAULT 0,
  `api_token` varchar(60) NOT NULL,
  `phone_no` varchar(30) DEFAULT NULL,
  `activation_key` varchar(6) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT 0,
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verify_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_users` (
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;


-- ---- Seed data: generic CMS infrastructure ----
/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_resets_table',1),
(3,'2016_05_31_164544_create_bp_media_table',1),
(4,'2016_05_31_164931_create_bp_menu_table',1),
(5,'2016_05_31_165146_create_bp_options_table',1),
(6,'2016_05_31_165349_create_bp_post_table',1),
(7,'2016_05_31_165807_create_bp_relationship_table',1),
(8,'2016_05_31_170006_create_bp_tax_table',1),
(9,'2016_05_31_170804_create_bp_comments_table',1),
(10,'2016_06_11_150749_create_bp_sliders_table',1),
(11,'2016_07_31_172759_create_bp_custom_table',1),
(12,'2018_03_06_103529_create_bp_module_table',1),
(13,'2018_03_06_103651_create_bp_langauge_table',1),
(14,'2018_03_06_103759_create_bp_messages_table',1),
(15,'2018_03_15_134030_create_bp_access_table',1),
(16,'2018_06_26_111720_create_verify_users_table',1),
(17,'2018_07_25_111122_create_bp_block_table',1),
(18,'2019_08_19_000000_create_failed_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_usertype` WRITE;
/*!40000 ALTER TABLE `bp_usertype` DISABLE KEYS */;
INSERT INTO `bp_usertype` (`id`, `role`, `created_at`, `updated_at`) VALUES (1,'user',NULL,NULL),
(2,'staff',NULL,NULL),
(3,'admin',NULL,NULL),
(4,'superadmin',NULL,NULL);
/*!40000 ALTER TABLE `bp_usertype` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `customer_types` WRITE;
/*!40000 ALTER TABLE `customer_types` DISABLE KEYS */;
INSERT INTO `customer_types` (`id`, `name`, `discount_amount`, `total_spend_amount`, `status`, `created_at`, `updated_at`) VALUES (1,'Basic',NULL,NULL,'active','2020-08-19 17:58:54','2020-08-19 17:58:54'),
(2,'Gold Member','3','4500','active','2020-08-19 18:03:22','2020-08-19 18:03:22'),
(3,'Diamond Member','5','10000','active','2020-08-19 18:04:05','2020-08-19 18:04:05');
/*!40000 ALTER TABLE `customer_types` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_languages` WRITE;
/*!40000 ALTER TABLE `bp_languages` DISABLE KEYS */;
INSERT INTO `bp_languages` (`id`, `language_iso`, `language_value`, `created_at`, `updated_at`) VALUES (1,'mm','Myanmar','2020-11-08 22:53:16',NULL),
(2,'en','English','2020-11-08 22:53:16',NULL);
/*!40000 ALTER TABLE `bp_languages` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_options` WRITE;
/*!40000 ALTER TABLE `bp_options` DISABLE KEYS */;
INSERT INTO `bp_options` (`option_id`, `option_name`, `option_value`, `autoload`, `created_at`, `updated_at`) VALUES (1,'siteurl','http://localhost','yes','2016-06-02 18:06:29','2021-12-25 04:31:31'),
(2,'home','http://localhost','yes','2016-06-02 18:06:29','2021-12-25 04:31:31'),
(3,'blogname','Beyond Plus CMS','yes','2016-06-02 18:06:29','2021-12-25 04:31:31'),
(4,'blogdescription','A Beyond Plus CMS sample site','yes','2016-06-02 18:06:29','2021-12-25 04:31:31'),
(5,'theme','default','yes','2016-06-02 18:06:29','2021-12-25 04:31:31'),
(7,'admin_email','admin@example.com','yes','2016-06-02 18:06:29','2021-12-25 04:31:31'),
(8,'crawler_text','','yes',NULL,NULL);
/*!40000 ALTER TABLE `bp_options` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_modules` WRITE;
/*!40000 ALTER TABLE `bp_modules` DISABLE KEYS */;
INSERT INTO `bp_modules` (`module_id`, `module_name`, `module_name_mm`, `module_link`, `module_weight`, `module_icon`, `parent_id`, `staff_id`, `section`, `created_at`, `updated_at`) VALUES (1,'Dashboard','ပင်မစာမျက်နှာ','/',0,'fa fa-dashboard',0,1,1,'2016-06-02 18:06:29',NULL),
(2,'Post','ပိုစ့်','post',1,'fa fa-edit',0,1,1,'2016-06-02 18:06:29','2021-03-15 03:56:51'),
(3,'Page','စာမျက်နှာ','page',2,'fa fa-edit',0,1,1,'2016-06-02 18:06:29','2021-03-15 03:56:52'),
(4,'Menu','အညွန်းများ','menu',3,'fa fa-table',0,1,1,'2016-06-02 18:06:29','2021-03-15 03:56:53'),
(5,'Media','မီဒီယာ','media',4,'fa fa-desktop',0,1,1,'2016-06-02 18:06:29','2021-03-15 03:56:54'),
(6,'Slider','ကြော်ငြာ','slider',5,'fa fa-image',0,1,1,'2016-06-02 18:06:29','2021-03-15 03:56:55'),
(7,'User Management','အဖွဲ့ဝင်များ','user',8,'fa fa-windows',0,1,1,'2016-06-02 18:06:29',NULL),
(8,'Settings','ထိန်းချုပ်ရေး','settings',9,'fa fa-bug',0,1,1,'2016-06-02 18:06:29',NULL),
(9,'Custom','ပြင်ဆင်ခြင်း','custom',0,'fa fa-windows',0,1,0,'2016-06-02 18:06:29',NULL),
(10,'Add Custom','ထပ်ထည့်ခြင်း','addcustom',0,'fa fa-sitemap',0,1,0,'2016-06-02 18:06:29',NULL),
(11,'Add Post','ပိုစ့်ထည့်ခြင်း','post/create',2,'fa fa-home',2,1,1,'2016-06-02 18:06:29','2021-03-11 04:46:49'),
(12,'Category','ကဏ္ဍတ','category',2,'fa fa-edit',2,1,0,'2016-06-02 18:06:29',NULL),
(13,'Block','ဘောက်လောက်တုန်း','block',4,'fa fa-table',2,1,1,'2016-06-02 18:06:29','2021-03-15 03:57:15'),
(14,'Account','အကောင့်','account',0,'fa fa-desktop',8,1,1,'2016-06-02 18:06:29','2021-03-17 08:32:45'),
(15,'Permission','ခွင့်ပြုချက်','permission',0,'fa fa-windows',8,1,1,'2016-06-02 18:06:29','2021-03-15 03:57:37'),
(16,'Generals','အခြေခံ','general',0,'fa fa-bug',8,1,1,'2016-06-02 18:06:29',NULL),
(17,'Report','Report','reports',10,'fa fa-edit',0,1,1,NULL,NULL),
(18,'Customer Report','Customer Report','reports/customer-report',10,'fa fa-edit',17,1,1,NULL,NULL),
(19,'Customer Report Export','Customer Report Export','reports/customer-report-export',10,'fa fa-edit',17,1,0,NULL,NULL),
(20,'User Guide','User Guide','user-guide',1,'fa fa-edit',0,1,0,NULL,NULL),
(21,'News and Events','သတင်းနှင့်အခမ်းအနားများ','news',1,'fa fa-edit',2,1,1,NULL,NULL),
(22,'Department Pages','Department Pages','department',1,'fa fa-edit',0,1,1,NULL,NULL);
/*!40000 ALTER TABLE `bp_modules` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_access` WRITE;
/*!40000 ALTER TABLE `bp_access` DISABLE KEYS */;
INSERT INTO `bp_access` (`access_id`, `module_id`, `usertype`, `canshow`, `cancreate`, `canedit`, `candelete`) VALUES (1,1,1,0,0,0,0),
(2,2,1,0,0,0,0),
(3,3,1,0,0,0,0),
(4,4,1,0,0,0,0),
(5,5,1,0,0,0,0),
(6,6,1,0,0,0,0),
(7,7,1,0,0,0,0),
(8,8,1,0,0,0,0),
(9,11,1,0,0,0,0),
(10,13,1,0,0,0,0),
(11,14,1,0,0,0,0),
(12,15,1,0,0,0,0),
(13,16,1,0,0,0,0),
(14,17,1,0,0,0,0),
(15,18,1,0,0,0,0),
(16,1,2,1,1,1,1),
(17,2,2,1,1,1,1),
(18,3,2,0,1,1,1),
(19,4,2,0,1,1,1),
(20,5,2,1,1,1,1),
(21,6,2,0,1,1,1),
(22,7,2,0,1,1,1),
(23,8,2,0,1,1,1),
(24,11,2,0,1,1,1),
(25,13,2,1,1,1,1),
(26,14,2,0,1,1,1),
(27,15,2,0,1,1,1),
(28,16,2,0,1,1,1),
(29,17,2,0,1,1,1),
(30,18,2,0,1,1,1),
(31,1,3,1,1,1,1),
(32,2,3,1,1,1,1),
(33,3,3,1,1,1,1),
(34,4,3,0,1,1,1),
(35,5,3,1,1,1,1),
(36,6,3,1,1,1,1),
(37,7,3,0,1,1,1),
(38,8,3,1,1,1,1),
(39,11,3,0,1,1,1),
(40,13,3,1,1,1,1),
(41,14,3,1,1,1,1),
(42,15,3,0,1,1,1),
(43,16,3,1,1,1,1),
(44,17,3,1,1,1,1),
(45,18,3,1,1,1,1),
(46,1,4,1,1,1,1),
(47,2,4,1,1,1,1),
(48,3,4,1,1,1,1),
(49,4,4,1,1,1,1),
(50,5,4,1,1,1,1),
(51,6,4,1,1,1,1),
(52,7,4,0,1,1,1),
(53,8,4,1,1,1,1),
(54,11,4,0,1,1,1),
(55,13,4,1,1,1,1),
(56,14,4,1,1,1,1),
(57,15,4,1,1,1,1),
(58,16,4,1,1,1,1),
(59,17,4,1,1,1,1),
(60,18,4,1,1,1,1),
(61,21,1,0,0,0,0),
(62,21,2,1,0,0,0),
(63,21,3,1,0,0,0),
(64,21,4,1,0,0,0),
(65,22,1,0,0,0,0),
(66,22,2,1,0,0,0),
(67,22,3,1,0,0,0),
(68,22,4,1,0,0,0),
(69,20,1,1,0,0,0),
(70,20,2,1,0,0,0),
(71,20,3,1,0,0,0),
(72,20,4,1,0,0,0),
(73,20,5,1,0,0,0),
(74,20,6,1,0,0,0);
/*!40000 ALTER TABLE `bp_access` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_customs` WRITE;
/*!40000 ALTER TABLE `bp_customs` DISABLE KEYS */;
INSERT INTO `bp_customs` (`custom_id`, `custom_name`, `custom_link`, `custom_blade`, `custom_weight`, `custom_icon`, `parent_id`, `staff_id`, `created_at`, `updated_at`) VALUES (1,'Test','test','test',0,'fa fa-edit',0,1,'2016-06-02 18:06:29','2020-11-08 22:53:16');
/*!40000 ALTER TABLE `bp_customs` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `bp_taxes` WRITE;
/*!40000 ALTER TABLE `bp_taxes` DISABLE KEYS */;
INSERT INTO `bp_taxes` (`tax_id`, `parent_id`, `tax_name`, `tax_link`, `tax_icon`, `tax_lan`, `tax_type`, `tax_active`, `translate_id`, `lang`, `staff_id`, `created_at`, `updated_at`) VALUES (1,0,'Uncategorized','uncategorized','fa fa-list',1,'cat','yes',0,1,1,'2016-06-02 18:06:29','2020-11-08 22:53:15');
/*!40000 ALTER TABLE `bp_taxes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;


-- Demo administrator (password: "password") ---------------------------------
DELETE FROM `users` WHERE `email` = 'admin@example.com';
INSERT INTO `users` (`id`,`name`,`email`,`password`,`api_token`,`role`,`status`,`verified`,`created_at`,`updated_at`)
VALUES (1,'Admin','admin@example.com','$2y$10$3nwR6lIQ0Qu7vQbefvyYueRTV0SpvfaCkbBXIpuL0lNBsfdteI632','demo-token',4,1,1,NOW(),NOW());

-- Demo customer (front-end login: phone 09000000000 / password "password") ---
DELETE FROM `customers` WHERE `phone` = '09000000000';
INSERT INTO `customers` (`customer_types_id`,`first_name`,`last_name`,`email`,`phone`,`password`,`status`,`is_verified`,`total_reward_points`,`created_at`,`updated_at`)
VALUES (1,'Demo','Customer','customer@example.com','09000000000','$2y$10$3nwR6lIQ0Qu7vQbefvyYueRTV0SpvfaCkbBXIpuL0lNBsfdteI632',1,1,150,DATE_SUB(NOW(), INTERVAL 2 DAY),DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Demo posts (neutral sample content, so the front-end renders out of the box)
INSERT INTO `bp_posts` (`id`,`title`,`body`,`featured_img`,`post_link`,`post_type`,`post_active`,`translate_id`,`lang`,`staff_id`,`created_at`,`updated_at`) VALUES
(3,'Welcome to Beyond Plus CMS','<p>Beyond Plus CMS is a lightweight, multi-language content management system built on Laravel. This sample post is here so you can see how the front-end theme renders content out of the box.</p><p>Log in to the admin panel to create, edit, and organise your own pages and posts.</p>','default.jpg','welcome-to-beyond-plus-cms','post','yes',0,1,1,DATE_SUB(NOW(), INTERVAL 1 DAY),DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2,'Getting Started with the Admin Panel','<p>The admin panel lives at <code>/bp-admin</code>. From there you can manage posts, pages, menus, media, sliders and site settings.</p><p>Use the demo administrator account to explore the dashboard and try creating your first post.</p>','default.jpg','getting-started-admin-panel','post','yes',0,1,1,DATE_SUB(NOW(), INTERVAL 3 DAY),DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1,'Building Multilingual Content','<p>Every post and menu item can have a translation. Switch the site locale and the matching translated content is served automatically.</p><p>This makes Beyond Plus CMS a good fit for sites that need to reach audiences in more than one language.</p>','default.jpg','building-multilingual-content','post','yes',0,1,1,DATE_SUB(NOW(), INTERVAL 5 DAY),DATE_SUB(NOW(), INTERVAL 5 DAY));

INSERT INTO `bp_relationships` (`tax_id`,`post_id`,`type`) VALUES (1,3,'cat'),(1,2,'cat'),(1,1,'cat');

-- Demo pages (post_type='page') demonstrating page-template usage
INSERT INTO `bp_posts` (`id`,`title`,`body`,`featured_img`,`post_link`,`post_type`,`post_template`,`post_active`,`translate_id`,`lang`,`staff_id`,`created_at`,`updated_at`) VALUES
(4,'About Us','<p>Beyond Plus CMS is a lightweight, multi-language content management system built on Laravel. This About page uses the <strong>default</strong> page template (with sidebar).</p>[block]1[/block]','default.jpg','about-us','page','default','yes',0,1,1,NOW(),NOW()),
(5,'Our Services','<p>This page uses the <strong>full-width</strong> template (no sidebar), selected via the page template option in the admin.</p>','default.jpg','services','page','fullwidth','yes',0,1,1,NOW(),NOW()),
(6,'Contact','<p>Reach out using the details on the right. This page uses the <strong>contact</strong> template.</p>','default.jpg','contact','page','contact','yes',0,1,1,NOW(),NOW());

-- Demo navigation menu: a "Company" dropdown (About Us, Our Services) + a top-level Contact link
INSERT INTO `bp_menus` (`menu_id`,`menu_name`,`menu_link`,`post_id`,`menu_weight`,`menu_icon`,`parent_id`,`menu_type`,`staff_id`,`lang`,`translate_id`,`created_at`) VALUES
(1,'Company','#',0,1,'',0,'custom',1,1,'0',NOW()),
(2,'About Us','about-us',4,1,'',1,'default',1,1,'0',NOW()),
(3,'Our Services','services',5,2,'',1,'default',1,1,'0',NOW()),
(4,'Contact','contact',6,3,'',0,'default',1,1,'0',NOW());

-- Demo homepage sliders (use the committed placeholder images)
INSERT INTO `bp_sliders` (`slider_id`,`slider_name`,`slider_link`,`slider_type`,`slider_weight`,`slider_description`,`staff_id`,`created_at`,`updated_at`) VALUES
(1,'Welcome to Beyond Plus CMS','la.jpg','slider',1,'A modern, multi-language content management system built on Laravel.',1,NOW(),NOW()),
(2,'Manage Everything in One Place','default.jpg','slider',2,'Posts, pages, menus, media and more — all from a clean admin panel.',1,NOW(),NOW());

-- Demo content block, embedded in the About Us page via the [block]1[/block] shortcode
INSERT INTO `bp_block` (`id`,`title`,`body`,`block_url`,`block_type`,`block_active`,`translate_id`,`lang`,`staff_id`,`created_at`,`updated_at`) VALUES
(1,'Why choose Beyond Plus CMS','Fast, multi-language, and easy to manage — everything you need to publish content, right out of the box.','why-choose','content','yes',0,1,1,NOW(),NOW());

-- Configuration defaults (secrets intentionally blank) + admin module
INSERT INTO `bp_options` (`option_name`,`option_value`,`autoload`,`created_at`) VALUES
('registration_type','phone','yes',NOW()),
('api_enabled','yes','yes',NOW()),
('sms_enabled','no','yes',NOW()),
('sms_provider','smspoh','yes',NOW()),
('sms_sender','','yes',NOW()),
('sms_api_token','','yes',NOW()),
('mail_enabled','no','yes',NOW()),
('mail_provider','mailgun','yes',NOW()),
('mailgun_domain','','yes',NOW()),
('mailgun_secret','','yes',NOW()),
('mail_from','','yes',NOW());

INSERT INTO `bp_modules` (`module_name`,`module_name_mm`,`module_link`,`module_weight`,`module_icon`,`parent_id`,`staff_id`,`section`,`created_at`) VALUES
('Configuration','ဖွဲ့စည်းမှု','configuration',5,'fa fa-cogs',8,1,1,NOW()),
('Themes','ပုံစံများ','themes',6,'fa fa-paint-brush',8,1,1,NOW());

-- Remove the client-specific "Department Pages" module
DELETE FROM `bp_access` WHERE `module_id` = 22;
DELETE FROM `bp_modules` WHERE `module_id` = 22;

-- Give the demo administrator (superadmin, role 4) full access to every module,
-- including modules that shipped without any access row (e.g. Category).
DELETE FROM `bp_access` WHERE `usertype` = 4;
INSERT INTO `bp_access` (`module_id`, `usertype`, `canshow`, `cancreate`, `canedit`, `candelete`)
SELECT `module_id`, 4, 1, 1, 1, 1 FROM `bp_modules`;

SET FOREIGN_KEY_CHECKS=1;

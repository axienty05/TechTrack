-- Backup created before PC Maintenance
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `cache` (`key`,`value`,`expiration`) VALUES ('356a192b7913b04c54574d18c28d46e6395428ab','i:2;','1789975599');
INSERT INTO `cache` (`key`,`value`,`expiration`) VALUES ('356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1789975599;','1789975599');

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `default_sla_minutes` int unsigned NOT NULL DEFAULT '60',
  `icon` varchar(50) DEFAULT 'heroicon-o-wrench',
  `color_hex` varchar(10) DEFAULT '#3B82F6',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  UNIQUE KEY `categories_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categories` (`id`,`name`,`slug`,`code`,`default_sla_minutes`,`icon`,`color_hex`,`created_at`,`updated_at`) VALUES ('1','Hardware & Software (Komputer)','hardware-software-komputer','PC','60','heroicon-o-computer-desktop','#3B82F6','2026-09-11 13:01:24','2026-09-11 13:01:24');
INSERT INTO `categories` (`id`,`name`,`slug`,`code`,`default_sla_minutes`,`icon`,`color_hex`,`created_at`,`updated_at`) VALUES ('2','Infrastruktur & Daya (Power)','infrastruktur-daya-power','PWR','45','heroicon-o-bolt','#F59E0B','2026-09-11 13:01:24','2026-09-11 13:01:24');
INSERT INTO `categories` (`id`,`name`,`slug`,`code`,`default_sla_minutes`,`icon`,`color_hex`,`created_at`,`updated_at`) VALUES ('3','CCTV & Keamanan','cctv-keamanan','CCTV','45','heroicon-o-video-camera','#EF4444','2026-09-11 13:01:24','2026-09-11 13:01:24');
INSERT INTO `categories` (`id`,`name`,`slug`,`code`,`default_sla_minutes`,`icon`,`color_hex`,`created_at`,`updated_at`) VALUES ('4','Perawatan Fisik (General Maintenance)','perawatan-fisik-general-maintenance','MTC','60','heroicon-o-sparkles','#10B981','2026-09-11 13:01:24','2026-09-11 13:01:24');
INSERT INTO `categories` (`id`,`name`,`slug`,`code`,`default_sla_minutes`,`icon`,`color_hex`,`created_at`,`updated_at`) VALUES ('5','Support Operasional & Administrasi','support-operasional-administrasi','OPS','30','heroicon-o-document-text','#8B5CF6','2026-09-11 13:01:24','2026-09-11 13:01:24');
INSERT INTO `categories` (`id`,`name`,`slug`,`code`,`default_sla_minutes`,`icon`,`color_hex`,`created_at`,`updated_at`) VALUES ('6','Lain-lain (Others)','lain-lain-others','OTH','60','heroicon-o-ellipsis-horizontal-circle','#6B7280','2026-09-11 13:01:24','2026-09-11 13:01:24');

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `floor_location` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('1','IT','IT',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('2','FA','Fa',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('3','EXIM','Exim',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('4','HRD','HRD',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('5','RND','R&D',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('6','LAB','Lab',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('7','SC','SC',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('8','ISO','ISO',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('9','PROD','Produksi',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');
INSERT INTO `departments` (`id`,`code`,`name`,`floor_location`,`created_at`,`updated_at`) VALUES ('10','TEK','Teknik',NULL,'2026-09-11 13:07:05','2026-09-11 13:07:05');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `log_attachments`;
CREATE TABLE `log_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_log_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `attachment_type` enum('before','after','scan_document','error_screenshot') NOT NULL DEFAULT 'after',
  `caption` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_attachments_work_log_id_foreign` (`work_log_id`),
  CONSTRAINT `log_attachments_work_log_id_foreign` FOREIGN KEY (`work_log_id`) REFERENCES `work_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('1','1','worklog-attachments/01M27JBBEDVKNVFMB86BV7VXMM.PNG',NULL,NULL,'before',NULL,'2026-09-11 13:26:46','2026-09-11 13:26:46');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('2','1','worklog-attachments/01M27JBBEGDBP7KMJ5FWW7DZH5.PNG',NULL,NULL,'after',NULL,'2026-09-11 13:26:46','2026-09-11 13:26:46');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('3','7','worklog-attachments/01M27N7CEH14Q10QZXA3N36426.jpeg',NULL,NULL,'after',NULL,'2026-09-11 14:17:02','2026-09-11 14:17:02');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('4','8','worklog-attachments/01M2FN59N6X7W3CDK4K17J4CS0.jpeg',NULL,NULL,'before',NULL,'2026-09-14 16:49:49','2026-09-14 16:49:49');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('5','8','worklog-attachments/01M2FN59N8W2JQVKGGW24X2MVK.PNG',NULL,NULL,'after',NULL,'2026-09-14 16:49:49','2026-09-14 16:49:49');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('6','10','worklog-attachments/01M2FNFJ2ZPVCMGM8E2BY646DP.jpeg',NULL,NULL,'before','Dapat menerima IP DHCP tetapi ketika di ubah static tetap trouble/tidak connect ketika di ping','2026-09-14 16:55:25','2026-09-14 16:55:25');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('7','10','worklog-attachments/01M2FNFJ4A44RWXB39Z42YQM9R.jpeg',NULL,NULL,'before',NULL,'2026-09-14 16:55:25','2026-09-14 16:55:25');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('9','15','worklog-attachments/oSeLSe9f9DFCtGn5cH64BNO8yGm42uw16ZU8dOS7.png','hasilscan.PNG','image/png','after',NULL,'2026-09-16 03:22:43','2026-09-16 03:22:43');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('10','12','worklog-attachments/cwwpbPvPxwdugrSYkaeMmUxCRHVax3ipzQvXWdtV.jpg','WhatsApp Image 2026-09-16 at 13.38.24 (1).jpeg','image/jpeg','before',NULL,'2026-09-16 06:41:06','2026-09-16 06:41:06');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('11','12','worklog-attachments/NJLzuuy4q07Vdws5REIEJMqRv47r3duEIbgr6nNO.jpg','WhatsApp Image 2026-09-16 at 13.38.24 (2).jpeg','image/jpeg','after',NULL,'2026-09-16 06:41:24','2026-09-16 06:41:24');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('12','15','worklog-attachments/mvnHFxcjwZOvXBK7VMW8dHVyi3APrZl5VJ9YI4ey.jpg','WhatsApp Image 2026-09-16 at 13.38.24.jpeg','image/jpeg','before',NULL,'2026-09-16 06:41:54','2026-09-16 06:41:54');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('18','1','worklog-attachments/5CBb2Aa5QZ0UahgO1Yqa7dKBOxMOvnRyP9XxfxuD.jpg','test_edit.jpg','image/jpeg','before','Foto Sebelum Perbaikan','2026-09-17 02:54:51','2026-09-17 02:54:51');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('19','1','worklog-attachments/qgDDg2Qgr6kr5Yvgj8oNQna6U8gVjoXqUoKySesy.jpg','test_temp.jpg','image/jpeg','before','Foto Sebelum Perbaikan','2026-09-17 02:56:31','2026-09-17 02:56:31');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('20','1','worklog-attachments/X1OD1kK9kQwW9XYxyw4czGPeWDN2FXTPpSxtIsuG.jpg','test_temp.jpg','image/jpeg','before','Foto Sebelum Perbaikan','2026-09-17 02:57:10','2026-09-17 02:57:10');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('21','1','worklog-attachments/FUf00Mf9roNQD2Uoz31ItNGBhOTlwohGumf4T6LY.jpg','test_inspect.jpg','image/jpeg','before','Foto Sebelum Perbaikan','2026-09-17 02:57:49','2026-09-17 02:57:49');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('22','1','worklog-attachments/cUdWqbR6h5r4NHhkPuU30tlJcNZaSnuPkzr9Z3h5.jpg','test_multi_req.jpg','image/jpeg','before','Foto Sebelum Perbaikan','2026-09-17 02:58:22','2026-09-17 02:58:22');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('23','1','worklog-attachments/CSbblQrPuCU2y0Dakb60NIrT8dzVVNwvdt8G8Ldf.jpg','test_fix.jpg','image/jpeg','before','Foto Sebelum Perbaikan','2026-09-17 03:04:33','2026-09-17 03:04:33');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('24','20','worklog-attachments/df8hv0c6buMMWcgY1B315GpDTqyAgEPkXcaVBweO.png','hasilscanelsa.PNG','image/png','after','Sesudah Scan & Rename','2026-09-17 03:10:11','2026-09-17 03:10:11');
INSERT INTO `log_attachments` (`id`,`work_log_id`,`file_path`,`original_name`,`mime_type`,`attachment_type`,`caption`,`created_at`,`updated_at`) VALUES ('25','33','worklog-attachments/jbZD9DTAkn9LHrnGkCHjOzHR9ntiAnNLEOD8EXO1.png','hasilscanyani21.PNG','image/png','after','Foto Sesudah Perbaikan','2026-09-21 07:26:02','2026-09-21 07:26:02');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('1','0001_01_01_000000_create_users_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('2','0001_01_01_000001_create_cache_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('3','0001_01_01_000002_create_jobs_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('4','2026_09_11_032052_create_categories_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('5','2026_09_11_032053_create_departments_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('6','2026_09_11_032054_create_routine_schedules_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('7','2026_09_11_032054b_create_work_logs_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('8','2026_09_11_032055a_create_routine_checklist_results_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('9','2026_09_11_032056_create_log_attachments_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('10','2026_09_11_032057_create_sparepart_usages_table','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('11','2026_09_11_151651_update_routine_schedules_table_for_flexible_frequencies','1');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES ('12','2026_09_16_024004_add_avatar_to_users_table','2');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `routine_checklist_results`;
CREATE TABLE `routine_checklist_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_log_id` bigint unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `is_passed` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text,
  `checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `routine_checklist_results_work_log_id_foreign` (`work_log_id`),
  CONSTRAINT `routine_checklist_results_work_log_id_foreign` FOREIGN KEY (`work_log_id`) REFERENCES `work_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `routine_schedules`;
CREATE TABLE `routine_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text,
  `category_id` bigint unsigned NOT NULL,
  `frequency` varchar(50) NOT NULL DEFAULT 'monthly',
  `target_day` varchar(100) DEFAULT NULL,
  `checklist_template` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `routine_schedules_category_id_foreign` (`category_id`),
  CONSTRAINT `routine_schedules_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `routine_schedules` (`id`,`title`,`description`,`category_id`,`frequency`,`target_day`,`checklist_template`,`is_active`,`created_at`,`updated_at`) VALUES ('1','Maintenance PC',NULL,'4','monthly','1 Bulan','[\"2 PC GBaku\"]','1','2026-09-11 14:05:44','2026-09-11 14:46:19');
INSERT INTO `routine_schedules` (`id`,`title`,`description`,`category_id`,`frequency`,`target_day`,`checklist_template`,`is_active`,`created_at`,`updated_at`) VALUES ('2','Maintenance Pembersihan PC GBaku','Pembersihan debu blower unit PC area GBaku, cek kelayakan kabel & komponen hardware.','1','monthly','Setiap 1 Bulan Sekali',NULL,'1','2026-09-11 15:20:30','2026-09-11 15:20:30');
INSERT INTO `routine_schedules` (`id`,`title`,`description`,`category_id`,`frequency`,`target_day`,`checklist_template`,`is_active`,`created_at`,`updated_at`) VALUES ('3','Maintenance Pembersihan PC Area Pabrik','Pembersihan debu & kotoran blower seluruh unit PC di area pabrik/produksi secara massal.','1','quarterly','Setiap 3 Bulan Sekali',NULL,'1','2026-09-11 15:20:30','2026-09-11 15:20:30');
INSERT INTO `routine_schedules` (`id`,`title`,`description`,`category_id`,`frequency`,`target_day`,`checklist_template`,`is_active`,`created_at`,`updated_at`) VALUES ('4','Maintenance Pembersihan PC Area Kantor','Pembersihan debu dan maintenance berkala unit komputer/PC staf area kantor.','1','quarterly','Setiap 3 Bulan Sekali',NULL,'1','2026-09-11 15:20:30','2026-09-16 04:39:45');
INSERT INTO `routine_schedules` (`id`,`title`,`description`,`category_id`,`frequency`,`target_day`,`checklist_template`,`is_active`,`created_at`,`updated_at`) VALUES ('5','Pembersihan Sawang & Posisi Kamera CCTV','Pembersihan sawang/sarang laba-laba, debu lensa kamera, dan koreksi sudut pandang CCTV.','3','incidental','Insidental / Fleksibel',NULL,'1','2026-09-11 15:20:30','2026-09-11 15:20:30');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('4Rba1d3US5Nmb4XZDSAwrtE7mL2ptbmUiBf1rk7P',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNGpVeXhuRktWb3pmS3lzdUtoS3F5azl5YjJFNVN2aDFXam90UXNhciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=','1789613162');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('CNoPWPrnDbWeWj05i5tWrS2xYRZpyfLP8U94r3n5','1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoibDVSTjdMVnM0VU1jeVZDZkU5TmRXWE5Jc1RCZHVkdHBRQU5FcFkxcCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoibWFyeSI7YToxOntzOjU6InRvYXN0IjthOjA6e319fQ==','1789975593');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('ipp6i0FgjBmzuyddplR0UtGbu42lyK8hDUvm9qMC',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVJsMHozaUdaOGF1a2JhdlRLMmVaNE1vRk0zYVhSMG94Y0lJQmMxbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=','1789963237');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('jbeecXl4zzepM0hq6aXJrDwN5EZxa6JlaO0D6HqY','1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiUEhDMzM3MmJ1N3h5TEQ1NWhiWjBjbHhPbG02TFIzT2d0eFlQc1UxdCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwMjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2xpdmV3aXJlL3ByZXZpZXctZmlsZS9CTHZ1aUI3WHk5UkNhUTVCSkhzZE45NjVVOUo2djctbWV0YWFHRnphV3h6WTJGdVpXeHpZUzVRVGtjPS0ucG5nP2V4cGlyZXM9MTc4OTYxNzU5OSZzaWduYXR1cmU9YTMyNWE1MTQ1NTRkZjcyN2E0NTcyZGZlNzcxYTQyYjU4MzlmYTg3YWNhNzhiYzI3YTM1ODA5OThlZmUwOTM5ZiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJtYXJ5IjthOjE6e3M6NToidG9hc3QiO2E6MDp7fX19','1789614875');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('MUI6ROcuZ5fCx2MyW15AaIl5Sl2VzLuIN2nNOdnJ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456','YToyOntzOjY6Il90b2tlbiI7czo0MDoicWhRek9KcjZmcTRMUEVCeWtINVA5cEIxT1FFbG9NbXZpbm9DVTlybCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==','1789725181');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('nBewn0oWzSFMCWXii4W6iXCWTZ31cWsuBddhvlZS','1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoibEl3TDN6Z1pyTFVJaFEyR0YyeUJiVjlZOUhITUlCYWM4RUFyYUhLRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6Mjp7aTowO3M6MTY6Im1hcnkudG9hc3QudGl0bGUiO2k6MTtzOjIyOiJtYXJ5LnRvYXN0LmRlc2NyaXB0aW9uIjt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC93b3JrLWxvZ3MiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoibWFyeSI7YToxOntzOjU6InRvYXN0IjthOjA6e319fQ==','1789631136');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('SYPjq0uQAvO8hYzZcpIwb3ULoviPGdhhpeHNd24Y',NULL,'127.0.0.1','curl/8.13.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUTE1WDFadUFZU2pLY3pvNEh1NUpkOGR0TllCaHZQM1FYWkhpT3c2cyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2xvZ291dCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9nb3V0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==','1789725409');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('WCtWej3EKFde4YD9KsRilPCrdhZ38FLxiRC8BSDY',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiT2lnRHpoVVkyQnhONTROc0RETzBLamNaWThyb0dwckN2WjVreEVqUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3dvcmstbG9ncyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19','1789638676');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('XAPSxJZVhwHSoE0L2Y0HXfPtxb0fQQXfidJRKVoL',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVEJFVmdZT2lYVUswTHZFY0pzWnE2UXJxQjRpazRScVV3VHF4eTdvaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=','1789725454');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('ym0DqYle9ziILeUhp44qF4lMfOx7dSAgtxnehB2Z','1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiT3p6SjVzWDhGUEFaNkdtMlptV3dYTkMwNHp6T2dRVEc5RjJtMlJPNyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvd29yay1sb2dzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6Im1hcnkiO2E6MTp7czo1OiJ0b2FzdCI7YTowOnt9fX0=','1789701224');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('zjoOJgU28EsPyi75j3JqMvPQbkMB6O1rQ5UF37KC',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2Z1U0ZBQXY4bnJtbGNPd0RLZjZQV212NktXVTJwYXdoMVBxdFN4dCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=','1789700658');

DROP TABLE IF EXISTS `sparepart_usages`;
CREATE TABLE `sparepart_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_log_id` bigint unsigned NOT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit` varchar(20) NOT NULL DEFAULT 'Pcs',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sparepart_usages_work_log_id_foreign` (`work_log_id`),
  CONSTRAINT `sparepart_usages_work_log_id_foreign` FOREIGN KEY (`work_log_id`) REFERENCES `work_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','it_support','it_lead') NOT NULL DEFAULT 'it_support',
  `phone_number` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`,`name`,`username`,`email`,`avatar`,`email_verified_at`,`password`,`role`,`phone_number`,`is_active`,`remember_token`,`created_at`,`updated_at`) VALUES ('1','Huri','Huri','huri@logit.local','avatars/U2mskokx9n3D3NAmpNHvjG9mkT71AjY6mBkocCw0.jpg',NULL,'$2y$12$mJqKs7qpae3/zTKjyYcsw.fGMaVwbR9dldsMMEnqM3SJSMa5iQuQS','admin','','1','x0l6pBcGEPQLY5hOa2wlHonqN1mYJFrQH5rOlh0kQcBFJABaQQOFp8XVIG00','2026-09-11 12:49:34','2026-09-16 02:47:06');

DROP TABLE IF EXISTS `work_logs`;
CREATE TABLE `work_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(30) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `routine_schedule_id` bigint unsigned DEFAULT NULL,
  `task_type` enum('reactive','preventive','administrative') NOT NULL DEFAULT 'reactive',
  `title` varchar(200) NOT NULL,
  `description` text,
  `action_taken` text,
  `requester_name` varchar(100) DEFAULT 'Internal IT',
  `device_identifier` varchar(100) DEFAULT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','waiting_sparepart','escalated','completed','cancelled') NOT NULL DEFAULT 'in_progress',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `duration_minutes` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_logs_ticket_number_unique` (`ticket_number`),
  KEY `work_logs_department_id_foreign` (`department_id`),
  KEY `work_logs_routine_schedule_id_foreign` (`routine_schedule_id`),
  KEY `work_logs_user_id_status_started_at_index` (`user_id`,`status`,`started_at`),
  KEY `work_logs_category_id_started_at_index` (`category_id`,`started_at`),
  KEY `work_logs_device_identifier_index` (`device_identifier`),
  CONSTRAINT `work_logs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `work_logs_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_logs_routine_schedule_id_foreign` FOREIGN KEY (`routine_schedule_id`) REFERENCES `routine_schedules` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('1','IT-20260911-123',NULL,'5','2',NULL,'administrative','Crosscheck Dokumen Scan','Hasil dokumen scan ada yang korup.','Crosscheck dokumen dan hasil scan.
Kemudian scan ulang dokumen yang korup','Elsa','','medium','completed','2026-09-11 00:00:00','2026-09-11 00:00:00','0','2026-09-11 13:22:36','2026-09-16 08:19:37');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('2','IT-20260911-564',NULL,'1','1',NULL,'reactive','Membuat program pencatatan pekerjaan IT',NULL,NULL,'Internal IT',NULL,'medium','in_progress','2026-09-11 09:30:00',NULL,'0','2026-09-11 13:30:39','2026-09-11 13:41:52');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('3','IT-20260911-263',NULL,'4','3',NULL,'preventive','Maintenance PC',NULL,NULL,'Yuli','PC-Yuli','medium','completed','2026-09-10 16:05:00','2026-09-10 16:25:00','4','2026-09-11 13:36:18','2026-09-11 14:06:27');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('4','IT-20260911-881',NULL,'4','6',NULL,'preventive','Maintenance UPS AAS',NULL,NULL,'AAS','UPS APC SRT10KXLI 10KVA','medium','completed','2026-09-10 16:30:40',NULL,'0','2026-09-11 13:53:22','2026-09-11 13:53:22');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('5','IT-20260911-215',NULL,'1','6',NULL,'reactive','Update/Pending windows',NULL,NULL,'Lab-Esti',NULL,'medium','completed','2026-09-10 16:40:00','2026-09-10 16:45:00','0','2026-09-11 13:56:06','2026-09-11 13:56:06');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('6','IT-20260911-692',NULL,'5','4',NULL,'administrative','Setting Laptop & Proyektor untuk Zoom Meeting','','','Bu Susi','','medium','completed','2026-09-10 00:00:00','2026-09-10 00:00:00','0','2026-09-11 13:59:47','2026-09-16 08:19:45');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('7','IT-20260911-556',NULL,'1','7',NULL,'reactive','Troubleshooting hardware','Laptop bu Intan jatuh sehingga connector mouse usb nya nyangkut/tidak bisa diambil','buka cover laptop kemudian congkel usb nya.
port usb nya masih bisa dipakai tetapi agak longgar.','Bu Intan','Laptop-Asus','medium','completed','2026-09-07 08:15:14','2026-09-07 21:13:01','0','2026-09-11 14:14:47','2026-09-11 14:14:47');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('8','IT-20260911-121',NULL,'5','2',NULL,'administrative','Scan Documents',NULL,NULL,'Yani',NULL,'medium','completed','2026-09-11 16:15:08','2026-09-14 16:44:40','0','2026-09-11 16:16:03','2026-09-14 16:45:02');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('9','IT-20260914-201',NULL,'1','7',NULL,'reactive','Mesin FC tidak bisa menyimpan hasil scan di DS(Server)','Status log pada scan NG bukan OK','Sementara scan di tempat IT kemudian kirim file lewat LMC','Yoga',NULL,'medium','completed','2026-09-11 16:45:09','2026-09-11 17:01:04','0','2026-09-14 10:16:15','2026-09-14 10:16:58');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('10','IT-20260914-484',NULL,'1','7',NULL,'reactive','Cek Network mesin FC belakang','Mesin FC masih belum bisa connect','Sedang menunggu teknisi mesin FC datang.
Interface ke 2 belum diubah ke wired lan sehingga terjadi trouble, karna setting interface sebelum nya masih wireless lan.','Supply Change',NULL,'medium','completed','2026-09-14 08:02:08','2026-09-14 01:17:53','0','2026-09-14 10:43:06','2026-09-14 16:55:25');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('11','IT-20260914-232',NULL,'1','6',NULL,'reactive','Tidak bisa save .pdf di word','Ketika save file dengan ext pdf masuk nya di temp C tidak bisa di Drive T/Lab','Klik Export > pilih Create PDF/XPS Document > Uncentang \"Open file after publishing\" > publish.','Ratih',NULL,'medium','completed','2026-09-14 12:10:03','2026-09-14 16:51:45','0','2026-09-14 12:22:08','2026-09-14 16:52:06');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('12','IT-20260914-555',NULL,'1','5',NULL,'reactive','Ups tidak bisa backup listrik','Ups bunyi tit panjang ketika dinyalakan','Ganti baterai UPS','Zainal','','medium','completed','2026-09-14 12:22:00','2026-09-15 08:25:00','1203','2026-09-14 12:24:27','2026-09-16 06:41:06');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('14','IT-20260915-518',NULL,'1','7',NULL,'reactive','PC tidak bisa buka office','Pada saat buka office status nya \"update kemudian forclose\".','Setelah di check disk C dan D tidak ada badsector, kemudian ketika di repair tidak bisa (masih error) kemudian coba uninstall akhir nya bisa.','Nunuk','PC-Nunuk','high','completed','2026-09-15 09:00:00','2026-09-16 07:55:00','1375','2026-09-15 09:17:06','2026-09-16 02:11:14');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('15','IT-20260916-693','1','5','2',NULL,'administrative','Scan Documents','','','Amel','','medium','completed','2026-09-16 00:00:00','2026-09-16 00:00:00','0','2026-09-16 03:19:44','2026-09-16 08:19:21');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('16','IT-20260916-499','1','4','3',NULL,'preventive','Maintenance PC','','','Pipit','PC-Pipit','medium','completed','2026-09-09 04:06:00','2026-09-09 04:18:00','12','2026-09-16 04:07:30','2026-09-16 04:07:30');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('17','IT-20260916-271','1','4','3',NULL,'preventive','Maintenance PC','','','Arif','PC-Arif','medium','completed','2026-09-02 04:07:00','2026-09-02 11:08:00','421','2026-09-16 04:08:29','2026-09-16 04:08:29');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('18','IT-20260916-991','1','4','9',NULL,'reactive','Maintenance PC','','','Packing','PC-Packing','medium','completed','2026-08-28 02:14:00','2026-08-28 15:26:00','792','2026-09-16 06:27:16','2026-09-16 06:27:16');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('20','IT-20260916-551','1','5','2',NULL,'administrative','Scan Documents','','','Elsa','','medium','completed','2026-09-16 00:00:00','2026-09-17 00:00:00','1440','2026-09-16 08:18:16','2026-09-18 03:07:33');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('21','IT-20260917-399','1','4','1',NULL,'preventive','Maintenance PC','','Maintenance PC Baru & Lama.','Cisca','PC-Cisca','medium','completed','2026-09-17 00:00:00','2026-09-17 00:00:00','0','2026-09-17 07:43:31','2026-09-17 07:43:31');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('22','IT-20260917-072','1','4','2',NULL,'preventive','Maintenance PC','','Maintenance PC Lama','Elvi','','medium','completed','2026-09-17 00:00:00','2026-09-17 00:00:00','0','2026-09-17 07:44:12','2026-09-17 07:44:12');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('23','IT-20260917-490','1','6','1',NULL,'administrative','Terima baterai UPS Yuasa','','','Huri','','medium','completed','2026-09-17 00:00:00','2026-09-17 00:00:00','0','2026-09-17 07:45:36','2026-09-17 07:45:36');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('24','IT-20260918-953','1','1','6',NULL,'reactive','Komputer tiba tiba mati','Komputer tiba tiba mati','Hasil CheckDisk tidak ada badsector, jadi sementara dipakai terlebih dahulu sambil di follow up','Stevi','','medium','completed','2026-09-18 00:00:00','2026-09-18 00:00:00','0','2026-09-18 03:12:02','2026-09-18 03:12:02');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('25','IT-20260918-262','1','1','7',NULL,'reactive','Komputer tidak bisa print','Komputer tidak bisa print/hasil cetak tidak keluar.','Restart komputer fgw-01 dan printer kemudian setelah dicoba print masih tidak bisa dengan status printer offline. kemudia coba colok ulang kabel printer dan setelah dicoba kembali print akhir nya keluar.','Khoiron/Citra','PC-FGW-01','medium','completed','2026-09-18 00:00:00','2026-09-18 00:00:00','0','2026-09-18 07:52:07','2026-09-18 07:52:07');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('26','IT-20260918-391','1','4','2',NULL,'preventive','Maintenance PC','','','Yani','PC-Yani','medium','completed','2026-09-18 00:00:00','2026-09-18 00:00:00','0','2026-09-18 07:52:42','2026-09-18 07:52:42');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('27','IT-20260918-733','1','4','2',NULL,'preventive','Maintenance PC','','','Amel','PC-Amel','medium','completed','2026-09-18 00:00:00','2026-09-18 00:00:00','0','2026-09-18 07:53:09','2026-09-18 07:53:09');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('28','IT-20260918-673','1','4','10',NULL,'preventive','Maintenance PC','','','I\'im','','medium','completed','2026-09-18 00:00:00','2026-09-18 00:00:00','0','2026-09-18 09:46:47','2026-09-18 09:46:47');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('29','IT-20260918-559','1','4','10',NULL,'preventive','Maintenance PC','','','Andre','','medium','completed','2026-09-18 00:00:00','2026-09-18 00:00:00','0','2026-09-18 09:47:11','2026-09-18 09:49:09');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('30','IT-20260918-302','1','1','10',NULL,'reactive','UPS sering bunyi','UPS sering bunyi, setelah diperiksa kabel power UPS ada yang terbakar sehingga input listrik terkendala, jadi UPS jalan baterai.','Ganti kabel power','Andre','','medium','completed','2026-09-18 00:00:00','2026-09-21 00:00:00','4320','2026-09-18 09:48:59','2026-09-21 07:26:29');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('31','IT-20260921-479','1','1','9',NULL,'reactive','Kipas CPU bunyi','Kabel kipas ada yang nyangkut sehingga menyebabkan bunyi','Tali kabel agar tidak berantakan','Iman','PC-Packing','medium','completed','2026-09-21 00:00:00','2026-09-21 00:00:00','0','2026-09-21 05:43:37','2026-09-21 05:43:37');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('32','IT-20260921-382','1','1','7',NULL,'reactive','Tidak bisa buka PDF','Pada saat dicek sudah buka PDF, jadi kemungkinan pada saat bu nunuk buka file attachment pdf sedang install update sehingga tidak bisa dibuka.','','Nunuk','PC-Nunuk','medium','completed','2026-09-21 00:00:00','2026-09-21 00:00:00','0','2026-09-21 05:45:07','2026-09-21 05:45:07');
INSERT INTO `work_logs` (`id`,`ticket_number`,`user_id`,`category_id`,`department_id`,`routine_schedule_id`,`task_type`,`title`,`description`,`action_taken`,`requester_name`,`device_identifier`,`priority`,`status`,`started_at`,`completed_at`,`duration_minutes`,`created_at`,`updated_at`) VALUES ('33','IT-20260921-390','1','5','2',NULL,'administrative','Scan Documents','','','Yani','','medium','completed','2026-09-21 00:00:00','2026-09-21 00:00:00','0','2026-09-21 06:02:50','2026-09-21 07:26:02');

SET FOREIGN_KEY_CHECKS=1;

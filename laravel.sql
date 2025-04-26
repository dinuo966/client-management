-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.7.26 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出 laravel 的数据库结构
CREATE DATABASE IF NOT EXISTS `laravel` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `laravel`;


-- 导出  表 laravel.customers 结构
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户ID，主键，自增',
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '客户名字',
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '客户姓氏',
  `age` int(11) DEFAULT NULL COMMENT '客户年龄',
  `dob` date DEFAULT NULL COMMENT '出生日期(Date of Birth)',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '电子邮箱地址',
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '客户创建时间(自动设置)',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户信息表';

-- 正在导出表  laravel.customers 的数据：~0 rows (大约)
DELETE FROM `customers`;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;


-- 导出  表 laravel.failed_jobs 结构
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID，主键',
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务唯一标识符UUID',
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队列连接名称',
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队列名称',
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务负载数据(序列化后的任务内容)',
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '异常信息(失败原因详情)',
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '任务失败时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='失败队列任务记录表';

-- 正在导出表  laravel.failed_jobs 的数据：~0 rows (大约)
DELETE FROM `failed_jobs`;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;


-- 导出  表 laravel.migrations 结构
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '迁移记录ID，自增主键',
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '迁移文件名称',
  `batch` int(11) NOT NULL COMMENT '迁移批次号，标识同一次执行的迁移',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Laravel迁移记录表';

-- 正在导出表  laravel.migrations 的数据：~10 rows (大约)
DELETE FROM `migrations`;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2025_04_26_095558_create_customers_table', 1),
	(6, '2016_06_01_000001_create_oauth_auth_codes_table', 2),
	(7, '2016_06_01_000002_create_oauth_access_tokens_table', 2),
	(8, '2016_06_01_000003_create_oauth_refresh_tokens_table', 2),
	(9, '2016_06_01_000004_create_oauth_clients_table', 2),
	(10, '2016_06_01_000005_create_oauth_personal_access_clients_table', 2),
	(11, '2014_10_12_100000_create_password_resets_table', 3),
	(12, '2025_04_26_101941_create_m_f_a_tokens_table', 4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;


-- 导出  表 laravel.m_f_a_tokens 结构
CREATE TABLE IF NOT EXISTS `m_f_a_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'MFA令牌ID，自增主键',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID，关联users表',
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '验证令牌，通常为一次性验证码',
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '令牌过期时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `m_f_a_tokens_user_id_foreign` (`user_id`),
  CONSTRAINT `m_f_a_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='多因素认证(MFA)令牌表';

-- 正在导出表  laravel.m_f_a_tokens 的数据：~0 rows (大约)
DELETE FROM `m_f_a_tokens`;
/*!40000 ALTER TABLE `m_f_a_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_f_a_tokens` ENABLE KEYS */;


-- 导出  表 laravel.oauth_access_tokens 结构
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '访问令牌ID，主键',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '用户ID，关联users表，可为空表示客户端凭证授权',
  `client_id` bigint(20) unsigned NOT NULL COMMENT 'OAuth客户端ID，关联oauth_clients表',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '令牌名称，用于个人访问令牌',
  `scopes` text COLLATE utf8mb4_unicode_ci COMMENT '授权范围，JSON数组格式',
  `revoked` tinyint(1) NOT NULL COMMENT '是否已撤销，1表示已撤销',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `expires_at` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OAuth访问令牌表';

-- 正在导出表  laravel.oauth_access_tokens 的数据：~31 rows (大约)
DELETE FROM `oauth_access_tokens`;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
	('0419d16bc898e7298cbd341052617d18475bf27e113702c641b12280f0ffedfe64d6644f2fe7f30b', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:31:06', '2025-04-26 12:31:06', '2025-10-26 12:31:06'),
	('104e52a6fb4bc97e833b068e338ff4eecd4925371a9f5addeb627b84e779eaacabe8930e08ff3222', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:15:10', '2025-04-26 13:15:10', '2025-10-26 13:15:10'),
	('104ec3f44ee1991097cf40117c2810e0ab74cbbd371026a8b025c2138d35c141dcac4a4ceb6b3145', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:56:36', '2025-04-26 12:56:36', '2025-10-26 12:56:36'),
	('19502def5530a60a362515e8e26e34838333f15f80cae499581b9bd7eb4e5c8f095b18fa6aaca1ed', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:03:45', '2025-04-26 13:03:45', '2025-10-26 13:03:45'),
	('2ac82d33409ea1a19aeece77aaaab6b98c91b34c80d1cfd8479ff2cb244588dad5f7cb364b2bc804', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:30:57', '2025-04-26 12:30:57', '2025-10-26 12:30:57'),
	('2de23ac62e03f625b17d64a3a1c843b2717ea7414df0968c28177f07b5dcd311c6d3fb196aad7bf5', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:05:05', '2025-04-26 13:05:05', '2025-10-26 13:05:05'),
	('3102342bce44f53441680d86eb0bb6a1460557effc181c46fcdd84c08a552771a32a3442bea9cbf5', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:48:15', '2025-04-26 12:48:15', '2025-10-26 12:48:15'),
	('40b1bfe4eea5ae8a035b97f22cc8223d800fb935f8f91ac9f154d15b47275b6d0f02a2c6091b140c', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:44:59', '2025-04-26 12:44:59', '2025-10-26 12:44:59'),
	('4e1f46c9892ffe6b23011f18fda609bd16e1bfa28b6b279691de61c35c91737a1afe31f6e2fb0330', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:58:07', '2025-04-26 12:58:07', '2025-10-26 12:58:07'),
	('6b2df3e947a1acd5a5813f40ad6080133b6ea31e161ffad91e0b5cfd935ec9f82afb6a4dc2d06571', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:04:16', '2025-04-26 13:04:16', '2025-10-26 13:04:16'),
	('72f141c32d29cac0afe51688fe9a8a4e81fa4c9eb5800cd8600ef973da43f2fe1b319fc1a0850e8c', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:34:52', '2025-04-26 12:34:52', '2025-10-26 12:34:52'),
	('7953df460ab728146acbe2215cd14d8cbe946779974bd80fb31c7b5bca1cec3310aadad862f4e428', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:29:38', '2025-04-26 12:29:38', '2025-10-26 12:29:38'),
	('7ca3cad143e84a9fe8e7068d270908c05cc38c926007ef542efabd6c4b94a7933305ff8eed8ba363', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:35:47', '2025-04-26 12:35:47', '2025-10-26 12:35:47'),
	('7e1d3706b9b4edc1d8f683115a83660d316d779e0014fccd559e301140fe7880661a0bf54e11099e', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:44:22', '2025-04-26 12:44:22', '2025-10-26 12:44:22'),
	('7ec2e3d6adfe8fc96b657d8649324c32d0ec880a3808e520bc168baa905902750ebb3d760db188fa', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:03:35', '2025-04-26 13:03:35', '2025-10-26 13:03:35'),
	('7f7bad9ad4cef5f668ccce2db0cc01602bd84f11da0f0f0f36913d7f2a87d10acd6b6b19a13242f6', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:30:48', '2025-04-26 12:30:48', '2025-10-26 12:30:48'),
	('85a570b56b8ae48044c3a76165f84331b3d818055d91120be7edaf899775020c590b0c1025316b04', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:30:45', '2025-04-26 12:30:45', '2025-10-26 12:30:45'),
	('8dfedbe11ebf09f177350451ccf2634c4414bec39c2c417af861b06ab1d70d31f6499a87090470f1', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:45:01', '2025-04-26 12:45:01', '2025-10-26 12:45:01'),
	('910c09c1aa17dd8f756d6db28c18d8744e2e0baa086c32bd0641fa938c10c5675522b805b50cbd7c', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:38:51', '2025-04-26 12:38:51', '2025-10-26 12:38:51'),
	('96d1ea880fa54f57dc1cd68de16a0dd3f9da99264942dc9a84cf04f050d1c54f76ddd13ebc246b99', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:15:27', '2025-04-26 13:15:27', '2025-10-26 13:15:27'),
	('a43bb217a8a841a8e5c59e067d880a6ee7e0e828b890f478388a509e83c0d8b5fe1f2fc9c4f6af62', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:59:06', '2025-04-26 12:59:06', '2025-10-26 12:59:06'),
	('a6613dc4804d580446f3fdea5dfdbb785d47a86bb2afd902758db9a87b29369c564a1dce30f465de', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:38:34', '2025-04-26 12:38:34', '2025-10-26 12:38:34'),
	('a7db2391f326a575eb79cb77a9f3617fd51f69212304b663fc2a3b7b2650e730bde85f91221ec675', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:33:47', '2025-04-26 12:33:47', '2025-10-26 12:33:47'),
	('ac8cf4c59db45101007866bd310d2542c7b392272f8d276aafce66acf9ef01a8cb8df10e1cdbb1ca', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:57:07', '2025-04-26 12:57:07', '2025-10-26 12:57:07'),
	('ae8dbe6ebd34b069d9f41abfbf4a328ae18f3f95272951200a55ccb3517df2df882b6efe66eae303', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:32:38', '2025-04-26 12:32:38', '2025-10-26 12:32:38'),
	('baec0f6ec5ca4ae1176502e21cf2a335b17bebe8fe7a7cd754d188a914a6798b113c6daa0e1ffa9b', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:45:28', '2025-04-26 12:45:28', '2025-10-26 12:45:28'),
	('c957b07e5c0ce02c7e93e9849c084360a9ea8f3d5f9806ee162f0eac414ef23b4ddbc3bc0c810055', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:48:37', '2025-04-26 12:48:37', '2025-10-26 12:48:37'),
	('ca7fc43237d5bce2fd7bb18e94fed3261649bac13e4a08ff9eae53e99c36883bb84e18d6cede0fdc', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:32:12', '2025-04-26 12:32:12', '2025-10-26 12:32:12'),
	('dbf4374d1a72c86f218becaf6e8af6d5cea29e1a61fb84602b09c5c9fee6b16c773609edab847a5e', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:48:29', '2025-04-26 12:48:29', '2025-10-26 12:48:29'),
	('de288b2886f596972199f749dd44819f7767fa1aba6bb5a9d164a480b78cd8a7d6495352bf7f9917', 2, 1, 'api-token', '[]', 0, '2025-04-26 13:14:50', '2025-04-26 13:14:50', '2025-10-26 13:14:50'),
	('ea223c2bfd99dd7a857b4f8fb7bc1fa8ec895390ffc25ba1cb52593e53ac721827297f9a0d194265', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:30:54', '2025-04-26 12:30:54', '2025-10-26 12:30:54'),
	('ef7f6e63cb39f6682cf7bbefa4a3b7d561728517e5273581102916ba9863a4110aa369f4703d9cd0', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:36:08', '2025-04-26 12:36:08', '2025-10-26 12:36:08'),
	('f13105dc132271eb1199336c74a86a851e5e53385fd7fe5c89b3149dbde8a6ce434780d4f8798967', 2, 1, 'web-token', '[]', 0, '2025-04-26 12:43:34', '2025-04-26 12:43:35', '2025-10-26 12:43:34'),
	('fa94a94595451e3c6a512042f9dd18fb9bd38db51d8ef6d655fc5673a3fcc8b0569af3c71ab67c50', 2, 1, 'api-token', '[]', 0, '2025-04-26 12:48:26', '2025-04-26 12:48:26', '2025-10-26 12:48:26');
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;


-- 导出  表 laravel.oauth_auth_codes 结构
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '授权码ID，主键',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID，关联users表',
  `client_id` bigint(20) unsigned NOT NULL COMMENT '客户端ID，关联oauth_clients表',
  `scopes` text COLLATE utf8mb4_unicode_ci COMMENT '授权范围，JSON数组格式',
  `revoked` tinyint(1) NOT NULL COMMENT '是否已撤销，1表示已撤销',
  `expires_at` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OAuth授权码表';

-- 正在导出表  laravel.oauth_auth_codes 的数据：~0 rows (大约)
DELETE FROM `oauth_auth_codes`;
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;


-- 导出  表 laravel.oauth_clients 结构
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户端ID，自增主键',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '关联的用户ID，可为空',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '客户端应用名称',
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '客户端密钥',
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '认证提供者',
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '授权成功后的重定向URL',
  `personal_access_client` tinyint(1) NOT NULL COMMENT '是否为个人访问客户端，1表示是',
  `password_client` tinyint(1) NOT NULL COMMENT '是否为密码授权客户端，1表示是',
  `revoked` tinyint(1) NOT NULL COMMENT '是否已撤销，1表示已撤销',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OAuth客户端应用表';

-- 正在导出表  laravel.oauth_clients 的数据：~2 rows (大约)
DELETE FROM `oauth_clients`;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Laravel Personal Access Client', 'mlW9y82iBqng9bu8sLbaRBaVY816t23W0dn73LN5', NULL, 'http://localhost', 1, 0, 0, '2025-04-26 10:08:43', '2025-04-26 10:08:43'),
	(2, NULL, 'Laravel Password Grant Client', 'KCulTttBJl6mAon5nyKWFtVJphADnKNUBVQj7LrA', 'users', 'http://localhost', 0, 1, 0, '2025-04-26 10:08:44', '2025-04-26 10:08:44');
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;


-- 导出  表 laravel.oauth_personal_access_clients 结构
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID，自增主键',
  `client_id` bigint(20) unsigned NOT NULL COMMENT '客户端ID，关联oauth_clients表',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OAuth个人访问客户端关联表';

-- 正在导出表  laravel.oauth_personal_access_clients 的数据：~0 rows (大约)
DELETE FROM `oauth_personal_access_clients`;
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-04-26 10:08:43', '2025-04-26 10:08:43');
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;


-- 导出  表 laravel.oauth_refresh_tokens 结构
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '刷新令牌ID，主键',
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关联的访问令牌ID',
  `revoked` tinyint(1) NOT NULL COMMENT '是否已撤销，1表示已撤销',
  `expires_at` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OAuth刷新令牌表';

-- 正在导出表  laravel.oauth_refresh_tokens 的数据：~0 rows (大约)
DELETE FROM `oauth_refresh_tokens`;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;


-- 导出  表 laravel.password_resets 结构
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱',
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '重置令牌',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='密码重置记录表(旧版)';

-- 正在导出表  laravel.password_resets 的数据：~0 rows (大约)
DELETE FROM `password_resets`;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;


-- 导出  表 laravel.password_reset_tokens 结构
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱，主键',
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '重置令牌',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='密码重置令牌表(新版)';

-- 正在导出表  laravel.password_reset_tokens 的数据：~0 rows (大约)
DELETE FROM `password_reset_tokens`;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;


-- 导出  表 laravel.personal_access_tokens 结构
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌ID，自增主键',
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '令牌所属模型类型',
  `tokenable_id` bigint(20) unsigned NOT NULL COMMENT '令牌所属模型ID',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '令牌名称',
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '哈希后的令牌值',
  `abilities` text COLLATE utf8mb4_unicode_ci COMMENT '令牌权限，JSON数组格式',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT '最后使用时间',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Laravel Sanctum个人访问令牌表';

-- 正在导出表  laravel.personal_access_tokens 的数据：~0 rows (大约)
DELETE FROM `personal_access_tokens`;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;


-- 导出  表 laravel.users 结构
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID，自增主键',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱，唯一',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT '邮箱验证时间，NULL表示未验证',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码（哈希后）',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '记住我令牌',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- 正在导出表  laravel.users 的数据：~2 rows (大约)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'TestUser', 'test@example.com', NULL, '$2y$10$qq0p3NuzL8vkjXlUz0zuge4PJ44Rn7UkLgVQsuHdhXeQR5qXZBLDC', NULL, '2025-04-26 10:10:53', '2025-04-26 10:10:53'),
	(2, '李世民', 'zq533124@163.com', NULL, '$2y$10$v6plJS3VWtkJEhd4z3ydt.l.yhL2lvOFtBV.TiVEGeVu4LdAN/mES', 'BpwIGxaZdtRJthmsAUeb0ddPUNSqi5bFvoXxZ7oebaqN4EBw8P4yj07EtXHR', '2025-04-26 11:29:10', '2025-04-26 14:13:02');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

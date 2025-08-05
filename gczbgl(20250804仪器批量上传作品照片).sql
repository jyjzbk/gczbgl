-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-08-04 10:34:57
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `gczbgl`
--

-- --------------------------------------------------------

--
-- 表的结构 `administrative_regions`
--

CREATE TABLE `administrative_regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL COMMENT '区域代码',
  `name` varchar(100) NOT NULL COMMENT '区域名称',
  `level` tinyint(4) NOT NULL COMMENT '级别：1省 2市 3区县 4学区',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '父级ID',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL COMMENT '详细地址',
  `contact_person` varchar(100) DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱地址',
  `description` text DEFAULT NULL COMMENT '机构描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `administrative_regions`
--

INSERT INTO `administrative_regions` (`id`, `code`, `name`, `level`, `parent_id`, `sort_order`, `status`, `created_at`, `updated_at`, `address`, `contact_person`, `contact_phone`, `email`, `description`) VALUES
(1, '410000', '河北省', 1, NULL, 1, 1, '2025-07-18 15:17:55', '2025-07-18 15:44:43', NULL, NULL, NULL, NULL, NULL),
(6, '41010202', '南董学区', 4, 10, 2, 1, '2025-07-18 15:17:55', '2025-07-18 16:09:34', NULL, NULL, NULL, NULL, NULL),
(7, '41010301', '南营学区', 4, 15, 1, 1, '2025-07-18 15:17:55', '2025-07-18 16:09:34', NULL, NULL, NULL, NULL, NULL),
(9, '130100', '石家庄市', 2, 1, 1, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(10, '130182', '藁城区', 3, 9, 1, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(11, '13018201', '廉州学区', 4, 10, 1, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(14, '13018204', '兴安学区', 4, 10, 4, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(15, '130183', '栾城区', 3, 9, 2, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(16, '130102', '长安区', 3, 9, 3, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(17, '130107', '井陉矿区', 3, 9, 4, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(18, '130185', '鹿泉区', 3, 9, 5, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(19, '130200', '唐山市', 2, 1, 2, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(20, '130900', '沧州市', 2, 1, 3, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL),
(21, '131100', '衡水市', 2, 1, 4, 1, '2025-07-18 15:31:16', '2025-07-18 15:31:16', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-user_permissions_15', 'a:32:{i:0;s:29:\"basic.equipment_standard.view\";i:1;s:27:\"basic.textbook_chapter.tree\";i:2;s:27:\"basic.textbook_chapter.view\";i:3;s:27:\"basic.textbook_version.view\";i:4;s:9:\"equipment\";i:5;s:16:\"equipment.borrow\";i:6;s:23:\"equipment.borrow.create\";i:7;s:21:\"equipment.borrow.edit\";i:8;s:21:\"equipment.borrow.view\";i:9;s:14:\"equipment.list\";i:10;s:28:\"equipment.maintenance.create\";i:11;s:26:\"equipment.maintenance.view\";i:12;s:21:\"equipment.qrcode.view\";i:13;s:14:\"equipment.view\";i:14;s:10:\"experiment\";i:15;s:18:\"experiment.booking\";i:16;s:25:\"experiment.booking.create\";i:17;s:23:\"experiment.booking.edit\";i:18;s:23:\"experiment.booking.view\";i:19;s:18:\"experiment.catalog\";i:20;s:23:\"experiment.catalog.view\";i:21;s:17:\"experiment.record\";i:22;s:26:\"experiment.record.complete\";i:23;s:24:\"experiment.record.create\";i:24;s:22:\"experiment.record.edit\";i:25;s:22:\"experiment.record.view\";i:26;s:15:\"laboratory_type\";i:27;s:20:\"laboratory_type.list\";i:28;s:20:\"statistics.dashboard\";i:29;s:20:\"statistics.equipment\";i:30;s:21:\"statistics.experiment\";i:31;s:15:\"statistics.view\";}', 1754218994),
('laravel-cache-user_permissions_44', 'a:72:{i:0;s:41:\"basic.equipment_standard.check_compliance\";i:1;s:31:\"basic.equipment_standard.create\";i:2;s:31:\"basic.equipment_standard.delete\";i:3;s:29:\"basic.equipment_standard.edit\";i:4;s:29:\"basic.equipment_standard.view\";i:5;s:13:\"basic.subject\";i:6;s:20:\"basic.subject.create\";i:7;s:20:\"basic.subject.delete\";i:8;s:18:\"basic.subject.edit\";i:9;s:18:\"basic.subject.view\";i:10;s:22:\"basic.textbook_chapter\";i:11;s:29:\"basic.textbook_chapter.create\";i:12;s:29:\"basic.textbook_chapter.delete\";i:13;s:27:\"basic.textbook_chapter.edit\";i:14;s:27:\"basic.textbook_chapter.tree\";i:15;s:27:\"basic.textbook_chapter.view\";i:16;s:27:\"basic.textbook_version.sort\";i:17;s:27:\"basic.textbook_version.view\";i:18;s:9:\"equipment\";i:19;s:16:\"equipment.borrow\";i:20;s:16:\"equipment.create\";i:21;s:16:\"equipment.delete\";i:22;s:14:\"equipment.list\";i:23;s:21:\"equipment.maintenance\";i:24;s:16:\"equipment.update\";i:25;s:10:\"experiment\";i:26;s:18:\"experiment.booking\";i:27;s:18:\"experiment.catalog\";i:28;s:23:\"experiment.catalog.copy\";i:29;s:25:\"experiment.catalog.create\";i:30;s:25:\"experiment.catalog.delete\";i:31;s:23:\"experiment.catalog.edit\";i:32;s:31:\"experiment.catalog.manage_level\";i:33;s:23:\"experiment.catalog.view\";i:34;s:17:\"experiment.record\";i:35;s:8:\"log.read\";i:36;s:4:\"role\";i:37;s:11:\"role.create\";i:38;s:11:\"role.delete\";i:39;s:9:\"role.list\";i:40;s:11:\"role.update\";i:41;s:10:\"statistics\";i:42;s:20:\"statistics.dashboard\";i:43;s:20:\"statistics.equipment\";i:44;s:21:\"statistics.experiment\";i:45;s:17:\"statistics.export\";i:46;s:22:\"statistics.performance\";i:47;s:15:\"statistics.user\";i:48;s:15:\"statistics.view\";i:49;s:6:\"system\";i:50;s:11:\"system.read\";i:51;s:17:\"textbook_chapters\";i:52;s:24:\"textbook_chapters.create\";i:53;s:24:\"textbook_chapters.delete\";i:54;s:22:\"textbook_chapters.list\";i:55;s:22:\"textbook_chapters.tree\";i:56;s:24:\"textbook_chapters.update\";i:57;s:22:\"textbook_chapters.view\";i:58;s:17:\"textbook_versions\";i:59;s:24:\"textbook_versions.create\";i:60;s:24:\"textbook_versions.delete\";i:61;s:22:\"textbook_versions.list\";i:62;s:24:\"textbook_versions.update\";i:63;s:22:\"textbook_versions.view\";i:64;s:4:\"user\";i:65;s:11:\"user.create\";i:66;s:11:\"user.delete\";i:67;s:9:\"user.edit\";i:68;s:11:\"user.export\";i:69;s:9:\"user.list\";i:70;s:19:\"user.reset_password\";i:71;s:11:\"user.update\";}', 1754299536),
('laravel-cache-user_permissions_45', 'a:50:{i:0;s:29:\"basic.equipment_standard.view\";i:1;s:18:\"basic.subject.view\";i:2;s:27:\"basic.textbook_chapter.tree\";i:3;s:27:\"basic.textbook_chapter.view\";i:4;s:29:\"basic.textbook_version.create\";i:5;s:27:\"basic.textbook_version.sort\";i:6;s:27:\"basic.textbook_version.view\";i:7;s:9:\"equipment\";i:8;s:16:\"equipment.borrow\";i:9;s:16:\"equipment.create\";i:10;s:16:\"equipment.delete\";i:11;s:14:\"equipment.list\";i:12;s:21:\"equipment.maintenance\";i:13;s:16:\"equipment.update\";i:14;s:10:\"experiment\";i:15;s:18:\"experiment.booking\";i:16;s:18:\"experiment.catalog\";i:17;s:23:\"experiment.catalog.copy\";i:18;s:25:\"experiment.catalog.create\";i:19;s:25:\"experiment.catalog.delete\";i:20;s:23:\"experiment.catalog.edit\";i:21;s:31:\"experiment.catalog.manage_level\";i:22;s:23:\"experiment.catalog.view\";i:23;s:17:\"experiment.record\";i:24;s:8:\"log.read\";i:25;s:10:\"statistics\";i:26;s:20:\"statistics.dashboard\";i:27;s:20:\"statistics.equipment\";i:28;s:21:\"statistics.experiment\";i:29;s:17:\"statistics.export\";i:30;s:22:\"statistics.performance\";i:31;s:15:\"statistics.user\";i:32;s:15:\"statistics.view\";i:33;s:6:\"system\";i:34;s:11:\"system.read\";i:35;s:17:\"textbook_chapters\";i:36;s:22:\"textbook_chapters.list\";i:37;s:22:\"textbook_chapters.tree\";i:38;s:22:\"textbook_chapters.view\";i:39;s:17:\"textbook_versions\";i:40;s:22:\"textbook_versions.list\";i:41;s:22:\"textbook_versions.view\";i:42;s:4:\"user\";i:43;s:11:\"user.create\";i:44;s:11:\"user.delete\";i:45;s:9:\"user.edit\";i:46;s:11:\"user.export\";i:47;s:9:\"user.list\";i:48;s:19:\"user.reset_password\";i:49;s:11:\"user.update\";}', 1754299383),
('laravel-cache-user_permissions_95', 'a:32:{i:0;s:29:\"basic.equipment_standard.view\";i:1;s:27:\"basic.textbook_chapter.tree\";i:2;s:27:\"basic.textbook_chapter.view\";i:3;s:27:\"basic.textbook_version.view\";i:4;s:9:\"equipment\";i:5;s:16:\"equipment.borrow\";i:6;s:23:\"equipment.borrow.create\";i:7;s:21:\"equipment.borrow.edit\";i:8;s:21:\"equipment.borrow.view\";i:9;s:14:\"equipment.list\";i:10;s:28:\"equipment.maintenance.create\";i:11;s:26:\"equipment.maintenance.view\";i:12;s:21:\"equipment.qrcode.view\";i:13;s:14:\"equipment.view\";i:14;s:10:\"experiment\";i:15;s:18:\"experiment.booking\";i:16;s:25:\"experiment.booking.create\";i:17;s:23:\"experiment.booking.edit\";i:18;s:23:\"experiment.booking.view\";i:19;s:18:\"experiment.catalog\";i:20;s:23:\"experiment.catalog.view\";i:21;s:17:\"experiment.record\";i:22;s:26:\"experiment.record.complete\";i:23;s:24:\"experiment.record.create\";i:24;s:22:\"experiment.record.edit\";i:25;s:22:\"experiment.record.view\";i:26;s:15:\"laboratory_type\";i:27;s:20:\"laboratory_type.list\";i:28;s:20:\"statistics.dashboard\";i:29;s:20:\"statistics.equipment\";i:30;s:21:\"statistics.experiment\";i:31;s:15:\"statistics.view\";}', 1754299300);

-- --------------------------------------------------------

--
-- 表的结构 `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `equipments`
--

CREATE TABLE `equipments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `laboratory_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '实验室ID',
  `category_id` bigint(20) UNSIGNED NOT NULL COMMENT '分类ID',
  `name` varchar(200) NOT NULL COMMENT '设备名称',
  `code` varchar(100) DEFAULT NULL COMMENT '设备编号',
  `model` varchar(100) DEFAULT NULL COMMENT '型号规格',
  `brand` varchar(100) DEFAULT NULL COMMENT '品牌',
  `supplier` varchar(200) DEFAULT NULL COMMENT '供应商',
  `supplier_phone` varchar(20) DEFAULT NULL COMMENT '供应商电话',
  `purchase_date` date DEFAULT NULL COMMENT '购入日期',
  `purchase_price` decimal(10,2) DEFAULT NULL COMMENT '购入价格',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '数量',
  `unit` varchar(20) NOT NULL DEFAULT '台' COMMENT '单位',
  `warranty_period` int(11) DEFAULT NULL COMMENT '保修期(月)',
  `service_life` int(11) DEFAULT NULL COMMENT '使用年限(年)',
  `funding_source` varchar(100) DEFAULT NULL COMMENT '经费来源',
  `storage_location` varchar(200) DEFAULT NULL COMMENT '存放位置',
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '保管人ID',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1正常 2维修 3报废 0停用',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `remark` text DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipments`
--

INSERT INTO `equipments` (`id`, `school_id`, `laboratory_id`, `category_id`, `name`, `code`, `model`, `brand`, `supplier`, `supplier_phone`, `purchase_date`, `purchase_price`, `quantity`, `unit`, `warranty_period`, `service_life`, `funding_source`, `storage_location`, `manager_id`, `status`, `qr_code`, `remark`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3, '生物显微镜XSP-2CA', 'BIO0010001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2024-12-27', 1500.00, 30, '台', 24, 10, '教育专项资金', '设备室', 5, 1, '/storage/qrcodes/equipment_1_1752880900.png', '系统初始化数据', '2025-07-18 15:21:40', '2025-07-18 15:21:40'),
(2, 2, NULL, 3, '生物显微镜XSP-2CA', 'BIO0020001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2025-02-16', 1500.00, 30, '台', 24, 10, '教育专项资金', '设备室', 25, 1, '/storage/qrcodes/equipment_2_1752880900.png', '系统初始化数据', '2025-07-18 15:21:40', '2025-07-18 15:21:40'),
(3, 3, NULL, 3, '生物显微镜XSP-2CA', 'BIO0030001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2024-09-21', 1500.00, 30, '台', 24, 10, '教育专项资金', '设备室', 13, 1, '/storage/qrcodes/equipment_3_1752880900.png', '系统初始化数据', '2025-07-18 15:21:40', '2025-07-18 15:21:40'),
(4, 1, NULL, 3, '学生用生物显微镜', 'BIO00110002', 'XSP-1C', '上海光学', '上海教学设备公司', '021-87654321', '2024-09-04', 800.00, 50, '台', 12, 8, '学校自筹', '设备室', 20, 1, '/storage/qrcodes/equipment_4_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(5, 18, NULL, 3, '学生用生物显微镜', 'BIO00220002', 'XSP-1C', '上海光学', '上海教学设备公司', '021-87654321', '2024-11-02', 800.00, 50, '台', 12, 8, '学校自筹', '设备室', 38, 1, '/storage/qrcodes/equipment_5_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(6, 19, NULL, 3, '学生用生物显微镜', 'BIO00330002', 'XSP-1C', '上海光学', '上海教学设备公司', '021-87654321', '2024-08-15', 800.00, 50, '台', 12, 8, '学校自筹', '设备室', 13, 1, '/storage/qrcodes/equipment_6_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(7, 1, NULL, 19, '电子天平', 'BAL0010001', 'FA2004', '上海精科', '上海精密仪器公司', '021-11111111', '2025-04-15', 3200.00, 5, '台', 36, 15, '实验室建设专项', '设备室', 4, 1, '/storage/qrcodes/equipment_7_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(8, 2, NULL, 19, '电子天平', 'BAL0020001', 'FA2004', '上海精科', '上海精密仪器公司', '021-11111111', '2024-11-12', 3200.00, 5, '台', 36, 15, '实验室建设专项', '设备室', 40, 1, '/storage/qrcodes/equipment_8_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(9, 3, NULL, 19, '电子天平', 'BAL0030001', 'FA2004', '上海精科', '上海精密仪器公司', '021-11111111', '2024-11-04', 3200.00, 5, '台', 36, 15, '实验室建设专项', '设备室', 31, 1, '/storage/qrcodes/equipment_9_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(10, 1, NULL, 19, '分析天平', 'BAL00110002', 'FA1004', '梅特勒', '北京梅特勒代理商', '010-22222222', '2024-11-14', 8500.00, 2, '台', 24, 12, '教育部专项', '设备室', 33, 1, '/storage/qrcodes/equipment_10_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(11, 2, NULL, 19, '分析天平', 'BAL00220002', 'FA1004', '梅特勒', '北京梅特勒代理商', '010-22222222', '2024-12-31', 8500.00, 2, '台', 24, 12, '教育部专项', '设备室', 11, 1, '/storage/qrcodes/equipment_11_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(12, 3, NULL, 19, '分析天平', 'BAL00330002', 'FA1004', '梅特勒', '北京梅特勒代理商', '010-22222222', '2025-01-19', 8500.00, 2, '台', 24, 12, '教育部专项', '设备室', 33, 1, '/storage/qrcodes/equipment_12_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(13, 1, NULL, 31, '数字万用表', 'MUL0010001', 'UT33D', '优利德', '深圳优利德公司', '0755-33333333', '2024-11-20', 120.00, 40, '台', 12, 5, '学校自筹', '设备室', 22, 1, '/storage/qrcodes/equipment_13_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(14, 2, NULL, 31, '数字万用表', 'MUL0020001', 'UT33D', '优利德', '深圳优利德公司', '0755-33333333', '2025-03-20', 120.00, 40, '台', 12, 5, '学校自筹', '设备室', 14, 1, '/storage/qrcodes/equipment_14_1752880901.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:41'),
(15, 3, NULL, 31, '数字万用表', 'MUL0030001', 'UT33D', '优利德', '深圳优利德公司', '0755-33333333', '2024-10-16', 120.00, 40, '台', 12, 5, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_15_1752880902.png', '系统初始化数据', '2025-07-18 15:21:41', '2025-07-18 15:21:42'),
(16, 1, NULL, 37, '玻璃烧杯100ml', 'BEA0010001', 'GB-100', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-03-19', 8.50, 200, '个', 6, 3, '实验耗材专项', '设备室', 28, 1, '/storage/qrcodes/equipment_16_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(17, 2, NULL, 37, '玻璃烧杯100ml', 'BEA0020001', 'GB-100', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-02-10', 8.50, 200, '个', 6, 3, '实验耗材专项', '设备室', 7, 1, '/storage/qrcodes/equipment_17_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(18, 3, NULL, 37, '玻璃烧杯100ml', 'BEA0030001', 'GB-100', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2024-07-24', 8.50, 200, '个', 6, 3, '实验耗材专项', '设备室', 5, 1, '/storage/qrcodes/equipment_18_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(19, 21, NULL, 37, '玻璃烧杯250ml', 'BEA00110002', 'GB-250', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2024-12-22', 12.00, 150, '个', 6, 3, '实验耗材专项', '设备室', 3, 1, '/storage/qrcodes/equipment_19_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(20, 20, NULL, 37, '玻璃烧杯250ml', 'BEA00220002', 'GB-250', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-02-19', 12.00, 150, '个', 6, 3, '实验耗材专项', '设备室', 38, 1, '/storage/qrcodes/equipment_20_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(21, 19, NULL, 37, '玻璃烧杯250ml', 'BEA00330002', 'GB-250', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2024-08-03', 12.00, 150, '个', 6, 3, '实验耗材专项', '设备室', 37, 1, '/storage/qrcodes/equipment_21_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(22, 18, NULL, 47, '解剖刀套装', 'SCA0010001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2024-07-24', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 6, 1, '/storage/qrcodes/equipment_22_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(23, 21, NULL, 47, '解剖刀套装', 'SCA0020001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2025-04-02', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 32, 1, '/storage/qrcodes/equipment_23_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(24, 20, NULL, 47, '解剖刀套装', 'SCA0030001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2024-11-29', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 29, 1, '/storage/qrcodes/equipment_24_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42'),
(25, 18, NULL, 3, '生物显微镜XSP-2CA', 'BIO0180001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', NULL, '2024-12-27', 1500.00, 1, '台', 24, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-23 02:40:53', '2025-07-23 02:40:53'),
(26, 18, NULL, 3, '学生用生物显微镜', 'BIO0180002', 'XSP-1C', '上海光学', '上海教学设备公司', NULL, '2024-09-04', 800.00, 1, '台', 12, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-23 02:43:24', '2025-07-23 02:43:24'),
(27, 18, NULL, 19, '电子天平', 'BAL0180001', 'FA2004', '上海精科', '上海精密仪器公司', NULL, '2025-04-15', 3200.00, 1, '台', 36, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-23 02:50:21', '2025-07-23 02:50:21'),
(28, 1, NULL, 54, '显微镜', 'EQ_001', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 119.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:39', '2025-07-24 01:14:39'),
(29, 1, NULL, 54, '天平', 'EQ_002', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 331.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:39', '2025-07-24 01:14:39'),
(30, 1, NULL, 54, '量筒', 'EQ_003', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 469.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:39', '2025-07-24 01:14:39'),
(31, 1, NULL, 54, '试管', 'EQ_004', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 351.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:39', '2025-07-24 01:14:39'),
(32, 1, NULL, 54, '烧杯', 'EQ_005', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 195.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:39', '2025-07-24 01:14:39'),
(33, 1, NULL, 54, '酒精灯', 'EQ_006', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 195.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:40', '2025-07-24 01:14:40'),
(34, 1, NULL, 54, '温度计', 'EQ_007', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 199.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:40', '2025-07-24 01:14:40'),
(35, 1, NULL, 54, '放大镜', 'EQ_008', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 437.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:40', '2025-07-24 01:14:40'),
(36, 1, NULL, 54, '磁铁', 'EQ_009', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 429.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:40', '2025-07-24 01:14:40'),
(37, 1, NULL, 54, '指南针', 'EQ_010', 'Standard', '教学设备厂', NULL, NULL, '2025-01-24', 396.00, 1, '台', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-24 01:14:40', '2025-07-24 01:14:40'),
(38, 4, NULL, 1, '生物显微镜XSP-2CA', 'BIO0410001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2024-01-01', 2500.00, 10, '台', 24, 10, '教育专项资金', '生物实验室', NULL, 1, NULL, '任课教师测试数据', '2025-08-03 02:06:38', '2025-08-03 02:06:38'),
(39, 4, NULL, 2, '电子天平', 'BAL0410001', 'FA2004', '上海精科', '上海精密仪器公司', '021-12345678', '2024-01-01', 1800.00, 5, '台', 24, 8, '教育专项资金', '化学实验室', NULL, 1, NULL, '任课教师测试数据', '2025-08-03 02:06:38', '2025-08-03 02:06:38'),
(40, 4, NULL, 3, '试管', 'TUB0410001', '标准型', '通用', '实验器材公司', '010-87654321', '2024-01-01', 2.50, 100, '支', 12, 5, '学校自筹', '化学实验室', NULL, 1, NULL, '任课教师测试数据', '2025-08-03 02:06:38', '2025-08-03 02:06:38'),
(41, 4, NULL, 4, '烧杯', 'BEA0410001', '250ml', '通用', '实验器材公司', '010-87654321', '2024-01-01', 5.00, 50, '个', 12, 5, '学校自筹', '化学实验室', NULL, 1, NULL, '任课教师测试数据', '2025-08-03 02:06:38', '2025-08-03 02:06:38'),
(42, 4, NULL, 5, '酒精灯', 'ALC0410001', '标准型', '通用', '实验器材公司', '010-87654321', '2024-01-01', 15.00, 20, '个', 12, 3, '学校自筹', '化学实验室', NULL, 1, NULL, '任课教师测试数据', '2025-08-03 02:06:38', '2025-08-03 02:06:38'),
(43, 4, NULL, 1, '测试设备-20250804064354', 'TEST20250804064354', 'TEST-MODEL-001', '测试品牌', '测试供应商', '010-12345678', '2024-01-01', 1000.00, 1, '台', 24, 10, '教育专项资金', '测试位置', NULL, 1, NULL, '测试设备备注', '2025-08-03 22:43:54', '2025-08-03 22:43:54'),
(44, 15, NULL, 2, '奇无可奈何花落去', '012220', '在在00', '夺在', '在', NULL, NULL, NULL, 2, '台', NULL, NULL, NULL, '在', NULL, 1, NULL, '夺', '2025-08-03 22:51:24', '2025-08-03 22:51:24'),
(45, 4, NULL, 1, '批量导入测试设备1', 'BATCH001', 'TEST-MODEL-001', '测试品牌', '测试供应商', '010-12345678', '2024-01-01', 1000.00, 2, '台', 24, 10, '教育专项资金', '测试位置1', NULL, 1, NULL, '批量导入测试设备1', '2025-08-03 23:11:02', '2025-08-03 23:11:02'),
(46, 4, NULL, 1, '批量导入测试设备2', 'BATCH002', 'TEST-MODEL-002', '测试品牌', '测试供应商', '010-12345678', '2024-01-01', 1500.00, 3, '个', 12, 8, '学校自筹', '测试位置2', NULL, 1, NULL, '批量导入测试设备2', '2025-08-03 23:11:02', '2025-08-03 23:11:02'),
(60, 4, NULL, 1, '示例设备1', 'EQ001', 'Model-001', '示例品牌1', '示例供应商1', '', '2024-01-01', 10000.00, 1, '台', 12, 0, '', '实验室101', NULL, 1, NULL, '设备描述信息', '2025-08-03 23:58:23', '2025-08-03 23:58:23'),
(61, 15, NULL, 1, '示例设备2', 'EQ002', 'Model-002', '示例品牌2', '示例供应商2', NULL, '2024-01-02', 10001.00, 1, '台', 12, NULL, NULL, '实验室102', NULL, 1, NULL, '设备描述信息2', '2025-08-03 23:59:28', '2025-08-03 23:59:28'),
(62, 15, NULL, 1, '示例设备3', 'EQ003', 'Model-003', '示例品牌3', '示例供应商3', NULL, '2024-01-03', 10002.00, 1, '台', 12, NULL, NULL, '实验室103', NULL, 1, NULL, '设备描述信息3', '2025-08-03 23:59:28', '2025-08-03 23:59:28'),
(63, 15, NULL, 1, '示例设备4', 'EQ004', 'Model-004', '示例品牌4', '示例供应商4', NULL, '2024-01-04', 10003.00, 1, '台', 12, NULL, NULL, '实验室104', NULL, 1, NULL, '设备描述信息4', '2025-08-03 23:59:28', '2025-08-03 23:59:28'),
(64, 15, NULL, 1, '示例设备5', 'EQ005', 'Model-005', '示例品牌5', '示例供应商5', NULL, '2024-01-05', 10004.00, 1, '台', 12, NULL, NULL, '实验室105', NULL, 1, NULL, '设备描述信息5', '2025-08-03 23:59:28', '2025-08-03 23:59:28'),
(65, 15, NULL, 1, '示例设备6', 'EQ006', 'Model-006', '示例品牌6', '示例供应商6', NULL, '2024-01-06', 10005.00, 1, '台', 12, NULL, NULL, '实验室106', NULL, 1, NULL, '设备描述信息6', '2025-08-03 23:59:28', '2025-08-03 23:59:28');

-- --------------------------------------------------------

--
-- 表的结构 `equipment_attachments`
--

CREATE TABLE `equipment_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL COMMENT '文件名',
  `original_name` varchar(255) NOT NULL COMMENT '原始文件名',
  `file_path` varchar(255) NOT NULL COMMENT '文件路径',
  `file_type` varchar(50) NOT NULL COMMENT '文件类型：image,document,video,audio,other',
  `mime_type` varchar(100) NOT NULL COMMENT 'MIME类型',
  `file_size` bigint(20) NOT NULL COMMENT '文件大小(字节)',
  `file_extension` varchar(10) NOT NULL COMMENT '文件扩展名',
  `attachment_type` varchar(50) NOT NULL COMMENT '附件类型：photo,manual,certificate,other',
  `description` text DEFAULT NULL COMMENT '文件描述',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否主图片',
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `equipment_borrows`
--

CREATE TABLE `equipment_borrows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL COMMENT '设备ID',
  `reservation_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '实验预约ID',
  `borrower_id` bigint(20) UNSIGNED NOT NULL COMMENT '借用人ID',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '借用数量',
  `actual_quantity` int(11) DEFAULT NULL COMMENT '实际借用数量',
  `borrow_date` date NOT NULL COMMENT '借用日期',
  `expected_return_date` date NOT NULL COMMENT '预期归还日期',
  `actual_return_date` date DEFAULT NULL COMMENT '实际归还日期',
  `purpose` text DEFAULT NULL COMMENT '借用目的',
  `remark` text DEFAULT NULL COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1借用中 2已归还 3逾期 4损坏',
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '审批人ID',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT '审批时间',
  `approval_remark` text DEFAULT NULL COMMENT '审批备注',
  `condition_before` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '借用前状态' CHECK (json_valid(`condition_before`)),
  `condition_after` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '归还后状态' CHECK (json_valid(`condition_after`)),
  `has_damage` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否有损坏',
  `damage_description` text DEFAULT NULL COMMENT '损坏描述',
  `damage_cost` decimal(10,2) DEFAULT NULL COMMENT '损坏赔偿费用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipment_borrows`
--

INSERT INTO `equipment_borrows` (`id`, `equipment_id`, `reservation_id`, `borrower_id`, `quantity`, `actual_quantity`, `borrow_date`, `expected_return_date`, `actual_return_date`, `purpose`, `remark`, `status`, `approver_id`, `approved_at`, `approval_remark`, `condition_before`, `condition_after`, `has_damage`, `damage_description`, `damage_cost`, `created_at`, `updated_at`) VALUES
(1, 15, NULL, 21, 3, NULL, '2025-07-17', '2025-07-30', NULL, '实验技能竞赛准备', '使用完毕请断电并整理', 5, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(2, 10, NULL, 7, 2, NULL, '2025-07-15', '2025-08-02', NULL, '实验技能竞赛准备', '实验过程中如有问题请联系管理员', 5, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(3, 16, NULL, 36, 3, NULL, '2025-07-12', '2025-07-27', NULL, '学生课外实验活动', '请妥善保管，按时归还', 5, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(4, 14, NULL, 19, 2, NULL, '2025-07-14', '2025-08-08', NULL, '教学研究实验', '', 5, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(5, 7, NULL, 20, 2, NULL, '2025-07-15', '2025-07-26', NULL, '科普展示活动', '如有损坏请及时报告', 5, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(6, 21, NULL, 11, 2, NULL, '2025-07-08', '2025-08-07', NULL, '设备功能测试', '实验过程中如有问题请联系管理员', 1, 25, '2025-07-09 02:21:52', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(7, 12, NULL, 12, 1, NULL, '2025-07-06', '2025-07-23', NULL, '实验方法研究', '感谢配合实验室管理工作', 1, 4, '2025-07-06 16:21:52', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(8, 19, NULL, 38, 2, NULL, '2025-07-06', '2025-07-26', NULL, '学生课外实验活动', '借用期间请勿转借他人', 1, 10, '2025-07-07 02:21:52', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(9, 9, NULL, 11, 1, NULL, '2025-07-11', '2025-07-31', NULL, '实验技能竞赛准备', '注意安全操作', 1, 37, '2025-07-12 08:21:52', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(10, 6, NULL, 14, 1, NULL, '2025-07-07', '2025-08-02', NULL, '科普展示活动', '归还时请检查设备完整性', 1, 19, '2025-07-07 16:21:52', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(11, 19, NULL, 39, 2, NULL, '2025-07-09', '2025-08-04', NULL, '教师培训使用', '注意安全操作', 1, 34, '2025-07-10 06:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(12, 5, NULL, 32, 2, NULL, '2025-07-09', '2025-07-23', NULL, '学生课外实验活动', '', 1, 14, '2025-07-10 04:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(13, 19, NULL, 32, 1, NULL, '2025-07-11', '2025-08-01', NULL, '实验方法研究', '借用期间请勿转借他人', 1, 31, '2025-07-12 00:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(14, 21, NULL, 6, 1, NULL, '2025-06-29', '2025-07-23', '2025-07-22', '实验室开放日活动', '实验结束后及时清洁设备', 2, 40, '2025-06-30 02:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(15, 10, NULL, 15, 1, NULL, '2025-07-01', '2025-07-22', '2025-07-20', '科学兴趣小组活动', '归还时请检查设备完整性', 2, 25, '2025-07-02 07:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(16, 4, NULL, 33, 3, NULL, '2025-06-29', '2025-07-20', '2025-07-19', '科学兴趣小组活动', '', 2, 13, '2025-06-30 07:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(17, 22, NULL, 39, 3, NULL, '2025-06-29', '2025-07-29', '2025-07-27', '学生毕业设计实验', '实验过程中如有问题请联系管理员', 2, 10, '2025-06-30 14:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(18, 14, NULL, 20, 1, NULL, '2025-06-28', '2025-07-26', '2025-07-26', '教学研究实验', '感谢配合实验室管理工作', 2, 25, '2025-06-29 12:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(19, 1, NULL, 22, 3, NULL, '2025-07-01', '2025-07-08', '2025-07-05', '科学兴趣小组活动', '请妥善保管，按时归还', 2, 28, '2025-07-02 03:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(20, 19, NULL, 31, 3, NULL, '2025-06-28', '2025-07-25', '2025-07-25', '实验技能竞赛准备', '使用完毕请断电并整理', 2, 7, '2025-06-28 20:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(21, 23, NULL, 33, 3, NULL, '2025-07-02', '2025-07-27', '2025-07-25', '实验室开放日活动', '', 2, 34, '2025-07-02 22:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(22, 8, NULL, 37, 3, NULL, '2025-06-30', '2025-07-28', '2025-07-25', '学生毕业设计实验', '', 2, 41, '2025-07-01 03:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(23, 24, NULL, 20, 2, NULL, '2025-06-29', '2025-07-27', '2025-07-25', '设备功能测试', '', 2, 3, '2025-06-30 13:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(24, 4, NULL, 3, 1, NULL, '2025-06-28', '2025-07-26', '2025-07-26', '生物实验课教学使用', '如有损坏请及时报告', 2, 23, '2025-06-29 10:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(25, 24, NULL, 26, 3, NULL, '2025-06-30', '2025-07-17', '2025-07-16', '学生课外实验活动', '', 2, 33, '2025-07-01 09:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(26, 18, NULL, 18, 3, NULL, '2025-06-25', '2025-07-12', NULL, '化学实验课教学使用', '归还时请检查设备完整性', 3, 8, '2025-06-25 20:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(27, 3, NULL, 14, 2, NULL, '2025-06-26', '2025-07-15', NULL, '实验方法研究', '如有损坏请及时报告', 3, 28, '2025-06-26 20:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(28, 7, NULL, 22, 1, NULL, '2025-06-25', '2025-07-12', NULL, '科普展示活动', '', 3, 20, '2025-06-26 13:21:53', '审批通过', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(29, 2, NULL, 23, 2, NULL, '2025-07-10', '2025-07-23', NULL, '设备功能测试', '申请借用', 6, 24, '2025-07-11 14:21:53', '设备维修中，暂不可借用', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(30, 13, NULL, 3, 2, NULL, '2025-07-13', '2025-07-25', NULL, '设备功能测试', '申请借用', 6, 21, '2025-07-13 19:21:53', '设备维修中，暂不可借用', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(31, 16, NULL, 40, 1, NULL, '2025-07-11', '2025-07-24', NULL, '教师培训使用', '申请借用', 6, 12, '2025-07-12 19:21:53', '设备维修中，暂不可借用', NULL, NULL, 0, NULL, NULL, '2025-07-18 15:21:53', '2025-07-18 15:21:53');

-- --------------------------------------------------------

--
-- 表的结构 `equipment_categories`
--

CREATE TABLE `equipment_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '分类名称',
  `code` varchar(50) NOT NULL COMMENT '分类代码',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '父级分类ID',
  `level` tinyint(4) NOT NULL COMMENT '分类级别',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipment_categories`
--

INSERT INTO `equipment_categories` (`id`, `name`, `code`, `parent_id`, `level`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, '光学仪器', 'OPTICAL', NULL, 1, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(2, '显微镜', 'MICROSCOPE', 1, 2, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(3, '生物显微镜', 'BIO_MICROSCOPE', 2, 3, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(4, '体视显微镜', 'STEREO_MICROSCOPE', 2, 3, 2, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(5, '电子显微镜', 'ELECTRON_MICROSCOPE', 2, 3, 3, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(6, '望远镜', 'TELESCOPE', 1, 2, 2, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(7, '天文望远镜', 'ASTRO_TELESCOPE', 6, 3, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(8, '地理望远镜', 'GEO_TELESCOPE', 6, 3, 2, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(9, '放大镜', 'MAGNIFIER', 1, 2, 3, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(10, '手持放大镜', 'HAND_MAGNIFIER', 9, 3, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(11, '台式放大镜', 'DESK_MAGNIFIER', 9, 3, 2, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(12, '测量仪器', 'MEASUREMENT', NULL, 1, 2, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(13, '长度测量', 'LENGTH_MEASURE', 12, 2, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(14, '直尺', 'RULER', 13, 3, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(15, '卷尺', 'TAPE_MEASURE', 13, 3, 2, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(16, '游标卡尺', 'VERNIER_CALIPER', 13, 3, 3, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(17, '螺旋测微器', 'MICROMETER', 13, 3, 4, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(18, '质量测量', 'MASS_MEASURE', 12, 2, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(19, '天平', 'BALANCE', 18, 3, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(20, '电子秤', 'ELECTRONIC_SCALE', 18, 3, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(21, '弹簧秤', 'SPRING_SCALE', 18, 3, 3, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(22, '时间测量', 'TIME_MEASURE', 12, 2, 3, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(23, '秒表', 'STOPWATCH', 22, 3, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(24, '计时器', 'TIMER', 22, 3, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(25, '电学仪器', 'ELECTRICAL', NULL, 1, 3, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(26, '电源设备', 'POWER_SUPPLY', 25, 2, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(27, '直流电源', 'DC_POWER', 26, 3, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(28, '交流电源', 'AC_POWER', 26, 3, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(29, '稳压电源', 'REGULATED_POWER', 26, 3, 3, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(30, '测量仪表', 'ELECTRICAL_METER', 25, 2, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(31, '万用表', 'MULTIMETER', 30, 3, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(32, '电压表', 'VOLTMETER', 30, 3, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(33, '电流表', 'AMMETER', 30, 3, 3, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(34, '示波器', 'OSCILLOSCOPE', 30, 3, 4, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(35, '化学仪器', 'CHEMICAL', NULL, 1, 4, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(36, '玻璃仪器', 'GLASSWARE', 35, 2, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(37, '烧杯', 'BEAKER', 36, 3, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(38, '试管', 'TEST_TUBE', 36, 3, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(39, '量筒', 'GRADUATED_CYLINDER', 36, 3, 3, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(40, '容量瓶', 'VOLUMETRIC_FLASK', 36, 3, 4, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(41, '加热设备', 'HEATING_EQUIPMENT', 35, 2, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(42, '酒精灯', 'ALCOHOL_LAMP', 41, 3, 1, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(43, '电热板', 'HOT_PLATE', 41, 3, 2, 1, '2025-07-18 15:17:57', '2025-07-18 15:17:57'),
(44, '马弗炉', 'MUFFLE_FURNACE', 41, 3, 3, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(45, '生物仪器', 'BIOLOGICAL', NULL, 1, 5, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(46, '解剖工具', 'DISSECTION_TOOLS', 45, 2, 1, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(47, '解剖刀', 'SCALPEL', 46, 3, 1, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(48, '解剖剪', 'DISSECTION_SCISSORS', 46, 3, 2, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(49, '镊子', 'FORCEPS', 46, 3, 3, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(50, '培养设备', 'CULTURE_EQUIPMENT', 45, 2, 2, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(51, '培养皿', 'PETRI_DISH', 50, 3, 1, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(52, '培养箱', 'INCUBATOR', 50, 3, 2, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(53, '接种环', 'INOCULATION_LOOP', 50, 3, 3, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58'),
(54, '实验器材', 'EXPERIMENT', NULL, 1, 0, 1, '2025-07-24 01:12:47', '2025-07-24 01:12:47');

-- --------------------------------------------------------

--
-- 表的结构 `equipment_maintenances`
--

CREATE TABLE `equipment_maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL COMMENT '设备ID',
  `reporter_id` bigint(20) UNSIGNED NOT NULL COMMENT '报修人ID',
  `fault_description` text NOT NULL COMMENT '故障描述',
  `fault_type` varchar(50) DEFAULT NULL COMMENT '故障类型',
  `urgency_level` tinyint(4) NOT NULL DEFAULT 2 COMMENT '紧急程度：1低 2中 3高',
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '故障照片' CHECK (json_valid(`photos`)),
  `report_date` date NOT NULL COMMENT '报修日期',
  `maintainer_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '维修人ID',
  `start_date` date DEFAULT NULL COMMENT '开始维修日期',
  `complete_date` date DEFAULT NULL COMMENT '完成维修日期',
  `cost` decimal(10,2) DEFAULT NULL COMMENT '维修费用',
  `solution` text DEFAULT NULL COMMENT '解决方案',
  `parts_replaced` text DEFAULT NULL COMMENT '更换部件',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1待维修 2维修中 3已完成 4无法修复',
  `quality_rating` tinyint(4) DEFAULT NULL COMMENT '维修质量评分(1-5)',
  `remark` text DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipment_maintenances`
--

INSERT INTO `equipment_maintenances` (`id`, `equipment_id`, `reporter_id`, `fault_description`, `fault_type`, `urgency_level`, `photos`, `report_date`, `maintainer_id`, `start_date`, `complete_date`, `cost`, `solution`, `parts_replaced`, `status`, `quality_rating`, `remark`, `created_at`, `updated_at`) VALUES
(1, 16, 11, '设备频繁死机，需要重启才能使用', '连接故障', 3, NULL, '2025-07-14', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '建议更新使用手册和操作指南', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(2, 18, 39, '显示屏出现花屏现象，图像不清晰', '控制系统故障', 2, NULL, '2025-07-13', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(3, 16, 31, '校准失效，需要重新校准', '过载故障', 2, NULL, '2025-07-13', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '建议定期检查相关部件', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(4, 21, 16, '设备无法正常启动，按下电源键无反应', '操作系统故障', 2, NULL, '2025-07-16', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(5, 24, 7, '精度下降，测量结果不准确', '机械故障', 1, NULL, '2025-07-14', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(6, 11, 30, '电源指示灯不亮，疑似电源故障', '精度问题', 1, NULL, '2025-07-14', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(7, 24, 38, '校准失效，需要重新校准', '传感器故障', 1, NULL, '2025-07-10', 29, '2025-07-12', NULL, NULL, NULL, NULL, 2, NULL, '需要对使用人员进行培训', '2025-07-18 15:22:02', '2025-07-18 15:22:02'),
(8, 21, 5, '连接线路松动，信号传输不稳定', '传感器故障', 1, NULL, '2025-07-10', 40, '2025-07-12', NULL, NULL, NULL, NULL, 2, NULL, '', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(9, 10, 14, '电源指示灯不亮，疑似电源故障', '显示故障', 1, NULL, '2025-07-08', 24, '2025-07-09', NULL, NULL, NULL, NULL, 2, NULL, '设备使用频率较高，建议加强日常维护', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(10, 14, 40, '机械部件卡死，无法正常转动', '电子元件故障', 3, NULL, '2025-07-13', 31, '2025-07-15', NULL, NULL, NULL, NULL, 2, NULL, '', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(11, 10, 27, '机械部件卡死，无法正常转动', '电源故障', 2, NULL, '2025-07-03', 10, '2025-07-04', '2025-07-06', 365.00, '更换控制电路板', '', 3, 5, '故障原因已查明并解决', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(12, 15, 6, '精度下降，测量结果不准确', '控制系统故障', 2, NULL, '2025-06-30', 8, '2025-07-03', '2025-07-10', 383.00, '更换损坏的传感器，重新连接线路', '电路板', 3, 4, '建议更新使用手册和操作指南', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(13, 2, 22, '电源指示灯不亮，疑似电源故障', '电源故障', 2, NULL, '2025-07-03', 9, '2025-07-05', '2025-07-11', 317.00, '调整温度控制系统，更换温控元件', '', 3, 5, '使用过程中请注意操作规范', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(14, 5, 29, '软件系统出错，界面显示异常', '操作系统故障', 1, NULL, '2025-07-03', 4, '2025-07-06', '2025-07-07', 52.00, '加强散热系统，清理散热通道', '电源适配器', 3, 5, '使用过程中请注意操作规范', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(15, 7, 19, '机械部件卡死，无法正常转动', '传感器故障', 3, NULL, '2025-06-30', 27, '2025-07-03', '2025-07-10', 314.00, '加强散热系统，清理散热通道', '电机', 3, 4, '故障原因已查明并解决', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(16, 18, 14, '按键失灵，部分功能无法操作', '外部损坏', 2, NULL, '2025-06-29', 10, '2025-06-30', '2025-07-03', 168.00, '重新设置系统参数，恢复出厂设置', '散热风扇', 3, 3, '需要对使用人员进行培训', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(17, 18, 29, '精度下降，测量结果不准确', '软件故障', 3, NULL, '2025-06-30', 17, '2025-07-01', '2025-07-06', 404.00, '重新校准设备，调整相关参数', '变压器', 3, 4, '', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(18, 4, 17, '设备过热，运行一段时间后自动关机', '过载故障', 3, NULL, '2025-07-03', 31, '2025-07-05', '2025-07-08', 38.00, '重新焊接松动的连接点', '电源适配器', 3, 3, '建议更新使用手册和操作指南', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(19, 5, 21, '校准失效，需要重新校准', '温度异常', 3, NULL, '2025-07-04', 6, '2025-07-05', '2025-07-10', NULL, '安全隐患严重，不适合继续使用', NULL, 4, NULL, '', '2025-07-18 15:22:03', '2025-07-18 15:22:03'),
(20, 4, 8, '按键失灵，部分功能无法操作', '电子元件故障', 2, NULL, '2025-07-06', 16, '2025-07-09', '2025-07-14', NULL, '设备主板严重损坏，无法修复，建议报废', NULL, 4, NULL, '', '2025-07-18 15:22:03', '2025-07-18 15:22:03');

-- --------------------------------------------------------

--
-- 表的结构 `equipment_operation_logs`
--

CREATE TABLE `equipment_operation_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `operation_type` varchar(50) NOT NULL COMMENT '操作类型：create,update,delete,borrow,return,maintenance,etc',
  `operation_module` varchar(50) NOT NULL COMMENT '操作模块：equipment,borrow,maintenance,qrcode',
  `operation_description` varchar(255) NOT NULL COMMENT '操作描述',
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '操作前数据' CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '操作后数据' CHECK (json_valid(`new_data`)),
  `ip_address` varchar(45) DEFAULT NULL COMMENT '操作IP地址',
  `user_agent` varchar(500) DEFAULT NULL COMMENT '用户代理',
  `extra_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '额外数据' CHECK (json_valid(`extra_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipment_operation_logs`
--

INSERT INTO `equipment_operation_logs` (`id`, `equipment_id`, `user_id`, `operation_type`, `operation_module`, `operation_description`, `old_data`, `new_data`, `ip_address`, `user_agent`, `extra_data`, `created_at`, `updated_at`) VALUES
(1, 44, 95, 'create', 'equipment', '创建设备', NULL, '{\"school_id\":15,\"category_id\":2,\"name\":\"\\u5947\\u65e0\\u53ef\\u5948\\u4f55\\u82b1\\u843d\\u53bb\",\"code\":\"012220\",\"model\":\"\\u5728\\u572800\",\"brand\":\"\\u593a\\u5728\",\"supplier\":\"\\u5728\",\"quantity\":2,\"unit\":\"\\u53f0\",\"storage_location\":\"\\u5728\",\"status\":1,\"remark\":\"\\u593a\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', NULL, '2025-08-03 22:51:24', '2025-08-03 22:51:24');

-- --------------------------------------------------------

--
-- 表的结构 `equipment_qrcodes`
--

CREATE TABLE `equipment_qrcodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `qr_code_url` varchar(255) NOT NULL COMMENT '二维码图片URL',
  `qr_code_content` varchar(1000) NOT NULL COMMENT '二维码内容',
  `qr_type` varchar(50) NOT NULL DEFAULT 'basic' COMMENT '二维码类型：basic,detailed,url',
  `qr_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '二维码生成选项' CHECK (json_valid(`qr_options`)),
  `size` int(11) NOT NULL DEFAULT 200 COMMENT '二维码尺寸',
  `format` varchar(10) NOT NULL DEFAULT 'png' COMMENT '图片格式',
  `download_count` int(11) NOT NULL DEFAULT 0 COMMENT '下载次数',
  `scan_count` int(11) NOT NULL DEFAULT 0 COMMENT '扫描次数',
  `last_scanned_at` timestamp NULL DEFAULT NULL COMMENT '最后扫描时间',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否有效',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `equipment_standards`
--

CREATE TABLE `equipment_standards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL COMMENT '标准名称',
  `code` varchar(100) NOT NULL COMMENT '标准代码',
  `authority_type` tinyint(4) NOT NULL COMMENT '制定机构：1教育部 2教育厅',
  `stage` tinyint(4) NOT NULL COMMENT '学段：1小学 2初中 3高中',
  `subject_code` varchar(50) NOT NULL COMMENT '学科代码',
  `subject_name` varchar(100) NOT NULL COMMENT '学科名称',
  `category_level_1` varchar(100) DEFAULT NULL COMMENT '一级分类',
  `category_level_2` varchar(100) DEFAULT NULL COMMENT '二级分类',
  `category_level_3` varchar(100) DEFAULT NULL COMMENT '三级分类',
  `description` text DEFAULT NULL COMMENT '标准描述',
  `equipment_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '设备清单JSON' CHECK (json_valid(`equipment_list`)),
  `estimated_unit_price` decimal(10,2) DEFAULT NULL COMMENT '预估单价（元）',
  `estimated_total_amount` decimal(12,2) DEFAULT NULL COMMENT '预估总金额（元）',
  `is_basic_standard` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否基本配备标准',
  `is_optional_standard` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否选配标准',
  `standard_reference` varchar(100) DEFAULT NULL COMMENT '执行标准代号',
  `implementation_notes` text DEFAULT NULL COMMENT '实施说明',
  `version` varchar(20) NOT NULL DEFAULT '1.0' COMMENT '版本号',
  `effective_date` date NOT NULL COMMENT '生效日期',
  `expiry_date` date DEFAULT NULL COMMENT '失效日期',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipment_standards`
--

INSERT INTO `equipment_standards` (`id`, `name`, `code`, `authority_type`, `stage`, `subject_code`, `subject_name`, `category_level_1`, `category_level_2`, `category_level_3`, `description`, `equipment_list`, `estimated_unit_price`, `estimated_total_amount`, `is_basic_standard`, `is_optional_standard`, `standard_reference`, `implementation_notes`, `version`, `effective_date`, `expiry_date`, `status`, `created_at`, `updated_at`) VALUES
(1, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023', 1, 1, 'SCIENCE', '科学', NULL, NULL, NULL, '根据教育部最新标准制定的小学科学教学仪器配备要求', '[{\"category\":\"\\u6d4b\\u91cf\\u5de5\\u5177\",\"items\":[{\"name\":\"\\u76f4\\u5c3a\",\"specification\":\"30cm\",\"quantity\":50,\"unit\":\"\\u628a\"},{\"name\":\"\\u5377\\u5c3a\",\"specification\":\"5m\",\"quantity\":5,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u5929\\u5e73\",\"specification\":\"\\u6258\\u76d8\\u5929\\u5e73500g\",\"quantity\":10,\"unit\":\"\\u53f0\"},{\"name\":\"\\u91cf\\u7b52\",\"specification\":\"100ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"}]},{\"category\":\"\\u89c2\\u5bdf\\u5de5\\u5177\",\"items\":[{\"name\":\"\\u653e\\u5927\\u955c\",\"specification\":\"5\\u500d\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u663e\\u5fae\\u955c\",\"specification\":\"\\u5b66\\u751f\\u7528\",\"quantity\":15,\"unit\":\"\\u53f0\"},{\"name\":\"\\u671b\\u8fdc\\u955c\",\"specification\":\"\\u53cc\\u7b52\",\"quantity\":5,\"unit\":\"\\u4e2a\"}]}]', NULL, NULL, 1, 0, NULL, NULL, '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42'),
(2, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023', 1, 2, 'PHYSICS', '物理', NULL, NULL, NULL, '根据教育部最新标准制定的初中物理教学仪器配备要求', '[{\"category\":\"\\u529b\\u5b66\\u5b9e\\u9a8c\\u5668\\u6750\",\"items\":[{\"name\":\"\\u5f39\\u7c27\\u6d4b\\u529b\\u8ba1\",\"specification\":\"5N\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u6ed1\\u8f6e\\u7ec4\",\"specification\":\"\\u6f14\\u793a\\u7528\",\"quantity\":5,\"unit\":\"\\u5957\"},{\"name\":\"\\u6760\\u6746\",\"specification\":\"\\u6f14\\u793a\\u7528\",\"quantity\":5,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u659c\\u9762\",\"specification\":\"\\u53ef\\u8c03\\u89d2\\u5ea6\",\"quantity\":10,\"unit\":\"\\u4e2a\"}]},{\"category\":\"\\u7535\\u5b66\\u5b9e\\u9a8c\\u5668\\u6750\",\"items\":[{\"name\":\"\\u7535\\u6d41\\u8868\",\"specification\":\"0-0.6A\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u7535\\u538b\\u8868\",\"specification\":\"0-3V\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u6ed1\\u52a8\\u53d8\\u963b\\u5668\",\"specification\":\"20\\u03a9 2A\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u7535\\u6e90\",\"specification\":\"\\u5b66\\u751f\\u7535\\u6e90\",\"quantity\":25,\"unit\":\"\\u53f0\"}]}]', NULL, NULL, 1, 0, NULL, NULL, '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42'),
(3, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023', 1, 2, 'CHEMISTRY', '化学', NULL, NULL, NULL, '根据教育部最新标准制定的初中化学教学仪器配备要求', '[{\"category\":\"\\u73bb\\u7483\\u4eea\\u5668\",\"items\":[{\"name\":\"\\u8bd5\\u7ba1\",\"specification\":\"18\\u00d7180mm\",\"quantity\":100,\"unit\":\"\\u652f\"},{\"name\":\"\\u70e7\\u676f\",\"specification\":\"250ml\",\"quantity\":50,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u9525\\u5f62\\u74f6\",\"specification\":\"250ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u91cf\\u7b52\",\"specification\":\"100ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"}]},{\"category\":\"\\u52a0\\u70ed\\u5668\\u6750\",\"items\":[{\"name\":\"\\u9152\\u7cbe\\u706f\",\"specification\":\"150ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u4e09\\u811a\\u67b6\",\"specification\":\"\\u94c1\\u5236\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u77f3\\u68c9\\u7f51\",\"specification\":\"\\u6807\\u51c6\",\"quantity\":50,\"unit\":\"\\u4e2a\"}]}]', NULL, NULL, 1, 0, NULL, NULL, '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42'),
(4, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023', 1, 2, 'BIOLOGY', '生物', NULL, NULL, NULL, '根据教育部最新标准制定的初中生物教学仪器配备要求', '[{\"category\":\"\\u89c2\\u5bdf\\u5668\\u6750\",\"items\":[{\"name\":\"\\u663e\\u5fae\\u955c\",\"specification\":\"\\u5b66\\u751f\\u7528\\u53cc\\u76ee\",\"quantity\":25,\"unit\":\"\\u53f0\"},{\"name\":\"\\u653e\\u5927\\u955c\",\"specification\":\"10\\u500d\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u89e3\\u5256\\u955c\",\"specification\":\"\\u53cc\\u76ee\\u7acb\\u4f53\",\"quantity\":5,\"unit\":\"\\u53f0\"}]},{\"category\":\"\\u6807\\u672c\\u6a21\\u578b\",\"items\":[{\"name\":\"\\u4eba\\u4f53\\u9aa8\\u9abc\\u6a21\\u578b\",\"specification\":\"85cm\",\"quantity\":1,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u5fc3\\u810f\\u6a21\\u578b\",\"specification\":\"\\u81ea\\u7136\\u5927\",\"quantity\":1,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u690d\\u7269\\u7ec6\\u80de\\u6a21\\u578b\",\"specification\":\"\\u653e\\u5927\",\"quantity\":1,\"unit\":\"\\u4e2a\"}]}]', NULL, NULL, 1, 0, NULL, NULL, '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_alerts`
--

CREATE TABLE `experiment_alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alert_type` enum('overdue','completion_rate','quality_score') NOT NULL COMMENT '预警类型',
  `target_type` enum('school','teacher','experiment','class') NOT NULL COMMENT '预警对象类型',
  `target_id` bigint(20) UNSIGNED NOT NULL COMMENT '预警对象ID',
  `target_name` varchar(200) NOT NULL COMMENT '预警对象名称',
  `alert_level` enum('low','medium','high','critical') NOT NULL COMMENT '预警级别',
  `alert_title` varchar(200) NOT NULL COMMENT '预警标题',
  `alert_message` text NOT NULL COMMENT '预警消息',
  `alert_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '预警相关数据' CHECK (json_valid(`alert_data`)),
  `alert_value` decimal(8,2) DEFAULT NULL COMMENT '预警数值',
  `threshold_value` decimal(8,2) DEFAULT NULL COMMENT '阈值',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已读',
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已解决',
  `resolve_note` text DEFAULT NULL COMMENT '解决说明',
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '解决人',
  `resolved_at` timestamp NULL DEFAULT NULL COMMENT '解决时间',
  `alert_time` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '预警时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_alerts`
--

INSERT INTO `experiment_alerts` (`id`, `alert_type`, `target_type`, `target_id`, `target_name`, `alert_level`, `alert_title`, `alert_message`, `alert_data`, `alert_value`, `threshold_value`, `is_read`, `is_resolved`, `resolve_note`, `resolved_by`, `resolved_at`, `alert_time`, `created_at`, `updated_at`) VALUES
(1, 'completion_rate', 'school', 1, '石家庄市藁城区实验小学', 'medium', '实验完成率过低', '学校「石家庄市藁城区实验小学」实验完成率为 75%，低于预警阈值 80%', '{\"semester\":\"2024-2025-1\",\"total_experiments\":60,\"completed_experiments\":45,\"overdue_experiments\":5}', 75.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-25 17:28:16', '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(2, 'overdue', 'experiment', 89, '物理实验：测量物体的密度', 'high', '实验超期未开', '实验「测量物体的密度」已超期 5 天未开课', '{\"school_id\":1,\"school_name\":\"\\u77f3\\u5bb6\\u5e84\\u5e02\\u85c1\\u57ce\\u533a\\u5b9e\\u9a8c\\u5c0f\\u5b66\",\"teacher_id\":7,\"planned_date\":\"2025-07-22\",\"days_overdue\":5}', 5.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-07-26 17:28:16', '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(3, 'completion_rate', 'school', 2, '石家庄市第一中学', 'medium', '实验完成率过低', '学校「石家庄市第一中学」实验完成率为 75%，低于预警阈值 80%', '{\"semester\":\"2024-2025-1\",\"total_experiments\":60,\"completed_experiments\":45,\"overdue_experiments\":5}', 75.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-24 17:28:16', '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(4, 'overdue', 'experiment', 59, '物理实验：测量物体的密度', 'high', '实验超期未开', '实验「测量物体的密度」已超期 5 天未开课', '{\"school_id\":2,\"school_name\":\"\\u77f3\\u5bb6\\u5e84\\u5e02\\u7b2c\\u4e00\\u4e2d\\u5b66\",\"teacher_id\":17,\"planned_date\":\"2025-07-22\",\"days_overdue\":5}', 5.00, 0.00, 1, 0, NULL, NULL, NULL, '2025-07-25 17:28:16', '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(5, 'completion_rate', 'school', 3, '南董第二小学', 'medium', '实验完成率过低', '学校「南董第二小学」实验完成率为 75%，低于预警阈值 80%', '{\"semester\":\"2024-2025-1\",\"total_experiments\":60,\"completed_experiments\":45,\"overdue_experiments\":5}', 75.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-23 17:28:16', '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(6, 'overdue', 'experiment', 15, '物理实验：测量物体的密度', 'high', '实验超期未开', '实验「测量物体的密度」已超期 5 天未开课', '{\"school_id\":3,\"school_name\":\"\\u5357\\u8463\\u7b2c\\u4e8c\\u5c0f\\u5b66\",\"teacher_id\":9,\"planned_date\":\"2025-07-22\",\"days_overdue\":5}', 5.00, 0.00, 1, 0, NULL, NULL, NULL, '2025-07-24 17:28:16', '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(7, 'completion_rate', 'school', 1, '石家庄市藁城区实验小学', 'medium', '实验完成率过低', '学校「石家庄市藁城区实验小学」实验完成率为 75%，低于预警阈值 80%', '{\"semester\":\"2024-2025-1\",\"total_experiments\":60,\"completed_experiments\":45,\"overdue_experiments\":5}', 75.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-25 17:36:07', '2025-07-26 17:36:07', '2025-07-26 17:36:07'),
(8, 'overdue', 'experiment', 68, '物理实验：测量物体的密度', 'high', '实验超期未开', '实验「测量物体的密度」已超期 5 天未开课', '{\"school_id\":1,\"school_name\":\"\\u77f3\\u5bb6\\u5e84\\u5e02\\u85c1\\u57ce\\u533a\\u5b9e\\u9a8c\\u5c0f\\u5b66\",\"teacher_id\":13,\"planned_date\":\"2025-07-22\",\"days_overdue\":5}', 5.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-07-26 17:36:07', '2025-07-26 17:36:07', '2025-07-26 17:36:07'),
(9, 'completion_rate', 'school', 2, '石家庄市第一中学', 'medium', '实验完成率过低', '学校「石家庄市第一中学」实验完成率为 75%，低于预警阈值 80%', '{\"semester\":\"2024-2025-1\",\"total_experiments\":60,\"completed_experiments\":45,\"overdue_experiments\":5}', 75.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-24 17:36:07', '2025-07-26 17:36:07', '2025-07-26 17:36:07'),
(10, 'overdue', 'experiment', 70, '物理实验：测量物体的密度', 'high', '实验超期未开', '实验「测量物体的密度」已超期 5 天未开课', '{\"school_id\":2,\"school_name\":\"\\u77f3\\u5bb6\\u5e84\\u5e02\\u7b2c\\u4e00\\u4e2d\\u5b66\",\"teacher_id\":4,\"planned_date\":\"2025-07-22\",\"days_overdue\":5}', 5.00, 0.00, 1, 0, NULL, NULL, NULL, '2025-07-25 17:36:07', '2025-07-26 17:36:07', '2025-07-26 17:36:07'),
(11, 'completion_rate', 'school', 3, '南董第二小学', 'medium', '实验完成率过低', '学校「南董第二小学」实验完成率为 75%，低于预警阈值 80%', '{\"semester\":\"2024-2025-1\",\"total_experiments\":60,\"completed_experiments\":45,\"overdue_experiments\":5}', 75.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-23 17:36:07', '2025-07-26 17:36:07', '2025-07-26 17:36:07'),
(12, 'overdue', 'experiment', 98, '物理实验：测量物体的密度', 'high', '实验超期未开', '实验「测量物体的密度」已超期 5 天未开课', '{\"school_id\":3,\"school_name\":\"\\u5357\\u8463\\u7b2c\\u4e8c\\u5c0f\\u5b66\",\"teacher_id\":4,\"planned_date\":\"2025-07-22\",\"days_overdue\":5}', 5.00, 0.00, 1, 0, NULL, NULL, NULL, '2025-07-24 17:36:07', '2025-07-26 17:36:07', '2025-07-26 17:36:07'),
(13, 'quality_score', 'teacher', 2, '石家庄市第一中学', 'low', '质量评分偏低', '学校 石家庄市第一中学 实验质量评分低于标准', '\"{\\\"school_id\\\":2}\"', 56.00, 70.00, 0, 0, NULL, NULL, NULL, '2025-07-20 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(14, 'quality_score', 'experiment', 2, '石家庄市第一中学', 'medium', '质量评分低于标准', '学校 石家庄市第一中学 实验质量评分低于标准', '\"{\\\"school_id\\\":2}\"', 87.00, 70.00, 1, 0, NULL, NULL, NULL, '2025-06-28 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(15, 'overdue', 'teacher', 3, '南董第二小学', 'medium', '实验已超期', '学校 南董第二小学 有实验超期未开展', '\"{\\\"school_id\\\":3}\"', 4.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-06-28 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(16, 'overdue', 'experiment', 3, '南董第二小学', 'medium', '实验已超期', '学校 南董第二小学 有实验超期未开展', '\"{\\\"school_id\\\":3}\"', 9.00, 0.00, 1, 1, NULL, NULL, NULL, '2025-07-26 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(17, 'quality_score', 'school', 1, '石家庄市藁城区实验小学', 'low', '质量评分偏低', '学校 石家庄市藁城区实验小学 实验质量评分低于标准', '\"{\\\"school_id\\\":1}\"', 76.00, 70.00, 1, 1, NULL, NULL, NULL, '2025-07-27 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(18, 'overdue', 'experiment', 5, '石家庄市栾城区实验小学', 'low', '实验即将超期', '学校 石家庄市栾城区实验小学 有实验超期未开展', '\"{\\\"school_id\\\":5}\"', 2.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-07-14 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(19, 'overdue', 'teacher', 1, '石家庄市藁城区实验小学', 'medium', '实验已超期', '学校 石家庄市藁城区实验小学 有实验超期未开展', '\"{\\\"school_id\\\":1}\"', 5.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-07-12 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(20, 'overdue', 'school', 5, '石家庄市栾城区实验小学', 'low', '实验即将超期', '学校 石家庄市栾城区实验小学 有实验超期未开展', '\"{\\\"school_id\\\":5}\"', 1.00, 0.00, 1, 0, NULL, NULL, NULL, '2025-07-07 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(21, 'overdue', 'school', 5, '石家庄市栾城区实验小学', 'high', '实验严重超期', '学校 石家庄市栾城区实验小学 有实验超期未开展', '\"{\\\"school_id\\\":5}\"', 2.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-07-09 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(22, 'completion_rate', 'school', 4, '南董实验中学1', 'low', '完成率偏低', '学校 南董实验中学1 实验完成率低于标准', '\"{\\\"school_id\\\":4}\"', 67.00, 80.00, 0, 0, NULL, NULL, NULL, '2025-07-13 00:33:26', '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(23, 'overdue', 'teacher', 1, '石家庄市藁城区实验小学', 'medium', '实验已超期', '学校 石家庄市藁城区实验小学 有实验超期未开展', '\"{\\\"school_id\\\":1}\"', 10.00, 0.00, 1, 1, NULL, NULL, NULL, '2025-07-12 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(24, 'quality_score', 'school', 4, '南董实验中学1', 'critical', '质量评分极低', '学校 南董实验中学1 实验质量评分低于标准', '\"{\\\"school_id\\\":4}\"', 88.00, 70.00, 1, 0, NULL, NULL, NULL, '2025-07-10 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(25, 'quality_score', 'school', 3, '南董第二小学', 'low', '质量评分偏低', '学校 南董第二小学 实验质量评分低于标准', '\"{\\\"school_id\\\":3}\"', 67.00, 70.00, 0, 0, NULL, NULL, NULL, '2025-06-28 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(26, 'completion_rate', 'school', 5, '石家庄市栾城区实验小学', 'critical', '完成率极低', '学校 石家庄市栾城区实验小学 实验完成率低于标准', '\"{\\\"school_id\\\":5}\"', 58.00, 80.00, 1, 1, NULL, NULL, NULL, '2025-07-11 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(27, 'overdue', 'teacher', 2, '石家庄市第一中学', 'high', '实验严重超期', '学校 石家庄市第一中学 有实验超期未开展', '\"{\\\"school_id\\\":2}\"', 7.00, 0.00, 0, 0, NULL, NULL, NULL, '2025-07-20 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(28, 'completion_rate', 'teacher', 2, '石家庄市第一中学', 'low', '完成率偏低', '学校 石家庄市第一中学 实验完成率低于标准', '\"{\\\"school_id\\\":2}\"', 79.00, 80.00, 1, 0, NULL, NULL, NULL, '2025-06-27 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(29, 'completion_rate', 'teacher', 1, '石家庄市藁城区实验小学', 'high', '完成率严重偏低', '学校 石家庄市藁城区实验小学 实验完成率低于标准', '\"{\\\"school_id\\\":1}\"', 61.00, 80.00, 1, 1, NULL, NULL, NULL, '2025-07-09 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(30, 'completion_rate', 'experiment', 5, '石家庄市栾城区实验小学', 'high', '完成率严重偏低', '学校 石家庄市栾城区实验小学 实验完成率低于标准', '\"{\\\"school_id\\\":5}\"', 74.00, 80.00, 1, 0, NULL, NULL, NULL, '2025-07-08 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(31, 'overdue', 'experiment', 4, '南董实验中学1', 'low', '实验即将超期', '学校 南董实验中学1 有实验超期未开展', '\"{\\\"school_id\\\":4}\"', 7.00, 0.00, 1, 1, NULL, NULL, NULL, '2025-07-07 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45'),
(32, 'quality_score', 'teacher', 5, '石家庄市栾城区实验小学', 'critical', '质量评分极低', '学校 石家庄市栾城区实验小学 实验质量评分低于标准', '\"{\\\"school_id\\\":5}\"', 60.00, 70.00, 1, 0, NULL, NULL, NULL, '2025-07-19 00:35:45', '2025-07-27 00:35:45', '2025-07-27 00:35:45');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_alert_config`
--

CREATE TABLE `experiment_alert_config` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_type` enum('province','city','county') NOT NULL COMMENT '组织类型',
  `organization_id` bigint(20) UNSIGNED NOT NULL COMMENT '组织ID',
  `organization_name` varchar(100) NOT NULL COMMENT '组织名称',
  `alert_type` enum('overdue','completion_rate','quality_score') NOT NULL COMMENT '预警类型',
  `threshold_value` decimal(5,2) NOT NULL COMMENT '预警阈值',
  `alert_days` int(11) NOT NULL DEFAULT 7 COMMENT '预警提前天数',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `alert_rules` text DEFAULT NULL COMMENT '预警规则说明',
  `notification_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '通知设置' CHECK (json_valid(`notification_settings`)),
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT '创建人',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_alert_config`
--

INSERT INTO `experiment_alert_config` (`id`, `organization_type`, `organization_id`, `organization_name`, `alert_type`, `threshold_value`, `alert_days`, `is_active`, `alert_rules`, `notification_settings`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'province', 1, '河北省', 'overdue', 0.00, 3, 1, '实验计划时间到期前3天开始预警，超期后立即发出高级预警', '{\"email\":true,\"sms\":false,\"system\":true,\"recipients\":[]}', 3, '2025-07-26 17:28:15', '2025-07-26 17:28:15'),
(2, 'province', 1, '河北省', 'completion_rate', 80.00, 7, 1, '学期过半时完成率低于80%发出预警，学期末低于60%发出严重预警', '{\"email\":true,\"sms\":true,\"system\":true,\"recipients\":[]}', 3, '2025-07-26 17:28:15', '2025-07-26 17:28:15'),
(3, 'province', 1, '河北省', 'quality_score', 70.00, 0, 1, '实验质量评分低于70分立即预警，低于60分发出严重预警', '{\"email\":true,\"sms\":false,\"system\":true,\"recipients\":[]}', 3, '2025-07-26 17:28:15', '2025-07-26 17:28:15'),
(4, 'city', 9, '石家庄市', 'completion_rate', 85.00, 5, 1, '石家庄市对实验完成率要求更高，低于85%即发出预警', '{\"email\":true,\"sms\":true,\"system\":true,\"recipients\":[]}', 6, '2025-07-26 17:28:16', '2025-07-26 17:28:16'),
(6, 'city', 1, '石家庄市', 'overdue', 0.00, 3, 1, '默认预警规则', '\"{\\\"email\\\":true,\\\"sms\\\":false}\"', 3, '2025-07-27 00:33:25', '2025-07-27 00:33:25'),
(7, 'city', 1, '石家庄市', 'completion_rate', 80.00, 7, 1, '默认预警规则', '\"{\\\"email\\\":true,\\\"sms\\\":false}\"', 3, '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(8, 'city', 1, '石家庄市', 'quality_score', 70.00, 7, 1, '默认预警规则', '\"{\\\"email\\\":true,\\\"sms\\\":false}\"', 3, '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(9, 'county', 1, '长安区', 'overdue', 0.00, 3, 1, '默认预警规则', '\"{\\\"email\\\":true,\\\"sms\\\":false}\"', 3, '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(10, 'county', 1, '长安区', 'completion_rate', 80.00, 7, 1, '默认预警规则', '\"{\\\"email\\\":true,\\\"sms\\\":false}\"', 3, '2025-07-27 00:33:26', '2025-07-27 00:33:26'),
(11, 'county', 1, '长安区', 'quality_score', 70.00, 7, 1, '默认预警规则', '\"{\\\"email\\\":true,\\\"sms\\\":false}\"', 3, '2025-07-27 00:33:26', '2025-07-27 00:33:26');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalogs`
--

CREATE TABLE `experiment_catalogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
  `textbook_version_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '教材版本ID',
  `chapter_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '章节ID',
  `grade_level` varchar(20) DEFAULT NULL COMMENT '年级',
  `volume` varchar(20) DEFAULT NULL COMMENT '册次',
  `management_level` tinyint(4) NOT NULL DEFAULT 5 COMMENT '管理级别（1省2市3区县4学区5学校）',
  `experiment_type` enum('必做','选做','演示','分组') NOT NULL DEFAULT '必做' COMMENT '实验类型',
  `parent_catalog_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '上级实验目录ID（继承关系）',
  `original_catalog_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '原始实验目录ID（版本追踪）',
  `version` int(11) NOT NULL DEFAULT 1 COMMENT '版本号',
  `is_deleted_by_lower` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否被下级删除',
  `delete_reason` text DEFAULT NULL COMMENT '删除理由',
  `created_by_level` tinyint(4) DEFAULT NULL COMMENT '创建者级别',
  `created_by_org_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '创建者组织ID',
  `created_by_org_type` varchar(20) DEFAULT NULL COMMENT '创建者组织类型',
  `name` varchar(200) NOT NULL COMMENT '实验名称',
  `code` varchar(50) NOT NULL COMMENT '实验编号',
  `type` tinyint(4) NOT NULL COMMENT '实验类型：1必做 2选做 3演示 4分组',
  `grade` tinyint(4) NOT NULL COMMENT '年级',
  `semester` tinyint(4) NOT NULL COMMENT '学期：1上学期 2下学期',
  `chapter` varchar(100) DEFAULT NULL COMMENT '章节',
  `duration` int(11) NOT NULL DEFAULT 45 COMMENT '实验时长(分钟)',
  `student_count` int(11) NOT NULL DEFAULT 1 COMMENT '建议学生数',
  `objective` text DEFAULT NULL COMMENT '实验目的',
  `materials` text DEFAULT NULL COMMENT '实验器材',
  `procedure` text DEFAULT NULL COMMENT '实验步骤',
  `safety_notes` text DEFAULT NULL COMMENT '安全注意事项',
  `difficulty_level` int(11) NOT NULL DEFAULT 1 COMMENT '难度等级：1-5',
  `is_standard` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否标准实验：1是 0否',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `is_baseline_catalog` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为基准目录（被学校选择的目录）',
  `baseline_priority` tinyint(4) NOT NULL DEFAULT 0 COMMENT '基准优先级：0普通 1推荐 2强制',
  `applicable_school_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '适用学校类型配置' CHECK (json_valid(`applicable_school_types`)),
  `usage_count` int(11) NOT NULL DEFAULT 0 COMMENT '被学校选择次数',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT '最后被使用时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_catalogs`
--

INSERT INTO `experiment_catalogs` (`id`, `subject_id`, `textbook_version_id`, `chapter_id`, `grade_level`, `volume`, `management_level`, `experiment_type`, `parent_catalog_id`, `original_catalog_id`, `version`, `is_deleted_by_lower`, `delete_reason`, `created_by_level`, `created_by_org_id`, `created_by_org_type`, `name`, `code`, `type`, `grade`, `semester`, `chapter`, `duration`, `student_count`, `objective`, `materials`, `procedure`, `safety_notes`, `difficulty_level`, `is_standard`, `status`, `is_baseline_catalog`, `baseline_priority`, `applicable_school_types`, `usage_count`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 2, '8', '上册', 1, '必做', NULL, NULL, 1, 0, NULL, 1, 1, 'province', '测量物体的长度', 'MP_001', 4, 8, 1, '6]', 45, 2, '学会使用刻度尺测量物体长度，掌握测量的基本方法', '刻度尺、铅笔、硬币、细线等', '1.观察刻度尺的结构\\n2.学习正确的测量方法\\n3.测量不同物体的长度\\n4.记录测量结果', '使用刻度尺时要轻拿轻放，避免弯折', 1, 1, 1, 1, 0, NULL, 1, '2025-07-27 17:27:49', '2025-07-18 15:17:56', '2025-07-28 06:21:24'),
(2, 2, 1, 2, '9', '上册', 2, '必做', NULL, NULL, 1, 0, NULL, 2, 9, 'region', '测量重力加速度', 'MP_002', 4, 9, 1, '第十三章 力和机械', 90, 4, '通过实验测量重力加速度的大小', '单摆装置、秒表、刻度尺、小球等', '1.组装单摆装置\\n2.测量摆长\\n3.测量周期\\n4.计算重力加速度', '注意摆球的安全，避免碰撞', 3, 1, 1, 1, 0, NULL, 2, '2025-07-27 17:27:49', '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(3, 3, 1, 2, '9', '上册', 3, '必做', NULL, NULL, 1, 0, NULL, 3, 15, 'county', '氧气的制取和性质', 'MC_001', 3, 9, 1, '第二单元 我们周围的空气', 45, 1, '学习氧气的制取方法，观察氧气的性质', '高锰酸钾、试管、酒精灯、导管、集气瓶等', '1.装置连接\\n2.加热制取氧气\\n3.收集氧气\\n4.验证氧气性质', '注意用火安全，避免烫伤；注意通风', 2, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(4, 4, 1, 3, '7', '上册', 4, '分组', NULL, NULL, 1, 0, NULL, 4, 25, 'district', '观察植物细胞', 'MB_001', 4, 7, 1, '第二单元 生物体的结构层次', 45, 2, '学会制作临时装片，观察植物细胞的基本结构', '显微镜、载玻片、盖玻片、洋葱、碘液等', '1.制作洋葱表皮临时装片\\n2.显微镜观察\\n3.绘制细胞结构图\\n4.总结细胞特点', '小心使用显微镜，避免损坏镜头', 2, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-18 15:17:56', '2025-07-24 19:55:29'),
(5, 5, 1, 3, '10', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '验证牛顿第二定律', 'HP_001', 4, 10, 2, '第四章 牛顿运动定律', 90, 4, '通过实验验证牛顿第二定律F=ma', '气垫导轨、滑块、砝码、光电门、计时器等', '1.调节气垫导轨水平\\n2.测量不同力下的加速度\\n3.测量不同质量下的加速度\\n4.分析数据验证定律', '注意气垫导轨的使用，避免损坏设备', 4, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(19, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '观察物体1', 'JKB-1X-01', 4, 1, 2, '第二章', 45, 4, '通过观察物体实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等', '1. 准备实验器材：玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等\n2. 按照教材第3页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:31', '2025-07-24 05:41:50'),
(20, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '探轻重排序', 'JKB-1X-02', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过探轻重排序实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '乒乓球、木块、塑料块、小橡皮、大橡皮、天平、回形针', '1. 准备实验器材：乒乓球、木块、塑料块、小橡皮、大橡皮、天平、回形针\n2. 按照教材第6页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(21, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '平描物体', 'JKB-1X-03', 3, 1, 2, '第一单元 我们周围的物体', 45, 1, '通过平描物体实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '乒乓球、木块、橡皮、蜡笔、方盒子', '1. 准备实验器材：乒乓球、木块、橡皮、蜡笔、方盒子\n2. 按照教材第9页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(22, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '给物体分类', 'JKB-1X-04', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过给物体分类实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等', '1. 准备实验器材：玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等\n2. 按照教材第11页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(23, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '观察水和洗发液', 'JKB-1X-05', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过观察水和洗发液实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '水、洗发液', '1. 准备实验器材：水、洗发液\n2. 按照教材第14页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(24, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '溶解实验', 'JKB-1X-06', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过溶解实验实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '水、红糖、食盐、小石子、小木根、小勺、烧杯', '1. 准备实验器材：水、红糖、食盐、小石子、小木根、小勺、烧杯\n2. 按照教材第17页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(25, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '观察空气', 'JKB-1X-07', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过观察空气实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '空气、水、木块、塑料袋', '1. 准备实验器材：空气、水、木块、塑料袋\n2. 按照教材第20页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(26, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '观察动物', 'JKB-1X-08', 3, 1, 2, '第二单元 动物', 45, 1, '通过观察动物实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '动物图片或标本等', '1. 准备实验器材：动物图片或标本等\n2. 按照教材第25页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(27, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '寻找小动物', 'JKB-1X-09', 4, 1, 2, '第二单元 动物', 45, 4, '通过寻找小动物实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '放大镜、镊子', '1. 准备实验器材：放大镜、镊子\n2. 按照教材第27页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(28, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '观察蜗牛', 'JKB-1X-10', 4, 1, 2, '第二单元 动物', 45, 4, '通过观察蜗牛实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '蜗牛、放大镜、玻璃', '1. 准备实验器材：蜗牛、放大镜、玻璃\n2. 按照教材第33页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(29, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '饲养蜗牛', 'JKB-1X-11', 3, 1, 2, '第二单元 动物', 45, 1, '通过饲养蜗牛实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '蜗牛、饲养箱、菜叶', '1. 准备实验器材：蜗牛、饲养箱、菜叶\n2. 按照教材第37页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(30, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '观察鱼', 'JKB-1X-12', 3, 1, 2, '第二单元 动物', 45, 1, '通过观察鱼实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '鱼缸、鱼、图片', '1. 准备实验器材：鱼缸、鱼、图片\n2. 按照教材第38页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(31, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, 5, 1, 'school', '给动物分类', 'JKB-1X-13', 4, 1, 2, '第二单元 动物', 45, 4, '通过给动物分类实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '动物卡片', '1. 准备实验器材：动物卡片\n2. 按照教材第40页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(32, 4, 1, NULL, '7', '第一册', 1, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察洋葱表皮细胞', 'MB-005', 2, 7, 1, NULL, 45, 35, '观察洋葱表皮细胞', '观察洋葱表皮细胞', '观察洋葱表皮细胞', '观察洋葱表皮细胞', 3, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-28 05:28:57', '2025-07-28 06:20:25'),
(33, 4, 1, NULL, '7', '第一册', 1, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察洋葱表皮细胞2', 'MB-0009', 2, 7, 1, NULL, 45, 45, '2观察洋葱表皮细胞2', '观察洋葱表皮细胞2', '观察洋葱表皮细胞2', '观察洋葱表皮细胞2', 3, 1, 1, 0, 0, NULL, 0, NULL, '2025-07-28 05:34:19', '2025-07-28 06:20:03');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalog_completion_baselines`
--

CREATE TABLE `experiment_catalog_completion_baselines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `config_id` bigint(20) UNSIGNED NOT NULL COMMENT '配置ID',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
  `grade` tinyint(4) NOT NULL COMMENT '年级',
  `semester` tinyint(4) NOT NULL COMMENT '学期：1上学期 2下学期',
  `total_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '总实验数量',
  `required_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '必做实验数量',
  `optional_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '选做实验数量',
  `demo_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '演示实验数量',
  `group_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '分组实验数量',
  `completed_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '已完成实验数量',
  `completion_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '完成率',
  `last_calculated_at` timestamp NULL DEFAULT NULL COMMENT '最后计算时间',
  `calculated_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '计算操作人',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalog_delete_permissions`
--

CREATE TABLE `experiment_catalog_delete_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_type` enum('province','city','county') NOT NULL COMMENT '组织类型',
  `organization_id` bigint(20) UNSIGNED NOT NULL COMMENT '组织ID',
  `organization_name` varchar(100) NOT NULL COMMENT '组织名称',
  `allow_school_delete` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否允许学校删除实验',
  `require_delete_reason` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否要求填写删除理由',
  `max_delete_percentage` int(11) NOT NULL DEFAULT 20 COMMENT '最大删除比例(%)',
  `delete_rules` text DEFAULT NULL COMMENT '删除规则说明',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT '创建人',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_catalog_delete_permissions`
--

INSERT INTO `experiment_catalog_delete_permissions` (`id`, `organization_type`, `organization_id`, `organization_name`, `allow_school_delete`, `require_delete_reason`, `max_delete_percentage`, `delete_rules`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'province', 1, '河北省', 1, 1, 20, '学校可以根据自身条件删除不适合的实验，但需要详细说明删除理由，删除比例不得超过20%。删除的实验不会影响上级统计，但需要在年度报告中说明情况。', 3, 1, '2025-07-26 15:54:20', '2025-07-26 15:54:20'),
(2, 'city', 9, '石家庄市', 1, 1, 15, '石家庄市对实验删除要求更严格，删除比例不得超过15%。学校需要提供详细的删除理由和替代方案，并经过学校实验教学委员会审议。', 6, 1, '2025-07-26 15:54:20', '2025-07-26 15:54:20');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalog_deletions`
--

CREATE TABLE `experiment_catalog_deletions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `catalog_id` bigint(20) UNSIGNED NOT NULL COMMENT '被删除的实验目录ID',
  `deleted_by_org_type` varchar(20) NOT NULL COMMENT '删除组织类型',
  `deleted_by_org_id` bigint(20) UNSIGNED NOT NULL COMMENT '删除组织ID',
  `deleted_by_user_id` bigint(20) UNSIGNED NOT NULL COMMENT '删除用户ID',
  `delete_reason` text NOT NULL COMMENT '删除理由',
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '删除时间',
  `restored_at` timestamp NULL DEFAULT NULL COMMENT '恢复时间',
  `restored_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '恢复人ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalog_permissions`
--

CREATE TABLE `experiment_catalog_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `catalog_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验目录ID',
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '学科ID',
  `organization_type` varchar(20) NOT NULL COMMENT '组织类型',
  `organization_id` bigint(20) UNSIGNED NOT NULL COMMENT '组织ID',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '用户ID',
  `permission_type` varchar(30) NOT NULL COMMENT '权限类型',
  `granted_by` bigint(20) UNSIGNED NOT NULL COMMENT '授权人ID',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalog_versions`
--

CREATE TABLE `experiment_catalog_versions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `catalog_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验目录ID',
  `version` int(11) NOT NULL COMMENT '版本号',
  `name` varchar(200) NOT NULL COMMENT '实验名称',
  `content` text DEFAULT NULL COMMENT '实验内容',
  `objective` text DEFAULT NULL COMMENT '实验目标',
  `procedure` text DEFAULT NULL COMMENT '实验步骤',
  `safety_notes` text DEFAULT NULL COMMENT '安全注意事项',
  `change_reason` varchar(500) NOT NULL COMMENT '变更原因',
  `changed_by` bigint(20) UNSIGNED NOT NULL COMMENT '变更人ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `experiment_equipment_requirements`
--

CREATE TABLE `experiment_equipment_requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `catalog_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验目录ID',
  `equipment_id` bigint(20) UNSIGNED NOT NULL COMMENT '设备ID',
  `required_quantity` int(11) NOT NULL DEFAULT 1 COMMENT '标准需要数量',
  `min_quantity` int(11) NOT NULL DEFAULT 1 COMMENT '最少需要数量',
  `is_required` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否必需器材',
  `calculation_type` enum('fixed','per_group','per_student') NOT NULL DEFAULT 'fixed' COMMENT '计算方式：固定数量/按组/按学生',
  `group_size` int(11) DEFAULT NULL COMMENT '分组大小（当calculation_type为per_group时使用）',
  `usage_note` text DEFAULT NULL COMMENT '使用说明',
  `safety_note` text DEFAULT NULL COMMENT '安全注意事项',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '创建人',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_equipment_requirements`
--

INSERT INTO `experiment_equipment_requirements` (`id`, `catalog_id`, `equipment_id`, `required_quantity`, `min_quantity`, `is_required`, `calculation_type`, `group_size`, `usage_note`, `safety_note`, `sort_order`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 2, 7, 3, 1, 1, 'per_student', 4, '实验用器材 - 电子天平', '注意安全使用', 1, 1, 1, '2025-07-20 05:22:55', '2025-07-20 05:22:55'),
(5, 2, 10, 3, 1, 0, 'per_student', 4, '实验用器材 - 分析天平', '注意安全使用', 2, 1, 1, '2025-07-20 05:22:55', '2025-07-20 05:22:55'),
(6, 2, 13, 2, 1, 0, 'fixed', 2, '实验用器材 - 数字万用表', '注意安全使用', 3, 1, 1, '2025-07-20 05:22:55', '2025-07-20 05:22:55'),
(13, 5, 1, 3, 1, 1, 'fixed', 5, '实验用器材 - 生物显微镜XSP-2CA', '注意安全使用', 1, 1, 1, '2025-07-20 05:22:55', '2025-07-20 05:22:55'),
(14, 5, 4, 3, 1, 0, 'fixed', 4, '实验用器材 - 学生用生物显微镜', '注意安全使用', 2, 1, 1, '2025-07-20 05:22:55', '2025-07-20 05:22:55'),
(15, 5, 7, 1, 1, 0, 'per_student', 5, '实验用器材 - 电子天平', '注意安全使用', 3, 1, 1, '2025-07-20 05:22:55', '2025-07-20 05:22:55'),
(16, 1, 29, 2, 1, 1, 'fixed', NULL, '用于测量物体质量', NULL, 1, 1, NULL, '2025-07-24 19:58:28', '2025-07-24 19:58:28'),
(17, 1, 30, 4, 2, 1, 'fixed', NULL, '用于测量液体体积', NULL, 2, 1, NULL, '2025-07-24 19:58:28', '2025-07-24 19:58:28'),
(18, 4, 28, 6, 3, 1, 'per_group', 2, '用于观察植物细胞结构', NULL, 1, 1, NULL, '2025-07-24 19:58:28', '2025-07-24 19:58:28'),
(19, 4, 1, 2, 1, 0, 'fixed', NULL, '高倍观察用', NULL, 2, 1, NULL, '2025-07-24 19:58:28', '2025-07-24 19:58:28'),
(20, 3, 16, 8, 4, 1, 'per_group', 3, '用于盛装化学试剂', NULL, 1, 1, NULL, '2025-07-24 19:58:28', '2025-07-24 19:58:28'),
(21, 3, 31, 12, 6, 1, 'per_group', 2, '用于化学反应', NULL, 2, 1, NULL, '2025-07-24 19:58:28', '2025-07-24 19:58:28');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_monitoring_statistics`
--

CREATE TABLE `experiment_monitoring_statistics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `target_type` enum('school','teacher','subject','grade') NOT NULL COMMENT '统计对象类型',
  `target_id` bigint(20) UNSIGNED NOT NULL COMMENT '统计对象ID',
  `target_name` varchar(200) NOT NULL COMMENT '统计对象名称',
  `semester` varchar(20) NOT NULL COMMENT '学期',
  `statistics_date` date NOT NULL COMMENT '统计日期',
  `total_planned_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '计划实验总数',
  `completed_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '已完成实验数',
  `overdue_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '超期实验数',
  `pending_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '待开实验数',
  `completion_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '完成率(%)',
  `overdue_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '超期率(%)',
  `quality_score` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '质量评分',
  `avg_completion_days` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT '平均完成天数',
  `max_overdue_days` int(11) NOT NULL DEFAULT 0 COMMENT '最大超期天数',
  `subject_statistics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '学科统计' CHECK (json_valid(`subject_statistics`)),
  `grade_statistics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '年级统计' CHECK (json_valid(`grade_statistics`)),
  `monthly_statistics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '月度统计' CHECK (json_valid(`monthly_statistics`)),
  `calculated_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '计算时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_monitoring_statistics`
--

INSERT INTO `experiment_monitoring_statistics` (`id`, `target_type`, `target_id`, `target_name`, `semester`, `statistics_date`, `total_planned_experiments`, `completed_experiments`, `overdue_experiments`, `pending_experiments`, `completion_rate`, `overdue_rate`, `quality_score`, `avg_completion_days`, `max_overdue_days`, `subject_statistics`, `grade_statistics`, `monthly_statistics`, `calculated_at`, `created_at`, `updated_at`) VALUES
(1, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-1', '2025-07-27', 80, 59, 8, 7, 80.00, 4.00, 82.00, 2.00, 5, '{\"physics\":{\"total\":20,\"completed\":15,\"completion_rate\":75},\"chemistry\":{\"total\":18,\"completed\":16,\"completion_rate\":89},\"biology\":{\"total\":15,\"completed\":12,\"completion_rate\":80}}', '{\"7\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"8\":{\"total\":20,\"completed\":16,\"completion_rate\":80},\"9\":{\"total\":18,\"completed\":15,\"completion_rate\":83}}', '{\"2024-09\":{\"total\":10,\"completed\":8,\"completion_rate\":80},\"2024-10\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"2024-11\":{\"total\":12,\"completed\":10,\"completion_rate\":83}}', '2025-07-26 17:36:07', '2025-07-26 17:28:16', '2025-07-26 17:36:07'),
(2, 'school', 2, '石家庄市第一中学', '2024-2025-1', '2025-07-27', 60, 32, 1, 14, 85.00, 15.00, 69.00, 2.00, 5, '{\"physics\":{\"total\":20,\"completed\":15,\"completion_rate\":75},\"chemistry\":{\"total\":18,\"completed\":16,\"completion_rate\":89},\"biology\":{\"total\":15,\"completed\":12,\"completion_rate\":80}}', '{\"7\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"8\":{\"total\":20,\"completed\":16,\"completion_rate\":80},\"9\":{\"total\":18,\"completed\":15,\"completion_rate\":83}}', '{\"2024-09\":{\"total\":10,\"completed\":8,\"completion_rate\":80},\"2024-10\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"2024-11\":{\"total\":12,\"completed\":10,\"completion_rate\":83}}', '2025-07-26 17:36:07', '2025-07-26 17:28:16', '2025-07-26 17:36:07'),
(3, 'school', 3, '南董第二小学', '2024-2025-1', '2025-07-27', 63, 49, 5, 16, 85.00, 0.00, 66.00, 5.00, 11, '{\"physics\":{\"total\":20,\"completed\":15,\"completion_rate\":75},\"chemistry\":{\"total\":18,\"completed\":16,\"completion_rate\":89},\"biology\":{\"total\":15,\"completed\":12,\"completion_rate\":80}}', '{\"7\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"8\":{\"total\":20,\"completed\":16,\"completion_rate\":80},\"9\":{\"total\":18,\"completed\":15,\"completion_rate\":83}}', '{\"2024-09\":{\"total\":10,\"completed\":8,\"completion_rate\":80},\"2024-10\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"2024-11\":{\"total\":12,\"completed\":10,\"completion_rate\":83}}', '2025-07-26 17:36:07', '2025-07-26 17:28:16', '2025-07-26 17:36:07'),
(4, 'school', 4, '南董实验中学1', '2024-2025-1', '2025-07-27', 51, 54, 8, 10, 65.00, 9.00, 73.00, 2.00, 6, '{\"physics\":{\"total\":20,\"completed\":15,\"completion_rate\":75},\"chemistry\":{\"total\":18,\"completed\":16,\"completion_rate\":89},\"biology\":{\"total\":15,\"completed\":12,\"completion_rate\":80}}', '{\"7\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"8\":{\"total\":20,\"completed\":16,\"completion_rate\":80},\"9\":{\"total\":18,\"completed\":15,\"completion_rate\":83}}', '{\"2024-09\":{\"total\":10,\"completed\":8,\"completion_rate\":80},\"2024-10\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"2024-11\":{\"total\":12,\"completed\":10,\"completion_rate\":83}}', '2025-07-26 17:36:07', '2025-07-26 17:28:16', '2025-07-26 17:36:07'),
(5, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-1', '2025-07-27', 70, 79, 6, 12, 93.00, 12.00, 89.00, 4.00, 9, '{\"physics\":{\"total\":20,\"completed\":15,\"completion_rate\":75},\"chemistry\":{\"total\":18,\"completed\":16,\"completion_rate\":89},\"biology\":{\"total\":15,\"completed\":12,\"completion_rate\":80}}', '{\"7\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"8\":{\"total\":20,\"completed\":16,\"completion_rate\":80},\"9\":{\"total\":18,\"completed\":15,\"completion_rate\":83}}', '{\"2024-09\":{\"total\":10,\"completed\":8,\"completion_rate\":80},\"2024-10\":{\"total\":15,\"completed\":12,\"completion_rate\":80},\"2024-11\":{\"total\":12,\"completed\":10,\"completion_rate\":83}}', '2025-07-26 17:36:07', '2025-07-26 17:28:16', '2025-07-26 17:36:07'),
(6, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-27', 20, 16, 3, 10, 69.00, 8.00, 80.00, 10.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(7, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-26', 43, 24, 5, 5, 81.00, 9.00, 76.00, 15.00, 7, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(8, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-25', 22, 29, 0, 5, 67.00, 8.00, 82.00, 7.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(9, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-24', 47, 33, 8, 6, 75.00, 25.00, 78.00, 9.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(10, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-23', 43, 38, 2, 10, 91.00, 25.00, 72.00, 5.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(11, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-22', 35, 25, 3, 10, 79.00, 14.00, 75.00, 6.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(12, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-21', 31, 34, 7, 3, 66.00, 14.00, 82.00, 11.00, 7, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(13, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-20', 30, 24, 3, 7, 79.00, 12.00, 83.00, 14.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(14, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-19', 35, 15, 0, 9, 83.00, 8.00, 68.00, 11.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(15, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-18', 22, 30, 4, 4, 84.00, 12.00, 70.00, 13.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(16, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-17', 22, 22, 3, 4, 88.00, 14.00, 78.00, 9.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(17, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-16', 23, 43, 0, 3, 92.00, 10.00, 68.00, 11.00, 2, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(18, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-15', 27, 38, 1, 10, 69.00, 10.00, 85.00, 13.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(19, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-14', 47, 30, 3, 9, 60.00, 16.00, 88.00, 6.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(20, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-13', 26, 22, 0, 5, 71.00, 9.00, 82.00, 7.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(21, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-12', 27, 38, 0, 9, 67.00, 10.00, 78.00, 14.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(22, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-11', 33, 40, 8, 7, 92.00, 22.00, 78.00, 9.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(23, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-10', 25, 36, 2, 2, 76.00, 16.00, 78.00, 5.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(24, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-09', 39, 37, 8, 8, 79.00, 24.00, 73.00, 11.00, 8, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(25, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-08', 32, 33, 7, 7, 81.00, 11.00, 70.00, 14.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(26, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-07', 28, 36, 6, 3, 63.00, 23.00, 71.00, 14.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(27, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-06', 49, 37, 2, 6, 76.00, 13.00, 82.00, 9.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(28, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-05', 24, 39, 7, 6, 95.00, 17.00, 65.00, 15.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(29, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-04', 47, 23, 2, 4, 63.00, 7.00, 87.00, 15.00, 15, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(30, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-03', 26, 36, 1, 5, 66.00, 7.00, 76.00, 9.00, 15, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(31, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-02', 40, 27, 1, 7, 85.00, 13.00, 70.00, 10.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(32, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-07-01', 47, 40, 1, 5, 93.00, 12.00, 90.00, 11.00, 6, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(33, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-06-30', 21, 40, 1, 10, 62.00, 11.00, 87.00, 8.00, 7, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(34, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-06-29', 49, 15, 3, 2, 85.00, 19.00, 75.00, 11.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(35, 'school', 1, '石家庄市藁城区实验小学', '2024-2025-2', '2025-06-28', 34, 33, 0, 6, 89.00, 25.00, 83.00, 14.00, 14, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(36, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-27', 24, 27, 0, 3, 73.00, 19.00, 89.00, 15.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(37, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-26', 30, 43, 2, 5, 60.00, 18.00, 66.00, 8.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:46', '2025-07-27 00:35:46', '2025-07-27 00:35:46'),
(38, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-25', 22, 33, 6, 8, 93.00, 11.00, 89.00, 14.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(39, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-24', 27, 19, 6, 10, 60.00, 14.00, 70.00, 6.00, 13, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(40, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-23', 47, 26, 8, 10, 94.00, 22.00, 82.00, 14.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(41, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-22', 29, 36, 4, 4, 66.00, 17.00, 75.00, 10.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(42, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-21', 38, 18, 5, 4, 66.00, 21.00, 87.00, 12.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(43, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-20', 26, 27, 1, 5, 82.00, 12.00, 75.00, 6.00, 2, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(44, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-19', 34, 32, 1, 10, 74.00, 10.00, 71.00, 13.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(45, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-18', 24, 27, 7, 9, 92.00, 14.00, 70.00, 8.00, 14, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(46, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-17', 50, 37, 2, 4, 91.00, 13.00, 65.00, 8.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(47, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-16', 42, 41, 3, 6, 74.00, 25.00, 67.00, 6.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(48, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-15', 38, 18, 8, 7, 65.00, 22.00, 86.00, 9.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(49, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-14', 27, 42, 0, 8, 82.00, 9.00, 71.00, 15.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(50, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-13', 22, 33, 1, 3, 69.00, 24.00, 78.00, 10.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(51, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-12', 49, 32, 3, 2, 64.00, 17.00, 89.00, 9.00, 15, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(52, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-11', 50, 18, 4, 4, 79.00, 8.00, 84.00, 10.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(53, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-10', 22, 42, 6, 8, 64.00, 7.00, 67.00, 5.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(54, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-09', 33, 23, 7, 8, 93.00, 11.00, 85.00, 9.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(55, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-08', 49, 44, 7, 4, 74.00, 18.00, 84.00, 8.00, 14, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(56, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-07', 38, 42, 7, 3, 84.00, 23.00, 87.00, 11.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(57, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-06', 25, 25, 8, 10, 65.00, 7.00, 74.00, 13.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(58, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-05', 43, 17, 8, 5, 60.00, 8.00, 89.00, 10.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(59, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-04', 24, 15, 8, 10, 88.00, 14.00, 90.00, 13.00, 6, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(60, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-03', 33, 37, 7, 7, 77.00, 24.00, 82.00, 15.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(61, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-02', 44, 17, 5, 6, 71.00, 20.00, 86.00, 11.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(62, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-07-01', 23, 30, 4, 9, 91.00, 10.00, 74.00, 15.00, 2, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(63, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-06-30', 46, 34, 0, 4, 77.00, 22.00, 76.00, 12.00, 7, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(64, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-06-29', 35, 34, 1, 4, 72.00, 21.00, 84.00, 8.00, 14, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(65, 'school', 2, '石家庄市第一中学', '2024-2025-2', '2025-06-28', 48, 26, 3, 2, 90.00, 7.00, 74.00, 5.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(66, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-27', 34, 18, 4, 6, 83.00, 11.00, 82.00, 13.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(67, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-26', 34, 34, 1, 6, 91.00, 22.00, 86.00, 11.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(68, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-25', 39, 33, 1, 5, 92.00, 18.00, 80.00, 14.00, 15, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:47', '2025-07-27 00:35:47', '2025-07-27 00:35:47'),
(69, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-24', 49, 21, 7, 5, 81.00, 18.00, 73.00, 8.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(70, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-23', 34, 16, 2, 9, 75.00, 23.00, 87.00, 5.00, 10, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(71, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-22', 48, 28, 2, 10, 86.00, 6.00, 79.00, 9.00, 8, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(72, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-21', 32, 36, 7, 3, 83.00, 10.00, 83.00, 7.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(73, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-20', 22, 40, 3, 9, 60.00, 25.00, 80.00, 9.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(74, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-19', 24, 43, 4, 4, 89.00, 13.00, 82.00, 13.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(75, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-18', 39, 45, 6, 8, 91.00, 21.00, 86.00, 9.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(76, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-17', 29, 27, 6, 9, 86.00, 23.00, 76.00, 12.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(77, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-16', 38, 33, 2, 5, 84.00, 12.00, 71.00, 11.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(78, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-15', 37, 36, 2, 6, 61.00, 16.00, 86.00, 14.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(79, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-14', 20, 30, 8, 10, 62.00, 15.00, 76.00, 10.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(80, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-13', 26, 45, 7, 6, 69.00, 9.00, 77.00, 7.00, 2, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(81, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-12', 20, 19, 1, 3, 67.00, 11.00, 75.00, 9.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(82, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-11', 47, 35, 4, 3, 83.00, 17.00, 75.00, 12.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(83, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-10', 46, 37, 8, 8, 83.00, 22.00, 75.00, 7.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(84, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-09', 36, 23, 8, 9, 76.00, 8.00, 66.00, 10.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(85, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-08', 46, 34, 2, 9, 62.00, 20.00, 77.00, 13.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(86, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-07', 48, 22, 4, 6, 90.00, 17.00, 90.00, 5.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(87, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-06', 37, 23, 7, 8, 90.00, 6.00, 82.00, 15.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(88, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-05', 31, 15, 5, 10, 66.00, 7.00, 81.00, 14.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(89, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-04', 20, 26, 8, 3, 85.00, 10.00, 90.00, 6.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(90, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-03', 42, 42, 3, 9, 72.00, 9.00, 89.00, 5.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(91, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-02', 46, 43, 0, 7, 91.00, 18.00, 75.00, 6.00, 13, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48');
INSERT INTO `experiment_monitoring_statistics` (`id`, `target_type`, `target_id`, `target_name`, `semester`, `statistics_date`, `total_planned_experiments`, `completed_experiments`, `overdue_experiments`, `pending_experiments`, `completion_rate`, `overdue_rate`, `quality_score`, `avg_completion_days`, `max_overdue_days`, `subject_statistics`, `grade_statistics`, `monthly_statistics`, `calculated_at`, `created_at`, `updated_at`) VALUES
(92, 'school', 3, '南董第二小学', '2024-2025-2', '2025-07-01', 37, 21, 3, 4, 87.00, 11.00, 84.00, 5.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(93, 'school', 3, '南董第二小学', '2024-2025-2', '2025-06-30', 27, 43, 3, 3, 61.00, 18.00, 88.00, 15.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(94, 'school', 3, '南董第二小学', '2024-2025-2', '2025-06-29', 31, 33, 1, 6, 81.00, 24.00, 88.00, 11.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(95, 'school', 3, '南董第二小学', '2024-2025-2', '2025-06-28', 39, 28, 7, 5, 80.00, 9.00, 82.00, 8.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:48', '2025-07-27 00:35:48', '2025-07-27 00:35:48'),
(96, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-27', 27, 43, 3, 2, 92.00, 13.00, 72.00, 15.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(97, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-26', 38, 37, 2, 9, 88.00, 24.00, 68.00, 6.00, 6, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(98, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-25', 42, 17, 4, 5, 70.00, 17.00, 73.00, 6.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(99, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-24', 25, 39, 6, 9, 90.00, 11.00, 70.00, 12.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(100, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-23', 42, 17, 4, 7, 86.00, 14.00, 68.00, 10.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(101, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-22', 29, 20, 5, 8, 88.00, 25.00, 75.00, 9.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(102, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-21', 43, 42, 4, 2, 60.00, 9.00, 68.00, 7.00, 13, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(103, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-20', 24, 38, 5, 8, 92.00, 6.00, 77.00, 7.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(104, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-19', 29, 27, 2, 7, 89.00, 12.00, 68.00, 8.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(105, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-18', 39, 37, 2, 4, 87.00, 18.00, 71.00, 7.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":15,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(106, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-17', 33, 22, 2, 6, 64.00, 20.00, 74.00, 7.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(107, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-16', 34, 31, 7, 7, 62.00, 22.00, 74.00, 6.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(108, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-15', 30, 34, 8, 6, 61.00, 17.00, 90.00, 5.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":12,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(109, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-14', 27, 27, 2, 9, 61.00, 12.00, 83.00, 7.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":5},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(110, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-13', 46, 35, 7, 4, 72.00, 16.00, 68.00, 7.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(111, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-12', 23, 38, 3, 9, 82.00, 19.00, 70.00, 9.00, 8, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(112, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-11', 24, 18, 8, 6, 86.00, 19.00, 74.00, 15.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(113, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-10', 31, 19, 0, 6, 62.00, 24.00, 89.00, 5.00, 16, '\"{\\\"physics\\\":{\\\"total\\\":11,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(114, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-09', 40, 27, 1, 3, 92.00, 17.00, 90.00, 11.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(115, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-08', 47, 37, 5, 2, 67.00, 21.00, 75.00, 5.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(116, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-07', 45, 33, 5, 10, 60.00, 9.00, 80.00, 5.00, 5, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(117, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-06', 50, 28, 1, 8, 66.00, 5.00, 86.00, 5.00, 14, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:49', '2025-07-27 00:35:49', '2025-07-27 00:35:49'),
(118, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-05', 48, 29, 5, 10, 84.00, 24.00, 89.00, 14.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(119, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-04', 21, 32, 8, 10, 88.00, 21.00, 90.00, 10.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(120, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-03', 50, 24, 1, 4, 60.00, 14.00, 85.00, 13.00, 15, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":7}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(121, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-02', 45, 33, 3, 9, 82.00, 14.00, 81.00, 11.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(122, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-07-01', 37, 18, 5, 7, 80.00, 24.00, 65.00, 14.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":9},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(123, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-06-30', 39, 16, 8, 6, 65.00, 21.00, 69.00, 8.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":3}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(124, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-06-29', 25, 30, 4, 6, 90.00, 8.00, 88.00, 8.00, 19, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(125, 'school', 4, '南董实验中学1', '2024-2025-2', '2025-06-28', 24, 21, 5, 8, 89.00, 8.00, 82.00, 12.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(126, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-27', 22, 42, 2, 5, 92.00, 23.00, 82.00, 8.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(127, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-26', 41, 22, 4, 2, 91.00, 20.00, 71.00, 8.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":6}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(128, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-25', 23, 39, 3, 6, 91.00, 13.00, 82.00, 11.00, 14, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(129, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-24', 22, 34, 7, 9, 69.00, 14.00, 84.00, 8.00, 6, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":5,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(130, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-23', 46, 35, 1, 9, 84.00, 24.00, 78.00, 14.00, 0, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":6},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(131, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-22', 31, 28, 4, 7, 77.00, 18.00, 83.00, 9.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"chemistry\\\":{\\\"total\\\":7,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":12}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(132, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-21', 22, 41, 1, 10, 76.00, 25.00, 90.00, 12.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(133, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-20', 40, 16, 1, 7, 60.00, 7.00, 79.00, 14.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(134, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-19', 30, 40, 5, 3, 87.00, 23.00, 86.00, 6.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"chemistry\\\":{\\\"total\\\":14,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(135, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-18', 31, 18, 6, 5, 60.00, 14.00, 80.00, 5.00, 3, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(136, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-17', 50, 41, 2, 6, 93.00, 25.00, 82.00, 14.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":12},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":6},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(137, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-16', 27, 36, 2, 6, 81.00, 13.00, 84.00, 12.00, 8, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":9}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":4},\\\"grade_8\\\":{\\\"total\\\":12,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(138, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-15', 44, 20, 7, 10, 86.00, 18.00, 83.00, 7.00, 2, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":9,\\\"completed\\\":10}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":5,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":13,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(139, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-14', 43, 27, 3, 10, 64.00, 19.00, 88.00, 8.00, 11, '\"{\\\"physics\\\":{\\\"total\\\":15,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":9,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":5}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(140, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-13', 30, 31, 2, 2, 68.00, 24.00, 80.00, 8.00, 6, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(141, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-12', 34, 32, 1, 10, 81.00, 11.00, 89.00, 6.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":6,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":6,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":10}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(142, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-11', 30, 20, 0, 4, 63.00, 9.00, 83.00, 6.00, 2, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":5,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(143, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-10', 34, 36, 6, 9, 90.00, 18.00, 70.00, 10.00, 7, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":7},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":8},\\\"grade_9\\\":{\\\"total\\\":5,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(144, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-09', 46, 22, 7, 4, 80.00, 21.00, 75.00, 9.00, 1, '\"{\\\"physics\\\":{\\\"total\\\":14,\\\"completed\\\":5},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":5,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":8}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(145, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-08', 42, 23, 0, 4, 81.00, 18.00, 84.00, 15.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":8,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:50', '2025-07-27 00:35:50', '2025-07-27 00:35:50'),
(146, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-07', 36, 43, 2, 6, 85.00, 22.00, 68.00, 13.00, 17, '\"{\\\"physics\\\":{\\\"total\\\":10,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":11,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":11},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(147, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-06', 49, 16, 2, 5, 79.00, 11.00, 82.00, 5.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":10,\\\"completed\\\":8},\\\"biology\\\":{\\\"total\\\":14,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":12,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(148, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-05', 46, 23, 3, 8, 62.00, 22.00, 69.00, 6.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":4},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":7,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":7},\\\"grade_9\\\":{\\\"total\\\":14,\\\"completed\\\":9}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(149, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-04', 45, 16, 4, 9, 70.00, 21.00, 71.00, 12.00, 20, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":8},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":10,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":6,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":8,\\\"completed\\\":5},\\\"grade_9\\\":{\\\"total\\\":6,\\\"completed\\\":12}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(150, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-03', 49, 43, 5, 5, 84.00, 9.00, 66.00, 12.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":7,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":11,\\\"completed\\\":10},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":8}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":3},\\\"grade_8\\\":{\\\"total\\\":14,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":15,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(151, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-02', 35, 15, 7, 10, 95.00, 16.00, 82.00, 10.00, 18, '\"{\\\"physics\\\":{\\\"total\\\":12,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":6,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":5}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":10,\\\"completed\\\":11},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":6},\\\"grade_9\\\":{\\\"total\\\":8,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(152, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-07-01', 27, 17, 0, 8, 78.00, 12.00, 70.00, 13.00, 4, '\"{\\\"physics\\\":{\\\"total\\\":8,\\\"completed\\\":12},\\\"chemistry\\\":{\\\"total\\\":9,\\\"completed\\\":5},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":4}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":7,\\\"completed\\\":10},\\\"grade_8\\\":{\\\"total\\\":10,\\\"completed\\\":3},\\\"grade_9\\\":{\\\"total\\\":9,\\\"completed\\\":11}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(153, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-06-30', 34, 33, 6, 7, 67.00, 18.00, 66.00, 9.00, 9, '\"{\\\"physics\\\":{\\\"total\\\":5,\\\"completed\\\":9},\\\"chemistry\\\":{\\\"total\\\":15,\\\"completed\\\":11},\\\"biology\\\":{\\\"total\\\":8,\\\"completed\\\":6}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":9,\\\"completed\\\":9},\\\"grade_8\\\":{\\\"total\\\":13,\\\"completed\\\":4},\\\"grade_9\\\":{\\\"total\\\":10,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(154, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-06-29', 33, 42, 6, 9, 81.00, 16.00, 70.00, 13.00, 6, '\"{\\\"physics\\\":{\\\"total\\\":13,\\\"completed\\\":6},\\\"chemistry\\\":{\\\"total\\\":12,\\\"completed\\\":9},\\\"biology\\\":{\\\"total\\\":12,\\\"completed\\\":7}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":15,\\\"completed\\\":8},\\\"grade_8\\\":{\\\"total\\\":11,\\\"completed\\\":12},\\\"grade_9\\\":{\\\"total\\\":7,\\\"completed\\\":3}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51'),
(155, 'school', 5, '石家庄市栾城区实验小学', '2024-2025-2', '2025-06-28', 30, 37, 2, 4, 64.00, 17.00, 76.00, 10.00, 12, '\"{\\\"physics\\\":{\\\"total\\\":9,\\\"completed\\\":11},\\\"chemistry\\\":{\\\"total\\\":8,\\\"completed\\\":4},\\\"biology\\\":{\\\"total\\\":13,\\\"completed\\\":11}}\"', '\"{\\\"grade_7\\\":{\\\"total\\\":14,\\\"completed\\\":7},\\\"grade_8\\\":{\\\"total\\\":15,\\\"completed\\\":10},\\\"grade_9\\\":{\\\"total\\\":11,\\\"completed\\\":4}}\"', NULL, '2025-07-27 00:35:51', '2025-07-27 00:35:51', '2025-07-27 00:35:51');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_records`
--

CREATE TABLE `experiment_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL COMMENT '预约ID',
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `catalog_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验目录ID',
  `laboratory_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验室ID',
  `teacher_id` bigint(20) UNSIGNED NOT NULL COMMENT '授课教师ID',
  `class_name` varchar(100) NOT NULL COMMENT '班级名称',
  `student_count` int(11) NOT NULL COMMENT '实际学生人数',
  `start_time` datetime DEFAULT NULL COMMENT '实际开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '实际结束时间',
  `completion_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `quality_score` tinyint(4) DEFAULT NULL COMMENT '质量评分(1-5)',
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '实验照片' CHECK (json_valid(`photos`)),
  `videos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '实验视频' CHECK (json_valid(`videos`)),
  `summary` text DEFAULT NULL COMMENT '实验总结',
  `problems` text DEFAULT NULL COMMENT '存在问题',
  `suggestions` text DEFAULT NULL COMMENT '改进建议',
  `work_count` int(11) NOT NULL DEFAULT 0 COMMENT '作品数量',
  `attendance_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '考勤数据' CHECK (json_valid(`attendance_data`)),
  `equipment_usage_rate` decimal(5,2) NOT NULL DEFAULT 100.00 COMMENT '器材使用率(%)',
  `safety_incidents` text DEFAULT NULL COMMENT '安全事件记录',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1进行中 2已完成 3异常结束',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_records`
--

INSERT INTO `experiment_records` (`id`, `reservation_id`, `school_id`, `catalog_id`, `laboratory_id`, `teacher_id`, `class_name`, `student_count`, `start_time`, `end_time`, `completion_rate`, `quality_score`, `photos`, `videos`, `summary`, `problems`, `suggestions`, `work_count`, `attendance_data`, `equipment_usage_rate`, `safety_incidents`, `status`, `created_at`, `updated_at`) VALUES
(2, 88, 1, 5, 1, 16, '五年级(3)班', 31, '2025-06-08 14:54:00', '2025-06-08 16:15:00', 98.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-06-08 15:30:44', '2025-06-08 15:30:44'),
(3, 15, 1, 1, 1, 14, '三年级(1)班', 27, '2025-07-03 13:50:00', '2025-07-03 14:53:00', 97.00, 8, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-07-03 15:30:44', '2025-07-03 15:30:44'),
(4, 53, 1, 4, 1, 14, '三年级(3)班', 27, '2025-07-04 14:26:00', '2025-07-04 15:34:00', 91.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-07-04 15:30:44', '2025-07-04 15:30:44'),
(5, 149, 1, 5, 1, 15, '九年级(1)班', 42, '2025-06-04 14:38:00', '2025-06-04 15:50:00', 95.00, 9, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-04 15:30:44', '2025-06-04 15:30:44'),
(6, 71, 1, 4, 1, 14, '六年级(2)班', 44, '2025-06-27 15:12:00', '2025-06-27 15:57:00', 86.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-06-27 15:30:44', '2025-06-27 15:30:44'),
(7, 83, 1, 5, 1, 16, '高二(6)班', 28, '2025-06-10 11:16:00', '2025-06-10 12:01:00', 88.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-10 15:30:44', '2025-06-10 15:30:44'),
(8, 65, 1, 1, 1, 15, '高三(4)班', 42, '2025-05-27 15:17:00', '2025-05-27 16:42:00', 90.00, 7, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-05-27 15:30:44', '2025-05-27 15:30:44'),
(9, 87, 1, 5, 1, 15, '八年级(4)班', 45, '2025-06-18 13:51:00', '2025-06-18 14:45:00', 100.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-18 15:30:45', '2025-06-18 15:30:45'),
(10, 1, 1, 2, 1, 16, '三年级(1)班', 44, '2025-04-23 11:03:00', '2025-04-23 12:03:00', 90.00, 8, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-04-23 15:30:45', '2025-04-23 15:30:45'),
(11, 112, 1, 4, 1, 16, '三年级(4)班', 27, '2025-07-17 10:19:00', '2025-07-17 11:04:00', 91.00, 9, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-07-17 15:30:45', '2025-07-17 15:30:45'),
(12, 42, 1, 1, 2, 15, '九年级(4)班', 30, '2025-06-17 11:25:00', '2025-06-17 12:07:00', 84.00, 10, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-06-17 15:30:45', '2025-06-17 15:30:45'),
(13, 98, 1, 1, 2, 16, '一年级(5)班', 42, '2025-07-13 12:14:00', '2025-07-13 13:07:00', 81.00, 7, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-07-13 15:30:45', '2025-07-13 15:30:45'),
(14, 22, 1, 4, 2, 15, '六年级(4)班', 40, '2025-07-09 13:12:00', '2025-07-09 13:55:00', 99.00, 10, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-07-09 15:30:45', '2025-07-09 15:30:45'),
(15, 141, 1, 5, 2, 15, '四年级(1)班', 25, '2025-05-25 11:06:00', '2025-05-25 11:48:00', 98.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-05-25 15:30:45', '2025-05-25 15:30:45'),
(16, 10, 1, 4, 2, 15, '七年级(5)班', 38, '2025-07-12 15:30:00', '2025-07-12 16:49:00', 89.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-07-12 15:30:45', '2025-07-12 15:30:45'),
(17, 129, 1, 3, 2, 14, '二年级(3)班', 31, '2025-05-07 13:54:00', '2025-05-07 14:52:00', 87.00, 7, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-05-07 15:30:45', '2025-05-07 15:30:45'),
(18, 145, 1, 3, 2, 16, '高二(5)班', 42, '2025-05-27 08:00:00', '2025-05-27 08:48:00', 89.00, 9, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-05-27 15:30:45', '2025-05-27 15:30:45'),
(19, 106, 1, 5, 2, 15, '高三(2)班', 34, '2025-06-27 11:53:00', '2025-06-27 13:13:00', 93.00, 9, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-06-27 15:30:45', '2025-06-27 15:30:45'),
(20, 94, 1, 5, 2, 15, '八年级(4)班', 28, '2025-05-25 14:33:00', '2025-05-25 15:34:00', 95.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-05-25 15:30:45', '2025-05-25 15:30:45'),
(21, 104, 1, 4, 2, 15, '高一(5)班', 31, '2025-05-18 13:08:00', '2025-05-18 14:04:00', 100.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-18 15:30:45', '2025-05-18 15:30:45'),
(22, 133, 2, 2, 4, 21, '高三(3)班', 28, '2025-05-17 12:00:00', '2025-05-17 13:12:00', 94.00, 7, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-05-17 15:30:45', '2025-05-17 15:30:45'),
(23, 78, 2, 1, 4, 19, '五年级(6)班', 32, '2025-06-07 12:45:00', '2025-06-07 14:03:00', 94.00, 8, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-07 15:30:45', '2025-06-07 15:30:45'),
(24, 43, 2, 3, 4, 19, '二年级(2)班', 26, '2025-05-10 13:47:00', '2025-05-10 14:45:00', 87.00, 7, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-05-10 15:30:45', '2025-05-10 15:30:45'),
(25, 116, 2, 2, 4, 21, '七年级(5)班', 30, '2025-04-25 09:46:00', '2025-04-25 10:56:00', 84.00, 9, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-04-25 15:30:46', '2025-04-25 15:30:46'),
(26, 142, 2, 3, 4, 20, '三年级(5)班', 29, '2025-06-10 12:44:00', '2025-06-10 13:38:00', 98.00, 7, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-10 15:30:46', '2025-06-10 15:30:46'),
(27, 54, 2, 4, 4, 19, '五年级(6)班', 44, '2025-05-17 10:21:00', '2025-05-17 11:02:00', 92.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-05-17 15:30:46', '2025-05-17 15:30:46'),
(28, 34, 2, 5, 4, 19, '八年级(3)班', 45, '2025-05-20 10:25:00', '2025-05-20 11:42:00', 94.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-05-20 15:30:46', '2025-05-20 15:30:46'),
(29, 80, 2, 5, 4, 21, '六年级(3)班', 27, '2025-05-28 08:25:00', '2025-05-28 09:37:00', 90.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-28 15:30:46', '2025-05-28 15:30:46'),
(30, 100, 2, 5, 4, 19, '四年级(4)班', 43, '2025-06-18 15:15:00', '2025-06-18 16:36:00', 90.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-18 15:30:46', '2025-06-18 15:30:46'),
(31, 75, 2, 4, 4, 19, '八年级(2)班', 42, '2025-05-12 15:42:00', '2025-05-12 17:01:00', 87.00, 7, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-12 15:30:46', '2025-05-12 15:30:46'),
(32, 136, 2, 3, 5, 20, '五年级(1)班', 31, '2025-04-29 10:54:00', '2025-04-29 12:21:00', 85.00, 8, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-04-29 15:30:46', '2025-04-29 15:30:46'),
(33, 70, 2, 2, 5, 20, '七年级(5)班', 27, '2025-06-26 15:03:00', '2025-06-26 16:13:00', 80.00, 7, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-06-26 15:30:46', '2025-06-26 15:30:46'),
(34, 127, 2, 4, 5, 20, '八年级(2)班', 39, '2025-05-12 08:15:00', '2025-05-12 09:25:00', 97.00, 10, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-12 15:30:46', '2025-05-12 15:30:46'),
(35, 66, 2, 2, 5, 20, '四年级(3)班', 39, '2025-05-23 12:28:00', '2025-05-23 13:43:00', 100.00, 10, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-23 15:30:46', '2025-05-23 15:30:46'),
(36, 23, 2, 1, 5, 20, '高一(6)班', 28, '2025-05-28 14:35:00', '2025-05-28 15:44:00', 83.00, 10, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-28 15:30:46', '2025-05-28 15:30:46'),
(37, 30, 2, 4, 5, 20, '高二(6)班', 34, '2025-06-27 09:39:00', '2025-06-27 11:05:00', 82.00, 9, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-06-27 15:30:47', '2025-06-27 15:30:47'),
(38, 47, 2, 3, 5, 20, '高三(6)班', 28, '2025-06-09 15:05:00', '2025-06-09 16:14:00', 91.00, 9, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-06-09 15:30:47', '2025-06-09 15:30:47'),
(39, 64, 2, 4, 5, 20, '八年级(3)班', 30, '2025-05-15 14:35:00', '2025-05-15 15:36:00', 84.00, 7, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-15 15:30:47', '2025-05-15 15:30:47'),
(40, 84, 2, 3, 5, 19, '四年级(1)班', 40, '2025-07-12 10:10:00', '2025-07-12 11:21:00', 81.00, 9, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-07-12 15:30:47', '2025-07-12 15:30:47'),
(41, 135, 2, 5, 5, 21, '五年级(5)班', 27, '2025-05-31 08:11:00', '2025-05-31 09:15:00', 96.00, 8, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-05-31 15:30:47', '2025-05-31 15:30:47'),
(42, 26, 3, 5, 9, 25, '一年级(1)班', 28, '2025-06-26 12:01:00', '2025-06-26 13:26:00', 81.00, 8, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-06-26 15:30:47', '2025-06-26 15:30:47'),
(43, 7, 3, 5, 9, 26, '四年级(2)班', 33, '2025-04-25 11:00:00', '2025-04-25 12:24:00', 98.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-04-25 15:30:47', '2025-04-25 15:30:47'),
(44, 118, 3, 4, 9, 25, '一年级(6)班', 34, '2025-07-01 15:00:00', '2025-07-01 16:07:00', 80.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 1, '2025-07-01 15:30:47', '2025-07-01 15:30:47'),
(45, 95, 3, 4, 9, 24, '八年级(2)班', 29, '2025-06-06 14:36:00', '2025-06-06 16:06:00', 95.00, 7, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 2, '2025-06-06 15:30:47', '2025-06-06 15:30:47'),
(46, 38, 3, 5, 9, 25, '七年级(5)班', 25, '2025-05-01 11:25:00', '2025-05-01 12:09:00', 95.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 0, NULL, 100.00, NULL, 3, '2025-05-01 15:30:47', '2025-05-01 15:30:47'),
(47, 255, 4, 25, 12, 59, '高三(1)班', 28, '2025-07-24 07:24:09', '2025-07-24 15:26:03', 100.00, 5, NULL, NULL, '实验完成良好，学生互动良好', NULL, NULL, 0, NULL, 100.00, NULL, 2, '2025-07-23 23:24:43', '2025-07-23 23:26:49'),
(48, 167, 1, 2, 1, 53, '四年级(2)班', 37, '2025-07-25 03:10:02', '2025-07-25 15:42:10', 100.00, 5, NULL, NULL, '实验良好，过程规范，学生认真', '操作问题', NULL, 0, NULL, 100.00, NULL, 2, '2025-07-24 19:10:32', '2025-07-24 22:43:16'),
(49, 318, 1, 4, 3, 90, '七年级（1）班', 30, '2025-07-25 05:11:10', '2025-07-25 14:33:49', 100.00, 5, NULL, NULL, '实验设计良好，学生反映热烈，教学效果良好。', '时间搭配问题', '认真设计实验', 0, NULL, 100.00, NULL, 2, '2025-07-24 21:11:23', '2025-07-24 22:41:17'),
(50, 319, 1, 4, 3, 90, '七年级（1）班', 30, '2025-07-25 07:29:46', '2025-07-25 15:55:57', 0.00, 4, NULL, NULL, '此试验测试上传图片，看是否能上传成功，这个功能以前不能实现', '是否能上传成功', '这个功能以前不能实现', 0, NULL, 100.00, NULL, 2, '2025-07-24 23:30:20', '2025-07-24 23:33:25'),
(51, 317, 1, 26, 2, 53, '11', 40, '2025-07-25 07:37:07', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 100.00, NULL, 1, '2025-07-24 23:37:39', '2025-07-24 23:37:39'),
(52, 320, 15, 1, 79, 95, '一年级（1）', 30, '2025-07-25 07:59:31', '2025-07-25 16:33:33', 100.00, 5, NULL, NULL, '重新注册一个新账号，东城小学测试教师，目的是测试是否能上传好照片', '否能上传好照片', '目的是测试是否能上传好照片', 0, NULL, 100.00, NULL, 2, '2025-07-25 00:00:13', '2025-07-25 00:02:26'),
(53, 321, 15, 2, 80, 95, '二年级(2)', 30, '2025-07-25 08:25:52', '2025-07-25 16:36:22', 100.00, 5, NULL, NULL, '这是一个测试实验，测试是否能上传实验好照片', '否能上传实验好照片', '测试是否能上传实验好照片', 0, NULL, 100.00, NULL, 2, '2025-07-25 00:26:10', '2025-07-25 00:27:40'),
(54, 322, 15, 2, 81, 95, '一年级1', 30, '2025-07-25 08:34:52', '2025-07-25 16:49:07', 100.00, 5, '[\"experiment_photos\\/1753432611_6883422344144.jpg\",\"experiment_photos\\/1753432611_68834223e259d.jpg\",\"experiment_photos\\/1753432611_68834223e2d11.jpg\"]', NULL, '以前实验都不能上传实验照片，现在这个能不能上传，进行测试？', '不能上传，进行测试', '上传实验照片', 0, NULL, 100.00, NULL, 2, '2025-07-25 00:35:02', '2025-07-25 00:36:51'),
(55, 323, 15, 24, 79, 95, '一年级（1）', 35, '2025-07-30 12:35:25', '2025-07-30 21:35:52', 100.00, 5, '[\"experiment_photos\\/1753879120_688a1250bbd39.jpg\",\"experiment_photos\\/1753879120_688a1250ccb15.jpg\",\"experiment_photos\\/1753879120_688a1250cdb2e.jpg\"]', NULL, '实验测试2实验测试2实验测试2实验测试2', '实验测试2实验测试2实验测试2实验测试2', '实验测试2实验测试2实验测试2实验测试2', 0, NULL, 100.00, NULL, 2, '2025-07-30 04:35:37', '2025-07-30 04:38:40'),
(56, 324, 15, 30, 79, 95, '一年级（2）', 40, '2025-08-04 08:11:22', '2025-08-04 17:11:33', 100.00, 5, '[\"experiment_photos\\/1754295149_68906b6dc0a5a.jpg\",\"experiment_photos\\/1754295149_68906b6dcd847.png\"]', NULL, '震天地堿奎屯干枯末枯枯一一在', '一震天地堿奎屯干枯末枯枯一一在', '震天地堿奎屯干枯末枯枯一一在', 0, NULL, 100.00, NULL, 2, '2025-08-04 00:11:28', '2025-08-04 00:12:29');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_requirements_config`
--

CREATE TABLE `experiment_requirements_config` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_type` enum('province','city','county') NOT NULL COMMENT '组织类型',
  `organization_id` bigint(20) UNSIGNED NOT NULL COMMENT '组织ID',
  `experiment_type` enum('分组实验','演示实验') NOT NULL COMMENT '实验类型',
  `min_images` int(11) NOT NULL DEFAULT 0 COMMENT '最少图片数量',
  `max_images` int(11) NOT NULL DEFAULT 10 COMMENT '最多图片数量',
  `min_videos` int(11) NOT NULL DEFAULT 0 COMMENT '最少视频数量',
  `max_videos` int(11) NOT NULL DEFAULT 3 COMMENT '最多视频数量',
  `is_inherited` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否继承上级配置',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT '创建人',
  `description` text DEFAULT NULL COMMENT '配置说明',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_requirements_config`
--

INSERT INTO `experiment_requirements_config` (`id`, `organization_type`, `organization_id`, `experiment_type`, `min_images`, `max_images`, `min_videos`, `max_videos`, `is_inherited`, `created_by`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'province', 1, '分组实验', 3, 8, 0, 2, 0, 3, '河北省分组实验图片视频上传要求：每次实验至少上传3张图片，最多8张；视频可选，最多2个', 1, '2025-07-26 14:40:07', '2025-07-26 14:40:07'),
(2, 'province', 1, '演示实验', 2, 5, 0, 1, 0, 3, '河北省演示实验图片视频上传要求：每次实验至少上传2张图片，最多5张；视频可选，最多1个', 1, '2025-07-26 14:40:07', '2025-07-26 14:40:07'),
(3, 'city', 9, '分组实验', 4, 10, 1, 3, 0, 6, '石家庄市分组实验要求：在省级基础上提高要求，至少4张图片，至少1个视频', 1, '2025-07-26 14:40:07', '2025-07-26 14:40:07'),
(4, 'city', 9, '演示实验', 2, 5, 0, 1, 1, 6, '石家庄市演示实验要求：继承省级配置', 1, '2025-07-26 14:40:07', '2025-07-26 14:40:07'),
(5, 'county', 16, '分组实验', 4, 10, 1, 3, 1, 44, '长安区分组实验要求：继承市级配置', 1, '2025-07-26 14:40:07', '2025-07-26 14:40:07'),
(6, 'county', 16, '演示实验', 3, 6, 0, 2, 0, 44, '长安区演示实验要求：在省级基础上适当提高要求', 1, '2025-07-26 14:40:07', '2025-07-26 14:40:07');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_reservations`
--

CREATE TABLE `experiment_reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `catalog_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验目录ID',
  `laboratory_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验室ID',
  `teacher_id` bigint(20) UNSIGNED NOT NULL COMMENT '授课教师ID',
  `class_name` varchar(100) NOT NULL COMMENT '班级名称',
  `student_count` int(11) NOT NULL COMMENT '学生人数',
  `reservation_date` date NOT NULL COMMENT '预约日期',
  `start_time` time NOT NULL COMMENT '开始时间',
  `end_time` time NOT NULL COMMENT '结束时间',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1待审核 2已通过 3已拒绝 4已完成 5已取消',
  `remark` text DEFAULT NULL COMMENT '备注',
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '审核人ID',
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '批次ID',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `review_remark` text DEFAULT NULL COMMENT '审核备注',
  `equipment_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '器材需求清单' CHECK (json_valid(`equipment_requirements`)),
  `auto_borrow_equipment` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否自动借用器材',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal' COMMENT '优先级',
  `preparation_notes` text DEFAULT NULL COMMENT '实验准备说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_reservations`
--

INSERT INTO `experiment_reservations` (`id`, `school_id`, `catalog_id`, `laboratory_id`, `teacher_id`, `class_name`, `student_count`, `reservation_date`, `start_time`, `end_time`, `status`, `remark`, `reviewer_id`, `batch_id`, `reviewed_at`, `review_remark`, `equipment_requirements`, `auto_borrow_equipment`, `priority`, `preparation_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 16, '一年级(5)班', 35, '2025-07-28', '13:47:00', '14:27:00', 4, '完成观察植物细胞实验教学任务', 39, NULL, '2025-07-13 15:27:44', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 15:27:44', '2025-07-18 15:30:45'),
(2, 1, 2, 1, 15, '四年级(1)班', 30, '2025-07-20', '14:01:00', '14:54:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 15:27:44', '2025-07-09 15:27:44'),
(3, 1, 4, 1, 16, '四年级(4)班', 42, '2025-07-28', '10:16:00', '11:26:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 15:27:44', '2025-07-15 15:27:44'),
(4, 1, 3, 1, 14, '五年级(2)班', 28, '2025-08-08', '12:24:00', '13:43:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 15:27:44', '2025-07-12 15:27:44'),
(5, 1, 5, 1, 15, '一年级(5)班', 31, '2025-07-23', '15:29:00', '16:58:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-10 15:27:44', '2025-07-16 15:27:44'),
(6, 1, 3, 1, 15, '七年级(3)班', 38, '2025-08-17', '08:21:00', '09:05:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-09 15:27:44', '2025-07-08 15:27:44'),
(7, 1, 4, 1, 15, '四年级(5)班', 30, '2025-07-24', '08:34:00', '09:58:00', 4, '完成观察植物细胞实验教学任务', 21, NULL, '2025-07-17 15:27:44', '审核通过', NULL, 1, 'normal', NULL, '2025-07-09 15:27:44', '2025-07-18 15:30:47'),
(8, 1, 5, 1, 15, '九年级(1)班', 34, '2025-08-13', '09:49:00', '10:35:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:44', '2025-07-09 15:27:44'),
(9, 1, 3, 1, 16, '七年级(6)班', 35, '2025-08-10', '08:15:00', '09:45:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 15:27:44', '2025-07-08 15:27:44'),
(10, 1, 4, 1, 14, '二年级(5)班', 42, '2025-08-05', '08:08:00', '08:51:00', 4, '完成观察植物细胞实验教学任务', 35, NULL, '2025-07-14 15:27:44', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:44', '2025-07-18 15:30:45'),
(11, 1, 5, 1, 16, '九年级(3)班', 35, '2025-08-17', '15:47:00', '17:13:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:44', '2025-07-16 15:27:44'),
(12, 1, 3, 1, 15, '八年级(3)班', 31, '2025-08-06', '11:47:00', '12:33:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:44', '2025-07-12 15:27:44'),
(13, 1, 4, 1, 16, '二年级(3)班', 42, '2025-07-25', '10:43:00', '11:42:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-09 15:27:45', '2025-07-17 15:27:45'),
(14, 1, 1, 1, 16, '六年级(1)班', 32, '2025-07-26', '08:21:00', '09:38:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:45', '2025-07-14 15:27:45'),
(15, 1, 2, 1, 14, '二年级(1)班', 40, '2025-07-22', '13:22:00', '14:04:00', 4, '完成测量重力加速度实验教学任务', 29, NULL, '2025-07-15 15:27:45', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 15:27:45', '2025-07-18 15:30:44'),
(16, 1, 2, 2, 14, '四年级(5)班', 40, '2025-08-05', '10:40:00', '11:32:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-10 15:27:45', '2025-07-10 15:27:45'),
(17, 1, 5, 2, 16, '五年级(1)班', 44, '2025-08-07', '08:37:00', '09:46:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:45', '2025-07-15 15:27:45'),
(18, 1, 2, 2, 14, '四年级(6)班', 34, '2025-07-23', '09:14:00', '09:56:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-09 15:27:45', '2025-07-11 15:27:45'),
(19, 1, 5, 2, 15, '九年级(3)班', 28, '2025-08-04', '10:44:00', '11:45:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:45', '2025-07-08 15:27:45'),
(20, 1, 1, 2, 16, '七年级(2)班', 37, '2025-07-29', '08:42:00', '09:58:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-10 15:27:45', '2025-07-14 15:27:45'),
(21, 1, 5, 2, 15, '七年级(5)班', 33, '2025-08-13', '10:34:00', '11:50:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:45', '2025-07-08 15:27:45'),
(22, 1, 4, 2, 14, '六年级(6)班', 34, '2025-08-04', '15:15:00', '16:01:00', 4, '完成观察植物细胞实验教学任务', 36, NULL, '2025-07-16 15:27:45', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:45', '2025-07-18 15:30:45'),
(23, 1, 5, 2, 14, '六年级(4)班', 28, '2025-07-30', '08:38:00', '09:49:00', 4, '完成验证牛顿第二定律实验教学任务', 20, NULL, '2025-07-16 15:27:45', '审核通过', NULL, 1, 'normal', NULL, '2025-07-08 15:27:45', '2025-07-18 15:30:46'),
(24, 1, 5, 2, 14, '五年级(4)班', 39, '2025-08-15', '15:04:00', '15:58:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:45', '2025-07-16 15:27:45'),
(25, 1, 2, 2, 14, '三年级(3)班', 34, '2025-07-27', '09:05:00', '10:15:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-08 15:27:45', '2025-07-09 15:27:45'),
(26, 1, 3, 2, 16, '九年级(4)班', 41, '2025-07-23', '12:09:00', '13:32:00', 4, '完成氧气的制取和性质实验教学任务', 25, NULL, '2025-07-17 15:27:45', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:45', '2025-07-18 15:30:47'),
(27, 1, 3, 2, 15, '八年级(3)班', 31, '2025-07-26', '09:13:00', '10:00:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-08 15:27:45', '2025-07-08 15:27:45'),
(28, 1, 1, 2, 15, '九年级(3)班', 27, '2025-08-09', '13:25:00', '14:31:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 15:27:45', '2025-07-10 15:27:45'),
(29, 1, 2, 2, 15, '高三(1)班', 27, '2025-07-26', '10:14:00', '11:19:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:45', '2025-07-13 15:27:45'),
(30, 1, 4, 2, 14, '五年级(5)班', 39, '2025-08-05', '10:02:00', '11:18:00', 4, '完成观察植物细胞实验教学任务', 35, NULL, '2025-07-11 15:27:45', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:45', '2025-07-18 15:30:47'),
(31, 2, 2, 4, 19, '五年级(5)班', 41, '2025-08-13', '12:34:00', '13:47:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-09 15:27:45', '2025-07-16 15:27:45'),
(32, 2, 2, 4, 19, '七年级(5)班', 42, '2025-08-03', '11:44:00', '12:56:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-10 15:27:45', '2025-07-16 15:27:45'),
(33, 2, 1, 4, 20, '五年级(4)班', 31, '2025-08-15', '09:05:00', '09:58:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:45', '2025-07-10 15:27:45'),
(34, 2, 3, 4, 20, '七年级(6)班', 39, '2025-07-22', '12:15:00', '13:02:00', 4, '完成氧气的制取和性质实验教学任务', 29, NULL, '2025-07-17 15:27:45', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 15:27:45', '2025-07-18 15:30:46'),
(35, 2, 2, 4, 21, '七年级(1)班', 29, '2025-08-09', '15:12:00', '16:01:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:45', '2025-07-15 15:27:45'),
(36, 2, 3, 4, 19, '高三(2)班', 31, '2025-08-16', '12:05:00', '13:32:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:46', '2025-07-09 15:27:46'),
(37, 2, 2, 4, 19, '五年级(4)班', 34, '2025-08-03', '08:14:00', '08:59:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 15:27:46', '2025-07-10 15:27:46'),
(38, 2, 2, 4, 21, '高一(4)班', 43, '2025-08-05', '11:26:00', '12:08:00', 4, '完成测量重力加速度实验教学任务', 25, NULL, '2025-07-13 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:46', '2025-07-18 15:30:47'),
(39, 2, 1, 4, 19, '高二(6)班', 39, '2025-07-29', '09:12:00', '10:34:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 15:27:46', '2025-07-16 15:27:46'),
(40, 2, 5, 4, 19, '八年级(5)班', 25, '2025-08-16', '15:49:00', '17:02:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:46', '2025-07-14 15:27:46'),
(41, 2, 2, 4, 19, '一年级(2)班', 26, '2025-08-17', '13:20:00', '14:24:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 15:27:46', '2025-07-08 15:27:46'),
(42, 2, 3, 4, 20, '高二(1)班', 43, '2025-08-15', '14:48:00', '15:41:00', 4, '完成氧气的制取和性质实验教学任务', 36, NULL, '2025-07-16 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 15:27:46', '2025-07-18 15:30:45'),
(43, 2, 5, 4, 21, '九年级(2)班', 39, '2025-08-16', '09:32:00', '10:24:00', 4, '完成验证牛顿第二定律实验教学任务', 25, NULL, '2025-07-13 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:46', '2025-07-18 15:30:45'),
(44, 2, 3, 4, 21, '三年级(3)班', 37, '2025-08-14', '08:06:00', '09:08:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-09 15:27:46', '2025-07-08 15:27:46'),
(45, 2, 5, 4, 21, '一年级(6)班', 28, '2025-08-16', '12:36:00', '13:34:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-09 15:27:46', '2025-07-12 15:27:46'),
(46, 2, 3, 5, 19, '四年级(5)班', 39, '2025-08-05', '11:01:00', '12:31:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-08 15:27:46', '2025-07-15 15:27:46'),
(47, 2, 2, 5, 21, '九年级(5)班', 40, '2025-07-26', '13:27:00', '14:44:00', 4, '完成测量重力加速度实验教学任务', 21, NULL, '2025-07-13 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-08 15:27:46', '2025-07-18 15:30:47'),
(48, 2, 4, 5, 20, '七年级(3)班', 25, '2025-08-12', '12:01:00', '12:49:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 15:27:46', '2025-07-12 15:27:46'),
(49, 2, 5, 5, 19, '二年级(1)班', 31, '2025-07-24', '08:40:00', '09:48:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:46', '2025-07-08 15:27:46'),
(50, 2, 4, 5, 21, '高三(6)班', 32, '2025-07-20', '12:23:00', '13:36:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 15:27:46', '2025-07-08 15:27:46'),
(51, 2, 3, 5, 19, '一年级(4)班', 36, '2025-07-19', '14:02:00', '15:20:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 15:27:46', '2025-07-15 15:27:46'),
(52, 2, 2, 5, 19, '六年级(2)班', 41, '2025-08-15', '12:00:00', '13:17:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 15:27:46', '2025-07-15 15:27:46'),
(53, 2, 4, 5, 21, '九年级(2)班', 42, '2025-07-24', '15:33:00', '16:39:00', 4, '完成观察植物细胞实验教学任务', 24, NULL, '2025-07-16 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:46', '2025-07-18 15:30:44'),
(54, 2, 2, 5, 19, '二年级(3)班', 37, '2025-07-31', '15:21:00', '16:34:00', 4, '完成测量重力加速度实验教学任务', 26, NULL, '2025-07-11 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-09 15:27:46', '2025-07-18 15:30:46'),
(55, 2, 1, 5, 21, '三年级(3)班', 40, '2025-08-17', '09:00:00', '10:30:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-08 15:27:46', '2025-07-17 15:27:46'),
(56, 2, 5, 5, 21, '四年级(5)班', 42, '2025-07-28', '14:19:00', '15:21:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:46', '2025-07-16 15:27:46'),
(57, 2, 5, 5, 20, '三年级(1)班', 41, '2025-08-07', '12:54:00', '13:39:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-10 15:27:46', '2025-07-17 15:27:46'),
(58, 2, 4, 5, 21, '七年级(3)班', 37, '2025-08-08', '14:00:00', '15:23:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 15:27:46', '2025-07-13 15:27:46'),
(59, 2, 5, 5, 21, '九年级(3)班', 26, '2025-08-07', '09:02:00', '09:52:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 15:27:46', '2025-07-17 15:27:46'),
(60, 2, 3, 5, 20, '高二(6)班', 34, '2025-07-26', '12:27:00', '13:46:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:46', '2025-07-09 15:27:46'),
(61, 3, 2, 9, 24, '高三(5)班', 45, '2025-08-02', '14:16:00', '15:03:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-08 15:27:46', '2025-07-15 15:27:46'),
(62, 3, 5, 9, 25, '七年级(2)班', 44, '2025-08-12', '15:48:00', '16:36:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:46', '2025-07-11 15:27:46'),
(63, 3, 1, 9, 25, '三年级(1)班', 41, '2025-08-10', '13:42:00', '14:47:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:46', '2025-07-12 15:27:46'),
(64, 3, 3, 9, 25, '五年级(3)班', 41, '2025-07-28', '13:40:00', '14:36:00', 4, '完成氧气的制取和性质实验教学任务', 29, NULL, '2025-07-12 15:27:46', '审核通过', NULL, 1, 'normal', NULL, '2025-07-09 15:27:46', '2025-07-18 15:30:47'),
(65, 3, 2, 9, 25, '二年级(5)班', 33, '2025-08-11', '13:02:00', '14:29:00', 4, '完成测量重力加速度实验教学任务', 40, NULL, '2025-07-11 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 15:27:47', '2025-07-18 15:30:44'),
(66, 3, 5, 9, 26, '高一(2)班', 25, '2025-08-04', '11:03:00', '11:59:00', 4, '完成验证牛顿第二定律实验教学任务', 31, NULL, '2025-07-17 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-08 15:27:47', '2025-07-18 15:30:46'),
(67, 3, 3, 9, 24, '高一(6)班', 27, '2025-07-26', '11:51:00', '13:05:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-13 15:27:47'),
(68, 3, 4, 9, 24, '九年级(6)班', 39, '2025-08-16', '14:50:00', '16:08:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:47', '2025-07-14 15:27:47'),
(69, 3, 2, 9, 25, '六年级(6)班', 38, '2025-08-16', '10:22:00', '11:12:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-09 15:27:47', '2025-07-15 15:27:47'),
(70, 3, 5, 9, 26, '八年级(1)班', 36, '2025-08-06', '08:52:00', '09:41:00', 4, '完成验证牛顿第二定律实验教学任务', 16, NULL, '2025-07-13 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:47', '2025-07-18 15:30:46'),
(71, 3, 5, 9, 25, '八年级(6)班', 43, '2025-08-09', '10:03:00', '11:30:00', 4, '完成验证牛顿第二定律实验教学任务', 35, NULL, '2025-07-17 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-18 15:30:44'),
(72, 3, 1, 9, 24, '九年级(2)班', 33, '2025-07-21', '15:31:00', '16:24:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-10 15:27:47', '2025-07-08 15:27:47'),
(73, 3, 1, 9, 26, '高三(6)班', 28, '2025-08-16', '11:40:00', '12:40:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-12 15:27:47'),
(74, 3, 5, 9, 26, '二年级(3)班', 35, '2025-08-06', '13:16:00', '14:38:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 15:27:47', '2025-07-12 15:27:47'),
(75, 3, 5, 9, 26, '二年级(5)班', 45, '2025-08-04', '12:12:00', '13:41:00', 4, '完成验证牛顿第二定律实验教学任务', 39, NULL, '2025-07-15 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-18 15:30:46'),
(76, 3, 5, 10, 26, '八年级(6)班', 33, '2025-07-21', '13:47:00', '14:27:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-15 15:27:47'),
(77, 3, 3, 10, 25, '高三(2)班', 34, '2025-07-23', '09:02:00', '10:07:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 15:27:47', '2025-07-11 15:27:47'),
(78, 3, 4, 10, 25, '三年级(6)班', 36, '2025-08-01', '15:47:00', '16:52:00', 4, '完成观察植物细胞实验教学任务', 39, NULL, '2025-07-11 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-18 15:30:45'),
(79, 3, 1, 10, 25, '六年级(4)班', 35, '2025-07-20', '15:23:00', '16:49:00', 2, '完成测量物体的长度实验教学任务', 14, NULL, '2025-07-22 05:41:36', NULL, NULL, 1, 'normal', NULL, '2025-07-11 15:27:47', '2025-07-22 05:41:36'),
(80, 3, 2, 10, 25, '一年级(3)班', 39, '2025-08-17', '15:15:00', '16:30:00', 4, '完成测量重力加速度实验教学任务', 16, NULL, '2025-07-12 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 15:27:47', '2025-07-18 15:30:46'),
(81, 3, 5, 10, 25, '高一(3)班', 25, '2025-08-06', '11:31:00', '12:57:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:47', '2025-07-15 15:27:47'),
(82, 3, 4, 10, 26, '高一(6)班', 38, '2025-08-15', '08:08:00', '09:34:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-08 15:27:47', '2025-07-08 15:27:47'),
(83, 3, 2, 10, 24, '高三(4)班', 40, '2025-07-30', '14:22:00', '15:04:00', 4, '完成测量重力加速度实验教学任务', 39, NULL, '2025-07-17 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-08 15:27:47', '2025-07-18 15:30:44'),
(84, 3, 2, 10, 26, '高二(3)班', 33, '2025-08-04', '09:01:00', '09:43:00', 4, '完成测量重力加速度实验教学任务', 40, NULL, '2025-07-14 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-09 15:27:47', '2025-07-18 15:30:47'),
(85, 3, 2, 10, 25, '四年级(1)班', 27, '2025-08-04', '10:54:00', '12:10:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 15:27:47', '2025-07-16 15:27:47'),
(86, 3, 2, 10, 26, '三年级(5)班', 32, '2025-08-14', '15:52:00', '17:21:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:47', '2025-07-12 15:27:47'),
(87, 3, 5, 10, 24, '九年级(6)班', 40, '2025-07-25', '09:31:00', '10:23:00', 4, '完成验证牛顿第二定律实验教学任务', 29, NULL, '2025-07-14 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:47', '2025-07-18 15:30:45'),
(88, 3, 3, 10, 25, '六年级(2)班', 28, '2025-07-24', '08:55:00', '10:23:00', 4, '完成氧气的制取和性质实验教学任务', 41, NULL, '2025-07-15 15:27:47', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 15:27:47', '2025-07-18 15:30:44'),
(89, 3, 5, 10, 24, '四年级(4)班', 32, '2025-08-04', '08:44:00', '09:37:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 15:27:47', '2025-07-11 15:27:47'),
(90, 3, 2, 10, 25, '高二(3)班', 39, '2025-08-09', '12:33:00', '13:27:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 15:27:47', '2025-07-11 15:27:47'),
(91, 4, 3, 12, 30, '高一(6)班', 45, '2025-08-06', '15:34:00', '16:26:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-08 15:27:47', '2025-07-14 15:27:47'),
(92, 4, 1, 12, 31, '四年级(3)班', 39, '2025-08-12', '15:42:00', '17:03:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:47', '2025-07-16 15:27:47'),
(93, 4, 3, 12, 29, '五年级(4)班', 40, '2025-07-25', '10:21:00', '11:40:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:48', '2025-07-12 15:27:48'),
(94, 4, 5, 12, 30, '一年级(2)班', 38, '2025-08-16', '11:41:00', '12:31:00', 4, '完成验证牛顿第二定律实验教学任务', 25, NULL, '2025-07-12 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:48', '2025-07-18 15:30:45'),
(95, 4, 5, 12, 30, '一年级(1)班', 30, '2025-07-30', '09:09:00', '10:21:00', 4, '完成验证牛顿第二定律实验教学任务', 41, NULL, '2025-07-15 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 15:27:48', '2025-07-18 15:30:47'),
(96, 4, 2, 12, 30, '高一(6)班', 28, '2025-07-25', '11:12:00', '12:41:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 15:27:48', '2025-07-09 15:27:48'),
(97, 4, 5, 12, 31, '八年级(1)班', 43, '2025-08-15', '12:50:00', '14:02:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-11 15:27:48', '2025-07-08 15:27:48'),
(98, 4, 1, 12, 29, '二年级(5)班', 43, '2025-08-06', '11:34:00', '12:53:00', 4, '完成测量物体的长度实验教学任务', 19, NULL, '2025-07-14 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-10 15:27:48', '2025-07-18 15:30:45'),
(99, 4, 2, 12, 31, '高三(6)班', 38, '2025-07-25', '11:00:00', '12:16:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:48', '2025-07-10 15:27:48'),
(100, 4, 3, 12, 30, '一年级(2)班', 43, '2025-07-19', '12:27:00', '13:46:00', 4, '完成氧气的制取和性质实验教学任务', 40, NULL, '2025-07-16 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-10 15:27:48', '2025-07-18 15:30:46'),
(101, 4, 5, 12, 29, '高一(2)班', 42, '2025-08-17', '14:40:00', '15:27:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:48', '2025-07-08 15:27:48'),
(102, 4, 3, 12, 31, '高一(1)班', 31, '2025-07-23', '11:26:00', '12:09:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-10 15:27:48', '2025-07-11 15:27:48'),
(103, 4, 2, 12, 29, '三年级(2)班', 43, '2025-07-29', '12:11:00', '13:32:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 15:27:48', '2025-07-14 15:27:48'),
(104, 4, 4, 12, 30, '九年级(5)班', 44, '2025-08-01', '14:26:00', '15:24:00', 4, '完成观察植物细胞实验教学任务', 41, NULL, '2025-07-17 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-08 15:27:48', '2025-07-18 15:30:45'),
(105, 4, 2, 12, 29, '高二(2)班', 36, '2025-08-04', '11:33:00', '12:51:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 15:27:48', '2025-07-17 15:27:48'),
(106, 4, 4, 13, 30, '高二(2)班', 40, '2025-08-08', '14:42:00', '15:23:00', 4, '完成观察植物细胞实验教学任务', 25, NULL, '2025-07-11 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-11 15:27:48', '2025-07-18 15:30:45'),
(107, 4, 4, 13, 29, '高三(3)班', 41, '2025-08-10', '08:29:00', '09:38:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-08 15:27:48', '2025-07-08 15:27:48'),
(108, 4, 2, 13, 29, '六年级(4)班', 29, '2025-07-31', '14:11:00', '15:27:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 15:27:48', '2025-07-08 15:27:48'),
(109, 4, 4, 13, 29, '三年级(4)班', 44, '2025-08-16', '08:52:00', '10:04:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:48', '2025-07-12 15:27:48'),
(110, 4, 1, 13, 29, '六年级(6)班', 40, '2025-08-06', '15:33:00', '16:49:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 15:27:48', '2025-07-17 15:27:48'),
(111, 4, 2, 13, 29, '二年级(6)班', 41, '2025-08-17', '09:39:00', '10:57:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:48', '2025-07-10 15:27:48'),
(112, 4, 1, 13, 29, '五年级(3)班', 42, '2025-08-04', '15:36:00', '16:27:00', 4, '完成测量物体的长度实验教学任务', 29, NULL, '2025-07-11 15:27:48', '审核通过', NULL, 1, 'normal', NULL, '2025-07-08 15:27:48', '2025-07-18 15:30:45'),
(113, 4, 3, 13, 30, '七年级(3)班', 27, '2025-08-03', '12:18:00', '13:48:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 15:27:48', '2025-07-12 15:27:48'),
(114, 4, 3, 13, 29, '高二(6)班', 43, '2025-08-07', '11:48:00', '12:58:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 15:27:49', '2025-07-08 15:27:49'),
(115, 4, 4, 13, 30, '高三(4)班', 31, '2025-07-28', '14:48:00', '16:08:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:49', '2025-07-16 15:27:49'),
(116, 4, 2, 13, 29, '一年级(3)班', 25, '2025-07-24', '14:53:00', '15:44:00', 4, '完成测量重力加速度实验教学任务', 20, NULL, '2025-07-11 15:27:49', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:49', '2025-07-18 15:30:46'),
(117, 4, 2, 13, 29, '四年级(6)班', 45, '2025-08-05', '13:22:00', '14:43:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 15:27:49', '2025-07-16 15:27:49'),
(118, 4, 5, 13, 31, '五年级(5)班', 27, '2025-07-29', '08:27:00', '09:27:00', 4, '完成验证牛顿第二定律实验教学任务', 31, NULL, '2025-07-13 15:27:49', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:49', '2025-07-18 15:30:47'),
(119, 4, 3, 13, 30, '三年级(3)班', 31, '2025-08-11', '11:23:00', '12:39:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 15:27:49', '2025-07-10 15:27:49'),
(120, 4, 1, 13, 29, '三年级(4)班', 36, '2025-07-27', '09:07:00', '10:08:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-15 15:27:49', '2025-07-13 15:27:49'),
(121, 5, 2, 15, 35, '八年级(6)班', 36, '2025-08-14', '12:36:00', '13:46:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-09 15:27:49', '2025-07-11 15:27:49'),
(122, 5, 5, 15, 36, '高二(3)班', 42, '2025-07-22', '09:52:00', '11:09:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:49', '2025-07-17 15:27:49'),
(123, 5, 2, 15, 34, '四年级(5)班', 39, '2025-08-03', '09:51:00', '11:14:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 15:27:49', '2025-07-17 15:27:49'),
(124, 5, 4, 15, 34, '四年级(2)班', 39, '2025-07-29', '10:12:00', '10:58:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 15:27:49', '2025-07-08 15:27:49'),
(125, 5, 2, 15, 35, '高一(4)班', 26, '2025-08-12', '08:24:00', '09:08:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:49', '2025-07-17 15:27:49'),
(126, 5, 3, 15, 34, '高一(3)班', 30, '2025-08-10', '13:20:00', '14:33:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-08 15:27:49', '2025-07-14 15:27:49'),
(127, 5, 4, 15, 34, '七年级(1)班', 25, '2025-08-10', '10:47:00', '11:38:00', 4, '完成观察植物细胞实验教学任务', 20, NULL, '2025-07-14 15:27:49', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:49', '2025-07-18 15:30:46'),
(128, 5, 2, 15, 36, '四年级(2)班', 36, '2025-08-10', '11:19:00', '12:01:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-08 15:27:49', '2025-07-08 15:27:49'),
(129, 5, 4, 15, 35, '高一(3)班', 31, '2025-08-02', '11:37:00', '12:29:00', 4, '完成观察植物细胞实验教学任务', 41, NULL, '2025-07-17 15:27:49', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:49', '2025-07-18 15:30:45'),
(130, 5, 3, 15, 36, '高二(5)班', 36, '2025-07-25', '13:34:00', '14:26:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-10 15:27:49', '2025-07-14 15:27:49'),
(131, 5, 5, 15, 35, '九年级(4)班', 36, '2025-08-04', '12:48:00', '14:03:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 15:27:49', '2025-07-08 15:27:49'),
(132, 5, 2, 15, 34, '三年级(5)班', 37, '2025-08-05', '15:03:00', '15:43:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-09 15:27:49', '2025-07-10 15:27:49'),
(133, 5, 5, 15, 36, '九年级(3)班', 36, '2025-08-13', '15:46:00', '16:34:00', 4, '完成验证牛顿第二定律实验教学任务', 30, NULL, '2025-07-17 15:27:49', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:49', '2025-07-18 15:30:45'),
(134, 5, 2, 15, 35, '一年级(2)班', 42, '2025-08-09', '14:28:00', '15:40:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 15:27:50', '2025-07-17 15:27:50'),
(135, 5, 3, 15, 34, '九年级(6)班', 34, '2025-07-31', '09:45:00', '10:25:00', 4, '完成氧气的制取和性质实验教学任务', 25, NULL, '2025-07-15 15:27:50', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 15:27:50', '2025-07-18 15:30:47'),
(136, 5, 3, 16, 36, '高三(4)班', 25, '2025-07-24', '11:11:00', '12:15:00', 4, '完成氧气的制取和性质实验教学任务', 15, NULL, '2025-07-14 15:27:50', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:50', '2025-07-18 15:30:46'),
(137, 5, 3, 16, 35, '二年级(4)班', 37, '2025-07-20', '14:49:00', '15:42:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 15:27:50', '2025-07-17 15:27:50'),
(138, 5, 4, 16, 35, '五年级(1)班', 41, '2025-07-27', '13:09:00', '14:32:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-11 15:27:50', '2025-07-08 15:27:50'),
(139, 5, 4, 16, 34, '一年级(3)班', 38, '2025-07-26', '14:41:00', '15:42:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-08 15:27:50', '2025-07-08 15:27:50'),
(140, 5, 1, 16, 34, '三年级(6)班', 38, '2025-07-25', '15:10:00', '16:13:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 15:27:50', '2025-07-17 15:27:50'),
(141, 5, 3, 16, 34, '高一(5)班', 34, '2025-08-05', '13:43:00', '14:45:00', 4, '完成氧气的制取和性质实验教学任务', 15, NULL, '2025-07-12 15:27:50', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 15:27:50', '2025-07-18 15:30:45'),
(142, 5, 2, 16, 34, '七年级(3)班', 45, '2025-08-04', '08:06:00', '09:25:00', 4, '完成测量重力加速度实验教学任务', 15, NULL, '2025-07-13 15:27:50', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 15:27:50', '2025-07-18 15:30:46'),
(143, 5, 3, 16, 35, '七年级(3)班', 28, '2025-08-03', '09:51:00', '10:47:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 15:27:50', '2025-07-12 15:27:50'),
(144, 5, 1, 16, 36, '高一(1)班', 25, '2025-07-29', '13:19:00', '14:44:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-09 15:27:50', '2025-07-15 15:27:50'),
(145, 5, 4, 16, 35, '三年级(3)班', 40, '2025-08-13', '09:47:00', '10:56:00', 4, '完成观察植物细胞实验教学任务', 39, NULL, '2025-07-14 15:27:50', '审核通过', NULL, 1, 'normal', NULL, '2025-07-09 15:27:50', '2025-07-18 15:30:45'),
(146, 5, 4, 16, 36, '高一(1)班', 37, '2025-07-20', '15:17:00', '16:30:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:50', '2025-07-15 15:27:50'),
(147, 5, 1, 16, 36, '三年级(2)班', 28, '2025-07-27', '09:08:00', '09:53:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-15 15:27:50', '2025-07-17 15:27:50'),
(148, 5, 2, 16, 34, '五年级(6)班', 42, '2025-07-29', '11:40:00', '12:30:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 15:27:50', '2025-07-13 15:27:50'),
(149, 5, 5, 16, 34, '九年级(4)班', 33, '2025-07-24', '08:05:00', '09:31:00', 4, '完成验证牛顿第二定律实验教学任务', 24, NULL, '2025-07-14 15:27:50', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 15:27:50', '2025-07-18 15:30:44'),
(150, 5, 2, 16, 36, '高一(3)班', 32, '2025-08-07', '14:51:00', '16:05:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-11 15:27:50', '2025-07-12 15:27:50'),
(151, 3, 20, 9, 84, 'edf', 40, '2025-07-23', '08:37:00', '09:37:00', 1, 'fv', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-21 15:39:01', '2025-07-21 15:39:01'),
(152, 5, 21, 16, 84, '一年级2班', 45, '2025-07-25', '10:21:00', '11:21:00', 1, '我', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-21 18:22:07', '2025-07-21 18:22:07'),
(153, 3, 19, 10, 70, '一年级3', 45, '2025-07-31', '14:39:00', '15:39:00', 2, NULL, 14, NULL, '2025-07-22 05:41:26', NULL, NULL, 1, 'normal', NULL, '2025-07-22 00:41:01', '2025-07-22 05:41:26'),
(154, 1, 23, 1, 54, '高三(1)班', 45, '2025-08-22', '12:16:00', '13:00:00', 2, '完成观察水和洗发液实验教学任务', 61, NULL, '2025-07-18 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-21 20:01:38', '2025-07-13 20:01:38'),
(155, 1, 27, 1, 54, '八年级(5)班', 32, '2025-08-11', '11:25:00', '12:28:00', 3, '完成寻找小动物实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:38', '2025-07-15 20:01:38'),
(156, 1, 3, 1, 53, '六年级(1)班', 28, '2025-07-31', '14:08:00', '14:53:00', 2, '完成氧气的制取和性质实验教学任务', 75, NULL, '2025-07-16 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-20 20:01:38', '2025-07-20 20:01:38'),
(157, 1, 3, 1, 54, '六年级(4)班', 33, '2025-08-14', '14:59:00', '16:08:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 20:01:38', '2025-07-14 20:01:38'),
(158, 1, 2, 1, 53, '二年级(5)班', 43, '2025-08-21', '15:19:00', '16:35:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-19 20:01:38', '2025-07-12 20:01:38'),
(159, 1, 5, 1, 54, '一年级(6)班', 25, '2025-08-10', '15:40:00', '16:54:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 20:01:38', '2025-07-19 20:01:38'),
(160, 1, 25, 1, 54, '五年级(3)班', 41, '2025-08-15', '11:51:00', '12:44:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:38', '2025-07-13 20:01:38'),
(161, 1, 31, 1, 54, '四年级(4)班', 36, '2025-07-28', '15:34:00', '16:43:00', 3, '完成给动物分类实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:38', '2025-07-20 20:01:38'),
(162, 1, 25, 1, 54, '六年级(6)班', 25, '2025-08-08', '14:58:00', '16:03:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:38', '2025-07-12 20:01:38'),
(163, 1, 26, 1, 53, '二年级(5)班', 29, '2025-07-29', '12:57:00', '14:18:00', 2, '完成观察动物实验教学任务', 57, NULL, '2025-07-21 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-19 20:01:38', '2025-07-17 20:01:38'),
(164, 1, 30, 1, 53, '高三(1)班', 44, '2025-07-29', '08:27:00', '09:56:00', 3, '完成观察鱼实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-19 20:01:38', '2025-07-16 20:01:38'),
(165, 1, 25, 1, 54, '七年级(4)班', 41, '2025-08-05', '08:08:00', '09:36:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:38', '2025-07-20 20:01:38'),
(166, 1, 31, 1, 54, '四年级(1)班', 29, '2025-08-13', '09:48:00', '11:16:00', 2, '完成给动物分类实验教学任务', 83, NULL, '2025-07-15 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:38', '2025-07-20 20:01:38'),
(167, 1, 2, 1, 53, '四年级(2)班', 37, '2025-07-25', '08:09:00', '09:06:00', 4, '完成测量重力加速度实验教学任务', 62, NULL, '2025-07-19 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:38', '2025-07-24 19:10:32'),
(168, 1, 20, 1, 54, '高三(5)班', 28, '2025-08-06', '09:47:00', '11:16:00', 3, '完成探轻重排序实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:38', '2025-07-13 20:01:38'),
(169, 1, 20, 2, 53, '高一(6)班', 26, '2025-08-16', '12:13:00', '13:42:00', 3, '完成探轻重排序实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-21 20:01:38', '2025-07-17 20:01:38'),
(170, 1, 5, 2, 54, '高三(6)班', 41, '2025-08-21', '13:05:00', '14:28:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 20:01:38', '2025-07-12 20:01:38'),
(171, 1, 3, 2, 54, '四年级(2)班', 30, '2025-08-03', '15:07:00', '16:31:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 20:01:38', '2025-07-20 20:01:38'),
(172, 1, 3, 2, 53, '一年级(5)班', 30, '2025-08-18', '15:37:00', '16:44:00', 2, '完成氧气的制取和性质实验教学任务', 67, NULL, '2025-07-20 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 20:01:38', '2025-07-19 20:01:38'),
(173, 1, 1, 2, 53, '六年级(1)班', 27, '2025-08-07', '12:44:00', '13:39:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:38', '2025-07-13 20:01:38'),
(174, 1, 20, 2, 53, '二年级(6)班', 29, '2025-08-15', '12:37:00', '13:21:00', 1, '完成探轻重排序实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:38', '2025-07-19 20:01:38'),
(175, 1, 21, 2, 53, '四年级(2)班', 25, '2025-08-05', '11:36:00', '12:33:00', 2, '完成平描物体实验教学任务', 60, NULL, '2025-07-17 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:38', '2025-07-14 20:01:38'),
(176, 1, 26, 2, 54, '三年级(4)班', 26, '2025-08-17', '15:05:00', '16:11:00', 3, '完成观察动物实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:38', '2025-07-18 20:01:38'),
(177, 1, 24, 2, 54, '高二(1)班', 43, '2025-08-21', '11:43:00', '12:42:00', 2, '完成溶解实验实验教学任务', 73, NULL, '2025-07-17 20:01:38', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 20:01:38', '2025-07-20 20:01:38'),
(178, 1, 19, 2, 54, '高三(6)班', 29, '2025-08-22', '08:50:00', '09:32:00', 3, '完成观察物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:39', '2025-07-12 20:01:39'),
(179, 1, 26, 2, 53, '二年级(3)班', 29, '2025-08-03', '13:14:00', '14:25:00', 3, '完成观察动物实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:39', '2025-07-21 20:01:39'),
(180, 1, 5, 2, 54, '一年级(2)班', 41, '2025-08-17', '12:45:00', '13:36:00', 2, '完成验证牛顿第二定律实验教学任务', 80, NULL, '2025-07-21 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 20:01:39', '2025-07-19 20:01:39'),
(181, 1, 24, 2, 54, '高一(1)班', 43, '2025-07-30', '15:22:00', '16:24:00', 2, '完成溶解实验实验教学任务', 74, NULL, '2025-07-18 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:39', '2025-07-20 20:01:39'),
(182, 1, 23, 2, 54, '八年级(2)班', 27, '2025-07-25', '14:55:00', '15:44:00', 1, '完成观察水和洗发液实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-20 20:01:39', '2025-07-12 20:01:39'),
(183, 1, 27, 2, 53, '四年级(4)班', 33, '2025-07-26', '09:52:00', '11:21:00', 1, '完成寻找小动物实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:39', '2025-07-20 20:01:39'),
(184, 2, 5, 4, 56, '九年级(6)班', 35, '2025-08-13', '09:20:00', '10:50:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 20:01:39', '2025-07-15 20:01:39'),
(185, 2, 3, 4, 56, '高一(6)班', 40, '2025-07-24', '14:33:00', '15:41:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-18 20:01:39', '2025-07-19 20:01:39'),
(186, 2, 31, 4, 56, '八年级(5)班', 39, '2025-07-31', '12:38:00', '13:31:00', 2, '完成给动物分类实验教学任务', 68, NULL, '2025-07-18 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-20 20:01:39', '2025-07-17 20:01:39'),
(187, 2, 25, 4, 56, '六年级(4)班', 28, '2025-07-27', '08:07:00', '09:21:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:39', '2025-07-16 20:01:39'),
(188, 2, 21, 4, 55, '四年级(2)班', 43, '2025-08-22', '09:26:00', '10:21:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:39', '2025-07-12 20:01:39'),
(189, 2, 28, 4, 55, '高二(5)班', 31, '2025-08-17', '14:23:00', '15:15:00', 1, '完成观察蜗牛实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-19 20:01:39', '2025-07-20 20:01:39'),
(190, 2, 21, 4, 56, '六年级(6)班', 45, '2025-08-13', '10:17:00', '11:05:00', 1, '完成平描物体实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:39', '2025-07-21 20:01:39'),
(191, 2, 2, 4, 55, '一年级(4)班', 37, '2025-07-28', '14:43:00', '15:31:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:39', '2025-07-18 20:01:39'),
(192, 2, 2, 4, 56, '一年级(4)班', 35, '2025-08-11', '11:29:00', '12:50:00', 2, '完成测量重力加速度实验教学任务', 58, NULL, '2025-07-21 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-19 20:01:39', '2025-07-18 20:01:39'),
(193, 2, 5, 4, 56, '二年级(1)班', 38, '2025-07-30', '09:30:00', '10:23:00', 2, '完成验证牛顿第二定律实验教学任务', 64, NULL, '2025-07-19 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-18 20:01:39', '2025-07-17 20:01:39'),
(194, 2, 23, 4, 56, '九年级(3)班', 45, '2025-07-31', '15:09:00', '16:30:00', 2, '完成观察水和洗发液实验教学任务', 80, NULL, '2025-07-20 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:39', '2025-07-21 20:01:39'),
(195, 2, 29, 4, 56, '一年级(3)班', 36, '2025-08-22', '09:25:00', '10:19:00', 2, '完成饲养蜗牛实验教学任务', 63, NULL, '2025-07-18 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-20 20:01:39', '2025-07-21 20:01:39'),
(196, 2, 28, 4, 56, '高三(1)班', 33, '2025-08-01', '14:58:00', '16:27:00', 1, '完成观察蜗牛实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 20:01:39', '2025-07-13 20:01:39'),
(197, 2, 19, 4, 55, '五年级(3)班', 31, '2025-07-29', '14:45:00', '15:49:00', 2, '完成观察物体实验教学任务', 54, NULL, '2025-07-17 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 20:01:39', '2025-07-12 20:01:39'),
(198, 2, 30, 4, 55, '六年级(1)班', 37, '2025-08-21', '09:16:00', '10:21:00', 3, '完成观察鱼实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:39', '2025-07-13 20:01:39'),
(199, 2, 20, 5, 55, '九年级(1)班', 27, '2025-08-03', '10:25:00', '11:42:00', 2, '完成探轻重排序实验教学任务', 74, NULL, '2025-07-15 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-21 20:01:39', '2025-07-18 20:01:39'),
(200, 2, 5, 5, 56, '七年级(3)班', 44, '2025-07-30', '08:08:00', '08:54:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:39', '2025-07-19 20:01:39'),
(201, 2, 21, 5, 55, '三年级(4)班', 28, '2025-08-18', '10:50:00', '11:59:00', 1, '完成平描物体实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-20 20:01:39', '2025-07-17 20:01:39'),
(202, 2, 26, 5, 56, '七年级(6)班', 25, '2025-08-09', '12:20:00', '13:25:00', 1, '完成观察动物实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-21 20:01:39', '2025-07-17 20:01:39'),
(203, 2, 23, 5, 55, '高二(6)班', 30, '2025-08-01', '09:05:00', '09:56:00', 2, '完成观察水和洗发液实验教学任务', 60, NULL, '2025-07-21 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-18 20:01:39', '2025-07-14 20:01:39'),
(204, 2, 31, 5, 55, '高二(6)班', 27, '2025-08-12', '12:58:00', '13:43:00', 2, '完成给动物分类实验教学任务', 60, NULL, '2025-07-16 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 20:01:39', '2025-07-12 20:01:39'),
(205, 2, 19, 5, 55, '高一(1)班', 28, '2025-08-02', '12:38:00', '13:58:00', 2, '完成观察物体实验教学任务', 84, NULL, '2025-07-17 20:01:39', '审核通过', NULL, 1, 'normal', NULL, '2025-07-21 20:01:39', '2025-07-14 20:01:39'),
(206, 2, 5, 5, 56, '七年级(5)班', 25, '2025-08-05', '15:53:00', '17:03:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 20:01:39', '2025-07-19 20:01:39'),
(207, 2, 25, 5, 55, '二年级(1)班', 29, '2025-07-27', '15:14:00', '16:32:00', 2, '完成观察空气实验教学任务', 83, NULL, '2025-07-21 20:01:40', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 20:01:40', '2025-07-19 20:01:40'),
(208, 2, 2, 5, 55, '二年级(6)班', 38, '2025-07-28', '15:32:00', '16:13:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-15 20:01:40', '2025-07-20 20:01:40'),
(209, 2, 4, 5, 55, '三年级(4)班', 35, '2025-08-06', '13:41:00', '14:41:00', 2, '完成观察植物细胞实验教学任务', 67, NULL, '2025-07-15 20:01:40', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:40', '2025-07-16 20:01:40'),
(210, 2, 3, 5, 55, '高三(3)班', 32, '2025-08-13', '15:25:00', '16:27:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:40', '2025-07-15 20:01:40'),
(211, 2, 30, 5, 56, '高二(6)班', 45, '2025-07-28', '15:05:00', '16:14:00', 1, '完成观察鱼实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-20 20:01:40', '2025-07-13 20:01:40'),
(212, 2, 22, 5, 55, '二年级(6)班', 45, '2025-07-29', '14:43:00', '16:00:00', 1, '完成给物体分类实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 20:01:40', '2025-07-13 20:01:40'),
(213, 2, 30, 5, 56, '六年级(2)班', 26, '2025-08-03', '11:47:00', '12:29:00', 2, '完成观察鱼实验教学任务', 70, NULL, '2025-07-17 20:01:40', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:40', '2025-07-21 20:01:40'),
(214, 3, 4, 9, 57, '二年级(5)班', 27, '2025-08-03', '10:56:00', '11:48:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 20:01:40', '2025-07-20 20:01:40'),
(215, 3, 2, 9, 57, '五年级(3)班', 38, '2025-08-22', '11:18:00', '12:38:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 20:01:40', '2025-07-21 20:01:40'),
(216, 3, 23, 9, 58, '五年级(1)班', 34, '2025-08-02', '12:30:00', '13:31:00', 1, '完成观察水和洗发液实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:40', '2025-07-18 20:01:40'),
(217, 3, 3, 9, 57, '八年级(3)班', 45, '2025-08-20', '14:21:00', '15:31:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:40', '2025-07-13 20:01:40'),
(218, 3, 24, 9, 58, '五年级(4)班', 25, '2025-08-08', '08:02:00', '08:54:00', 3, '完成溶解实验实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 20:01:40', '2025-07-20 20:01:40'),
(219, 3, 1, 9, 57, '四年级(3)班', 28, '2025-08-17', '09:45:00', '10:31:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 20:01:40', '2025-07-18 20:01:40'),
(220, 3, 3, 9, 57, '八年级(5)班', 32, '2025-08-07', '14:55:00', '16:09:00', 2, '完成氧气的制取和性质实验教学任务', 73, NULL, '2025-07-21 20:01:40', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 20:01:40', '2025-07-19 20:01:40'),
(221, 3, 21, 9, 58, '九年级(1)班', 27, '2025-08-16', '10:08:00', '10:50:00', 1, '完成平描物体实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:40', '2025-07-15 20:01:40'),
(222, 3, 26, 9, 58, '七年级(3)班', 42, '2025-07-24', '10:15:00', '10:56:00', 3, '完成观察动物实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:40', '2025-07-21 20:01:40'),
(223, 3, 23, 9, 58, '八年级(3)班', 30, '2025-08-06', '14:39:00', '15:46:00', 3, '完成观察水和洗发液实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:40', '2025-07-18 20:01:40'),
(224, 3, 25, 9, 57, '六年级(6)班', 33, '2025-08-15', '12:29:00', '13:21:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-18 20:01:40', '2025-07-15 20:01:40'),
(225, 3, 1, 9, 58, '五年级(6)班', 28, '2025-07-27', '15:04:00', '15:45:00', 2, '完成测量物体的长度实验教学任务', 64, NULL, '2025-07-21 20:01:40', '审核通过', NULL, 1, 'normal', NULL, '2025-07-21 20:01:40', '2025-07-17 20:01:40'),
(226, 3, 25, 9, 58, '五年级(6)班', 43, '2025-08-14', '08:52:00', '09:51:00', 1, '完成观察空气实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 20:01:40', '2025-07-15 20:01:40'),
(227, 3, 26, 9, 58, '二年级(3)班', 40, '2025-08-02', '10:29:00', '11:36:00', 3, '完成观察动物实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:40', '2025-07-19 20:01:40'),
(228, 3, 23, 9, 57, '四年级(2)班', 34, '2025-08-14', '14:24:00', '15:40:00', 1, '完成观察水和洗发液实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-20 20:01:40', '2025-07-21 20:01:40'),
(229, 3, 28, 10, 57, '四年级(5)班', 28, '2025-08-09', '12:14:00', '13:07:00', 2, '完成观察蜗牛实验教学任务', 80, NULL, '2025-07-20 20:01:40', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 20:01:40', '2025-07-18 20:01:40'),
(230, 3, 28, 10, 58, '一年级(4)班', 42, '2025-08-13', '14:23:00', '15:35:00', 1, '完成观察蜗牛实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-15 20:01:40', '2025-07-12 20:01:40'),
(231, 3, 4, 10, 57, '五年级(4)班', 36, '2025-08-20', '13:40:00', '14:58:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:41', '2025-07-14 20:01:41'),
(232, 3, 5, 10, 57, '高二(2)班', 44, '2025-07-28', '11:33:00', '12:50:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-15 20:01:41', '2025-07-18 20:01:41'),
(233, 3, 27, 10, 58, '八年级(1)班', 43, '2025-08-01', '08:41:00', '09:22:00', 2, '完成寻找小动物实验教学任务', 85, NULL, '2025-07-16 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 20:01:41', '2025-07-21 20:01:41'),
(234, 3, 23, 10, 58, '九年级(5)班', 29, '2025-08-03', '14:57:00', '15:38:00', 3, '完成观察水和洗发液实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:41', '2025-07-18 20:01:41'),
(235, 3, 26, 10, 58, '一年级(2)班', 42, '2025-07-28', '11:57:00', '13:16:00', 1, '完成观察动物实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-20 20:01:41', '2025-07-13 20:01:41'),
(236, 3, 29, 10, 58, '七年级(2)班', 38, '2025-08-18', '08:26:00', '09:09:00', 3, '完成饲养蜗牛实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:41', '2025-07-20 20:01:41'),
(237, 3, 31, 10, 58, '六年级(5)班', 25, '2025-07-27', '09:20:00', '10:16:00', 3, '完成给动物分类实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 20:01:41', '2025-07-15 20:01:41'),
(238, 3, 28, 10, 57, '五年级(2)班', 33, '2025-08-17', '14:55:00', '16:19:00', 2, '完成观察蜗牛实验教学任务', 61, NULL, '2025-07-21 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:41', '2025-07-17 20:01:41'),
(239, 3, 21, 10, 58, '二年级(4)班', 33, '2025-08-21', '12:55:00', '14:22:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:41', '2025-07-18 20:01:41'),
(240, 3, 25, 10, 57, '五年级(6)班', 27, '2025-08-15', '08:18:00', '09:27:00', 2, '完成观察空气实验教学任务', 63, NULL, '2025-07-16 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-18 20:01:41', '2025-07-19 20:01:41'),
(241, 3, 30, 10, 57, '高一(6)班', 45, '2025-08-03', '08:16:00', '09:31:00', 3, '完成观察鱼实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-19 20:01:41', '2025-07-15 20:01:41'),
(242, 3, 2, 10, 58, '一年级(3)班', 30, '2025-07-31', '12:12:00', '13:31:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-16 20:01:41', '2025-07-15 20:01:41'),
(243, 3, 4, 10, 57, '高二(2)班', 37, '2025-08-12', '15:42:00', '16:40:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-18 20:01:41', '2025-07-13 20:01:41'),
(244, 4, 29, 12, 59, '八年级(2)班', 39, '2025-08-07', '08:49:00', '09:44:00', 2, '完成饲养蜗牛实验教学任务', 77, NULL, '2025-07-20 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:41', '2025-07-16 20:01:41'),
(245, 4, 2, 12, 59, '七年级(4)班', 33, '2025-08-20', '11:31:00', '12:29:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 20:01:41', '2025-07-16 20:01:41'),
(246, 4, 27, 12, 60, '二年级(2)班', 39, '2025-07-24', '09:35:00', '10:26:00', 2, '完成寻找小动物实验教学任务', 73, NULL, '2025-07-16 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:41', '2025-07-21 20:01:41'),
(247, 4, 4, 12, 59, '高一(5)班', 42, '2025-08-06', '12:02:00', '13:32:00', 2, '完成观察植物细胞实验教学任务', 56, NULL, '2025-07-18 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 20:01:41', '2025-07-18 20:01:41'),
(248, 4, 28, 12, 59, '四年级(2)班', 33, '2025-08-20', '12:37:00', '13:52:00', 3, '完成观察蜗牛实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-18 20:01:41', '2025-07-15 20:01:41'),
(249, 4, 26, 12, 60, '八年级(4)班', 37, '2025-08-18', '15:35:00', '17:04:00', 2, '完成观察动物实验教学任务', 58, NULL, '2025-07-16 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 20:01:41', '2025-07-14 20:01:41'),
(250, 4, 1, 12, 60, '九年级(2)班', 25, '2025-08-12', '15:00:00', '15:55:00', 2, '完成测量物体的长度实验教学任务', 85, NULL, '2025-07-20 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-20 20:01:41', '2025-07-16 20:01:41'),
(251, 4, 2, 12, 59, '七年级(5)班', 30, '2025-08-11', '09:42:00', '10:33:00', 2, '完成测量重力加速度实验教学任务', 60, NULL, '2025-07-20 20:01:41', '审核通过', NULL, 1, 'normal', NULL, '2025-07-20 20:01:41', '2025-07-18 20:01:41'),
(252, 4, 25, 12, 60, '九年级(4)班', 38, '2025-08-09', '15:06:00', '16:06:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:41', '2025-07-17 20:01:41'),
(253, 4, 30, 12, 60, '高二(6)班', 35, '2025-08-13', '11:56:00', '13:01:00', 3, '完成观察鱼实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:41', '2025-07-18 20:01:41'),
(254, 4, 24, 12, 60, '四年级(2)班', 37, '2025-08-14', '14:28:00', '15:16:00', 2, '完成溶解实验实验教学任务', 62, NULL, '2025-07-20 20:01:42', '审核通过', NULL, 1, 'normal', NULL, '2025-07-15 20:01:42', '2025-07-18 20:01:42');
INSERT INTO `experiment_reservations` (`id`, `school_id`, `catalog_id`, `laboratory_id`, `teacher_id`, `class_name`, `student_count`, `reservation_date`, `start_time`, `end_time`, `status`, `remark`, `reviewer_id`, `batch_id`, `reviewed_at`, `review_remark`, `equipment_requirements`, `auto_borrow_equipment`, `priority`, `preparation_notes`, `created_at`, `updated_at`) VALUES
(255, 4, 25, 12, 59, '高三(1)班', 28, '2025-07-24', '10:51:00', '12:00:00', 4, '完成观察空气实验教学任务', 59, NULL, '2025-07-18 20:01:42', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:42', '2025-07-23 23:24:43'),
(256, 4, 22, 12, 60, '七年级(5)班', 27, '2025-07-24', '12:46:00', '13:59:00', 1, '完成给物体分类实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-16 20:01:42', '2025-07-18 20:01:42'),
(257, 4, 5, 12, 60, '高二(1)班', 38, '2025-07-24', '14:15:00', '15:10:00', 2, '完成验证牛顿第二定律实验教学任务', 59, NULL, '2025-07-23 23:12:32', NULL, NULL, 1, 'normal', NULL, '2025-07-21 20:01:42', '2025-07-23 23:12:32'),
(258, 4, 24, 12, 59, '高一(3)班', 37, '2025-07-27', '15:40:00', '16:28:00', 1, '完成溶解实验实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-18 20:01:42', '2025-07-14 20:01:42'),
(259, 4, 3, 13, 60, '五年级(2)班', 26, '2025-08-16', '13:14:00', '14:28:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:42', '2025-07-15 20:01:42'),
(260, 4, 30, 13, 60, '六年级(3)班', 38, '2025-08-10', '10:10:00', '11:35:00', 3, '完成观察鱼实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-21 20:01:42', '2025-07-12 20:01:42'),
(261, 4, 20, 13, 60, '七年级(1)班', 38, '2025-08-05', '09:29:00', '10:09:00', 2, '完成探轻重排序实验教学任务', 65, NULL, '2025-07-20 20:01:42', '审核通过', NULL, 1, 'normal', NULL, '2025-07-19 20:01:42', '2025-07-20 20:01:42'),
(262, 4, 23, 13, 59, '六年级(6)班', 35, '2025-08-20', '11:16:00', '11:59:00', 2, '完成观察水和洗发液实验教学任务', 73, NULL, '2025-07-19 20:01:42', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:42', '2025-07-15 20:01:42'),
(263, 4, 21, 13, 59, '三年级(1)班', 38, '2025-07-31', '09:32:00', '10:33:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:42', '2025-07-21 20:01:42'),
(264, 4, 24, 13, 60, '四年级(2)班', 33, '2025-08-05', '15:30:00', '16:53:00', 2, '完成溶解实验实验教学任务', 54, NULL, '2025-07-15 20:01:42', '审核通过', NULL, 1, 'normal', NULL, '2025-07-19 20:01:42', '2025-07-14 20:01:42'),
(265, 4, 4, 13, 60, '七年级(1)班', 25, '2025-08-01', '09:10:00', '09:54:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:42', '2025-07-15 20:01:42'),
(266, 4, 25, 13, 59, '高三(3)班', 26, '2025-08-20', '13:01:00', '14:01:00', 2, '完成观察空气实验教学任务', 63, NULL, '2025-07-15 20:01:42', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:42', '2025-07-12 20:01:42'),
(267, 4, 26, 13, 59, '一年级(3)班', 44, '2025-08-05', '13:30:00', '14:14:00', 3, '完成观察动物实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:42', '2025-07-15 20:01:42'),
(268, 4, 31, 13, 59, '二年级(2)班', 27, '2025-07-26', '11:54:00', '13:20:00', 3, '完成给动物分类实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:42', '2025-07-18 20:01:42'),
(269, 4, 30, 13, 60, '四年级(3)班', 33, '2025-08-09', '08:24:00', '09:30:00', 1, '完成观察鱼实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-19 20:01:42', '2025-07-17 20:01:42'),
(270, 4, 25, 13, 59, '七年级(5)班', 28, '2025-08-06', '15:30:00', '16:32:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:43', '2025-07-12 20:01:43'),
(271, 4, 31, 13, 60, '八年级(4)班', 34, '2025-07-24', '10:59:00', '12:47:00', 2, '完成给动物分类实验教学任务', 59, NULL, '2025-07-23 23:12:36', NULL, NULL, 1, 'normal', NULL, '2025-07-19 20:01:43', '2025-07-23 23:12:36'),
(272, 4, 3, 13, 60, '六年级(4)班', 37, '2025-08-22', '11:31:00', '12:51:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-17 20:01:43', '2025-07-20 20:01:43'),
(273, 4, 2, 13, 59, '六年级(2)班', 33, '2025-08-05', '10:37:00', '11:45:00', 2, '完成测量重力加速度实验教学任务', 73, NULL, '2025-07-19 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-18 20:01:43', '2025-07-12 20:01:43'),
(274, 5, 28, 15, 62, '九年级(5)班', 43, '2025-08-19', '08:36:00', '09:29:00', 2, '完成观察蜗牛实验教学任务', 86, NULL, '2025-07-17 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-18 20:01:43', '2025-07-13 20:01:43'),
(275, 5, 23, 15, 61, '七年级(1)班', 39, '2025-07-24', '15:51:00', '16:49:00', 2, '完成观察水和洗发液实验教学任务', 55, NULL, '2025-07-18 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 20:01:43', '2025-07-15 20:01:43'),
(276, 5, 30, 15, 62, '五年级(3)班', 28, '2025-08-06', '14:08:00', '14:56:00', 1, '完成观察鱼实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-19 20:01:43', '2025-07-17 20:01:43'),
(277, 5, 21, 15, 61, '四年级(2)班', 39, '2025-07-24', '11:17:00', '12:23:00', 1, '完成平描物体实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-13 20:01:43', '2025-07-13 20:01:43'),
(278, 5, 31, 15, 62, '七年级(3)班', 40, '2025-07-27', '12:43:00', '13:36:00', 1, '完成给动物分类实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-19 20:01:43', '2025-07-17 20:01:43'),
(279, 5, 30, 15, 62, '七年级(6)班', 37, '2025-08-18', '15:58:00', '17:26:00', 2, '完成观察鱼实验教学任务', 53, NULL, '2025-07-15 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-12 20:01:43', '2025-07-20 20:01:43'),
(280, 5, 3, 15, 61, '九年级(2)班', 28, '2025-08-04', '10:37:00', '11:20:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 20:01:43', '2025-07-19 20:01:43'),
(281, 5, 19, 15, 62, '四年级(4)班', 29, '2025-08-19', '10:13:00', '11:40:00', 3, '完成观察物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:43', '2025-07-19 20:01:43'),
(282, 5, 21, 15, 62, '八年级(5)班', 30, '2025-08-17', '10:28:00', '11:21:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 20:01:43', '2025-07-13 20:01:43'),
(283, 5, 3, 15, 62, '五年级(6)班', 36, '2025-08-03', '11:43:00', '12:40:00', 2, '完成氧气的制取和性质实验教学任务', 73, NULL, '2025-07-16 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-21 20:01:43', '2025-07-12 20:01:43'),
(284, 5, 25, 15, 61, '二年级(2)班', 27, '2025-08-11', '10:01:00', '10:48:00', 3, '完成观察空气实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:43', '2025-07-20 20:01:43'),
(285, 5, 26, 15, 62, '四年级(3)班', 33, '2025-08-01', '14:02:00', '14:46:00', 2, '完成观察动物实验教学任务', 75, NULL, '2025-07-20 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-16 20:01:43', '2025-07-20 20:01:43'),
(286, 5, 25, 15, 62, '高一(5)班', 40, '2025-08-06', '10:23:00', '11:49:00', 2, '完成观察空气实验教学任务', 74, NULL, '2025-07-16 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-17 20:01:43', '2025-07-15 20:01:43'),
(287, 5, 25, 15, 61, '五年级(2)班', 39, '2025-07-24', '10:49:00', '11:51:00', 1, '完成观察空气实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-12 20:01:43', '2025-07-12 20:01:43'),
(288, 5, 22, 15, 62, '一年级(6)班', 37, '2025-07-29', '15:55:00', '17:20:00', 2, '完成给物体分类实验教学任务', 87, NULL, '2025-07-16 20:01:43', '审核通过', NULL, 1, 'normal', NULL, '2025-07-14 20:01:43', '2025-07-14 20:01:43'),
(289, 5, 2, 16, 61, '六年级(5)班', 42, '2025-08-22', '13:39:00', '15:00:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:43', '2025-07-14 20:01:43'),
(290, 5, 22, 16, 61, '五年级(5)班', 44, '2025-08-10', '10:14:00', '11:43:00', 3, '完成给物体分类实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-12 20:01:43', '2025-07-21 20:01:43'),
(291, 5, 2, 16, 61, '高二(2)班', 40, '2025-08-03', '10:37:00', '11:44:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-14 20:01:44', '2025-07-15 20:01:44'),
(292, 5, 22, 16, 61, '七年级(2)班', 35, '2025-08-10', '08:27:00', '09:57:00', 1, '完成给物体分类实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 20:01:44', '2025-07-15 20:01:44'),
(293, 5, 19, 16, 61, '高三(3)班', 32, '2025-08-19', '12:54:00', '14:10:00', 3, '完成观察物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-14 20:01:44', '2025-07-17 20:01:44'),
(294, 5, 31, 16, 61, '二年级(4)班', 42, '2025-07-27', '10:44:00', '12:03:00', 3, '完成给动物分类实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:44', '2025-07-14 20:01:44'),
(295, 5, 2, 16, 62, '高一(5)班', 42, '2025-08-15', '09:10:00', '10:22:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-20 20:01:44', '2025-07-15 20:01:44'),
(296, 5, 21, 16, 62, '高二(1)班', 28, '2025-08-17', '08:58:00', '10:05:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-21 20:01:44', '2025-07-20 20:01:44'),
(297, 5, 3, 16, 62, '一年级(3)班', 32, '2025-08-14', '08:14:00', '09:32:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-13 20:01:44', '2025-07-15 20:01:44'),
(298, 5, 23, 16, 61, '八年级(2)班', 34, '2025-08-02', '12:37:00', '13:45:00', 1, '完成观察水和洗发液实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-17 20:01:44', '2025-07-15 20:01:44'),
(299, 5, 20, 16, 61, '五年级(2)班', 44, '2025-08-14', '08:53:00', '09:52:00', 3, '完成探轻重排序实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-20 20:01:44', '2025-07-18 20:01:44'),
(300, 5, 21, 16, 61, '三年级(4)班', 31, '2025-07-29', '14:49:00', '15:59:00', 2, '完成平描物体实验教学任务', 54, NULL, '2025-07-15 20:01:44', '审核通过', NULL, 1, 'normal', NULL, '2025-07-13 20:01:44', '2025-07-12 20:01:44'),
(301, 5, 21, 16, 62, '高三(5)班', 34, '2025-08-08', '10:34:00', '11:27:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:44', '2025-07-20 20:01:44'),
(302, 5, 4, 16, 62, '一年级(5)班', 44, '2025-08-21', '11:06:00', '11:46:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, NULL, NULL, 1, 'normal', NULL, '2025-07-15 20:01:44', '2025-07-15 20:01:44'),
(303, 5, 21, 16, 62, '四年级(6)班', 41, '2025-08-07', '10:19:00', '11:39:00', 3, '完成平描物体实验教学任务', NULL, NULL, NULL, '实验室设备维护中', NULL, 1, 'normal', NULL, '2025-07-15 20:01:44', '2025-07-15 20:01:44'),
(316, 18, 1, 88, 29, '二年级1', 30, '2025-07-25', '09:12:00', '10:12:00', 2, '智能预约：测量物体的长度', 42, NULL, '2025-07-23 04:57:20', NULL, '[{\"equipment_id\":25,\"equipment_name\":\"\\u751f\\u7269\\u663e\\u5fae\\u955cXSP-2CA\",\"equipment_code\":\"BIO0180001\",\"required_quantity\":12,\"min_quantity\":6,\"is_required\":true,\"calculation_type\":\"per_group\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u751f\\u7269\\u663e\\u5fae\\u955cXSP-2CA\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":1,\"shortage\":11},{\"equipment_id\":5,\"equipment_name\":\"\\u5b66\\u751f\\u7528\\u751f\\u7269\\u663e\\u5fae\\u955c\",\"equipment_code\":\"BIO00220002\",\"required_quantity\":16,\"min_quantity\":8,\"is_required\":false,\"calculation_type\":\"per_group\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u5b66\\u751f\\u7528\\u751f\\u7269\\u663e\\u5fae\\u955c\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":1,\"shortage\":15},{\"equipment_id\":27,\"equipment_name\":\"\\u7535\\u5b50\\u5929\\u5e73\",\"equipment_code\":\"BAL0180001\",\"required_quantity\":10,\"min_quantity\":5,\"is_required\":false,\"calculation_type\":\"per_group\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u7535\\u5b50\\u5929\\u5e73\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":1,\"shortage\":9}]', 1, 'normal', NULL, '2025-07-23 04:26:39', '2025-07-23 04:57:20'),
(317, 1, 26, 2, 53, '11', 40, '2025-07-25', '08:46:00', '09:45:00', 4, NULL, 90, NULL, '2025-07-24 05:48:21', NULL, NULL, 1, 'normal', NULL, '2025-07-24 05:46:20', '2025-07-24 23:37:39'),
(318, 1, 4, 3, 90, '七年级（1）班', 30, '2025-07-26', '09:00:00', '10:31:00', 2, NULL, 44, NULL, '2025-07-24 20:40:05', NULL, NULL, 1, 'normal', NULL, '2025-07-24 20:35:43', '2025-07-24 21:25:35'),
(319, 1, 4, 3, 90, '七年级（1）班', 30, '2025-07-25', '15:21:00', '16:21:00', 4, '今天 202507251522预约', 90, NULL, '2025-07-24 23:23:45', '此实验由本人审核通过，不是管理员审核', NULL, 1, 'normal', NULL, '2025-07-24 23:22:38', '2025-07-24 23:30:20'),
(320, 15, 1, 79, 95, '一年级（1）', 30, '2025-07-25', '14:52:00', '16:52:00', 4, '智能预约：测量物体的长度', 95, NULL, '2025-07-24 23:59:05', '此实验审核是由本人自己审核通过，不是由管理员', '[{\"equipment_id\":29,\"equipment_name\":\"\\u5929\\u5e73\",\"equipment_code\":\"EQ_002\",\"required_quantity\":2,\"min_quantity\":1,\"is_required\":true,\"calculation_type\":\"fixed\",\"usage_note\":\"\\u7528\\u4e8e\\u6d4b\\u91cf\\u7269\\u4f53\\u8d28\\u91cf\",\"safety_note\":null,\"available_quantity\":0,\"shortage\":2},{\"equipment_id\":30,\"equipment_name\":\"\\u91cf\\u7b52\",\"equipment_code\":\"EQ_003\",\"required_quantity\":4,\"min_quantity\":2,\"is_required\":true,\"calculation_type\":\"fixed\",\"usage_note\":\"\\u7528\\u4e8e\\u6d4b\\u91cf\\u6db2\\u4f53\\u4f53\\u79ef\",\"safety_note\":null,\"available_quantity\":0,\"shortage\":4}]', 1, 'high', '此实验为了测试实验照片是否能够上传', '2025-07-24 23:54:21', '2025-07-25 00:00:13'),
(321, 15, 2, 80, 95, '二年级(2)', 30, '2025-07-25', '16:30:00', '18:00:00', 4, '智能预约：测量重力加速度', 95, NULL, '2025-07-25 00:25:21', '此实验测试能否上传实验照片', '[{\"equipment_id\":7,\"equipment_name\":\"\\u7535\\u5b50\\u5929\\u5e73\",\"equipment_code\":\"BAL0010001\",\"required_quantity\":90,\"min_quantity\":30,\"is_required\":true,\"calculation_type\":\"per_student\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u7535\\u5b50\\u5929\\u5e73\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":0,\"shortage\":90},{\"equipment_id\":10,\"equipment_name\":\"\\u5206\\u6790\\u5929\\u5e73\",\"equipment_code\":\"BAL00110002\",\"required_quantity\":90,\"min_quantity\":30,\"is_required\":false,\"calculation_type\":\"per_student\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u5206\\u6790\\u5929\\u5e73\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":0,\"shortage\":90},{\"equipment_id\":13,\"equipment_name\":\"\\u6570\\u5b57\\u4e07\\u7528\\u8868\",\"equipment_code\":\"MUL0010001\",\"required_quantity\":2,\"min_quantity\":1,\"is_required\":false,\"calculation_type\":\"fixed\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u6570\\u5b57\\u4e07\\u7528\\u8868\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":0,\"shortage\":2}]', 1, 'high', '测试是否能上传实验照片', '2025-07-25 00:24:53', '2025-07-25 00:26:10'),
(322, 15, 2, 81, 95, '一年级1', 30, '2025-07-25', '14:33:00', '17:33:00', 4, '智能预约：测量重力加速度', 95, NULL, '2025-07-25 00:34:44', NULL, '[{\"equipment_id\":7,\"equipment_name\":\"\\u7535\\u5b50\\u5929\\u5e73\",\"equipment_code\":\"BAL0010001\",\"required_quantity\":90,\"min_quantity\":30,\"is_required\":true,\"calculation_type\":\"per_student\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u7535\\u5b50\\u5929\\u5e73\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":0,\"shortage\":90},{\"equipment_id\":10,\"equipment_name\":\"\\u5206\\u6790\\u5929\\u5e73\",\"equipment_code\":\"BAL00110002\",\"required_quantity\":90,\"min_quantity\":30,\"is_required\":false,\"calculation_type\":\"per_student\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u5206\\u6790\\u5929\\u5e73\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":0,\"shortage\":90},{\"equipment_id\":13,\"equipment_name\":\"\\u6570\\u5b57\\u4e07\\u7528\\u8868\",\"equipment_code\":\"MUL0010001\",\"required_quantity\":2,\"min_quantity\":1,\"is_required\":false,\"calculation_type\":\"fixed\",\"usage_note\":\"\\u5b9e\\u9a8c\\u7528\\u5668\\u6750 - \\u6570\\u5b57\\u4e07\\u7528\\u8868\",\"safety_note\":\"\\u6ce8\\u610f\\u5b89\\u5168\\u4f7f\\u7528\",\"available_quantity\":0,\"shortage\":2}]', 1, 'high', NULL, '2025-07-25 00:34:14', '2025-07-25 00:35:02'),
(323, 15, 24, 79, 95, '一年级（1）', 35, '2025-07-31', '08:28:00', '09:30:00', 4, NULL, 95, NULL, '2025-07-30 04:34:50', NULL, NULL, 1, 'normal', NULL, '2025-07-30 04:31:31', '2025-07-30 04:35:37'),
(324, 15, 30, 79, 95, '一年级（2）', 40, '2025-08-05', '16:10:00', '17:10:00', 4, NULL, 46, NULL, '2025-08-04 00:11:02', NULL, NULL, 1, 'normal', NULL, '2025-08-04 00:10:53', '2025-08-04 00:11:28');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_reservation_templates`
--

CREATE TABLE `experiment_reservation_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
  `grade` tinyint(4) NOT NULL COMMENT '年级',
  `semester` tinyint(4) NOT NULL COMMENT '学期：1上学期 2下学期',
  `template_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '模板数据：包含实验安排、时间等' CHECK (json_valid(`template_data`)),
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT '创建人',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `description` text DEFAULT NULL COMMENT '模板描述',
  `use_count` int(11) NOT NULL DEFAULT 0 COMMENT '使用次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `experiment_works`
--

CREATE TABLE `experiment_works` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `record_id` bigint(20) UNSIGNED NOT NULL COMMENT '实验记录ID',
  `student_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '学生ID（如果是学生作品）',
  `title` varchar(200) NOT NULL COMMENT '作品标题',
  `description` text DEFAULT NULL COMMENT '作品描述',
  `type` enum('photo','video','document','other') NOT NULL COMMENT '作品类型',
  `file_path` varchar(255) NOT NULL COMMENT '文件路径',
  `file_name` varchar(255) NOT NULL COMMENT '原始文件名',
  `file_size` varchar(255) NOT NULL COMMENT '文件大小',
  `mime_type` varchar(255) NOT NULL COMMENT '文件类型',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '文件元数据' CHECK (json_valid(`metadata`)),
  `quality_score` tinyint(4) DEFAULT NULL COMMENT '质量评分(1-5)',
  `teacher_comment` text DEFAULT NULL COMMENT '教师评语',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否精选作品',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否公开展示',
  `uploaded_by` bigint(20) UNSIGNED NOT NULL COMMENT '上传人',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `laboratories`
--

CREATE TABLE `laboratories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `name` varchar(100) NOT NULL COMMENT '实验室名称',
  `code` varchar(50) NOT NULL COMMENT '实验室编号',
  `type` int(11) DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '实验室类型ID',
  `location` varchar(200) DEFAULT NULL COMMENT '位置',
  `area` decimal(8,2) DEFAULT NULL COMMENT '面积(平方米)',
  `capacity` int(11) NOT NULL DEFAULT 50 COMMENT '容纳人数',
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '管理员ID',
  `equipment_list` text DEFAULT NULL COMMENT '设备清单',
  `safety_rules` text DEFAULT NULL COMMENT '安全规则',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1正常 2维修 0停用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `laboratories`
--

INSERT INTO `laboratories` (`id`, `school_id`, `name`, `code`, `type`, `type_id`, `location`, `area`, `capacity`, `manager_id`, `equipment_list`, `safety_rules`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(2, 1, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(3, 1, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(4, 2, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(5, 2, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(6, 2, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(7, 2, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(8, 2, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(9, 3, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(10, 3, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(11, 3, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(12, 4, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(13, 4, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(14, 4, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(15, 5, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:22', '2025-07-18 15:22:22'),
(16, 5, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 15:22:23', '2025-07-18 15:22:23'),
(17, 5, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:23', '2025-07-18 15:22:23'),
(18, 6, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 15:22:23', '2025-07-18 15:22:23'),
(19, 6, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 15:22:23', '2025-07-18 15:22:23'),
(20, 6, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:23', '2025-07-18 15:22:23'),
(21, 1, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(22, 1, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(23, 1, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(24, 2, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(25, 2, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(26, 2, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(27, 2, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(28, 2, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(29, 3, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(30, 3, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(31, 3, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(32, 4, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(33, 4, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(34, 4, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(35, 5, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(36, 5, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(37, 5, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(38, 6, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(39, 6, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(40, 6, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(41, 7, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:41', '2025-07-22 21:38:41'),
(42, 7, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(43, 7, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(44, 7, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(45, 7, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(46, 8, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(47, 8, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(48, 8, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(49, 8, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(50, 8, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(51, 9, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(52, 9, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(53, 9, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(54, 9, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(55, 9, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(56, 10, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(57, 10, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(58, 10, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(59, 10, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(60, 10, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(61, 11, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(62, 11, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(63, 11, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(64, 11, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(65, 11, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(66, 12, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(67, 12, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(68, 12, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(69, 12, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(70, 12, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(71, 13, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(72, 13, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:42', '2025-07-22 21:38:42'),
(73, 13, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(74, 13, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(75, 13, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(76, 14, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(77, 14, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(78, 14, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(79, 15, '东城物理实验室1', 'PHY_LAB_01', NULL, 1, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:43', '2025-07-24 23:08:09'),
(80, 15, '东城化学实验室1', 'CHE_LAB_01', NULL, 2, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:43', '2025-07-24 23:07:37'),
(81, 15, '东城生物实验室1', 'BIO_LAB_01', NULL, 3, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:43', '2025-07-24 23:08:20'),
(82, 16, '物理实验室1', 'PHY_LAB_01', NULL, 1, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:43', '2025-07-24 23:08:55'),
(83, 16, '化学实验室1', 'CHE_LAB_01', NULL, 2, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:43', '2025-07-24 23:08:43'),
(84, 16, '生物实验室1', 'BIO_LAB_01', NULL, 3, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:43', '2025-07-24 23:09:00'),
(85, 17, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(86, 17, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(87, 17, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:43', '2025-07-22 21:38:43'),
(88, 18, '物理实验室1', 'PHY_LAB_01', NULL, 1, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:44', '2025-07-23 01:56:18'),
(89, 18, '化学实验室1', 'CHE_LAB_01', NULL, 2, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:44', '2025-07-23 01:56:03'),
(90, 18, '生物实验室1', 'BIO_LAB_01', NULL, 3, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:44', '2025-07-23 01:56:29'),
(91, 19, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(92, 19, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(93, 19, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(94, 20, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(95, 20, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(96, 20, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(97, 21, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(98, 21, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44'),
(99, 21, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-22 21:38:44', '2025-07-22 21:38:44');

-- --------------------------------------------------------

--
-- 表的结构 `laboratory_types`
--

CREATE TABLE `laboratory_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '类型名称',
  `code` varchar(50) NOT NULL COMMENT '类型代码',
  `description` varchar(500) DEFAULT NULL COMMENT '类型描述',
  `icon` varchar(50) DEFAULT NULL COMMENT '图标名称',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色代码',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `laboratory_types`
--

INSERT INTO `laboratory_types` (`id`, `name`, `code`, `description`, `icon`, `color`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, '物理实验室', 'PHYSICS', '用于物理实验教学的专用实验室', 'Physics', '#409EFF', 1, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(2, '化学实验室', 'CHEMISTRY', '用于化学实验教学的专用实验室', 'Chemistry', '#67C23A', 2, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(3, '生物实验室', 'BIOLOGY', '用于生物实验教学的专用实验室', 'Biology', '#E6A23C', 3, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(4, '综合实验室', 'COMPREHENSIVE', '用于多学科综合实验教学的实验室', 'Comprehensive', '#F56C6C', 4, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(5, '科学实验室', 'SCIENCE', '用于小学科学实验教学的实验室', 'Science', '#909399', 5, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(6, '音乐实验室', 'MUSIC', '用于音乐教学和实践的专用教室', 'Music', '#9C27B0', 6, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(7, '美术实验室', 'ART', '用于美术教学和创作的专用教室', 'Art', '#FF5722', 7, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(8, '体育器材室', 'SPORTS', '用于体育器材存放和管理的专用场所', 'Sports', '#4CAF50', 8, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(9, 'STEAM实验室', 'STEAM', '用于科学、技术、工程、艺术、数学综合教育的实验室', 'Steam', '#795548', 9, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13'),
(10, '创客实验室', 'MAKER', '用于创新制作和动手实践的实验室', 'Maker', '#607D8B', 10, 1, '2025-07-18 15:22:13', '2025-07-18 15:22:13');

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(31, '0001_01_01_000000_create_users_table', 1),
(32, '0001_01_01_000001_create_cache_table', 1),
(33, '0001_01_01_000002_create_jobs_table', 1),
(34, '2025_01_17_000001_create_laboratory_types_table', 1),
(35, '2025_01_17_000002_create_equipment_standards_table', 1),
(36, '2025_07_13_095051_create_administrative_regions_table', 1),
(37, '2025_07_13_095149_create_schools_table', 1),
(38, '2025_07_13_095215_create_roles_table', 1),
(39, '2025_07_13_095239_create_user_roles_table', 1),
(40, '2025_07_13_100001_create_subjects_table', 1),
(41, '2025_07_13_100002_create_experiment_catalogs_table', 1),
(42, '2025_07_13_100003_create_laboratories_table', 1),
(43, '2025_07_13_100004_create_experiment_reservations_table', 1),
(44, '2025_07_13_100005_create_experiment_records_table', 1),
(45, '2025_07_13_100006_create_equipment_categories_table', 1),
(46, '2025_07_13_100007_create_equipments_table', 1),
(47, '2025_07_13_100008_create_equipment_borrows_table', 1),
(48, '2025_07_13_100009_create_equipment_maintenances_table', 1),
(49, '2025_07_13_100010_create_equipment_qrcodes_table', 1),
(50, '2025_07_13_100010_create_statistics_summary_table', 1),
(51, '2025_07_13_100011_create_equipment_operation_logs_table', 1),
(52, '2025_07_13_100011_create_system_configs_table', 1),
(53, '2025_07_13_100012_create_equipment_attachments_table', 1),
(54, '2025_07_13_100012_create_operation_logs_table', 1),
(55, '2025_07_13_100013_add_type_id_to_laboratories_table', 1),
(56, '2025_07_13_100014_update_laboratories_type_field', 1),
(57, '2025_07_14_000001_add_user_management_fields_to_users_table', 1),
(58, '2025_07_14_034529_create_role_permissions_table', 1),
(59, '2025_07_15_000001_add_organization_fields_to_users_table', 1),
(60, '2025_07_17_020000_add_contact_fields_to_administrative_regions_table', 1),
(61, '2025_07_18_150000_create_teaching_equipment_standards_table', 2),
(64, '2025_07_20_125815_create_textbook_versions_table', 3),
(65, '2025_07_20_125826_create_textbook_chapters_table', 4),
(66, '2025_07_20_140028_create_experiment_catalog_deletions_table', 5),
(67, '2025_07_20_140111_create_experiment_catalog_permissions_table', 6),
(68, '2025_07_20_140151_create_experiment_catalog_versions_table', 7),
(69, '2025_01_21_000001_create_experiment_reservation_enhancements', 8),
(70, '2025_07_25_053118_fix_experiment_records_completion_rate', 9),
(71, '2025_07_26_000001_create_experiment_requirements_config_table', 10),
(72, '2025_07_26_000002_create_school_experiment_catalog_selections_table', 11),
(73, '2025_07_26_000003_create_experiment_catalog_delete_permissions_table', 12),
(74, '2025_07_26_000004_create_experiment_alert_config_table', 13),
(75, '2025_07_26_000005_create_experiment_alerts_table', 14),
(76, '2025_07_26_000006_create_experiment_monitoring_statistics_table', 15),
(77, '2025_07_27_084418_add_missing_fields_to_users_and_schools_tables', 16),
(78, '2025_01_28_000001_create_school_experiment_catalog_configs_table', 17),
(79, '2025_01_28_000002_create_experiment_catalog_completion_baselines_table', 18),
(80, '2025_01_28_000003_enhance_experiment_catalogs_table', 19),
(81, '2025_01_28_000004_migrate_school_catalog_selections_data', 20),
(82, '2025_07_18_150001_update_equipment_standards_table', 21),
(83, '2025_07_28_create_school_classes_table', 22),
(84, '2025_07_28_create_school_teachers_table', 23),
(85, '2025_07_28_create_textbook_version_assignments_table', 24);

-- --------------------------------------------------------

--
-- 表的结构 `operation_logs`
--

CREATE TABLE `operation_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '操作用户ID',
  `module` varchar(50) NOT NULL COMMENT '操作模块',
  `action` varchar(50) NOT NULL COMMENT '操作动作',
  `description` text DEFAULT NULL COMMENT '操作描述',
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '请求数据' CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '响应数据' CHECK (json_valid(`response_data`)),
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text DEFAULT NULL COMMENT '用户代理',
  `execution_time` int(11) DEFAULT NULL COMMENT '执行时间(毫秒)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `reservation_batches`
--

CREATE TABLE `reservation_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_name` varchar(100) NOT NULL COMMENT '批次名称',
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
  `grade` tinyint(4) NOT NULL COMMENT '年级',
  `semester` tinyint(4) NOT NULL COMMENT '学期',
  `start_date` date NOT NULL COMMENT '开始日期',
  `end_date` date NOT NULL COMMENT '结束日期',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT '创建人（备课组长）',
  `status` enum('draft','submitted','approved','rejected','completed') NOT NULL DEFAULT 'draft' COMMENT '状态',
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '审核人',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `review_remark` text DEFAULT NULL COMMENT '审核备注',
  `total_reservations` int(11) NOT NULL DEFAULT 0 COMMENT '总预约数',
  `completed_reservations` int(11) NOT NULL DEFAULT 0 COMMENT '已完成预约数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `reservation_conflict_logs`
--

CREATE TABLE `reservation_conflict_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL COMMENT '预约ID',
  `conflict_type` enum('time','equipment','capacity','teacher') NOT NULL COMMENT '冲突类型',
  `conflict_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '冲突详情' CHECK (json_valid(`conflict_details`)),
  `severity` enum('low','medium','high') NOT NULL DEFAULT 'medium' COMMENT '严重程度',
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已解决',
  `resolved_at` timestamp NULL DEFAULT NULL COMMENT '解决时间',
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '解决人',
  `resolution_note` text DEFAULT NULL COMMENT '解决说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `reservation_conflict_logs`
--

INSERT INTO `reservation_conflict_logs` (`id`, `reservation_id`, `conflict_type`, `conflict_details`, `severity`, `is_resolved`, `resolved_at`, `resolved_by`, `resolution_note`, `created_at`, `updated_at`) VALUES
(1, 321, 'teacher', '[{\"reservation_id\":320,\"experiment_name\":\"\\u6d4b\\u91cf\\u7269\\u4f53\\u7684\\u957f\\u5ea6\",\"laboratory_name\":\"\\u4e1c\\u57ce\\u7269\\u7406\\u5b9e\\u9a8c\\u5ba41\",\"time_slot\":\"14:52 - 16:52\"}]', 'high', 0, NULL, NULL, NULL, '2025-07-25 00:24:53', '2025-07-25 00:24:53'),
(2, 322, 'teacher', '[{\"reservation_id\":320,\"experiment_name\":\"\\u6d4b\\u91cf\\u7269\\u4f53\\u7684\\u957f\\u5ea6\",\"laboratory_name\":\"\\u4e1c\\u57ce\\u7269\\u7406\\u5b9e\\u9a8c\\u5ba41\",\"time_slot\":\"14:52 - 16:52\"},{\"reservation_id\":321,\"experiment_name\":\"\\u6d4b\\u91cf\\u91cd\\u529b\\u52a0\\u901f\\u5ea6\",\"laboratory_name\":\"\\u4e1c\\u57ce\\u5316\\u5b66\\u5b9e\\u9a8c\\u5ba41\",\"time_slot\":\"16:30 - 18:00\"}]', 'high', 0, NULL, NULL, NULL, '2025-07-25 00:34:14', '2025-07-25 00:34:14');

-- --------------------------------------------------------

--
-- 表的结构 `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `code` varchar(50) NOT NULL COMMENT '角色代码',
  `level` tinyint(4) NOT NULL COMMENT '角色级别：1省级 2市级 3区县级 4学区级 5学校级',
  `description` text DEFAULT NULL COMMENT '角色描述',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `roles`
--

INSERT INTO `roles` (`id`, `name`, `code`, `level`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, '省级管理员', 'province_admin', 1, '省级系统管理员，拥有全省数据管理权限', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(2, '省级教研员', 'province_researcher', 1, '省级教研员，负责全省实验教学研究和指导', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(3, '市级管理员', 'city_admin', 2, '市级系统管理员，管理本市实验教学数据', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(4, '市级教研员', 'city_researcher', 2, '市级教研员，负责本市实验教学研究和指导', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(5, '区县管理员', 'county_admin', 3, '区县级系统管理员，管理本区县实验教学数据', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(6, '区县教研员', 'county_researcher', 3, '区县级教研员，负责本区县实验教学研究和指导', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(7, '学区管理员', 'district_admin', 4, '学区管理员，管理学区内学校实验教学数据', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(8, '校长', 'school_principal', 5, '学校校长，拥有本校所有数据查看权限', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(9, '教务主任', 'school_dean', 5, '教务主任，负责学校实验教学管理', 1, '2025-07-18 15:17:54', '2025-07-18 15:17:54'),
(10, '实验员', 'school_experimenter', 5, '实验员，负责实验室管理和实验教学记录', 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(11, '任课教师', 'school_teacher', 5, '任课教师，可查看和录入实验教学数据', 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(12, '学校管理员', 'school_admin', 5, NULL, 1, '2025-07-18 15:31:17', '2025-07-18 15:31:17'),
(13, '学生', 'student', 5, '学生', 1, '2025-07-18 16:24:10', '2025-07-18 16:24:10');

-- --------------------------------------------------------

--
-- 表的结构 `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_code` varchar(100) NOT NULL COMMENT '权限代码',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_code`, `created_at`, `updated_at`) VALUES
(52, 2, 'experiment', '2025-07-27 18:35:35', '2025-07-27 18:35:35'),
(53, 2, 'experiment.catalog', '2025-07-27 18:35:35', '2025-07-27 18:35:35'),
(54, 2, 'experiment.booking', '2025-07-27 18:35:36', '2025-07-27 18:35:36'),
(55, 2, 'experiment.record', '2025-07-27 18:35:36', '2025-07-27 18:35:36'),
(56, 2, 'equipment', '2025-07-27 18:35:36', '2025-07-27 18:35:36'),
(57, 2, 'equipment.list', '2025-07-27 18:35:36', '2025-07-27 18:35:36'),
(58, 2, 'laboratory_type', '2025-07-27 18:35:36', '2025-07-27 18:35:36'),
(59, 2, 'laboratory_type.list', '2025-07-27 18:35:36', '2025-07-27 18:35:36'),
(60, 2, 'basic.equipment_standard.view', '2025-07-27 18:35:36', '2025-08-02 19:56:34'),
(108, 4, 'experiment', '2025-07-27 18:35:37', '2025-07-27 18:35:37'),
(109, 4, 'experiment.catalog', '2025-07-27 18:35:37', '2025-07-27 18:35:37'),
(110, 4, 'experiment.booking', '2025-07-27 18:35:37', '2025-07-27 18:35:37'),
(111, 4, 'experiment.record', '2025-07-27 18:35:37', '2025-07-27 18:35:37'),
(112, 4, 'equipment', '2025-07-27 18:35:37', '2025-07-27 18:35:37'),
(113, 4, 'equipment.list', '2025-07-27 18:35:38', '2025-07-27 18:35:38'),
(114, 4, 'laboratory_type', '2025-07-27 18:35:38', '2025-07-27 18:35:38'),
(115, 4, 'laboratory_type.list', '2025-07-27 18:35:38', '2025-07-27 18:35:38'),
(116, 4, 'basic.equipment_standard.view', '2025-07-27 18:35:38', '2025-08-02 19:56:34'),
(165, 6, 'experiment', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(166, 6, 'experiment.catalog', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(167, 6, 'experiment.booking', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(168, 6, 'experiment.record', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(169, 6, 'equipment', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(170, 6, 'equipment.list', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(171, 6, 'laboratory_type', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(172, 6, 'laboratory_type.list', '2025-07-27 18:35:40', '2025-07-27 18:35:40'),
(173, 6, 'basic.equipment_standard.view', '2025-07-27 18:35:40', '2025-08-02 19:56:34'),
(200, 8, 'experiment', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(201, 8, 'experiment.catalog', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(202, 8, 'experiment.booking', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(203, 8, 'experiment.record', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(204, 8, 'equipment', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(205, 8, 'equipment.list', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(206, 8, 'laboratory_type', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(207, 8, 'laboratory_type.list', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(208, 8, 'basic.equipment_standard.view', '2025-07-27 18:35:41', '2025-08-02 19:56:34'),
(210, 9, 'experiment', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(211, 9, 'experiment.catalog', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(212, 9, 'experiment.booking', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(213, 9, 'experiment.record', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(214, 9, 'equipment', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(215, 9, 'equipment.list', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(216, 9, 'equipment.borrow', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(217, 9, 'equipment.maintenance', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(218, 9, 'laboratory_type', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(219, 9, 'laboratory_type.list', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(220, 9, 'basic.equipment_standard.view', '2025-07-27 18:35:41', '2025-08-02 19:56:34'),
(222, 10, 'experiment', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(223, 10, 'experiment.catalog', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(224, 10, 'experiment.booking', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(225, 10, 'experiment.record', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(226, 10, 'equipment', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(227, 10, 'equipment.list', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(228, 10, 'equipment.borrow', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(229, 10, 'equipment.maintenance', '2025-07-27 18:35:41', '2025-07-27 18:35:41'),
(230, 10, 'laboratory_type', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(231, 10, 'laboratory_type.list', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(232, 10, 'basic.equipment_standard.view', '2025-07-27 18:35:42', '2025-08-02 19:56:34'),
(234, 11, 'experiment', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(235, 11, 'experiment.catalog', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(236, 11, 'experiment.booking', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(237, 11, 'experiment.record', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(238, 11, 'equipment', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(239, 11, 'equipment.list', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(240, 11, 'equipment.borrow', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(241, 11, 'laboratory_type', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(242, 11, 'laboratory_type.list', '2025-07-27 18:35:42', '2025-07-27 18:35:42'),
(243, 11, 'basic.equipment_standard.view', '2025-07-27 18:35:42', '2025-08-02 19:56:34'),
(267, 1, 'user', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(268, 1, 'user.list', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(269, 1, 'user.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(270, 1, 'user.update', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(271, 1, 'user.edit', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(272, 1, 'user.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(273, 1, 'user.export', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(274, 1, 'user.reset_password', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(275, 1, 'role', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(276, 1, 'role.list', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(277, 1, 'role.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(278, 1, 'role.update', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(279, 1, 'role.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(280, 1, 'experiment', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(281, 1, 'experiment.catalog', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(282, 1, 'experiment.catalog.view', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(283, 1, 'experiment.catalog.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(284, 1, 'experiment.catalog.edit', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(285, 1, 'experiment.catalog.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(286, 1, 'experiment.catalog.copy', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(287, 1, 'experiment.catalog.manage_level', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(288, 1, 'experiment.booking', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(289, 1, 'experiment.record', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(290, 1, 'equipment', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(291, 1, 'equipment.list', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(292, 1, 'equipment.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(293, 1, 'equipment.update', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(294, 1, 'equipment.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(295, 1, 'equipment.borrow', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(296, 1, 'equipment.maintenance', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(297, 1, 'basic', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(298, 1, 'basic.subject', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(299, 1, 'basic.subject.view', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(300, 1, 'basic.subject.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(301, 1, 'basic.subject.edit', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(302, 1, 'basic.subject.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(303, 1, 'basic.equipment_standard', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(304, 1, 'basic.equipment_standard.view', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(305, 1, 'basic.equipment_standard.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(306, 1, 'basic.equipment_standard.edit', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(307, 1, 'basic.equipment_standard.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(308, 1, 'basic.equipment_standard.check_compliance', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(309, 1, 'basic.textbook_version', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(310, 1, 'basic.textbook_version.view', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(311, 1, 'basic.textbook_version.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(312, 1, 'basic.textbook_version.edit', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(313, 1, 'basic.textbook_version.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(314, 1, 'basic.textbook_version.batch_status', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(315, 1, 'basic.textbook_version.sort', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(316, 1, 'basic.textbook_chapter', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(317, 1, 'basic.textbook_chapter.view', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(318, 1, 'basic.textbook_chapter.tree', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(319, 1, 'basic.textbook_chapter.create', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(320, 1, 'basic.textbook_chapter.edit', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(321, 1, 'basic.textbook_chapter.delete', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(322, 1, 'system', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(323, 1, 'system.read', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(324, 1, 'log.read', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(325, 1, 'statistics', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(326, 1, 'statistics.view', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(327, 1, 'statistics.dashboard', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(328, 1, 'statistics.experiment', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(329, 1, 'statistics.equipment', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(330, 1, 'statistics.user', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(331, 1, 'statistics.performance', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(332, 1, 'statistics.export', '2025-07-27 19:16:28', '2025-07-27 19:16:28'),
(333, 13, 'experiment.catalog.view', '2025-07-27 19:17:53', '2025-07-27 19:17:53'),
(334, 13, 'experiment.catalog.create', '2025-07-27 19:17:53', '2025-07-27 19:17:53'),
(335, 13, 'experiment.booking', '2025-07-27 19:17:53', '2025-07-27 19:17:53'),
(336, 13, 'experiment.record', '2025-07-27 19:17:53', '2025-07-27 19:17:53'),
(337, 5, 'user', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(338, 5, 'user.list', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(339, 5, 'user.create', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(340, 5, 'user.update', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(341, 5, 'user.edit', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(342, 5, 'user.delete', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(343, 5, 'user.export', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(344, 5, 'user.reset_password', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(345, 5, 'role', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(346, 5, 'role.list', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(347, 5, 'role.create', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(348, 5, 'role.update', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(349, 5, 'role.delete', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(350, 5, 'experiment', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(351, 5, 'experiment.catalog', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(352, 5, 'experiment.catalog.view', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(353, 5, 'experiment.catalog.create', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(354, 5, 'experiment.catalog.edit', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(355, 5, 'experiment.catalog.delete', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(356, 5, 'experiment.catalog.copy', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(357, 5, 'experiment.catalog.manage_level', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(358, 5, 'experiment.booking', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(359, 5, 'experiment.record', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(360, 5, 'equipment', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(361, 5, 'equipment.list', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(362, 5, 'equipment.create', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(363, 5, 'equipment.update', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(364, 5, 'equipment.delete', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(365, 5, 'equipment.borrow', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(366, 5, 'equipment.maintenance', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(367, 5, 'basic.subject', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(368, 5, 'basic.subject.view', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(369, 5, 'basic.subject.create', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(370, 5, 'basic.subject.edit', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(371, 5, 'basic.subject.delete', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(372, 5, 'basic.equipment_standard.view', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(373, 5, 'basic.textbook_version.view', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(374, 5, 'basic.textbook_version.sort', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(375, 5, 'basic.textbook_chapter', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(376, 5, 'basic.textbook_chapter.view', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(377, 5, 'basic.textbook_chapter.tree', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(378, 5, 'basic.textbook_chapter.create', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(379, 5, 'basic.textbook_chapter.edit', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(380, 5, 'basic.textbook_chapter.delete', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(381, 5, 'system', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(382, 5, 'system.read', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(383, 5, 'log.read', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(384, 5, 'statistics', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(385, 5, 'statistics.view', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(386, 5, 'statistics.dashboard', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(387, 5, 'statistics.experiment', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(388, 5, 'statistics.equipment', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(389, 5, 'statistics.user', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(390, 5, 'statistics.performance', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(391, 5, 'statistics.export', '2025-07-29 17:40:33', '2025-07-29 17:40:33'),
(392, 7, 'user', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(393, 7, 'user.list', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(394, 7, 'user.create', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(395, 7, 'user.update', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(396, 7, 'user.edit', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(397, 7, 'user.delete', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(398, 7, 'user.export', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(399, 7, 'user.reset_password', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(400, 7, 'experiment', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(401, 7, 'experiment.catalog', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(402, 7, 'experiment.catalog.view', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(403, 7, 'experiment.catalog.create', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(404, 7, 'experiment.catalog.edit', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(405, 7, 'experiment.catalog.delete', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(406, 7, 'experiment.catalog.copy', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(407, 7, 'experiment.catalog.manage_level', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(408, 7, 'experiment.booking', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(409, 7, 'experiment.record', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(410, 7, 'equipment', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(411, 7, 'equipment.list', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(412, 7, 'equipment.create', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(413, 7, 'equipment.update', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(414, 7, 'equipment.delete', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(415, 7, 'equipment.borrow', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(416, 7, 'equipment.maintenance', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(417, 7, 'basic.subject.view', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(418, 7, 'basic.equipment_standard.view', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(419, 7, 'basic.textbook_version.view', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(420, 7, 'basic.textbook_version.create', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(421, 7, 'basic.textbook_version.sort', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(422, 7, 'basic.textbook_chapter.view', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(423, 7, 'basic.textbook_chapter.tree', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(424, 7, 'system', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(425, 7, 'system.read', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(426, 7, 'log.read', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(427, 7, 'statistics', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(428, 7, 'statistics.view', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(429, 7, 'statistics.dashboard', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(430, 7, 'statistics.experiment', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(431, 7, 'statistics.equipment', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(432, 7, 'statistics.user', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(433, 7, 'statistics.performance', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(434, 7, 'statistics.export', '2025-07-29 17:41:15', '2025-07-29 17:41:15'),
(435, 1, 'textbook_versions', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(436, 1, 'textbook_versions.list', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(437, 1, 'textbook_versions.create', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(438, 1, 'textbook_versions.update', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(439, 1, 'textbook_versions.delete', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(440, 1, 'textbook_versions.view', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(441, 1, 'textbook_chapters', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(442, 1, 'textbook_chapters.list', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(443, 1, 'textbook_chapters.create', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(444, 1, 'textbook_chapters.update', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(445, 1, 'textbook_chapters.delete', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(446, 1, 'textbook_chapters.view', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(447, 1, 'textbook_chapters.tree', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(461, 5, 'textbook_versions', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(462, 5, 'textbook_versions.list', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(463, 5, 'textbook_versions.create', '2025-07-29 23:27:51', '2025-07-29 23:27:51'),
(464, 5, 'textbook_versions.update', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(465, 5, 'textbook_versions.delete', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(466, 5, 'textbook_versions.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(467, 5, 'textbook_chapters', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(468, 5, 'textbook_chapters.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(469, 5, 'textbook_chapters.create', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(470, 5, 'textbook_chapters.update', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(471, 5, 'textbook_chapters.delete', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(472, 5, 'textbook_chapters.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(473, 5, 'textbook_chapters.tree', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(474, 7, 'textbook_versions', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(475, 7, 'textbook_versions.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(476, 7, 'textbook_versions.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(477, 7, 'textbook_chapters', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(478, 7, 'textbook_chapters.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(479, 7, 'textbook_chapters.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(480, 7, 'textbook_chapters.tree', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(481, 8, 'textbook_versions', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(482, 8, 'textbook_versions.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(483, 8, 'textbook_versions.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(484, 8, 'textbook_chapters', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(485, 8, 'textbook_chapters.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(486, 8, 'textbook_chapters.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(487, 8, 'textbook_chapters.tree', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(488, 10, 'textbook_versions', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(489, 10, 'textbook_versions.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(490, 10, 'textbook_versions.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(491, 10, 'textbook_chapters', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(492, 10, 'textbook_chapters.list', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(493, 10, 'textbook_chapters.view', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(494, 10, 'textbook_chapters.tree', '2025-07-29 23:27:52', '2025-07-29 23:27:52'),
(524, 12, 'user', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(525, 12, 'user.list', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(526, 12, 'user.create', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(527, 12, 'user.update', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(528, 12, 'user.edit', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(529, 12, 'user.delete', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(530, 12, 'user.export', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(531, 12, 'user.reset_password', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(532, 12, 'experiment', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(533, 12, 'experiment.catalog', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(534, 12, 'experiment.catalog.view', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(535, 12, 'experiment.catalog.create', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(536, 12, 'experiment.catalog.edit', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(537, 12, 'experiment.catalog.delete', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(538, 12, 'experiment.catalog.copy', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(539, 12, 'experiment.catalog.manage_level', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(540, 12, 'experiment.booking', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(541, 12, 'experiment.record', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(542, 12, 'equipment', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(543, 12, 'equipment.list', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(544, 12, 'equipment.create', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(545, 12, 'equipment.update', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(546, 12, 'equipment.delete', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(547, 12, 'equipment.borrow', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(548, 12, 'equipment.maintenance', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(549, 12, 'basic.textbook_version.view', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(550, 12, 'basic.textbook_version.create', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(551, 12, 'basic.textbook_chapter.view', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(552, 12, 'basic.textbook_chapter.tree', '2025-07-30 00:42:36', '2025-07-30 00:42:36'),
(553, 3, 'user', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(554, 3, 'user.list', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(555, 3, 'user.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(556, 3, 'user.update', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(557, 3, 'user.edit', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(558, 3, 'user.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(559, 3, 'user.export', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(560, 3, 'user.reset_password', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(561, 3, 'role', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(562, 3, 'role.list', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(563, 3, 'role.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(564, 3, 'role.update', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(565, 3, 'role.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(566, 3, 'experiment', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(567, 3, 'experiment.catalog', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(568, 3, 'experiment.catalog.view', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(569, 3, 'experiment.catalog.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(570, 3, 'experiment.catalog.edit', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(571, 3, 'experiment.catalog.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(572, 3, 'experiment.catalog.copy', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(573, 3, 'experiment.catalog.manage_level', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(574, 3, 'experiment.booking', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(575, 3, 'experiment.record', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(576, 3, 'equipment', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(577, 3, 'equipment.list', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(578, 3, 'equipment.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(579, 3, 'equipment.update', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(580, 3, 'equipment.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(581, 3, 'equipment.borrow', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(582, 3, 'equipment.maintenance', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(583, 3, 'basic', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(584, 3, 'basic.subject', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(585, 3, 'basic.subject.view', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(586, 3, 'basic.subject.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(587, 3, 'basic.subject.edit', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(588, 3, 'basic.subject.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(589, 3, 'basic.equipment_standard', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(590, 3, 'basic.equipment_standard.view', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(591, 3, 'basic.equipment_standard.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(592, 3, 'basic.equipment_standard.edit', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(593, 3, 'basic.equipment_standard.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(594, 3, 'basic.equipment_standard.check_compliance', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(595, 3, 'basic.textbook_version', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(596, 3, 'basic.textbook_version.view', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(597, 3, 'basic.textbook_version.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(598, 3, 'basic.textbook_version.edit', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(599, 3, 'basic.textbook_version.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(600, 3, 'basic.textbook_version.batch_status', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(601, 3, 'basic.textbook_version.sort', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(602, 3, 'basic.textbook_chapter', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(603, 3, 'basic.textbook_chapter.view', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(604, 3, 'basic.textbook_chapter.tree', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(605, 3, 'basic.textbook_chapter.create', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(606, 3, 'basic.textbook_chapter.edit', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(607, 3, 'basic.textbook_chapter.delete', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(608, 3, 'system', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(609, 3, 'system.read', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(610, 3, 'log.read', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(611, 3, 'statistics', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(612, 3, 'statistics.view', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(613, 3, 'statistics.dashboard', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(614, 3, 'statistics.experiment', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(615, 3, 'statistics.equipment', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(616, 3, 'statistics.user', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(617, 3, 'statistics.performance', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(618, 3, 'statistics.export', '2025-08-02 18:10:41', '2025-08-02 18:10:41'),
(619, 5, 'basic.equipment_standard.create', '2025-08-02 19:58:36', '2025-08-02 19:58:36'),
(620, 5, 'basic.equipment_standard.edit', '2025-08-02 19:58:36', '2025-08-02 19:58:36'),
(621, 5, 'basic.equipment_standard.delete', '2025-08-02 19:58:36', '2025-08-02 19:58:36'),
(622, 5, 'basic.equipment_standard.check_compliance', '2025-08-02 19:58:36', '2025-08-02 19:58:36'),
(623, 12, 'basic.equipment_standard.view', '2025-08-02 19:58:36', '2025-08-02 19:58:36'),
(624, 11, 'basic.textbook_version.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(625, 11, 'basic.textbook_chapter.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(626, 11, 'basic.textbook_chapter.tree', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(627, 11, 'experiment.catalog.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(628, 11, 'experiment.booking.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(629, 11, 'experiment.booking.create', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(630, 11, 'experiment.booking.edit', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(631, 11, 'experiment.record.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(632, 11, 'experiment.record.create', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(633, 11, 'experiment.record.edit', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(634, 11, 'experiment.record.complete', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(635, 11, 'equipment.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(636, 11, 'equipment.borrow.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(637, 11, 'equipment.borrow.create', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(638, 11, 'equipment.borrow.edit', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(639, 11, 'equipment.maintenance.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(640, 11, 'equipment.maintenance.create', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(641, 11, 'equipment.qrcode.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(642, 11, 'statistics.view', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(643, 11, 'statistics.dashboard', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(644, 11, 'statistics.experiment', '2025-08-03 01:46:40', '2025-08-03 01:46:40'),
(645, 11, 'statistics.equipment', '2025-08-03 01:46:40', '2025-08-03 01:46:40');

-- --------------------------------------------------------

--
-- 表的结构 `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL COMMENT '学校代码',
  `name` varchar(200) NOT NULL COMMENT '学校名称',
  `type` tinyint(4) NOT NULL COMMENT '学校类型：1小学 2初中 3高中 4九年一贯制',
  `level` tinyint(4) NOT NULL COMMENT '管理级别：1省直 2市直 3区县直 4学区',
  `region_id` bigint(20) UNSIGNED NOT NULL COMMENT '所属区域ID',
  `organization_type` varchar(20) DEFAULT NULL COMMENT '组织类型：province/city/county',
  `organization_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '组织ID',
  `address` text DEFAULT NULL COMMENT '学校地址',
  `contact_person` varchar(50) DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `student_count` int(11) NOT NULL DEFAULT 0 COMMENT '学生总数',
  `class_count` int(11) NOT NULL DEFAULT 0 COMMENT '班级总数',
  `teacher_count` int(11) NOT NULL DEFAULT 0 COMMENT '教师总数',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1正常 0停用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `schools`
--

INSERT INTO `schools` (`id`, `code`, `name`, `type`, `level`, `region_id`, `organization_type`, `organization_id`, `address`, `contact_person`, `contact_phone`, `student_count`, `class_count`, `teacher_count`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ZY001', '藁城区实验小学', 1, 3, 10, 'county', 1, '河北省石家庄市藁城区建设路123号', '张校长', '0371-12345678', 1200, 24, 80, 1, '2025-07-18 15:17:55', '2025-07-29 01:58:12'),
(2, 'ZY002', '石家庄市第一中学', 3, 2, 9, 'city', 1, '河北省石家庄市藁城区中原路456号', '李校长', '0371-23456789', 2400, 48, 180, 1, '2025-07-18 15:17:55', '2025-07-28 23:45:10'),
(3, 'ZY003', '南董第二小学', 1, 3, 6, 'county', 1, '河北省石家庄市藁城区桐柏路789号', '王校长', '0371-34567890', 800, 18, 60, 1, '2025-07-18 15:17:55', '2025-07-27 00:49:54'),
(4, 'ZY004', '南董实验中学1', 2, 3, 6, 'county', 1, '河北省石家庄市藁城区秦岭路321号', '赵校长', '0371-45678901', 1500, 30, 120, 1, '2025-07-18 15:17:55', '2025-07-29 17:29:02'),
(5, 'EQ001', '石家庄市栾城区实验小学', 1, 3, 7, 'county', 1, '河北省石家庄市栾城区大学路654号', '陈校长', '0371-56789012', 1000, 20, 70, 1, '2025-07-18 15:17:55', '2025-07-27 00:49:54'),
(6, 'EQ002', '石家庄市栾城区九年制学校', 4, 3, 7, 'county', 1, '河北省石家庄市栾城区航海路987号', '刘校长', '0371-67890123', 1800, 36, 140, 1, '2025-07-18 15:17:55', '2025-07-27 00:49:54'),
(7, 'HB001', '石家庄精英中学', 3, 1, 16, 'province', 1, '河北省石家庄市长安区', '张校长', '0311-12345678', 2000, 40, 150, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:54'),
(8, 'HB002', '衡水中学', 3, 1, 21, 'province', 1, '河北省衡水市桃城区', '李校长', '0318-12345678', 3000, 60, 200, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(9, 'HB003', '保定七中', 3, 1, 1, 'province', 1, '河北省保定市莲池区', '王校长', '0312-12345678', 1800, 36, 120, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(10, 'HB004', '邢台一中', 3, 1, 1, 'province', 1, '河北省邢台市桥东区', '赵校长', '0319-12345678', 1600, 32, 110, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(11, 'SJZ001', '石家庄市第一中学', 3, 2, 9, 'city', 1, '河北省石家庄市裕华区', '校长1', '0311-87654320', 1500, 30, 100, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(12, 'SJZ002', '石家庄市第二中学', 3, 2, 9, 'city', 1, '河北省石家庄市新华区', '校长2', '0311-87654321', 1600, 32, 110, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(13, 'SJZ003', '石家庄外国语学校', 3, 2, 9, 'city', 1, '河北省石家庄市桥西区', '校长3', '0311-87654322', 1700, 34, 120, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(14, 'SJZ004', '石家庄实验中学', 2, 2, 9, 'city', 1, '河北省石家庄市长安区', '校长4', '0311-87654323', 1800, 36, 130, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(15, 'LZ001', '廉州东城小学', 1, 4, 11, 'county', 1, '河北省石家庄市藁城区廉州镇廉州东城小学地址', '廉州东城小学校长', '0311-99999990', 200, 8, 15, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(16, 'LZ002', '廉州北街小学', 1, 4, 11, 'county', 1, '河北省石家庄市藁城区廉州镇廉州北街小学地址', '廉州北街小学校长', '0311-99999991', 250, 10, 20, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(17, 'LZ003', '廉州第四中学', 2, 4, 11, 'county', 1, '河北省石家庄市藁城区廉州镇廉州第四中学地址', '廉州第四中学校长', '0311-99999992', 300, 12, 25, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(18, 'LZ004', '廉州第一中学', 2, 4, 11, 'county', 1, '河北省石家庄市藁城区廉州镇廉州第一中学地址', '廉州第一中学校长', '0311-99999993', 350, 14, 30, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(19, 'GC001', '通安小学', 1, 3, 10, 'county', 1, '河北省石家庄市藁城区通安小学地址', '通安小学校长', '0311-88888880', 600, 18, 40, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(20, 'GC002', '实验小学', 1, 3, 10, 'county', 1, '河北省石家庄市藁城区实验小学地址', '实验小学校长', '0311-88888881', 700, 21, 50, 1, '2025-07-18 15:31:16', '2025-07-27 00:49:55'),
(21, 'GC003', '藁城区第八中学', 4, 3, 10, 'county', 1, '河北省藁城区石家庄第八中学地址', '石家庄第八中学校长', '0311-88888882', 800, 24, 60, 1, '2025-07-18 15:31:16', '2025-07-29 01:58:42');

-- --------------------------------------------------------

--
-- 表的结构 `school_classes`
--

CREATE TABLE `school_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `name` varchar(50) NOT NULL COMMENT '班级名称，如：一年级（1）',
  `code` varchar(20) NOT NULL COMMENT '班级代码，如：G1C1',
  `grade` tinyint(4) NOT NULL COMMENT '年级：1-9',
  `class_number` tinyint(4) NOT NULL COMMENT '班级序号：1,2,3...',
  `student_count` int(11) NOT NULL DEFAULT 0 COMMENT '学生人数',
  `head_teacher_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '班主任ID',
  `classroom_location` varchar(100) DEFAULT NULL COMMENT '教室位置',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1正常 0停用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `school_classes`
--

INSERT INTO `school_classes` (`id`, `school_id`, `name`, `code`, `grade`, `class_number`, `student_count`, `head_teacher_id`, `classroom_location`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '一年级（1）', 'G1C1', 1, 1, 42, NULL, '1楼11教室', 1, '2025-07-27 20:28:34', '2025-07-27 20:28:34'),
(2, 1, '一年级（2）', 'G1C2', 1, 2, 35, NULL, '1楼12教室', 1, '2025-07-27 20:28:34', '2025-07-27 20:28:34'),
(3, 1, '一年级（3）', 'G1C3', 1, 3, 31, NULL, '1楼13教室', 1, '2025-07-27 20:28:34', '2025-07-27 20:28:34'),
(4, 1, '一年级（4）', 'G1C4', 1, 4, 37, NULL, '1楼14教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(5, 1, '二年级（1）', 'G2C1', 2, 1, 25, NULL, '1楼21教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(6, 1, '二年级（2）', 'G2C2', 2, 2, 44, NULL, '1楼22教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(7, 1, '三年级（1）', 'G3C1', 3, 1, 39, NULL, '1楼1教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(8, 1, '三年级（2）', 'G3C2', 3, 2, 41, NULL, '1楼2教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(9, 1, '三年级（3）', 'G3C3', 3, 3, 41, NULL, '1楼3教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(10, 1, '三年级（4）', 'G3C4', 3, 4, 25, NULL, '1楼4教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(11, 1, '四年级（1）', 'G4C1', 4, 1, 41, NULL, '2楼11教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(12, 1, '四年级（2）', 'G4C2', 4, 2, 27, NULL, '2楼12教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(13, 1, '四年级（3）', 'G4C3', 4, 3, 28, NULL, '2楼13教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(14, 1, '五年级（1）', 'G5C1', 5, 1, 38, NULL, '2楼21教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(15, 1, '五年级（2）', 'G5C2', 5, 2, 25, NULL, '2楼22教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(16, 1, '五年级（3）', 'G5C3', 5, 3, 28, NULL, '2楼23教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(17, 1, '六年级（1）', 'G6C1', 6, 1, 31, NULL, '2楼1教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(18, 1, '六年级（2）', 'G6C2', 6, 2, 41, NULL, '2楼2教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(19, 2, '高一（1）', 'G10C1', 10, 1, 35, NULL, '4楼11教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(20, 2, '高一（2）', 'G10C2', 10, 2, 38, NULL, '4楼12教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(21, 2, '高一（3）', 'G10C3', 10, 3, 27, NULL, '4楼13教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(22, 2, '高二（1）', 'G11C1', 11, 1, 25, NULL, '4楼21教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(23, 2, '高二（2）', 'G11C2', 11, 2, 40, NULL, '4楼22教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(24, 2, '高二（3）', 'G11C3', 11, 3, 44, NULL, '4楼23教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(25, 2, '高二（4）', 'G11C4', 11, 4, 28, NULL, '4楼24教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(26, 2, '高三（1）', 'G12C1', 12, 1, 32, NULL, '4楼1教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(27, 2, '高三（2）', 'G12C2', 12, 2, 45, NULL, '4楼2教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(28, 3, '一年级（1）', 'G1C1', 1, 1, 32, NULL, '1楼11教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(29, 3, '一年级（2）', 'G1C2', 1, 2, 45, NULL, '1楼12教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(30, 3, '二年级（1）', 'G2C1', 2, 1, 40, NULL, '1楼21教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(31, 3, '二年级（2）', 'G2C2', 2, 2, 42, NULL, '1楼22教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(32, 3, '二年级（3）', 'G2C3', 2, 3, 37, NULL, '1楼23教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(33, 3, '三年级（1）', 'G3C1', 3, 1, 31, NULL, '1楼1教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(34, 3, '三年级（2）', 'G3C2', 3, 2, 33, NULL, '1楼2教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(35, 3, '三年级（3）', 'G3C3', 3, 3, 41, NULL, '1楼3教室', 1, '2025-07-27 20:28:35', '2025-07-27 20:28:35'),
(36, 3, '四年级（1）', 'G4C1', 4, 1, 36, NULL, '2楼11教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(37, 3, '四年级（2）', 'G4C2', 4, 2, 39, NULL, '2楼12教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(38, 3, '四年级（3）', 'G4C3', 4, 3, 28, NULL, '2楼13教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(39, 3, '四年级（4）', 'G4C4', 4, 4, 44, NULL, '2楼14教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(40, 3, '五年级（1）', 'G5C1', 5, 1, 38, NULL, '2楼21教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(41, 3, '五年级（2）', 'G5C2', 5, 2, 41, NULL, '2楼22教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(42, 3, '五年级（3）', 'G5C3', 5, 3, 33, NULL, '2楼23教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(43, 3, '六年级（1）', 'G6C1', 6, 1, 34, NULL, '2楼1教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(44, 3, '六年级（2）', 'G6C2', 6, 2, 40, NULL, '2楼2教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(45, 3, '六年级（3）', 'G6C3', 6, 3, 25, NULL, '2楼3教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(46, 4, '七年级（1）', 'G7C1', 7, 1, 39, NULL, '3楼11教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(47, 4, '七年级（2）', 'G7C2', 7, 2, 27, NULL, '3楼12教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(48, 4, '八年级（1）', 'G8C1', 8, 1, 45, NULL, '3楼21教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(49, 4, '八年级（2）', 'G8C2', 8, 2, 27, NULL, '3楼22教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(50, 4, '九年级（1）', 'G9C1', 9, 1, 32, NULL, '3楼1教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(51, 4, '九年级（2）', 'G9C2', 9, 2, 32, NULL, '3楼2教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(52, 5, '一年级（1）', 'G1C1', 1, 1, 26, NULL, '1楼11教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(53, 5, '一年级（2）', 'G1C2', 1, 2, 36, NULL, '1楼12教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(54, 5, '一年级（3）', 'G1C3', 1, 3, 27, NULL, '1楼13教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(55, 5, '一年级（4）', 'G1C4', 1, 4, 29, NULL, '1楼14教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(56, 5, '二年级（1）', 'G2C1', 2, 1, 42, NULL, '1楼21教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(57, 5, '二年级（2）', 'G2C2', 2, 2, 41, NULL, '1楼22教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(58, 5, '二年级（3）', 'G2C3', 2, 3, 34, NULL, '1楼23教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(59, 5, '三年级（1）', 'G3C1', 3, 1, 31, NULL, '1楼1教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(60, 5, '三年级（2）', 'G3C2', 3, 2, 40, NULL, '1楼2教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(61, 5, '四年级（1）', 'G4C1', 4, 1, 44, NULL, '2楼11教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(62, 5, '四年级（2）', 'G4C2', 4, 2, 38, NULL, '2楼12教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(63, 5, '四年级（3）', 'G4C3', 4, 3, 36, NULL, '2楼13教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(64, 5, '五年级（1）', 'G5C1', 5, 1, 28, NULL, '2楼21教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(65, 5, '五年级（2）', 'G5C2', 5, 2, 25, NULL, '2楼22教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(66, 5, '六年级（1）', 'G6C1', 6, 1, 27, NULL, '2楼1教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(67, 5, '六年级（2）', 'G6C2', 6, 2, 38, NULL, '2楼2教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(68, 5, '六年级（3）', 'G6C3', 6, 3, 43, NULL, '2楼3教室', 1, '2025-07-27 20:28:36', '2025-07-27 20:28:36'),
(69, 15, '一年级（1）', 'G1C1', 1, 1, 35, NULL, '1-1', 1, '2025-07-27 22:41:16', '2025-07-27 22:41:16'),
(70, 15, '一年级（2）', 'G1C2', 1, 2, 40, NULL, '1-2', 1, '2025-07-27 22:41:51', '2025-07-27 22:41:51'),
(71, 15, '二年级（1）', 'G2C1', 2, 1, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20'),
(72, 15, '二年级（2）', 'G2C2', 2, 2, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20'),
(73, 15, '二年级（3）', 'G2C3', 2, 3, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20'),
(74, 15, '二年级（4）', 'G2C4', 2, 4, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20'),
(75, 15, '三年级（1）', 'G3C1', 3, 1, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20'),
(76, 15, '三年级（2）', 'G3C2', 3, 2, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20'),
(77, 15, '三年级（3）', 'G3C3', 3, 3, 0, NULL, NULL, 1, '2025-07-27 22:42:20', '2025-07-27 22:42:20');

-- --------------------------------------------------------

--
-- 表的结构 `school_experiment_catalog_configs`
--

CREATE TABLE `school_experiment_catalog_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `config_type` enum('selection','assignment') NOT NULL COMMENT '配置类型：selection=学校选择，assignment=上级指定',
  `source_level` tinyint(4) NOT NULL COMMENT '目录来源级别：1省 2市 3区县',
  `source_org_id` bigint(20) UNSIGNED NOT NULL COMMENT '目录来源组织ID',
  `source_org_name` varchar(100) NOT NULL COMMENT '目录来源组织名称',
  `can_modify_selection` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否允许修改选择',
  `can_delete_experiments` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否允许删除实验项目',
  `configured_by` bigint(20) UNSIGNED NOT NULL COMMENT '配置操作人ID',
  `configured_by_level` tinyint(4) NOT NULL COMMENT '配置操作人级别',
  `configured_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '配置时间',
  `config_reason` text DEFAULT NULL COMMENT '配置理由',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `effective_date` date DEFAULT NULL COMMENT '生效日期',
  `expiry_date` date DEFAULT NULL COMMENT '失效日期',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `school_experiment_catalog_configs`
--

INSERT INTO `school_experiment_catalog_configs` (`id`, `school_id`, `config_type`, `source_level`, `source_org_id`, `source_org_name`, `can_modify_selection`, `can_delete_experiments`, `configured_by`, `configured_by_level`, `configured_at`, `config_reason`, `status`, `effective_date`, `expiry_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'selection', 2, 9, '石家庄市', 1, 1, 15, 5, '2025-07-26 15:54:21', '本校选择石家庄市级实验目录标准，该标准更符合我校的实际教学条件和设备配置情况。同时申请删除权限，以便根据学校实际情况调整实验内容。', 1, '2025-07-28', NULL, '2025-07-26 15:54:21', '2025-07-26 15:54:21'),
(2, 2, 'selection', 1, 1, '河北省', 1, 0, 15, 5, '2025-07-26 15:54:21', '本校选择省级标准实验目录，严格按照省级要求执行，不申请删除权限。', 1, '2025-07-28', NULL, '2025-07-26 15:54:21', '2025-07-26 15:54:21'),
(3, 3, 'selection', 2, 9, '石家庄市', 1, 1, 15, 5, '2025-07-26 15:54:21', '选择市级标准，申请删除权限以适应学校特色教学需求。', 1, '2025-07-28', NULL, '2025-07-26 15:54:21', '2025-07-26 15:54:21'),
(4, 4, 'selection', 3, 16, '长安区', 1, 0, 15, 5, '2025-07-26 15:54:21', '选择区县级标准，该标准最贴近本校实际情况。', 1, '2025-07-28', NULL, '2025-07-26 15:54:21', '2025-07-26 15:54:21');

-- --------------------------------------------------------

--
-- 表的结构 `school_experiment_catalog_selections`
--

CREATE TABLE `school_experiment_catalog_selections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `selected_level` enum('province','city','county') NOT NULL COMMENT '选择的标准级别',
  `selected_org_id` bigint(20) UNSIGNED NOT NULL COMMENT '选择的组织ID',
  `selected_org_name` varchar(100) NOT NULL COMMENT '选择的组织名称',
  `can_delete_experiments` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否允许删除实验',
  `selection_reason` text DEFAULT NULL COMMENT '选择理由',
  `selected_by` bigint(20) UNSIGNED NOT NULL COMMENT '选择操作人',
  `selected_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '选择时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `school_experiment_catalog_selections`
--

INSERT INTO `school_experiment_catalog_selections` (`id`, `school_id`, `selected_level`, `selected_org_id`, `selected_org_name`, `can_delete_experiments`, `selection_reason`, `selected_by`, `selected_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'city', 9, '石家庄市', 1, '本校选择石家庄市级实验目录标准，该标准更符合我校的实际教学条件和设备配置情况。同时申请删除权限，以便根据学校实际情况调整实验内容。', 15, '2025-07-26 15:54:21', '2025-07-26 15:54:21', '2025-07-26 15:54:21'),
(2, 2, 'province', 1, '河北省', 0, '本校选择省级标准实验目录，严格按照省级要求执行，不申请删除权限。', 15, '2025-07-26 15:54:21', '2025-07-26 15:54:21', '2025-07-26 15:54:21'),
(3, 3, 'city', 9, '石家庄市', 1, '选择市级标准，申请删除权限以适应学校特色教学需求。', 15, '2025-07-26 15:54:21', '2025-07-26 15:54:21', '2025-07-26 15:54:21'),
(4, 4, 'county', 16, '长安区', 0, '选择区县级标准，该标准最贴近本校实际情况。', 15, '2025-07-26 15:54:21', '2025-07-26 15:54:21', '2025-07-26 15:54:21');

-- --------------------------------------------------------

--
-- 表的结构 `school_teachers`
--

CREATE TABLE `school_teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '学校ID',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '教师用户ID',
  `employee_number` varchar(50) DEFAULT NULL COMMENT '工号',
  `subject` varchar(50) DEFAULT NULL COMMENT '任教学科',
  `teaching_grades` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '任教年级，如：[1,2,3]' CHECK (json_valid(`teaching_grades`)),
  `title` varchar(50) DEFAULT NULL COMMENT '职称',
  `education` varchar(50) DEFAULT NULL COMMENT '学历',
  `join_date` date DEFAULT NULL COMMENT '入职日期',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1在职 2离职 0停用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `school_teachers`
--

INSERT INTO `school_teachers` (`id`, `school_id`, `user_id`, `employee_number`, `subject`, `teaching_grades`, `title`, `education`, `join_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 19, 'T001001', '英语', '[2,5]', '副教授', '专科', '2022-11-20', 1, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(2, 1, 20, 'T001002', '美术', '[1,5,3]', '教授', '本科', '2022-11-29', 1, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(3, 1, 21, 'T001003', '历史', '[5,6,1]', '初级教师', '硕士', '2024-08-12', 1, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(4, 1, 53, 'T001004', '地理', '[2,6]', '高级教师', '博士', '2023-01-23', 1, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(5, 1, 54, 'T001005', '语文', '[6,4]', '教授', '硕士', '2023-08-07', 1, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(6, 1, 96, 'T001006', '化学', '[1,5,2]', '助教', '本科', '2025-03-18', 1, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(7, 1, 97, 'T001007', '历史', '[3,1,2]', '中级教师', '专科', '2023-01-06', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(8, 1, 98, 'T001008', '地理', '[6,3,2]', '教授', '专科', '2024-07-02', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(9, 2, 24, 'T002001', '化学', '[10,11]', '讲师', '专科', '2024-10-03', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(10, 2, 25, 'T002002', '语文', '[12]', '特级教师', '本科', '2022-12-12', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(11, 2, 26, 'T002003', '生物', '[12,11]', '高级教师', '硕士', '2023-06-27', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(12, 2, 55, 'T002004', '政治', '[11]', '助教', '硕士', '2023-07-16', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(13, 2, 56, 'T002005', '数学', '[12]', '特级教师', '博士', '2025-04-28', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(14, 2, 99, 'T002006', '政治', '[12,10]', '助教', '本科', '2023-11-21', 1, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(15, 2, 100, 'T002007', '语文', '[11]', '副教授', '博士', '2024-05-30', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(16, 2, 101, 'T002008', '道德与法治', '[10]', '副教授', '专科', '2025-04-12', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(17, 2, 102, 'T002009', '化学', '[12,11]', '教授', '本科', '2024-01-10', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(18, 3, 29, 'T003001', '化学', '[6,2]', '高级教师', '博士', '2023-11-12', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(19, 3, 30, 'T003002', '生物', '[5,6,1]', '高级教师', '专科', '2023-11-22', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(20, 3, 31, 'T003003', '美术', '[3,4,2]', '中级教师', '专科', '2024-07-09', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(21, 3, 57, 'T003004', '体育', '[3,6]', '特级教师', '本科', '2024-06-04', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(22, 3, 58, 'T003005', '科学', '[5,4,2]', '初级教师', '本科', '2025-04-15', 1, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(23, 3, 103, 'T003006', '数学', '[2,5,3]', '教授', '硕士', '2025-01-18', 1, '2025-07-27 20:28:54', '2025-07-27 20:28:54'),
(24, 3, 104, 'T003007', '政治', '[2,1]', '高级教师', '硕士', '2023-12-16', 1, '2025-07-27 20:28:54', '2025-07-27 20:28:54'),
(25, 3, 105, 'T003008', '语文', '[5,4]', '中级教师', '博士', '2025-02-16', 1, '2025-07-27 20:28:54', '2025-07-27 20:28:54'),
(26, 3, 106, 'T003009', '物理', '[6,5,2]', '讲师', '硕士', '2024-03-20', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(27, 3, 107, 'T003010', '生物', '[3,1]', '高级教师', '专科', '2024-10-25', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(28, 4, 34, 'T004001', '化学', '[7]', '初级教师', '硕士', '2023-02-23', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(29, 4, 35, 'T004002', '美术', '[8,9]', '特级教师', '专科', '2024-12-13', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(30, 4, 36, 'T004003', '英语', '[7]', '高级教师', '博士', '2023-05-15', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(31, 4, 59, 'T004004', '数学', '[9]', '初级教师', '本科', '2024-05-06', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(32, 4, 60, 'T004005', '历史', '[9]', '高级教师', '本科', '2023-11-17', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(33, 4, 108, 'T004006', '信息技术', '[8]', '初级教师', '硕士', '2023-08-16', 1, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(34, 4, 109, 'T004007', '化学', '[7]', '助教', '博士', '2024-09-13', 1, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(35, 4, 110, 'T004008', '地理', '[9]', '助教', '专科', '2023-11-29', 1, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(36, 4, 111, 'T004009', '英语', '[7]', '特级教师', '硕士', '2024-04-05', 1, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(37, 4, 112, 'T004010', '英语', '[9,8]', '初级教师', '博士', '2025-01-04', 1, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(38, 5, 39, 'T005001', '体育', '[5,1]', '讲师', '硕士', '2024-01-03', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(39, 5, 40, 'T005002', '英语', '[6,2,5]', '教授', '本科', '2023-01-08', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(40, 5, 41, 'T005003', '物理', '[5,6,3]', '助教', '专科', '2023-09-08', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(41, 5, 61, 'T005004', '道德与法治', '[6,3]', '中级教师', '本科', '2022-11-18', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(42, 5, 62, 'T005005', '音乐', '[4,3]', '助教', '专科', '2023-09-07', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(43, 5, 113, 'T005006', '英语', '[3,5]', '副教授', '硕士', '2024-11-05', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(44, 5, 114, 'T005007', '物理', '[4,1,6]', '讲师', '硕士', '2024-03-01', 1, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(45, 5, 115, 'T005008', '科学', '[5,1]', '中级教师', '专科', '2022-12-14', 1, '2025-07-27 20:28:58', '2025-07-27 20:28:58'),
(46, 5, 116, 'T005009', '体育', '[2,5,1]', '助教', '专科', '2024-10-14', 1, '2025-07-27 20:28:58', '2025-07-27 20:28:58'),
(47, 5, 117, 'T005010', '信息技术', '[3,6]', '中级教师', '博士', '2024-12-01', 1, '2025-07-27 20:28:58', '2025-07-27 20:28:58'),
(48, 5, 118, 'T005011', '音乐', '[6,4,1]', '助教', '硕士', '2023-09-14', 1, '2025-07-27 20:28:59', '2025-07-27 20:28:59'),
(49, 5, 119, 'T005012', '数学', '[1,4,6]', '助教', '硕士', '2023-10-23', 1, '2025-07-27 20:28:59', '2025-07-27 20:28:59'),
(50, 15, 95, 'dc001', '科学', '[1,3,5]', '讲师', '本科', '2022-06-14', 1, '2025-07-27 22:43:38', '2025-07-27 22:43:38');

-- --------------------------------------------------------

--
-- 表的结构 `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2NWwPlurrFG158BD2LT9rz1HAC1reLD4AVmursyX', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjhZU1BMY2VuMmN4UXFMckNxRnMzVHdCS3NuR3BhMklQdGNvb09TTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753879433),
('6D7oMpWuv71H0GFSTX8QkkJKEHw4hWIBsgol85SK', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidXBabzY3YUUwVW5pSk1HbTNLNEp3SWlib0pxZ3ZpRWFDVnlpbjNQNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753943315),
('B8jPpWXf0NVcMinQWzdbtr5sdPbG8g1dJRnDBK4U', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGNic1VycU82SEJRQ25YV0VnY2ZSZjlRcWowMHdOY3FYcnExeGQyWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754215709),
('FbecgjpmVfuRySZcU7udlPIprwNnLW3xuPA0mTBc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlNrSEVCaFlpYVVJaktTUm9xRmgwTGMzb2Zjd0NCQVdxZ2lRVzljcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754209314),
('gGgnzzVqLnppptYGIsFmfabh4D4vVNBNHqd5xcRo', 44, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiclN4OWlQT1F3UERyR1l1SERvaWwxMHNuZ3Rhckh4UFRNN3BJU0ZBTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754199164),
('hHG3SdQnxi818R75YedwN3ILTEoXQ37nR4lQjkIm', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid0xZUjBZWlgwYUsxWmFrZU1idTB2ektpRG1nN3M3TnhiRXpYdWNjTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754212691),
('IrmgifZlnKvt9ebZAtdDToYNVtGKYwIAEtYy1rkg', 46, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY095NGhrOTdnOXRJeENvWlJVUW9OcGlEN0pMb3ZhdUtib0h2VG4zSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753877756),
('myERn9P2nIVOjqwXntjmCQH6KhWVeMOS2Ztyfuz5', 44, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRHJaTlg2Qk56bE5RMkF6WVhta0JkZDlqQ1FRYzE0a3JMQnlJdlNKbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754199165),
('okdQGSmzd8VyEvvKyC66PuFZ1Pfbpmi9m4qBCC0x', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXZjQ3VNU2tyREhpZTFkRzJqQ1l0QXJEYlR6ZDRYR0NqbFowQlhMeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754288772),
('qBZ53lqmWNkvft54iGVPgjjy51Uqqp6Fie7b5Y3x', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDhVR0VOazVhUFF6S1VLdkZQSWxvdnN1WmNKOEZac0Z0dXduSmVoSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754296373),
('TfGkr6PdYxeE8lQ6N4YfkfPE1w9xsn14ySGzSh2u', 42, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHdjam1KbHZ3QnhqdzZocXh5RW42eW1jQzdFTm1EUmlKTXVEUGR1ciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754198955),
('W6nLw0wyYNXoJYD5lEpRkshT9hOjk0ieE8YiGpUj', 42, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVDZaVFcxY3lWZDBFUmJ5RHp6ZHZjRVZhd0VsYldHOEZCOEx5ZjJQOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754191046),
('WYmUNiLrf44RlqRB2RtytpM9rfmIh8xc7BRVVxNI', 95, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaFNkRjExM2h2SnpvWWdiV0J1c28zejhTT0FmRloweHRxeG1RN3JuQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753943321),
('XLHS6jMCcoTQJYwlRui6FA7TGn2ZzmWloFDI6B2o', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.26100.4652', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmZPVzU4bHVCRjFlUmlUU0R4allQMjhNMjdUdXFSOUVQSlVUMk9oUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754192164),
('ZILNtmlYBIFhQkyIwvaI0ktYoZluptXJ5369IDQO', 42, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmdmUUhyMXpDMVljdVNtY1FkWG9NT2NLVk5wTzFuWXJRelpLVVJ0NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvdGV4dGJvb2stdmVyc2lvbnMvb3B0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754212554);

-- --------------------------------------------------------

--
-- 表的结构 `statistics_summary`
--

CREATE TABLE `statistics_summary` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scope_type` varchar(20) NOT NULL COMMENT '统计范围：province/city/county/district/school',
  `scope_id` bigint(20) UNSIGNED NOT NULL COMMENT '范围ID',
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '学科ID',
  `stat_date` date NOT NULL COMMENT '统计日期',
  `stat_type` varchar(50) NOT NULL COMMENT '统计类型',
  `total_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '总实验数',
  `completed_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '已完成实验数',
  `completion_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '完成率',
  `group_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '分组实验数',
  `demo_experiments` int(11) NOT NULL DEFAULT 0 COMMENT '演示实验数',
  `total_value` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT '总价值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '学科名称',
  `code` varchar(20) NOT NULL COMMENT '学科代码',
  `type` tinyint(4) NOT NULL COMMENT '学科类型：1理科 2文科 3综合',
  `stage` tinyint(4) NOT NULL COMMENT '学段：1小学 2初中 3高中',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `type`, `stage`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, '小学科学', 'PRIMARY_SCIENCE', 1, 1, 1, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(2, '初中物理', 'MIDDLE_PHYSICS', 1, 2, 2, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(3, '初中化学', 'MIDDLE_CHEMISTRY', 1, 2, 3, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(4, '初中生物', 'MIDDLE_BIOLOGY', 1, 2, 4, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(5, '高中物理', 'HIGH_PHYSICS', 1, 3, 5, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(6, '高中化学', 'HIGH_CHEMISTRY', 1, 3, 6, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(7, '高中生物', 'HIGH_BIOLOGY', 1, 3, 7, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(8, '通用技术', 'GENERAL_TECH', 3, 3, 8, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55'),
(9, '信息技术', 'INFO_TECH', 3, 3, 9, 1, '2025-07-18 15:17:55', '2025-07-18 15:17:55');

-- --------------------------------------------------------

--
-- 表的结构 `system_configs`
--

CREATE TABLE `system_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key_name` varchar(100) NOT NULL COMMENT '配置键',
  `key_value` text DEFAULT NULL COMMENT '配置值',
  `description` varchar(255) DEFAULT NULL COMMENT '配置描述',
  `type` varchar(20) NOT NULL DEFAULT 'string' COMMENT '数据类型',
  `group_name` varchar(50) DEFAULT NULL COMMENT '配置分组',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `is_system` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否系统配置',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `teaching_equipment_standards`
--

CREATE TABLE `teaching_equipment_standards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `standard_name` varchar(200) NOT NULL COMMENT '标准名称',
  `standard_code` varchar(100) NOT NULL COMMENT '标准代码',
  `authority_type` tinyint(4) NOT NULL COMMENT '制定机构：1教育部 2教育厅 3地方',
  `stage` tinyint(4) NOT NULL COMMENT '学段：1小学 2初中 3高中',
  `subject_code` varchar(50) NOT NULL COMMENT '学科代码',
  `subject_name` varchar(100) NOT NULL COMMENT '学科名称',
  `description` text DEFAULT NULL COMMENT '标准描述',
  `category_level_1` varchar(100) NOT NULL COMMENT '一级分类',
  `category_level_2` varchar(100) DEFAULT NULL COMMENT '二级分类',
  `category_level_3` varchar(100) DEFAULT NULL COMMENT '三级分类',
  `item_code` varchar(50) NOT NULL COMMENT '器材编码',
  `item_name` varchar(200) NOT NULL COMMENT '器材名称',
  `specs_requirements` text DEFAULT NULL COMMENT '规格、教学性能要求',
  `unit` varchar(20) NOT NULL COMMENT '单位',
  `quantity` int(11) NOT NULL COMMENT '配备数量',
  `unit_price` decimal(10,2) DEFAULT NULL COMMENT '单价（元）',
  `total_amount` decimal(12,2) DEFAULT NULL COMMENT '金额（元）',
  `is_basic` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否基本配备',
  `is_optional` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否选配',
  `standard_reference` varchar(100) DEFAULT NULL COMMENT '执行标准代号',
  `remarks` text DEFAULT NULL COMMENT '备注',
  `activity_suggestion` text DEFAULT NULL COMMENT '实践活动建议',
  `version` varchar(20) NOT NULL DEFAULT '1.0' COMMENT '版本号',
  `effective_date` date NOT NULL COMMENT '生效日期',
  `expiry_date` date DEFAULT NULL COMMENT '失效日期',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `teaching_equipment_standards`
--

INSERT INTO `teaching_equipment_standards` (`id`, `standard_name`, `standard_code`, `authority_type`, `stage`, `subject_code`, `subject_name`, `description`, `category_level_1`, `category_level_2`, `category_level_3`, `item_code`, `item_name`, `specs_requirements`, `unit`, `quantity`, `unit_price`, `total_amount`, `is_basic`, `is_optional`, `standard_reference`, `remarks`, `activity_suggestion`, `version`, `effective_date`, `expiry_date`, `status`, `created_at`, `updated_at`) VALUES
(1, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_001', 1, 1, 'SCIENCE', '科学', '根据教育部最新标准制定的小学科学教学仪器配备要求', '实验室基础器材', '温度测量工具', NULL, '30204001001', '红外温度仪', '红外电子测温，测量范围-20~50℃', '支', 48, 45.00, 2160.00, 1, 0, 'JY/T 0386-2006', NULL, '温度变化观察实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:11', '2025-07-18 15:09:11'),
(2, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_002', 1, 1, 'SCIENCE', '科学', NULL, '实验室基础器材', '质量测量工具', NULL, '30202000321', '托盘天平', '500g，配6类M2砝码、镊子', '台', 2, 220.00, 440.00, 1, 0, 'QB/T 2087–2016', NULL, '物体质量测量实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:11', '2025-07-18 15:09:11'),
(3, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_001', 1, 1, 'SCIENCE', '科学', NULL, '主题学习器材', '物质领域', '物质的特征', '30601000103', '量筒', '25mL 玻璃，刻度清晰', '个', 12, 6.80, 81.60, 1, 0, 'GB/T 12804-2011', NULL, '探究物体体积', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:11', '2025-07-18 15:09:11'),
(4, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_002', 1, 1, 'SCIENCE', '科学', NULL, '主题学习器材', '生命科学领域', '动物与环境', '30701002001', '放大镜', '5倍，直径60mm', '个', 24, 12.50, 300.00, 1, 0, 'QB/T 1132-2010', NULL, '观察昆虫结构', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:11', '2025-07-18 15:09:11'),
(5, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_003', 1, 1, 'SCIENCE', '科学', NULL, '主题学习器材', '地球与宇宙领域', '地球运动', '30801003001', '地球仪', '直径20cm，政区版', '个', 1, 85.00, 85.00, 1, 0, 'GB/T 14511-2008', NULL, '地球自转公转演示', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:11', '2025-07-18 15:09:11'),
(6, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_004', 1, 1, 'SCIENCE', '科学', NULL, '实验室基础器材', '光学器材', NULL, '30501001001', '学生显微镜', '单目，10×、40×物镜', '台', 12, 380.00, 4560.00, 0, 1, 'JY/T 0364-2004', NULL, '细胞结构观察', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:11', '2025-07-18 15:09:11'),
(7, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_001', 1, 2, 'PHYSICS', '物理', NULL, '力学实验器材', '测力器材', NULL, '40101001001', '弹簧测力计', '5N，精度0.1N', '个', 25, 18.50, 462.50, 1, 0, 'JY/T 0374-2007', NULL, '力的测量实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(8, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_002', 1, 2, 'PHYSICS', '物理', NULL, '电学实验器材', '电源器材', NULL, '40201001001', '学生电源', '直流1.5V、3V、4.5V、6V、9V', '台', 13, 125.00, 1625.00, 1, 0, 'JY/T 0375-2007', NULL, '电路连接实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(9, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_003', 1, 2, 'PHYSICS', '物理', NULL, '光学实验器材', '透镜器材', NULL, '40301001001', '凸透镜', 'f=10cm，直径50mm', '个', 25, 8.50, 212.50, 1, 0, 'JY/T 0376-2007', NULL, '透镜成像实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(10, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_004', 1, 2, 'PHYSICS', '物理', NULL, '热学实验器材', '温度测量', NULL, '40401001001', '温度计', '0~100℃，精度1℃', '支', 25, 15.00, 375.00, 1, 0, 'JY/T 0377-2007', NULL, '温度变化测量', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(11, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_005', 1, 2, 'PHYSICS', '物理', NULL, '声学实验器材', '发声器材', NULL, '40501001001', '音叉', '440Hz，带共鸣箱', '个', 5, 35.00, 175.00, 1, 0, 'JY/T 0378-2007', NULL, '声音传播实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(12, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_006', 1, 2, 'PHYSICS', '物理', NULL, '力学实验器材', '简单机械', NULL, '40102001001', '滑轮组', '演示用，可组合', '套', 5, 68.00, 340.00, 0, 1, 'JY/T 0379-2007', NULL, '机械效率测定', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(13, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_001', 1, 2, 'CHEMISTRY', '化学', NULL, '玻璃器皿', '反应容器', NULL, '41101001001', '试管', '18×180mm，硼硅玻璃', '支', 100, 2.50, 250.00, 1, 0, 'GB/T 12803-2004', NULL, '化学反应实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(14, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_002', 1, 2, 'CHEMISTRY', '化学', NULL, '玻璃器皿', '测量容器', NULL, '41102001001', '量筒', '50mL，A级精度', '个', 25, 12.00, 300.00, 1, 0, 'GB/T 12804-2011', NULL, '溶液配制实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(15, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_003', 1, 2, 'CHEMISTRY', '化学', NULL, '加热器材', '酒精灯', NULL, '41201001001', '酒精灯', '150mL，带灯芯', '个', 25, 8.50, 212.50, 1, 0, 'JY/T 0380-2007', NULL, '物质加热实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(16, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_004', 1, 2, 'CHEMISTRY', '化学', NULL, '支撑器材', '铁架台', NULL, '41301001001', '铁架台', '高度可调，带夹具', '套', 25, 45.00, 1125.00, 1, 0, 'JY/T 0381-2007', NULL, '装置固定支撑', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(17, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_005', 1, 2, 'CHEMISTRY', '化学', NULL, '分离器材', '过滤器材', NULL, '41401001001', '漏斗', '60mm口径，长颈', '个', 25, 6.00, 150.00, 1, 0, 'GB/T 28211-2011', NULL, '混合物分离实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(18, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_006', 1, 2, 'CHEMISTRY', '化学', NULL, '测量器材', '天平', NULL, '41501001001', '电子天平', '精度0.1g，量程200g', '台', 5, 280.00, 1400.00, 0, 1, 'JY/T 0382-2007', NULL, '物质质量测定', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(19, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_001', 1, 2, 'BIOLOGY', '生物', NULL, '观察器材', '显微镜', NULL, '42101001001', '生物显微镜', '双目，10×、40×物镜', '台', 25, 1200.00, 30000.00, 1, 0, 'JY/T 0364-2004', NULL, '细胞结构观察', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(20, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_002', 1, 2, 'BIOLOGY', '生物', NULL, '标本模型', '人体模型', NULL, '42201001001', '人体骨骼模型', '85cm高，可拆装', '个', 1, 450.00, 450.00, 1, 0, 'JY/T 0383-2007', NULL, '人体结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(21, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_003', 1, 2, 'BIOLOGY', '生物', NULL, '实验器材', '解剖器材', NULL, '42301001001', '解剖器', '不锈钢，含镊子、剪刀', '套', 25, 15.00, 375.00, 1, 0, 'JY/T 0384-2007', NULL, '生物解剖实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(22, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_004', 1, 2, 'BIOLOGY', '生物', NULL, '培养器材', '培养皿', NULL, '42401001001', '培养皿', '90mm直径，玻璃', '个', 50, 3.50, 175.00, 1, 0, 'GB/T 12805-2011', NULL, '微生物培养实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(23, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_005', 1, 2, 'BIOLOGY', '生物', NULL, '测量器材', '放大镜', NULL, '42501001001', '放大镜', '10倍，直径50mm', '个', 25, 8.00, 200.00, 1, 0, 'QB/T 1132-2010', NULL, '植物结构观察', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(24, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_006', 1, 2, 'BIOLOGY', '生物', NULL, '标本模型', '植物模型', NULL, '42202001001', '植物细胞模型', '放大1000倍，彩色', '个', 1, 180.00, 180.00, 0, 1, 'JY/T 0385-2007', NULL, '细胞结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(25, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_001', 1, 3, 'PHYSICS', '物理', NULL, '力学实验器材', '精密测量', NULL, '50101001001', '数字化力传感器', '±50N，精度0.01N', '个', 10, 380.00, 3800.00, 1, 0, 'JY/T 0390-2007', NULL, '牛顿定律验证', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(26, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_002', 1, 3, 'PHYSICS', '物理', NULL, '电学实验器材', '数字化设备', NULL, '50201001001', '数字万用表', '3½位，自动量程', '台', 25, 150.00, 3750.00, 1, 0, 'JY/T 0391-2007', NULL, '电路参数测量', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(27, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_003', 1, 3, 'PHYSICS', '物理', NULL, '光学实验器材', '激光器材', NULL, '50301001001', '激光器', '红光，功率<1mW', '台', 5, 280.00, 1400.00, 1, 0, 'JY/T 0392-2007', NULL, '光的干涉衍射实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(28, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_004', 1, 3, 'PHYSICS', '物理', NULL, '现代物理器材', '原子物理', NULL, '50401001001', '盖革计数器', '数字显示，带探头', '台', 2, 1200.00, 2400.00, 0, 1, 'JY/T 0393-2007', NULL, '放射性检测实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(29, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_005', 1, 3, 'PHYSICS', '物理', NULL, '热学实验器材', '温度传感器', NULL, '50501001001', '数字温度计', '-50~150℃，精度0.1℃', '支', 25, 85.00, 2125.00, 1, 0, 'JY/T 0394-2007', NULL, '热力学定律验证', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(30, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_006', 1, 3, 'PHYSICS', '物理', NULL, '波动实验器材', '示波器', NULL, '50601001001', '数字示波器', '双通道，100MHz', '台', 5, 2800.00, 14000.00, 0, 1, 'JY/T 0395-2007', NULL, '波形分析实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(31, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_001', 1, 3, 'CHEMISTRY', '化学', NULL, '精密仪器', '分析天平', NULL, '51101001001', '电子分析天平', '精度0.0001g，量程220g', '台', 2, 8500.00, 17000.00, 1, 0, 'JY/T 0396-2007', NULL, '定量分析实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(32, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_002', 1, 3, 'CHEMISTRY', '化学', NULL, '测量仪器', 'pH计', NULL, '51201001001', '数字pH计', '精度0.01，自动温补', '台', 5, 450.00, 2250.00, 1, 0, 'JY/T 0397-2007', NULL, '酸碱滴定实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:12', '2025-07-18 15:09:12'),
(33, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_003', 1, 3, 'CHEMISTRY', '化学', NULL, '加热设备', '电热板', NULL, '51301001001', '数显电热板', '温控范围50-300℃', '台', 10, 380.00, 3800.00, 1, 0, 'JY/T 0398-2007', NULL, '有机合成实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(34, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_004', 1, 3, 'CHEMISTRY', '化学', NULL, '分离设备', '离心机', NULL, '51401001001', '台式离心机', '最高转速4000rpm', '台', 2, 1200.00, 2400.00, 0, 1, 'JY/T 0399-2007', NULL, '胶体分离实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(35, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_005', 1, 3, 'CHEMISTRY', '化学', NULL, '光谱仪器', '分光光度计', NULL, '51501001001', '可见分光光度计', '波长范围400-700nm', '台', 1, 15000.00, 15000.00, 0, 1, 'JY/T 0400-2007', NULL, '物质浓度测定', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(36, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_006', 1, 3, 'CHEMISTRY', '化学', NULL, '安全设备', '通风橱', NULL, '51601001001', '实验室通风橱', '1.2m宽，变风量控制', '台', 2, 12000.00, 24000.00, 1, 0, 'JY/T 0401-2007', NULL, '有害气体实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(37, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_001', 1, 3, 'BIOLOGY', '生物', NULL, '精密仪器', '荧光显微镜', NULL, '52101001001', '荧光显微镜', '三目，LED光源', '台', 2, 25000.00, 50000.00, 0, 1, 'JY/T 0402-2007', NULL, '细胞荧光标记观察', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(38, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_002', 1, 3, 'BIOLOGY', '生物', NULL, '培养设备', '恒温培养箱', NULL, '52201001001', '生化培养箱', '温控范围5-50℃', '台', 2, 3500.00, 7000.00, 1, 0, 'JY/T 0403-2007', NULL, '微生物培养实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(39, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_003', 1, 3, 'BIOLOGY', '生物', NULL, '分析仪器', '电泳仪', NULL, '52301001001', '水平电泳仪', '凝胶板10×8cm', '套', 5, 1200.00, 6000.00, 1, 0, 'JY/T 0404-2007', NULL, 'DNA电泳分析', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(40, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_004', 1, 3, 'BIOLOGY', '生物', NULL, '测量仪器', '分光光度计', NULL, '52401001001', '紫外分光光度计', '波长范围200-800nm', '台', 1, 18000.00, 18000.00, 0, 1, 'JY/T 0405-2007', NULL, '蛋白质浓度测定', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(41, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_005', 1, 3, 'BIOLOGY', '生物', NULL, '制备设备', '超净工作台', NULL, '52501001001', '生物安全柜', 'II级A2型，1.2m宽', '台', 1, 15000.00, 15000.00, 1, 0, 'JY/T 0406-2007', NULL, '无菌操作实验', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(42, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_006', 1, 3, 'BIOLOGY', '生物', NULL, '模型标本', 'DNA模型', NULL, '52601001001', 'DNA双螺旋模型', '可拆装，彩色编码', '套', 5, 280.00, 1400.00, 1, 0, 'JY/T 0407-2007', NULL, '分子结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13'),
(43, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_001', 1, 1, 'SCIENCE', '科学', '根据教育部最新标准制定的小学科学教学仪器配备要求', '实验室基础器材', '温度测量工具', NULL, '30204001001', '红外温度仪', '红外电子测温，测量范围-20~50℃', '支', 48, 45.00, 2160.00, 1, 0, 'JY/T 0386-2006', NULL, '温度变化观察实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(44, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_002', 1, 1, 'SCIENCE', '科学', NULL, '实验室基础器材', '质量测量工具', NULL, '30202000321', '托盘天平', '500g，配6类M2砝码、镊子', '台', 2, 220.00, 440.00, 1, 0, 'QB/T 2087–2016', NULL, '物体质量测量实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(45, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_001', 1, 1, 'SCIENCE', '科学', NULL, '主题学习器材', '物质领域', '物质的特征', '30601000103', '量筒', '25mL 玻璃，刻度清晰', '个', 12, 6.80, 81.60, 1, 0, 'GB/T 12804-2011', NULL, '探究物体体积', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(46, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_002', 1, 1, 'SCIENCE', '科学', NULL, '主题学习器材', '生命科学领域', '动物与环境', '30701002001', '放大镜', '5倍，直径60mm', '个', 24, 12.50, 300.00, 1, 0, 'QB/T 1132-2010', NULL, '观察昆虫结构', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(47, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_003', 1, 1, 'SCIENCE', '科学', NULL, '主题学习器材', '地球与宇宙领域', '地球运动', '30801003001', '地球仪', '直径20cm，政区版', '个', 1, 85.00, 85.00, 1, 0, 'GB/T 14511-2008', NULL, '地球自转公转演示', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(48, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023_004', 1, 1, 'SCIENCE', '科学', NULL, '实验室基础器材', '光学器材', NULL, '30501001001', '学生显微镜', '单目，10×、40×物镜', '台', 12, 380.00, 4560.00, 0, 1, 'JY/T 0364-2004', NULL, '细胞结构观察', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(49, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_001', 1, 2, 'PHYSICS', '物理', NULL, '力学实验器材', '测力器材', NULL, '40101001001', '弹簧测力计', '5N，精度0.1N', '个', 25, 18.50, 462.50, 1, 0, 'JY/T 0374-2007', NULL, '力的测量实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(50, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_002', 1, 2, 'PHYSICS', '物理', NULL, '电学实验器材', '电源器材', NULL, '40201001001', '学生电源', '直流1.5V、3V、4.5V、6V、9V', '台', 13, 125.00, 1625.00, 1, 0, 'JY/T 0375-2007', NULL, '电路连接实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(51, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_003', 1, 2, 'PHYSICS', '物理', NULL, '光学实验器材', '透镜器材', NULL, '40301001001', '凸透镜', 'f=10cm，直径50mm', '个', 25, 8.50, 212.50, 1, 0, 'JY/T 0376-2007', NULL, '透镜成像实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(52, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_004', 1, 2, 'PHYSICS', '物理', NULL, '热学实验器材', '温度测量', NULL, '40401001001', '温度计', '0~100℃，精度1℃', '支', 25, 15.00, 375.00, 1, 0, 'JY/T 0377-2007', NULL, '温度变化测量', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(53, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_005', 1, 2, 'PHYSICS', '物理', NULL, '声学实验器材', '发声器材', NULL, '40501001001', '音叉', '440Hz，带共鸣箱', '个', 5, 35.00, 175.00, 1, 0, 'JY/T 0378-2007', NULL, '声音传播实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(54, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023_006', 1, 2, 'PHYSICS', '物理', NULL, '力学实验器材', '简单机械', NULL, '40102001001', '滑轮组', '演示用，可组合', '套', 5, 68.00, 340.00, 0, 1, 'JY/T 0379-2007', NULL, '机械效率测定', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(55, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_001', 1, 2, 'CHEMISTRY', '化学', NULL, '玻璃器皿', '反应容器', NULL, '41101001001', '试管', '18×180mm，硼硅玻璃', '支', 100, 2.50, 250.00, 1, 0, 'GB/T 12803-2004', NULL, '化学反应实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(56, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_002', 1, 2, 'CHEMISTRY', '化学', NULL, '玻璃器皿', '测量容器', NULL, '41102001001', '量筒', '50mL，A级精度', '个', 25, 12.00, 300.00, 1, 0, 'GB/T 12804-2011', NULL, '溶液配制实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(57, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_003', 1, 2, 'CHEMISTRY', '化学', NULL, '加热器材', '酒精灯', NULL, '41201001001', '酒精灯', '150mL，带灯芯', '个', 25, 8.50, 212.50, 1, 0, 'JY/T 0380-2007', NULL, '物质加热实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(58, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_004', 1, 2, 'CHEMISTRY', '化学', NULL, '支撑器材', '铁架台', NULL, '41301001001', '铁架台', '高度可调，带夹具', '套', 25, 45.00, 1125.00, 1, 0, 'JY/T 0381-2007', NULL, '装置固定支撑', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(59, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_005', 1, 2, 'CHEMISTRY', '化学', NULL, '分离器材', '过滤器材', NULL, '41401001001', '漏斗', '60mm口径，长颈', '个', 25, 6.00, 150.00, 1, 0, 'GB/T 28211-2011', NULL, '混合物分离实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(60, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023_006', 1, 2, 'CHEMISTRY', '化学', NULL, '测量器材', '天平', NULL, '41501001001', '电子天平', '精度0.1g，量程200g', '台', 5, 280.00, 1400.00, 0, 1, 'JY/T 0382-2007', NULL, '物质质量测定', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(61, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_001', 1, 2, 'BIOLOGY', '生物', NULL, '观察器材', '显微镜', NULL, '42101001001', '生物显微镜', '双目，10×、40×物镜', '台', 25, 1200.00, 30000.00, 1, 0, 'JY/T 0364-2004', NULL, '细胞结构观察', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(62, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_002', 1, 2, 'BIOLOGY', '生物', NULL, '标本模型', '人体模型', NULL, '42201001001', '人体骨骼模型', '85cm高，可拆装', '个', 1, 450.00, 450.00, 1, 0, 'JY/T 0383-2007', NULL, '人体结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(63, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_003', 1, 2, 'BIOLOGY', '生物', NULL, '实验器材', '解剖器材', NULL, '42301001001', '解剖器', '不锈钢，含镊子、剪刀', '套', 25, 15.00, 375.00, 1, 0, 'JY/T 0384-2007', NULL, '生物解剖实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(64, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_004', 1, 2, 'BIOLOGY', '生物', NULL, '培养器材', '培养皿', NULL, '42401001001', '培养皿', '90mm直径，玻璃', '个', 50, 3.50, 175.00, 1, 0, 'GB/T 12805-2011', NULL, '微生物培养实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(65, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_005', 1, 2, 'BIOLOGY', '生物', NULL, '测量器材', '放大镜', NULL, '42501001001', '放大镜', '10倍，直径50mm', '个', 25, 8.00, 200.00, 1, 0, 'QB/T 1132-2010', NULL, '植物结构观察', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(66, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023_006', 1, 2, 'BIOLOGY', '生物', NULL, '标本模型', '植物模型', NULL, '42202001001', '植物细胞模型', '放大1000倍，彩色', '个', 1, 180.00, 180.00, 0, 1, 'JY/T 0385-2007', NULL, '细胞结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(67, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_001', 1, 3, 'PHYSICS', '物理', NULL, '力学实验器材', '精密测量', NULL, '50101001001', '数字化力传感器', '±50N，精度0.01N', '个', 10, 380.00, 3800.00, 1, 0, 'JY/T 0390-2007', NULL, '牛顿定律验证', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(68, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_002', 1, 3, 'PHYSICS', '物理', NULL, '电学实验器材', '数字化设备', NULL, '50201001001', '数字万用表', '3½位，自动量程', '台', 25, 150.00, 3750.00, 1, 0, 'JY/T 0391-2007', NULL, '电路参数测量', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(69, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_003', 1, 3, 'PHYSICS', '物理', NULL, '光学实验器材', '激光器材', NULL, '50301001001', '激光器', '红光，功率<1mW', '台', 5, 280.00, 1400.00, 1, 0, 'JY/T 0392-2007', NULL, '光的干涉衍射实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(70, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_004', 1, 3, 'PHYSICS', '物理', NULL, '现代物理器材', '原子物理', NULL, '50401001001', '盖革计数器', '数字显示，带探头', '台', 2, 1200.00, 2400.00, 0, 1, 'JY/T 0393-2007', NULL, '放射性检测实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(71, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_005', 1, 3, 'PHYSICS', '物理', NULL, '热学实验器材', '温度传感器', NULL, '50501001001', '数字温度计', '-50~150℃，精度0.1℃', '支', 25, 85.00, 2125.00, 1, 0, 'JY/T 0394-2007', NULL, '热力学定律验证', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(72, '高中物理教学仪器配备标准（教育部）', 'MOE_SENIOR_PHYSICS_2023_006', 1, 3, 'PHYSICS', '物理', NULL, '波动实验器材', '示波器', NULL, '50601001001', '数字示波器', '双通道，100MHz', '台', 5, 2800.00, 14000.00, 0, 1, 'JY/T 0395-2007', NULL, '波形分析实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:25', '2025-08-02 19:03:25'),
(73, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_001', 1, 3, 'CHEMISTRY', '化学', NULL, '精密仪器', '分析天平', NULL, '51101001001', '电子分析天平', '精度0.0001g，量程220g', '台', 2, 8500.00, 17000.00, 1, 0, 'JY/T 0396-2007', NULL, '定量分析实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(74, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_002', 1, 3, 'CHEMISTRY', '化学', NULL, '测量仪器', 'pH计', NULL, '51201001001', '数字pH计', '精度0.01，自动温补', '台', 5, 450.00, 2250.00, 1, 0, 'JY/T 0397-2007', NULL, '酸碱滴定实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(75, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_003', 1, 3, 'CHEMISTRY', '化学', NULL, '加热设备', '电热板', NULL, '51301001001', '数显电热板', '温控范围50-300℃', '台', 10, 380.00, 3800.00, 1, 0, 'JY/T 0398-2007', NULL, '有机合成实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(76, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_004', 1, 3, 'CHEMISTRY', '化学', NULL, '分离设备', '离心机', NULL, '51401001001', '台式离心机', '最高转速4000rpm', '台', 2, 1200.00, 2400.00, 0, 1, 'JY/T 0399-2007', NULL, '胶体分离实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(77, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_005', 1, 3, 'CHEMISTRY', '化学', NULL, '光谱仪器', '分光光度计', NULL, '51501001001', '可见分光光度计', '波长范围400-700nm', '台', 1, 15000.00, 15000.00, 0, 1, 'JY/T 0400-2007', NULL, '物质浓度测定', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(78, '高中化学教学仪器配备标准（教育部）', 'MOE_SENIOR_CHEMISTRY_2023_006', 1, 3, 'CHEMISTRY', '化学', NULL, '安全设备', '通风橱', NULL, '51601001001', '实验室通风橱', '1.2m宽，变风量控制', '台', 2, 12000.00, 24000.00, 1, 0, 'JY/T 0401-2007', NULL, '有害气体实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(79, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_001', 1, 3, 'BIOLOGY', '生物', NULL, '精密仪器', '荧光显微镜', NULL, '52101001001', '荧光显微镜', '三目，LED光源', '台', 2, 25000.00, 50000.00, 0, 1, 'JY/T 0402-2007', NULL, '细胞荧光标记观察', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(80, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_002', 1, 3, 'BIOLOGY', '生物', NULL, '培养设备', '恒温培养箱', NULL, '52201001001', '生化培养箱', '温控范围5-50℃', '台', 2, 3500.00, 7000.00, 1, 0, 'JY/T 0403-2007', NULL, '微生物培养实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(81, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_003', 1, 3, 'BIOLOGY', '生物', NULL, '分析仪器', '电泳仪', NULL, '52301001001', '水平电泳仪', '凝胶板10×8cm', '套', 5, 1200.00, 6000.00, 1, 0, 'JY/T 0404-2007', NULL, 'DNA电泳分析', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(82, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_004', 1, 3, 'BIOLOGY', '生物', NULL, '测量仪器', '分光光度计', NULL, '52401001001', '紫外分光光度计', '波长范围200-800nm', '台', 1, 18000.00, 18000.00, 0, 1, 'JY/T 0405-2007', NULL, '蛋白质浓度测定', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(83, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_005', 1, 3, 'BIOLOGY', '生物', NULL, '制备设备', '超净工作台', NULL, '52501001001', '生物安全柜', 'II级A2型，1.2m宽', '台', 1, 15000.00, 15000.00, 1, 0, 'JY/T 0406-2007', NULL, '无菌操作实验', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26'),
(84, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_006', 1, 3, 'BIOLOGY', '生物', NULL, '模型标本', 'DNA模型', NULL, '52601001001', 'DNA双螺旋模型', '可拆装，彩色编码', '套', 5, 280.00, 1400.00, 1, 0, 'JY/T 0407-2007', NULL, '分子结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-08-02 19:03:26', '2025-08-02 19:03:26');

-- --------------------------------------------------------

--
-- 表的结构 `textbook_chapters`
--

CREATE TABLE `textbook_chapters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
  `textbook_version_id` bigint(20) UNSIGNED NOT NULL COMMENT '教材版本ID',
  `grade_level` varchar(20) NOT NULL COMMENT '年级',
  `volume` varchar(20) NOT NULL COMMENT '册次（上册、下册）',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '父级章节ID',
  `level` tinyint(4) NOT NULL COMMENT '层级（1章2节3小节）',
  `code` varchar(50) NOT NULL COMMENT '章节编码（如：01、01-01、01-01-01）',
  `name` varchar(200) NOT NULL COMMENT '章节名称',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `textbook_chapters`
--

INSERT INTO `textbook_chapters` (`id`, `subject_id`, `textbook_version_id`, `grade_level`, `volume`, `parent_id`, `level`, `code`, `name`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 1, '1', '上册', NULL, 1, '01', '力学', 0, 1, '2025-07-20 19:43:40', '2025-07-20 19:43:40'),
(3, 2, 1, '3', '上册', 2, 2, '01-03', '牛顿第三定律', 3, 1, '2025-07-20 19:44:54', '2025-07-24 01:12:12'),
(5, 1, 7, '1', '下册', NULL, 1, '01', '第一单元 我们周围的物体', 1, 1, '2025-07-20 22:43:28', '2025-07-20 22:43:28'),
(6, 1, 7, '1', '下册', NULL, 1, '02', '第二单元 动物', 2, 1, '2025-07-20 22:43:28', '2025-07-20 22:43:28'),
(7, 1, 7, '1', '下册', 6, 2, '02-12', '观察鱼', 0, 1, '2025-07-22 17:09:16', '2025-07-22 17:09:54'),
(8, 1, 7, '1', '下册', 6, 2, '02-08', '我们知道的动物', 0, 1, '2025-07-22 17:10:26', '2025-07-22 17:10:26'),
(9, 1, 7, '1', '下册', 5, 2, '01-01', '发现物体的特征', 0, 1, '2025-07-22 17:11:35', '2025-07-22 17:11:35'),
(10, 2, 1, '1', '上册', 2, 2, '01-01', '牛顿第一定律', 0, 1, '2025-07-24 01:10:21', '2025-07-24 01:12:30'),
(11, 2, 1, '1', '上册', 2, 2, '01-02', '牛顿第二定律', 0, 1, '2025-07-24 01:11:00', '2025-07-24 01:12:37');

-- --------------------------------------------------------

--
-- 表的结构 `textbook_versions`
--

CREATE TABLE `textbook_versions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '版本名称（人教版、苏教版等）',
  `code` varchar(20) NOT NULL COMMENT '版本代码',
  `publisher` varchar(100) DEFAULT NULL COMMENT '出版社',
  `description` text DEFAULT NULL COMMENT '版本描述',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态（1启用0禁用）',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `textbook_versions`
--

INSERT INTO `textbook_versions` (`id`, `name`, `code`, `publisher`, `description`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '人教版', 'PEP', '人民教育出版社', '人民教育出版社出版的教材', 1, 1, '2025-07-20 05:05:08', '2025-07-20 19:24:43'),
(2, '苏教版', 'JSEP', '江苏教育出版社', '江苏教育出版社出版的教材', 1, 2, '2025-07-20 05:05:08', '2025-07-20 05:05:08'),
(3, '北师大版', 'BNU', '北京师范大学出版社', '北京师范大学出版社出版的教材', 1, 3, '2025-07-20 05:05:08', '2025-07-20 05:05:08'),
(4, '沪科版', 'HKEP', '上海科学技术出版社', '上海科学技术出版社出版的教材', 1, 4, '2025-07-20 05:05:08', '2025-07-20 05:05:08'),
(5, '粤教版', 'GDEP', '广东教育出版社', '广东教育出版社出版的教材', 1, 5, '2025-07-20 05:05:08', '2025-07-20 05:05:08'),
(7, '教科版', 'JKB', '教育科学出版社', '教育科学出版社', 1, 6, '2025-07-20 19:36:39', '2025-07-20 19:36:39');

-- --------------------------------------------------------

--
-- 表的结构 `textbook_version_assignments`
--

CREATE TABLE `textbook_version_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assigner_level` tinyint(4) NOT NULL COMMENT '指定者级别（1省2市3区县4学区5学校）',
  `assigner_org_id` bigint(20) UNSIGNED NOT NULL COMMENT '指定者组织ID',
  `assigner_org_type` varchar(20) NOT NULL COMMENT '指定者组织类型',
  `assigner_user_id` bigint(20) UNSIGNED NOT NULL COMMENT '指定操作用户ID',
  `school_id` bigint(20) UNSIGNED NOT NULL COMMENT '目标学校ID',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
  `grade_level` varchar(20) NOT NULL COMMENT '年级',
  `textbook_version_id` bigint(20) UNSIGNED NOT NULL COMMENT '指定的教材版本ID',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1生效 0失效',
  `assignment_reason` text DEFAULT NULL COMMENT '指定理由',
  `effective_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '生效日期',
  `expiry_date` timestamp NULL DEFAULT NULL COMMENT '失效日期',
  `replaced_assignment_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '被替换的指定记录ID',
  `change_reason` text DEFAULT NULL COMMENT '变更理由',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `textbook_version_assignments`
--

INSERT INTO `textbook_version_assignments` (`id`, `assigner_level`, `assigner_org_id`, `assigner_org_type`, `assigner_user_id`, `school_id`, `subject_id`, `grade_level`, `textbook_version_id`, `status`, `assignment_reason`, `effective_date`, `expiry_date`, `replaced_assignment_id`, `change_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'region', 42, 1, 4, '4', 2, 1, '配合实验设备标准化', '2025-06-28 19:08:19', '2027-07-28 19:08:19', NULL, NULL, '2025-07-28 19:08:19', '2025-07-28 19:08:19'),
(2, 1, 1, 'system', 3, 2, 9, '8', 4, 1, '提高教学效果', '2025-07-25 19:08:20', '2026-03-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(3, 1, 1, 'region', 42, 2, 3, '1', 5, 1, '统一教材版本，确保教学质量', '2025-07-13 19:08:20', '2026-12-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(4, 1, 1, 'system', 3, 3, 1, '2', 1, 0, '规范实验教学管理', '2025-07-30 08:15:06', NULL, NULL, '被新指定替换', '2025-07-28 19:08:20', '2025-07-30 00:15:06'),
(5, 1, 1, 'system', 3, 3, 3, '2', 1, 1, '根据学校实际情况指定', '2025-07-17 19:08:20', '2026-09-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(6, 1, 1, 'system', 3, 3, 8, '4', 5, 1, '规范实验教学管理', '2025-07-08 19:08:20', '2027-07-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(7, 1, 1, 'system', 3, 3, 7, '7', 2, 1, '根据学校实际情况指定', '2025-07-05 19:08:20', '2026-07-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(8, 1, 1, 'system', 3, 4, 3, '3', 7, 1, '配合实验设备标准化', '2025-06-30 19:08:20', '2026-07-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(9, 1, 1, 'system', 3, 4, 1, '9', 4, 1, '规范实验教学管理', '2025-07-21 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(10, 1, 1, 'system', 3, 5, 7, '8', 3, 1, '统一教材版本，确保教学质量', '2025-06-29 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(11, 1, 1, 'region', 42, 5, 8, '1', 5, 1, '规范实验教学管理', '2025-06-28 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(12, 1, 1, 'region', 42, 6, 6, '7', 3, 1, '规范实验教学管理', '2025-07-07 19:08:20', '2027-05-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(13, 1, 1, 'region', 42, 6, 2, '9', 5, 1, '统一教材版本，确保教学质量', '2025-07-23 19:08:20', '2026-01-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(14, 1, 1, 'system', 3, 6, 1, '9', 3, 1, '提高教学效果', '2025-07-26 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(15, 1, 1, 'region', 42, 6, 2, '7', 4, 1, '提高教学效果', '2025-07-05 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(16, 1, 1, 'system', 3, 7, 5, '1', 5, 1, '规范实验教学管理', '2025-07-13 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(17, 1, 1, 'system', 3, 7, 8, '2', 7, 1, '提高教学效果', '2025-06-30 19:08:20', '2026-04-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(18, 1, 1, 'region', 42, 8, 8, '9', 7, 1, '统一教材版本，确保教学质量', '2025-07-09 19:08:20', '2026-07-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(19, 1, 1, 'system', 3, 8, 5, '7', 3, 1, '提高教学效果', '2025-07-13 19:08:20', '2027-01-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(20, 1, 1, 'system', 3, 8, 5, '1', 3, 1, '规范实验教学管理', '2025-07-27 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(21, 1, 1, 'region', 42, 8, 7, '6', 2, 1, '根据学校实际情况指定', '2025-07-10 19:08:20', '2026-07-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(22, 1, 1, 'system', 3, 9, 9, '3', 5, 1, '提高教学效果', '2025-07-04 19:08:20', '2026-08-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(23, 1, 1, 'region', 42, 9, 3, '8', 1, 1, '配合实验设备标准化', '2025-07-02 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(24, 1, 1, 'region', 42, 9, 6, '2', 1, 1, '规范实验教学管理', '2025-07-09 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(25, 1, 1, 'system', 3, 9, 7, '8', 2, 1, '统一教材版本，确保教学质量', '2025-07-01 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(26, 1, 1, 'system', 3, 10, 2, '4', 5, 1, '提高教学效果', '2025-07-11 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(27, 1, 1, 'region', 42, 10, 3, '5', 2, 1, '配合实验设备标准化', '2025-07-25 19:08:20', '2026-11-28 19:08:20', NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(28, 1, 1, 'system', 3, 10, 2, '6', 1, 1, '根据学校实际情况指定', '2025-07-19 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(29, 1, 1, 'region', 42, 10, 7, '1', 7, 1, '规范实验教学管理', '2025-07-02 19:08:20', NULL, NULL, NULL, '2025-07-28 19:08:20', '2025-07-28 19:08:20'),
(30, 3, 10, 'region', 44, 3, 1, '1', 7, 1, NULL, '2025-07-30 00:15:06', NULL, NULL, NULL, '2025-07-30 00:15:06', '2025-07-30 00:15:06'),
(31, 3, 10, 'region', 44, 3, 1, '2', 7, 1, NULL, '2025-07-30 00:15:06', NULL, 4, NULL, '2025-07-30 00:15:06', '2025-07-30 00:15:06'),
(32, 3, 10, 'region', 44, 20, 1, '1', 7, 1, NULL, '2025-07-30 00:15:06', NULL, NULL, NULL, '2025-07-30 00:15:06', '2025-07-30 00:15:06'),
(33, 3, 10, 'region', 44, 20, 1, '2', 7, 1, NULL, '2025-07-30 00:15:06', NULL, NULL, NULL, '2025-07-30 00:15:06', '2025-07-30 00:15:06'),
(34, 3, 10, 'region', 44, 15, 1, '1', 7, 1, NULL, '2025-07-30 00:15:07', NULL, NULL, NULL, '2025-07-30 00:15:07', '2025-07-30 00:15:07'),
(35, 3, 10, 'region', 44, 15, 1, '2', 7, 1, NULL, '2025-07-30 00:15:07', NULL, NULL, NULL, '2025-07-30 00:15:07', '2025-07-30 00:15:07'),
(36, 3, 10, 'region', 44, 16, 1, '1', 7, 1, NULL, '2025-07-30 00:15:07', NULL, NULL, NULL, '2025-07-30 00:15:07', '2025-07-30 00:15:07'),
(37, 3, 10, 'region', 44, 16, 1, '2', 7, 1, NULL, '2025-07-30 00:15:07', NULL, NULL, NULL, '2025-07-30 00:15:07', '2025-07-30 00:15:07'),
(38, 3, 10, 'region', 44, 1, 1, '1', 7, 1, NULL, '2025-07-30 00:15:07', NULL, NULL, NULL, '2025-07-30 00:15:07', '2025-07-30 00:15:07'),
(39, 3, 10, 'region', 44, 1, 1, '2', 7, 1, NULL, '2025-07-30 00:15:07', NULL, NULL, NULL, '2025-07-30 00:15:07', '2025-07-30 00:15:07');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `real_name` varchar(50) NOT NULL COMMENT '真实姓名',
  `name` varchar(50) DEFAULT NULL COMMENT '姓名（别名字段）',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `gender` tinyint(4) DEFAULT NULL COMMENT '性别：1男 2女',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `id_card` varchar(18) DEFAULT NULL COMMENT '身份证号',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1正常 0禁用',
  `role` varchar(50) DEFAULT NULL COMMENT '用户角色',
  `department` varchar(100) DEFAULT NULL COMMENT '部门',
  `position` varchar(100) DEFAULT NULL COMMENT '职位',
  `bio` text DEFAULT NULL COMMENT '个人简介',
  `school_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '学校ID',
  `school_name` varchar(100) DEFAULT NULL COMMENT '学校名称',
  `organization_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '主要组织ID（区域或学校）',
  `organization_type` varchar(20) DEFAULT NULL COMMENT '组织类型：region/school',
  `organization_level` tinyint(4) DEFAULT NULL COMMENT '组织级别：1省 2市 3区县 4学区 5学校',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT '邮箱验证时间',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `real_name`, `name`, `avatar`, `gender`, `birthday`, `id_card`, `status`, `role`, `department`, `position`, `bio`, `school_id`, `school_name`, `organization_id`, `organization_type`, `organization_level`, `last_login_at`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 'admin', 'admin@example.com', NULL, '$2y$12$Y73v9WLCwX8jyeIwOG6Yau1sKg8/yEL8KMAAWBVdAc3GDoEaajgF6', '系统管理员', '系统管理员', NULL, NULL, NULL, NULL, 1, '省级管理员', NULL, NULL, NULL, NULL, NULL, 1, 'system', 1, '2025-07-18 15:36:07', NULL, NULL, '2025-07-18 15:21:21', '2025-07-27 00:49:51'),
(4, 'province_admin_1', 'province_admin_0@example.com', NULL, '$2y$12$uBYPxEmDIQeJzYPgJrOmLeCNSke0hp3HM6qcTmaVB9wBbJQOsZyBW', '张伟', '张伟', NULL, NULL, NULL, NULL, 1, '省级管理员', NULL, NULL, NULL, NULL, NULL, 1, 'province', NULL, '2025-07-18 15:39:49', NULL, NULL, '2025-07-18 15:21:21', '2025-07-27 00:49:51'),
(5, 'province_researcher_1', 'province_researcher_0@example.com', NULL, '$2y$12$j2K.rM3KHIdlyviiUfcvFeBJ5x5N.ODFIIgSeld03uXIKK/mPrAve', '李娜', '李娜', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 1, 'province', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-27 00:49:51'),
(6, 'city_admin_1', 'city_admin_0@example.com', NULL, '$2y$12$8C/THpejw4tTgjBpLU6c8OGnnodiN7VFsHVOZpGtTIOW3ImNq5eZy', '王强1', '王强1', NULL, NULL, NULL, NULL, 1, 'city_admin', NULL, NULL, NULL, NULL, NULL, 9, 'region', 2, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-27 00:49:51'),
(7, 'city_researcher_1', 'city_researcher_0@example.com', NULL, '$2y$12$VrfLYXt1pvav56Zgjr6UEuARtsEVIDPLKzAGhWG/057i3dTZ64C6q', '刘敏', '刘敏', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 9, 'city', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-27 00:49:51'),
(8, 'county_admin_1', 'county_admin_0@example.com', NULL, '$2y$12$A3OtwNWxRLGa9gk1Rrzrfu2Mz5daEmR1yMju/qh9x1SWy2LXo.w6.', '陈静', '陈静', NULL, NULL, NULL, NULL, 1, '区县管理员', NULL, NULL, NULL, NULL, NULL, 10, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-27 00:49:51'),
(9, 'county_researcher_1', 'county_researcher_0@example.com', NULL, '$2y$12$fBxwKINv8v27cO1z6VYnyusY3bxoLEsbvAqJfL5Xpw.39bFaRAFS2', '杨勇', '杨勇', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 10, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-27 00:49:51'),
(10, 'county_admin_2', 'county_admin_1@example.com', NULL, '$2y$12$CUDzeiFKvKYz6UWv/8FszOPLalm4E9PO2FDNpWhvD3yNVjFyNv7YO', '赵丽', '赵丽', NULL, NULL, NULL, NULL, 1, '区县管理员', NULL, NULL, NULL, NULL, NULL, 15, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-27 00:49:51'),
(11, 'county_researcher_2', 'county_researcher_1@example.com', NULL, '$2y$12$VU1yCyt68Yg/w8zMHrEVOeplV5FM8f2.4Q6FoiiZHM/bg2tj9oIK.', '黄磊', '黄磊', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 15, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-27 00:49:51'),
(12, 'school_admin_1', 'school_admin_0@example.com', NULL, '$2y$12$azMzm2fcExzqbdhe7IlBReb2wGMYfxaNudEjuHKRz2EEBgbNN.mTG', '周杰', '周杰', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 1, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-27 00:49:52'),
(13, 'principal_1', 'principal_0@example.com', NULL, '$2y$12$PEFveT6dMzE42ZSxhrX.6ug4EzeK6bMQ8n2skIVN/TpMmL6V2R6aG', '吴琳', '吴琳', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 2, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:24', '2025-07-27 00:49:52'),
(14, 'teacher_0_1', 'teacher_0_1@example.com', NULL, '$2y$12$NVQegDa7FRFdi9ico3Tb2u5G4nspAeOUVMlis4.6Y6uiUlFmQdjuu', '徐明', '徐明', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 3, NULL, 1, 'school', NULL, '2025-07-24 01:21:47', NULL, NULL, '2025-07-18 15:21:24', '2025-07-27 00:49:52'),
(15, 'teacher_0_2', 'teacher_0_2@example.com', NULL, '$2y$12$T3fmcf9kVVpm.yrL7h.J9.K8f7F6cB8q9iLL8D0xwCj81GiZWtoR6', '朱红-实验中学', '朱红-实验中学', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, '2025-07-24 23:17:15', NULL, NULL, '2025-07-18 15:21:24', '2025-07-27 00:49:52'),
(16, 'teacher_0_3', 'teacher_0_3@example.com', NULL, '$2y$12$1TRo5cdwrCW4MFLdExNH8uT576WIwdNHhoqPjifw2XTxRWET4WS9y', '马超', '马超', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 5, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:24', '2025-07-27 00:49:52'),
(17, 'school_admin_2', 'school_admin_1@example.com', NULL, '$2y$12$NVNS8vzbkvtYVek44Ve/8./GtwGeytYrsdzLALTbvB5KGgzotKAyK', '胡斌', '胡斌', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 6, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:25', '2025-07-27 00:49:52'),
(18, 'principal_2', 'principal_1@example.com', NULL, '$2y$12$odUiwoVZk5m4zC7srF4MmOWMdptyrwhtlnTh/XZLkXCv0rKR9HfrG', '郭华', '郭华', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 7, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:25', '2025-07-27 00:49:52'),
(19, 'teacher_1_1', 'teacher_1_1@example.com', NULL, '$2y$12$ZoQ5p.plSU2YQ6t5kmzk6.Aix8.LmjN2e0mjgexj0Dv7zLq20lvsq', '林峰', '林峰', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 8, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:25', '2025-07-27 00:49:52'),
(20, 'teacher_1_2', 'teacher_1_2@example.com', NULL, '$2y$12$ibkMTNFPAuZT8LNhBIWAHOjm4x9vbPZV5YNMjYPSAA90SgTZWp0QG', '何艳', '何艳', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 9, NULL, 9, 'school', NULL, '2025-07-28 04:46:27', NULL, NULL, '2025-07-18 15:21:26', '2025-07-28 04:46:27'),
(21, 'teacher_1_3', 'teacher_1_3@example.com', NULL, '$2y$12$IHDYqBzHKRX9TXJdQXpwaOFNGEv2NonQ08mlVuUHQOG6XUkNwhYBm', '高飞', '高飞', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 10, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-27 00:49:52'),
(22, 'school_admin_3', 'school_admin_2@example.com', NULL, '$2y$12$XAWoiGsa9eMf64y1qyNUMOvlACTzF9uVHKuRL01FEDanXh5r4JYxS', '梁雪', '梁雪', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 11, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-27 00:49:52'),
(23, 'principal_3', 'principal_2@example.com', NULL, '$2y$12$ZCk5g5ChyAfAv8CNfTpfDeFXa73QY1ldkhgOsnsSmKX6VLX3CJKbm', '宋涛', '宋涛', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 12, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-27 00:49:52'),
(24, 'teacher_2_1', 'teacher_2_1@example.com', NULL, '$2y$12$aSD7ycfK/boKTkdmZUXS8uIB16X1kYCXz2/CQoLAmr.FVgyD.t6je', '唐丽', '唐丽', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 13, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-27 00:49:52'),
(25, 'teacher_2_2', 'teacher_2_2@example.com', NULL, '$2y$12$X.wCDS3XwGNeCKY0BaaU5.iN1VRpDf6ONvPMlMoIqTtkUjeruM9hW', '韩冰', '韩冰', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 14, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-27 00:49:52'),
(26, 'teacher_2_3', 'teacher_2_3@example.com', NULL, '$2y$12$70b7A1ycMC7ridYDLHFrP.xifrfbbcVoRsP8LLTcKSduzeUfOhMr.', '冯军', '冯军', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 15, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-27 00:49:52'),
(27, 'school_admin_4', 'school_admin_3@example.com', NULL, '$2y$12$0SV46Bo6xcZ0JBRTh2KtXuFvU8/F68n9AbivW5KSW4yvZurVZpvUK', '曹阳', '曹阳', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 16, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-27 00:49:52'),
(28, 'principal_4', 'principal_3@example.com', NULL, '$2y$12$iJfOK/lPaEIzZm28UVPNQe3oqatuhf9fsKi.7FnC0HgKZpD9Fg47G', '彭亮', '彭亮', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 17, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-27 00:49:52'),
(29, 'teacher_3_1', 'teacher_3_1@example.com', NULL, '$2y$12$mEvoCJ4kmN75Cjz66U2zGOvv2.nNscHOSGebvfcV57uoXn7.xiIuy', '董梅', '董梅', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 18, NULL, 15, 'school', NULL, '2025-07-23 19:39:27', NULL, NULL, '2025-07-18 15:21:28', '2025-07-27 00:49:52'),
(30, 'teacher_3_2', 'teacher_3_2@example.com', NULL, '$2y$12$dqeyBS7QM1JgFeeJgjfubepocUcQ2jpU1SVorkHwdGr6I1ZVbyxGi', '袁刚', '袁刚', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 19, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-27 00:49:52'),
(31, 'teacher_3_3', 'teacher_3_3@example.com', NULL, '$2y$12$y5xwkVo.2d1uvq3udknq.uqfh4aPa8jpHmldKD9yjqWC2aodZ.3n.', '邓萍', '邓萍', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 20, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-27 00:49:52'),
(32, 'school_admin_5', 'school_admin_4@example.com', NULL, '$2y$12$AwSBfa1DVdJgrP4fTdXMDeJk7ZXE4VCqjDEWlmfeOeIdCSKGKz9nC', '范伟', '范伟', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 21, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-27 00:49:52'),
(33, 'principal_5', 'principal_4@example.com', NULL, '$2y$12$RGNt29pARCtE8tulnjHSnung2EEVFNiE94vVeSFP9KwHCc6c0ZwKi', '石磊', '石磊', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 1, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-27 00:49:52'),
(34, 'teacher_4_1', 'teacher_4_1@example.com', NULL, '$2y$12$Gf52nnWzEUCUPpUZUaHv8e69Vwr6voH104V9zhMtshiZSZuonUVhC', '郑州市二七区实验小学实验教师1', '郑州市二七区实验小学实验教师1', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 2, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-27 00:49:52'),
(35, 'teacher_4_2', 'teacher_4_2@example.com', NULL, '$2y$12$WyekQawv5ViP6p9.SBvjCeOdoDP9a8k6Uq/lSpj87t7G5M5L4hQQm', '郑州市二七区实验小学实验教师2', '郑州市二七区实验小学实验教师2', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 3, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-27 00:49:52'),
(36, 'teacher_4_3', 'teacher_4_3@example.com', NULL, '$2y$12$vu.QSvjaDDu2/pitZxQoXunNDhBrpIOuuc04IL1/4gyvI5TmWs0TC', '实验中学-实验教师3', '实验中学-实验教师3', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-27 00:49:52'),
(37, 'school_admin_6', 'school_admin_5@example.com', NULL, '$2y$12$koZnJ6e3LM1T4NO1YP8QvO7/1rBvSFeGZe9S7SPV/I9Xum7OPehQO', '郑州市二七区九年制学校管理员', '郑州市二七区九年制学校管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 5, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-27 00:49:52'),
(38, 'principal_6', 'principal_5@example.com', NULL, '$2y$12$f9iWErSOkJSqqdrWTNvixe9NfF7qJzVt3yPkfCfHNhoN1UM6fCPQm', '郑州市二七区九年制学校校长', '郑州市二七区九年制学校校长', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 6, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-27 00:49:52'),
(39, 'teacher_5_1', 'teacher_5_1@example.com', NULL, '$2y$12$YYOa75K22mUrBgzAAfP4S.EGwCvmWtqTv7lXwiLyrAzrVsby/A2LW', '郑州市二七区九年制学校实验教师1', '郑州市二七区九年制学校实验教师1', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 7, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-27 00:49:52'),
(40, 'teacher_5_2', 'teacher_5_2@example.com', NULL, '$2y$12$Z1L/8d4HeVaJ1ZLc2DWd1u7H588qLheiqxGqAaIv6dbcTjXRWlm/K', '郑州市二七区九年制学校实验教师2', '郑州市二七区九年制学校实验教师2', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 8, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:31', '2025-07-27 00:49:52'),
(41, 'teacher_5_3', 'teacher_5_3@example.com', NULL, '$2y$12$HmO7O4XcKQf/mZWrwHrHc.qMniM.SYh4.xCONC8DtqH9LrrUwThm6', '郑州市二七区九年制学校实验教师3', '郑州市二七区九年制学校实验教师3', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 9, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:31', '2025-07-27 00:49:52'),
(42, 'province_admin_test', 'province_admin@hebei.edu.cn', '0311-12345678', '$2y$12$RiSgNjSRKelrCh0NoQ1E7OcpvJ2ikV9gpGZOS91qgSHJ4C3iijrGW', '河北省管理员', '河北省管理员', NULL, NULL, NULL, NULL, 1, '省级管理员', NULL, NULL, NULL, NULL, NULL, 1, 'region', 1, '2025-08-03 00:21:59', NULL, NULL, '2025-07-18 15:31:17', '2025-08-03 00:21:59'),
(43, 'city_admin_test', 'city_admin@sjz.edu.cn', '0311-23456789', '$2y$12$3AuJimHjo0YeHW5zaLPJvuVH6ININ7jYKqPbdFBwLinuU265nxRfu', '石家庄市管理员', '石家庄市管理员', NULL, NULL, NULL, NULL, 1, '市级管理员', NULL, NULL, NULL, NULL, NULL, 9, 'region', 2, '2025-08-03 01:50:58', NULL, NULL, '2025-07-18 15:31:17', '2025-08-03 01:50:58'),
(44, 'county_admin_test', 'county_admin@gaocheng.edu.cn', '0311-34567890', '$2y$12$Kc.X162hdYNfs/LnUcfcmexnbECQ9qiRuMibtwdhiR6CGT9Q4GyTC', '藁城区管理员', '藁城区管理员', NULL, NULL, NULL, NULL, 1, '区县管理员', NULL, NULL, NULL, NULL, NULL, 10, 'region', 3, '2025-08-04 00:24:32', NULL, NULL, '2025-07-18 15:31:17', '2025-08-04 00:24:32'),
(45, 'district_admin_test', 'district_admin@lianzhou.edu.cn', '0311-45678901', '$2y$12$KjcDqoNnyFdHjAlBcQDzmuxPUcFjHcStKe6M6FDHCUhYTSpxNa.5G', '廉州学区管理员', '廉州学区管理员', NULL, NULL, NULL, NULL, 1, '学区管理员', NULL, NULL, NULL, NULL, NULL, 11, 'region', 4, '2025-08-04 00:22:35', NULL, NULL, '2025-07-18 15:31:18', '2025-08-04 00:22:35'),
(46, 'school_admin_test', 'school_admin@school.edu.cn', '0311-56789012', '$2y$12$FNK9kzw3gdZLplLvz5yBoubzpj7JPLlb8UF3kpCid8obOHueAyFpC', '学校管理员', '学校管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 15, NULL, 11, 'school', 5, '2025-08-03 23:24:49', NULL, NULL, '2025-07-18 15:31:18', '2025-08-03 23:24:49'),
(47, 'province_school_admin', 'province_school@test.com', NULL, '$2y$12$RseUUp9UeNuAq9PkdznhIOsAYVbsolwT5icZDZXOfDAR8bvXTLIle', '石家庄精英中学管理员', '石家庄精英中学管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:18', '2025-07-27 00:49:53'),
(48, 'city_school_admin', 'city_school@test.com', NULL, '$2y$12$4cBIduQCinkyfr1HymX4eOBawsln5MTM6HnF9UP8yeJdaC6sMROe2', '石家庄市第一中学管理员', '石家庄市第一中学管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 11, NULL, 11, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:19', '2025-07-27 00:49:53'),
(49, 'county_school_admin', 'county_school@test.com', NULL, '$2y$12$L6tI7Ac/TplCp7FbNyhrZevOkiOpitGgdkaOlLON2hjOvrdU3eF/K', '通安小学管理员', '通安小学管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 19, NULL, 19, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:19', '2025-07-27 00:49:53'),
(50, 'school_admin', 'school_admin@example.com', NULL, '$2y$12$HorJAWuhBDWFbICg8WctkOYSqdcJK.Jt7lotaJbTc4VOJa4Shccu6', '校长', '校长', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 1, NULL, 11, NULL, NULL, NULL, NULL, NULL, '2025-07-18 15:31:38', '2025-07-27 00:49:53'),
(51, 'student001', 'student001@163.com', NULL, '$2y$12$BGGyj2edBlfsRFWxbL389uY5lvlAxMcZwWP1xV4ro8oZMwnTJc7pW', 'student001', 'student001', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 1, NULL, 11, NULL, NULL, NULL, NULL, NULL, '2025-07-18 15:31:39', '2025-07-27 00:49:53'),
(53, 'teacher_1_4', 'teacher_1_4@example.com', NULL, '$2y$12$wut2VEVVuC6DnN6hA17D3uwDM5haFcjUPyDGNrnV/sjmqrRxTexXS', '区实验小学实验教师4', '区实验小学实验教师4', NULL, NULL, NULL, NULL, 1, 'school_teacher', '实验教学部', '实验教师', NULL, 1, NULL, 1, 'school', 5, '2025-07-24 23:42:54', NULL, NULL, '2025-07-19 23:52:42', '2025-07-27 00:49:53'),
(54, 'teacher_1_5', 'teacher_1_5@example.com', NULL, '$2y$12$GueqASAfp2pBgWXlJ/Yod.bFy8GAYtS5UI0jzoP7dShDUnFbVPIou', '石家庄市藁城区实验小学实验教师5', '石家庄市藁城区实验小学实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 1, NULL, 1, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:42', '2025-07-27 00:49:53'),
(55, 'teacher_2_4', 'teacher_2_4@example.com', NULL, '$2y$12$tmMFb3xWS21OqKxdmNXo..S66YxOzL4zzq2/W4qLiHZRYGbTf31rS', '石家庄市第一中学实验教师4', '石家庄市第一中学实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 2, NULL, 2, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:42', '2025-07-27 00:49:53'),
(56, 'teacher_2_5', 'teacher_2_5@example.com', NULL, '$2y$12$tGgHWu/Q9M34SOKkPEBayui0OLQlBh1qE8JsbpLbVkbaVfkOuaS6W', '石家庄市第一中学实验教师5', '石家庄市第一中学实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 2, NULL, 2, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:42', '2025-07-27 00:49:53'),
(57, 'teacher_3_4', 'teacher_3_4@example.com', NULL, '$2y$12$Q.Y5EznFtXCqipDnyknpfOLK7/awJzZ7whEIxQ5lx3x8Ekmq.Q4zO', '石家庄市藁城区第二小学实验教师4', '石家庄市藁城区第二小学实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:43', '2025-07-27 00:49:53'),
(58, 'teacher_3_5', 'teacher_3_5@example.com', NULL, '$2y$12$cUtskyVGT6nzf0hOKdG02u03RCr0Li9FT/4OHD5hJ3HSYahny2ume', '石家庄市藁城区第二小学实验教师5', '石家庄市藁城区第二小学实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:43', '2025-07-27 00:49:53'),
(59, 'teacher_4_4', 'teacher_4_4@example.com', NULL, '$2y$12$9syvA9of7/Uwl8gaemOUOeLy7/C/8OQGvIzH2BEkIx49SQru1i3Yy', '实验中学实验教师4', '实验中学实验教师4', NULL, NULL, NULL, NULL, 1, 'school_teacher', '实验教学部', '实验教师', NULL, 4, NULL, 4, 'school', 5, '2025-07-24 01:30:42', NULL, NULL, '2025-07-19 23:52:43', '2025-07-27 00:49:53'),
(60, 'teacher_4_5', 'teacher_4_5@example.com', NULL, '$2y$12$3z3bjXLJMlEmJUP9Tk5QROJoEbIGFjTTpf4ttkAwPqx.v139OSpAG', '实验中学实验教师5', '实验中学实验教师5', NULL, NULL, NULL, NULL, 1, 'school_teacher', '实验教学部', '实验教师', NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:44', '2025-07-27 00:49:53'),
(61, 'teacher_5_4', 'teacher_5_4@example.com', NULL, '$2y$12$./SGG24/shEaLBauh.YQ7.nP.CM7PkMlbwDgbgNQkDEEK/n34D2Gi', '石家庄市栾城区实验小学实验教师4', '石家庄市栾城区实验小学实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:44', '2025-07-27 00:49:53'),
(62, 'teacher_5_5', 'teacher_5_5@example.com', NULL, '$2y$12$ijRmE3fzGnRmOheTz9Wj4e/lBFG//zF6xgcgmH.mXgxul/6uqjGO6', '石家庄市栾城区实验小学实验教师5', '石家庄市栾城区实验小学实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:44', '2025-07-27 00:49:53'),
(63, 'teacher_6_1', 'teacher_6_1@example.com', NULL, '$2y$12$C53PazXzVpqbm.oD/GOqEuvygxW6K1GKg67sURgzRiznAWQUQdXoa', '石家庄市栾城区九年制学校实验教师1', '石家庄市栾城区九年制学校实验教师1', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 6, NULL, 6, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:44', '2025-07-27 00:49:53'),
(64, 'teacher_6_2', 'teacher_6_2@example.com', NULL, '$2y$12$2SpHH.9nZ3mRPERGv9whWuDMW58piKyphCl/IBOFxUjS14taBygbi', '石家庄市栾城区九年制学校实验教师2', '石家庄市栾城区九年制学校实验教师2', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 6, NULL, 6, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:45', '2025-07-27 00:49:53'),
(65, 'teacher_6_3', 'teacher_6_3@example.com', NULL, '$2y$12$spaFXwKOW0xLTlFcgO3iBe8bbQg3Lnn7Thv6qtpwljBeZKlfpVwiS', '石家庄市栾城区九年制学校实验教师3', '石家庄市栾城区九年制学校实验教师3', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 6, NULL, 6, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:45', '2025-07-27 00:49:53'),
(66, 'teacher_6_4', 'teacher_6_4@example.com', NULL, '$2y$12$rZ59m4/vYydJ7zJWdVUNn.Aosz499CK93RuUFEHsYUG9ymWNE5Iwi', '石家庄市栾城区九年制学校实验教师4', '石家庄市栾城区九年制学校实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 6, NULL, 6, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:45', '2025-07-27 00:49:53'),
(67, 'teacher_6_5', 'teacher_6_5@example.com', NULL, '$2y$12$qMgRBpHpg82v08qy7cyyaOdJFEJhHS8Pm/tL9vp0dGav6PKi9uuye', '石家庄市栾城区九年制学校实验教师5', '石家庄市栾城区九年制学校实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 6, NULL, 6, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:45', '2025-07-27 00:49:53'),
(68, 'teacher_7_1', 'teacher_7_1@example.com', NULL, '$2y$12$BC.uAWjES04Khqjz6aXoIeSDUuoTSXmKsv3rQv1kXslJjpYx4ahLG', '石家庄精英中学实验教师1', '石家庄精英中学实验教师1', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:45', '2025-07-27 00:49:53'),
(69, 'teacher_7_2', 'teacher_7_2@example.com', NULL, '$2y$12$rR8b7JioYEreZ5TknXA05O4Wco25x5bT89gmywaWJAZPLxlLqdxI6', '石家庄精英中学实验教师2', '石家庄精英中学实验教师2', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:46', '2025-07-27 00:49:53'),
(70, 'teacher_7_3', 'teacher_7_3@example.com', NULL, '$2y$12$phcpkimeOKM5FKimt6DWaORyr/ihz7a/JnEAU2zrOYjFR88FExFrK', '石家庄精英中学实验教师3', '石家庄精英中学实验教师3', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:46', '2025-07-27 00:49:53'),
(71, 'teacher_7_4', 'teacher_7_4@example.com', NULL, '$2y$12$y/.7eO/jR3hIgoXVMTeupe3BlHFwaeSU5PFsSFtKtfcKMVlQ1p/fO', '石家庄精英中学实验教师4', '石家庄精英中学实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:46', '2025-07-27 00:49:53'),
(72, 'teacher_7_5', 'teacher_7_5@example.com', NULL, '$2y$12$TGzwZZk/vDQq1nqPG.RFhuuxtz0n204KLItK8CuZpGbdBMFgVVAB.', '石家庄精英中学实验教师5', '石家庄精英中学实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:46', '2025-07-27 00:49:53'),
(73, 'teacher_8_1', 'teacher_8_1@example.com', NULL, '$2y$12$YsXn6.JZCfboGuEozbbEDuUQoQywjLMd45I26p80E5uJ8VEmy/EyC', '衡水中学实验教师1', '衡水中学实验教师1', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 8, NULL, 8, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:47', '2025-07-27 00:49:53'),
(74, 'teacher_8_2', 'teacher_8_2@example.com', NULL, '$2y$12$rcT99LPJ8c8J0q9VPvCUSulEoUqk85mCjXSqWGkZEyGUEPY26mLnK', '衡水中学实验教师2', '衡水中学实验教师2', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 8, NULL, 8, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:47', '2025-07-27 00:49:53'),
(75, 'teacher_8_3', 'teacher_8_3@example.com', NULL, '$2y$12$whke8jjodF6iVVKN4LxOae4oVvB8nMYOet8g1pu/IXJE121g2HFwW', '衡水中学实验教师3', '衡水中学实验教师3', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 8, NULL, 8, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:47', '2025-07-27 00:49:53'),
(76, 'teacher_8_4', 'teacher_8_4@example.com', NULL, '$2y$12$cZVgyb9p.Qa2jT8orJTUXerl4Qkv6wMiadzgoniib0YJy9qbF9.ei', '衡水中学实验教师4', '衡水中学实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 8, NULL, 8, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:48', '2025-07-27 00:49:53'),
(77, 'teacher_8_5', 'teacher_8_5@example.com', NULL, '$2y$12$qdNgz2xmUVSIn7VQcCZFL.FK1B.D6vbtTgNJxOy8JB6oNxxdGOLfS', '衡水中学实验教师5', '衡水中学实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 8, NULL, 8, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:48', '2025-07-27 00:49:53'),
(78, 'teacher_9_1', 'teacher_9_1@example.com', NULL, '$2y$12$uxDXC8w.1w7SXqHSCn2mF.PvDRVqg4IQbyShNv8Cj8qJk/I1rtx.W', '保定七中实验教师1', '保定七中实验教师1', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 9, NULL, 9, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:48', '2025-07-27 00:49:53'),
(79, 'teacher_9_2', 'teacher_9_2@example.com', NULL, '$2y$12$M8NalE/t4Z0QlAOD/7dzXOnE3LSEruIGrvo82PWEFf0oUUJcMO8vy', '保定七中实验教师2', '保定七中实验教师2', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 9, NULL, 9, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:48', '2025-07-27 00:49:54'),
(80, 'teacher_9_3', 'teacher_9_3@example.com', NULL, '$2y$12$p7/XPZAcm14CMJzAq05OT.qTzyYdpKMBmz8I/wd1q1wMjpNuXSFR2', '保定七中实验教师3', '保定七中实验教师3', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 9, NULL, 9, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:49', '2025-07-27 00:49:54'),
(81, 'teacher_9_4', 'teacher_9_4@example.com', NULL, '$2y$12$VpF28kGJJgrYpS4.PuyI8.NVWA5YrFpOoEWc.1zjBOjRSX6khly82', '保定七中实验教师4', '保定七中实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 9, NULL, 9, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:49', '2025-07-27 00:49:54'),
(82, 'teacher_9_5', 'teacher_9_5@example.com', NULL, '$2y$12$703UlXh8kUGuiE4.X797j.ivD9iKZWYv4P4OPoQ46xloF8sMHyAeW', '保定七中实验教师5', '保定七中实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 9, NULL, 9, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:49', '2025-07-27 00:49:54'),
(83, 'teacher_10_1', 'teacher_10_1@example.com', NULL, '$2y$12$hVw3DAug4d0Fq1UBj2K7KuLItQSMsLdh2DGxxkcTNVq3xi8UEg2Va', '邢台一中实验教师1', '邢台一中实验教师1', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 10, NULL, 10, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:49', '2025-07-27 00:49:54'),
(84, 'teacher_10_2', 'teacher_10_2@example.com', NULL, '$2y$12$wd.3yTP8.p9Gs.TqSp8YEO.Gd30ren/MsSF5hNWakYuGDK57Q7Ati', '邢台一中实验教师2', '邢台一中实验教师2', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 10, NULL, 10, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:50', '2025-07-27 00:49:54'),
(85, 'teacher_10_3', 'teacher_10_3@example.com', NULL, '$2y$12$oYOSlg/u5OxQ/h4Xpv.9vecqZmyVivgqhdAkK0URmSJQU/mFy0y9O', '邢台一中实验教师3', '邢台一中实验教师3', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 10, NULL, 10, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:50', '2025-07-27 00:49:54'),
(86, 'teacher_10_4', 'teacher_10_4@example.com', NULL, '$2y$12$YpeEKxWPHV3LvrEU01DfL.tybXqRdSGpOv1xn603325xgkfRwmGIe', '邢台一中实验教师4', '邢台一中实验教师4', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 10, NULL, 10, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:50', '2025-07-27 00:49:54'),
(87, 'teacher_10_5', 'teacher_10_5@example.com', NULL, '$2y$12$0eAE2SJU6DHtsAKPhbE9JeHVnP/nmn87Ucd8f1MExfjAVJGk4yCE6', '邢台一中实验教师5', '邢台一中实验教师5', NULL, NULL, NULL, NULL, 1, 'teacher', '实验教学部', '实验教师', NULL, 10, NULL, 10, 'school', 5, NULL, NULL, NULL, '2025-07-19 23:52:50', '2025-07-27 00:49:54'),
(88, 'test_teacher', 'test_teacher@example.com', NULL, '$2y$12$as4fv4KkiTbBy0YMIhIwnOb3PIblOCtfDT0rE8fukCaNLuGyeoje.', '测试任课教师', '测试任课教师', NULL, NULL, NULL, NULL, 1, 'school_teacher', '科学教研', '科学教师', NULL, 1, NULL, 1, 'school', 5, '2025-07-24 01:37:21', NULL, NULL, '2025-07-24 01:08:57', '2025-07-27 00:49:54'),
(90, 'test11', 'test11@163.com', NULL, '$2y$12$PUmg0B2/DYxx4ZBvhUQ9sOBm22Wna2edMhfeEPiupAusUAhX4HxPa', '测试教师', '测试教师', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 1, NULL, 1, 'school', 5, '2025-07-24 23:20:57', NULL, NULL, '2025-07-24 04:05:56', '2025-07-27 00:49:54'),
(91, 'liceshi', 'liceshi@163.com', '15129889877', '$2y$12$H7J3aUUbbkrhmmpUQndAmeztviegf/hJxKl4gMdvLJv2LBy96tY0e', 'liceshi', 'liceshi', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, '2025-07-27 22:23:31', NULL, NULL, '2025-07-24 05:16:53', '2025-07-27 22:23:31'),
(94, 'liceshi1', 'liceshi1@163.com', '15134556577', '$2y$12$3Vq.aZqbImKbmCy3LoUuwusYsfZguhEf2blOKhZ9qeDjzvCBHgUaK', 'liceshi1', 'liceshi1', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, '2025-07-24 05:35:45', NULL, NULL, '2025-07-24 05:35:32', '2025-07-27 00:49:54'),
(95, 'dongcheng1', 'dongcheng1@163.com', '15124889866', '$2y$12$ZNUq9XGOK1U/LhcS0iRgJuo9PV4X/2vCAaPzQIMleCCSEF0q.Jddm', '东城小学教师', '东城小学教师', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 15, NULL, 15, 'school', 5, '2025-08-04 00:27:32', NULL, NULL, '2025-07-24 23:50:41', '2025-08-04 00:27:32'),
(96, 'teacher_1_6', 'teacher_1_6@ZY001.edu.cn', '13112263060', '$2y$12$536tS7yloUNRj79jWEz1vevLcAxS0iqsIulzowpOJuxzcAeX50dUW', '张杰', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 1, NULL, 1, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:51', '2025-07-27 20:28:51'),
(97, 'teacher_1_7', 'teacher_1_7@ZY001.edu.cn', '18029584692', '$2y$12$BlFEaJkI9Fot.XnxCcTBwe6/kqhmdMAvB7fHyIIxjl6MApU1WFiku', '徐超', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 1, NULL, 1, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(98, 'teacher_1_8', 'teacher_1_8@ZY001.edu.cn', '18098245988', '$2y$12$uvsHekSg46kj/f/OsZkDO.Qhu85byqu7w6sa9ye3vKFjdObUxZ3Ca', '罗勇', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 1, NULL, 1, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(99, 'teacher_2_6', 'teacher_2_6@ZY002.edu.cn', '13692994820', '$2y$12$N7b7EBNk.vOQGTY1nURo1OnA3Bb9zKvS754ExSwjZZnUHL6YcMU..', '刘敏', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 2, NULL, 2, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:52', '2025-07-27 20:28:52'),
(100, 'teacher_2_7', 'teacher_2_7@ZY002.edu.cn', '13813433596', '$2y$12$l2qpZU3xuFPG1LEF4EBDFeEXg188gTg0bM9RJZTDIlNp0w2qzIXhK', '刘明', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 2, NULL, 2, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(101, 'teacher_2_8', 'teacher_2_8@ZY002.edu.cn', '18763426896', '$2y$12$kikpNBOgyx8dJ9TneHnXvO89pVyGdJMkfm8SfEjEtPliuFsEWs9xG', '黄敏', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 2, NULL, 2, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(102, 'teacher_2_9', 'teacher_2_9@ZY002.edu.cn', '13429167095', '$2y$12$QKTOYlEqCCrys0PFuQmQ5OdepD.oOw9tFmwpa2.fjkzqKpzD/K.rO', '郭秀英', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 2, NULL, 2, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:53', '2025-07-27 20:28:53'),
(103, 'teacher_3_6', 'teacher_3_6@ZY003.edu.cn', '15721100189', '$2y$12$J7UhzN9xVDysd3lVtn/cReqhdLHhSzURke.2EfNdjRtUXbxfW8Euu', '孙霞', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:54', '2025-07-27 20:28:54'),
(104, 'teacher_3_7', 'teacher_3_7@ZY003.edu.cn', '15344049009', '$2y$12$cbOFSQ2kQxYtjY9s5LIpt.eyxhWwD3k9RVuM8TzoQzkiXlQFOsnyS', '张洋', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:54', '2025-07-27 20:28:54'),
(105, 'teacher_3_8', 'teacher_3_8@ZY003.edu.cn', '18393593645', '$2y$12$5dYuH/T12cA56arcGKOenux0myYNSo1EL69LeN3qLbP62PU5hZEDC', '张明', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:54', '2025-07-27 20:28:54'),
(106, 'teacher_3_9', 'teacher_3_9@ZY003.edu.cn', '15116445967', '$2y$12$mwN9xgsmIi6MUYKixH.3X.9DFU.xMM8wR9mro/3ZPhrWUVtPQ6ytm', '马涛', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(107, 'teacher_3_10', 'teacher_3_10@ZY003.edu.cn', '18951829764', '$2y$12$F1KSzWpXboVcwAsBs4CzRu4ZOErpoFBAQYbsCTBtkuYaXrVWe/qlK', '王伟', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 3, NULL, 3, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(108, 'teacher_4_6', 'teacher_4_6@ZY004.edu.cn', '15643062239', '$2y$12$NXxbqU7dym4TgG0ur3VEteHFMCc6jLfuvEjkXCNcP1XJE6FyHK29q', '黄秀兰', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:55', '2025-07-27 20:28:55'),
(109, 'teacher_4_7', 'teacher_4_7@ZY004.edu.cn', '13342776961', '$2y$12$ezUkR/pI8fhNRd8vniFvKeipfPmHWUYQtmJEDlCstiEyHxhSGFUtG', '林艳', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(110, 'teacher_4_8', 'teacher_4_8@ZY004.edu.cn', '15720223212', '$2y$12$gMb1EmKCh0N1Fs6e3RtO7.KL5trPm3AZU5zL7CMRO8a4LPx.CtevG', '黄军', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(111, 'teacher_4_9', 'teacher_4_9@ZY004.edu.cn', '15658083267', '$2y$12$1zjqXv8ch/dsfgQGZ0QLe.PfaSn3P5v/FA7AWDT/ApQq5gNqJSil2', '郭芳', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(112, 'teacher_4_10', 'teacher_4_10@ZY004.edu.cn', '18964933049', '$2y$12$8jghq.l.FJpRIGkxACPgVu8kyfohL7FDQRpnRpG1Wo5YToOAg4Kgi', '郭娜', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 4, NULL, 4, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:56', '2025-07-27 20:28:56'),
(113, 'teacher_5_6', 'teacher_5_6@EQ001.edu.cn', '18916602333', '$2y$12$1nPaxCLh6HZU8bSPdNJ..uJNDnTkjlbDUfasTYYHOx7R.U5UihpVm', '黄娜', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(114, 'teacher_5_7', 'teacher_5_7@EQ001.edu.cn', '13155517156', '$2y$12$Ag1XQ6xFXCW9e9kuZcZWyuw2/.ZrtPmNYxPsfB0WlLRABT/aEuPTG', '吴军', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:57', '2025-07-27 20:28:57'),
(115, 'teacher_5_8', 'teacher_5_8@EQ001.edu.cn', '18776346972', '$2y$12$FRk.ErlLuu3RGU/tL6v0t.iTRcpQ/VlzsNMrjwuefZLI3VwthvYBy', '刘军', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:58', '2025-07-27 20:28:58'),
(116, 'teacher_5_9', 'teacher_5_9@EQ001.edu.cn', '15630528945', '$2y$12$yKW/DNOjQ.T8R/uhPbAqSOulkJvxYrRIdNzMUzvyF3AoWJAXxaBBS', '孙洋', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:58', '2025-07-27 20:28:58'),
(117, 'teacher_5_10', 'teacher_5_10@EQ001.edu.cn', '18028087222', '$2y$12$zvuotnMRpoAfPND8ohy7ze4Bm0EESc4P9LTkvNqEVLXwOsHkRHLBO', '朱洋', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:58', '2025-07-27 20:28:58'),
(118, 'teacher_5_11', 'teacher_5_11@EQ001.edu.cn', '15640222280', '$2y$12$WcxZsRRhKlJ2yMLaAs2.2uHoLlQZTJoczH6GtHEiuYztZgfJ3fTvG', '郭霞', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:59', '2025-07-27 20:28:59'),
(119, 'teacher_5_12', 'teacher_5_12@EQ001.edu.cn', '18072327538', '$2y$12$H9/YI3IL8N3asmFS95fPSeTXoaDrwYf8vG0VcBQFs1a5BIFGlr/Hq', '陈丽', NULL, NULL, NULL, NULL, NULL, 1, 'teacher', NULL, NULL, NULL, 5, NULL, 5, 'school', 5, NULL, NULL, NULL, '2025-07-27 20:28:59', '2025-07-27 20:28:59'),
(120, 'baodingqizhong01', 'baodingqizhong01@163.com', NULL, '$2y$12$hdve3PMNEJqnwmXL.w5X2egkGSb5HDrkDwqRgG3KsGeBPm8D3JlHu', '保定七中测试', NULL, NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 9, NULL, 9, 'school', 5, '2025-07-29 17:33:34', NULL, NULL, '2025-07-28 04:49:35', '2025-07-29 17:33:34');

-- --------------------------------------------------------

--
-- 表的结构 `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色ID',
  `scope_type` varchar(20) NOT NULL COMMENT '权限范围类型：region/school',
  `scope_id` bigint(20) UNSIGNED NOT NULL COMMENT '权限范围ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `scope_type`, `scope_id`, `created_at`, `updated_at`) VALUES
(98, 6, 3, 'region', 9, '2025-07-27 18:12:15', '2025-07-27 18:12:15'),
(99, 15, 11, 'school', 4, '2025-07-27 18:12:15', '2025-07-27 18:12:15'),
(100, 36, 11, 'school', 4, '2025-07-27 18:12:16', '2025-07-27 18:12:16'),
(101, 53, 11, 'school', 1, '2025-07-27 18:12:16', '2025-07-27 18:12:16'),
(102, 59, 11, 'school', 4, '2025-07-27 18:12:16', '2025-07-27 18:12:16'),
(103, 60, 11, 'school', 4, '2025-07-27 18:12:17', '2025-07-27 18:12:17'),
(104, 88, 11, 'school', 1, '2025-07-27 18:12:17', '2025-07-27 18:12:17'),
(105, 90, 11, 'school', 1, '2025-07-27 18:12:17', '2025-07-27 18:12:17'),
(106, 91, 11, 'school', 3, '2025-07-27 18:12:17', '2025-07-27 18:12:17'),
(107, 94, 11, 'school', 3, '2025-07-27 18:12:17', '2025-07-27 18:12:17'),
(108, 95, 11, 'school', 15, '2025-07-27 18:12:17', '2025-07-27 18:12:17'),
(109, 3, 1, 'region', 1, '2025-07-27 18:17:32', '2025-07-27 18:17:32'),
(110, 42, 1, 'region', 1, '2025-07-27 19:02:39', '2025-07-27 19:02:39'),
(111, 43, 3, 'region', 9, '2025-07-27 19:08:45', '2025-07-27 19:08:45'),
(112, 44, 5, 'region', 10, '2025-07-27 19:08:45', '2025-07-27 19:08:45'),
(113, 45, 7, 'region', 11, '2025-07-27 19:08:45', '2025-07-27 19:08:45'),
(114, 46, 12, 'region', 11, '2025-07-27 19:08:46', '2025-07-27 19:08:46'),
(115, 120, 11, 'school', 9, '2025-07-28 04:49:35', '2025-07-28 04:49:35');

--
-- 转储表的索引
--

--
-- 表的索引 `administrative_regions`
--
ALTER TABLE `administrative_regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `administrative_regions_code_unique` (`code`),
  ADD KEY `administrative_regions_code_index` (`code`),
  ADD KEY `administrative_regions_parent_id_index` (`parent_id`),
  ADD KEY `administrative_regions_level_index` (`level`);

--
-- 表的索引 `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- 表的索引 `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- 表的索引 `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipments_school_id_index` (`school_id`),
  ADD KEY `equipments_laboratory_id_index` (`laboratory_id`),
  ADD KEY `equipments_category_id_index` (`category_id`),
  ADD KEY `equipments_code_index` (`code`),
  ADD KEY `equipments_manager_id_index` (`manager_id`),
  ADD KEY `equipments_status_index` (`status`);

--
-- 表的索引 `equipment_attachments`
--
ALTER TABLE `equipment_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_attachments_equipment_id_attachment_type_index` (`equipment_id`,`attachment_type`),
  ADD KEY `equipment_attachments_file_type_is_primary_index` (`file_type`,`is_primary`),
  ADD KEY `equipment_attachments_uploaded_by_index` (`uploaded_by`),
  ADD KEY `equipment_attachments_created_at_index` (`created_at`);

--
-- 表的索引 `equipment_borrows`
--
ALTER TABLE `equipment_borrows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_borrows_equipment_id_index` (`equipment_id`),
  ADD KEY `equipment_borrows_reservation_id_index` (`reservation_id`),
  ADD KEY `equipment_borrows_borrower_id_index` (`borrower_id`),
  ADD KEY `equipment_borrows_borrow_date_index` (`borrow_date`),
  ADD KEY `equipment_borrows_status_index` (`status`),
  ADD KEY `equipment_borrows_approver_id_index` (`approver_id`),
  ADD KEY `equipment_borrows_has_damage_index` (`has_damage`);

--
-- 表的索引 `equipment_categories`
--
ALTER TABLE `equipment_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_categories_code_unique` (`code`),
  ADD KEY `equipment_categories_parent_id_index` (`parent_id`),
  ADD KEY `equipment_categories_level_index` (`level`),
  ADD KEY `equipment_categories_status_index` (`status`);

--
-- 表的索引 `equipment_maintenances`
--
ALTER TABLE `equipment_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_maintenances_equipment_id_index` (`equipment_id`),
  ADD KEY `equipment_maintenances_reporter_id_index` (`reporter_id`),
  ADD KEY `equipment_maintenances_maintainer_id_index` (`maintainer_id`),
  ADD KEY `equipment_maintenances_report_date_index` (`report_date`),
  ADD KEY `equipment_maintenances_status_index` (`status`);

--
-- 表的索引 `equipment_operation_logs`
--
ALTER TABLE `equipment_operation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_operation_logs_equipment_id_operation_type_index` (`equipment_id`,`operation_type`),
  ADD KEY `equipment_operation_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `equipment_operation_logs_operation_module_index` (`operation_module`),
  ADD KEY `equipment_operation_logs_created_at_index` (`created_at`);

--
-- 表的索引 `equipment_qrcodes`
--
ALTER TABLE `equipment_qrcodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_qrcodes_equipment_id_is_active_index` (`equipment_id`,`is_active`),
  ADD KEY `equipment_qrcodes_qr_type_index` (`qr_type`),
  ADD KEY `equipment_qrcodes_created_at_index` (`created_at`);

--
-- 表的索引 `equipment_standards`
--
ALTER TABLE `equipment_standards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_standards_code_unique` (`code`),
  ADD KEY `equipment_standards_authority_type_stage_subject_code_index` (`authority_type`,`stage`,`subject_code`),
  ADD KEY `equipment_standards_status_effective_date_index` (`status`,`effective_date`),
  ADD KEY `idx_eq_std_category_levels` (`category_level_1`,`category_level_2`),
  ADD KEY `idx_eq_std_basic_optional` (`is_basic_standard`,`is_optional_standard`);

--
-- 表的索引 `experiment_alerts`
--
ALTER TABLE `experiment_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_alert_level` (`alert_level`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_is_resolved` (`is_resolved`),
  ADD KEY `idx_alert_time` (`alert_time`),
  ADD KEY `experiment_alerts_resolved_by_foreign` (`resolved_by`);

--
-- 表的索引 `experiment_alert_config`
--
ALTER TABLE `experiment_alert_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_org_alert_type` (`organization_type`,`organization_id`,`alert_type`),
  ADD KEY `idx_organization` (`organization_type`,`organization_id`),
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `experiment_alert_config_created_by_foreign` (`created_by`);

--
-- 表的索引 `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_catalogs_subject_id_index` (`subject_id`),
  ADD KEY `experiment_catalogs_code_index` (`code`),
  ADD KEY `experiment_catalogs_type_index` (`type`),
  ADD KEY `experiment_catalogs_grade_index` (`grade`),
  ADD KEY `experiment_catalogs_semester_index` (`semester`),
  ADD KEY `experiment_catalogs_is_standard_index` (`is_standard`),
  ADD KEY `experiment_catalogs_status_index` (`status`),
  ADD KEY `experiment_catalogs_management_level_index` (`management_level`),
  ADD KEY `idx_baseline_catalog` (`is_baseline_catalog`,`baseline_priority`),
  ADD KEY `idx_level_baseline` (`management_level`,`is_baseline_catalog`),
  ADD KEY `idx_usage_count` (`usage_count`),
  ADD KEY `idx_last_used` (`last_used_at`);

--
-- 表的索引 `experiment_catalog_completion_baselines`
--
ALTER TABLE `experiment_catalog_completion_baselines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_baseline` (`school_id`,`config_id`,`subject_id`,`grade`,`semester`),
  ADD KEY `idx_school_config` (`school_id`,`config_id`),
  ADD KEY `idx_subject_grade_semester` (`subject_id`,`grade`,`semester`),
  ADD KEY `idx_completion_rate` (`completion_rate`),
  ADD KEY `idx_last_calculated` (`last_calculated_at`),
  ADD KEY `idx_school_subject` (`school_id`,`subject_id`),
  ADD KEY `idx_school_grade` (`school_id`,`grade`),
  ADD KEY `experiment_catalog_completion_baselines_config_id_foreign` (`config_id`),
  ADD KEY `experiment_catalog_completion_baselines_calculated_by_foreign` (`calculated_by`);

--
-- 表的索引 `experiment_catalog_delete_permissions`
--
ALTER TABLE `experiment_catalog_delete_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_org_permission` (`organization_type`,`organization_id`),
  ADD KEY `idx_organization` (`organization_type`,`organization_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `experiment_catalog_delete_permissions_created_by_foreign` (`created_by`);

--
-- 表的索引 `experiment_catalog_deletions`
--
ALTER TABLE `experiment_catalog_deletions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_catalog_org` (`catalog_id`,`deleted_by_org_type`,`deleted_by_org_id`),
  ADD KEY `experiment_catalog_deletions_deleted_by_user_id_foreign` (`deleted_by_user_id`),
  ADD KEY `experiment_catalog_deletions_restored_by_foreign` (`restored_by`);

--
-- 表的索引 `experiment_catalog_permissions`
--
ALTER TABLE `experiment_catalog_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_catalog_org` (`catalog_id`,`organization_type`,`organization_id`),
  ADD KEY `idx_user_permission` (`user_id`,`permission_type`),
  ADD KEY `experiment_catalog_permissions_subject_id_foreign` (`subject_id`),
  ADD KEY `experiment_catalog_permissions_granted_by_foreign` (`granted_by`);

--
-- 表的索引 `experiment_catalog_versions`
--
ALTER TABLE `experiment_catalog_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_catalog_version` (`catalog_id`,`version`),
  ADD KEY `experiment_catalog_versions_changed_by_foreign` (`changed_by`);

--
-- 表的索引 `experiment_equipment_requirements`
--
ALTER TABLE `experiment_equipment_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_equipment_requirements_catalog_id_foreign` (`catalog_id`);

--
-- 表的索引 `experiment_monitoring_statistics`
--
ALTER TABLE `experiment_monitoring_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_target_semester_date` (`target_type`,`target_id`,`semester`,`statistics_date`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_semester` (`semester`),
  ADD KEY `idx_statistics_date` (`statistics_date`),
  ADD KEY `idx_completion_rate` (`completion_rate`),
  ADD KEY `idx_overdue_rate` (`overdue_rate`);

--
-- 表的索引 `experiment_records`
--
ALTER TABLE `experiment_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_records_reservation_id_index` (`reservation_id`),
  ADD KEY `experiment_records_school_id_index` (`school_id`),
  ADD KEY `experiment_records_catalog_id_index` (`catalog_id`),
  ADD KEY `experiment_records_laboratory_id_index` (`laboratory_id`),
  ADD KEY `experiment_records_teacher_id_index` (`teacher_id`),
  ADD KEY `experiment_records_start_time_index` (`start_time`),
  ADD KEY `experiment_records_status_index` (`status`),
  ADD KEY `experiment_records_work_count_index` (`work_count`);

--
-- 表的索引 `experiment_requirements_config`
--
ALTER TABLE `experiment_requirements_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_org_exp_type` (`organization_type`,`organization_id`,`experiment_type`),
  ADD KEY `idx_organization` (`organization_type`,`organization_id`),
  ADD KEY `idx_experiment_type` (`experiment_type`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `experiment_requirements_config_created_by_foreign` (`created_by`);

--
-- 表的索引 `experiment_reservations`
--
ALTER TABLE `experiment_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_reservations_school_id_index` (`school_id`),
  ADD KEY `experiment_reservations_catalog_id_index` (`catalog_id`),
  ADD KEY `experiment_reservations_laboratory_id_index` (`laboratory_id`),
  ADD KEY `experiment_reservations_teacher_id_index` (`teacher_id`),
  ADD KEY `experiment_reservations_reservation_date_index` (`reservation_date`),
  ADD KEY `experiment_reservations_status_index` (`status`),
  ADD KEY `experiment_reservations_reviewer_id_index` (`reviewer_id`),
  ADD KEY `experiment_reservations_batch_id_index` (`batch_id`),
  ADD KEY `experiment_reservations_priority_index` (`priority`);

--
-- 表的索引 `experiment_reservation_templates`
--
ALTER TABLE `experiment_reservation_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_reservation_templates_school_id_subject_id_index` (`school_id`,`subject_id`),
  ADD KEY `experiment_reservation_templates_grade_semester_index` (`grade`,`semester`),
  ADD KEY `experiment_reservation_templates_created_by_index` (`created_by`),
  ADD KEY `experiment_reservation_templates_is_active_index` (`is_active`),
  ADD KEY `experiment_reservation_templates_subject_id_foreign` (`subject_id`);

--
-- 表的索引 `experiment_works`
--
ALTER TABLE `experiment_works`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_works_record_id_index` (`record_id`),
  ADD KEY `experiment_works_student_id_index` (`student_id`),
  ADD KEY `experiment_works_type_index` (`type`),
  ADD KEY `experiment_works_is_featured_index` (`is_featured`),
  ADD KEY `experiment_works_is_public_index` (`is_public`),
  ADD KEY `experiment_works_uploaded_by_index` (`uploaded_by`);

--
-- 表的索引 `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- 表的索引 `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- 表的索引 `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `laboratories`
--
ALTER TABLE `laboratories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laboratories_school_id_index` (`school_id`),
  ADD KEY `laboratories_code_index` (`code`),
  ADD KEY `laboratories_type_index` (`type`),
  ADD KEY `laboratories_manager_id_index` (`manager_id`),
  ADD KEY `laboratories_status_index` (`status`),
  ADD KEY `laboratories_type_id_index` (`type_id`);

--
-- 表的索引 `laboratory_types`
--
ALTER TABLE `laboratory_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `laboratory_types_code_unique` (`code`),
  ADD KEY `laboratory_types_status_sort_order_index` (`status`,`sort_order`);

--
-- 表的索引 `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `operation_logs`
--
ALTER TABLE `operation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `operation_logs_user_id_index` (`user_id`),
  ADD KEY `operation_logs_module_index` (`module`),
  ADD KEY `operation_logs_action_index` (`action`),
  ADD KEY `operation_logs_created_at_index` (`created_at`);

--
-- 表的索引 `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- 表的索引 `reservation_batches`
--
ALTER TABLE `reservation_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_batches_school_id_subject_id_index` (`school_id`,`subject_id`),
  ADD KEY `reservation_batches_grade_semester_index` (`grade`,`semester`),
  ADD KEY `reservation_batches_created_by_index` (`created_by`),
  ADD KEY `reservation_batches_status_index` (`status`),
  ADD KEY `reservation_batches_reviewer_id_index` (`reviewer_id`),
  ADD KEY `reservation_batches_subject_id_foreign` (`subject_id`);

--
-- 表的索引 `reservation_conflict_logs`
--
ALTER TABLE `reservation_conflict_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_conflict_logs_reservation_id_index` (`reservation_id`),
  ADD KEY `reservation_conflict_logs_conflict_type_index` (`conflict_type`),
  ADD KEY `reservation_conflict_logs_is_resolved_index` (`is_resolved`),
  ADD KEY `reservation_conflict_logs_severity_index` (`severity`),
  ADD KEY `reservation_conflict_logs_resolved_by_foreign` (`resolved_by`);

--
-- 表的索引 `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_code_unique` (`code`),
  ADD KEY `roles_level_index` (`level`);

--
-- 表的索引 `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_id_permission_code_unique` (`role_id`,`permission_code`),
  ADD KEY `role_permissions_role_id_index` (`role_id`),
  ADD KEY `role_permissions_permission_code_index` (`permission_code`);

--
-- 表的索引 `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schools_code_unique` (`code`),
  ADD KEY `schools_region_id_index` (`region_id`),
  ADD KEY `schools_type_index` (`type`),
  ADD KEY `schools_level_index` (`level`),
  ADD KEY `schools_organization_type_organization_id_index` (`organization_type`,`organization_id`);

--
-- 表的索引 `school_classes`
--
ALTER TABLE `school_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_classes_school_id_code_unique` (`school_id`,`code`),
  ADD KEY `school_classes_head_teacher_id_foreign` (`head_teacher_id`),
  ADD KEY `school_classes_school_id_grade_index` (`school_id`,`grade`);

--
-- 表的索引 `school_experiment_catalog_configs`
--
ALTER TABLE `school_experiment_catalog_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_active_school_config` (`school_id`,`status`),
  ADD KEY `idx_school_id` (`school_id`),
  ADD KEY `idx_source_org` (`source_level`,`source_org_id`),
  ADD KEY `idx_configured_by` (`configured_by`,`configured_by_level`),
  ADD KEY `idx_status_effective` (`status`,`effective_date`),
  ADD KEY `idx_config_type` (`config_type`);

--
-- 表的索引 `school_experiment_catalog_selections`
--
ALTER TABLE `school_experiment_catalog_selections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_school_selection` (`school_id`),
  ADD KEY `idx_school_id` (`school_id`),
  ADD KEY `idx_selected_org` (`selected_level`,`selected_org_id`),
  ADD KEY `school_experiment_catalog_selections_selected_by_foreign` (`selected_by`);

--
-- 表的索引 `school_teachers`
--
ALTER TABLE `school_teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_teachers_school_id_user_id_unique` (`school_id`,`user_id`),
  ADD KEY `school_teachers_user_id_foreign` (`user_id`),
  ADD KEY `school_teachers_school_id_subject_index` (`school_id`,`subject`);

--
-- 表的索引 `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- 表的索引 `statistics_summary`
--
ALTER TABLE `statistics_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_scope_subject_date_type` (`scope_type`,`scope_id`,`subject_id`,`stat_date`,`stat_type`),
  ADD KEY `statistics_summary_stat_date_index` (`stat_date`),
  ADD KEY `statistics_summary_stat_type_index` (`stat_type`),
  ADD KEY `statistics_summary_subject_id_index` (`subject_id`);

--
-- 表的索引 `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`),
  ADD KEY `subjects_type_index` (`type`),
  ADD KEY `subjects_stage_index` (`stage`),
  ADD KEY `subjects_status_index` (`status`);

--
-- 表的索引 `system_configs`
--
ALTER TABLE `system_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_configs_key_name_unique` (`key_name`),
  ADD KEY `system_configs_group_name_index` (`group_name`);

--
-- 表的索引 `teaching_equipment_standards`
--
ALTER TABLE `teaching_equipment_standards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_authority_stage_subject` (`authority_type`,`stage`,`subject_code`),
  ADD KEY `idx_category_levels` (`category_level_1`,`category_level_2`),
  ADD KEY `idx_status_effective` (`status`,`effective_date`),
  ADD KEY `idx_item_standard_code` (`item_code`,`standard_code`);

--
-- 表的索引 `textbook_chapters`
--
ALTER TABLE `textbook_chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_version_grade` (`subject_id`,`textbook_version_id`,`grade_level`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_level_sort` (`level`,`sort_order`),
  ADD KEY `textbook_chapters_textbook_version_id_foreign` (`textbook_version_id`);

--
-- 表的索引 `textbook_versions`
--
ALTER TABLE `textbook_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `textbook_versions_code_unique` (`code`),
  ADD KEY `textbook_versions_status_sort_order_index` (`status`,`sort_order`);

--
-- 表的索引 `textbook_version_assignments`
--
ALTER TABLE `textbook_version_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_school_subject_grade_active` (`school_id`,`subject_id`,`grade_level`,`status`),
  ADD KEY `idx_school_subject_grade_status` (`school_id`,`subject_id`,`grade_level`,`status`),
  ADD KEY `idx_assigner` (`assigner_level`,`assigner_org_id`),
  ADD KEY `idx_textbook_version` (`textbook_version_id`),
  ADD KEY `idx_date_range` (`effective_date`,`expiry_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `textbook_version_assignments_subject_id_foreign` (`subject_id`),
  ADD KEY `textbook_version_assignments_assigner_user_id_foreign` (`assigner_user_id`),
  ADD KEY `textbook_version_assignments_replaced_assignment_id_foreign` (`replaced_assignment_id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_phone_index` (`phone`),
  ADD KEY `users_status_index` (`status`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_school_id_index` (`school_id`),
  ADD KEY `users_organization_id_index` (`organization_id`),
  ADD KEY `users_organization_type_index` (`organization_type`),
  ADD KEY `users_organization_level_index` (`organization_level`),
  ADD KEY `idx_organization` (`organization_type`,`organization_id`);

--
-- 表的索引 `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_role_scope` (`user_id`,`role_id`,`scope_type`,`scope_id`),
  ADD KEY `user_roles_user_id_index` (`user_id`),
  ADD KEY `user_roles_role_id_index` (`role_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `administrative_regions`
--
ALTER TABLE `administrative_regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用表AUTO_INCREMENT `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- 使用表AUTO_INCREMENT `equipment_attachments`
--
ALTER TABLE `equipment_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `equipment_borrows`
--
ALTER TABLE `equipment_borrows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `equipment_categories`
--
ALTER TABLE `equipment_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- 使用表AUTO_INCREMENT `equipment_maintenances`
--
ALTER TABLE `equipment_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `equipment_operation_logs`
--
ALTER TABLE `equipment_operation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `equipment_qrcodes`
--
ALTER TABLE `equipment_qrcodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `equipment_standards`
--
ALTER TABLE `equipment_standards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `experiment_alerts`
--
ALTER TABLE `experiment_alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- 使用表AUTO_INCREMENT `experiment_alert_config`
--
ALTER TABLE `experiment_alert_config`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- 使用表AUTO_INCREMENT `experiment_catalog_completion_baselines`
--
ALTER TABLE `experiment_catalog_completion_baselines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_catalog_delete_permissions`
--
ALTER TABLE `experiment_catalog_delete_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `experiment_catalog_deletions`
--
ALTER TABLE `experiment_catalog_deletions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_catalog_permissions`
--
ALTER TABLE `experiment_catalog_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_catalog_versions`
--
ALTER TABLE `experiment_catalog_versions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_equipment_requirements`
--
ALTER TABLE `experiment_equipment_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用表AUTO_INCREMENT `experiment_monitoring_statistics`
--
ALTER TABLE `experiment_monitoring_statistics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- 使用表AUTO_INCREMENT `experiment_records`
--
ALTER TABLE `experiment_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- 使用表AUTO_INCREMENT `experiment_requirements_config`
--
ALTER TABLE `experiment_requirements_config`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `experiment_reservations`
--
ALTER TABLE `experiment_reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;

--
-- 使用表AUTO_INCREMENT `experiment_reservation_templates`
--
ALTER TABLE `experiment_reservation_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_works`
--
ALTER TABLE `experiment_works`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `laboratories`
--
ALTER TABLE `laboratories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- 使用表AUTO_INCREMENT `laboratory_types`
--
ALTER TABLE `laboratory_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- 使用表AUTO_INCREMENT `operation_logs`
--
ALTER TABLE `operation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `reservation_batches`
--
ALTER TABLE `reservation_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `reservation_conflict_logs`
--
ALTER TABLE `reservation_conflict_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用表AUTO_INCREMENT `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=646;

--
-- 使用表AUTO_INCREMENT `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用表AUTO_INCREMENT `school_classes`
--
ALTER TABLE `school_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- 使用表AUTO_INCREMENT `school_experiment_catalog_configs`
--
ALTER TABLE `school_experiment_catalog_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `school_experiment_catalog_selections`
--
ALTER TABLE `school_experiment_catalog_selections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `school_teachers`
--
ALTER TABLE `school_teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- 使用表AUTO_INCREMENT `statistics_summary`
--
ALTER TABLE `statistics_summary`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `system_configs`
--
ALTER TABLE `system_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `teaching_equipment_standards`
--
ALTER TABLE `teaching_equipment_standards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- 使用表AUTO_INCREMENT `textbook_chapters`
--
ALTER TABLE `textbook_chapters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `textbook_versions`
--
ALTER TABLE `textbook_versions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `textbook_version_assignments`
--
ALTER TABLE `textbook_version_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- 使用表AUTO_INCREMENT `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- 限制导出的表
--

--
-- 限制表 `equipments`
--
ALTER TABLE `equipments`
  ADD CONSTRAINT `equipments_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `equipment_categories` (`id`),
  ADD CONSTRAINT `equipments_laboratory_id_foreign` FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories` (`id`),
  ADD CONSTRAINT `equipments_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `equipments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`);

--
-- 限制表 `equipment_attachments`
--
ALTER TABLE `equipment_attachments`
  ADD CONSTRAINT `equipment_attachments_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipment_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- 限制表 `equipment_borrows`
--
ALTER TABLE `equipment_borrows`
  ADD CONSTRAINT `equipment_borrows_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `equipment_borrows_borrower_id_foreign` FOREIGN KEY (`borrower_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `equipment_borrows_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`),
  ADD CONSTRAINT `equipment_borrows_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `experiment_reservations` (`id`);

--
-- 限制表 `equipment_maintenances`
--
ALTER TABLE `equipment_maintenances`
  ADD CONSTRAINT `equipment_maintenances_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`),
  ADD CONSTRAINT `equipment_maintenances_maintainer_id_foreign` FOREIGN KEY (`maintainer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `equipment_maintenances_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`);

--
-- 限制表 `equipment_operation_logs`
--
ALTER TABLE `equipment_operation_logs`
  ADD CONSTRAINT `equipment_operation_logs_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipment_operation_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 限制表 `equipment_qrcodes`
--
ALTER TABLE `equipment_qrcodes`
  ADD CONSTRAINT `equipment_qrcodes_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE;

--
-- 限制表 `experiment_alerts`
--
ALTER TABLE `experiment_alerts`
  ADD CONSTRAINT `experiment_alerts_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_alert_config`
--
ALTER TABLE `experiment_alert_config`
  ADD CONSTRAINT `experiment_alert_config_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  ADD CONSTRAINT `experiment_catalogs_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- 限制表 `experiment_catalog_completion_baselines`
--
ALTER TABLE `experiment_catalog_completion_baselines`
  ADD CONSTRAINT `experiment_catalog_completion_baselines_calculated_by_foreign` FOREIGN KEY (`calculated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `experiment_catalog_completion_baselines_config_id_foreign` FOREIGN KEY (`config_id`) REFERENCES `school_experiment_catalog_configs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_catalog_completion_baselines_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_catalog_completion_baselines_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- 限制表 `experiment_catalog_delete_permissions`
--
ALTER TABLE `experiment_catalog_delete_permissions`
  ADD CONSTRAINT `experiment_catalog_delete_permissions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_catalog_deletions`
--
ALTER TABLE `experiment_catalog_deletions`
  ADD CONSTRAINT `experiment_catalog_deletions_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_catalog_deletions_deleted_by_user_id_foreign` FOREIGN KEY (`deleted_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `experiment_catalog_deletions_restored_by_foreign` FOREIGN KEY (`restored_by`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_catalog_permissions`
--
ALTER TABLE `experiment_catalog_permissions`
  ADD CONSTRAINT `experiment_catalog_permissions_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_catalog_permissions_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `experiment_catalog_permissions_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `experiment_catalog_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_catalog_versions`
--
ALTER TABLE `experiment_catalog_versions`
  ADD CONSTRAINT `experiment_catalog_versions_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_catalog_versions_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_equipment_requirements`
--
ALTER TABLE `experiment_equipment_requirements`
  ADD CONSTRAINT `experiment_equipment_requirements_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`) ON DELETE CASCADE;

--
-- 限制表 `experiment_records`
--
ALTER TABLE `experiment_records`
  ADD CONSTRAINT `experiment_records_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`),
  ADD CONSTRAINT `experiment_records_laboratory_id_foreign` FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories` (`id`),
  ADD CONSTRAINT `experiment_records_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `experiment_reservations` (`id`),
  ADD CONSTRAINT `experiment_records_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`),
  ADD CONSTRAINT `experiment_records_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_requirements_config`
--
ALTER TABLE `experiment_requirements_config`
  ADD CONSTRAINT `experiment_requirements_config_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_reservations`
--
ALTER TABLE `experiment_reservations`
  ADD CONSTRAINT `experiment_reservations_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `reservation_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `experiment_reservations_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`),
  ADD CONSTRAINT `experiment_reservations_laboratory_id_foreign` FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories` (`id`),
  ADD CONSTRAINT `experiment_reservations_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `experiment_reservations_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`),
  ADD CONSTRAINT `experiment_reservations_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- 限制表 `experiment_reservation_templates`
--
ALTER TABLE `experiment_reservation_templates`
  ADD CONSTRAINT `experiment_reservation_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_reservation_templates_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_reservation_templates_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- 限制表 `experiment_works`
--
ALTER TABLE `experiment_works`
  ADD CONSTRAINT `experiment_works_record_id_foreign` FOREIGN KEY (`record_id`) REFERENCES `experiment_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `experiment_works_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `experiment_works_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 限制表 `laboratories`
--
ALTER TABLE `laboratories`
  ADD CONSTRAINT `laboratories_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `laboratories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`),
  ADD CONSTRAINT `laboratories_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `laboratory_types` (`id`) ON DELETE SET NULL;

--
-- 限制表 `operation_logs`
--
ALTER TABLE `operation_logs`
  ADD CONSTRAINT `operation_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 限制表 `reservation_batches`
--
ALTER TABLE `reservation_batches`
  ADD CONSTRAINT `reservation_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_batches_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservation_batches_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_batches_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- 限制表 `reservation_conflict_logs`
--
ALTER TABLE `reservation_conflict_logs`
  ADD CONSTRAINT `reservation_conflict_logs_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `experiment_reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_conflict_logs_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 限制表 `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- 限制表 `schools`
--
ALTER TABLE `schools`
  ADD CONSTRAINT `schools_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `administrative_regions` (`id`);

--
-- 限制表 `school_classes`
--
ALTER TABLE `school_classes`
  ADD CONSTRAINT `school_classes_head_teacher_id_foreign` FOREIGN KEY (`head_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `school_classes_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- 限制表 `school_experiment_catalog_configs`
--
ALTER TABLE `school_experiment_catalog_configs`
  ADD CONSTRAINT `school_experiment_catalog_configs_configured_by_foreign` FOREIGN KEY (`configured_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `school_experiment_catalog_configs_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- 限制表 `school_experiment_catalog_selections`
--
ALTER TABLE `school_experiment_catalog_selections`
  ADD CONSTRAINT `school_experiment_catalog_selections_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `school_experiment_catalog_selections_selected_by_foreign` FOREIGN KEY (`selected_by`) REFERENCES `users` (`id`);

--
-- 限制表 `school_teachers`
--
ALTER TABLE `school_teachers`
  ADD CONSTRAINT `school_teachers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `school_teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 限制表 `statistics_summary`
--
ALTER TABLE `statistics_summary`
  ADD CONSTRAINT `statistics_summary_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- 限制表 `textbook_chapters`
--
ALTER TABLE `textbook_chapters`
  ADD CONSTRAINT `textbook_chapters_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `textbook_chapters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `textbook_chapters_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `textbook_chapters_textbook_version_id_foreign` FOREIGN KEY (`textbook_version_id`) REFERENCES `textbook_versions` (`id`) ON DELETE CASCADE;

--
-- 限制表 `textbook_version_assignments`
--
ALTER TABLE `textbook_version_assignments`
  ADD CONSTRAINT `textbook_version_assignments_assigner_user_id_foreign` FOREIGN KEY (`assigner_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `textbook_version_assignments_replaced_assignment_id_foreign` FOREIGN KEY (`replaced_assignment_id`) REFERENCES `textbook_version_assignments` (`id`),
  ADD CONSTRAINT `textbook_version_assignments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`),
  ADD CONSTRAINT `textbook_version_assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `textbook_version_assignments_textbook_version_id_foreign` FOREIGN KEY (`textbook_version_id`) REFERENCES `textbook_versions` (`id`);

--
-- 限制表 `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

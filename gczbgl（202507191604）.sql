-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-07-19 10:04:12
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
('laravel-cache-user_permissions_3', 'a:0:{}', 1752885099),
('laravel-cache-user_permissions_42', 'a:45:{i:0;s:9:\"equipment\";i:1;s:18:\"equipment_standard\";i:2;s:25:\"equipment_standard.create\";i:3;s:25:\"equipment_standard.delete\";i:4;s:23:\"equipment_standard.list\";i:5;s:25:\"equipment_standard.update\";i:6;s:16:\"equipment.borrow\";i:7;s:16:\"equipment.create\";i:8;s:16:\"equipment.delete\";i:9;s:14:\"equipment.list\";i:10;s:21:\"equipment.maintenance\";i:11;s:16:\"equipment.update\";i:12;s:10:\"experiment\";i:13;s:18:\"experiment.booking\";i:14;s:18:\"experiment.catalog\";i:15;s:17:\"experiment.record\";i:16;s:15:\"laboratory_type\";i:17;s:22:\"laboratory_type.create\";i:18;s:22:\"laboratory_type.delete\";i:19;s:20:\"laboratory_type.list\";i:20;s:22:\"laboratory_type.update\";i:21;s:3:\"log\";i:22;s:8:\"log.read\";i:23;s:4:\"role\";i:24;s:11:\"role.create\";i:25;s:11:\"role.delete\";i:26;s:9:\"role.list\";i:27;s:11:\"role.update\";i:28;s:20:\"statistics.dashboard\";i:29;s:20:\"statistics.equipment\";i:30;s:21:\"statistics.experiment\";i:31;s:17:\"statistics.export\";i:32;s:22:\"statistics.performance\";i:33;s:15:\"statistics.user\";i:34;s:15:\"statistics.view\";i:35;s:6:\"system\";i:36;s:11:\"system.read\";i:37;s:4:\"user\";i:38;s:11:\"user.create\";i:39;s:11:\"user.delete\";i:40;s:9:\"user.edit\";i:41;s:11:\"user.export\";i:42;s:9:\"user.list\";i:43;s:19:\"user.reset_password\";i:44;s:11:\"user.update\";}', 1752892761),
('laravel-cache-user_permissions_50', 'a:0:{}', 1752885099),
('laravel-cache-user_permissions_51', 'a:0:{}', 1752885099);

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
(24, 20, NULL, 47, '解剖刀套装', 'SCA0030001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2024-11-29', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 29, 1, '/storage/qrcodes/equipment_24_1752880902.png', '系统初始化数据', '2025-07-18 15:21:42', '2025-07-18 15:21:42');

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
  `borrow_date` date NOT NULL COMMENT '借用日期',
  `expected_return_date` date NOT NULL COMMENT '预期归还日期',
  `actual_return_date` date DEFAULT NULL COMMENT '实际归还日期',
  `purpose` text DEFAULT NULL COMMENT '借用目的',
  `remark` text DEFAULT NULL COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1借用中 2已归还 3逾期 4损坏',
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '审批人ID',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT '审批时间',
  `approval_remark` text DEFAULT NULL COMMENT '审批备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `equipment_borrows`
--

INSERT INTO `equipment_borrows` (`id`, `equipment_id`, `reservation_id`, `borrower_id`, `quantity`, `borrow_date`, `expected_return_date`, `actual_return_date`, `purpose`, `remark`, `status`, `approver_id`, `approved_at`, `approval_remark`, `created_at`, `updated_at`) VALUES
(1, 15, NULL, 21, 3, '2025-07-17', '2025-07-30', NULL, '实验技能竞赛准备', '使用完毕请断电并整理', 5, NULL, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(2, 10, NULL, 7, 2, '2025-07-15', '2025-08-02', NULL, '实验技能竞赛准备', '实验过程中如有问题请联系管理员', 5, NULL, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(3, 16, NULL, 36, 3, '2025-07-12', '2025-07-27', NULL, '学生课外实验活动', '请妥善保管，按时归还', 5, NULL, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(4, 14, NULL, 19, 2, '2025-07-14', '2025-08-08', NULL, '教学研究实验', '', 5, NULL, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(5, 7, NULL, 20, 2, '2025-07-15', '2025-07-26', NULL, '科普展示活动', '如有损坏请及时报告', 5, NULL, NULL, NULL, '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(6, 21, NULL, 11, 2, '2025-07-08', '2025-08-07', NULL, '设备功能测试', '实验过程中如有问题请联系管理员', 1, 25, '2025-07-09 02:21:52', '审批通过', '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(7, 12, NULL, 12, 1, '2025-07-06', '2025-07-23', NULL, '实验方法研究', '感谢配合实验室管理工作', 1, 4, '2025-07-06 16:21:52', '审批通过', '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(8, 19, NULL, 38, 2, '2025-07-06', '2025-07-26', NULL, '学生课外实验活动', '借用期间请勿转借他人', 1, 10, '2025-07-07 02:21:52', '审批通过', '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(9, 9, NULL, 11, 1, '2025-07-11', '2025-07-31', NULL, '实验技能竞赛准备', '注意安全操作', 1, 37, '2025-07-12 08:21:52', '审批通过', '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(10, 6, NULL, 14, 1, '2025-07-07', '2025-08-02', NULL, '科普展示活动', '归还时请检查设备完整性', 1, 19, '2025-07-07 16:21:52', '审批通过', '2025-07-18 15:21:52', '2025-07-18 15:21:52'),
(11, 19, NULL, 39, 2, '2025-07-09', '2025-08-04', NULL, '教师培训使用', '注意安全操作', 1, 34, '2025-07-10 06:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(12, 5, NULL, 32, 2, '2025-07-09', '2025-07-23', NULL, '学生课外实验活动', '', 1, 14, '2025-07-10 04:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(13, 19, NULL, 32, 1, '2025-07-11', '2025-08-01', NULL, '实验方法研究', '借用期间请勿转借他人', 1, 31, '2025-07-12 00:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(14, 21, NULL, 6, 1, '2025-06-29', '2025-07-23', '2025-07-22', '实验室开放日活动', '实验结束后及时清洁设备', 2, 40, '2025-06-30 02:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(15, 10, NULL, 15, 1, '2025-07-01', '2025-07-22', '2025-07-20', '科学兴趣小组活动', '归还时请检查设备完整性', 2, 25, '2025-07-02 07:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(16, 4, NULL, 33, 3, '2025-06-29', '2025-07-20', '2025-07-19', '科学兴趣小组活动', '', 2, 13, '2025-06-30 07:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(17, 22, NULL, 39, 3, '2025-06-29', '2025-07-29', '2025-07-27', '学生毕业设计实验', '实验过程中如有问题请联系管理员', 2, 10, '2025-06-30 14:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(18, 14, NULL, 20, 1, '2025-06-28', '2025-07-26', '2025-07-26', '教学研究实验', '感谢配合实验室管理工作', 2, 25, '2025-06-29 12:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(19, 1, NULL, 22, 3, '2025-07-01', '2025-07-08', '2025-07-05', '科学兴趣小组活动', '请妥善保管，按时归还', 2, 28, '2025-07-02 03:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(20, 19, NULL, 31, 3, '2025-06-28', '2025-07-25', '2025-07-25', '实验技能竞赛准备', '使用完毕请断电并整理', 2, 7, '2025-06-28 20:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(21, 23, NULL, 33, 3, '2025-07-02', '2025-07-27', '2025-07-25', '实验室开放日活动', '', 2, 34, '2025-07-02 22:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(22, 8, NULL, 37, 3, '2025-06-30', '2025-07-28', '2025-07-25', '学生毕业设计实验', '', 2, 41, '2025-07-01 03:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(23, 24, NULL, 20, 2, '2025-06-29', '2025-07-27', '2025-07-25', '设备功能测试', '', 2, 3, '2025-06-30 13:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(24, 4, NULL, 3, 1, '2025-06-28', '2025-07-26', '2025-07-26', '生物实验课教学使用', '如有损坏请及时报告', 2, 23, '2025-06-29 10:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(25, 24, NULL, 26, 3, '2025-06-30', '2025-07-17', '2025-07-16', '学生课外实验活动', '', 2, 33, '2025-07-01 09:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(26, 18, NULL, 18, 3, '2025-06-25', '2025-07-12', NULL, '化学实验课教学使用', '归还时请检查设备完整性', 3, 8, '2025-06-25 20:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(27, 3, NULL, 14, 2, '2025-06-26', '2025-07-15', NULL, '实验方法研究', '如有损坏请及时报告', 3, 28, '2025-06-26 20:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(28, 7, NULL, 22, 1, '2025-06-25', '2025-07-12', NULL, '科普展示活动', '', 3, 20, '2025-06-26 13:21:53', '审批通过', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(29, 2, NULL, 23, 2, '2025-07-10', '2025-07-23', NULL, '设备功能测试', '申请借用', 6, 24, '2025-07-11 14:21:53', '设备维修中，暂不可借用', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(30, 13, NULL, 3, 2, '2025-07-13', '2025-07-25', NULL, '设备功能测试', '申请借用', 6, 21, '2025-07-13 19:21:53', '设备维修中，暂不可借用', '2025-07-18 15:21:53', '2025-07-18 15:21:53'),
(31, 16, NULL, 40, 1, '2025-07-11', '2025-07-24', NULL, '教师培训使用', '申请借用', 6, 12, '2025-07-12 19:21:53', '设备维修中，暂不可借用', '2025-07-18 15:21:53', '2025-07-18 15:21:53');

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
(53, '接种环', 'INOCULATION_LOOP', 50, 3, 3, 1, '2025-07-18 15:17:58', '2025-07-18 15:17:58');

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
  `description` text DEFAULT NULL COMMENT '标准描述',
  `equipment_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '设备清单JSON' CHECK (json_valid(`equipment_list`)),
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

INSERT INTO `equipment_standards` (`id`, `name`, `code`, `authority_type`, `stage`, `subject_code`, `subject_name`, `description`, `equipment_list`, `version`, `effective_date`, `expiry_date`, `status`, `created_at`, `updated_at`) VALUES
(1, '小学科学教学仪器配备标准（教育部）', 'MOE_PRIMARY_SCIENCE_2023', 1, 1, 'SCIENCE', '科学', '根据教育部最新标准制定的小学科学教学仪器配备要求', '[{\"category\":\"\\u6d4b\\u91cf\\u5de5\\u5177\",\"items\":[{\"name\":\"\\u76f4\\u5c3a\",\"specification\":\"30cm\",\"quantity\":50,\"unit\":\"\\u628a\"},{\"name\":\"\\u5377\\u5c3a\",\"specification\":\"5m\",\"quantity\":5,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u5929\\u5e73\",\"specification\":\"\\u6258\\u76d8\\u5929\\u5e73500g\",\"quantity\":10,\"unit\":\"\\u53f0\"},{\"name\":\"\\u91cf\\u7b52\",\"specification\":\"100ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"}]},{\"category\":\"\\u89c2\\u5bdf\\u5de5\\u5177\",\"items\":[{\"name\":\"\\u653e\\u5927\\u955c\",\"specification\":\"5\\u500d\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u663e\\u5fae\\u955c\",\"specification\":\"\\u5b66\\u751f\\u7528\",\"quantity\":15,\"unit\":\"\\u53f0\"},{\"name\":\"\\u671b\\u8fdc\\u955c\",\"specification\":\"\\u53cc\\u7b52\",\"quantity\":5,\"unit\":\"\\u4e2a\"}]}]', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42'),
(2, '初中物理教学仪器配备标准（教育部）', 'MOE_JUNIOR_PHYSICS_2023', 1, 2, 'PHYSICS', '物理', '根据教育部最新标准制定的初中物理教学仪器配备要求', '[{\"category\":\"\\u529b\\u5b66\\u5b9e\\u9a8c\\u5668\\u6750\",\"items\":[{\"name\":\"\\u5f39\\u7c27\\u6d4b\\u529b\\u8ba1\",\"specification\":\"5N\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u6ed1\\u8f6e\\u7ec4\",\"specification\":\"\\u6f14\\u793a\\u7528\",\"quantity\":5,\"unit\":\"\\u5957\"},{\"name\":\"\\u6760\\u6746\",\"specification\":\"\\u6f14\\u793a\\u7528\",\"quantity\":5,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u659c\\u9762\",\"specification\":\"\\u53ef\\u8c03\\u89d2\\u5ea6\",\"quantity\":10,\"unit\":\"\\u4e2a\"}]},{\"category\":\"\\u7535\\u5b66\\u5b9e\\u9a8c\\u5668\\u6750\",\"items\":[{\"name\":\"\\u7535\\u6d41\\u8868\",\"specification\":\"0-0.6A\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u7535\\u538b\\u8868\",\"specification\":\"0-3V\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u6ed1\\u52a8\\u53d8\\u963b\\u5668\",\"specification\":\"20\\u03a9 2A\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u7535\\u6e90\",\"specification\":\"\\u5b66\\u751f\\u7535\\u6e90\",\"quantity\":25,\"unit\":\"\\u53f0\"}]}]', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42'),
(3, '初中化学教学仪器配备标准（教育部）', 'MOE_JUNIOR_CHEMISTRY_2023', 1, 2, 'CHEMISTRY', '化学', '根据教育部最新标准制定的初中化学教学仪器配备要求', '[{\"category\":\"\\u73bb\\u7483\\u4eea\\u5668\",\"items\":[{\"name\":\"\\u8bd5\\u7ba1\",\"specification\":\"18\\u00d7180mm\",\"quantity\":100,\"unit\":\"\\u652f\"},{\"name\":\"\\u70e7\\u676f\",\"specification\":\"250ml\",\"quantity\":50,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u9525\\u5f62\\u74f6\",\"specification\":\"250ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u91cf\\u7b52\",\"specification\":\"100ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"}]},{\"category\":\"\\u52a0\\u70ed\\u5668\\u6750\",\"items\":[{\"name\":\"\\u9152\\u7cbe\\u706f\",\"specification\":\"150ml\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u4e09\\u811a\\u67b6\",\"specification\":\"\\u94c1\\u5236\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u77f3\\u68c9\\u7f51\",\"specification\":\"\\u6807\\u51c6\",\"quantity\":50,\"unit\":\"\\u4e2a\"}]}]', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42'),
(4, '初中生物教学仪器配备标准（教育部）', 'MOE_JUNIOR_BIOLOGY_2023', 1, 2, 'BIOLOGY', '生物', '根据教育部最新标准制定的初中生物教学仪器配备要求', '[{\"category\":\"\\u89c2\\u5bdf\\u5668\\u6750\",\"items\":[{\"name\":\"\\u663e\\u5fae\\u955c\",\"specification\":\"\\u5b66\\u751f\\u7528\\u53cc\\u76ee\",\"quantity\":25,\"unit\":\"\\u53f0\"},{\"name\":\"\\u653e\\u5927\\u955c\",\"specification\":\"10\\u500d\",\"quantity\":25,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u89e3\\u5256\\u955c\",\"specification\":\"\\u53cc\\u76ee\\u7acb\\u4f53\",\"quantity\":5,\"unit\":\"\\u53f0\"}]},{\"category\":\"\\u6807\\u672c\\u6a21\\u578b\",\"items\":[{\"name\":\"\\u4eba\\u4f53\\u9aa8\\u9abc\\u6a21\\u578b\",\"specification\":\"85cm\",\"quantity\":1,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u5fc3\\u810f\\u6a21\\u578b\",\"specification\":\"\\u81ea\\u7136\\u5927\",\"quantity\":1,\"unit\":\"\\u4e2a\"},{\"name\":\"\\u690d\\u7269\\u7ec6\\u80de\\u6a21\\u578b\",\"specification\":\"\\u653e\\u5927\",\"quantity\":1,\"unit\":\"\\u4e2a\"}]}]', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:22:42', '2025-07-18 15:22:42');

-- --------------------------------------------------------

--
-- 表的结构 `experiment_catalogs`
--

CREATE TABLE `experiment_catalogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT '学科ID',
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_catalogs`
--

INSERT INTO `experiment_catalogs` (`id`, `subject_id`, `name`, `code`, `type`, `grade`, `semester`, `chapter`, `duration`, `student_count`, `objective`, `materials`, `procedure`, `safety_notes`, `difficulty_level`, `is_standard`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, '测量物体的长度', 'MP_001', 4, 8, 1, '第一章 机械运动', 45, 2, '学会使用刻度尺测量物体长度，掌握测量的基本方法', '刻度尺、铅笔、硬币、细线等', '1.观察刻度尺的结构\\n2.学习正确的测量方法\\n3.测量不同物体的长度\\n4.记录测量结果', '使用刻度尺时要轻拿轻放，避免弯折', 1, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(2, 2, '测量重力加速度', 'MP_002', 4, 9, 1, '第十三章 力和机械', 90, 4, '通过实验测量重力加速度的大小', '单摆装置、秒表、刻度尺、小球等', '1.组装单摆装置\\n2.测量摆长\\n3.测量周期\\n4.计算重力加速度', '注意摆球的安全，避免碰撞', 3, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(3, 3, '氧气的制取和性质', 'MC_001', 3, 9, 1, '第二单元 我们周围的空气', 45, 1, '学习氧气的制取方法，观察氧气的性质', '高锰酸钾、试管、酒精灯、导管、集气瓶等', '1.装置连接\\n2.加热制取氧气\\n3.收集氧气\\n4.验证氧气性质', '注意用火安全，避免烫伤；注意通风', 2, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(4, 4, '观察植物细胞', 'MB_001', 4, 7, 1, '第二单元 生物体的结构层次', 45, 2, '学会制作临时装片，观察植物细胞的基本结构', '显微镜、载玻片、盖玻片、洋葱、碘液等', '1.制作洋葱表皮临时装片\\n2.显微镜观察\\n3.绘制细胞结构图\\n4.总结细胞特点', '小心使用显微镜，避免损坏镜头', 2, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56'),
(5, 5, '验证牛顿第二定律', 'HP_001', 4, 10, 2, '第四章 牛顿运动定律', 90, 4, '通过实验验证牛顿第二定律F=ma', '气垫导轨、滑块、砝码、光电门、计时器等', '1.调节气垫导轨水平\\n2.测量不同力下的加速度\\n3.测量不同质量下的加速度\\n4.分析数据验证定律', '注意气垫导轨的使用，避免损坏设备', 4, 1, 1, '2025-07-18 15:17:56', '2025-07-18 15:17:56');

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
  `completion_rate` decimal(5,2) NOT NULL DEFAULT 100.00 COMMENT '完成率(%)',
  `quality_score` tinyint(4) DEFAULT NULL COMMENT '质量评分(1-5)',
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '实验照片' CHECK (json_valid(`photos`)),
  `videos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '实验视频' CHECK (json_valid(`videos`)),
  `summary` text DEFAULT NULL COMMENT '实验总结',
  `problems` text DEFAULT NULL COMMENT '存在问题',
  `suggestions` text DEFAULT NULL COMMENT '改进建议',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：1进行中 2已完成 3异常结束',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_records`
--

INSERT INTO `experiment_records` (`id`, `reservation_id`, `school_id`, `catalog_id`, `laboratory_id`, `teacher_id`, `class_name`, `student_count`, `start_time`, `end_time`, `completion_rate`, `quality_score`, `photos`, `videos`, `summary`, `problems`, `suggestions`, `status`, `created_at`, `updated_at`) VALUES
(2, 88, 1, 5, 1, 16, '五年级(3)班', 31, '2025-06-08 14:54:00', '2025-06-08 16:15:00', 98.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-06-08 15:30:44', '2025-06-08 15:30:44'),
(3, 15, 1, 1, 1, 14, '三年级(1)班', 27, '2025-07-03 13:50:00', '2025-07-03 14:53:00', 97.00, 8, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-07-03 15:30:44', '2025-07-03 15:30:44'),
(4, 53, 1, 4, 1, 14, '三年级(3)班', 27, '2025-07-04 14:26:00', '2025-07-04 15:34:00', 91.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-07-04 15:30:44', '2025-07-04 15:30:44'),
(5, 149, 1, 5, 1, 15, '九年级(1)班', 42, '2025-06-04 14:38:00', '2025-06-04 15:50:00', 95.00, 9, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-06-04 15:30:44', '2025-06-04 15:30:44'),
(6, 71, 1, 4, 1, 14, '六年级(2)班', 44, '2025-06-27 15:12:00', '2025-06-27 15:57:00', 86.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-06-27 15:30:44', '2025-06-27 15:30:44'),
(7, 83, 1, 5, 1, 16, '高二(6)班', 28, '2025-06-10 11:16:00', '2025-06-10 12:01:00', 88.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-06-10 15:30:44', '2025-06-10 15:30:44'),
(8, 65, 1, 1, 1, 15, '高三(4)班', 42, '2025-05-27 15:17:00', '2025-05-27 16:42:00', 90.00, 7, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 3, '2025-05-27 15:30:44', '2025-05-27 15:30:44'),
(9, 87, 1, 5, 1, 15, '八年级(4)班', 45, '2025-06-18 13:51:00', '2025-06-18 14:45:00', 100.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-06-18 15:30:45', '2025-06-18 15:30:45'),
(10, 1, 1, 2, 1, 16, '三年级(1)班', 44, '2025-04-23 11:03:00', '2025-04-23 12:03:00', 90.00, 8, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-04-23 15:30:45', '2025-04-23 15:30:45'),
(11, 112, 1, 4, 1, 16, '三年级(4)班', 27, '2025-07-17 10:19:00', '2025-07-17 11:04:00', 91.00, 9, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 2, '2025-07-17 15:30:45', '2025-07-17 15:30:45'),
(12, 42, 1, 1, 2, 15, '九年级(4)班', 30, '2025-06-17 11:25:00', '2025-06-17 12:07:00', 84.00, 10, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-06-17 15:30:45', '2025-06-17 15:30:45'),
(13, 98, 1, 1, 2, 16, '一年级(5)班', 42, '2025-07-13 12:14:00', '2025-07-13 13:07:00', 81.00, 7, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-07-13 15:30:45', '2025-07-13 15:30:45'),
(14, 22, 1, 4, 2, 15, '六年级(4)班', 40, '2025-07-09 13:12:00', '2025-07-09 13:55:00', 99.00, 10, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 3, '2025-07-09 15:30:45', '2025-07-09 15:30:45'),
(15, 141, 1, 5, 2, 15, '四年级(1)班', 25, '2025-05-25 11:06:00', '2025-05-25 11:48:00', 98.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 3, '2025-05-25 15:30:45', '2025-05-25 15:30:45'),
(16, 10, 1, 4, 2, 15, '七年级(5)班', 38, '2025-07-12 15:30:00', '2025-07-12 16:49:00', 89.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-07-12 15:30:45', '2025-07-12 15:30:45'),
(17, 129, 1, 3, 2, 14, '二年级(3)班', 31, '2025-05-07 13:54:00', '2025-05-07 14:52:00', 87.00, 7, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 3, '2025-05-07 15:30:45', '2025-05-07 15:30:45'),
(18, 145, 1, 3, 2, 16, '高二(5)班', 42, '2025-05-27 08:00:00', '2025-05-27 08:48:00', 89.00, 9, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-05-27 15:30:45', '2025-05-27 15:30:45'),
(19, 106, 1, 5, 2, 15, '高三(2)班', 34, '2025-06-27 11:53:00', '2025-06-27 13:13:00', 93.00, 9, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-06-27 15:30:45', '2025-06-27 15:30:45'),
(20, 94, 1, 5, 2, 15, '八年级(4)班', 28, '2025-05-25 14:33:00', '2025-05-25 15:34:00', 95.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-05-25 15:30:45', '2025-05-25 15:30:45'),
(21, 104, 1, 4, 2, 15, '高一(5)班', 31, '2025-05-18 13:08:00', '2025-05-18 14:04:00', 100.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-05-18 15:30:45', '2025-05-18 15:30:45'),
(22, 133, 2, 2, 4, 21, '高三(3)班', 28, '2025-05-17 12:00:00', '2025-05-17 13:12:00', 94.00, 7, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-05-17 15:30:45', '2025-05-17 15:30:45'),
(23, 78, 2, 1, 4, 19, '五年级(6)班', 32, '2025-06-07 12:45:00', '2025-06-07 14:03:00', 94.00, 8, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 3, '2025-06-07 15:30:45', '2025-06-07 15:30:45'),
(24, 43, 2, 3, 4, 19, '二年级(2)班', 26, '2025-05-10 13:47:00', '2025-05-10 14:45:00', 87.00, 7, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 2, '2025-05-10 15:30:45', '2025-05-10 15:30:45'),
(25, 116, 2, 2, 4, 21, '七年级(5)班', 30, '2025-04-25 09:46:00', '2025-04-25 10:56:00', 84.00, 9, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-04-25 15:30:46', '2025-04-25 15:30:46'),
(26, 142, 2, 3, 4, 20, '三年级(5)班', 29, '2025-06-10 12:44:00', '2025-06-10 13:38:00', 98.00, 7, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-06-10 15:30:46', '2025-06-10 15:30:46'),
(27, 54, 2, 4, 4, 19, '五年级(6)班', 44, '2025-05-17 10:21:00', '2025-05-17 11:02:00', 92.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 2, '2025-05-17 15:30:46', '2025-05-17 15:30:46'),
(28, 34, 2, 5, 4, 19, '八年级(3)班', 45, '2025-05-20 10:25:00', '2025-05-20 11:42:00', 94.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-05-20 15:30:46', '2025-05-20 15:30:46'),
(29, 80, 2, 5, 4, 21, '六年级(3)班', 27, '2025-05-28 08:25:00', '2025-05-28 09:37:00', 90.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-05-28 15:30:46', '2025-05-28 15:30:46'),
(30, 100, 2, 5, 4, 19, '四年级(4)班', 43, '2025-06-18 15:15:00', '2025-06-18 16:36:00', 90.00, 7, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-06-18 15:30:46', '2025-06-18 15:30:46'),
(31, 75, 2, 4, 4, 19, '八年级(2)班', 42, '2025-05-12 15:42:00', '2025-05-12 17:01:00', 87.00, 7, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-05-12 15:30:46', '2025-05-12 15:30:46'),
(32, 136, 2, 3, 5, 20, '五年级(1)班', 31, '2025-04-29 10:54:00', '2025-04-29 12:21:00', 85.00, 8, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 2, '2025-04-29 15:30:46', '2025-04-29 15:30:46'),
(33, 70, 2, 2, 5, 20, '七年级(5)班', 27, '2025-06-26 15:03:00', '2025-06-26 16:13:00', 80.00, 7, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 2, '2025-06-26 15:30:46', '2025-06-26 15:30:46'),
(34, 127, 2, 4, 5, 20, '八年级(2)班', 39, '2025-05-12 08:15:00', '2025-05-12 09:25:00', 97.00, 10, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-05-12 15:30:46', '2025-05-12 15:30:46'),
(35, 66, 2, 2, 5, 20, '四年级(3)班', 39, '2025-05-23 12:28:00', '2025-05-23 13:43:00', 100.00, 10, NULL, NULL, '实验测量重力加速度顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-05-23 15:30:46', '2025-05-23 15:30:46'),
(36, 23, 2, 1, 5, 20, '高一(6)班', 28, '2025-05-28 14:35:00', '2025-05-28 15:44:00', 83.00, 10, NULL, NULL, '实验测量物体的长度顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-05-28 15:30:46', '2025-05-28 15:30:46'),
(37, 30, 2, 4, 5, 20, '高二(6)班', 34, '2025-06-27 09:39:00', '2025-06-27 11:05:00', 82.00, 9, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 2, '2025-06-27 15:30:47', '2025-06-27 15:30:47'),
(38, 47, 2, 3, 5, 20, '高三(6)班', 28, '2025-06-09 15:05:00', '2025-06-09 16:14:00', 91.00, 9, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-06-09 15:30:47', '2025-06-09 15:30:47'),
(39, 64, 2, 4, 5, 20, '八年级(3)班', 30, '2025-05-15 14:35:00', '2025-05-15 15:36:00', 84.00, 7, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-05-15 15:30:47', '2025-05-15 15:30:47'),
(40, 84, 2, 3, 5, 19, '四年级(1)班', 40, '2025-07-12 10:10:00', '2025-07-12 11:21:00', 81.00, 9, NULL, NULL, '实验氧气的制取和性质顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-07-12 15:30:47', '2025-07-12 15:30:47'),
(41, 135, 2, 5, 5, 21, '五年级(5)班', 27, '2025-05-31 08:11:00', '2025-05-31 09:15:00', 96.00, 8, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-05-31 15:30:47', '2025-05-31 15:30:47'),
(42, 26, 3, 5, 9, 25, '一年级(1)班', 28, '2025-06-26 12:01:00', '2025-06-26 13:26:00', 81.00, 8, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-06-26 15:30:47', '2025-06-26 15:30:47'),
(43, 7, 3, 5, 9, 26, '四年级(2)班', 33, '2025-04-25 11:00:00', '2025-04-25 12:24:00', 98.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', '部分学生操作不够熟练', '建议增加预习环节，提高实验效率。', 1, '2025-04-25 15:30:47', '2025-04-25 15:30:47'),
(44, 118, 3, 4, 9, 25, '一年级(6)班', 34, '2025-07-01 15:00:00', '2025-07-01 16:07:00', 80.00, 8, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 1, '2025-07-01 15:30:47', '2025-07-01 15:30:47'),
(45, 95, 3, 4, 9, 24, '八年级(2)班', 29, '2025-06-06 14:36:00', '2025-06-06 16:06:00', 95.00, 7, NULL, NULL, '实验观察植物细胞顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 2, '2025-06-06 15:30:47', '2025-06-06 15:30:47'),
(46, 38, 3, 5, 9, 25, '七年级(5)班', 25, '2025-05-01 11:25:00', '2025-05-01 12:09:00', 95.00, 10, NULL, NULL, '实验验证牛顿第二定律顺利完成，学生掌握了实验要点。', NULL, '建议增加预习环节，提高实验效率。', 3, '2025-05-01 15:30:47', '2025-05-01 15:30:47');

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
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `review_remark` text DEFAULT NULL COMMENT '审核备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_reservations`
--

INSERT INTO `experiment_reservations` (`id`, `school_id`, `catalog_id`, `laboratory_id`, `teacher_id`, `class_name`, `student_count`, `reservation_date`, `start_time`, `end_time`, `status`, `remark`, `reviewer_id`, `reviewed_at`, `review_remark`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 16, '一年级(5)班', 35, '2025-07-28', '13:47:00', '14:27:00', 4, '完成观察植物细胞实验教学任务', 39, '2025-07-13 15:27:44', '审核通过', '2025-07-14 15:27:44', '2025-07-18 15:30:45'),
(2, 1, 2, 1, 15, '四年级(1)班', 30, '2025-07-20', '14:01:00', '14:54:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-14 15:27:44', '2025-07-09 15:27:44'),
(3, 1, 4, 1, 16, '四年级(4)班', 42, '2025-07-28', '10:16:00', '11:26:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '2025-07-12 15:27:44', '2025-07-15 15:27:44'),
(4, 1, 3, 1, 14, '五年级(2)班', 28, '2025-08-08', '12:24:00', '13:43:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-14 15:27:44', '2025-07-12 15:27:44'),
(5, 1, 5, 1, 15, '一年级(5)班', 31, '2025-07-23', '15:29:00', '16:58:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-10 15:27:44', '2025-07-16 15:27:44'),
(6, 1, 3, 1, 15, '七年级(3)班', 38, '2025-08-17', '08:21:00', '09:05:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-09 15:27:44', '2025-07-08 15:27:44'),
(7, 1, 4, 1, 15, '四年级(5)班', 30, '2025-07-24', '08:34:00', '09:58:00', 4, '完成观察植物细胞实验教学任务', 21, '2025-07-17 15:27:44', '审核通过', '2025-07-09 15:27:44', '2025-07-18 15:30:47'),
(8, 1, 5, 1, 15, '九年级(1)班', 34, '2025-08-13', '09:49:00', '10:35:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:44', '2025-07-09 15:27:44'),
(9, 1, 3, 1, 16, '七年级(6)班', 35, '2025-08-10', '08:15:00', '09:45:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-12 15:27:44', '2025-07-08 15:27:44'),
(10, 1, 4, 1, 14, '二年级(5)班', 42, '2025-08-05', '08:08:00', '08:51:00', 4, '完成观察植物细胞实验教学任务', 35, '2025-07-14 15:27:44', '审核通过', '2025-07-11 15:27:44', '2025-07-18 15:30:45'),
(11, 1, 5, 1, 16, '九年级(3)班', 35, '2025-08-17', '15:47:00', '17:13:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:44', '2025-07-16 15:27:44'),
(12, 1, 3, 1, 15, '八年级(3)班', 31, '2025-08-06', '11:47:00', '12:33:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:44', '2025-07-12 15:27:44'),
(13, 1, 4, 1, 16, '二年级(3)班', 42, '2025-07-25', '10:43:00', '11:42:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-09 15:27:45', '2025-07-17 15:27:45'),
(14, 1, 1, 1, 16, '六年级(1)班', 32, '2025-07-26', '08:21:00', '09:38:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:45', '2025-07-14 15:27:45'),
(15, 1, 2, 1, 14, '二年级(1)班', 40, '2025-07-22', '13:22:00', '14:04:00', 4, '完成测量重力加速度实验教学任务', 29, '2025-07-15 15:27:45', '审核通过', '2025-07-14 15:27:45', '2025-07-18 15:30:44'),
(16, 1, 2, 2, 14, '四年级(5)班', 40, '2025-08-05', '10:40:00', '11:32:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-10 15:27:45', '2025-07-10 15:27:45'),
(17, 1, 5, 2, 16, '五年级(1)班', 44, '2025-08-07', '08:37:00', '09:46:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:45', '2025-07-15 15:27:45'),
(18, 1, 2, 2, 14, '四年级(6)班', 34, '2025-07-23', '09:14:00', '09:56:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-09 15:27:45', '2025-07-11 15:27:45'),
(19, 1, 5, 2, 15, '九年级(3)班', 28, '2025-08-04', '10:44:00', '11:45:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:45', '2025-07-08 15:27:45'),
(20, 1, 1, 2, 16, '七年级(2)班', 37, '2025-07-29', '08:42:00', '09:58:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-10 15:27:45', '2025-07-14 15:27:45'),
(21, 1, 5, 2, 15, '七年级(5)班', 33, '2025-08-13', '10:34:00', '11:50:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:45', '2025-07-08 15:27:45'),
(22, 1, 4, 2, 14, '六年级(6)班', 34, '2025-08-04', '15:15:00', '16:01:00', 4, '完成观察植物细胞实验教学任务', 36, '2025-07-16 15:27:45', '审核通过', '2025-07-15 15:27:45', '2025-07-18 15:30:45'),
(23, 1, 5, 2, 14, '六年级(4)班', 28, '2025-07-30', '08:38:00', '09:49:00', 4, '完成验证牛顿第二定律实验教学任务', 20, '2025-07-16 15:27:45', '审核通过', '2025-07-08 15:27:45', '2025-07-18 15:30:46'),
(24, 1, 5, 2, 14, '五年级(4)班', 39, '2025-08-15', '15:04:00', '15:58:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:45', '2025-07-16 15:27:45'),
(25, 1, 2, 2, 14, '三年级(3)班', 34, '2025-07-27', '09:05:00', '10:15:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-08 15:27:45', '2025-07-09 15:27:45'),
(26, 1, 3, 2, 16, '九年级(4)班', 41, '2025-07-23', '12:09:00', '13:32:00', 4, '完成氧气的制取和性质实验教学任务', 25, '2025-07-17 15:27:45', '审核通过', '2025-07-16 15:27:45', '2025-07-18 15:30:47'),
(27, 1, 3, 2, 15, '八年级(3)班', 31, '2025-07-26', '09:13:00', '10:00:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-08 15:27:45', '2025-07-08 15:27:45'),
(28, 1, 1, 2, 15, '九年级(3)班', 27, '2025-08-09', '13:25:00', '14:31:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-12 15:27:45', '2025-07-10 15:27:45'),
(29, 1, 2, 2, 15, '高三(1)班', 27, '2025-07-26', '10:14:00', '11:19:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:45', '2025-07-13 15:27:45'),
(30, 1, 4, 2, 14, '五年级(5)班', 39, '2025-08-05', '10:02:00', '11:18:00', 4, '完成观察植物细胞实验教学任务', 35, '2025-07-11 15:27:45', '审核通过', '2025-07-16 15:27:45', '2025-07-18 15:30:47'),
(31, 2, 2, 4, 19, '五年级(5)班', 41, '2025-08-13', '12:34:00', '13:47:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-09 15:27:45', '2025-07-16 15:27:45'),
(32, 2, 2, 4, 19, '七年级(5)班', 42, '2025-08-03', '11:44:00', '12:56:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-10 15:27:45', '2025-07-16 15:27:45'),
(33, 2, 1, 4, 20, '五年级(4)班', 31, '2025-08-15', '09:05:00', '09:58:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:45', '2025-07-10 15:27:45'),
(34, 2, 3, 4, 20, '七年级(6)班', 39, '2025-07-22', '12:15:00', '13:02:00', 4, '完成氧气的制取和性质实验教学任务', 29, '2025-07-17 15:27:45', '审核通过', '2025-07-17 15:27:45', '2025-07-18 15:30:46'),
(35, 2, 2, 4, 21, '七年级(1)班', 29, '2025-08-09', '15:12:00', '16:01:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:45', '2025-07-15 15:27:45'),
(36, 2, 3, 4, 19, '高三(2)班', 31, '2025-08-16', '12:05:00', '13:32:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:46', '2025-07-09 15:27:46'),
(37, 2, 2, 4, 19, '五年级(4)班', 34, '2025-08-03', '08:14:00', '08:59:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-12 15:27:46', '2025-07-10 15:27:46'),
(38, 2, 2, 4, 21, '高一(4)班', 43, '2025-08-05', '11:26:00', '12:08:00', 4, '完成测量重力加速度实验教学任务', 25, '2025-07-13 15:27:46', '审核通过', '2025-07-15 15:27:46', '2025-07-18 15:30:47'),
(39, 2, 1, 4, 19, '高二(6)班', 39, '2025-07-29', '09:12:00', '10:34:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-13 15:27:46', '2025-07-16 15:27:46'),
(40, 2, 5, 4, 19, '八年级(5)班', 25, '2025-08-16', '15:49:00', '17:02:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:46', '2025-07-14 15:27:46'),
(41, 2, 2, 4, 19, '一年级(2)班', 26, '2025-08-17', '13:20:00', '14:24:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-13 15:27:46', '2025-07-08 15:27:46'),
(42, 2, 3, 4, 20, '高二(1)班', 43, '2025-08-15', '14:48:00', '15:41:00', 4, '完成氧气的制取和性质实验教学任务', 36, '2025-07-16 15:27:46', '审核通过', '2025-07-12 15:27:46', '2025-07-18 15:30:45'),
(43, 2, 5, 4, 21, '九年级(2)班', 39, '2025-08-16', '09:32:00', '10:24:00', 4, '完成验证牛顿第二定律实验教学任务', 25, '2025-07-13 15:27:46', '审核通过', '2025-07-11 15:27:46', '2025-07-18 15:30:45'),
(44, 2, 3, 4, 21, '三年级(3)班', 37, '2025-08-14', '08:06:00', '09:08:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-09 15:27:46', '2025-07-08 15:27:46'),
(45, 2, 5, 4, 21, '一年级(6)班', 28, '2025-08-16', '12:36:00', '13:34:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-09 15:27:46', '2025-07-12 15:27:46'),
(46, 2, 3, 5, 19, '四年级(5)班', 39, '2025-08-05', '11:01:00', '12:31:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-08 15:27:46', '2025-07-15 15:27:46'),
(47, 2, 2, 5, 21, '九年级(5)班', 40, '2025-07-26', '13:27:00', '14:44:00', 4, '完成测量重力加速度实验教学任务', 21, '2025-07-13 15:27:46', '审核通过', '2025-07-08 15:27:46', '2025-07-18 15:30:47'),
(48, 2, 4, 5, 20, '七年级(3)班', 25, '2025-08-12', '12:01:00', '12:49:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '2025-07-14 15:27:46', '2025-07-12 15:27:46'),
(49, 2, 5, 5, 19, '二年级(1)班', 31, '2025-07-24', '08:40:00', '09:48:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:46', '2025-07-08 15:27:46'),
(50, 2, 4, 5, 21, '高三(6)班', 32, '2025-07-20', '12:23:00', '13:36:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '2025-07-13 15:27:46', '2025-07-08 15:27:46'),
(51, 2, 3, 5, 19, '一年级(4)班', 36, '2025-07-19', '14:02:00', '15:20:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-12 15:27:46', '2025-07-15 15:27:46'),
(52, 2, 2, 5, 19, '六年级(2)班', 41, '2025-08-15', '12:00:00', '13:17:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-17 15:27:46', '2025-07-15 15:27:46'),
(53, 2, 4, 5, 21, '九年级(2)班', 42, '2025-07-24', '15:33:00', '16:39:00', 4, '完成观察植物细胞实验教学任务', 24, '2025-07-16 15:27:46', '审核通过', '2025-07-16 15:27:46', '2025-07-18 15:30:44'),
(54, 2, 2, 5, 19, '二年级(3)班', 37, '2025-07-31', '15:21:00', '16:34:00', 4, '完成测量重力加速度实验教学任务', 26, '2025-07-11 15:27:46', '审核通过', '2025-07-09 15:27:46', '2025-07-18 15:30:46'),
(55, 2, 1, 5, 21, '三年级(3)班', 40, '2025-08-17', '09:00:00', '10:30:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-08 15:27:46', '2025-07-17 15:27:46'),
(56, 2, 5, 5, 21, '四年级(5)班', 42, '2025-07-28', '14:19:00', '15:21:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:46', '2025-07-16 15:27:46'),
(57, 2, 5, 5, 20, '三年级(1)班', 41, '2025-08-07', '12:54:00', '13:39:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '2025-07-10 15:27:46', '2025-07-17 15:27:46'),
(58, 2, 4, 5, 21, '七年级(3)班', 37, '2025-08-08', '14:00:00', '15:23:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '2025-07-13 15:27:46', '2025-07-13 15:27:46'),
(59, 2, 5, 5, 21, '九年级(3)班', 26, '2025-08-07', '09:02:00', '09:52:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '2025-07-12 15:27:46', '2025-07-17 15:27:46'),
(60, 2, 3, 5, 20, '高二(6)班', 34, '2025-07-26', '12:27:00', '13:46:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:46', '2025-07-09 15:27:46'),
(61, 3, 2, 9, 24, '高三(5)班', 45, '2025-08-02', '14:16:00', '15:03:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-08 15:27:46', '2025-07-15 15:27:46'),
(62, 3, 5, 9, 25, '七年级(2)班', 44, '2025-08-12', '15:48:00', '16:36:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:46', '2025-07-11 15:27:46'),
(63, 3, 1, 9, 25, '三年级(1)班', 41, '2025-08-10', '13:42:00', '14:47:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:46', '2025-07-12 15:27:46'),
(64, 3, 3, 9, 25, '五年级(3)班', 41, '2025-07-28', '13:40:00', '14:36:00', 4, '完成氧气的制取和性质实验教学任务', 29, '2025-07-12 15:27:46', '审核通过', '2025-07-09 15:27:46', '2025-07-18 15:30:47'),
(65, 3, 2, 9, 25, '二年级(5)班', 33, '2025-08-11', '13:02:00', '14:29:00', 4, '完成测量重力加速度实验教学任务', 40, '2025-07-11 15:27:47', '审核通过', '2025-07-13 15:27:47', '2025-07-18 15:30:44'),
(66, 3, 5, 9, 26, '高一(2)班', 25, '2025-08-04', '11:03:00', '11:59:00', 4, '完成验证牛顿第二定律实验教学任务', 31, '2025-07-17 15:27:47', '审核通过', '2025-07-08 15:27:47', '2025-07-18 15:30:46'),
(67, 3, 3, 9, 24, '高一(6)班', 27, '2025-07-26', '11:51:00', '13:05:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:47', '2025-07-13 15:27:47'),
(68, 3, 4, 9, 24, '九年级(6)班', 39, '2025-08-16', '14:50:00', '16:08:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:47', '2025-07-14 15:27:47'),
(69, 3, 2, 9, 25, '六年级(6)班', 38, '2025-08-16', '10:22:00', '11:12:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-09 15:27:47', '2025-07-15 15:27:47'),
(70, 3, 5, 9, 26, '八年级(1)班', 36, '2025-08-06', '08:52:00', '09:41:00', 4, '完成验证牛顿第二定律实验教学任务', 16, '2025-07-13 15:27:47', '审核通过', '2025-07-16 15:27:47', '2025-07-18 15:30:46'),
(71, 3, 5, 9, 25, '八年级(6)班', 43, '2025-08-09', '10:03:00', '11:30:00', 4, '完成验证牛顿第二定律实验教学任务', 35, '2025-07-17 15:27:47', '审核通过', '2025-07-11 15:27:47', '2025-07-18 15:30:44'),
(72, 3, 1, 9, 24, '九年级(2)班', 33, '2025-07-21', '15:31:00', '16:24:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-10 15:27:47', '2025-07-08 15:27:47'),
(73, 3, 1, 9, 26, '高三(6)班', 28, '2025-08-16', '11:40:00', '12:40:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-11 15:27:47', '2025-07-12 15:27:47'),
(74, 3, 5, 9, 26, '二年级(3)班', 35, '2025-08-06', '13:16:00', '14:38:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '2025-07-13 15:27:47', '2025-07-12 15:27:47'),
(75, 3, 5, 9, 26, '二年级(5)班', 45, '2025-08-04', '12:12:00', '13:41:00', 4, '完成验证牛顿第二定律实验教学任务', 39, '2025-07-15 15:27:47', '审核通过', '2025-07-11 15:27:47', '2025-07-18 15:30:46'),
(76, 3, 5, 10, 26, '八年级(6)班', 33, '2025-07-21', '13:47:00', '14:27:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:47', '2025-07-15 15:27:47'),
(77, 3, 3, 10, 25, '高三(2)班', 34, '2025-07-23', '09:02:00', '10:07:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-14 15:27:47', '2025-07-11 15:27:47'),
(78, 3, 4, 10, 25, '三年级(6)班', 36, '2025-08-01', '15:47:00', '16:52:00', 4, '完成观察植物细胞实验教学任务', 39, '2025-07-11 15:27:47', '审核通过', '2025-07-11 15:27:47', '2025-07-18 15:30:45'),
(79, 3, 1, 10, 25, '六年级(4)班', 35, '2025-07-20', '15:23:00', '16:49:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-11 15:27:47', '2025-07-14 15:27:47'),
(80, 3, 2, 10, 25, '一年级(3)班', 39, '2025-08-17', '15:15:00', '16:30:00', 4, '完成测量重力加速度实验教学任务', 16, '2025-07-12 15:27:47', '审核通过', '2025-07-13 15:27:47', '2025-07-18 15:30:46'),
(81, 3, 5, 10, 25, '高一(3)班', 25, '2025-08-06', '11:31:00', '12:57:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:47', '2025-07-15 15:27:47'),
(82, 3, 4, 10, 26, '高一(6)班', 38, '2025-08-15', '08:08:00', '09:34:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-08 15:27:47', '2025-07-08 15:27:47'),
(83, 3, 2, 10, 24, '高三(4)班', 40, '2025-07-30', '14:22:00', '15:04:00', 4, '完成测量重力加速度实验教学任务', 39, '2025-07-17 15:27:47', '审核通过', '2025-07-08 15:27:47', '2025-07-18 15:30:44'),
(84, 3, 2, 10, 26, '高二(3)班', 33, '2025-08-04', '09:01:00', '09:43:00', 4, '完成测量重力加速度实验教学任务', 40, '2025-07-14 15:27:47', '审核通过', '2025-07-09 15:27:47', '2025-07-18 15:30:47'),
(85, 3, 2, 10, 25, '四年级(1)班', 27, '2025-08-04', '10:54:00', '12:10:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-13 15:27:47', '2025-07-16 15:27:47'),
(86, 3, 2, 10, 26, '三年级(5)班', 32, '2025-08-14', '15:52:00', '17:21:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:47', '2025-07-12 15:27:47'),
(87, 3, 5, 10, 24, '九年级(6)班', 40, '2025-07-25', '09:31:00', '10:23:00', 4, '完成验证牛顿第二定律实验教学任务', 29, '2025-07-14 15:27:47', '审核通过', '2025-07-15 15:27:47', '2025-07-18 15:30:45'),
(88, 3, 3, 10, 25, '六年级(2)班', 28, '2025-07-24', '08:55:00', '10:23:00', 4, '完成氧气的制取和性质实验教学任务', 41, '2025-07-15 15:27:47', '审核通过', '2025-07-13 15:27:47', '2025-07-18 15:30:44'),
(89, 3, 5, 10, 24, '四年级(4)班', 32, '2025-08-04', '08:44:00', '09:37:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-12 15:27:47', '2025-07-11 15:27:47'),
(90, 3, 2, 10, 25, '高二(3)班', 39, '2025-08-09', '12:33:00', '13:27:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-14 15:27:47', '2025-07-11 15:27:47'),
(91, 4, 3, 12, 30, '高一(6)班', 45, '2025-08-06', '15:34:00', '16:26:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-08 15:27:47', '2025-07-14 15:27:47'),
(92, 4, 1, 12, 31, '四年级(3)班', 39, '2025-08-12', '15:42:00', '17:03:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:47', '2025-07-16 15:27:47'),
(93, 4, 3, 12, 29, '五年级(4)班', 40, '2025-07-25', '10:21:00', '11:40:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:48', '2025-07-12 15:27:48'),
(94, 4, 5, 12, 30, '一年级(2)班', 38, '2025-08-16', '11:41:00', '12:31:00', 4, '完成验证牛顿第二定律实验教学任务', 25, '2025-07-12 15:27:48', '审核通过', '2025-07-11 15:27:48', '2025-07-18 15:30:45'),
(95, 4, 5, 12, 30, '一年级(1)班', 30, '2025-07-30', '09:09:00', '10:21:00', 4, '完成验证牛顿第二定律实验教学任务', 41, '2025-07-15 15:27:48', '审核通过', '2025-07-17 15:27:48', '2025-07-18 15:30:47'),
(96, 4, 2, 12, 30, '高一(6)班', 28, '2025-07-25', '11:12:00', '12:41:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-13 15:27:48', '2025-07-09 15:27:48'),
(97, 4, 5, 12, 31, '八年级(1)班', 43, '2025-08-15', '12:50:00', '14:02:00', 1, '完成验证牛顿第二定律实验教学任务', NULL, NULL, NULL, '2025-07-11 15:27:48', '2025-07-08 15:27:48'),
(98, 4, 1, 12, 29, '二年级(5)班', 43, '2025-08-06', '11:34:00', '12:53:00', 4, '完成测量物体的长度实验教学任务', 19, '2025-07-14 15:27:48', '审核通过', '2025-07-10 15:27:48', '2025-07-18 15:30:45'),
(99, 4, 2, 12, 31, '高三(6)班', 38, '2025-07-25', '11:00:00', '12:16:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:48', '2025-07-10 15:27:48'),
(100, 4, 3, 12, 30, '一年级(2)班', 43, '2025-07-19', '12:27:00', '13:46:00', 4, '完成氧气的制取和性质实验教学任务', 40, '2025-07-16 15:27:48', '审核通过', '2025-07-10 15:27:48', '2025-07-18 15:30:46'),
(101, 4, 5, 12, 29, '高一(2)班', 42, '2025-08-17', '14:40:00', '15:27:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:48', '2025-07-08 15:27:48'),
(102, 4, 3, 12, 31, '高一(1)班', 31, '2025-07-23', '11:26:00', '12:09:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-10 15:27:48', '2025-07-11 15:27:48'),
(103, 4, 2, 12, 29, '三年级(2)班', 43, '2025-07-29', '12:11:00', '13:32:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-12 15:27:48', '2025-07-14 15:27:48'),
(104, 4, 4, 12, 30, '九年级(5)班', 44, '2025-08-01', '14:26:00', '15:24:00', 4, '完成观察植物细胞实验教学任务', 41, '2025-07-17 15:27:48', '审核通过', '2025-07-08 15:27:48', '2025-07-18 15:30:45'),
(105, 4, 2, 12, 29, '高二(2)班', 36, '2025-08-04', '11:33:00', '12:51:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-17 15:27:48', '2025-07-17 15:27:48'),
(106, 4, 4, 13, 30, '高二(2)班', 40, '2025-08-08', '14:42:00', '15:23:00', 4, '完成观察植物细胞实验教学任务', 25, '2025-07-11 15:27:48', '审核通过', '2025-07-11 15:27:48', '2025-07-18 15:30:45'),
(107, 4, 4, 13, 29, '高三(3)班', 41, '2025-08-10', '08:29:00', '09:38:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-08 15:27:48', '2025-07-08 15:27:48'),
(108, 4, 2, 13, 29, '六年级(4)班', 29, '2025-07-31', '14:11:00', '15:27:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-17 15:27:48', '2025-07-08 15:27:48'),
(109, 4, 4, 13, 29, '三年级(4)班', 44, '2025-08-16', '08:52:00', '10:04:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:48', '2025-07-12 15:27:48'),
(110, 4, 1, 13, 29, '六年级(6)班', 40, '2025-08-06', '15:33:00', '16:49:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-13 15:27:48', '2025-07-17 15:27:48'),
(111, 4, 2, 13, 29, '二年级(6)班', 41, '2025-08-17', '09:39:00', '10:57:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:48', '2025-07-10 15:27:48'),
(112, 4, 1, 13, 29, '五年级(3)班', 42, '2025-08-04', '15:36:00', '16:27:00', 4, '完成测量物体的长度实验教学任务', 29, '2025-07-11 15:27:48', '审核通过', '2025-07-08 15:27:48', '2025-07-18 15:30:45'),
(113, 4, 3, 13, 30, '七年级(3)班', 27, '2025-08-03', '12:18:00', '13:48:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-16 15:27:48', '2025-07-12 15:27:48'),
(114, 4, 3, 13, 29, '高二(6)班', 43, '2025-08-07', '11:48:00', '12:58:00', 3, '完成氧气的制取和性质实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-13 15:27:49', '2025-07-08 15:27:49'),
(115, 4, 4, 13, 30, '高三(4)班', 31, '2025-07-28', '14:48:00', '16:08:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:49', '2025-07-16 15:27:49'),
(116, 4, 2, 13, 29, '一年级(3)班', 25, '2025-07-24', '14:53:00', '15:44:00', 4, '完成测量重力加速度实验教学任务', 20, '2025-07-11 15:27:49', '审核通过', '2025-07-16 15:27:49', '2025-07-18 15:30:46'),
(117, 4, 2, 13, 29, '四年级(6)班', 45, '2025-08-05', '13:22:00', '14:43:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-17 15:27:49', '2025-07-16 15:27:49'),
(118, 4, 5, 13, 31, '五年级(5)班', 27, '2025-07-29', '08:27:00', '09:27:00', 4, '完成验证牛顿第二定律实验教学任务', 31, '2025-07-13 15:27:49', '审核通过', '2025-07-16 15:27:49', '2025-07-18 15:30:47'),
(119, 4, 3, 13, 30, '三年级(3)班', 31, '2025-08-11', '11:23:00', '12:39:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-17 15:27:49', '2025-07-10 15:27:49'),
(120, 4, 1, 13, 29, '三年级(4)班', 36, '2025-07-27', '09:07:00', '10:08:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-15 15:27:49', '2025-07-13 15:27:49'),
(121, 5, 2, 15, 35, '八年级(6)班', 36, '2025-08-14', '12:36:00', '13:46:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-09 15:27:49', '2025-07-11 15:27:49'),
(122, 5, 5, 15, 36, '高二(3)班', 42, '2025-07-22', '09:52:00', '11:09:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:49', '2025-07-17 15:27:49'),
(123, 5, 2, 15, 34, '四年级(5)班', 39, '2025-08-03', '09:51:00', '11:14:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-16 15:27:49', '2025-07-17 15:27:49'),
(124, 5, 4, 15, 34, '四年级(2)班', 39, '2025-07-29', '10:12:00', '10:58:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-14 15:27:49', '2025-07-08 15:27:49'),
(125, 5, 2, 15, 35, '高一(4)班', 26, '2025-08-12', '08:24:00', '09:08:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:49', '2025-07-17 15:27:49'),
(126, 5, 3, 15, 34, '高一(3)班', 30, '2025-08-10', '13:20:00', '14:33:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-08 15:27:49', '2025-07-14 15:27:49'),
(127, 5, 4, 15, 34, '七年级(1)班', 25, '2025-08-10', '10:47:00', '11:38:00', 4, '完成观察植物细胞实验教学任务', 20, '2025-07-14 15:27:49', '审核通过', '2025-07-15 15:27:49', '2025-07-18 15:30:46'),
(128, 5, 2, 15, 36, '四年级(2)班', 36, '2025-08-10', '11:19:00', '12:01:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-08 15:27:49', '2025-07-08 15:27:49'),
(129, 5, 4, 15, 35, '高一(3)班', 31, '2025-08-02', '11:37:00', '12:29:00', 4, '完成观察植物细胞实验教学任务', 41, '2025-07-17 15:27:49', '审核通过', '2025-07-15 15:27:49', '2025-07-18 15:30:45'),
(130, 5, 3, 15, 36, '高二(5)班', 36, '2025-07-25', '13:34:00', '14:26:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-10 15:27:49', '2025-07-14 15:27:49'),
(131, 5, 5, 15, 35, '九年级(4)班', 36, '2025-08-04', '12:48:00', '14:03:00', 3, '完成验证牛顿第二定律实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-17 15:27:49', '2025-07-08 15:27:49'),
(132, 5, 2, 15, 34, '三年级(5)班', 37, '2025-08-05', '15:03:00', '15:43:00', 1, '完成测量重力加速度实验教学任务', NULL, NULL, NULL, '2025-07-09 15:27:49', '2025-07-10 15:27:49'),
(133, 5, 5, 15, 36, '九年级(3)班', 36, '2025-08-13', '15:46:00', '16:34:00', 4, '完成验证牛顿第二定律实验教学任务', 30, '2025-07-17 15:27:49', '审核通过', '2025-07-15 15:27:49', '2025-07-18 15:30:45'),
(134, 5, 2, 15, 35, '一年级(2)班', 42, '2025-08-09', '14:28:00', '15:40:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-13 15:27:50', '2025-07-17 15:27:50'),
(135, 5, 3, 15, 34, '九年级(6)班', 34, '2025-07-31', '09:45:00', '10:25:00', 4, '完成氧气的制取和性质实验教学任务', 25, '2025-07-15 15:27:50', '审核通过', '2025-07-15 15:27:50', '2025-07-18 15:30:47'),
(136, 5, 3, 16, 36, '高三(4)班', 25, '2025-07-24', '11:11:00', '12:15:00', 4, '完成氧气的制取和性质实验教学任务', 15, '2025-07-14 15:27:50', '审核通过', '2025-07-16 15:27:50', '2025-07-18 15:30:46'),
(137, 5, 3, 16, 35, '二年级(4)班', 37, '2025-07-20', '14:49:00', '15:42:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-14 15:27:50', '2025-07-17 15:27:50'),
(138, 5, 4, 16, 35, '五年级(1)班', 41, '2025-07-27', '13:09:00', '14:32:00', 1, '完成观察植物细胞实验教学任务', NULL, NULL, NULL, '2025-07-11 15:27:50', '2025-07-08 15:27:50'),
(139, 5, 4, 16, 34, '一年级(3)班', 38, '2025-07-26', '14:41:00', '15:42:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-08 15:27:50', '2025-07-08 15:27:50'),
(140, 5, 1, 16, 34, '三年级(6)班', 38, '2025-07-25', '15:10:00', '16:13:00', 3, '完成测量物体的长度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-14 15:27:50', '2025-07-17 15:27:50'),
(141, 5, 3, 16, 34, '高一(5)班', 34, '2025-08-05', '13:43:00', '14:45:00', 4, '完成氧气的制取和性质实验教学任务', 15, '2025-07-12 15:27:50', '审核通过', '2025-07-17 15:27:50', '2025-07-18 15:30:45'),
(142, 5, 2, 16, 34, '七年级(3)班', 45, '2025-08-04', '08:06:00', '09:25:00', 4, '完成测量重力加速度实验教学任务', 15, '2025-07-13 15:27:50', '审核通过', '2025-07-17 15:27:50', '2025-07-18 15:30:46'),
(143, 5, 3, 16, 35, '七年级(3)班', 28, '2025-08-03', '09:51:00', '10:47:00', 1, '完成氧气的制取和性质实验教学任务', NULL, NULL, NULL, '2025-07-13 15:27:50', '2025-07-12 15:27:50'),
(144, 5, 1, 16, 36, '高一(1)班', 25, '2025-07-29', '13:19:00', '14:44:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-09 15:27:50', '2025-07-15 15:27:50'),
(145, 5, 4, 16, 35, '三年级(3)班', 40, '2025-08-13', '09:47:00', '10:56:00', 4, '完成观察植物细胞实验教学任务', 39, '2025-07-14 15:27:50', '审核通过', '2025-07-09 15:27:50', '2025-07-18 15:30:45'),
(146, 5, 4, 16, 36, '高一(1)班', 37, '2025-07-20', '15:17:00', '16:30:00', 3, '完成观察植物细胞实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:50', '2025-07-15 15:27:50'),
(147, 5, 1, 16, 36, '三年级(2)班', 28, '2025-07-27', '09:08:00', '09:53:00', 1, '完成测量物体的长度实验教学任务', NULL, NULL, NULL, '2025-07-15 15:27:50', '2025-07-17 15:27:50'),
(148, 5, 2, 16, 34, '五年级(6)班', 42, '2025-07-29', '11:40:00', '12:30:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-15 15:27:50', '2025-07-13 15:27:50'),
(149, 5, 5, 16, 34, '九年级(4)班', 33, '2025-07-24', '08:05:00', '09:31:00', 4, '完成验证牛顿第二定律实验教学任务', 24, '2025-07-14 15:27:50', '审核通过', '2025-07-16 15:27:50', '2025-07-18 15:30:44'),
(150, 5, 2, 16, 36, '高一(3)班', 32, '2025-08-07', '14:51:00', '16:05:00', 3, '完成测量重力加速度实验教学任务', NULL, NULL, '实验室设备维护中', '2025-07-11 15:27:50', '2025-07-12 15:27:50');

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
(20, 6, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 15:22:23', '2025-07-18 15:22:23');

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
(61, '2025_07_18_150000_create_teaching_equipment_standards_table', 2);

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
(1, 1, 'user', '2025-07-18 15:30:57', '2025-07-18 15:30:57'),
(2, 1, 'user.list', '2025-07-18 15:30:57', '2025-07-18 15:30:57'),
(3, 1, 'user.create', '2025-07-18 15:30:57', '2025-07-18 15:30:57'),
(4, 1, 'user.update', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(5, 1, 'user.delete', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(6, 1, 'user.edit', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(7, 1, 'user.export', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(8, 1, 'user.reset_password', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(9, 1, 'role', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(10, 1, 'role.list', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(11, 1, 'role.create', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(12, 1, 'role.update', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(13, 1, 'role.delete', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(14, 1, 'experiment', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(15, 1, 'experiment.catalog', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(16, 1, 'experiment.booking', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(17, 1, 'experiment.record', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(18, 1, 'equipment', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(19, 1, 'equipment.list', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(20, 1, 'equipment.create', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(21, 1, 'equipment.update', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(22, 1, 'equipment.delete', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(23, 1, 'equipment.borrow', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(24, 1, 'equipment.maintenance', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(25, 1, 'laboratory_type', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(26, 1, 'laboratory_type.list', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(27, 1, 'laboratory_type.create', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(28, 1, 'laboratory_type.update', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(29, 1, 'laboratory_type.delete', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(30, 1, 'equipment_standard', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(31, 1, 'equipment_standard.list', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(32, 1, 'equipment_standard.create', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(33, 1, 'equipment_standard.update', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(34, 1, 'equipment_standard.delete', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(35, 1, 'statistics.view', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(36, 1, 'statistics.dashboard', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(37, 1, 'statistics.experiment', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(38, 1, 'statistics.equipment', '2025-07-18 15:30:58', '2025-07-18 15:30:58'),
(39, 1, 'statistics.user', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(40, 1, 'statistics.performance', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(41, 1, 'statistics.export', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(42, 1, 'system', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(43, 1, 'system.read', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(44, 1, 'log', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(45, 1, 'log.read', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(46, 2, 'experiment', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(47, 2, 'experiment.catalog', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(48, 2, 'experiment.booking', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(49, 2, 'experiment.record', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(50, 2, 'equipment', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(51, 2, 'equipment.list', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(52, 2, 'laboratory_type', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(53, 2, 'laboratory_type.list', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(54, 2, 'equipment_standard', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(55, 2, 'equipment_standard.list', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(56, 3, 'user', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(57, 3, 'user.list', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(58, 3, 'user.create', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(59, 3, 'user.update', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(60, 3, 'user.delete', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(61, 3, 'user.edit', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(62, 3, 'user.export', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(63, 3, 'user.reset_password', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(64, 3, 'role', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(65, 3, 'role.list', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(66, 3, 'role.create', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(67, 3, 'role.update', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(68, 3, 'role.delete', '2025-07-18 15:30:59', '2025-07-18 15:30:59'),
(69, 3, 'experiment', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(70, 3, 'experiment.catalog', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(71, 3, 'experiment.booking', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(72, 3, 'experiment.record', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(73, 3, 'equipment', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(74, 3, 'equipment.list', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(75, 3, 'equipment.create', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(76, 3, 'equipment.update', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(77, 3, 'equipment.delete', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(78, 3, 'equipment.borrow', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(79, 3, 'equipment.maintenance', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(80, 3, 'laboratory_type', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(81, 3, 'laboratory_type.list', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(82, 3, 'laboratory_type.create', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(83, 3, 'laboratory_type.update', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(84, 3, 'laboratory_type.delete', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(85, 3, 'equipment_standard', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(86, 3, 'equipment_standard.list', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(87, 3, 'equipment_standard.create', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(88, 3, 'equipment_standard.update', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(89, 3, 'equipment_standard.delete', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(90, 3, 'statistics', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(91, 3, 'statistics.view', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(92, 3, 'statistics.dashboard', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(93, 3, 'statistics.experiment', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(94, 3, 'statistics.equipment', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(95, 3, 'statistics.user', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(96, 3, 'statistics.performance', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(97, 3, 'statistics.export', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(98, 4, 'experiment', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(99, 4, 'experiment.catalog', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(100, 4, 'experiment.booking', '2025-07-18 15:31:00', '2025-07-18 15:31:00'),
(101, 4, 'experiment.record', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(102, 4, 'equipment', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(103, 4, 'equipment.list', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(104, 4, 'laboratory_type', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(105, 4, 'laboratory_type.list', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(106, 4, 'equipment_standard', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(107, 4, 'equipment_standard.list', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(108, 5, 'user', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(109, 5, 'user.list', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(110, 5, 'user.create', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(111, 5, 'user.update', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(112, 5, 'user.delete', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(113, 5, 'user.edit', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(114, 5, 'user.export', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(115, 5, 'user.reset_password', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(116, 5, 'role', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(117, 5, 'role.list', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(118, 5, 'role.create', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(119, 5, 'role.update', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(120, 5, 'role.delete', '2025-07-18 15:31:01', '2025-07-18 15:31:01'),
(121, 5, 'experiment', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(122, 5, 'experiment.catalog', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(123, 5, 'experiment.booking', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(124, 5, 'experiment.record', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(125, 5, 'equipment', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(126, 5, 'equipment.list', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(127, 5, 'equipment.create', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(128, 5, 'equipment.update', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(129, 5, 'equipment.delete', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(130, 5, 'equipment.borrow', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(131, 5, 'equipment.maintenance', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(132, 5, 'laboratory_type', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(133, 5, 'laboratory_type.list', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(134, 5, 'laboratory_type.create', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(135, 5, 'laboratory_type.update', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(136, 5, 'laboratory_type.delete', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(137, 5, 'equipment_standard', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(138, 5, 'equipment_standard.list', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(139, 5, 'equipment_standard.create', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(140, 5, 'equipment_standard.update', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(141, 5, 'equipment_standard.delete', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(142, 5, 'statistics', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(143, 5, 'statistics.view', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(144, 5, 'statistics.dashboard', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(145, 5, 'statistics.experiment', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(146, 5, 'statistics.equipment', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(147, 5, 'statistics.user', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(148, 5, 'statistics.performance', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(149, 5, 'statistics.export', '2025-07-18 15:31:02', '2025-07-18 15:31:02'),
(150, 6, 'experiment', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(151, 6, 'experiment.catalog', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(152, 6, 'experiment.booking', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(153, 6, 'experiment.record', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(154, 6, 'equipment', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(155, 6, 'equipment.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(156, 6, 'laboratory_type', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(157, 6, 'laboratory_type.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(158, 6, 'equipment_standard', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(159, 6, 'equipment_standard.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(160, 7, 'user', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(161, 7, 'user.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(162, 7, 'user.create', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(163, 7, 'user.update', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(164, 7, 'experiment', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(165, 7, 'experiment.catalog', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(166, 7, 'experiment.booking', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(167, 7, 'experiment.record', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(168, 7, 'equipment', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(169, 7, 'equipment.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(170, 7, 'equipment.create', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(171, 7, 'equipment.update', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(172, 7, 'equipment.borrow', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(173, 7, 'equipment.maintenance', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(174, 7, 'laboratory_type', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(175, 7, 'laboratory_type.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(176, 7, 'equipment_standard', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(177, 7, 'equipment_standard.list', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(178, 7, 'statistics', '2025-07-18 15:31:03', '2025-07-18 15:31:03'),
(179, 7, 'statistics.view', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(180, 7, 'statistics.dashboard', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(181, 7, 'statistics.experiment', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(182, 7, 'statistics.equipment', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(183, 7, 'statistics.user', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(184, 7, 'statistics.performance', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(185, 8, 'experiment', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(186, 8, 'experiment.catalog', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(187, 8, 'experiment.booking', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(188, 8, 'experiment.record', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(189, 8, 'equipment', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(190, 8, 'equipment.list', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(191, 8, 'laboratory_type', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(192, 8, 'laboratory_type.list', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(193, 8, 'equipment_standard', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(194, 8, 'equipment_standard.list', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(195, 9, 'experiment', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(196, 9, 'experiment.catalog', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(197, 9, 'experiment.booking', '2025-07-18 15:31:04', '2025-07-18 15:31:04'),
(198, 9, 'experiment.record', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(199, 9, 'equipment', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(200, 9, 'equipment.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(201, 9, 'equipment.borrow', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(202, 9, 'equipment.maintenance', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(203, 9, 'laboratory_type', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(204, 9, 'laboratory_type.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(205, 9, 'equipment_standard', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(206, 9, 'equipment_standard.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(207, 10, 'experiment', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(208, 10, 'experiment.catalog', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(209, 10, 'experiment.booking', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(210, 10, 'experiment.record', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(211, 10, 'equipment', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(212, 10, 'equipment.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(213, 10, 'equipment.borrow', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(214, 10, 'equipment.maintenance', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(215, 10, 'laboratory_type', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(216, 10, 'laboratory_type.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(217, 10, 'equipment_standard', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(218, 10, 'equipment_standard.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(219, 11, 'experiment', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(220, 11, 'experiment.catalog', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(221, 11, 'experiment.booking', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(222, 11, 'experiment.record', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(223, 11, 'equipment', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(224, 11, 'equipment.list', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(225, 11, 'equipment.borrow', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(226, 11, 'laboratory_type', '2025-07-18 15:31:05', '2025-07-18 15:31:05'),
(227, 11, 'laboratory_type.list', '2025-07-18 15:31:06', '2025-07-18 15:31:06'),
(228, 11, 'equipment_standard', '2025-07-18 15:31:06', '2025-07-18 15:31:06'),
(229, 11, 'equipment_standard.list', '2025-07-18 15:31:06', '2025-07-18 15:31:06'),
(260, 12, 'equipment', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(261, 12, 'equipment.list', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(262, 12, 'equipment.create', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(263, 12, 'equipment.update', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(264, 12, 'equipment.delete', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(265, 12, 'equipment.borrow', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(266, 12, 'equipment.maintenance', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(267, 12, 'experiment', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(268, 12, 'experiment.catalog', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(269, 12, 'experiment.booking', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(270, 12, 'experiment.record', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(271, 12, 'statistics.view', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(272, 12, 'statistics.dashboard', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(273, 12, 'statistics.equipment', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(274, 12, 'statistics.experiment', '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(275, 13, 'experiment', '2025-07-18 16:29:10', '2025-07-18 16:29:10'),
(276, 13, 'experiment.catalog', '2025-07-18 16:29:10', '2025-07-18 16:29:10'),
(277, 13, 'experiment.booking', '2025-07-18 16:29:10', '2025-07-18 16:29:10'),
(278, 13, 'experiment.record', '2025-07-18 16:29:10', '2025-07-18 16:29:10'),
(279, 13, 'equipment.list', '2025-07-18 16:29:10', '2025-07-18 16:29:10'),
(280, 13, 'equipment.borrow', '2025-07-18 16:29:10', '2025-07-18 16:29:10');

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

INSERT INTO `schools` (`id`, `code`, `name`, `type`, `level`, `region_id`, `address`, `contact_person`, `contact_phone`, `student_count`, `class_count`, `teacher_count`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ZY001', '石家庄市藁城区实验小学', 1, 3, 10, '河北省石家庄市藁城区建设路123号', '张校长', '0371-12345678', 1200, 24, 80, 1, '2025-07-18 15:17:55', '2025-07-18 16:09:34'),
(2, 'ZY002', '石家庄市第一中学', 3, 2, 10, '河北省石家庄市藁城区中原路456号', '李校长', '0371-23456789', 2400, 48, 180, 1, '2025-07-18 15:17:55', '2025-07-18 16:09:34'),
(3, 'ZY003', '石家庄市藁城区第二小学', 1, 3, 6, '河北省石家庄市藁城区桐柏路789号', '王校长', '0371-34567890', 800, 18, 60, 1, '2025-07-18 15:17:55', '2025-07-18 15:44:43'),
(4, 'ZY004', '石家庄市藁城区实验中学', 2, 3, 6, '河北省石家庄市藁城区秦岭路321号', '赵校长', '0371-45678901', 1500, 30, 120, 1, '2025-07-18 15:17:55', '2025-07-18 15:44:43'),
(5, 'EQ001', '石家庄市栾城区实验小学', 1, 3, 7, '河北省石家庄市栾城区大学路654号', '陈校长', '0371-56789012', 1000, 20, 70, 1, '2025-07-18 15:17:55', '2025-07-18 15:44:43'),
(6, 'EQ002', '石家庄市栾城区九年制学校', 4, 3, 7, '河北省石家庄市栾城区航海路987号', '刘校长', '0371-67890123', 1800, 36, 140, 1, '2025-07-18 15:17:55', '2025-07-18 15:44:43'),
(7, 'HB001', '石家庄精英中学', 3, 1, 16, '河北省石家庄市长安区', '张校长', '0311-12345678', 2000, 40, 150, 1, '2025-07-18 15:31:16', '2025-07-18 16:09:34'),
(8, 'HB002', '衡水中学', 3, 1, 21, '河北省衡水市桃城区', '李校长', '0318-12345678', 3000, 60, 200, 1, '2025-07-18 15:31:16', '2025-07-18 16:09:34'),
(9, 'HB003', '保定七中', 3, 1, 1, '河北省保定市莲池区', '王校长', '0312-12345678', 1800, 36, 120, 1, '2025-07-18 15:31:16', '2025-07-18 16:09:34'),
(10, 'HB004', '邢台一中', 3, 1, 1, '河北省邢台市桥东区', '赵校长', '0319-12345678', 1600, 32, 110, 1, '2025-07-18 15:31:16', '2025-07-18 16:09:34'),
(11, 'SJZ001', '石家庄市第一中学', 3, 2, 9, '河北省石家庄市裕华区', '校长1', '0311-87654320', 1500, 30, 100, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(12, 'SJZ002', '石家庄市第二中学', 3, 2, 9, '河北省石家庄市新华区', '校长2', '0311-87654321', 1600, 32, 110, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(13, 'SJZ003', '石家庄外国语学校', 3, 2, 9, '河北省石家庄市桥西区', '校长3', '0311-87654322', 1700, 34, 120, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(14, 'SJZ004', '石家庄实验中学', 2, 2, 9, '河北省石家庄市长安区', '校长4', '0311-87654323', 1800, 36, 130, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(15, 'LZ001', '廉州东城小学', 1, 4, 11, '河北省石家庄市藁城区廉州镇廉州东城小学地址', '廉州东城小学校长', '0311-99999990', 200, 8, 15, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(16, 'LZ002', '廉州北街小学', 1, 4, 11, '河北省石家庄市藁城区廉州镇廉州北街小学地址', '廉州北街小学校长', '0311-99999991', 250, 10, 20, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(17, 'LZ003', '廉州第四中学', 2, 4, 11, '河北省石家庄市藁城区廉州镇廉州第四中学地址', '廉州第四中学校长', '0311-99999992', 300, 12, 25, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(18, 'LZ004', '廉州第一中学', 2, 4, 11, '河北省石家庄市藁城区廉州镇廉州第一中学地址', '廉州第一中学校长', '0311-99999993', 350, 14, 30, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(19, 'GC001', '通安小学', 1, 3, 10, '河北省石家庄市藁城区通安小学地址', '通安小学校长', '0311-88888880', 600, 18, 40, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(20, 'GC002', '实验小学', 1, 3, 10, '河北省石家庄市藁城区实验小学地址', '实验小学校长', '0311-88888881', 700, 21, 50, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00'),
(21, 'GC003', '石家庄第八中学', 2, 3, 10, '河北省藁城区石家庄第八中学地址', '石家庄第八中学校长', '0311-88888882', 800, 24, 60, 1, '2025-07-18 15:31:16', '2025-07-18 15:48:00');

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
(42, '高中生物教学仪器配备标准（教育部）', 'MOE_SENIOR_BIOLOGY_2023_006', 1, 3, 'BIOLOGY', '生物', NULL, '模型标本', 'DNA模型', NULL, '52601001001', 'DNA双螺旋模型', '可拆装，彩色编码', '套', 5, 280.00, 1400.00, 1, 0, 'JY/T 0407-2007', NULL, '分子结构学习', '2023.1', '2023-09-01', NULL, 1, '2025-07-18 15:09:13', '2025-07-18 15:09:13');

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

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `real_name`, `avatar`, `gender`, `birthday`, `id_card`, `status`, `role`, `department`, `position`, `bio`, `school_id`, `school_name`, `organization_id`, `organization_type`, `organization_level`, `last_login_at`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 'admin', 'admin@example.com', NULL, '$2y$12$Y73v9WLCwX8jyeIwOG6Yau1sKg8/yEL8KMAAWBVdAc3GDoEaajgF6', '系统管理员', NULL, NULL, NULL, NULL, 1, '省级管理员', NULL, NULL, NULL, NULL, NULL, 1, 'system', NULL, '2025-07-18 15:36:07', NULL, NULL, '2025-07-18 15:21:21', '2025-07-18 16:12:20'),
(4, 'province_admin_1', 'province_admin_0@example.com', NULL, '$2y$12$uBYPxEmDIQeJzYPgJrOmLeCNSke0hp3HM6qcTmaVB9wBbJQOsZyBW', '张伟', NULL, NULL, NULL, NULL, 1, '省级管理员', NULL, NULL, NULL, NULL, NULL, 1, 'province', NULL, '2025-07-18 15:39:49', NULL, NULL, '2025-07-18 15:21:21', '2025-07-18 16:12:20'),
(5, 'province_researcher_1', 'province_researcher_0@example.com', NULL, '$2y$12$j2K.rM3KHIdlyviiUfcvFeBJ5x5N.ODFIIgSeld03uXIKK/mPrAve', '李娜', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 1, 'province', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-18 15:49:13'),
(6, 'city_admin_1', 'city_admin_0@example.com', NULL, '$2y$12$8C/THpejw4tTgjBpLU6c8OGnnodiN7VFsHVOZpGtTIOW3ImNq5eZy', '王强', NULL, NULL, NULL, NULL, 1, '市级管理员', NULL, NULL, NULL, NULL, NULL, 9, 'city', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-18 16:12:20'),
(7, 'city_researcher_1', 'city_researcher_0@example.com', NULL, '$2y$12$VrfLYXt1pvav56Zgjr6UEuARtsEVIDPLKzAGhWG/057i3dTZ64C6q', '刘敏', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 9, 'city', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-18 16:09:34'),
(8, 'county_admin_1', 'county_admin_0@example.com', NULL, '$2y$12$A3OtwNWxRLGa9gk1Rrzrfu2Mz5daEmR1yMju/qh9x1SWy2LXo.w6.', '陈静', NULL, NULL, NULL, NULL, 1, '区县管理员', NULL, NULL, NULL, NULL, NULL, 10, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:22', '2025-07-18 16:12:20'),
(9, 'county_researcher_1', 'county_researcher_0@example.com', NULL, '$2y$12$fBxwKINv8v27cO1z6VYnyusY3bxoLEsbvAqJfL5Xpw.39bFaRAFS2', '杨勇', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 10, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-18 16:09:34'),
(10, 'county_admin_2', 'county_admin_1@example.com', NULL, '$2y$12$CUDzeiFKvKYz6UWv/8FszOPLalm4E9PO2FDNpWhvD3yNVjFyNv7YO', '赵丽', NULL, NULL, NULL, NULL, 1, '区县管理员', NULL, NULL, NULL, NULL, NULL, 15, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-18 16:12:21'),
(11, 'county_researcher_2', 'county_researcher_1@example.com', NULL, '$2y$12$VU1yCyt68Yg/w8zMHrEVOeplV5FM8f2.4Q6FoiiZHM/bg2tj9oIK.', '黄磊', NULL, NULL, NULL, NULL, 1, '省级教研员', NULL, NULL, NULL, NULL, NULL, 15, 'county', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-18 16:09:34'),
(12, 'school_admin_1', 'school_admin_0@example.com', NULL, '$2y$12$azMzm2fcExzqbdhe7IlBReb2wGMYfxaNudEjuHKRz2EEBgbNN.mTG', '周杰', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 1, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:23', '2025-07-18 16:12:21'),
(13, 'principal_1', 'principal_0@example.com', NULL, '$2y$12$PEFveT6dMzE42ZSxhrX.6ug4EzeK6bMQ8n2skIVN/TpMmL6V2R6aG', '吴琳', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 2, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:24', '2025-07-18 16:12:21'),
(14, 'teacher_0_1', 'teacher_0_1@example.com', NULL, '$2y$12$NVQegDa7FRFdi9ico3Tb2u5G4nspAeOUVMlis4.6Y6uiUlFmQdjuu', '徐明', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 3, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:24', '2025-07-18 16:12:21'),
(15, 'teacher_0_2', 'teacher_0_2@example.com', NULL, '$2y$12$T3fmcf9kVVpm.yrL7h.J9.K8f7F6cB8q9iLL8D0xwCj81GiZWtoR6', '朱红', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 4, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:24', '2025-07-18 16:12:21'),
(16, 'teacher_0_3', 'teacher_0_3@example.com', NULL, '$2y$12$1TRo5cdwrCW4MFLdExNH8uT576WIwdNHhoqPjifw2XTxRWET4WS9y', '马超', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 5, NULL, 1, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:24', '2025-07-18 16:12:21'),
(17, 'school_admin_2', 'school_admin_1@example.com', NULL, '$2y$12$NVNS8vzbkvtYVek44Ve/8./GtwGeytYrsdzLALTbvB5KGgzotKAyK', '胡斌', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 6, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:25', '2025-07-18 16:12:21'),
(18, 'principal_2', 'principal_1@example.com', NULL, '$2y$12$odUiwoVZk5m4zC7srF4MmOWMdptyrwhtlnTh/XZLkXCv0rKR9HfrG', '郭华', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 7, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:25', '2025-07-18 16:12:21'),
(19, 'teacher_1_1', 'teacher_1_1@example.com', NULL, '$2y$12$ZoQ5p.plSU2YQ6t5kmzk6.Aix8.LmjN2e0mjgexj0Dv7zLq20lvsq', '林峰', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 8, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:25', '2025-07-18 16:12:21'),
(20, 'teacher_1_2', 'teacher_1_2@example.com', NULL, '$2y$12$ibkMTNFPAuZT8LNhBIWAHOjm4x9vbPZV5YNMjYPSAA90SgTZWp0QG', '何艳', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 9, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-18 16:12:21'),
(21, 'teacher_1_3', 'teacher_1_3@example.com', NULL, '$2y$12$IHDYqBzHKRX9TXJdQXpwaOFNGEv2NonQ08mlVuUHQOG6XUkNwhYBm', '高飞', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 10, NULL, 9, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-18 16:12:21'),
(22, 'school_admin_3', 'school_admin_2@example.com', NULL, '$2y$12$XAWoiGsa9eMf64y1qyNUMOvlACTzF9uVHKuRL01FEDanXh5r4JYxS', '梁雪', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 11, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-18 16:12:21'),
(23, 'principal_3', 'principal_2@example.com', NULL, '$2y$12$ZCk5g5ChyAfAv8CNfTpfDeFXa73QY1ldkhgOsnsSmKX6VLX3CJKbm', '宋涛', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 12, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:26', '2025-07-18 16:12:21'),
(24, 'teacher_2_1', 'teacher_2_1@example.com', NULL, '$2y$12$aSD7ycfK/boKTkdmZUXS8uIB16X1kYCXz2/CQoLAmr.FVgyD.t6je', '唐丽', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 13, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-18 16:12:21'),
(25, 'teacher_2_2', 'teacher_2_2@example.com', NULL, '$2y$12$X.wCDS3XwGNeCKY0BaaU5.iN1VRpDf6ONvPMlMoIqTtkUjeruM9hW', '韩冰', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 14, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-18 16:12:21'),
(26, 'teacher_2_3', 'teacher_2_3@example.com', NULL, '$2y$12$70b7A1ycMC7ridYDLHFrP.xifrfbbcVoRsP8LLTcKSduzeUfOhMr.', '冯军', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 15, NULL, 10, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-18 16:12:21'),
(27, 'school_admin_4', 'school_admin_3@example.com', NULL, '$2y$12$0SV46Bo6xcZ0JBRTh2KtXuFvU8/F68n9AbivW5KSW4yvZurVZpvUK', '曹阳', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 16, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:27', '2025-07-18 16:12:21'),
(28, 'principal_4', 'principal_3@example.com', NULL, '$2y$12$iJfOK/lPaEIzZm28UVPNQe3oqatuhf9fsKi.7FnC0HgKZpD9Fg47G', '彭亮', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 17, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-18 16:12:21'),
(29, 'teacher_3_1', 'teacher_3_1@example.com', NULL, '$2y$12$mEvoCJ4kmN75Cjz66U2zGOvv2.nNscHOSGebvfcV57uoXn7.xiIuy', '董梅', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 18, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-18 16:12:21'),
(30, 'teacher_3_2', 'teacher_3_2@example.com', NULL, '$2y$12$dqeyBS7QM1JgFeeJgjfubepocUcQ2jpU1SVorkHwdGr6I1ZVbyxGi', '袁刚', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 19, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-18 16:12:21'),
(31, 'teacher_3_3', 'teacher_3_3@example.com', NULL, '$2y$12$y5xwkVo.2d1uvq3udknq.uqfh4aPa8jpHmldKD9yjqWC2aodZ.3n.', '邓萍', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 20, NULL, 15, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:28', '2025-07-18 16:12:21'),
(32, 'school_admin_5', 'school_admin_4@example.com', NULL, '$2y$12$AwSBfa1DVdJgrP4fTdXMDeJk7ZXE4VCqjDEWlmfeOeIdCSKGKz9nC', '范伟', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 21, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-18 16:12:21'),
(33, 'principal_5', 'principal_4@example.com', NULL, '$2y$12$RGNt29pARCtE8tulnjHSnung2EEVFNiE94vVeSFP9KwHCc6c0ZwKi', '石磊', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 1, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-18 16:13:26'),
(34, 'teacher_4_1', 'teacher_4_1@example.com', NULL, '$2y$12$Gf52nnWzEUCUPpUZUaHv8e69Vwr6voH104V9zhMtshiZSZuonUVhC', '郑州市二七区实验小学实验教师1', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 2, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-18 16:13:26'),
(35, 'teacher_4_2', 'teacher_4_2@example.com', NULL, '$2y$12$WyekQawv5ViP6p9.SBvjCeOdoDP9a8k6Uq/lSpj87t7G5M5L4hQQm', '郑州市二七区实验小学实验教师2', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 3, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:29', '2025-07-18 16:13:26'),
(36, 'teacher_4_3', 'teacher_4_3@example.com', NULL, '$2y$12$vu.QSvjaDDu2/pitZxQoXunNDhBrpIOuuc04IL1/4gyvI5TmWs0TC', '郑州市二七区实验小学实验教师3', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 4, NULL, 11, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-18 16:13:26'),
(37, 'school_admin_6', 'school_admin_5@example.com', NULL, '$2y$12$koZnJ6e3LM1T4NO1YP8QvO7/1rBvSFeGZe9S7SPV/I9Xum7OPehQO', '郑州市二七区九年制学校管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 5, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-18 16:13:26'),
(38, 'principal_6', 'principal_5@example.com', NULL, '$2y$12$f9iWErSOkJSqqdrWTNvixe9NfF7qJzVt3yPkfCfHNhoN1UM6fCPQm', '郑州市二七区九年制学校校长', NULL, NULL, NULL, NULL, 1, '校长', NULL, NULL, NULL, 6, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-18 16:13:26'),
(39, 'teacher_5_1', 'teacher_5_1@example.com', NULL, '$2y$12$YYOa75K22mUrBgzAAfP4S.EGwCvmWtqTv7lXwiLyrAzrVsby/A2LW', '郑州市二七区九年制学校实验教师1', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 7, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:30', '2025-07-18 16:13:26'),
(40, 'teacher_5_2', 'teacher_5_2@example.com', NULL, '$2y$12$Z1L/8d4HeVaJ1ZLc2DWd1u7H588qLheiqxGqAaIv6dbcTjXRWlm/K', '郑州市二七区九年制学校实验教师2', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 8, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:31', '2025-07-18 16:13:26'),
(41, 'teacher_5_3', 'teacher_5_3@example.com', NULL, '$2y$12$HmO7O4XcKQf/mZWrwHrHc.qMniM.SYh4.xCONC8DtqH9LrrUwThm6', '郑州市二七区九年制学校实验教师3', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 9, NULL, 6, 'school', NULL, NULL, NULL, NULL, '2025-07-18 15:21:31', '2025-07-18 16:13:26'),
(42, 'province_admin_test', 'province_admin@hebei.edu.cn', '0311-12345678', '$2y$12$RiSgNjSRKelrCh0NoQ1E7OcpvJ2ikV9gpGZOS91qgSHJ4C3iijrGW', '河北省管理员', NULL, NULL, NULL, NULL, 1, '省级管理员', NULL, NULL, NULL, NULL, NULL, 1, 'region', 1, '2025-07-18 20:03:01', NULL, NULL, '2025-07-18 15:31:17', '2025-07-18 20:03:01'),
(43, 'city_admin_test', 'city_admin@sjz.edu.cn', '0311-23456789', '$2y$12$3AuJimHjo0YeHW5zaLPJvuVH6ININ7jYKqPbdFBwLinuU265nxRfu', '石家庄市管理员', NULL, NULL, NULL, NULL, 1, '市级管理员', NULL, NULL, NULL, NULL, NULL, 9, 'region', 2, '2025-07-18 15:50:02', NULL, NULL, '2025-07-18 15:31:17', '2025-07-18 16:09:34'),
(44, 'county_admin_test', 'county_admin@gaocheng.edu.cn', '0311-34567890', '$2y$12$Kc.X162hdYNfs/LnUcfcmexnbECQ9qiRuMibtwdhiR6CGT9Q4GyTC', '藁城区管理员', NULL, NULL, NULL, NULL, 1, '区县管理员', NULL, NULL, NULL, NULL, NULL, 10, 'region', 3, NULL, NULL, NULL, '2025-07-18 15:31:17', '2025-07-18 16:09:34'),
(45, 'district_admin_test', 'district_admin@lianzhou.edu.cn', '0311-45678901', '$2y$12$KjcDqoNnyFdHjAlBcQDzmuxPUcFjHcStKe6M6FDHCUhYTSpxNa.5G', '廉州学区管理员', NULL, NULL, NULL, NULL, 1, '学区管理员', NULL, NULL, NULL, NULL, NULL, 11, 'region', 4, NULL, NULL, NULL, '2025-07-18 15:31:18', '2025-07-18 16:09:34'),
(46, 'school_admin_test', 'school_admin@school.edu.cn', '0311-56789012', '$2y$12$FNK9kzw3gdZLplLvz5yBoubzpj7JPLlb8UF3kpCid8obOHueAyFpC', '学校管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 15, NULL, 11, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:18', '2025-07-18 16:09:34'),
(47, 'province_school_admin', 'province_school@test.com', NULL, '$2y$12$RseUUp9UeNuAq9PkdznhIOsAYVbsolwT5icZDZXOfDAR8bvXTLIle', '石家庄精英中学管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 7, NULL, 7, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:18', '2025-07-18 16:13:26'),
(48, 'city_school_admin', 'city_school@test.com', NULL, '$2y$12$4cBIduQCinkyfr1HymX4eOBawsln5MTM6HnF9UP8yeJdaC6sMROe2', '石家庄市第一中学管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 11, NULL, 11, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:19', '2025-07-18 16:13:26'),
(49, 'county_school_admin', 'county_school@test.com', NULL, '$2y$12$L6tI7Ac/TplCp7FbNyhrZevOkiOpitGgdkaOlLON2hjOvrdU3eF/K', '通安小学管理员', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 19, NULL, 19, 'school', 5, NULL, NULL, NULL, '2025-07-18 15:31:19', '2025-07-18 16:13:26'),
(50, 'school_admin', 'school_admin@example.com', NULL, '$2y$12$HorJAWuhBDWFbICg8WctkOYSqdcJK.Jt7lotaJbTc4VOJa4Shccu6', '校长', NULL, NULL, NULL, NULL, 1, '学校管理员', NULL, NULL, NULL, 1, NULL, 11, NULL, NULL, NULL, NULL, NULL, '2025-07-18 15:31:38', '2025-07-18 16:12:20'),
(51, 'student001', 'student001@163.com', NULL, '$2y$12$BGGyj2edBlfsRFWxbL389uY5lvlAxMcZwWP1xV4ro8oZMwnTJc7pW', 'student001', NULL, NULL, NULL, NULL, 1, '任课教师', NULL, NULL, NULL, 1, NULL, 11, NULL, NULL, NULL, NULL, NULL, '2025-07-18 15:31:39', '2025-07-18 16:12:20');

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
(9, 50, 12, 'school', 1, '2025-07-18 15:31:39', '2025-07-18 15:31:39'),
(11, 5, 2, 'region', 1, '2025-07-18 15:33:43', '2025-07-18 15:33:43'),
(13, 7, 2, 'region', 2, '2025-07-18 15:33:43', '2025-07-18 15:33:43'),
(15, 9, 2, 'region', 3, '2025-07-18 15:33:43', '2025-07-18 15:33:43'),
(17, 11, 2, 'region', 4, '2025-07-18 15:33:43', '2025-07-18 15:33:43'),
(19, 13, 8, 'school', 2, '2025-07-18 15:33:43', '2025-07-18 16:12:21'),
(20, 14, 11, 'school', 3, '2025-07-18 15:33:43', '2025-07-18 16:12:21'),
(21, 15, 11, 'school', 4, '2025-07-18 15:33:43', '2025-07-18 16:12:21'),
(22, 16, 11, 'school', 5, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(24, 18, 8, 'school', 7, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(25, 19, 11, 'school', 8, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(26, 20, 11, 'school', 9, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(27, 21, 11, 'school', 10, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(29, 23, 8, 'school', 12, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(30, 24, 11, 'school', 13, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(31, 25, 11, 'school', 14, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(32, 26, 11, 'school', 15, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(34, 28, 8, 'school', 17, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(35, 29, 11, 'school', 18, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(36, 30, 11, 'school', 19, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(37, 31, 11, 'school', 20, '2025-07-18 15:33:44', '2025-07-18 16:12:21'),
(39, 33, 8, 'school', 1, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(40, 34, 11, 'school', 2, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(41, 35, 11, 'school', 3, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(42, 36, 11, 'school', 4, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(44, 38, 8, 'school', 6, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(45, 39, 11, 'school', 7, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(46, 40, 11, 'school', 8, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(47, 41, 11, 'school', 9, '2025-07-18 15:33:44', '2025-07-18 16:13:26'),
(49, 42, 1, 'region', 1, '2025-07-18 15:56:33', '2025-07-18 15:56:33'),
(50, 43, 3, 'region', 2, '2025-07-18 15:56:33', '2025-07-18 15:56:33'),
(51, 44, 5, 'region', 3, '2025-07-18 15:56:33', '2025-07-18 15:56:33'),
(52, 45, 7, 'region', 5, '2025-07-18 15:56:33', '2025-07-18 15:56:33'),
(53, 46, 12, 'school', 5, '2025-07-18 15:56:33', '2025-07-18 15:56:33'),
(72, 47, 12, 'school', 7, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(73, 48, 12, 'school', 11, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(74, 49, 12, 'school', 19, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(75, 51, 11, 'school', 1, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(76, 3, 1, 'region', 1, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(77, 4, 1, 'region', 1, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(78, 6, 3, 'region', 9, '2025-07-18 16:12:20', '2025-07-18 16:12:20'),
(79, 8, 5, 'region', 10, '2025-07-18 16:12:21', '2025-07-18 16:12:21'),
(80, 10, 5, 'region', 15, '2025-07-18 16:12:21', '2025-07-18 16:12:21'),
(86, 12, 12, 'school', 1, '2025-07-18 16:13:26', '2025-07-18 16:13:26'),
(87, 17, 12, 'school', 6, '2025-07-18 16:13:26', '2025-07-18 16:13:26'),
(88, 22, 12, 'school', 11, '2025-07-18 16:13:26', '2025-07-18 16:13:26'),
(89, 27, 12, 'school', 16, '2025-07-18 16:13:26', '2025-07-18 16:13:26'),
(90, 32, 12, 'school', 21, '2025-07-18 16:13:26', '2025-07-18 16:13:26'),
(91, 37, 12, 'school', 5, '2025-07-19 00:13:47', '2025-07-19 00:13:47');

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
  ADD KEY `equipment_borrows_approver_id_index` (`approver_id`);

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
  ADD KEY `equipment_standards_status_effective_date_index` (`status`,`effective_date`);

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
  ADD KEY `experiment_catalogs_status_index` (`status`);

--
-- 表的索引 `experiment_equipment_requirements`
--
ALTER TABLE `experiment_equipment_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiment_equipment_requirements_catalog_id_foreign` (`catalog_id`);

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
  ADD KEY `experiment_records_status_index` (`status`);

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
  ADD KEY `experiment_reservations_reviewer_id_index` (`reviewer_id`);

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
  ADD KEY `schools_level_index` (`level`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- 使用表AUTO_INCREMENT `equipment_maintenances`
--
ALTER TABLE `equipment_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `equipment_operation_logs`
--
ALTER TABLE `equipment_operation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `equipment_qrcodes`
--
ALTER TABLE `equipment_qrcodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `equipment_standards`
--
ALTER TABLE `equipment_standards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `experiment_equipment_requirements`
--
ALTER TABLE `experiment_equipment_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_records`
--
ALTER TABLE `experiment_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- 使用表AUTO_INCREMENT `experiment_reservations`
--
ALTER TABLE `experiment_reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `laboratory_types`
--
ALTER TABLE `laboratory_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- 使用表AUTO_INCREMENT `operation_logs`
--
ALTER TABLE `operation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- 使用表AUTO_INCREMENT `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- 使用表AUTO_INCREMENT `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

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
-- 限制表 `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  ADD CONSTRAINT `experiment_catalogs_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

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
-- 限制表 `experiment_reservations`
--
ALTER TABLE `experiment_reservations`
  ADD CONSTRAINT `experiment_reservations_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `experiment_catalogs` (`id`),
  ADD CONSTRAINT `experiment_reservations_laboratory_id_foreign` FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories` (`id`),
  ADD CONSTRAINT `experiment_reservations_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `experiment_reservations_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`),
  ADD CONSTRAINT `experiment_reservations_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

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
-- 限制表 `statistics_summary`
--
ALTER TABLE `statistics_summary`
  ADD CONSTRAINT `statistics_summary_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

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

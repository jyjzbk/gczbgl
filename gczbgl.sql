-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-07-19 00:17:34
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
(1, '130000', '河北省教育厅', 1, NULL, 1, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '石家庄市长安区裕华东路113号', '张教育厅长', '0311-66005000', 'hebei@edu.gov.cn', '河北省教育厅'),
(2, '130100', '石家庄市教育局', 2, 1, 1, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '石家庄市长安区中山东路87号', '李局长', '0311-86036000', 'sjz@edu.gov.cn', '石家庄市教育局'),
(3, '130200', '唐山市教育局', 2, 1, 2, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '唐山市路南区西山道6号', '王局长', '0315-2801000', 'ts@edu.gov.cn', '唐山市教育局'),
(4, '130900', '沧州市教育局', 2, 1, 3, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '沧州市运河区御河路1号', '刘局长', '0317-2160000', 'cz@edu.gov.cn', '沧州市教育局'),
(5, '131100', '衡水市教育局', 2, 1, 4, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '衡水市桃城区人民东路1号', '陈局长', '0318-2124000', 'hs@edu.gov.cn', '衡水市教育局'),
(6, '130182', '藁城区', 3, 2, 1, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '藁城区廉州西路1号', '赵区长', '0311-88012345', 'gaocheng@sjz.gov.cn', '石家庄市藁城区'),
(7, '130183', '鹿泉区', 3, 2, 2, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '鹿泉区获鹿镇', '孙区长', '0311-82011234', 'luquan@sjz.gov.cn', '石家庄市鹿泉区'),
(8, '130184', '栾城区', 3, 2, 3, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '栾城区栾城镇', '周区长', '0311-85011234', 'luancheng@sjz.gov.cn', '石家庄市栾城区'),
(9, '130102', '长安区', 3, 2, 4, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '长安区中山东路216号', '马区长', '0311-86011234', 'changan@sjz.gov.cn', '石家庄市长安区'),
(10, '130104', '桥西区', 3, 2, 5, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '桥西区维明南大街46号', '田区长', '0311-83011234', 'qiaoxi@sjz.gov.cn', '石家庄市桥西区'),
(11, '13018201', '廉州学区', 4, 6, 1, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '藁城区廉州镇教育办', '杨主任', '0311-88012346', 'lianzhou@gaocheng.edu.cn', '藁城区廉州学区'),
(12, '13018202', '梅花学区', 4, 6, 2, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '藁城区梅花镇教育办', '郭主任', '0311-88012347', 'meihua@gaocheng.edu.cn', '藁城区梅花学区'),
(13, '13018203', '南营学区', 4, 6, 3, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '藁城区南营镇教育办', '何主任', '0311-88012348', 'nanying@gaocheng.edu.cn', '藁城区南营学区'),
(14, '13018204', '岗上学区', 4, 6, 4, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20', '藁城区岗上镇教育办', '许主任', '0311-88012349', 'gangshang@gaocheng.edu.cn', '藁城区岗上学区');

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
('laravel-cache-user_permissions_1', 'a:45:{i:0;s:9:\"equipment\";i:1;s:18:\"equipment_standard\";i:2;s:25:\"equipment_standard.create\";i:3;s:25:\"equipment_standard.delete\";i:4;s:23:\"equipment_standard.list\";i:5;s:25:\"equipment_standard.update\";i:6;s:16:\"equipment.borrow\";i:7;s:16:\"equipment.create\";i:8;s:16:\"equipment.delete\";i:9;s:14:\"equipment.list\";i:10;s:21:\"equipment.maintenance\";i:11;s:16:\"equipment.update\";i:12;s:10:\"experiment\";i:13;s:18:\"experiment.booking\";i:14;s:18:\"experiment.catalog\";i:15;s:17:\"experiment.record\";i:16;s:15:\"laboratory_type\";i:17;s:22:\"laboratory_type.create\";i:18;s:22:\"laboratory_type.delete\";i:19;s:20:\"laboratory_type.list\";i:20;s:22:\"laboratory_type.update\";i:21;s:3:\"log\";i:22;s:8:\"log.read\";i:23;s:4:\"role\";i:24;s:11:\"role.create\";i:25;s:11:\"role.delete\";i:26;s:9:\"role.list\";i:27;s:11:\"role.update\";i:28;s:20:\"statistics.dashboard\";i:29;s:20:\"statistics.equipment\";i:30;s:21:\"statistics.experiment\";i:31;s:17:\"statistics.export\";i:32;s:22:\"statistics.performance\";i:33;s:15:\"statistics.user\";i:34;s:15:\"statistics.view\";i:35;s:6:\"system\";i:36;s:11:\"system.read\";i:37;s:4:\"user\";i:38;s:11:\"user.create\";i:39;s:11:\"user.delete\";i:40;s:9:\"user.edit\";i:41;s:11:\"user.export\";i:42;s:9:\"user.list\";i:43;s:19:\"user.reset_password\";i:44;s:11:\"user.update\";}', 1752854802);

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
(1, 12, NULL, 3, '生物显微镜XSP-2CA', 'BIO0010001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2025-01-24', 1500.00, 30, '台', 24, 10, '教育专项资金', '设备室', 10, 1, '/storage/qrcodes/equipment_1_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(2, 13, NULL, 3, '生物显微镜XSP-2CA', 'BIO0020001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2025-06-06', 1500.00, 30, '台', 24, 10, '教育专项资金', '设备室', 10, 1, '/storage/qrcodes/equipment_2_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(3, 14, NULL, 3, '生物显微镜XSP-2CA', 'BIO0030001', 'XSP-2CA', '奥林巴斯', '北京科学仪器公司', '010-12345678', '2024-09-11', 1500.00, 30, '台', 24, 10, '教育专项资金', '设备室', 10, 1, '/storage/qrcodes/equipment_3_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(4, 12, NULL, 3, '学生用生物显微镜', 'BIO00110002', 'XSP-1C', '上海光学', '上海教学设备公司', '021-87654321', '2025-01-23', 800.00, 50, '台', 12, 8, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_4_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(5, 13, NULL, 3, '学生用生物显微镜', 'BIO00220002', 'XSP-1C', '上海光学', '上海教学设备公司', '021-87654321', '2024-09-22', 800.00, 50, '台', 12, 8, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_5_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(6, 14, NULL, 3, '学生用生物显微镜', 'BIO00330002', 'XSP-1C', '上海光学', '上海教学设备公司', '021-87654321', '2025-01-14', 800.00, 50, '台', 12, 8, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_6_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(7, 12, NULL, 19, '电子天平', 'BAL0010001', 'FA2004', '上海精科', '上海精密仪器公司', '021-11111111', '2025-05-22', 3200.00, 5, '台', 36, 15, '实验室建设专项', '设备室', 10, 1, '/storage/qrcodes/equipment_7_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(8, 13, NULL, 19, '电子天平', 'BAL0020001', 'FA2004', '上海精科', '上海精密仪器公司', '021-11111111', '2025-03-22', 3200.00, 5, '台', 36, 15, '实验室建设专项', '设备室', 10, 1, '/storage/qrcodes/equipment_8_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(9, 14, NULL, 19, '电子天平', 'BAL0030001', 'FA2004', '上海精科', '上海精密仪器公司', '021-11111111', '2025-03-11', 3200.00, 5, '台', 36, 15, '实验室建设专项', '设备室', 10, 1, '/storage/qrcodes/equipment_9_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(10, 12, NULL, 19, '分析天平', 'BAL00110002', 'FA1004', '梅特勒', '北京梅特勒代理商', '010-22222222', '2025-05-20', 8500.00, 2, '台', 24, 12, '教育部专项', '设备室', 10, 1, '/storage/qrcodes/equipment_10_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(11, 13, NULL, 19, '分析天平', 'BAL00220002', 'FA1004', '梅特勒', '北京梅特勒代理商', '010-22222222', '2025-05-25', 8500.00, 2, '台', 24, 12, '教育部专项', '设备室', 10, 1, '/storage/qrcodes/equipment_11_1752840475.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:55'),
(12, 14, NULL, 19, '分析天平', 'BAL00330002', 'FA1004', '梅特勒', '北京梅特勒代理商', '010-22222222', '2024-08-27', 8500.00, 2, '台', 24, 12, '教育部专项', '设备室', 10, 1, '/storage/qrcodes/equipment_12_1752840476.png', '系统初始化数据', '2025-07-18 04:07:55', '2025-07-18 04:07:56'),
(13, 12, NULL, 31, '数字万用表', 'MUL0010001', 'UT33D', '优利德', '深圳优利德公司', '0755-33333333', '2025-04-26', 120.00, 40, '台', 12, 5, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_13_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(14, 13, NULL, 31, '数字万用表', 'MUL0020001', 'UT33D', '优利德', '深圳优利德公司', '0755-33333333', '2024-08-05', 120.00, 40, '台', 12, 5, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_14_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(15, 14, NULL, 31, '数字万用表', 'MUL0030001', 'UT33D', '优利德', '深圳优利德公司', '0755-33333333', '2024-10-01', 120.00, 40, '台', 12, 5, '学校自筹', '设备室', 10, 1, '/storage/qrcodes/equipment_15_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(16, 12, NULL, 37, '玻璃烧杯100ml', 'BEA0010001', 'GB-100', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-05-06', 8.50, 200, '个', 6, 3, '实验耗材专项', '设备室', 10, 1, '/storage/qrcodes/equipment_16_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(17, 13, NULL, 37, '玻璃烧杯100ml', 'BEA0020001', 'GB-100', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-04-15', 8.50, 200, '个', 6, 3, '实验耗材专项', '设备室', 10, 1, '/storage/qrcodes/equipment_17_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(18, 14, NULL, 37, '玻璃烧杯100ml', 'BEA0030001', 'GB-100', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2024-09-29', 8.50, 200, '个', 6, 3, '实验耗材专项', '设备室', 10, 1, '/storage/qrcodes/equipment_18_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(19, 12, NULL, 37, '玻璃烧杯250ml', 'BEA00110002', 'GB-250', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-03-22', 12.00, 150, '个', 6, 3, '实验耗材专项', '设备室', 10, 1, '/storage/qrcodes/equipment_19_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(20, 13, NULL, 37, '玻璃烧杯250ml', 'BEA00220002', 'GB-250', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2024-09-16', 12.00, 150, '个', 6, 3, '实验耗材专项', '设备室', 10, 1, '/storage/qrcodes/equipment_20_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(21, 14, NULL, 37, '玻璃烧杯250ml', 'BEA00330002', 'GB-250', '舒博玻璃', '江苏舒博实验器材公司', '0512-44444444', '2025-01-06', 12.00, 150, '个', 6, 3, '实验耗材专项', '设备室', 10, 1, '/storage/qrcodes/equipment_21_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(22, 12, NULL, 47, '解剖刀套装', 'SCA0010001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2025-02-27', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 10, 1, '/storage/qrcodes/equipment_22_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(23, 13, NULL, 47, '解剖刀套装', 'SCA0020001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2024-10-23', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 10, 1, '/storage/qrcodes/equipment_23_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56'),
(24, 14, NULL, 47, '解剖刀套装', 'SCA0030001', 'JPS-01', '金鹏生物', '北京金鹏生物器材公司', '010-55555555', '2024-12-18', 25.00, 60, '套', 3, 2, '生物实验专项', '设备室', 10, 1, '/storage/qrcodes/equipment_24_1752840476.png', '系统初始化数据', '2025-07-18 04:07:56', '2025-07-18 04:07:56');

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
(1, 7, NULL, 10, 1, '2025-07-14', '2025-07-31', NULL, '学生毕业设计实验', '', 5, NULL, NULL, NULL, '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(2, 10, NULL, 10, 1, '2025-07-17', '2025-08-10', NULL, '教学观摩课使用', '感谢配合实验室管理工作', 5, NULL, NULL, NULL, '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(3, 13, NULL, 10, 2, '2025-07-16', '2025-08-10', NULL, '教师演示实验', '', 5, NULL, NULL, NULL, '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(4, 8, NULL, 10, 3, '2025-07-17', '2025-08-09', NULL, '科普展示活动', '请妥善保管，按时归还', 5, NULL, NULL, NULL, '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(5, 13, NULL, 10, 1, '2025-07-13', '2025-08-01', NULL, '学生课外实验活动', '归还时请检查设备完整性', 5, NULL, NULL, NULL, '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(6, 7, NULL, 10, 3, '2025-07-09', '2025-08-07', NULL, '教师演示实验', '请妥善保管，按时归还', 1, 8, '2025-07-09 20:08:15', '审批通过', '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(7, 4, NULL, 10, 1, '2025-07-11', '2025-08-09', NULL, '教学观摩课使用', '', 1, 8, '2025-07-11 12:08:15', '审批通过', '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(8, 14, NULL, 10, 1, '2025-07-10', '2025-07-31', NULL, '设备功能测试', '感谢配合实验室管理工作', 1, 8, '2025-07-11 04:08:15', '审批通过', '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(9, 17, NULL, 10, 2, '2025-07-07', '2025-08-06', NULL, '科学兴趣小组活动', '实验过程中如有问题请联系管理员', 1, 8, '2025-07-07 15:08:15', '审批通过', '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(10, 13, NULL, 10, 2, '2025-07-08', '2025-08-05', NULL, '实验技能竞赛准备', '', 1, 8, '2025-07-08 23:08:15', '审批通过', '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(11, 19, NULL, 10, 3, '2025-07-07', '2025-07-20', NULL, '教学观摩课使用', '归还时请检查设备完整性', 1, 8, '2025-07-07 15:08:15', '审批通过', '2025-07-18 04:08:15', '2025-07-18 04:08:15'),
(12, 24, NULL, 10, 3, '2025-07-08', '2025-07-23', NULL, '生物实验课教学使用', '', 1, 8, '2025-07-09 03:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(13, 7, NULL, 10, 2, '2025-07-08', '2025-07-18', NULL, '教师培训使用', '实验结束后及时清洁设备', 1, 8, '2025-07-08 09:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(14, 23, NULL, 10, 2, '2025-06-30', '2025-07-25', '2025-07-25', '科普展示活动', '实验过程中如有问题请联系管理员', 2, 8, '2025-06-30 17:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(15, 22, NULL, 10, 3, '2025-07-01', '2025-07-28', '2025-07-26', '实验方法研究', '使用完毕请断电并整理', 2, 8, '2025-07-01 14:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(16, 23, NULL, 10, 1, '2025-07-01', '2025-07-08', '2025-07-05', '实验室开放日活动', '感谢配合实验室管理工作', 2, 8, '2025-07-01 16:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(17, 23, NULL, 10, 3, '2025-07-01', '2025-07-10', '2025-07-08', '设备功能测试', '归还时请检查设备完整性', 2, 8, '2025-07-01 16:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(18, 11, NULL, 10, 2, '2025-07-03', '2025-07-20', '2025-07-20', '物理实验课教学使用', '', 2, 8, '2025-07-03 15:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(19, 11, NULL, 10, 2, '2025-07-01', '2025-07-08', '2025-07-08', '设备功能测试', '', 2, 8, '2025-07-01 17:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(20, 21, NULL, 10, 1, '2025-07-02', '2025-07-11', '2025-07-10', '物理实验课教学使用', '实验结束后及时清洁设备', 2, 8, '2025-07-02 18:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(21, 23, NULL, 10, 3, '2025-06-28', '2025-07-15', '2025-07-14', '生物实验课教学使用', '', 2, 8, '2025-06-28 12:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(22, 2, NULL, 10, 1, '2025-07-02', '2025-07-10', '2025-07-07', '教学观摩课使用', '感谢配合实验室管理工作', 2, 8, '2025-07-02 19:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(23, 14, NULL, 10, 1, '2025-06-29', '2025-07-23', '2025-07-20', '物理实验课教学使用', '注意安全操作', 2, 8, '2025-06-30 03:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(24, 11, NULL, 10, 2, '2025-06-28', '2025-07-28', '2025-07-25', '实验室开放日活动', '感谢配合实验室管理工作', 2, 8, '2025-06-28 14:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(25, 13, NULL, 10, 2, '2025-06-28', '2025-07-26', '2025-07-24', '教师培训使用', '', 2, 8, '2025-06-29 02:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(26, 23, NULL, 10, 2, '2025-06-27', '2025-07-13', NULL, '实验方法研究', '使用完毕请断电并整理', 3, 8, '2025-06-27 09:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(27, 19, NULL, 10, 3, '2025-06-27', '2025-07-14', NULL, '设备功能测试', '', 3, 8, '2025-06-27 21:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(28, 24, NULL, 10, 3, '2025-06-26', '2025-07-09', NULL, '教师培训使用', '', 3, 8, '2025-06-26 15:08:16', '审批通过', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(29, 20, NULL, 10, 2, '2025-07-08', '2025-07-16', NULL, '设备功能测试', '申请借用', 6, 8, '2025-07-08 23:08:16', '设备维修中，暂不可借用', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(30, 12, NULL, 10, 1, '2025-07-10', '2025-07-24', NULL, '学生毕业设计实验', '申请借用', 6, 8, '2025-07-11 19:08:16', '设备维修中，暂不可借用', '2025-07-18 04:08:16', '2025-07-18 04:08:16'),
(31, 9, NULL, 10, 2, '2025-07-09', '2025-07-19', NULL, '实验技能竞赛准备', '申请借用', 6, 8, '2025-07-09 21:08:16', '设备维修中，暂不可借用', '2025-07-18 04:08:16', '2025-07-18 04:08:16');

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
(1, '光学仪器', 'OPTICAL', NULL, 1, 1, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(2, '显微镜', 'MICROSCOPE', 1, 2, 1, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(3, '生物显微镜', 'BIO_MICROSCOPE', 2, 3, 1, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(4, '体视显微镜', 'STEREO_MICROSCOPE', 2, 3, 2, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(5, '电子显微镜', 'ELECTRON_MICROSCOPE', 2, 3, 3, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(6, '望远镜', 'TELESCOPE', 1, 2, 2, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(7, '天文望远镜', 'ASTRO_TELESCOPE', 6, 3, 1, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(8, '地理望远镜', 'GEO_TELESCOPE', 6, 3, 2, 1, '2025-07-18 04:04:24', '2025-07-18 04:04:24'),
(9, '放大镜', 'MAGNIFIER', 1, 2, 3, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(10, '手持放大镜', 'HAND_MAGNIFIER', 9, 3, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(11, '台式放大镜', 'DESK_MAGNIFIER', 9, 3, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(12, '测量仪器', 'MEASUREMENT', NULL, 1, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(13, '长度测量', 'LENGTH_MEASURE', 12, 2, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(14, '直尺', 'RULER', 13, 3, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(15, '卷尺', 'TAPE_MEASURE', 13, 3, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(16, '游标卡尺', 'VERNIER_CALIPER', 13, 3, 3, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(17, '螺旋测微器', 'MICROMETER', 13, 3, 4, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(18, '质量测量', 'MASS_MEASURE', 12, 2, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(19, '天平', 'BALANCE', 18, 3, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(20, '电子秤', 'ELECTRONIC_SCALE', 18, 3, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(21, '弹簧秤', 'SPRING_SCALE', 18, 3, 3, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(22, '时间测量', 'TIME_MEASURE', 12, 2, 3, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(23, '秒表', 'STOPWATCH', 22, 3, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(24, '计时器', 'TIMER', 22, 3, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(25, '电学仪器', 'ELECTRICAL', NULL, 1, 3, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(26, '电源设备', 'POWER_SUPPLY', 25, 2, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(27, '直流电源', 'DC_POWER', 26, 3, 1, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(28, '交流电源', 'AC_POWER', 26, 3, 2, 1, '2025-07-18 04:04:25', '2025-07-18 04:04:25'),
(29, '稳压电源', 'REGULATED_POWER', 26, 3, 3, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(30, '测量仪表', 'ELECTRICAL_METER', 25, 2, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(31, '万用表', 'MULTIMETER', 30, 3, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(32, '电压表', 'VOLTMETER', 30, 3, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(33, '电流表', 'AMMETER', 30, 3, 3, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(34, '示波器', 'OSCILLOSCOPE', 30, 3, 4, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(35, '化学仪器', 'CHEMICAL', NULL, 1, 4, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(36, '玻璃仪器', 'GLASSWARE', 35, 2, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(37, '烧杯', 'BEAKER', 36, 3, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(38, '试管', 'TEST_TUBE', 36, 3, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(39, '量筒', 'GRADUATED_CYLINDER', 36, 3, 3, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(40, '容量瓶', 'VOLUMETRIC_FLASK', 36, 3, 4, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(41, '加热设备', 'HEATING_EQUIPMENT', 35, 2, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(42, '酒精灯', 'ALCOHOL_LAMP', 41, 3, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(43, '电热板', 'HOT_PLATE', 41, 3, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(44, '马弗炉', 'MUFFLE_FURNACE', 41, 3, 3, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(45, '生物仪器', 'BIOLOGICAL', NULL, 1, 5, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(46, '解剖工具', 'DISSECTION_TOOLS', 45, 2, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(47, '解剖刀', 'SCALPEL', 46, 3, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(48, '解剖剪', 'DISSECTION_SCISSORS', 46, 3, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(49, '镊子', 'FORCEPS', 46, 3, 3, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(50, '培养设备', 'CULTURE_EQUIPMENT', 45, 2, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(51, '培养皿', 'PETRI_DISH', 50, 3, 1, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(52, '培养箱', 'INCUBATOR', 50, 3, 2, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26'),
(53, '接种环', 'INOCULATION_LOOP', 50, 3, 3, 1, '2025-07-18 04:04:26', '2025-07-18 04:04:26');

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
(1, 7, 10, '镜头模糊，无法清晰观察', '控制系统故障', 1, NULL, '2025-07-16', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '建议更新使用手册和操作指南', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(2, 10, 10, '连接线路松动，信号传输不稳定', '软件故障', 1, NULL, '2025-07-16', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(3, 1, 10, '设备频繁死机，需要重启才能使用', '显示故障', 2, NULL, '2025-07-16', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '建议建立设备维护档案', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(4, 3, 10, '显示屏出现花屏现象，图像不清晰', '校准问题', 2, NULL, '2025-07-15', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '建议更新使用手册和操作指南', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(5, 1, 10, '设备频繁死机，需要重启才能使用', '过载故障', 3, NULL, '2025-07-17', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(6, 6, 10, '设备外壳出现裂纹，影响使用安全', '软件故障', 3, NULL, '2025-07-14', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '故障可能与使用环境有关，建议改善使用条件', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(7, 8, 10, '机械部件卡死，无法正常转动', '软件故障', 3, NULL, '2025-07-09', 8, '2025-07-11', NULL, NULL, NULL, NULL, 2, NULL, '', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(8, 6, 10, '设备过热，运行一段时间后自动关机', '温度异常', 1, NULL, '2025-07-12', 8, '2025-07-14', NULL, NULL, NULL, NULL, 2, NULL, '', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(9, 18, 10, '设备运行过程中发出异常噪音', '操作系统故障', 3, NULL, '2025-07-08', 8, '2025-07-09', NULL, NULL, NULL, NULL, 2, NULL, '建议定期检查相关部件', '2025-07-18 04:08:27', '2025-07-18 04:08:27'),
(10, 9, 10, '精度下降，测量结果不准确', '精度问题', 3, NULL, '2025-07-08', 8, '2025-07-09', NULL, NULL, NULL, NULL, 2, NULL, '建议定期检查相关部件', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(11, 20, 10, '设备运行过程中发出异常噪音', '机械故障', 3, NULL, '2025-07-03', 8, '2025-07-05', '2025-07-10', 153.00, '更换损坏的电源模块，重新测试设备功能', '变压器', 3, 3, '故障原因已查明并解决', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(12, 20, 10, '设备过热，运行一段时间后自动关机', '过载故障', 1, NULL, '2025-06-29', 8, '2025-07-01', '2025-07-05', 395.00, '更换磨损的机械部件，添加润滑油', '电机', 3, 5, '故障可能与使用环境有关，建议改善使用条件', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(13, 22, 10, '设备过热，运行一段时间后自动关机', '电子元件故障', 3, NULL, '2025-06-28', 8, '2025-07-01', '2025-07-06', 67.00, '更新软件版本，修复系统漏洞', '', 3, 4, '', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(14, 6, 10, '精度下降，测量结果不准确', '传感器故障', 3, NULL, '2025-06-28', 8, '2025-06-29', '2025-07-04', 228.00, '更新软件版本，修复系统漏洞', '按键开关', 3, 4, '建议定期检查相关部件', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(15, 12, 10, '精度下降，测量结果不准确', '校准问题', 3, NULL, '2025-07-02', 8, '2025-07-04', '2025-07-05', 60.00, '修复软件系统，重新安装驱动程序', '连接线', 3, 5, '', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(16, 17, 10, '镜头模糊，无法清晰观察', '传感器故障', 2, NULL, '2025-07-02', 8, '2025-07-04', '2025-07-05', 365.00, '修复显示模块，更换显示屏', '', 3, 3, '故障原因已查明并解决', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(17, 6, 10, '温度控制失效，无法达到设定温度', '校准问题', 1, NULL, '2025-07-02', 8, '2025-07-04', '2025-07-10', 172.00, '重新设置系统参数，恢复出厂设置', '按键开关', 3, 4, '建议定期检查相关部件', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(18, 24, 10, '连接线路松动，信号传输不稳定', '软件故障', 2, NULL, '2025-06-30', 8, '2025-07-02', '2025-07-06', 61.00, '更换损坏的传感器，重新连接线路', '光学镜头', 3, 4, '需要对使用人员进行培训', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(19, 14, 10, '设备运行过程中发出异常噪音', '机械故障', 1, NULL, '2025-07-03', 8, '2025-07-04', '2025-07-07', NULL, '设备老化严重，多个部件同时故障', NULL, 4, NULL, '故障可能与使用环境有关，建议改善使用条件', '2025-07-18 04:08:28', '2025-07-18 04:08:28'),
(20, 5, 10, '连接线路松动，信号传输不稳定', '软件故障', 1, NULL, '2025-07-04', 8, '2025-07-06', '2025-07-10', NULL, '关键部件已停产，无法找到替换件', NULL, 4, NULL, '', '2025-07-18 04:08:28', '2025-07-18 04:08:28');

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
(1, 2, '测量物体的长度', 'MP_001', 4, 8, 1, '第一章 机械运动', 45, 2, '学会使用刻度尺测量物体长度，掌握测量的基本方法', '刻度尺、铅笔、硬币、细线等', '1.观察刻度尺的结构\\n2.学习正确的测量方法\\n3.测量不同物体的长度\\n4.记录测量结果', '使用刻度尺时要轻拿轻放，避免弯折', 1, 1, 1, '2025-07-18 04:04:23', '2025-07-18 04:04:23'),
(2, 2, '测量重力加速度', 'MP_002', 4, 9, 1, '第十三章 力和机械', 90, 4, '通过实验测量重力加速度的大小', '单摆装置、秒表、刻度尺、小球等', '1.组装单摆装置\\n2.测量摆长\\n3.测量周期\\n4.计算重力加速度', '注意摆球的安全，避免碰撞', 3, 1, 1, '2025-07-18 04:04:23', '2025-07-18 04:04:23'),
(3, 3, '氧气的制取和性质', 'MC_001', 3, 9, 1, '第二单元 我们周围的空气', 45, 1, '学习氧气的制取方法，观察氧气的性质', '高锰酸钾、试管、酒精灯、导管、集气瓶等', '1.装置连接\\n2.加热制取氧气\\n3.收集氧气\\n4.验证氧气性质', '注意用火安全，避免烫伤；注意通风', 2, 1, 1, '2025-07-18 04:04:23', '2025-07-18 04:04:23'),
(4, 4, '观察植物细胞', 'MB_001', 4, 7, 1, '第二单元 生物体的结构层次', 45, 2, '学会制作临时装片，观察植物细胞的基本结构', '显微镜、载玻片、盖玻片、洋葱、碘液等', '1.制作洋葱表皮临时装片\\n2.显微镜观察\\n3.绘制细胞结构图\\n4.总结细胞特点', '小心使用显微镜，避免损坏镜头', 2, 1, 1, '2025-07-18 04:04:23', '2025-07-18 04:04:23'),
(5, 5, '验证牛顿第二定律', 'HP_001', 4, 10, 2, '第四章 牛顿运动定律', 90, 4, '通过实验验证牛顿第二定律F=ma', '气垫导轨、滑块、砝码、光电门、计时器等', '1.调节气垫导轨水平\\n2.测量不同力下的加速度\\n3.测量不同质量下的加速度\\n4.分析数据验证定律', '注意气垫导轨的使用，避免损坏设备', 4, 1, 1, '2025-07-18 04:04:23', '2025-07-18 04:04:23');

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
(1, 1, 12, 1, 1, 10, '八年级1班', 45, '2025-07-18 08:00:00', '2025-07-18 09:25:00', 95.00, 4, NULL, NULL, '学生基本掌握了刻度尺的使用方法，测量结果较为准确', '部分学生读数时存在视差问题', '建议加强读数方法的训练', 2, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(2, 2, 12, 2, 1, 10, '九年级1班', 42, '2025-07-17 10:00:00', '2025-07-17 11:28:00', 88.00, 4, NULL, NULL, '实验过程顺利，大部分学生能够正确操作', '摆长测量精度有待提高', '建议使用更精确的测量工具', 2, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(3, 3, 13, 3, 5, 10, '九年级2班', 40, '2025-07-16 14:00:00', '2025-07-16 15:30:00', 92.00, 5, NULL, NULL, '实验效果良好，学生对氧气性质有了直观认识', '个别学生操作不够规范', '需要加强安全操作培训', 2, '2025-07-18 14:40:20', '2025-07-18 14:40:20');

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
(1, 12, 1, 1, 10, '八年级1班', 45, '2025-07-20', '08:00:00', '09:30:00', 2, '测量物体长度实验', 8, '2025-07-18 14:40:20', '审核通过', '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(2, 12, 2, 1, 10, '九年级1班', 42, '2025-07-21', '10:00:00', '11:30:00', 2, '重力加速度测量', 8, '2025-07-18 14:40:20', '审核通过', '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(3, 13, 3, 5, 10, '九年级2班', 40, '2025-07-22', '14:00:00', '15:30:00', 2, '氧气制取实验', 8, '2025-07-18 14:40:20', '审核通过', '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(4, 14, 4, 11, 10, '七年级1班', 38, '2025-07-23', '09:00:00', '10:30:00', 2, '观察植物细胞', 8, '2025-07-18 14:40:20', '审核通过', '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(5, 15, 5, 4, 10, '高一1班', 35, '2025-07-24', '15:00:00', '16:30:00', 1, '验证牛顿第二定律', NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20');

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
(1, 12, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(2, 12, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(3, 12, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(4, 13, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(5, 13, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(6, 13, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(7, 13, '物理实验室2', 'PHY_LAB_02', 1, NULL, '教学楼4楼401室', 125.00, 52, NULL, '高级力学实验台、电磁学实验台、现代物理实验设备等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(8, 13, '综合实验室', 'COM_LAB_01', 4, NULL, '教学楼4楼402室', 140.00, 60, NULL, '多媒体设备、计算机、通用实验台等', '1.爱护设备\\n2.按规定使用\\n3.保持整洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(9, 14, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(10, 14, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(11, 14, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(12, 15, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(13, 15, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(14, 15, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(15, 9, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(16, 9, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(17, 9, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(18, 10, '物理实验室1', 'PHY_LAB_01', 1, NULL, '教学楼3楼301室', 120.00, 50, NULL, '力学实验台、电学实验台、光学实验台等', '1.进入实验室必须穿实验服\\n2.严禁携带易燃易爆物品\\n3.实验结束后整理器材', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(19, 10, '化学实验室1', 'CHE_LAB_01', 2, NULL, '教学楼3楼302室', 130.00, 48, NULL, '通风橱、实验台、试剂柜、洗眼器等', '1.必须穿实验服和护目镜\\n2.严格按照实验步骤操作\\n3.注意通风和防火', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41'),
(20, 10, '生物实验室1', 'BIO_LAB_01', 3, NULL, '教学楼3楼303室', 110.00, 45, NULL, '显微镜、解剖台、标本柜、培养箱等', '1.爱护显微镜等精密仪器\\n2.正确处理生物标本\\n3.保持实验室清洁', 1, '2025-07-18 04:08:41', '2025-07-18 04:08:41');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_17_000001_create_laboratory_types_table', 1),
(5, '2025_01_17_000002_create_equipment_standards_table', 1),
(6, '2025_07_13_095051_create_administrative_regions_table', 1),
(7, '2025_07_13_095149_create_schools_table', 1),
(8, '2025_07_13_095215_create_roles_table', 1),
(9, '2025_07_13_095239_create_user_roles_table', 1),
(10, '2025_07_13_100001_create_subjects_table', 1),
(11, '2025_07_13_100002_create_experiment_catalogs_table', 1),
(12, '2025_07_13_100003_create_laboratories_table', 1),
(13, '2025_07_13_100004_create_experiment_reservations_table', 1),
(14, '2025_07_13_100005_create_experiment_records_table', 1),
(15, '2025_07_13_100006_create_equipment_categories_table', 1),
(16, '2025_07_13_100007_create_equipments_table', 1),
(17, '2025_07_13_100008_create_equipment_borrows_table', 1),
(18, '2025_07_13_100009_create_equipment_maintenances_table', 1),
(19, '2025_07_13_100010_create_equipment_qrcodes_table', 1),
(20, '2025_07_13_100010_create_statistics_summary_table', 1),
(21, '2025_07_13_100011_create_equipment_operation_logs_table', 1),
(22, '2025_07_13_100011_create_system_configs_table', 1),
(23, '2025_07_13_100012_create_equipment_attachments_table', 1),
(24, '2025_07_13_100012_create_operation_logs_table', 1),
(25, '2025_07_13_100013_add_type_id_to_laboratories_table', 1),
(26, '2025_07_13_100014_update_laboratories_type_field', 1),
(27, '2025_07_14_000001_add_user_management_fields_to_users_table', 1),
(28, '2025_07_14_034529_create_role_permissions_table', 1),
(29, '2025_07_15_000001_add_organization_fields_to_users_table', 1),
(30, '2025_07_17_020000_add_contact_fields_to_administrative_regions_table', 1);

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
(1, '省级管理员', 'province_admin', 1, '省级系统管理员，拥有全省数据管理权限', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(2, '省级教研员', 'province_researcher', 1, '省级教研员，负责全省实验教学研究和指导', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(3, '市级管理员', 'city_admin', 2, '市级系统管理员，管理本市实验教学数据', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(4, '市级教研员', 'city_researcher', 2, '市级教研员，负责本市实验教学研究和指导', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(5, '区县管理员', 'county_admin', 3, '区县级系统管理员，管理本区县实验教学数据', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(6, '区县教研员', 'county_researcher', 3, '区县级教研员，负责本区县实验教学研究和指导', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(7, '学区管理员', 'district_admin', 4, '学区管理员，管理学区内学校实验教学数据', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(8, '校长', 'school_principal', 5, '学校校长，拥有本校所有数据查看权限', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(9, '教务主任', 'school_dean', 5, '教务主任，负责学校实验教学管理', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(10, '实验员', 'school_experimenter', 5, '实验员，负责实验室管理和实验教学记录', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22'),
(11, '任课教师', 'school_teacher', 5, '任课教师，可查看和录入实验教学数据', 1, '2025-07-18 04:04:22', '2025-07-18 04:04:22');

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
(1, 1, 'user', '2025-07-18 04:08:53', '2025-07-18 04:08:53'),
(2, 1, 'user.list', '2025-07-18 04:08:53', '2025-07-18 04:08:53'),
(3, 1, 'user.create', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(4, 1, 'user.update', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(5, 1, 'user.delete', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(6, 1, 'user.edit', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(7, 1, 'user.export', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(8, 1, 'user.reset_password', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(9, 1, 'role', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(10, 1, 'role.list', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(11, 1, 'role.create', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(12, 1, 'role.update', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(13, 1, 'role.delete', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(14, 1, 'experiment', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(15, 1, 'experiment.catalog', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(16, 1, 'experiment.booking', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(17, 1, 'experiment.record', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(18, 1, 'equipment', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(19, 1, 'equipment.list', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(20, 1, 'equipment.create', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(21, 1, 'equipment.update', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(22, 1, 'equipment.delete', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(23, 1, 'equipment.borrow', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(24, 1, 'equipment.maintenance', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(25, 1, 'laboratory_type', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(26, 1, 'laboratory_type.list', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(27, 1, 'laboratory_type.create', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(28, 1, 'laboratory_type.update', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(29, 1, 'laboratory_type.delete', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(30, 1, 'equipment_standard', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(31, 1, 'equipment_standard.list', '2025-07-18 04:08:54', '2025-07-18 04:08:54'),
(32, 1, 'equipment_standard.create', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(33, 1, 'equipment_standard.update', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(34, 1, 'equipment_standard.delete', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(35, 1, 'statistics.view', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(36, 1, 'statistics.dashboard', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(37, 1, 'statistics.experiment', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(38, 1, 'statistics.equipment', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(39, 1, 'statistics.user', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(40, 1, 'statistics.performance', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(41, 1, 'statistics.export', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(42, 1, 'system', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(43, 1, 'system.read', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(44, 1, 'log', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(45, 1, 'log.read', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(46, 2, 'experiment', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(47, 2, 'experiment.catalog', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(48, 2, 'experiment.booking', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(49, 2, 'experiment.record', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(50, 2, 'equipment', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(51, 2, 'equipment.list', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(52, 2, 'laboratory_type', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(53, 2, 'laboratory_type.list', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(54, 2, 'equipment_standard', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(55, 2, 'equipment_standard.list', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(56, 3, 'user', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(57, 3, 'user.list', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(58, 3, 'user.create', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(59, 3, 'user.update', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(60, 3, 'user.delete', '2025-07-18 04:08:55', '2025-07-18 04:08:55'),
(61, 3, 'user.edit', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(62, 3, 'user.export', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(63, 3, 'user.reset_password', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(64, 3, 'role', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(65, 3, 'role.list', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(66, 3, 'role.create', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(67, 3, 'role.update', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(68, 3, 'role.delete', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(69, 3, 'experiment', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(70, 3, 'experiment.catalog', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(71, 3, 'experiment.booking', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(72, 3, 'experiment.record', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(73, 3, 'equipment', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(74, 3, 'equipment.list', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(75, 3, 'equipment.create', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(76, 3, 'equipment.update', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(77, 3, 'equipment.delete', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(78, 3, 'equipment.borrow', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(79, 3, 'equipment.maintenance', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(80, 3, 'laboratory_type', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(81, 3, 'laboratory_type.list', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(82, 3, 'laboratory_type.create', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(83, 3, 'laboratory_type.update', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(84, 3, 'laboratory_type.delete', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(85, 3, 'equipment_standard', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(86, 3, 'equipment_standard.list', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(87, 3, 'equipment_standard.create', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(88, 3, 'equipment_standard.update', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(89, 3, 'equipment_standard.delete', '2025-07-18 04:08:56', '2025-07-18 04:08:56'),
(90, 3, 'statistics', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(91, 3, 'statistics.view', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(92, 3, 'statistics.dashboard', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(93, 3, 'statistics.experiment', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(94, 3, 'statistics.equipment', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(95, 3, 'statistics.user', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(96, 3, 'statistics.performance', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(97, 3, 'statistics.export', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(98, 4, 'experiment', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(99, 4, 'experiment.catalog', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(100, 4, 'experiment.booking', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(101, 4, 'experiment.record', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(102, 4, 'equipment', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(103, 4, 'equipment.list', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(104, 4, 'laboratory_type', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(105, 4, 'laboratory_type.list', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(106, 4, 'equipment_standard', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(107, 4, 'equipment_standard.list', '2025-07-18 04:08:57', '2025-07-18 04:08:57'),
(108, 5, 'user', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(109, 5, 'user.list', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(110, 5, 'user.create', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(111, 5, 'user.update', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(112, 5, 'user.delete', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(113, 5, 'user.edit', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(114, 5, 'user.export', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(115, 5, 'user.reset_password', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(116, 5, 'role', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(117, 5, 'role.list', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(118, 5, 'role.create', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(119, 5, 'role.update', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(120, 5, 'role.delete', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(121, 5, 'experiment', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(122, 5, 'experiment.catalog', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(123, 5, 'experiment.booking', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(124, 5, 'experiment.record', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(125, 5, 'equipment', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(126, 5, 'equipment.list', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(127, 5, 'equipment.create', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(128, 5, 'equipment.update', '2025-07-18 04:08:58', '2025-07-18 04:08:58'),
(129, 5, 'equipment.delete', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(130, 5, 'equipment.borrow', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(131, 5, 'equipment.maintenance', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(132, 5, 'laboratory_type', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(133, 5, 'laboratory_type.list', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(134, 5, 'laboratory_type.create', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(135, 5, 'laboratory_type.update', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(136, 5, 'laboratory_type.delete', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(137, 5, 'equipment_standard', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(138, 5, 'equipment_standard.list', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(139, 5, 'equipment_standard.create', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(140, 5, 'equipment_standard.update', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(141, 5, 'equipment_standard.delete', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(142, 5, 'statistics', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(143, 5, 'statistics.view', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(144, 5, 'statistics.dashboard', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(145, 5, 'statistics.experiment', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(146, 5, 'statistics.equipment', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(147, 5, 'statistics.user', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(148, 5, 'statistics.performance', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(149, 5, 'statistics.export', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(150, 6, 'experiment', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(151, 6, 'experiment.catalog', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(152, 6, 'experiment.booking', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(153, 6, 'experiment.record', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(154, 6, 'equipment', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(155, 6, 'equipment.list', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(156, 6, 'laboratory_type', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(157, 6, 'laboratory_type.list', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(158, 6, 'equipment_standard', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(159, 6, 'equipment_standard.list', '2025-07-18 04:08:59', '2025-07-18 04:08:59'),
(160, 7, 'user', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(161, 7, 'user.list', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(162, 7, 'user.create', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(163, 7, 'user.update', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(164, 7, 'experiment', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(165, 7, 'experiment.catalog', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(166, 7, 'experiment.booking', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(167, 7, 'experiment.record', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(168, 7, 'equipment', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(169, 7, 'equipment.list', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(170, 7, 'equipment.create', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(171, 7, 'equipment.update', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(172, 7, 'equipment.borrow', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(173, 7, 'equipment.maintenance', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(174, 7, 'laboratory_type', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(175, 7, 'laboratory_type.list', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(176, 7, 'equipment_standard', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(177, 7, 'equipment_standard.list', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(178, 7, 'statistics', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(179, 7, 'statistics.view', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(180, 7, 'statistics.dashboard', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(181, 7, 'statistics.experiment', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(182, 7, 'statistics.equipment', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(183, 7, 'statistics.user', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(184, 7, 'statistics.performance', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(185, 8, 'experiment', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(186, 8, 'experiment.catalog', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(187, 8, 'experiment.booking', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(188, 8, 'experiment.record', '2025-07-18 04:09:00', '2025-07-18 04:09:00'),
(189, 8, 'equipment', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(190, 8, 'equipment.list', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(191, 8, 'laboratory_type', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(192, 8, 'laboratory_type.list', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(193, 8, 'equipment_standard', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(194, 8, 'equipment_standard.list', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(195, 9, 'experiment', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(196, 9, 'experiment.catalog', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(197, 9, 'experiment.booking', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(198, 9, 'experiment.record', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(199, 9, 'equipment', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(200, 9, 'equipment.list', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(201, 9, 'equipment.borrow', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(202, 9, 'equipment.maintenance', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(203, 9, 'laboratory_type', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(204, 9, 'laboratory_type.list', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(205, 9, 'equipment_standard', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(206, 9, 'equipment_standard.list', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(207, 10, 'experiment', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(208, 10, 'experiment.catalog', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(209, 10, 'experiment.booking', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(210, 10, 'experiment.record', '2025-07-18 04:09:01', '2025-07-18 04:09:01'),
(211, 10, 'equipment', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(212, 10, 'equipment.list', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(213, 10, 'equipment.borrow', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(214, 10, 'equipment.maintenance', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(215, 10, 'laboratory_type', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(216, 10, 'laboratory_type.list', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(217, 10, 'equipment_standard', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(218, 10, 'equipment_standard.list', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(219, 11, 'experiment', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(220, 11, 'experiment.catalog', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(221, 11, 'experiment.booking', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(222, 11, 'experiment.record', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(223, 11, 'equipment', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(224, 11, 'equipment.list', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(225, 11, 'equipment.borrow', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(226, 11, 'laboratory_type', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(227, 11, 'laboratory_type.list', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(228, 11, 'equipment_standard', '2025-07-18 04:09:02', '2025-07-18 04:09:02'),
(229, 11, 'equipment_standard.list', '2025-07-18 04:09:02', '2025-07-18 04:09:02');

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
(1, 'SJZJY001', '石家庄精英中学', 3, 1, 1, '石家庄市高新区学苑路25号', '李校长', '0311-80790000', 3200, 60, 280, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(2, 'HSZX001', '衡水中学', 3, 1, 1, '衡水市桃城区英才路228号', '郗校长', '0318-2126000', 4500, 90, 350, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(3, 'BD7Z001', '保定七中', 3, 1, 1, '保定市莲池区五四东路742号', '王校长', '0312-5079000', 2800, 54, 220, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(4, 'XT1Z001', '邢台一中', 3, 1, 1, '邢台市桥东区泉北东大街88号', '张校长', '0319-2235000', 3500, 70, 290, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(5, 'SJZ1Z001', '石家庄市第一中学', 3, 2, 2, '石家庄市长安区范西路9号', '刘校长', '0311-86061000', 3000, 60, 250, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(6, 'SJZ2Z001', '石家庄市第二中学', 3, 2, 2, '石家庄市长安区南二环东路20号', '陈校长', '0311-87060000', 2900, 58, 240, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(7, 'SJZWY001', '石家庄外国语学校', 3, 2, 2, '石家庄市裕华区育才街318号', '马校长', '0311-85270000', 2200, 44, 200, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(8, 'SJZ42001', '石家庄市第四十二中学', 2, 2, 2, '石家庄市新华区北二环西路88号', '周校长', '0311-87050000', 1800, 36, 150, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(9, 'GC1Z001', '藁城区第一中学', 3, 3, 6, '藁城区廉州西路88号', '杨校长', '0311-88040000', 2500, 50, 200, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(10, 'GCSYXX001', '藁城区实验学校', 2, 3, 6, '藁城区廉州东路168号', '郭校长', '0311-88041000', 1500, 30, 120, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(11, 'GCZJZX001', '藁城区职业技术教育中心', 3, 3, 6, '藁城区工业路99号', '何校长', '0311-88042000', 2000, 40, 180, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(12, 'LZDCXX001', '廉州东城小学', 1, 4, 11, '藁城区廉州镇东城村', '许校长', '0311-88043000', 800, 18, 45, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(13, 'LZXCXX001', '廉州西城小学', 1, 4, 11, '藁城区廉州镇西城村', '田校长', '0311-88043100', 750, 16, 42, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(14, 'LZZXXX001', '廉州中心小学', 1, 4, 11, '藁城区廉州镇中心街88号', '孙校长', '0311-88043200', 900, 20, 50, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(15, 'LZZX001', '廉州中学', 2, 4, 11, '藁城区廉州镇学府路66号', '赵校长', '0311-88043300', 1200, 24, 80, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20');

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
(1, '数学', 'MATH', 0, 1, 1, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(2, '物理', 'PHYSICS', 0, 2, 2, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(3, '化学', 'CHEMISTRY', 0, 2, 3, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(4, '生物', 'BIOLOGY', 0, 2, 4, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(5, '高中物理', 'HIGH_PHYSICS', 0, 3, 5, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(6, '高中化学', 'HIGH_CHEMISTRY', 0, 3, 6, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(7, '高中生物', 'HIGH_BIOLOGY', 0, 3, 7, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(8, '科学', 'SCIENCE', 0, 1, 8, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(9, '信息技术', 'IT', 0, 1, 9, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(10, '通用技术', 'GENERAL_TECH', 0, 3, 10, 1, '2025-07-18 14:40:20', '2025-07-18 14:40:20');

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
(1, 'province_admin_test', 'province@test.com', '13800000001', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '省级管理员', NULL, NULL, NULL, NULL, 1, 'province_admin', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '2025-07-18 07:05:50', NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 07:05:50'),
(2, 'province_researcher', 'researcher@test.com', '13800000002', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '省级教研员', NULL, NULL, NULL, NULL, 1, 'province_researcher', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(3, 'city_admin_test', 'city@test.com', '13800000003', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '市级管理员', NULL, NULL, NULL, NULL, 1, 'city_admin', NULL, NULL, NULL, NULL, NULL, 2, NULL, 2, '2025-07-18 07:07:55', NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 07:07:55'),
(4, 'city_researcher', 'cityres@test.com', '13800000004', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '市级教研员', NULL, NULL, NULL, NULL, 1, 'city_researcher', NULL, NULL, NULL, NULL, NULL, 2, NULL, 2, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(5, 'county_admin_test', 'county@test.com', '13800000005', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '区县管理员', NULL, NULL, NULL, NULL, 1, 'county_admin', NULL, NULL, NULL, NULL, NULL, 6, NULL, 3, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(6, 'county_researcher', 'countyres@test.com', '13800000006', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '区县教研员', NULL, NULL, NULL, NULL, 1, 'county_researcher', NULL, NULL, NULL, NULL, NULL, 6, NULL, 3, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(7, 'district_admin_test', 'district@test.com', '13800000007', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '学区管理员', NULL, NULL, NULL, NULL, 1, 'district_admin', NULL, NULL, NULL, NULL, NULL, 11, NULL, 4, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(8, 'school_admin_test', 'school@test.com', '13800000008', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '学校管理员', NULL, NULL, NULL, NULL, 1, 'school_admin', NULL, NULL, NULL, 12, NULL, 11, NULL, 4, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(9, 'school_principal', 'principal@test.com', '13800000009', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '校长', NULL, NULL, NULL, NULL, 1, 'school_principal', NULL, NULL, NULL, 12, NULL, 11, NULL, 4, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(10, 'school_teacher', 'teacher@test.com', '13800000010', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '实验教师', NULL, NULL, NULL, NULL, 1, 'school_teacher', NULL, NULL, NULL, 12, NULL, 11, NULL, 4, NULL, NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(11, 'admin', 'admin@test.com', '13800000000', '$2y$10$qxvHPQcw.YK/PCBuz2MV8eVVtke9iSNs92oiNrnslliMaSXS67Zp.', '系统管理员', NULL, NULL, NULL, NULL, 1, 'super_admin', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '2025-07-18 07:07:15', NULL, NULL, '2025-07-18 14:40:20', '2025-07-18 07:07:15');

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
(1, 1, 1, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(2, 2, 2, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(3, 3, 3, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(4, 4, 4, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(5, 5, 5, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(6, 6, 6, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(7, 7, 7, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(8, 8, 9, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(9, 9, 8, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(10, 10, 11, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20'),
(11, 11, 1, '', 0, '2025-07-18 14:40:20', '2025-07-18 14:40:20');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `experiment_records`
--
ALTER TABLE `experiment_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `experiment_reservations`
--
ALTER TABLE `experiment_reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 使用表AUTO_INCREMENT `operation_logs`
--
ALTER TABLE `operation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- 使用表AUTO_INCREMENT `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用表AUTO_INCREMENT `statistics_summary`
--
ALTER TABLE `statistics_summary`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `system_configs`
--
ALTER TABLE `system_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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

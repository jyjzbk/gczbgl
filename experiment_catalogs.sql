-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-07-23 03:48:13
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `experiment_catalogs`
--

INSERT INTO `experiment_catalogs` (`id`, `subject_id`, `textbook_version_id`, `chapter_id`, `grade_level`, `volume`, `management_level`, `experiment_type`, `parent_catalog_id`, `original_catalog_id`, `version`, `is_deleted_by_lower`, `delete_reason`, `created_by_level`, `created_by_org_id`, `created_by_org_type`, `name`, `code`, `type`, `grade`, `semester`, `chapter`, `duration`, `student_count`, `objective`, `materials`, `procedure`, `safety_notes`, `difficulty_level`, `is_standard`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 2, '8', '上册', 1, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '测量物体的长度', 'MP_001', 4, 8, 1, '第一章 机械运动', 45, 2, '学会使用刻度尺测量物体长度，掌握测量的基本方法', '刻度尺、铅笔、硬币、细线等', '1.观察刻度尺的结构\\n2.学习正确的测量方法\\n3.测量不同物体的长度\\n4.记录测量结果', '使用刻度尺时要轻拿轻放，避免弯折', 1, 1, 1, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(2, 2, 1, 2, '9', '上册', 2, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '测量重力加速度', 'MP_002', 4, 9, 1, '第十三章 力和机械', 90, 4, '通过实验测量重力加速度的大小', '单摆装置、秒表、刻度尺、小球等', '1.组装单摆装置\\n2.测量摆长\\n3.测量周期\\n4.计算重力加速度', '注意摆球的安全，避免碰撞', 3, 1, 1, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(3, 3, 1, 2, '9', '上册', 3, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '氧气的制取和性质', 'MC_001', 3, 9, 1, '第二单元 我们周围的空气', 45, 1, '学习氧气的制取方法，观察氧气的性质', '高锰酸钾、试管、酒精灯、导管、集气瓶等', '1.装置连接\\n2.加热制取氧气\\n3.收集氧气\\n4.验证氧气性质', '注意用火安全，避免烫伤；注意通风', 2, 1, 1, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(4, 4, 1, 3, '7', '上册', 4, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察植物细胞', 'MB_001', 4, 7, 1, '第二单元 生物体的结构层次', 45, 2, '学会制作临时装片，观察植物细胞的基本结构', '显微镜、载玻片、盖玻片、洋葱、碘液等', '1.制作洋葱表皮临时装片\\n2.显微镜观察\\n3.绘制细胞结构图\\n4.总结细胞特点', '小心使用显微镜，避免损坏镜头', 2, 1, 1, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(5, 5, 1, 3, '10', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '验证牛顿第二定律', 'HP_001', 4, 10, 2, '第四章 牛顿运动定律', 90, 4, '通过实验验证牛顿第二定律F=ma', '气垫导轨、滑块、砝码、光电门、计时器等', '1.调节气垫导轨水平\\n2.测量不同力下的加速度\\n3.测量不同质量下的加速度\\n4.分析数据验证定律', '注意气垫导轨的使用，避免损坏设备', 4, 1, 1, '2025-07-18 15:17:56', '2025-07-21 19:31:52'),
(19, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察物体', 'JKB-1X-01', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过观察物体实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等', '1. 准备实验器材：玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等\n2. 按照教材第3页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:31', '2025-07-21 18:19:15'),
(20, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '探轻重排序', 'JKB-1X-02', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过探轻重排序实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '乒乓球、木块、塑料块、小橡皮、大橡皮、天平、回形针', '1. 准备实验器材：乒乓球、木块、塑料块、小橡皮、大橡皮、天平、回形针\n2. 按照教材第6页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(21, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '平描物体', 'JKB-1X-03', 3, 1, 2, '第一单元 我们周围的物体', 45, 1, '通过平描物体实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '乒乓球、木块、橡皮、蜡笔、方盒子', '1. 准备实验器材：乒乓球、木块、橡皮、蜡笔、方盒子\n2. 按照教材第9页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(22, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '给物体分类', 'JKB-1X-04', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过给物体分类实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等', '1. 准备实验器材：玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等\n2. 按照教材第11页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(23, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察水和洗发液', 'JKB-1X-05', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过观察水和洗发液实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '水、洗发液', '1. 准备实验器材：水、洗发液\n2. 按照教材第14页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(24, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '溶解实验', 'JKB-1X-06', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过溶解实验实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '水、红糖、食盐、小石子、小木根、小勺、烧杯', '1. 准备实验器材：水、红糖、食盐、小石子、小木根、小勺、烧杯\n2. 按照教材第17页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(25, 1, 7, 5, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察空气', 'JKB-1X-07', 4, 1, 2, '第一单元 我们周围的物体', 45, 4, '通过观察空气实验，培养学生的观察能力和科学探究精神，了解我们周围的物体相关知识。', '空气、水、木块、塑料袋', '1. 准备实验器材：空气、水、木块、塑料袋\n2. 按照教材第20页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(26, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察动物', 'JKB-1X-08', 3, 1, 2, '第二单元 动物', 45, 1, '通过观察动物实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '动物图片或标本等', '1. 准备实验器材：动物图片或标本等\n2. 按照教材第25页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(27, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '寻找小动物', 'JKB-1X-09', 4, 1, 2, '第二单元 动物', 45, 4, '通过寻找小动物实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '放大镜、镊子', '1. 准备实验器材：放大镜、镊子\n2. 按照教材第27页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(28, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察蜗牛', 'JKB-1X-10', 4, 1, 2, '第二单元 动物', 45, 4, '通过观察蜗牛实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '蜗牛、放大镜、玻璃', '1. 准备实验器材：蜗牛、放大镜、玻璃\n2. 按照教材第33页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(29, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '饲养蜗牛', 'JKB-1X-11', 3, 1, 2, '第二单元 动物', 45, 1, '通过饲养蜗牛实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '蜗牛、饲养箱、菜叶', '1. 准备实验器材：蜗牛、饲养箱、菜叶\n2. 按照教材第37页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(30, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '观察鱼', 'JKB-1X-12', 3, 1, 2, '第二单元 动物', 45, 1, '通过观察鱼实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '鱼缸、鱼、图片', '1. 准备实验器材：鱼缸、鱼、图片\n2. 按照教材第38页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15'),
(31, 1, 7, 6, '1', '下册', 5, '必做', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '给动物分类', 'JKB-1X-13', 4, 1, 2, '第二单元 动物', 45, 4, '通过给动物分类实验，培养学生的观察能力和科学探究精神，了解动物相关知识。', '动物卡片', '1. 准备实验器材：动物卡片\n2. 按照教材第40页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材', '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。', 1, 1, 1, '2025-07-20 23:42:32', '2025-07-21 18:19:15');

--
-- 转储表的索引
--

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
  ADD KEY `experiment_catalogs_management_level_index` (`management_level`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 限制导出的表
--

--
-- 限制表 `experiment_catalogs`
--
ALTER TABLE `experiment_catalogs`
  ADD CONSTRAINT `experiment_catalogs_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

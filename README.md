# 中小学实验教学管理平台 (GCZBGL)

> 完整实现五级组织架构管理和权限控制系统

## 📋 项目概述

实验教学管理平台是一个面向中小学的实验教学管理系统，已实现完整的五级组织架构（省→市→区县→学区→学校）和基于组织层级的权限控制系统。本项目采用前后端分离架构，提供完整的组织管理、用户管理、实验管理和设备管理功能。

## 🎯 项目特色

- **完整的组织架构**：实现真实的教育系统五级组织结构
- **精确的权限控制**：基于组织层级的递归权限计算
- **自动化权限过滤**：中间件自动应用数据权限
- **完整的业务闭环**：从实验管理到设备管理的完整流程

## ✨ 已完成功能

### 🏢 组织架构管理
- **五级组织结构** - 省→市→区县→学区→学校完整层级
- **权限控制系统** - 基于组织层级的数据权限控制
- **向下管理机制** - 高级组织可管理所有下级组织数据

### 🔐 用户管理模块
- **用户列表管理** - 用户CRUD操作、角色分配、状态管理
- **角色管理** - 系统角色、权限统计、角色编辑
- **权限管理** - 树形权限配置、实时保存、权限同步
- **组织归属** - 用户组织归属管理

## 🛠️ 技术栈

### 前端
- **Vue 3.4+** + TypeScript
- **Element Plus** - UI组件库
- **Vue Router** - 路由管理
- **Pinia** - 状态管理
- **Axios** - HTTP客户端
- **Vite** - 构建工具
- **ESLint + Prettier** - 代码规范

### 后端
- **Laravel 12** - PHP 8.2框架
- **MySQL 8.0 / MariaDB 10.4** - 数据库
- **JWT** - 身份认证
- **RESTful API** - 25个接口
- **RBAC + 组织层级权限** - 权限控制

## 🚀 快速开始

### 环境要求
- Node.js 18+
- PHP 8.2+
- MySQL 8.0+
- Composer 2.0+

### 安装步骤

1. **克隆项目**
```bash
git clone https://github.com/your-username/gczbgl.git
cd gczbgl
```

2. **后端设置**
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

3. **前端设置**
```bash
cd frontend
npm install
npm run dev
```

4. **访问应用**
- 前端：http://localhost:3000
- 后端：http://localhost:8000

## 🧪 测试账号

| 用户名 | 密码 | 角色 | 组织 | 权限范围 |
|--------|------|------|------|----------|
| province_admin_test | password | 省级管理员 | 河北省教育厅 | 全省数据 |
| city_admin_test | password | 市级管理员 | 石家庄市教育局 | 本市及下级 |
| county_admin_test | password | 区县管理员 | 藁城区 | 本区县及下级 |
| district_admin_test | password | 学区管理员 | 廉州学区 | 本学区学校 |
| school_admin_test | password | 学校管理员 | 廉州东城小学 | 本校数据 |

## 🔍 权限测试

### 运行权限测试脚本
```bash
cd backend
# 测试组织权限控制
php test_organization_permissions.php
# 测试API权限控制
php test_organization_api.php
```

### 预期测试结果
- ✅ 省级管理员：可访问15所学校，20台设备
- ✅ 市级管理员：可访问11所学校，15台设备
- ✅ 区县管理员：可访问7所学校，10台设备
- ✅ 学区管理员：可访问4所学校，5台设备
- ✅ 学校管理员：可访问1所学校，5台设备

## 📁 项目结构

```
gczbgl/
├── frontend/                 # Vue 3 前端项目
│   ├── src/
│   │   ├── views/user/      # 用户管理页面
│   │   ├── api/             # API接口
│   │   ├── components/      # 公共组件
│   │   └── router/          # 路由配置
│   └── package.json
├── backend/                  # Laravel 后端项目
│   ├── app/
│   │   ├── Http/Controllers/Api/  # API控制器
│   │   ├── Models/          # 数据模型
│   │   └── Middleware/      # 中间件
│   ├── database/
│   │   ├── migrations/      # 数据库迁移
│   │   └── seeders/         # 数据填充
│   └── routes/api.php       # API路由
├── docs/                     # 项目文档
└── README.md
```

## 🎯 功能特性

### 用户管理
- ✅ 用户增删改查
- ✅ 角色分配与权限控制
- ✅ 用户状态管理
- ✅ 搜索与筛选
- ✅ 分页显示

### 角色管理
- ✅ 12种系统角色
- ✅ 角色权限统计
- ✅ 角色信息编辑
- ✅ 权限配置跳转

### 权限管理
- ✅ 树形权限展示
- ✅ 角色权限配置
- ✅ 实时保存与同步
- ✅ 默认权限模板

## 📊 系统角色

| 角色 | 级别 | 权限范围 |
|------|------|----------|
| 省级管理员 | 1 | 全省数据管理 |
| 省级教研员 | 1 | 全省教研指导 |
| 市级管理员 | 2 | 本市数据管理 |
| 市级教研员 | 2 | 本市教研指导 |
| 区县管理员 | 3 | 本区县数据管理 |
| 区县教研员 | 3 | 本区县教研指导 |
| 学区管理员 | 4 | 学区数据管理 |
| 校长 | 5 | 本校全部数据 |
| 教务主任 | 5 | 教学管理 |
| 实验员 | 5 | 实验室管理 |
| 任课教师 | 5 | 实验教学 |
| 学生 | 5 | 基础查看 |

## 📖 文档

- [系统总体设计方案](docs/01-系统总体设计方案.md)
- [数据库设计方案](docs/02-数据库设计方案.md)
- [API接口设计文档](docs/04-API接口设计文档.md)
- [前端组件设计文档](docs/05-前端组件设计文档.md)
- [问题记录与解决方案](docs/08-用户管理模块-问题记录与解决方案.md)
- [用户管理模块完成总结](docs/09-用户管理模块-完成总结.md)

## 🔄 开发进度

- [x] **用户管理模块** (已完成)
  - [x] 用户列表管理
  - [x] 角色管理
  - [x] 权限管理
- [ ] **实验管理模块** (计划中)
- [ ] **设备管理模块** (计划中)
- [ ] **统计报表模块** (计划中)
- [ ] **移动端应用** (计划中)

## 🤝 贡献指南

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 打开 Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情

## 📞 联系方式

- 项目地址：https://github.com/your-username/gczbgl
- 问题反馈：https://github.com/your-username/gczbgl/issues

---

**开发状态**：用户管理模块已完成 ✅  
**最后更新**：2025-07-14

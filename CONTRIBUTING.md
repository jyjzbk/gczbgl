# 贡献指南

感谢您对实验教学管理平台项目的关注！我们欢迎任何形式的贡献。

## 🤝 如何贡献

### 报告问题
- 使用 [GitHub Issues](https://github.com/your-username/gczbgl/issues) 报告bug
- 提供详细的问题描述和复现步骤
- 包含系统环境信息（操作系统、浏览器版本等）

### 提交代码
1. Fork 本仓库
2. 创建特性分支：`git checkout -b feature/amazing-feature`
3. 提交更改：`git commit -m 'Add some amazing feature'`
4. 推送到分支：`git push origin feature/amazing-feature`
5. 创建 Pull Request

### 代码规范
- **前端**：遵循 ESLint + Prettier 配置
- **后端**：遵循 PSR-12 PHP 编码标准
- **提交信息**：使用清晰的提交信息，格式：`类型: 简短描述`

## 🛠️ 开发环境设置

### 环境要求
- Node.js 18+
- PHP 8.2+
- MySQL 8.0+
- Composer 2.0+

### 安装步骤
```bash
# 克隆项目
git clone https://github.com/your-username/gczbgl.git
cd gczbgl

# 后端设置
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# 前端设置
cd ../frontend
npm install
npm run dev
```

## 📋 开发规范

### 分支管理
- `main` - 主分支，稳定版本
- `develop` - 开发分支
- `feature/*` - 功能分支
- `hotfix/*` - 热修复分支

### 测试要求
- 新功能必须包含相应的测试用例
- 确保所有测试通过后再提交PR
- 前端测试：`npm run test`
- 后端测试：`php artisan test`

### 文档更新
- 新功能需要更新相关文档
- API变更需要更新接口文档
- 重要变更需要更新CHANGELOG.md

## 🎯 项目结构

```
gczbgl/
├── frontend/          # Vue 3前端项目
├── backend/           # Laravel后端项目
├── docs/             # 项目文档
└── README.md         # 项目说明
```

## 📞 联系方式

- 项目地址：https://github.com/your-username/gczbgl
- 问题反馈：https://github.com/your-username/gczbgl/issues

感谢您的贡献！🎉

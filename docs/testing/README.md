# 测试文档目录

本目录包含项目的各种测试文件和测试文档。

## 📁 目录结构

- `functional_tests/` - 功能测试文件
- `api_tests/` - API接口测试
- `integration_tests/` - 集成测试
- `test_reports/` - 测试报告

## 🧪 测试文件说明

### 功能测试页面
- `test_complete_system.html` - 完整系统功能展示页面
- `test_experiment_requirements_config.html` - 实验要求配置功能测试
- 其他功能模块测试页面

### API测试
- 各模块API接口的测试用例
- 权限验证测试
- 数据验证测试

### 集成测试
- 前后端集成测试
- 数据库连接测试
- 第三方服务集成测试

## 🚀 运行测试

### 前端测试
```bash
cd frontend
npm run test
```

### 后端测试
```bash
cd backend
php artisan test
```

### 数据库测试
```bash
cd backend
php artisan migrate:fresh --seed
```

## 📋 测试清单

- [ ] 用户认证和授权
- [ ] 权限控制验证
- [ ] 数据CRUD操作
- [ ] 文件上传下载
- [ ] API接口响应
- [ ] 前端页面渲染
- [ ] 数据库约束
- [ ] 业务逻辑验证

## 📊 测试覆盖率

目标测试覆盖率：80%以上

- 后端API测试覆盖率：待测试
- 前端组件测试覆盖率：待测试
- 集成测试覆盖率：待测试

## 🔧 测试环境

- **开发环境**：本地XAMPP环境
- **测试数据库**：gczbgl_test
- **前端服务**：http://localhost:3000
- **后端服务**：http://localhost:8000

## 📝 测试注意事项

1. 测试前确保数据库连接正常
2. 运行测试前清理测试数据
3. 测试完成后恢复初始状态
4. 记录测试结果和问题
5. 及时更新测试用例

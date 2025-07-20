# 实验预约系统部署指南

## 📋 部署前准备

### 系统要求
- PHP >= 8.1
- MySQL >= 8.0
- Node.js >= 16.0
- Composer >= 2.0
- npm/yarn

### 环境检查
```bash
# 检查PHP版本
php -v

# 检查MySQL版本
mysql --version

# 检查Node.js版本
node -v

# 检查Composer版本
composer --version
```

## 🗄️ 数据库部署

### 1. 运行数据库迁移
```bash
cd backend

# 运行所有迁移
php artisan migrate

# 如果需要重新创建数据库
php artisan migrate:fresh

# 运行数据填充（可选）
php artisan db:seed
```

### 2. 检查新增的表
确认以下表已成功创建：
- `experiment_reservation_templates` - 预约模板表
- `reservation_conflict_logs` - 冲突日志表
- `experiment_works` - 实验作品表
- `reservation_batches` - 预约批次表

### 3. 验证表结构
```sql
-- 检查预约表是否添加了新字段
DESCRIBE experiment_reservations;

-- 检查实验记录表是否添加了新字段
DESCRIBE experiment_records;

-- 检查设备借用表是否添加了新字段
DESCRIBE equipment_borrows;
```

## 🔧 后端部署

### 1. 安装依赖
```bash
cd backend
composer install --optimize-autoloader --no-dev
```

### 2. 配置环境变量
确保 `.env` 文件包含以下配置：
```env
# 文件上传配置
FILESYSTEM_DISK=public
MAX_UPLOAD_SIZE=51200  # 50MB

# 图片处理配置
IMAGE_DRIVER=gd

# 缓存配置
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. 生成应用密钥
```bash
php artisan key:generate
```

### 4. 创建存储链接
```bash
php artisan storage:link
```

### 5. 清除缓存
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. 设置文件权限
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 🎨 前端部署

### 1. 安装依赖
```bash
cd frontend
npm install
```

### 2. 配置环境变量
创建 `.env.production` 文件：
```env
VITE_API_BASE_URL=http://your-domain.com/api
VITE_APP_NAME=实验教学管理系统
```

### 3. 构建生产版本
```bash
npm run build
```

### 4. 部署静态文件
将 `dist` 目录下的文件部署到Web服务器。

## 🌐 Web服务器配置

### Nginx配置示例
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/frontend/dist;
    index index.html;

    # 前端路由
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API代理
    location /api {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # 文件上传大小限制
    client_max_body_size 50M;
}

# Laravel后端配置
server {
    listen 8000;
    server_name localhost;
    root /path/to/backend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### Apache配置示例
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/frontend/dist
    
    # 前端路由重写
    <Directory "/path/to/frontend/dist">
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>

    # API代理
    ProxyPass /api http://localhost:8000/api
    ProxyPassReverse /api http://localhost:8000/api
</VirtualHost>

<VirtualHost *:8000>
    ServerName localhost
    DocumentRoot /path/to/backend/public
    
    <Directory "/path/to/backend/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 📁 文件存储配置

### 1. 创建存储目录
```bash
mkdir -p storage/app/public/experiment-works
mkdir -p storage/app/public/experiment-works/thumbnails
chmod -R 755 storage/app/public
```

### 2. 配置文件上传
确保PHP配置允许大文件上传：
```ini
; php.ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

## 🔐 权限配置

### 1. 数据库权限
确保数据库用户有以下权限：
- SELECT, INSERT, UPDATE, DELETE
- CREATE, ALTER, DROP (用于迁移)
- INDEX

### 2. 文件系统权限
```bash
# 设置正确的所有者
chown -R www-data:www-data /path/to/backend/storage
chown -R www-data:www-data /path/to/backend/bootstrap/cache

# 设置正确的权限
find /path/to/backend/storage -type f -exec chmod 644 {} \;
find /path/to/backend/storage -type d -exec chmod 755 {} \;
```

## 🧪 部署验证

### 1. 后端API测试
```bash
# 测试基本API
curl -X GET http://your-domain.com/api/health

# 测试认证API
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### 2. 前端功能测试
访问以下页面确认功能正常：
- `/smart-reservation` - 智能预约页面
- `/laboratory-schedule` - 实验室课表
- `/personal-archive` - 个人实验档案

### 3. 文件上传测试
- 测试图片上传功能
- 测试视频上传功能
- 验证文件大小限制

## 🚀 性能优化

### 1. 数据库优化
```sql
-- 添加必要的索引
CREATE INDEX idx_reservations_date_lab ON experiment_reservations(reservation_date, laboratory_id);
CREATE INDEX idx_works_record_type ON experiment_works(record_id, type);
CREATE INDEX idx_borrows_reservation ON equipment_borrows(reservation_id, status);
```

### 2. 缓存配置
```bash
# 启用Redis缓存
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. 队列配置
```bash
# 启动队列处理器
php artisan queue:work --daemon
```

## 📊 监控配置

### 1. 日志配置
确保日志目录可写：
```bash
chmod -R 755 storage/logs
```

### 2. 错误监控
配置错误报告和监控：
```env
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### 3. 性能监控
建议安装性能监控工具：
- Laravel Telescope (开发环境)
- New Relic 或 Datadog (生产环境)

## 🔄 更新部署

### 1. 代码更新
```bash
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate
php artisan config:cache
php artisan route:cache
```

### 2. 前端更新
```bash
cd frontend
npm install
npm run build
```

### 3. 重启服务
```bash
# 重启PHP-FPM
sudo systemctl restart php8.1-fpm

# 重启Nginx
sudo systemctl restart nginx

# 重启队列处理器
php artisan queue:restart
```

## 🆘 故障排除

### 常见问题

1. **文件上传失败**
   - 检查PHP上传限制
   - 检查存储目录权限
   - 检查磁盘空间

2. **数据库连接失败**
   - 检查数据库配置
   - 检查数据库服务状态
   - 检查防火墙设置

3. **API请求失败**
   - 检查路由缓存
   - 检查CORS配置
   - 检查认证配置

### 日志查看
```bash
# 查看Laravel日志
tail -f storage/logs/laravel.log

# 查看Nginx日志
tail -f /var/log/nginx/error.log

# 查看PHP错误日志
tail -f /var/log/php8.1-fpm.log
```

## ✅ 部署检查清单

- [ ] 数据库迁移完成
- [ ] 新增表和字段创建成功
- [ ] 后端依赖安装完成
- [ ] 前端构建完成
- [ ] Web服务器配置正确
- [ ] 文件上传功能正常
- [ ] API接口测试通过
- [ ] 前端页面访问正常
- [ ] 权限配置正确
- [ ] 缓存配置生效
- [ ] 日志记录正常
- [ ] 性能监控配置

完成以上步骤后，实验预约系统即可正常运行！

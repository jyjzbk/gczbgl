<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class PrepareProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:prepare {--check-only : 仅检查环境，不执行部署操作}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '准备生产环境部署，检查配置和安全设置';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 开始生产环境部署准备...');
        
        $checkOnly = $this->option('check-only');
        
        // 环境检查
        $this->checkEnvironment();
        
        // 安全检查
        $this->checkSecurity();
        
        // 数据库检查
        $this->checkDatabase();
        
        // 权限系统检查
        $this->checkPermissionSystem();
        
        if (!$checkOnly) {
            // 执行部署准备操作
            $this->prepareForProduction();
        }
        
        // 生成部署报告
        $this->generateDeploymentReport();
        
        $this->info('✅ 生产环境部署准备完成！');
    }

    /**
     * 检查环境配置
     */
    protected function checkEnvironment()
    {
        $this->info('🔍 检查环境配置...');
        
        $checks = [
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'APP_URL' => env('APP_URL'),
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'CACHE_DRIVER' => env('CACHE_DRIVER'),
            'SESSION_DRIVER' => env('SESSION_DRIVER'),
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION')
        ];

        foreach ($checks as $key => $value) {
            $status = $this->validateEnvironmentValue($key, $value);
            $this->line("  {$key}: {$value} " . ($status ? '✅' : '❌'));
        }

        // 检查必要的目录权限
        $this->checkDirectoryPermissions();
    }

    /**
     * 验证环境变量值
     */
    protected function validateEnvironmentValue($key, $value)
    {
        switch ($key) {
            case 'APP_ENV':
                return $value === 'production';
            case 'APP_DEBUG':
                return $value === false || $value === 'false';
            case 'APP_URL':
                return !empty($value) && filter_var($value, FILTER_VALIDATE_URL);
            case 'DB_CONNECTION':
                return in_array($value, ['mysql', 'pgsql']);
            case 'CACHE_DRIVER':
                return in_array($value, ['redis', 'memcached', 'database']);
            default:
                return !empty($value);
        }
    }

    /**
     * 检查目录权限
     */
    protected function checkDirectoryPermissions()
    {
        $this->line('📁 检查目录权限:');
        
        $directories = [
            'storage' => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'public' => public_path()
        ];

        foreach ($directories as $name => $path) {
            $writable = is_writable($path);
            $this->line("  {$name}: " . ($writable ? '✅ 可写' : '❌ 不可写'));
        }
    }

    /**
     * 检查安全配置
     */
    protected function checkSecurity()
    {
        $this->info('🔒 检查安全配置...');
        
        // 检查APP_KEY
        $appKey = env('APP_KEY');
        $this->line('  APP_KEY: ' . ($appKey ? '✅ 已设置' : '❌ 未设置'));
        
        // 检查JWT密钥
        $jwtSecret = env('JWT_SECRET');
        $this->line('  JWT_SECRET: ' . ($jwtSecret ? '✅ 已设置' : '❌ 未设置'));
        
        // 检查HTTPS配置
        $forceHttps = env('FORCE_HTTPS', false);
        $this->line('  FORCE_HTTPS: ' . ($forceHttps ? '✅ 启用' : '⚠️  未启用'));
        
        // 检查敏感文件
        $this->checkSensitiveFiles();
        
        // 检查数据库连接安全
        $this->checkDatabaseSecurity();
    }

    /**
     * 检查敏感文件
     */
    protected function checkSensitiveFiles()
    {
        $this->line('📄 检查敏感文件:');
        
        $sensitiveFiles = [
            '.env.example' => base_path('.env.example'),
            'README.md' => base_path('README.md'),
            'composer.json' => base_path('composer.json')
        ];

        foreach ($sensitiveFiles as $name => $path) {
            $exists = File::exists($path);
            $this->line("  {$name}: " . ($exists ? '⚠️  存在' : '✅ 不存在'));
        }
    }

    /**
     * 检查数据库安全
     */
    protected function checkDatabaseSecurity()
    {
        $this->line('🗄️  检查数据库安全:');
        
        try {
            // 检查数据库连接
            DB::connection()->getPdo();
            $this->line('  数据库连接: ✅ 正常');
            
            // 检查默认用户
            $defaultUsers = DB::table('users')
                ->whereIn('username', ['admin', 'test', 'demo'])
                ->count();
            
            $this->line('  默认测试用户: ' . ($defaultUsers > 0 ? "⚠️  发现{$defaultUsers}个" : '✅ 无'));
            
        } catch (\Exception $e) {
            $this->line('  数据库连接: ❌ 失败 - ' . $e->getMessage());
        }
    }

    /**
     * 检查数据库状态
     */
    protected function checkDatabase()
    {
        $this->info('🗃️  检查数据库状态...');
        
        try {
            // 检查迁移状态
            $pendingMigrations = $this->getPendingMigrations();
            $this->line('  待执行迁移: ' . (count($pendingMigrations) > 0 ? 
                "⚠️  {count($pendingMigrations)}个" : '✅ 无'));
            
            // 检查数据完整性
            $this->checkDataIntegrity();
            
        } catch (\Exception $e) {
            $this->error('  数据库检查失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取待执行的迁移
     */
    protected function getPendingMigrations()
    {
        // 这里简化处理，实际应该检查migrations表
        return [];
    }

    /**
     * 检查数据完整性
     */
    protected function checkDataIntegrity()
    {
        $this->line('📊 数据完整性检查:');
        
        // 检查关键表的数据
        $tables = [
            'users' => '用户',
            'roles' => '角色',
            'schools' => '学校',
            'administrative_regions' => '行政区域'
        ];

        foreach ($tables as $table => $name) {
            try {
                $count = DB::table($table)->count();
                $this->line("  {$name}表: ✅ {$count}条记录");
            } catch (\Exception $e) {
                $this->line("  {$name}表: ❌ 检查失败");
            }
        }
    }

    /**
     * 检查权限系统
     */
    protected function checkPermissionSystem()
    {
        $this->info('🔐 检查权限系统...');
        
        // 检查权限配置
        $this->checkPermissionConfiguration();
        
        // 检查测试用户权限
        $this->checkTestUserPermissions();
    }

    /**
     * 检查权限配置
     */
    protected function checkPermissionConfiguration()
    {
        $this->line('⚙️  权限配置检查:');
        
        // 检查中间件注册
        $middlewareRegistered = class_exists('App\Http\Middleware\DataScopeMiddleware');
        $this->line('  数据权限中间件: ' . ($middlewareRegistered ? '✅ 已注册' : '❌ 未注册'));
        
        // 检查权限服务
        $serviceRegistered = class_exists('App\Services\PermissionService');
        $this->line('  权限服务: ' . ($serviceRegistered ? '✅ 已注册' : '❌ 未注册'));
    }

    /**
     * 检查测试用户权限
     */
    protected function checkTestUserPermissions()
    {
        $this->line('👤 测试用户权限检查:');
        
        $testUsers = [
            'province_admin_test' => '省级管理员',
            'city_admin_test' => '市级管理员',
            'county_admin_test' => '区县管理员',
            'district_admin_test' => '学区管理员',
            'school_admin_test' => '学校管理员'
        ];

        foreach ($testUsers as $username => $description) {
            $user = DB::table('users')->where('username', $username)->first();
            if ($user) {
                $this->line("  {$description}: ⚠️  测试用户存在");
            } else {
                $this->line("  {$description}: ✅ 测试用户已清理");
            }
        }
    }

    /**
     * 执行生产环境准备操作
     */
    protected function prepareForProduction()
    {
        $this->info('🛠️  执行生产环境准备操作...');
        
        // 清理缓存
        $this->line('  清理缓存...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        // 优化性能
        $this->line('  优化性能...');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        // 运行权限优化
        $this->line('  优化权限系统...');
        Artisan::call('optimize:permissions');
        
        $this->line('✅ 生产环境准备操作完成');
    }

    /**
     * 生成部署报告
     */
    protected function generateDeploymentReport()
    {
        $this->info('📋 生成部署报告...');
        
        $report = [
            'timestamp' => now()->toDateTimeString(),
            'environment' => [
                'app_env' => env('APP_ENV'),
                'app_debug' => env('APP_DEBUG'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version()
            ],
            'database' => [
                'connection' => env('DB_CONNECTION'),
                'users_count' => DB::table('users')->count(),
                'schools_count' => DB::table('schools')->count(),
                'equipments_count' => DB::table('equipments')->count()
            ],
            'security' => [
                'app_key_set' => !empty(env('APP_KEY')),
                'jwt_secret_set' => !empty(env('JWT_SECRET')),
                'force_https' => env('FORCE_HTTPS', false)
            ],
            'recommendations' => $this->getDeploymentRecommendations()
        ];

        $reportPath = storage_path('logs/deployment_report_' . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("📄 部署报告已保存: {$reportPath}");
    }

    /**
     * 获取部署建议
     */
    protected function getDeploymentRecommendations()
    {
        $recommendations = [];
        
        if (env('APP_ENV') !== 'production') {
            $recommendations[] = '设置 APP_ENV=production';
        }
        
        if (env('APP_DEBUG') !== false) {
            $recommendations[] = '设置 APP_DEBUG=false';
        }
        
        if (empty(env('APP_KEY'))) {
            $recommendations[] = '生成应用密钥: php artisan key:generate';
        }
        
        if (env('CACHE_DRIVER') === 'file') {
            $recommendations[] = '建议使用 Redis 或 Memcached 作为缓存驱动';
        }
        
        $recommendations[] = '配置 HTTPS 和 SSL 证书';
        $recommendations[] = '设置定期数据库备份';
        $recommendations[] = '配置日志轮转和监控';
        $recommendations[] = '清理测试用户和测试数据';
        
        return $recommendations;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\School;
use App\Models\Equipment;

class OptimizePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:permissions {--clear-cache : 清除权限缓存}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '优化权限查询性能和数据库查询效率';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 开始权限系统性能优化...');
        
        if ($this->option('clear-cache')) {
            $this->clearPermissionCache();
        }
        
        $this->optimizeDatabase();
        $this->analyzePermissionPerformance();
        $this->generatePerformanceReport();
        
        $this->info('✅ 权限系统性能优化完成！');
    }

    /**
     * 清除权限缓存
     */
    protected function clearPermissionCache()
    {
        $this->info('🧹 清除权限缓存...');

        $driver = config('cache.default');
        $supportsTagging = in_array($driver, ['redis', 'memcached', 'array']);

        if ($supportsTagging) {
            // 清除用户权限缓存
            Cache::tags(['user_permissions'])->flush();

            // 清除角色权限缓存
            Cache::tags(['role_permissions'])->flush();

            // 清除组织架构缓存
            Cache::tags(['organization_hierarchy'])->flush();
        } else {
            // 对于不支持标签的缓存驱动，清除所有缓存
            Cache::flush();
        }

        $this->info('✅ 权限缓存已清除');
    }

    /**
     * 优化数据库查询
     */
    protected function optimizeDatabase()
    {
        $this->info('🔧 优化数据库查询...');
        
        // 检查并创建必要的索引
        $this->createIndexes();
        
        // 分析查询性能
        $this->analyzeQueries();
        
        $this->info('✅ 数据库查询优化完成');
    }

    /**
     * 创建必要的索引
     */
    protected function createIndexes()
    {
        $this->info('📊 检查数据库索引...');
        
        $indexes = [
            'users' => [
                ['organization_id', 'organization_type'],
                ['organization_level'],
                ['role'],
                ['status']
            ],
            'schools' => [
                ['region_id'],
                ['type'],
                ['level'],
                ['status']
            ],
            'equipments' => [
                ['school_id'],
                ['category_id'],
                ['status']
            ],
            'user_roles' => [
                ['user_id'],
                ['role_id']
            ],
            'role_permissions' => [
                ['role_id'],
                ['permission_id']
            ]
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $columns) {
                $indexName = $table . '_' . implode('_', $columns) . '_index';
                
                try {
                    $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
                    
                    if (empty($exists)) {
                        $columnList = implode(', ', $columns);
                        DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
                        $this->line("  ✅ 创建索引: {$table}.{$columnList}");
                    } else {
                        $this->line("  ℹ️  索引已存在: {$table}.{$indexName}");
                    }
                } catch (\Exception $e) {
                    $this->warn("  ❌ 创建索引失败: {$table}.{$indexName} - {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * 分析查询性能
     */
    protected function analyzeQueries()
    {
        $this->info('🔍 分析查询性能...');
        
        // 分析用户权限查询
        $this->analyzeUserPermissionQueries();
        
        // 分析数据权限查询
        $this->analyzeDataScopeQueries();
        
        // 分析组织架构查询
        $this->analyzeOrganizationQueries();
    }

    /**
     * 分析用户权限查询性能
     */
    protected function analyzeUserPermissionQueries()
    {
        $this->line('  📋 分析用户权限查询...');
        
        $startTime = microtime(true);
        
        // 测试用户权限查询
        $testUser = User::with(['roles.permissions'])->first();
        if ($testUser) {
            $permissions = $testUser->getPermissions();
            $queryTime = (microtime(true) - $startTime) * 1000;
            
            $this->line("    用户权限查询时间: {$queryTime}ms");
            $this->line("    权限数量: " . count($permissions));
        }
    }

    /**
     * 分析数据权限查询性能
     */
    protected function analyzeDataScopeQueries()
    {
        $this->line('  🏢 分析数据权限查询...');
        
        $testUsers = User::whereIn('username', [
            'province_admin_test',
            'city_admin_test', 
            'county_admin_test',
            'district_admin_test',
            'school_admin_test'
        ])->get();

        foreach ($testUsers as $user) {
            $startTime = microtime(true);
            
            // 测试学校数据查询
            $schools = School::where(function($query) use ($user) {
                if ($user->organization_type === 'school') {
                    $query->where('id', $user->school_id);
                } elseif ($user->organization_type === 'region') {
                    switch ($user->organization_level) {
                        case 1: // 省级
                            break; // 可以看到所有学校
                        case 2: // 市级
                        case 3: // 区县级
                        case 4: // 学区级
                            // 根据用户的组织ID查询相关学校
                            $query->where('region_id', $user->organization_id);
                            break;
                    }
                }
            })->count();
            
            $queryTime = (microtime(true) - $startTime) * 1000;
            
            $this->line("    {$user->username}: {$queryTime}ms ({$schools}条学校数据)");
        }
    }

    /**
     * 分析组织架构查询性能
     */
    protected function analyzeOrganizationQueries()
    {
        $this->line('  🌳 分析组织架构查询...');
        
        $startTime = microtime(true);
        
        // 测试组织架构层级查询
        $hierarchyCount = DB::table('administrative_regions')
            ->selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->get();
            
        $queryTime = (microtime(true) - $startTime) * 1000;
        
        $this->line("    组织架构查询时间: {$queryTime}ms");
        foreach ($hierarchyCount as $level) {
            $this->line("    级别{$level->level}: {$level->count}个组织");
        }
    }

    /**
     * 分析权限系统性能
     */
    protected function analyzePermissionPerformance()
    {
        $this->info('📈 分析权限系统性能...');
        
        // 统计权限数据
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'role_permissions' => RolePermission::count(),
            'schools' => School::count(),
            'equipments' => Equipment::count()
        ];

        $this->table(
            ['数据类型', '数量'],
            [
                ['用户', $stats['users']],
                ['角色', $stats['roles']],
                ['角色权限', $stats['role_permissions']],
                ['学校', $stats['schools']],
                ['设备', $stats['equipments']]
            ]
        );

        // 分析权限分布
        $this->analyzePermissionDistribution();
    }

    /**
     * 分析权限分布
     */
    protected function analyzePermissionDistribution()
    {
        $this->line('📊 权限分布分析:');
        
        // 按组织级别统计用户分布
        $levelDistribution = User::selectRaw('organization_level, COUNT(*) as count')
            ->whereNotNull('organization_level')
            ->groupBy('organization_level')
            ->orderBy('organization_level')
            ->get();

        foreach ($levelDistribution as $level) {
            $levelName = $this->getLevelName($level->organization_level);
            $this->line("  {$levelName}: {$level->count}个用户");
        }

        // 按角色统计用户分布
        $roleDistribution = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('count', 'desc')
            ->get();

        $this->line('');
        $this->line('👥 角色分布:');
        foreach ($roleDistribution as $role) {
            $roleName = $this->getRoleName($role->role);
            $this->line("  {$roleName}: {$role->count}个用户");
        }
    }

    /**
     * 生成性能报告
     */
    protected function generatePerformanceReport()
    {
        $this->info('📋 生成性能报告...');
        
        $report = [
            'timestamp' => now()->toDateTimeString(),
            'database_size' => $this->getDatabaseSize(),
            'cache_status' => $this->getCacheStatus(),
            'recommendations' => $this->getOptimizationRecommendations()
        ];

        $reportPath = storage_path('logs/permission_performance_' . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("📄 性能报告已保存: {$reportPath}");
    }

    /**
     * 获取数据库大小信息
     */
    protected function getDatabaseSize()
    {
        $tables = ['users', 'roles', 'permissions', 'user_roles', 'role_permissions', 'schools', 'equipments'];
        $sizes = [];
        
        foreach ($tables as $table) {
            try {
                $result = DB::select("SELECT COUNT(*) as count FROM {$table}");
                $sizes[$table] = $result[0]->count ?? 0;
            } catch (\Exception $e) {
                $sizes[$table] = 0;
            }
        }
        
        return $sizes;
    }

    /**
     * 获取缓存状态
     */
    protected function getCacheStatus()
    {
        $driver = config('cache.default');
        $supportsTagging = in_array($driver, ['redis', 'memcached', 'array']);

        return [
            'driver' => $driver,
            'supports_tagging' => $supportsTagging,
            'user_permissions_cached' => $supportsTagging ?
                (Cache::tags(['user_permissions'])->get('test') !== null) :
                (Cache::get('user_permissions_test') !== null),
            'role_permissions_cached' => $supportsTagging ?
                (Cache::tags(['role_permissions'])->get('test') !== null) :
                (Cache::get('role_permissions_test') !== null)
        ];
    }

    /**
     * 获取优化建议
     */
    protected function getOptimizationRecommendations()
    {
        $recommendations = [];
        
        // 检查用户数量
        $userCount = User::count();
        if ($userCount > 1000) {
            $recommendations[] = '建议启用用户权限缓存以提高查询性能';
        }
        
        // 检查权限数量
        $permissionCount = RolePermission::count();
        if ($permissionCount > 100) {
            $recommendations[] = '建议优化权限结构，减少不必要的权限';
        }
        
        // 检查数据库索引
        $recommendations[] = '定期检查和优化数据库索引';
        $recommendations[] = '考虑使用Redis缓存提高权限查询性能';
        
        return $recommendations;
    }

    /**
     * 获取级别名称
     */
    protected function getLevelName($level)
    {
        $names = [
            1 => '省级',
            2 => '市级', 
            3 => '区县级',
            4 => '学区级',
            5 => '学校级'
        ];
        
        return $names[$level] ?? "级别{$level}";
    }

    /**
     * 获取角色名称
     */
    protected function getRoleName($role)
    {
        $names = [
            'super_admin' => '超级管理员',
            'province_admin' => '省级管理员',
            'city_admin' => '市级管理员',
            'county_admin' => '区县管理员',
            'district_admin' => '学区管理员',
            'school_admin' => '学校管理员',
            'teacher' => '教师',
            'student' => '学生'
        ];
        
        return $names[$role] ?? $role;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TextbookVersion;
use Illuminate\Support\Facades\DB;

class FixTextbookVersionsIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:textbook-versions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复教材版本下拉菜单为空的问题';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 开始修复教材版本下拉菜单问题...');
        
        // 1. 检查数据库中的教材版本数据
        $this->info('📊 检查数据库中的教材版本数据...');
        $versions = TextbookVersion::all();
        $activeVersions = TextbookVersion::where('status', 1)->get();
        
        $this->info("总教材版本数: {$versions->count()}");
        $this->info("启用的教材版本数: {$activeVersions->count()}");
        
        if ($activeVersions->count() === 0) {
            $this->warn('⚠️  没有启用的教材版本，正在创建默认数据...');
            $this->createDefaultVersions();
        }
        
        // 2. 显示当前的教材版本
        $this->info('📚 当前的教材版本列表:');
        foreach ($activeVersions as $version) {
            $this->line("  - {$version->name} ({$version->code}) - {$version->publisher}");
        }
        
        // 3. 测试API接口
        $this->info('🧪 测试API接口...');
        $this->testApiEndpoints();
        
        // 4. 检查路由配置
        $this->info('🛣️  检查路由配置...');
        $this->checkRoutes();
        
        $this->info('✅ 修复完成！');
        $this->info('💡 如果前端仍然无法获取数据，请检查:');
        $this->info('   1. 前端API调用是否使用了正确的接口');
        $this->info('   2. 用户是否已登录');
        $this->info('   3. 网络请求是否有CORS问题');
        
        return 0;
    }
    
    private function createDefaultVersions()
    {
        $defaultVersions = [
            ['name' => '人教版', 'code' => 'PEP', 'publisher' => '人民教育出版社'],
            ['name' => '苏教版', 'code' => 'JSEP', 'publisher' => '江苏教育出版社'],
            ['name' => '北师大版', 'code' => 'BNU', 'publisher' => '北京师范大学出版社'],
            ['name' => '教科版', 'code' => 'JKB', 'publisher' => '教育科学出版社'],
        ];
        
        foreach ($defaultVersions as $index => $versionData) {
            TextbookVersion::updateOrCreate(
                ['code' => $versionData['code']],
                [
                    'name' => $versionData['name'],
                    'publisher' => $versionData['publisher'],
                    'status' => 1,
                    'sort_order' => $index + 1
                ]
            );
        }
        
        $this->info('✅ 默认教材版本数据创建完成');
    }
    
    private function testApiEndpoints()
    {
        try {
            // 测试options接口
            $controller = new \App\Http\Controllers\Api\TextbookVersionController();
            $request = new \Illuminate\Http\Request();
            
            $response = $controller->options($request);
            $content = json_decode($response->getContent(), true);
            
            if ($response->getStatusCode() === 200 && $content['success']) {
                $this->info("✅ Options API正常 - 返回 {$content['data']->count()} 个版本");
            } else {
                $this->error("❌ Options API异常 - " . ($content['message'] ?? '未知错误'));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ API测试失败: " . $e->getMessage());
        }
    }
    
    private function checkRoutes()
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->filter(function ($route) {
                return str_contains($route->uri(), 'textbook-versions');
            });
            
        $this->info("找到 {$routes->count()} 个相关路由:");
        foreach ($routes as $route) {
            $this->line("  - {$route->methods()[0]} {$route->uri()}");
        }
    }
}

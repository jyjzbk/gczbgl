<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TextbookVersion;
use App\Http\Controllers\Api\TextbookVersionController;
use Illuminate\Http\Request;

class TestTextbookVersionsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:textbook-versions-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试教材版本API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('测试教材版本API...');
        
        // 测试数据库查询
        $versions = TextbookVersion::where('status', 1)->get(['id', 'name', 'code', 'publisher']);
        $this->info('数据库中的教材版本数量: ' . $versions->count());
        
        foreach ($versions as $version) {
            $this->line("- {$version->name} ({$version->code}) - {$version->publisher}");
        }
        
        // 测试API控制器
        try {
            $controller = new TextbookVersionController();
            $request = new Request();
            $response = $controller->options($request);
            
            $this->info('API响应状态: ' . $response->getStatusCode());
            $content = json_decode($response->getContent(), true);
            
            if ($content['success']) {
                $this->info('API返回的版本数量: ' . count($content['data']));
                foreach ($content['data'] as $version) {
                    $this->line("- {$version['name']} ({$version['code']})");
                }
            } else {
                $this->error('API返回错误: ' . $content['message']);
            }
            
        } catch (\Exception $e) {
            $this->error('API测试失败: ' . $e->getMessage());
        }
        
        return 0;
    }
}

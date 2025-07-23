<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExperimentCatalog;
use App\Models\Subject;
use App\Models\TextbookVersion;

class DiagnoseExperimentCatalogs extends Command
{
    protected $signature = 'diagnose:experiment-catalogs';
    protected $description = '诊断实验目录管理页面显示问题';

    public function handle()
    {
        $this->info('🔍 开始诊断实验目录管理页面问题...');
        
        // 1. 检查实验目录数据
        $this->info('📊 检查实验目录数据:');
        $totalCatalogs = ExperimentCatalog::count();
        $activeCatalogs = ExperimentCatalog::where('status', 1)->count();
        $this->info("总实验目录数: {$totalCatalogs}");
        $this->info("启用的实验目录数: {$activeCatalogs}");
        
        // 2. 检查学科数据
        $this->info('📚 检查学科数据:');
        $subjects = Subject::where('status', 1)->get();
        foreach ($subjects as $subject) {
            $catalogCount = ExperimentCatalog::where('subject_id', $subject->id)->count();
            $this->line("- {$subject->name} (ID: {$subject->id}): {$catalogCount} 个实验");
        }
        
        // 3. 检查教材版本数据
        $this->info('📖 检查教材版本数据:');
        $versions = TextbookVersion::where('status', 1)->get();
        $this->info("启用的教材版本数: {$versions->count()}");
        foreach ($versions as $version) {
            $this->line("- {$version->name} (ID: {$version->id}, Code: {$version->code})");
        }
        
        // 4. 检查小学科学实验数据
        $this->info('🔬 检查小学科学实验数据:');
        $primaryScience = Subject::where('name', 'LIKE', '%科学%')->first();
        if ($primaryScience) {
            $primaryCatalogs = ExperimentCatalog::where('subject_id', $primaryScience->id)->get();
            $this->info("小学科学实验数: {$primaryCatalogs->count()}");
            
            foreach ($primaryCatalogs as $catalog) {
                $versionInfo = $catalog->textbook_version_id ? "版本ID: {$catalog->textbook_version_id}" : "无版本ID";
                $this->line("- {$catalog->name} ({$catalog->code}) - {$catalog->grade}年级{$catalog->semester}学期 - {$versionInfo}");
            }
        } else {
            $this->error('❌ 未找到小学科学学科');
        }
        
        // 5. 检查数据关联问题
        $this->info('🔗 检查数据关联问题:');
        $catalogsWithoutVersion = ExperimentCatalog::whereNull('textbook_version_id')->count();
        $this->info("没有教材版本ID的实验数: {$catalogsWithoutVersion}");
        
        // 6. 模拟前端查询
        $this->info('🎯 模拟前端查询:');
        $query = ExperimentCatalog::query();
        
        if ($primaryScience) {
            $query->where('subject_id', $primaryScience->id);
            $this->line("筛选学科: {$primaryScience->name}");
        }
        
        $query->where('grade', 1)->where('semester', 2);
        $this->line("筛选条件: 1年级下册");
        
        $results = $query->get();
        $this->info("查询结果数: {$results->count()}");
        
        if ($results->count() > 0) {
            $this->info('查询结果:');
            foreach ($results->take(5) as $result) {
                $this->line("- {$result->name} ({$result->code})");
            }
        }
        
        // 7. 建议修复方案
        $this->info('💡 建议修复方案:');
        if ($catalogsWithoutVersion > 0) {
            $this->warn("1. 更新实验目录的教材版本ID");
        }
        if ($versions->count() === 0) {
            $this->warn("2. 创建教材版本数据");
        }
        $this->info("3. 检查前端API调用和权限");
        
        return 0;
    }
}

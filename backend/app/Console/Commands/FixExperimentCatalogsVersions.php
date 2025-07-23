<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExperimentCatalog;
use App\Models\TextbookVersion;
use App\Models\Subject;

class FixExperimentCatalogsVersions extends Command
{
    protected $signature = 'fix:experiment-catalogs-versions';
    protected $description = '修复实验目录的教材版本ID';

    public function handle()
    {
        $this->info('🔧 开始修复实验目录的教材版本ID...');
        
        // 1. 获取教科版教材版本
        $jkbVersion = TextbookVersion::where('code', 'JKB')->first();
        if (!$jkbVersion) {
            $this->error('❌ 未找到教科版教材版本，请先创建');
            return 1;
        }
        
        $this->info("找到教科版: {$jkbVersion->name} (ID: {$jkbVersion->id})");
        
        // 2. 获取小学科学学科
        $primaryScience = Subject::where('name', 'LIKE', '%科学%')->first();
        if (!$primaryScience) {
            $this->error('❌ 未找到小学科学学科');
            return 1;
        }
        
        $this->info("找到学科: {$primaryScience->name} (ID: {$primaryScience->id})");
        
        // 3. 查找需要更新的实验目录
        $catalogsToUpdate = ExperimentCatalog::where('subject_id', $primaryScience->id)
            ->whereNull('textbook_version_id')
            ->get();
            
        $this->info("找到需要更新的实验目录数: {$catalogsToUpdate->count()}");
        
        if ($catalogsToUpdate->count() === 0) {
            $this->info('✅ 所有实验目录都已有教材版本ID');
            return 0;
        }
        
        // 4. 更新实验目录
        $updated = 0;
        foreach ($catalogsToUpdate as $catalog) {
            $catalog->update(['textbook_version_id' => $jkbVersion->id]);
            $this->line("✓ 更新: {$catalog->name} ({$catalog->code})");
            $updated++;
        }
        
        $this->info("✅ 成功更新 {$updated} 个实验目录的教材版本ID");
        
        // 5. 验证更新结果
        $this->info('🔍 验证更新结果...');
        $primaryCatalogs = ExperimentCatalog::where('subject_id', $primaryScience->id)
            ->where('grade', 1)
            ->where('semester', 2)
            ->with('textbookVersion')
            ->get();
            
        $this->info("一年级下册实验数: {$primaryCatalogs->count()}");
        foreach ($primaryCatalogs as $catalog) {
            $versionName = $catalog->textbookVersion ? $catalog->textbookVersion->name : '无版本';
            $this->line("- {$catalog->name} - {$versionName}");
        }
        
        $this->info('💡 现在可以在前端选择"教科版"来查看小学科学实验目录了！');
        
        return 0;
    }
}

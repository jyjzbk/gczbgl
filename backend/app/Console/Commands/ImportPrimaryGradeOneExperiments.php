<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportPrimaryGradeOneExperiments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'experiments:import-primary-grade-one';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入教科版小学科学一年级下册实验目录数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('开始导入教科版小学科学一年级下册实验目录数据...');
        
        // 运行Seeder
        Artisan::call('db:seed', [
            '--class' => 'PrimaryGradeOneVolume2ExperimentCatalogsSeeder',
            '--force' => true
        ]);
        
        $this->info('导入完成！');
        $this->info('可以通过以下SQL查询验证导入结果：');
        $this->info('SELECT * FROM experiment_catalogs WHERE grade = 1 AND semester = 2;');
        
        return 0;
    }
}

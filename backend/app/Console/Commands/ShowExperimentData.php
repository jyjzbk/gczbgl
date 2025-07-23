<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExperimentCatalog;

class ShowExperimentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'experiments:show {grade=1} {semester=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '显示实验目录数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $grade = $this->argument('grade');
        $semester = $this->argument('semester');
        
        $experiments = ExperimentCatalog::where('grade', $grade)
            ->where('semester', $semester)
            ->orderBy('id')
            ->get();
            
        $this->info("年级：{$grade}年级，学期：" . ($semester == 1 ? '上册' : '下册'));
        $this->info("总实验数：" . $experiments->count());
        $this->line('');
        
        $headers = ['编号', '实验名称', '章节', '类型', '器材'];
        $rows = [];
        
        foreach ($experiments as $exp) {
            $type = match($exp->type) {
                1 => '必做',
                2 => '选做', 
                3 => '演示',
                4 => '分组',
                default => '未知'
            };
            
            $rows[] = [
                $exp->code,
                $exp->name,
                $exp->chapter,
                $type,
                substr($exp->materials, 0, 30) . (strlen($exp->materials) > 30 ? '...' : '')
            ];
        }
        
        $this->table($headers, $rows);
        
        return 0;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TextbookChapter;
use App\Models\Subject;
use App\Models\TextbookVersion;

class PrimaryGradeOneVolume2ChaptersSeeder extends Seeder
{
    /**
     * 创建小学科学一年级下册章节数据
     */
    public function run(): void
    {
        // 获取学科和教材版本ID
        $subject = Subject::where('name', '小学科学')->first();
        $textbookVersion = TextbookVersion::where('name', '教科版')->first();

        if (!$subject || !$textbookVersion) {
            $this->command->error('未找到小学科学学科或教科版教材版本');
            return;
        }

        // 一年级下册章节数据
        $chapters = [
            [
                'code' => '01',
                'name' => '第一单元 我们周围的物体',
                'level' => 1,
                'sort_order' => 1,
            ],
            [
                'code' => '02', 
                'name' => '第二单元 动物',
                'level' => 1,
                'sort_order' => 2,
            ]
        ];

        foreach ($chapters as $chapterData) {
            TextbookChapter::create([
                'subject_id' => $subject->id,
                'textbook_version_id' => $textbookVersion->id,
                'grade_level' => '1',
                'volume' => '下册',
                'parent_id' => null,
                'level' => $chapterData['level'],
                'code' => $chapterData['code'],
                'name' => $chapterData['name'],
                'sort_order' => $chapterData['sort_order'],
                'status' => 1
            ]);
        }

        $this->command->info('小学科学一年级下册章节数据创建完成');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建教师用户...');

        // 获取所有学校
        $schools = School::all();

        if ($schools->isEmpty()) {
            $this->command->error('没有找到学校数据，请先运行学校种子文件');
            return;
        }

        $teacherCount = 0;

        // 为每个学校创建教师
        foreach ($schools->take(10) as $index => $school) {
            // 为每个学校创建3-5个教师
            for ($i = 1; $i <= 5; $i++) {
                $username = "teacher_{$school->id}_{$i}";
                
                // 检查用户是否已存在
                if (User::where('username', $username)->exists()) {
                    continue;
                }

                User::create([
                    'username' => $username,
                    'email' => "teacher_{$school->id}_{$i}@example.com",
                    'password' => Hash::make('password'),
                    'real_name' => $school->name . "实验教师{$i}",
                    'status' => 1,
                    'role' => 'teacher',
                    'department' => '实验教学部',
                    'position' => '实验教师',
                    'school_id' => $school->id,
                    'organization_type' => 'school',
                    'organization_id' => $school->id,
                    'organization_level' => 5, // 学校级别
                ]);

                $teacherCount++;
            }
        }

        $this->command->info("教师用户创建完成！共创建 {$teacherCount} 个教师用户");
        $this->command->info("教师用户名格式: teacher_{学校ID}_{序号}");
        $this->command->info("密码统一为: password");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\School;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestApiConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试API连接和数据库状态';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== API连接和数据库测试 ===');

        try {
            // 测试数据库连接
            DB::connection()->getPdo();
            $this->info('✅ 数据库连接正常');

            // 检查基础数据
            $userCount = User::count();
            $roleCount = Role::count();
            $schoolCount = School::count();
            $equipmentCount = Equipment::count();

            $this->info("✅ 用户数量: {$userCount}");
            $this->info("✅ 角色数量: {$roleCount}");
            $this->info("✅ 学校数量: {$schoolCount}");
            $this->info("✅ 设备数量: {$equipmentCount}");

            // 检查现有用户
            $existingUser = User::first();
            if ($existingUser) {
                $this->info('✅ 现有用户: ' . $existingUser->username);
                $this->info('✅ 用户角色: ' . $existingUser->role);
            }

            // 创建测试用户（如果不存在）
            $testUser = User::where('username', 'test_admin')->first();
            if (!$testUser) {
                $school = School::first();

                if ($school) {
                    $testUser = User::create([
                        'username' => 'test_admin',
                        'password' => Hash::make('password123'),
                        'real_name' => '测试管理员',
                        'email' => 'test@example.com',
                        'role' => 'super_admin',
                        'school_id' => $school->id,
                        'status' => 1
                    ]);
                    $this->info('✅ 创建测试用户成功');
                } else {
                    $this->error('❌ 缺少学校数据');
                }
            } else {
                $this->info('✅ 测试用户已存在');
            }

            // 测试JWT配置
            $jwtSecret = config('jwt.secret');
            if ($jwtSecret) {
                $this->info('✅ JWT配置正常');
            } else {
                $this->error('❌ JWT配置缺失');
            }

            $this->info('测试完成');

        } catch (\Exception $e) {
            $this->error('❌ 错误: ' . $e->getMessage());
            $this->error('文件: ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}

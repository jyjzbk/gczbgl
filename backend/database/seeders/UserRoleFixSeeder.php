<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\School;
use App\Models\AdministrativeRegion;

class UserRoleFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始修复用户角色关联...');

        // 1. 创建测试用户（如果不存在）
        $this->createTestUsers();

        // 2. 修复现有用户的角色关联
        $this->fixUserRoles();

        // 3. 验证权限配置
        $this->verifyPermissions();

        $this->command->info('用户角色关联修复完成！');
    }

    /**
     * 创建测试用户
     */
    private function createTestUsers()
    {
        $this->command->info('创建测试用户...');

        // 获取第一个学校
        $school = School::first();
        if (!$school) {
            $this->command->error('没有找到学校数据，请先运行学校种子文件');
            return;
        }

        // 创建school_admin用户（如果不存在）
        $schoolAdmin = User::where('username', 'school_admin')->first();
        if (!$schoolAdmin) {
            $schoolAdmin = User::create([
                'username' => 'school_admin',
                'email' => 'school_admin@example.com',
                'password' => bcrypt('password'),
                'real_name' => '校长',
                'role' => 'school_admin',
                'school_id' => $school->id,
                'status' => User::STATUS_ACTIVE
            ]);
            $this->command->info('✅ 创建school_admin用户');
        }

        // 创建student001用户（如果不存在）
        $student = User::where('username', 'student001')->first();
        if (!$student) {
            $student = User::create([
                'username' => 'student001',
                'email' => 'student001@163.com',
                'password' => bcrypt('password'),
                'real_name' => 'student001',
                'role' => 'school_student',
                'school_id' => $school->id,
                'status' => User::STATUS_ACTIVE
            ]);
            $this->command->info('✅ 创建student001用户');
        }
    }

    /**
     * 修复用户角色关联
     */
    private function fixUserRoles()
    {
        $this->command->info('修复用户角色关联...');

        // 获取所有用户
        $users = User::all();
        
        foreach ($users as $user) {
            // 清除现有的角色关联
            $user->roles()->detach();

            // 根据用户的role字段分配正确的角色
            $this->assignUserRole($user);
        }
    }

    /**
     * 为用户分配角色
     */
    private function assignUserRole(User $user)
    {
        $roleCode = $user->role;
        if (!$roleCode) {
            $this->command->warn("用户 {$user->username} 没有角色信息");
            return;
        }

        // 查找角色
        $role = Role::where('code', $roleCode)->first();
        if (!$role) {
            $this->command->warn("角色 {$roleCode} 不存在");
            return;
        }

        // 确定权限范围
        $scopeType = null;
        $scopeId = null;

        switch ($role->level) {
            case Role::LEVEL_PROVINCE:
                $scopeType = UserRole::SCOPE_REGION;
                $scopeId = AdministrativeRegion::where('level', AdministrativeRegion::LEVEL_PROVINCE)->first()?->id;
                break;
            case Role::LEVEL_CITY:
                $scopeType = UserRole::SCOPE_REGION;
                $scopeId = AdministrativeRegion::where('level', AdministrativeRegion::LEVEL_CITY)->first()?->id;
                break;
            case Role::LEVEL_COUNTY:
                $scopeType = UserRole::SCOPE_REGION;
                $scopeId = AdministrativeRegion::where('level', AdministrativeRegion::LEVEL_COUNTY)->first()?->id;
                break;
            case Role::LEVEL_SCHOOL:
                $scopeType = UserRole::SCOPE_SCHOOL;
                $scopeId = $user->school_id ?: School::first()?->id;
                break;
        }

        if ($scopeType && $scopeId) {
            // 创建用户角色关联
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'scope_type' => $scopeType,
                'scope_id' => $scopeId
            ]);

            $this->command->info("✅ 为用户 {$user->username} 分配角色 {$role->name}");
        } else {
            $this->command->warn("无法为用户 {$user->username} 确定权限范围");
        }
    }

    /**
     * 验证权限配置
     */
    private function verifyPermissions()
    {
        $this->command->info('验证权限配置...');

        $testUsers = [
            'school_admin' => ['equipment', 'equipment.list', 'equipment.create', 'equipment.delete'],
            'student001' => ['equipment', 'equipment.list'],
            'admin' => ['equipment', 'equipment.list', 'equipment.create', 'equipment.update']
        ];

        foreach ($testUsers as $username => $expectedPermissions) {
            $user = User::where('username', $username)->first();
            if (!$user) {
                continue;
            }

            $userPermissions = $user->getPermissions();
            $this->command->info("用户 {$username} 的权限: " . implode(', ', $userPermissions));

            foreach ($expectedPermissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $this->command->info("✅ {$username} 拥有权限: {$permission}");
                } else {
                    $this->command->warn("❌ {$username} 缺少权限: {$permission}");
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class FixTeacherPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始修复任课教师权限...');

        // 查找任课教师角色
        $teacherRole = Role::where('code', 'school_teacher')->first();
        if (!$teacherRole) {
            $this->command->error('未找到任课教师角色');
            return;
        }

        // 需要添加的权限
        $requiredPermissions = [
            'basic.textbook_version.view',
            'basic.textbook_chapter.view',
            'basic.textbook_chapter.tree',
            'experiment.catalog.view',
            'experiment.booking.view',
            'experiment.booking.create',
            'experiment.booking.edit',
            'experiment.record.view',
            'experiment.record.create',
            'experiment.record.edit',
            'experiment.record.complete',
            'equipment.view',
            'equipment.borrow.view',
            'equipment.borrow.create',
            'equipment.borrow.edit',
            'equipment.maintenance.view',
            'equipment.maintenance.create',
            'equipment.qrcode.view',
            'statistics.view',
            'statistics.dashboard',
            'statistics.experiment',
            'statistics.equipment',
        ];

        $addedCount = 0;
        $existingCount = 0;

        foreach ($requiredPermissions as $permissionCode) {
            // 检查角色是否已有此权限
            $exists = DB::table('role_permissions')
                ->where('role_id', $teacherRole->id)
                ->where('permission_code', $permissionCode)
                ->exists();

            if (!$exists) {
                // 添加权限到角色
                DB::table('role_permissions')->insert([
                    'role_id' => $teacherRole->id,
                    'permission_code' => $permissionCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $addedCount++;
                $this->command->info("添加权限到任课教师角色: {$permissionCode}");
            } else {
                $existingCount++;
            }
        }

        $this->command->info("权限修复完成！");
        $this->command->info("新增权限: {$addedCount} 个");
        $this->command->info("已存在权限: {$existingCount} 个");

        // 清除权限缓存
        \Illuminate\Support\Facades\Cache::forget("role_permissions_{$teacherRole->id}");
        $this->command->info("已清除权限缓存");
    }

    private function getPermissionName($code): string
    {
        $names = [
            'basic.textbook_version.view' => '查看教材版本',
            'basic.textbook_chapter.view' => '查看教材章节',
            'basic.textbook_chapter.tree' => '查看章节树形结构',
            'experiment.catalog.view' => '查看实验目录',
            'experiment.booking.view' => '查看实验预约',
            'experiment.booking.create' => '创建实验预约',
            'experiment.booking.edit' => '编辑实验预约',
            'experiment.record.view' => '查看实验记录',
            'experiment.record.create' => '创建实验记录',
            'experiment.record.edit' => '编辑实验记录',
            'experiment.record.complete' => '完成实验记录',
            'equipment.view' => '查看设备',
            'equipment.borrow.view' => '查看设备借用',
            'equipment.borrow.create' => '创建设备借用',
            'equipment.borrow.edit' => '编辑设备借用',
            'equipment.maintenance.view' => '查看设备维修',
            'equipment.maintenance.create' => '创建设备维修',
            'equipment.qrcode.view' => '查看设备二维码',
            'statistics.view' => '查看统计',
            'statistics.dashboard' => '查看仪表盘',
            'statistics.experiment' => '查看实验统计',
            'statistics.equipment' => '查看设备统计',
        ];

        return $names[$code] ?? $code;
    }

    private function getPermissionDescription($code): string
    {
        return "任课教师权限：{$this->getPermissionName($code)}";
    }

    private function getPermissionModule($code): string
    {
        if (str_starts_with($code, 'basic.')) return 'basic';
        if (str_starts_with($code, 'experiment.')) return 'experiment';
        if (str_starts_with($code, 'equipment.')) return 'equipment';
        if (str_starts_with($code, 'statistics.')) return 'statistics';
        return 'system';
    }
}

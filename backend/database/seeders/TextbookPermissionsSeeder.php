<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TextbookPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始添加教材版本和章节管理权限...');

        // 定义新权限代码
        $newPermissions = [
            // 教材版本权限
            'textbook_versions',
            'textbook_versions.list',
            'textbook_versions.create',
            'textbook_versions.update',
            'textbook_versions.delete',
            'textbook_versions.view',

            // 章节结构权限
            'textbook_chapters',
            'textbook_chapters.list',
            'textbook_chapters.create',
            'textbook_chapters.update',
            'textbook_chapters.delete',
            'textbook_chapters.view',
            'textbook_chapters.tree',
        ];

        // 获取角色ID
        $roles = DB::table('roles')->whereIn('code', [
            'province_admin',
            'city_admin',
            'county_admin',
            'district_admin',
            'school_principal',
            'school_experimenter'
        ])->pluck('id', 'code');

        // 为各个角色分配权限
        $rolePermissionMap = [
            'province_admin' => $newPermissions,             // 省级管理员拥有所有权限
            'city_admin' => $newPermissions,                 // 市级管理员拥有所有权限
            'county_admin' => $newPermissions,               // 区县管理员拥有所有权限
            'district_admin' => [                            // 学区管理员只有查看权限
                'textbook_versions', 'textbook_versions.list', 'textbook_versions.view',
                'textbook_chapters', 'textbook_chapters.list', 'textbook_chapters.view', 'textbook_chapters.tree'
            ],
            'school_principal' => [                          // 学校校长只有查看权限
                'textbook_versions', 'textbook_versions.list', 'textbook_versions.view',
                'textbook_chapters', 'textbook_chapters.list', 'textbook_chapters.view', 'textbook_chapters.tree'
            ],
            'school_experimenter' => [                       // 实验员只有查看权限
                'textbook_versions', 'textbook_versions.list', 'textbook_versions.view',
                'textbook_chapters', 'textbook_chapters.list', 'textbook_chapters.view', 'textbook_chapters.tree'
            ]
        ];

        // 分配权限给角色
        foreach ($rolePermissionMap as $roleName => $permissions) {
            if (!isset($roles[$roleName])) {
                $this->command->warn("⚠️  角色 {$roleName} 不存在，跳过权限分配");
                continue;
            }

            $roleId = $roles[$roleName];

            foreach ($permissions as $permissionCode) {
                // 检查是否已存在
                $exists = DB::table('role_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_code', $permissionCode)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_code' => $permissionCode,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $this->command->info("✅ 为角色 {$roleName} 分配权限: {$permissionCode}");
                } else {
                    $this->command->info("ℹ️  角色 {$roleName} 已有权限: {$permissionCode}");
                }
            }
        }

        $this->command->info('🎉 教材版本和章节管理权限添加完成！');
    }
}

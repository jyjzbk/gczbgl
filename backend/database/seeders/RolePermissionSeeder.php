<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空现有权限
        RolePermission::truncate();

        // 权限配置映射
        $permissionMap = [
            // 省级角色
            'province_admin' => [
                'user', 'user.list', 'user.create', 'user.update', 'user.delete',
                'role', 'role.list', 'role.create', 'role.update', 'role.delete',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance'
            ],
            'province_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],

            // 市级角色
            'city_admin' => [
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.borrow', 'equipment.maintenance'
            ],
            'city_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],

            // 区县级角色
            'county_admin' => [
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.borrow', 'equipment.maintenance'
            ],
            'county_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],

            // 学区级角色
            'district_admin' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance'
            ],

            // 学校级角色
            'school_principal' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],
            'school_dean' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance'
            ],
            'school_experimenter' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance'
            ],
            'school_teacher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow'
            ]
        ];

        // 为每个角色分配默认权限
        foreach ($permissionMap as $roleCode => $permissions) {
            $role = Role::where('code', $roleCode)->first();
            if ($role) {
                foreach ($permissions as $permissionCode) {
                    RolePermission::create([
                        'role_id' => $role->id,
                        'permission_code' => $permissionCode
                    ]);
                }
            }
        }
    }
}

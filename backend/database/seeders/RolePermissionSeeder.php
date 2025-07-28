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
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit', 'user.export', 'user.reset_password',
                'role', 'role.list', 'role.create', 'role.update', 'role.delete',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list', 'laboratory_type.create', 'laboratory_type.update', 'laboratory_type.delete',
                'equipment_standard', 'equipment_standard.list', 'equipment_standard.create', 'equipment_standard.update', 'equipment_standard.delete',
                'statistics.view', 'statistics.dashboard', 'statistics.experiment', 'statistics.equipment', 'statistics.user', 'statistics.performance', 'statistics.export',
                'system', 'system.read', 'log', 'log.read',
                // 学校实验目录管理权限
                'school_experiment_catalog.view', 'school_experiment_catalog.assign', 'school_experiment_catalog.completion_stats', 'school_experiment_catalog.baseline_manage',
                'experiment_catalog.create_province', 'experiment_catalog.approve_baseline'
            ],
            'province_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],

            // 市级角色
            'city_admin' => [
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit', 'user.export', 'user.reset_password',
                'role', 'role.list', 'role.create', 'role.update', 'role.delete',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list', 'laboratory_type.create', 'laboratory_type.update', 'laboratory_type.delete',
                'equipment_standard', 'equipment_standard.list', 'equipment_standard.create', 'equipment_standard.update', 'equipment_standard.delete',
                'statistics', 'statistics.view', 'statistics.dashboard', 'statistics.experiment', 'statistics.equipment', 'statistics.user', 'statistics.performance', 'statistics.export',
                // 学校实验目录管理权限
                'school_experiment_catalog.view', 'school_experiment_catalog.assign', 'school_experiment_catalog.completion_stats',
                'experiment_catalog.create_city'
            ],
            'city_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],

            // 区县级角色
            'county_admin' => [
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit', 'user.export', 'user.reset_password',
                'role', 'role.list', 'role.create', 'role.update', 'role.delete',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list', 'laboratory_type.create', 'laboratory_type.update', 'laboratory_type.delete',
                'equipment_standard', 'equipment_standard.list', 'equipment_standard.create', 'equipment_standard.update', 'equipment_standard.delete',
                'statistics', 'statistics.view', 'statistics.dashboard', 'statistics.experiment', 'statistics.equipment', 'statistics.user', 'statistics.performance', 'statistics.export',
                // 学校实验目录管理权限
                'school_experiment_catalog.view', 'school_experiment_catalog.assign', 'school_experiment_catalog.completion_stats', 'school_experiment_catalog.baseline_manage',
                'experiment_catalog.create_county'
            ],
            'county_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],

            // 学区级角色
            'district_admin' => [
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list',
                'statistics', 'statistics.view', 'statistics.dashboard', 'statistics.experiment', 'statistics.equipment', 'statistics.user', 'statistics.performance'
            ],

            // 学校级角色
            'school_principal' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],
            'school_dean' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],
            'school_experimenter' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],
            'school_teacher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
            ],
            'school_admin' => [
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list',
                // 学校实验目录管理权限
                'school_experiment_catalog.view', 'school_experiment_catalog.config', 'school_experiment_catalog.completion_stats'
            ],
            'school_student' => [
                'experiment', 'experiment.catalog', 'experiment.booking',
                'equipment', 'equipment.list',
                'laboratory_type', 'laboratory_type.list',
                'equipment_standard', 'equipment_standard.list'
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

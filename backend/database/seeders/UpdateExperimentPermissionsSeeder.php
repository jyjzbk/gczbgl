<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class UpdateExperimentPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 实验管理相关权限
        $experimentPermissions = [
            'experiment.catalog.view',
            'experiment.catalog.create',
            'experiment.catalog.edit',
            'experiment.catalog.delete',
            'experiment.booking.view',
            'experiment.booking.create',
            'experiment.booking.edit',
            'experiment.booking.approve',
            'experiment.booking.cancel',
            'experiment.record.view',
            'experiment.record.create',
            'experiment.record.edit',
            'experiment.record.complete',
        ];

        // 角色权限配置
        $rolePermissions = [
            'province_admin' => $experimentPermissions,
            'city_admin' => $experimentPermissions,
            'county_admin' => $experimentPermissions,
            'district_admin' => $experimentPermissions,
            'school_principal' => $experimentPermissions,
            'school_admin' => $experimentPermissions,
            'experiment_teacher' => [
                'experiment.catalog.view',
                'experiment.booking.view',
                'experiment.booking.create',
                'experiment.booking.edit',
                'experiment.record.view',
                'experiment.record.create',
                'experiment.record.edit',
                'experiment.record.complete',
            ],
            'lab_manager' => [
                'experiment.catalog.view',
                'experiment.booking.view',
                'experiment.booking.approve',
                'experiment.record.view',
                'experiment.record.create',
                'experiment.record.edit',
            ],
        ];

        foreach ($rolePermissions as $roleCode => $permissions) {
            $role = Role::where('code', $roleCode)->first();
            
            if ($role) {
                echo "更新角色权限: {$role->name} ({$roleCode})\n";
                
                // 添加新权限（如果不存在）
                foreach ($permissions as $permission) {
                    $exists = RolePermission::where('role_id', $role->id)
                        ->where('permission_code', $permission)
                        ->exists();
                    
                    if (!$exists) {
                        RolePermission::create([
                            'role_id' => $role->id,
                            'permission_code' => $permission,
                        ]);
                        echo "  添加权限: {$permission}\n";
                    }
                }
            } else {
                echo "角色不存在: {$roleCode}\n";
            }
        }

        echo "实验管理权限更新完成！\n";
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * 获取权限树
     */
    public function tree(): JsonResponse
    {
        // 返回权限树结构
        $permissions = [
            [
                'id' => 'user',
                'name' => '用户管理',
                'code' => 'user',
                'children' => [
                    ['id' => 'user.list', 'name' => '用户列表', 'code' => 'user.list', 'type' => 'read'],
                    ['id' => 'user.create', 'name' => '创建用户', 'code' => 'user.create', 'type' => 'write'],
                    ['id' => 'user.update', 'name' => '更新用户', 'code' => 'user.update', 'type' => 'write'],
                    ['id' => 'user.edit', 'name' => '编辑用户', 'code' => 'user.edit', 'type' => 'write'],
                    ['id' => 'user.delete', 'name' => '删除用户', 'code' => 'user.delete', 'type' => 'delete'],
                    ['id' => 'user.export', 'name' => '导出用户', 'code' => 'user.export', 'type' => 'advanced', 'level' => 'high'],
                    ['id' => 'user.reset_password', 'name' => '重置密码', 'code' => 'user.reset_password', 'type' => 'advanced', 'level' => 'high']
                ]
            ],
            [
                'id' => 'role',
                'name' => '角色管理',
                'code' => 'role',
                'children' => [
                    ['id' => 'role.list', 'name' => '角色列表', 'code' => 'role.list', 'type' => 'read'],
                    ['id' => 'role.create', 'name' => '创建角色', 'code' => 'role.create', 'type' => 'write'],
                    ['id' => 'role.update', 'name' => '编辑角色', 'code' => 'role.update', 'type' => 'write'],
                    ['id' => 'role.delete', 'name' => '删除角色', 'code' => 'role.delete', 'type' => 'delete']
                ]
            ],
            [
                'id' => 'experiment',
                'name' => '实验管理',
                'code' => 'experiment',
                'children' => [
                    ['id' => 'experiment.catalog', 'name' => '实验目录', 'code' => 'experiment.catalog', 'type' => 'read'],
                    ['id' => 'experiment.booking', 'name' => '实验预约', 'code' => 'experiment.booking', 'type' => 'write'],
                    ['id' => 'experiment.record', 'name' => '实验记录', 'code' => 'experiment.record', 'type' => 'write']
                ]
            ],
            [
                'id' => 'equipment',
                'name' => '设备管理',
                'code' => 'equipment',
                'children' => [
                    ['id' => 'equipment.list', 'name' => '设备列表', 'code' => 'equipment.list', 'type' => 'read'],
                    ['id' => 'equipment.create', 'name' => '添加设备', 'code' => 'equipment.create', 'type' => 'write'],
                    ['id' => 'equipment.update', 'name' => '编辑设备', 'code' => 'equipment.update', 'type' => 'write'],
                    ['id' => 'equipment.delete', 'name' => '删除设备', 'code' => 'equipment.delete', 'type' => 'delete'],
                    ['id' => 'equipment.borrow', 'name' => '设备借用', 'code' => 'equipment.borrow', 'type' => 'write'],
                    ['id' => 'equipment.maintenance', 'name' => '设备维修', 'code' => 'equipment.maintenance', 'type' => 'write']
                ]
            ],
            [
                'id' => 'system',
                'name' => '系统管理',
                'code' => 'system',
                'children' => [
                    ['id' => 'system.read', 'name' => '系统信息', 'code' => 'system.read', 'type' => 'advanced', 'level' => 'high'],
                    ['id' => 'log.read', 'name' => '日志查看', 'code' => 'log.read', 'type' => 'advanced', 'level' => 'high']
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * 获取权限列表（扁平结构）
     */
    public function index(): JsonResponse
    {
        $permissions = [
            ['id' => 'user.list', 'name' => '用户列表', 'code' => 'user.list', 'type' => 'read', 'module' => 'user'],
            ['id' => 'user.create', 'name' => '创建用户', 'code' => 'user.create', 'type' => 'write', 'module' => 'user'],
            ['id' => 'user.update', 'name' => '编辑用户', 'code' => 'user.update', 'type' => 'write', 'module' => 'user'],
            ['id' => 'user.delete', 'name' => '删除用户', 'code' => 'user.delete', 'type' => 'delete', 'module' => 'user'],
            
            ['id' => 'role.list', 'name' => '角色列表', 'code' => 'role.list', 'type' => 'read', 'module' => 'role'],
            ['id' => 'role.create', 'name' => '创建角色', 'code' => 'role.create', 'type' => 'write', 'module' => 'role'],
            ['id' => 'role.update', 'name' => '编辑角色', 'code' => 'role.update', 'type' => 'write', 'module' => 'role'],
            ['id' => 'role.delete', 'name' => '删除角色', 'code' => 'role.delete', 'type' => 'delete', 'module' => 'role'],
            
            ['id' => 'experiment.catalog', 'name' => '实验目录', 'code' => 'experiment.catalog', 'type' => 'read', 'module' => 'experiment'],
            ['id' => 'experiment.booking', 'name' => '实验预约', 'code' => 'experiment.booking', 'type' => 'write', 'module' => 'experiment'],
            ['id' => 'experiment.record', 'name' => '实验记录', 'code' => 'experiment.record', 'type' => 'write', 'module' => 'experiment'],
            
            ['id' => 'equipment.list', 'name' => '设备列表', 'code' => 'equipment.list', 'type' => 'read', 'module' => 'equipment'],
            ['id' => 'equipment.create', 'name' => '添加设备', 'code' => 'equipment.create', 'type' => 'write', 'module' => 'equipment'],
            ['id' => 'equipment.update', 'name' => '编辑设备', 'code' => 'equipment.update', 'type' => 'write', 'module' => 'equipment'],
            ['id' => 'equipment.delete', 'name' => '删除设备', 'code' => 'equipment.delete', 'type' => 'delete', 'module' => 'equipment'],
            ['id' => 'equipment.borrow', 'name' => '设备借用', 'code' => 'equipment.borrow', 'type' => 'write', 'module' => 'equipment'],
            ['id' => 'equipment.maintenance', 'name' => '设备维修', 'code' => 'equipment.maintenance', 'type' => 'write', 'module' => 'equipment']
        ];

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }
}

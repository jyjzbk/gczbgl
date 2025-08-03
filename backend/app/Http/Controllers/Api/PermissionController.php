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
                    [
                        'id' => 'experiment.catalog',
                        'name' => '实验目录',
                        'code' => 'experiment.catalog',
                        'type' => 'read',
                        'children' => [
                            ['id' => 'experiment.catalog.view', 'name' => '查看实验目录', 'code' => 'experiment.catalog.view', 'type' => 'read'],
                            ['id' => 'experiment.catalog.create', 'name' => '创建实验目录', 'code' => 'experiment.catalog.create', 'type' => 'write'],
                            ['id' => 'experiment.catalog.edit', 'name' => '编辑实验目录', 'code' => 'experiment.catalog.edit', 'type' => 'write'],
                            ['id' => 'experiment.catalog.delete', 'name' => '删除实验目录', 'code' => 'experiment.catalog.delete', 'type' => 'delete'],
                            ['id' => 'experiment.catalog.copy', 'name' => '复制实验目录', 'code' => 'experiment.catalog.copy', 'type' => 'write'],
                            ['id' => 'experiment.catalog.manage_level', 'name' => '管理级别权限', 'code' => 'experiment.catalog.manage_level', 'type' => 'advanced', 'level' => 'high']
                        ]
                    ],
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
                'id' => 'basic',
                'name' => '基础数据',
                'code' => 'basic',
                'children' => [
                    [
                        'id' => 'basic.subject',
                        'name' => '学科管理',
                        'code' => 'basic.subject',
                        'type' => 'read',
                        'children' => [
                            ['id' => 'basic.subject.view', 'name' => '查看学科', 'code' => 'basic.subject.view', 'type' => 'read'],
                            ['id' => 'basic.subject.create', 'name' => '创建学科', 'code' => 'basic.subject.create', 'type' => 'write'],
                            ['id' => 'basic.subject.edit', 'name' => '编辑学科', 'code' => 'basic.subject.edit', 'type' => 'write'],
                            ['id' => 'basic.subject.delete', 'name' => '删除学科', 'code' => 'basic.subject.delete', 'type' => 'delete']
                        ]
                    ],
                    [
                        'id' => 'basic.equipment_standard',
                        'name' => '教学仪器配备标准',
                        'code' => 'basic.equipment_standard',
                        'type' => 'read',
                        'children' => [
                            ['id' => 'basic.equipment_standard.view', 'name' => '查看配备标准', 'code' => 'basic.equipment_standard.view', 'type' => 'read'],
                            ['id' => 'basic.equipment_standard.create', 'name' => '创建配备标准', 'code' => 'basic.equipment_standard.create', 'type' => 'write'],
                            ['id' => 'basic.equipment_standard.edit', 'name' => '编辑配备标准', 'code' => 'basic.equipment_standard.edit', 'type' => 'write'],
                            ['id' => 'basic.equipment_standard.delete', 'name' => '删除配备标准', 'code' => 'basic.equipment_standard.delete', 'type' => 'delete'],
                            ['id' => 'basic.equipment_standard.check_compliance', 'name' => '合规性检查', 'code' => 'basic.equipment_standard.check_compliance', 'type' => 'advanced', 'level' => 'high']
                        ]
                    ],
                    [
                        'id' => 'basic.textbook_version',
                        'name' => '📚 教材版本管理',
                        'code' => 'basic.textbook_version',
                        'type' => 'read',
                        'children' => [
                            ['id' => 'basic.textbook_version.view', 'name' => '查看教材版本', 'code' => 'basic.textbook_version.view', 'type' => 'read'],
                            ['id' => 'basic.textbook_version.create', 'name' => '创建教材版本', 'code' => 'basic.textbook_version.create', 'type' => 'write'],
                            ['id' => 'basic.textbook_version.edit', 'name' => '编辑教材版本', 'code' => 'basic.textbook_version.edit', 'type' => 'write'],
                            ['id' => 'basic.textbook_version.delete', 'name' => '删除教材版本', 'code' => 'basic.textbook_version.delete', 'type' => 'delete'],
                            ['id' => 'basic.textbook_version.batch_status', 'name' => '批量状态更新', 'code' => 'basic.textbook_version.batch_status', 'type' => 'write'],
                            ['id' => 'basic.textbook_version.sort', 'name' => '排序管理', 'code' => 'basic.textbook_version.sort', 'type' => 'write']
                        ]
                    ],
                    [
                        'id' => 'basic.textbook_chapter',
                        'name' => '📖 章节结构管理',
                        'code' => 'basic.textbook_chapter',
                        'type' => 'read',
                        'children' => [
                            ['id' => 'basic.textbook_chapter.view', 'name' => '查看章节结构', 'code' => 'basic.textbook_chapter.view', 'type' => 'read'],
                            ['id' => 'basic.textbook_chapter.tree', 'name' => '章节树形结构', 'code' => 'basic.textbook_chapter.tree', 'type' => 'read'],
                            ['id' => 'basic.textbook_chapter.create', 'name' => '创建章节', 'code' => 'basic.textbook_chapter.create', 'type' => 'write'],
                            ['id' => 'basic.textbook_chapter.edit', 'name' => '编辑章节', 'code' => 'basic.textbook_chapter.edit', 'type' => 'write'],
                            ['id' => 'basic.textbook_chapter.delete', 'name' => '删除章节', 'code' => 'basic.textbook_chapter.delete', 'type' => 'delete']
                        ]
                    ]
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
            ],
            [
                'id' => 'statistics',
                'name' => '统计报表',
                'code' => 'statistics',
                'children' => [
                    ['id' => 'statistics.view', 'name' => '查看统计', 'code' => 'statistics.view', 'type' => 'read'],
                    ['id' => 'statistics.dashboard', 'name' => '统计仪表板', 'code' => 'statistics.dashboard', 'type' => 'read'],
                    ['id' => 'statistics.experiment', 'name' => '实验统计', 'code' => 'statistics.experiment', 'type' => 'read'],
                    ['id' => 'statistics.equipment', 'name' => '设备统计', 'code' => 'statistics.equipment', 'type' => 'read'],
                    ['id' => 'statistics.user', 'name' => '用户统计', 'code' => 'statistics.user', 'type' => 'read'],
                    ['id' => 'statistics.performance', 'name' => '绩效统计', 'code' => 'statistics.performance', 'type' => 'read'],
                    ['id' => 'statistics.export', 'name' => '导出统计', 'code' => 'statistics.export', 'type' => 'advanced', 'level' => 'high']
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
            ['id' => 'equipment.maintenance', 'name' => '设备维修', 'code' => 'equipment.maintenance', 'type' => 'write', 'module' => 'equipment'],

            ['id' => 'statistics.view', 'name' => '查看统计', 'code' => 'statistics.view', 'type' => 'read', 'module' => 'statistics'],
            ['id' => 'statistics.dashboard', 'name' => '统计仪表板', 'code' => 'statistics.dashboard', 'type' => 'read', 'module' => 'statistics'],
            ['id' => 'statistics.experiment', 'name' => '实验统计', 'code' => 'statistics.experiment', 'type' => 'read', 'module' => 'statistics'],
            ['id' => 'statistics.equipment', 'name' => '设备统计', 'code' => 'statistics.equipment', 'type' => 'read', 'module' => 'statistics'],
            ['id' => 'statistics.user', 'name' => '用户统计', 'code' => 'statistics.user', 'type' => 'read', 'module' => 'statistics'],
            ['id' => 'statistics.performance', 'name' => '绩效统计', 'code' => 'statistics.performance', 'type' => 'read', 'module' => 'statistics'],
            ['id' => 'statistics.export', 'name' => '导出统计', 'code' => 'statistics.export', 'type' => 'advanced', 'module' => 'statistics']
        ];

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * 清除权限缓存
     */
    public function clearCache(): JsonResponse
    {
        try {
            $permissionService = app(\App\Services\PermissionService::class);
            $permissionService->clearPermissionCache();

            return response()->json([
                'success' => true,
                'message' => '权限缓存清除成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '清除权限缓存失败：' . $e->getMessage()
            ], 500);
        }
    }
}

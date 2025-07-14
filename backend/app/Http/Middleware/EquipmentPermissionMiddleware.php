<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipmentPermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '未认证'
            ], 401);
        }

        // 检查用户是否有指定权限
        if (!$this->hasPermission($user, $permission)) {
            return response()->json([
                'code' => 403,
                'message' => '权限不足'
            ], 403);
        }

        return $next($request);
    }

    /**
     * 检查用户权限
     */
    private function hasPermission($user, string $permission): bool
    {
        // 超级管理员拥有所有权限
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // 检查用户角色权限
        return $user->hasPermission($permission);
    }
}

class CheckEquipmentPermission
{
    /**
     * 设备管理权限映射
     */
    private static array $permissionMap = [
        // 设备档案权限
        'equipment.view' => '查看设备',
        'equipment.create' => '创建设备',
        'equipment.edit' => '编辑设备',
        'equipment.delete' => '删除设备',
        'equipment.import' => '批量导入设备',
        'equipment.export' => '导出设备数据',
        'equipment.photo.upload' => '上传设备照片',
        'equipment.photo.delete' => '删除设备照片',

        // 设备借用权限
        'equipment.borrow.view' => '查看借用记录',
        'equipment.borrow.create' => '创建借用申请',
        'equipment.borrow.edit' => '编辑借用申请',
        'equipment.borrow.approve' => '审批借用申请',
        'equipment.borrow.return' => '归还设备',
        'equipment.borrow.batch' => '批量操作借用',

        // 设备维修权限
        'equipment.maintenance.view' => '查看维修记录',
        'equipment.maintenance.create' => '创建维修申请',
        'equipment.maintenance.edit' => '编辑维修申请',
        'equipment.maintenance.assign' => '分配维修技师',
        'equipment.maintenance.complete' => '完成维修',
        'equipment.maintenance.statistics' => '查看维修统计',

        // 设备二维码权限
        'equipment.qrcode.generate' => '生成二维码',
        'equipment.qrcode.view' => '查看二维码',
        'equipment.qrcode.delete' => '删除二维码',
        'equipment.qrcode.batch' => '批量生成二维码',
    ];

    /**
     * 根据路由和方法获取所需权限
     */
    public static function getRequiredPermission(Request $request): ?string
    {
        $route = $request->route();
        $routeName = $route->getName();
        $method = $request->method();
        $action = $route->getActionName();

        // 设备档案管理权限
        if (str_contains($action, 'EquipmentController')) {
            return self::getEquipmentPermission($method, $routeName);
        }

        // 设备借用管理权限
        if (str_contains($action, 'EquipmentBorrowController')) {
            return self::getEquipmentBorrowPermission($method, $routeName);
        }

        // 设备维修管理权限
        if (str_contains($action, 'EquipmentMaintenanceController')) {
            return self::getEquipmentMaintenancePermission($method, $routeName);
        }

        // 设备二维码管理权限
        if (str_contains($action, 'EquipmentQrcodeController')) {
            return self::getEquipmentQrcodePermission($method, $routeName);
        }

        return null;
    }

    /**
     * 获取设备档案权限
     */
    private static function getEquipmentPermission(string $method, ?string $routeName): string
    {
        switch ($method) {
            case 'GET':
                if (str_contains($routeName, 'export')) {
                    return 'equipment.export';
                }
                return 'equipment.view';
            case 'POST':
                if (str_contains($routeName, 'batch-import')) {
                    return 'equipment.import';
                }
                if (str_contains($routeName, 'photos')) {
                    return 'equipment.photo.upload';
                }
                return 'equipment.create';
            case 'PUT':
            case 'PATCH':
                return 'equipment.edit';
            case 'DELETE':
                if (str_contains($routeName, 'photos')) {
                    return 'equipment.photo.delete';
                }
                return 'equipment.delete';
            default:
                return 'equipment.view';
        }
    }

    /**
     * 获取设备借用权限
     */
    private static function getEquipmentBorrowPermission(string $method, ?string $routeName): string
    {
        switch ($method) {
            case 'GET':
                return 'equipment.borrow.view';
            case 'POST':
                if (str_contains($routeName, 'review')) {
                    return 'equipment.borrow.approve';
                }
                if (str_contains($routeName, 'return')) {
                    return 'equipment.borrow.return';
                }
                if (str_contains($routeName, 'batch')) {
                    return 'equipment.borrow.batch';
                }
                return 'equipment.borrow.create';
            case 'PUT':
            case 'PATCH':
                return 'equipment.borrow.edit';
            case 'DELETE':
                return 'equipment.borrow.edit';
            default:
                return 'equipment.borrow.view';
        }
    }

    /**
     * 获取设备维修权限
     */
    private static function getEquipmentMaintenancePermission(string $method, ?string $routeName): string
    {
        switch ($method) {
            case 'GET':
                if (str_contains($routeName, 'statistics')) {
                    return 'equipment.maintenance.statistics';
                }
                return 'equipment.maintenance.view';
            case 'POST':
                if (str_contains($routeName, 'assign')) {
                    return 'equipment.maintenance.assign';
                }
                if (str_contains($routeName, 'complete')) {
                    return 'equipment.maintenance.complete';
                }
                return 'equipment.maintenance.create';
            case 'PUT':
            case 'PATCH':
                return 'equipment.maintenance.edit';
            case 'DELETE':
                return 'equipment.maintenance.edit';
            default:
                return 'equipment.maintenance.view';
        }
    }

    /**
     * 获取设备二维码权限
     */
    private static function getEquipmentQrcodePermission(string $method, ?string $routeName): string
    {
        switch ($method) {
            case 'GET':
                return 'equipment.qrcode.view';
            case 'POST':
                if (str_contains($routeName, 'batch')) {
                    return 'equipment.qrcode.batch';
                }
                return 'equipment.qrcode.generate';
            case 'DELETE':
                return 'equipment.qrcode.delete';
            default:
                return 'equipment.qrcode.view';
        }
    }

    /**
     * 获取权限描述
     */
    public static function getPermissionDescription(string $permission): string
    {
        return self::$permissionMap[$permission] ?? '未知权限';
    }

    /**
     * 获取所有权限列表
     */
    public static function getAllPermissions(): array
    {
        return self::$permissionMap;
    }
}

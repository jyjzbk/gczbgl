<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StatisticsPermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = 'statistics.view'): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '未认证'
            ], 401);
        }

        // 检查用户是否有统计报表权限
        if (!$this->hasStatisticsPermission($user, $permission)) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问统计报表功能'
            ], 403);
        }

        return $next($request);
    }

    /**
     * 检查用户统计权限
     */
    private function hasStatisticsPermission($user, string $permission): bool
    {
        // 超级管理员拥有所有权限
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // 检查具体的统计权限
        if ($user->hasPermission($permission)) {
            return true;
        }

        // 检查基础统计权限
        if ($user->hasPermission('statistics.view')) {
            return true;
        }

        // 根据不同的统计类型检查相关权限
        switch ($permission) {
            case 'statistics.experiment':
                return $user->hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']);
                
            case 'statistics.equipment':
                return $user->hasAnyPermission(['equipment.view', 'equipment.list']);
                
            case 'statistics.user':
                return $user->hasAnyPermission(['user', 'user.list']);
                
            case 'statistics.performance':
                // 绩效统计需要管理员级别权限
                return $user->hasRole(['admin', 'super_admin']);
                
            default:
                return false;
        }
    }

    /**
     * 根据路由获取所需权限
     */
    public static function getRequiredPermission(Request $request): string
    {
        $route = $request->route();
        $action = $route->getActionName();
        
        if (str_contains($action, 'getExperimentStats')) {
            return 'statistics.experiment';
        }
        
        if (str_contains($action, 'getEquipmentStats')) {
            return 'statistics.equipment';
        }
        
        if (str_contains($action, 'getUserActivityStats')) {
            return 'statistics.user';
        }
        
        if (str_contains($action, 'getOrganizationPerformance')) {
            return 'statistics.performance';
        }
        
        return 'statistics.view';
    }
}

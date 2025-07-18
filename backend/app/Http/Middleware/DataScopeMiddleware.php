<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PermissionService;

class DataScopeMiddleware
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '未认证'
            ], 401);
        }

        // 获取用户数据访问范围
        $dataScope = $this->permissionService->getUserDataScope($user);
        
        // 将数据范围添加到请求中，供控制器使用
        $request->merge([
            'data_scope' => $dataScope,
            'manageable_school_ids' => $dataScope['school_ids'],
            'manageable_region_ids' => $dataScope['region_ids'] ?? []
        ]);

        return $next($request);
    }

    /**
     * 为查询构建器添加数据范围过滤
     */
    public static function applyDataScope($query, Request $request, string $table = null)
    {
        $dataScope = $request->get('data_scope');

        if (!$dataScope || $dataScope['type'] === 'all') {
            return $query; // 超级管理员或无限制
        }

        $schoolIds = $dataScope['school_ids'];

        // 根据表类型应用不同的过滤逻辑
        switch ($table) {
            case 'equipments':
            case 'laboratories':
            case 'experiment_reservations':
            case 'experiment_records':
                // 这些表直接通过school_id过滤
                if (!empty($schoolIds)) {
                    $query->whereIn($table . '.school_id', $schoolIds);
                } else {
                    // 如果没有可访问的学校，返回空结果
                    $query->whereRaw('1 = 0');
                }
                break;

            case 'schools':
                // 学校表过滤
                if (!empty($schoolIds)) {
                    $query->whereIn('schools.id', $schoolIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
                
            case 'administrative_regions':
                // 区域表过滤
                $regionIds = $dataScope['region_ids'] ?? [];
                if (!empty($regionIds)) {
                    $query->whereIn('id', $regionIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
                
            case 'users':
                // 用户表过滤 - 只能管理下级用户
                $regionIds = $dataScope['region_ids'] ?? [];

                if (!empty($schoolIds) || !empty($regionIds)) {
                    $query->where(function($q) use ($schoolIds, $regionIds, $dataScope) {
                        // 学校用户 - 只显示管理范围内的学校用户
                        if (!empty($schoolIds)) {
                            $q->orWhere(function($sq) use ($schoolIds) {
                                $sq->where('organization_type', 'school')
                                   ->whereIn('organization_id', $schoolIds);
                            });
                            // 兼容旧数据 - 只显示管理范围内的学校用户
                            $q->orWhere(function($sq) use ($schoolIds) {
                                $sq->whereIn('school_id', $schoolIds)
                                   ->where(function($ssq) {
                                       $ssq->whereNull('organization_type')
                                          ->orWhere('organization_type', 'school');
                                   });
                            });
                        }

                        // 区域用户 - 根据数据范围类型决定显示哪些区域用户
                        if (!empty($regionIds)) {
                            $q->orWhere(function($sq) use ($regionIds, $dataScope) {
                                $sq->where('organization_type', 'region');

                                // 根据用户级别决定可以看到的区域用户
                                switch ($dataScope['type']) {
                                    case 'district': // 学区级管理员
                                        // 学区级管理员不应该看到区域用户，只看学校用户
                                        $sq->whereRaw('1 = 0');
                                        break;
                                    default:
                                        $sq->whereIn('organization_id', $regionIds);
                                }
                            });
                        }
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
                
            default:
                // 默认情况，如果表有school_id字段则过滤
                if (!empty($schoolIds)) {
                    $query->whereIn('school_id', $schoolIds);
                }
        }

        return $query;
    }

    /**
     * 检查用户是否可以访问指定资源
     */
    public static function canAccess(Request $request, string $resourceType, int $resourceId): bool
    {
        $dataScope = $request->get('data_scope');
        
        if (!$dataScope || $dataScope['type'] === 'all') {
            return true; // 超级管理员
        }

        switch ($resourceType) {
            case 'school':
                return in_array($resourceId, $dataScope['school_ids']);
                
            case 'region':
                $regionIds = $dataScope['region_ids'] ?? [];
                return in_array($resourceId, $regionIds);
                
            default:
                return false;
        }
    }

    /**
     * 验证创建资源的权限
     */
    public static function canCreate(Request $request, array $data): bool
    {
        $dataScope = $request->get('data_scope');
        
        if (!$dataScope || $dataScope['type'] === 'all') {
            return true; // 超级管理员
        }

        // 检查要创建的资源是否在用户的管理范围内
        if (isset($data['school_id'])) {
            return in_array($data['school_id'], $dataScope['school_ids']);
        }

        if (isset($data['region_id'])) {
            $regionIds = $dataScope['region_ids'] ?? [];
            return in_array($data['region_id'], $regionIds);
        }

        return false;
    }

    /**
     * 验证更新资源的权限
     */
    public static function canUpdate(Request $request, $model, array $data = []): bool
    {
        $dataScope = $request->get('data_scope');
        
        if (!$dataScope || $dataScope['type'] === 'all') {
            return true; // 超级管理员
        }

        // 检查现有资源是否在管理范围内
        if (isset($model->school_id)) {
            $canAccessCurrent = in_array($model->school_id, $dataScope['school_ids']);
        } elseif (isset($model->region_id)) {
            $regionIds = $dataScope['region_ids'] ?? [];
            $canAccessCurrent = in_array($model->region_id, $regionIds);
        } else {
            $canAccessCurrent = false;
        }

        if (!$canAccessCurrent) {
            return false;
        }

        // 如果要修改归属关系，检查新的归属是否在管理范围内
        if (!empty($data)) {
            if (isset($data['school_id']) && $data['school_id'] != $model->school_id) {
                return in_array($data['school_id'], $dataScope['school_ids']);
            }

            if (isset($data['region_id']) && $data['region_id'] != $model->region_id) {
                $regionIds = $dataScope['region_ids'] ?? [];
                return in_array($data['region_id'], $regionIds);
            }
        }

        return true;
    }
}

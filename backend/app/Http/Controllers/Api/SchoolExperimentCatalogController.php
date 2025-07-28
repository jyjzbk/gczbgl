<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExperimentCatalogSelection;
use App\Models\ExperimentCatalogDeletePermission;
use App\Models\ExperimentCatalog;
use App\Models\ExperimentCatalogDeletion;
use App\Models\School;
use App\Models\AdministrativeRegion;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolExperimentCatalogController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取学校实验目录选择
     */
    public function getSchoolSelection(Request $request): JsonResponse
    {
        try {
            $schoolId = $request->get('school_id');
            $user = Auth::user();

            // 如果没有指定学校ID，使用当前用户的学校
            if (!$schoolId && $user->organization_type === 'school') {
                $schoolId = $user->organization_id;
            }

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => '请指定学校ID'
                ], 400);
            }

            // 检查权限
            if (!$this->canManageSchool($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问该学校信息'
                ], 403);
            }

            $selection = SchoolExperimentCatalogSelection::with(['school:id,name', 'selectedBy:id,name'])
                ->where('school_id', $schoolId)
                ->first();

            if (!$selection) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => '学校尚未选择实验目录标准'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $selection
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取学校选择失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 设置学校实验目录选择
     */
    public function setSchoolSelection(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
                'selected_level' => ['required', Rule::in(['province', 'city', 'county'])],
                'selected_org_id' => 'required|integer|min:1',
                'selected_org_name' => 'required|string|max:100',
                'can_delete_experiments' => 'boolean',
                'selection_reason' => 'nullable|string|max:500'
            ]);

            // 检查权限
            if (!$this->canManageSchool($user, $validated['school_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理该学校'
                ], 403);
            }

            // 验证选择的组织是否存在
            $organization = AdministrativeRegion::find($validated['selected_org_id']);
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => '选择的组织不存在'
                ], 422);
            }

            DB::beginTransaction();

            $selection = SchoolExperimentCatalogSelection::setSchoolSelection(
                $validated['school_id'],
                $validated['selected_level'],
                $validated['selected_org_id'],
                $validated['selected_org_name'],
                $validated['can_delete_experiments'] ?? false,
                $validated['selection_reason'],
                $user->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '设置学校实验目录选择成功',
                'data' => $selection->load(['school:id,name', 'selectedBy:id,name'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '设置学校选择失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取可选择的标准组织列表
     */
    public function getAvailableStandards(Request $request): JsonResponse
    {
        try {
            $schoolId = $request->get('school_id');
            $user = Auth::user();

            // 如果没有指定学校ID，使用当前用户的学校
            if (!$schoolId && $user->organization_type === 'school') {
                $schoolId = $user->organization_id;
            }

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => '请指定学校ID'
                ], 400);
            }

            // 获取学校信息
            $school = School::find($schoolId);
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => '学校不存在'
                ], 404);
            }

            $standards = [];

            // 获取省级标准
            if ($school->province_id) {
                $province = AdministrativeRegion::find($school->province_id);
                if ($province) {
                    $catalogCount = ExperimentCatalog::where('management_level', 1)
                        ->where('created_by_org_id', $province->id)
                        ->where('status', 1)
                        ->count();
                    
                    if ($catalogCount > 0) {
                        $standards[] = [
                            'level' => 'province',
                            'level_name' => '省级',
                            'org_id' => $province->id,
                            'org_name' => $province->name,
                            'catalog_count' => $catalogCount,
                            'description' => '使用省级统一实验目录标准'
                        ];
                    }
                }
            }

            // 获取市级标准
            if ($school->city_id) {
                $city = AdministrativeRegion::find($school->city_id);
                if ($city) {
                    $catalogCount = ExperimentCatalog::where('management_level', 2)
                        ->where('created_by_org_id', $city->id)
                        ->where('status', 1)
                        ->count();
                    
                    if ($catalogCount > 0) {
                        $standards[] = [
                            'level' => 'city',
                            'level_name' => '市级',
                            'org_id' => $city->id,
                            'org_name' => $city->name,
                            'catalog_count' => $catalogCount,
                            'description' => '使用市级实验目录标准'
                        ];
                    }
                }
            }

            // 获取区县级标准
            if ($school->county_id) {
                $county = AdministrativeRegion::find($school->county_id);
                if ($county) {
                    $catalogCount = ExperimentCatalog::where('management_level', 3)
                        ->where('created_by_org_id', $county->id)
                        ->where('status', 1)
                        ->count();
                    
                    if ($catalogCount > 0) {
                        $standards[] = [
                            'level' => 'county',
                            'level_name' => '区县级',
                            'org_id' => $county->id,
                            'org_name' => $county->name,
                            'catalog_count' => $catalogCount,
                            'description' => '使用区县级实验目录标准'
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $standards
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取可选标准失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取学校可用的实验目录
     */
    public function getAvailableCatalogs(Request $request): JsonResponse
    {
        try {
            $schoolId = $request->get('school_id');
            $user = Auth::user();

            // 如果没有指定学校ID，使用当前用户的学校
            if (!$schoolId && $user->organization_type === 'school') {
                $schoolId = $user->organization_id;
            }

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => '请指定学校ID'
                ], 400);
            }

            // 检查权限
            if (!$this->canViewSchool($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校信息'
                ], 403);
            }

            $catalogs = SchoolExperimentCatalogSelection::getAvailableExperimentCatalogs($schoolId);

            // 获取已删除的实验目录
            $deletedCatalogIds = ExperimentCatalogDeletion::where('deleted_by_org_id', $schoolId)
                ->where('deleted_by_org_type', 'school')
                ->whereNull('restored_at')
                ->pluck('catalog_id')
                ->toArray();

            // 标记已删除的实验
            $catalogs = $catalogs->map(function ($catalog) use ($deletedCatalogIds) {
                $catalog->is_deleted_by_school = in_array($catalog->id, $deletedCatalogIds);
                return $catalog;
            });

            return response()->json([
                'success' => true,
                'data' => $catalogs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取可用实验目录失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除实验目录（学校级别）
     */
    public function deleteExperimentCatalog(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
                'catalog_id' => 'required|integer|exists:experiment_catalogs,id',
                'delete_reason' => 'required|string|max:500'
            ]);

            // 检查权限
            if (!$this->canManageSchool($user, $validated['school_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理该学校'
                ], 403);
            }

            // 检查学校是否可以删除该实验
            $deleteCheck = ExperimentCatalogDeletePermission::canSchoolDeleteCatalog(
                $validated['school_id'],
                $validated['catalog_id']
            );

            if (!$deleteCheck['can_delete']) {
                return response()->json([
                    'success' => false,
                    'message' => $deleteCheck['reason']
                ], 403);
            }

            // 检查是否已经删除过
            $existingDeletion = ExperimentCatalogDeletion::where('catalog_id', $validated['catalog_id'])
                ->where('deleted_by_org_id', $validated['school_id'])
                ->where('deleted_by_org_type', 'school')
                ->whereNull('restored_at')
                ->first();

            if ($existingDeletion) {
                return response()->json([
                    'success' => false,
                    'message' => '该实验目录已经被删除'
                ], 422);
            }

            DB::beginTransaction();

            // 创建删除记录
            ExperimentCatalogDeletion::create([
                'catalog_id' => $validated['catalog_id'],
                'deleted_by_org_type' => 'school',
                'deleted_by_org_id' => $validated['school_id'],
                'deleted_by_user_id' => $user->id,
                'delete_reason' => $validated['delete_reason'],
                'deleted_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '删除实验目录成功'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '删除实验目录失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 恢复实验目录
     */
    public function restoreExperimentCatalog(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
                'catalog_id' => 'required|integer|exists:experiment_catalogs,id'
            ]);

            // 检查权限
            if (!$this->canManageSchool($user, $validated['school_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理该学校'
                ], 403);
            }

            // 查找删除记录
            $deletion = ExperimentCatalogDeletion::where('catalog_id', $validated['catalog_id'])
                ->where('deleted_by_org_id', $validated['school_id'])
                ->where('deleted_by_org_type', 'school')
                ->whereNull('restored_at')
                ->first();

            if (!$deletion) {
                return response()->json([
                    'success' => false,
                    'message' => '未找到删除记录'
                ], 404);
            }

            DB::beginTransaction();

            // 恢复实验目录
            $deletion->update([
                'restored_at' => now(),
                'restored_by' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '恢复实验目录成功'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '恢复实验目录失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取学校删除的实验目录列表
     */
    public function getDeletedCatalogs(Request $request): JsonResponse
    {
        try {
            $schoolId = $request->get('school_id');
            $user = Auth::user();

            // 如果没有指定学校ID，使用当前用户的学校
            if (!$schoolId && $user->organization_type === 'school') {
                $schoolId = $user->organization_id;
            }

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => '请指定学校ID'
                ], 400);
            }

            // 检查权限
            if (!$this->canViewSchool($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校信息'
                ], 403);
            }

            $deletions = ExperimentCatalogDeletion::with([
                'catalog:id,name,code,type,grade,semester',
                'deletedByUser:id,name'
            ])
            ->where('deleted_by_org_id', $schoolId)
            ->where('deleted_by_org_type', 'school')
            ->orderBy('deleted_at', 'desc')
            ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $deletions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取删除记录失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查是否可以管理学校
     */
    private function canManageSchool($user, int $schoolId): bool
    {
        // 学校管理员只能管理自己的学校
        if ($user->organization_type === 'school') {
            return $user->organization_id === $schoolId;
        }

        // 上级管理员可以管理下级学校
        $school = School::find($schoolId);
        if (!$school) {
            return false;
        }

        return $this->permissionService->canAccessSchool($user, $school);
    }

    /**
     * 检查是否可以查看学校
     */
    private function canViewSchool($user, int $schoolId): bool
    {
        return $this->canManageSchool($user, $schoolId);
    }
}

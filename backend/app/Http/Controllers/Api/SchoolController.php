<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Http\Middleware\DataScopeMiddleware;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    /**
     * 获取学校列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = School::with('region');

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'schools');

        // 搜索条件
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 按学校类型筛选
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 按管理级别筛选
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // 按区域筛选
        if ($request->filled('region_id')) {
            // 验证用户是否可以访问指定区域
            if (DataScopeMiddleware::canAccess($request, 'region', $request->region_id)) {
                $query->where('region_id', $request->region_id);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定区域的数据'
                ], 403);
            }
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $schools = $query->orderBy('id', 'desc')->paginate($perPage);

        // 添加类型名称
        $schools->getCollection()->transform(function ($school) {
            $school->type_name = $school->getTypeNameAttribute();
            $school->level_name = $this->getLevelName($school->level);
            $school->region_name = $school->region ? $school->region->name : '';
            return $school;
        });

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'data' => $schools->items(),
                'pagination' => [
                    'current_page' => $schools->currentPage(),
                    'last_page' => $schools->lastPage(),
                    'per_page' => $schools->perPage(),
                    'total' => $schools->total()
                ]
            ]
        ]);
    }

    /**
     * 创建学校
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:schools',
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'level' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'region_id' => 'required|integer|exists:administrative_regions,id',
            'address' => 'required|string',
            'contact_person' => 'required|string|max:50',
            'contact_phone' => 'required|string|max:20',
            'student_count' => 'integer|min:0',
            'class_count' => 'integer|min:0',
            'teacher_count' => 'integer|min:0',
            'status' => 'integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'name', 'code', 'type', 'level', 'region_id', 'address',
            'contact_person', 'contact_phone', 'student_count',
            'class_count', 'teacher_count', 'status'
        ]);

        // 验证创建权限
        if (!DataScopeMiddleware::canCreate($request, ['region_id' => $data['region_id']])) {
            return response()->json([
                'code' => 403,
                'message' => '无权限在指定区域创建学校'
            ], 403);
        }

        $school = School::create($data);
        $school->load('region');
        $school->type_name = $school->getTypeNameAttribute();
        $school->level_name = $this->getLevelName($school->level);
        $school->region_name = $school->region ? $school->region->name : '';

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $school
        ], 201);
    }

    /**
     * 获取学校详情
     */
    public function show(Request $request, School $school): JsonResponse
    {
        // 验证访问权限
        if (!DataScopeMiddleware::canAccess($request, 'school', $school->id)) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问该学校信息'
            ], 403);
        }

        $school->load('region');
        $school->type_name = $school->getTypeNameAttribute();
        $school->level_name = $this->getLevelName($school->level);
        $school->region_name = $school->region ? $school->region->name : '';

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $school
        ]);
    }

    /**
     * 更新学校
     */
    public function update(Request $request, School $school): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('schools')->ignore($school->id)
            ],
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'level' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'region_id' => 'required|integer|exists:administrative_regions,id',
            'address' => 'required|string',
            'contact_person' => 'required|string|max:50',
            'contact_phone' => 'required|string|max:20',
            'student_count' => 'integer|min:0',
            'class_count' => 'integer|min:0',
            'teacher_count' => 'integer|min:0',
            'status' => 'integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'name', 'code', 'type', 'level', 'region_id', 'address',
            'contact_person', 'contact_phone', 'student_count',
            'class_count', 'teacher_count', 'status'
        ]);

        // 验证更新权限
        if (!DataScopeMiddleware::canUpdate($request, $school, $data)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限更新该学校或修改其归属'
            ], 403);
        }

        $school->update($data);
        $school->load('region');
        $school->type_name = $school->getTypeNameAttribute();
        $school->level_name = $this->getLevelName($school->level);
        $school->region_name = $school->region ? $school->region->name : '';

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $school
        ]);
    }

    /**
     * 删除学校
     */
    public function destroy(Request $request, School $school): JsonResponse
    {
        // 验证删除权限
        if (!DataScopeMiddleware::canAccess($request, 'school', $school->id)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限删除该学校'
            ], 403);
        }

        // 检查是否有关联的用户
        if ($school->users()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该学校下有用户，无法删除'
            ], 400);
        }

        // 检查是否有关联的实验室
        if ($school->laboratories()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该学校下有实验室，无法删除'
            ], 400);
        }

        // 检查是否有关联的设备
        if ($school->equipments()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该学校下有设备，无法删除'
            ], 400);
        }

        $school->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 获取学校选项（用于下拉框）
     */
    public function options(Request $request): JsonResponse
    {
        $query = School::where('status', 1);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'schools');

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 按区域筛选
        if ($request->filled('region_id')) {
            // 验证用户是否可以访问指定区域
            if (DataScopeMiddleware::canAccess($request, 'region', $request->region_id)) {
                $query->where('region_id', $request->region_id);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定区域的数据'
                ], 403);
            }
        }

        $schools = $query->orderBy('name')
                        ->get(['id', 'name', 'code']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $schools
        ]);
    }

    /**
     * 获取管理级别名称
     */
    private function getLevelName(int $level): string
    {
        $levels = [
            1 => '省直',
            2 => '市直',
            3 => '区县直',
            4 => '学区'
        ];

        return $levels[$level] ?? '未知';
    }
}

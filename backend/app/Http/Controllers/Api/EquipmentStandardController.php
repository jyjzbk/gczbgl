<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentStandard;
use App\Http\Middleware\DataScopeMiddleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentStandardController extends Controller
{
    /**
     * 获取配备标准列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = EquipmentStandard::query();

        // 按制定机构筛选
        if ($request->filled('authority_type')) {
            $query->byAuthority($request->authority_type);
        }

        // 按学段筛选
        if ($request->filled('stage')) {
            $query->byStage($request->stage);
        }

        // 按学科筛选
        if ($request->filled('subject_code')) {
            $query->bySubject($request->subject_code);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active(); // 默认只显示启用的
        }

        // 只显示有效期内的
        if ($request->boolean('effective_only', true)) {
            $query->effective();
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }

        // 排序
        $query->orderBy('authority_type')
              ->orderBy('stage')
              ->orderBy('subject_code')
              ->orderBy('version', 'desc');

        // 分页
        $data = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $data
        ]);
    }

    /**
     * 创建配备标准
     */
    public function store(Request $request): JsonResponse
    {
        // 权限检查：只有省级管理员可以创建
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理配备标准'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:100|unique:equipment_standards,code',
            'authority_type' => ['required', 'integer', Rule::in([1, 2])],
            'stage' => ['required', 'integer', Rule::in([1, 2, 3])],
            'subject_code' => 'required|string|max:50',
            'subject_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'equipment_list' => 'required|array',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'status' => 'boolean'
        ]);

        $standard = EquipmentStandard::create($validated);

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $standard
        ], 201);
    }

    /**
     * 显示指定配备标准
     */
    public function show(EquipmentStandard $equipmentStandard): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $equipmentStandard
        ]);
    }

    /**
     * 更新配备标准
     */
    public function update(Request $request, EquipmentStandard $equipmentStandard): JsonResponse
    {
        // 权限检查：只有省级管理员可以更新
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理配备标准'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => ['required', 'string', 'max:100', Rule::unique('equipment_standards')->ignore($equipmentStandard->id)],
            'authority_type' => ['required', 'integer', Rule::in([1, 2])],
            'stage' => ['required', 'integer', Rule::in([1, 2, 3])],
            'subject_code' => 'required|string|max:50',
            'subject_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'equipment_list' => 'required|array',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'status' => 'boolean'
        ]);

        $equipmentStandard->update($validated);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $equipmentStandard
        ]);
    }

    /**
     * 删除配备标准
     */
    public function destroy(Request $request, EquipmentStandard $equipmentStandard): JsonResponse
    {
        // 权限检查：只有省级管理员可以删除
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理配备标准'
            ], 403);
        }

        $equipmentStandard->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 获取学科列表
     */
    public function getSubjects(): JsonResponse
    {
        $subjects = [
            // 小学
            ['code' => 'SCIENCE', 'name' => '科学', 'stages' => [1]],
            ['code' => 'MUSIC', 'name' => '音乐', 'stages' => [1, 2, 3]],
            ['code' => 'ART', 'name' => '美术', 'stages' => [1, 2, 3]],
            ['code' => 'PE', 'name' => '体育', 'stages' => [1, 2, 3]],

            // 初中高中
            ['code' => 'PHYSICS', 'name' => '物理', 'stages' => [2, 3]],
            ['code' => 'CHEMISTRY', 'name' => '化学', 'stages' => [2, 3]],
            ['code' => 'BIOLOGY', 'name' => '生物', 'stages' => [2, 3]],
            ['code' => 'GEOGRAPHY', 'name' => '地理', 'stages' => [2, 3]],
            ['code' => 'HISTORY', 'name' => '历史', 'stages' => [2, 3]],
        ];

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $subjects
        ]);
    }

    /**
     * 检查学校设备达标情况
     */
    public function checkCompliance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'standard_id' => 'required|exists:equipment_standards,id'
        ]);

        // 验证数据权限
        if (!DataScopeMiddleware::canAccess($request, 'school', $validated['school_id'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问指定学校的数据'
            ], 403);
        }

        try {
            $school = \App\Models\School::findOrFail($validated['school_id']);
            $standard = EquipmentStandard::findOrFail($validated['standard_id']);

            // 获取学校现有设备
            $schoolEquipments = \App\Models\Equipment::where('school_id', $school->id)
                ->with('category')
                ->get()
                ->groupBy('category.name');

            $complianceResult = [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'type_name' => $school->type_name
                ],
                'standard' => [
                    'id' => $standard->id,
                    'name' => $standard->name,
                    'authority_type_text' => $standard->authority_type_text,
                    'stage_text' => $standard->stage_text,
                    'subject_name' => $standard->subject_name
                ],
                'categories' => [],
                'overall_compliance' => 0,
                'total_required' => 0,
                'total_actual' => 0,
                'compliance_rate' => 0
            ];

            $totalRequired = 0;
            $totalCompliant = 0;

            foreach ($standard->equipment_list as $category) {
                $categoryResult = [
                    'category' => $category['category'],
                    'items' => [],
                    'category_compliance' => 0,
                    'category_required' => 0,
                    'category_compliant' => 0
                ];

                $categoryRequired = 0;
                $categoryCompliant = 0;

                foreach ($category['items'] as $item) {
                    $requiredQuantity = $item['quantity'];
                    $actualQuantity = 0;

                    // 查找学校对应设备的数量
                    if (isset($schoolEquipments[$category['category']])) {
                        $matchingEquipments = $schoolEquipments[$category['category']]->filter(function($equipment) use ($item) {
                            return stripos($equipment->name, $item['name']) !== false;
                        });
                        $actualQuantity = $matchingEquipments->sum('quantity');
                    }

                    $isCompliant = $actualQuantity >= $requiredQuantity;
                    $complianceRate = $requiredQuantity > 0 ? min(($actualQuantity / $requiredQuantity) * 100, 100) : 100;

                    $categoryResult['items'][] = [
                        'name' => $item['name'],
                        'specification' => $item['specification'],
                        'required_quantity' => $requiredQuantity,
                        'actual_quantity' => $actualQuantity,
                        'unit' => $item['unit'],
                        'is_compliant' => $isCompliant,
                        'compliance_rate' => round($complianceRate, 2),
                        'shortage' => max(0, $requiredQuantity - $actualQuantity)
                    ];

                    $categoryRequired++;
                    if ($isCompliant) {
                        $categoryCompliant++;
                    }
                }

                $categoryResult['category_required'] = $categoryRequired;
                $categoryResult['category_compliant'] = $categoryCompliant;
                $categoryResult['category_compliance'] = $categoryRequired > 0 ?
                    round(($categoryCompliant / $categoryRequired) * 100, 2) : 100;

                $complianceResult['categories'][] = $categoryResult;
                $totalRequired += $categoryRequired;
                $totalCompliant += $categoryCompliant;
            }

            $complianceResult['total_required'] = $totalRequired;
            $complianceResult['total_actual'] = $totalCompliant;
            $complianceResult['compliance_rate'] = $totalRequired > 0 ?
                round(($totalCompliant / $totalRequired) * 100, 2) : 100;
            $complianceResult['overall_compliance'] = $complianceResult['compliance_rate'];

            return response()->json([
                'code' => 200,
                'message' => '检查完成',
                'data' => $complianceResult
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '检查失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查是否有省级权限
     */
    private function hasProvincePermission(Request $request): bool
    {
        $user = $request->user();
        
        // 检查用户是否有省级管理员角色
        return $user && $user->hasRole('province_admin');
    }
}

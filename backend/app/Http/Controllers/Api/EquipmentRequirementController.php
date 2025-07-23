<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentEquipmentRequirement;
use App\Models\ExperimentCatalog;
use App\Models\Equipment;
use App\Services\EquipmentRequirementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EquipmentRequirementController extends Controller
{
    protected $equipmentRequirementService;

    public function __construct(EquipmentRequirementService $equipmentRequirementService)
    {
        $this->equipmentRequirementService = $equipmentRequirementService;
    }

    /**
     * 获取实验目录的器材需求配置
     */
    public function index(Request $request, $catalogId): JsonResponse
    {
        try {
            // 验证实验目录是否存在
            $catalog = ExperimentCatalog::findOrFail($catalogId);

            // 获取器材需求配置
            $query = ExperimentEquipmentRequirement::with(['equipment' => function($query) {
                $query->select('id', 'name', 'code', 'model', 'brand', 'quantity', 'unit', 'status');
            }])
            ->where('catalog_id', $catalogId);

            // 只获取激活的配置
            if ($request->boolean('active_only', true)) {
                $query->where('is_active', true);
            }

            $requirements = $query->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $requirements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取器材需求配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量更新器材需求配置
     */
    public function batchStore(Request $request, $catalogId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'requirements' => 'required|array|min:1',
            'requirements.*.equipment_id' => 'required|exists:equipments,id',
            'requirements.*.required_quantity' => 'required|integer|min:1',
            'requirements.*.min_quantity' => 'nullable|integer|min:1',
            'requirements.*.is_required' => 'boolean',
            'requirements.*.calculation_type' => 'required|in:fixed,per_group,per_student',
            'requirements.*.group_size' => 'nullable|integer|min:1',
            'requirements.*.usage_note' => 'nullable|string|max:500',
            'requirements.*.safety_note' => 'nullable|string|max:500',
            'requirements.*.sort_order' => 'nullable|integer|min:0',
            'replace_all' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 验证实验目录是否存在
            $catalog = ExperimentCatalog::findOrFail($catalogId);

            // 如果是替换全部，先删除现有配置
            if ($request->boolean('replace_all', false)) {
                ExperimentEquipmentRequirement::where('catalog_id', $catalogId)->delete();
            }

            $requirements = $request->requirements;
            $createdRequirements = [];

            foreach ($requirements as $index => $requirementData) {
                $requirement = ExperimentEquipmentRequirement::updateOrCreate(
                    [
                        'catalog_id' => $catalogId,
                        'equipment_id' => $requirementData['equipment_id']
                    ],
                    [
                        'required_quantity' => $requirementData['required_quantity'],
                        'min_quantity' => $requirementData['min_quantity'] ?? 1,
                        'is_required' => $requirementData['is_required'] ?? true,
                        'calculation_type' => $requirementData['calculation_type'],
                        'group_size' => $requirementData['group_size'] ?? null,
                        'usage_note' => $requirementData['usage_note'] ?? null,
                        'safety_note' => $requirementData['safety_note'] ?? null,
                        'sort_order' => $requirementData['sort_order'] ?? $index,
                        'is_active' => true,
                        'created_by' => auth()->id()
                    ]
                );

                $createdRequirements[] = $requirement->load('equipment');
            }

            return response()->json([
                'success' => true,
                'message' => '器材需求配置保存成功',
                'data' => $createdRequirements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '保存器材需求配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取智能推荐器材
     */
    public function getRecommendations(Request $request, $catalogId): JsonResponse
    {
        try {
            $catalog = ExperimentCatalog::findOrFail($catalogId);
            
            $recommendations = $this->equipmentRequirementService->getRecommendations($catalog);

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取推荐器材失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 从其他实验复制配置
     */
    public function copyFromCatalog(Request $request, $catalogId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source_catalog_id' => 'required|exists:experiment_catalogs,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sourceCatalogId = $request->source_catalog_id;
            
            // 获取源实验的器材配置
            $sourceRequirements = ExperimentEquipmentRequirement::where('catalog_id', $sourceCatalogId)
                ->where('is_active', true)
                ->get();

            if ($sourceRequirements->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => '源实验目录没有器材配置'
                ], 400);
            }

            // 复制配置到目标实验
            $copiedRequirements = [];
            foreach ($sourceRequirements as $sourceReq) {
                $requirement = ExperimentEquipmentRequirement::updateOrCreate(
                    [
                        'catalog_id' => $catalogId,
                        'equipment_id' => $sourceReq->equipment_id
                    ],
                    [
                        'required_quantity' => $sourceReq->required_quantity,
                        'min_quantity' => $sourceReq->min_quantity,
                        'is_required' => $sourceReq->is_required,
                        'calculation_type' => $sourceReq->calculation_type,
                        'group_size' => $sourceReq->group_size,
                        'usage_note' => $sourceReq->usage_note,
                        'safety_note' => $sourceReq->safety_note,
                        'sort_order' => $sourceReq->sort_order,
                        'is_active' => true,
                        'created_by' => auth()->id()
                    ]
                );

                $copiedRequirements[] = $requirement->load('equipment');
            }

            return response()->json([
                'success' => true,
                'message' => '器材配置复制成功',
                'data' => $copiedRequirements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '复制器材配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新排序
     */
    public function updateSortOrder(Request $request, $catalogId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sort_data' => 'required|array|min:1',
            'sort_data.*.equipment_id' => 'required|integer',
            'sort_data.*.sort_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sortData = $request->sort_data;

            foreach ($sortData as $item) {
                ExperimentEquipmentRequirement::where('catalog_id', $catalogId)
                    ->where('equipment_id', $item['equipment_id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => '排序更新成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新排序失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新单个器材需求
     */
    public function update(Request $request, $catalogId, $requirementId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'required_quantity' => 'nullable|integer|min:1',
            'min_quantity' => 'nullable|integer|min:1',
            'is_required' => 'nullable|boolean',
            'calculation_type' => 'nullable|in:fixed,per_group,per_student',
            'group_size' => 'nullable|integer|min:1',
            'usage_note' => 'nullable|string|max:500',
            'safety_note' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $requirement = ExperimentEquipmentRequirement::where('catalog_id', $catalogId)
                ->where('id', $requirementId)
                ->firstOrFail();

            $requirement->update($request->only([
                'required_quantity', 'min_quantity', 'is_required', 'calculation_type',
                'group_size', 'usage_note', 'safety_note', 'sort_order', 'is_active'
            ]));

            return response()->json([
                'success' => true,
                'message' => '器材需求更新成功',
                'data' => $requirement->load('equipment')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新器材需求失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除器材需求
     */
    public function destroy($catalogId, $requirementId): JsonResponse
    {
        try {
            $requirement = ExperimentEquipmentRequirement::where('catalog_id', $catalogId)
                ->where('id', $requirementId)
                ->firstOrFail();

            $requirement->delete();

            return response()->json([
                'success' => true,
                'message' => '器材需求删除成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '删除器材需求失败：' . $e->getMessage()
            ], 500);
        }
    }
}

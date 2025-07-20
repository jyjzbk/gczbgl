<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentRequirementTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EquipmentRequirementTemplateController extends Controller
{
    /**
     * 获取模板列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = EquipmentRequirementTemplate::with(['subject', 'creator', 'school']);

        // 筛选条件
        if ($request->has('subject_id')) {
            $query->bySubject($request->subject_id);
        }

        if ($request->has('experiment_type')) {
            $query->byType($request->experiment_type);
        }

        if ($request->has('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        // 权限过滤：只能看到公开模板或自己学校的模板
        $user = auth()->user();
        $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
              ->orWhere('school_id', $user->school_id);
        });

        $templates = $query->orderByDesc('use_count')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'code' => 200,
            'data' => $templates
        ]);
    }

    /**
     * 创建模板
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'subject_id' => 'nullable|exists:subjects,id',
            'experiment_type' => 'required|in:演示实验,分组实验,探究实验,综合实验',
            'equipment_list' => 'required|array|min:1',
            'equipment_list.*.equipment_id' => 'required|exists:equipment,id',
            'equipment_list.*.required_quantity' => 'required|integer|min:1',
            'equipment_list.*.calculation_type' => 'required|in:fixed,per_group,per_student',
            'is_public' => 'boolean'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['school_id'] = auth()->user()->school_id;

        $template = EquipmentRequirementTemplate::create($validated);

        return response()->json([
            'code' => 200,
            'message' => '模板创建成功',
            'data' => $template->load(['subject', 'creator'])
        ]);
    }

    /**
     * 获取模板详情
     */
    public function show(EquipmentRequirementTemplate $equipmentRequirementTemplate): JsonResponse
    {
        $template = $equipmentRequirementTemplate->load(['subject', 'creator', 'school']);

        // 权限检查
        $user = auth()->user();
        if (!$template->is_public && $template->school_id !== $user->school_id) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问此模板'
            ], 403);
        }

        return response()->json([
            'code' => 200,
            'data' => $template
        ]);
    }

    /**
     * 更新模板
     */
    public function update(Request $request, EquipmentRequirementTemplate $equipmentRequirementTemplate): JsonResponse
    {
        // 权限检查：只能修改自己创建的模板
        if ($equipmentRequirementTemplate->created_by !== auth()->id()) {
            return response()->json([
                'code' => 403,
                'message' => '无权修改此模板'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'subject_id' => 'nullable|exists:subjects,id',
            'experiment_type' => 'required|in:演示实验,分组实验,探究实验,综合实验',
            'equipment_list' => 'required|array|min:1',
            'equipment_list.*.equipment_id' => 'required|exists:equipment,id',
            'equipment_list.*.required_quantity' => 'required|integer|min:1',
            'equipment_list.*.calculation_type' => 'required|in:fixed,per_group,per_student',
            'is_public' => 'boolean'
        ]);

        $equipmentRequirementTemplate->update($validated);

        return response()->json([
            'code' => 200,
            'message' => '模板更新成功',
            'data' => $equipmentRequirementTemplate->load(['subject', 'creator'])
        ]);
    }

    /**
     * 删除模板
     */
    public function destroy(EquipmentRequirementTemplate $equipmentRequirementTemplate): JsonResponse
    {
        // 权限检查：只能删除自己创建的模板
        if ($equipmentRequirementTemplate->created_by !== auth()->id()) {
            return response()->json([
                'code' => 403,
                'message' => '无权删除此模板'
            ], 403);
        }

        $equipmentRequirementTemplate->delete();

        return response()->json([
            'code' => 200,
            'message' => '模板删除成功'
        ]);
    }

    /**
     * 应用模板到实验目录
     */
    public function applyTemplate(Request $request, EquipmentRequirementTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'catalog_id' => 'required|exists:experiment_catalogs,id'
        ]);

        try {
            $service = app(EquipmentRequirementService::class);
            $results = $service->applyTemplate($validated['catalog_id'], $template->id);

            // 增加模板使用次数
            $template->incrementUseCount();

            return response()->json([
                'code' => 200,
                'message' => '模板应用成功',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '模板应用失败：' . $e->getMessage()
            ], 500);
        }
    }
}


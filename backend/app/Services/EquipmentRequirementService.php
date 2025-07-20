<?php

namespace App\Services;

use App\Models\ExperimentEquipmentRequirement;
use App\Models\ExperimentCatalog;
use App\Models\Equipment;
use App\Models\EquipmentRequirementTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentRequirementService
{
    /**
     * 根据实验目录和学生人数生成器材需求清单
     */
    public function generateRequirements(int $catalogId, int $studentCount): array
    {
        $catalog = ExperimentCatalog::findOrFail($catalogId);

        // 获取实验的器材需求配置
        $requirements = ExperimentEquipmentRequirement::with('equipment')
            ->where('catalog_id', $catalogId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $equipmentList = [];

        foreach ($requirements as $requirement) {
            $quantity = $this->calculateQuantity($requirement, $studentCount);

            if ($quantity > 0) {
                $equipmentList[] = [
                    'equipment_id' => $requirement->equipment_id,
                    'equipment_name' => $requirement->equipment->name,
                    'equipment_code' => $requirement->equipment->code,
                    'required_quantity' => $quantity,
                    'min_quantity' => $this->calculateMinQuantity($requirement, $studentCount),
                    'is_required' => $requirement->is_required,
                    'calculation_type' => $requirement->calculation_type,
                    'usage_note' => $requirement->usage_note,
                    'safety_note' => $requirement->safety_note,
                    'available_quantity' => $this->getAvailableQuantity($requirement->equipment_id),
                    'shortage' => max(0, $quantity - $this->getAvailableQuantity($requirement->equipment_id))
                ];
            }
        }

        return $equipmentList;
    }

    /**
     * 为实验目录批量配置器材需求
     */
    public function batchConfigureRequirements(int $catalogId, array $requirements): array
    {
        return DB::transaction(function () use ($catalogId, $requirements) {
            $catalog = ExperimentCatalog::findOrFail($catalogId);
            $results = [];
            
            // 删除现有配置（如果需要完全重新配置）
            if (request('replace_all', false)) {
                ExperimentEquipmentRequirement::where('catalog_id', $catalogId)->delete();
            }

            foreach ($requirements as $requirement) {
                try {
                    $result = $this->createOrUpdateRequirement($catalogId, $requirement);
                    $results[] = [
                        'success' => true,
                        'equipment_id' => $requirement['equipment_id'],
                        'equipment_name' => $result->equipment->name,
                        'action' => $result->wasRecentlyCreated ? 'created' : 'updated'
                    ];
                } catch (\Exception $e) {
                    Log::error('配置器材需求失败', [
                        'catalog_id' => $catalogId,
                        'requirement' => $requirement,
                        'error' => $e->getMessage()
                    ]);
                    
                    $results[] = [
                        'success' => false,
                        'equipment_id' => $requirement['equipment_id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * 创建或更新器材需求配置
     */
    private function createOrUpdateRequirement(int $catalogId, array $data): ExperimentEquipmentRequirement
    {
        // 验证器材是否存在
        $equipment = Equipment::findOrFail($data['equipment_id']);
        
        // 数据验证和处理
        $validated = $this->validateRequirementData($data);
        $validated['catalog_id'] = $catalogId;
        $validated['created_by'] = auth()->id();

        return ExperimentEquipmentRequirement::updateOrCreate(
            [
                'catalog_id' => $catalogId,
                'equipment_id' => $data['equipment_id']
            ],
            $validated
        );
    }

    /**
     * 验证器材需求数据
     */
    private function validateRequirementData(array $data): array
    {
        $validated = [
            'equipment_id' => $data['equipment_id'],
            'required_quantity' => max(1, (int)($data['required_quantity'] ?? 1)),
            'min_quantity' => max(1, (int)($data['min_quantity'] ?? 1)),
            'is_required' => (bool)($data['is_required'] ?? true),
            'calculation_type' => $data['calculation_type'] ?? 'fixed',
            'usage_note' => $data['usage_note'] ?? null,
            'safety_note' => $data['safety_note'] ?? null,
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active' => (bool)($data['is_active'] ?? true)
        ];

        // 验证计算方式
        if (!in_array($validated['calculation_type'], ['fixed', 'per_group', 'per_student'])) {
            $validated['calculation_type'] = 'fixed';
        }

        // 如果是按组计算，需要设置组大小
        if ($validated['calculation_type'] === 'per_group') {
            $validated['group_size'] = max(1, (int)($data['group_size'] ?? 4));
        }

        // 确保最小数量不大于标准数量
        if ($validated['min_quantity'] > $validated['required_quantity']) {
            $validated['min_quantity'] = $validated['required_quantity'];
        }

        return $validated;
    }

    /**
     * 从模板应用器材配置
     */
    public function applyTemplate(int $catalogId, int $templateId): array
    {
        $template = EquipmentRequirementTemplate::findOrFail($templateId);
        $equipmentList = $template->equipment_list;

        // 增加模板使用次数
        $template->incrementUseCount();

        return $this->batchConfigureRequirements($catalogId, $equipmentList);
    }

    /**
     * 复制其他实验的器材配置
     */
    public function copyFromCatalog(int $targetCatalogId, int $sourceCatalogId): array
    {
        $sourceRequirements = ExperimentEquipmentRequirement::where('catalog_id', $sourceCatalogId)
            ->active()
            ->get()
            ->map(function ($requirement) {
                return [
                    'equipment_id' => $requirement->equipment_id,
                    'required_quantity' => $requirement->required_quantity,
                    'min_quantity' => $requirement->min_quantity,
                    'is_required' => $requirement->is_required,
                    'calculation_type' => $requirement->calculation_type,
                    'group_size' => $requirement->group_size,
                    'usage_note' => $requirement->usage_note,
                    'safety_note' => $requirement->safety_note,
                    'sort_order' => $requirement->sort_order
                ];
            })
            ->toArray();

        return $this->batchConfigureRequirements($targetCatalogId, $sourceRequirements);
    }

    /**
     * 智能推荐器材配置
     */
    public function getRecommendedEquipment(int $catalogId): array
    {
        $catalog = ExperimentCatalog::with('subject')->findOrFail($catalogId);
        
        // 基于学科和实验类型推荐器材
        $recommendations = Equipment::with('category')
            ->where('subject_id', $catalog->subject_id)
            ->where('status', Equipment::STATUS_NORMAL)
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($equipment) use ($catalog) {
                return [
                    'equipment_id' => $equipment->id,
                    'equipment_name' => $equipment->name,
                    'equipment_code' => $equipment->code,
                    'category_name' => $equipment->category->name ?? '',
                    'recommended_quantity' => $this->calculateRecommendedQuantity($equipment, $catalog),
                    'calculation_type' => $this->getRecommendedCalculationType($equipment),
                    'confidence' => $this->calculateRecommendationConfidence($equipment, $catalog),
                    'available_quantity' => $equipment->quantity,
                    'unit' => $equipment->unit
                ];
            })
            ->sortByDesc('confidence')
            ->take(20)
            ->values()
            ->toArray();

        return $recommendations;
    }

    /**
     * 计算推荐数量
     */
    private function calculateRecommendedQuantity(Equipment $equipment, ExperimentCatalog $catalog): int
    {
        $categoryName = $equipment->category->name ?? '';
        
        if (str_contains($categoryName, '演示')) {
            return 1;
        } elseif (str_contains($categoryName, '分组')) {
            return 8;
        } elseif (str_contains($categoryName, '消耗')) {
            return 50;
        } else {
            return 2;
        }
    }

    /**
     * 获取推荐的计算方式
     */
    private function getRecommendedCalculationType(Equipment $equipment): string
    {
        $categoryName = $equipment->category->name ?? '';
        
        if (str_contains($categoryName, '演示')) {
            return 'fixed';
        } elseif (str_contains($categoryName, '分组')) {
            return 'per_group';
        } elseif (str_contains($categoryName, '消耗')) {
            return 'per_student';
        } else {
            return 'fixed';
        }
    }

    /**
     * 计算推荐置信度
     */
    private function calculateRecommendationConfidence(Equipment $equipment, ExperimentCatalog $catalog): float
    {
        $confidence = 0.5;
        
        // 同学科器材增加置信度
        if ($equipment->subject_id === $catalog->subject_id) {
            $confidence += 0.3;
        }
        
        // 器材名称包含实验关键词
        $experimentKeywords = explode(' ', $catalog->name);
        foreach ($experimentKeywords as $keyword) {
            if (str_contains($equipment->name, $keyword)) {
                $confidence += 0.1;
                break;
            }
        }
        
        // 器材库存充足
        if ($equipment->quantity > 10) {
            $confidence += 0.1;
        }
        
        return min(1.0, $confidence);
    }

    /**
     * 批量更新排序
     */
    public function updateSortOrder(int $catalogId, array $sortData): bool
    {
        return DB::transaction(function () use ($catalogId, $sortData) {
            foreach ($sortData as $item) {
                ExperimentEquipmentRequirement::where('catalog_id', $catalogId)
                    ->where('equipment_id', $item['equipment_id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
            return true;
        });
    }

    /**
     * 创建配置模板
     */
    public function createTemplate(array $data): EquipmentRequirementTemplate
    {
        $data['created_by'] = auth()->id();
        $data['school_id'] = auth()->user()->school_id;
        
        return EquipmentRequirementTemplate::create($data);
    }

    /**
     * 获取可用模板列表
     */
    public function getAvailableTemplates(int $subjectId = null): array
    {
        $query = EquipmentRequirementTemplate::with(['subject', 'creator'])
            ->where(function ($q) {
                $q->where('is_public', true)
                  ->orWhere('school_id', auth()->user()->school_id);
            });

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        return $query->orderByDesc('use_count')
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    /**
     * 计算所需数量
     */
    private function calculateQuantity(ExperimentEquipmentRequirement $requirement, int $studentCount): int
    {
        switch ($requirement->calculation_type) {
            case 'fixed':
                return $requirement->required_quantity;

            case 'per_student':
                return $requirement->required_quantity * $studentCount;

            case 'per_group':
                $groupSize = $requirement->group_size ?: 4; // 默认4人一组
                $groupCount = ceil($studentCount / $groupSize);
                return $requirement->required_quantity * $groupCount;

            default:
                return $requirement->required_quantity;
        }
    }

    /**
     * 计算最少需要数量
     */
    private function calculateMinQuantity(ExperimentEquipmentRequirement $requirement, int $studentCount): int
    {
        switch ($requirement->calculation_type) {
            case 'fixed':
                return $requirement->min_quantity;

            case 'per_student':
                return $requirement->min_quantity * $studentCount;

            case 'per_group':
                $groupSize = $requirement->group_size ?: 4;
                $groupCount = ceil($studentCount / $groupSize);
                return $requirement->min_quantity * $groupCount;

            default:
                return $requirement->min_quantity;
        }
    }

    /**
     * 获取设备可用数量
     */
    private function getAvailableQuantity(int $equipmentId): int
    {
        $equipment = Equipment::find($equipmentId);
        if (!$equipment) {
            return 0;
        }

        // 这里简化处理，实际应该考虑借用状态、维修状态等
        // 假设每个设备记录代表1个设备单位
        return Equipment::where('id', $equipmentId)
            ->where('status', Equipment::STATUS_NORMAL)
            ->count();
    }
}
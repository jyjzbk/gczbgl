<?php

namespace App\Services;

use App\Models\TextbookVersionAssignment;
use App\Models\TextbookAssignmentTemplate;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TextbookVersionAssignmentService
{
    /**
     * 检查用户是否有指定权限
     */
    public function canAssignTextbookVersion(User $user, int $schoolId): bool
    {
        $school = School::with('region')->find($schoolId);
        if (!$school) {
            return false;
        }

        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id;

        // 根据用户级别和学校管理级别判断权限
        switch ($userLevel) {
            case 1: // 省级
                return $school->level === 1; // 只能管理省直学校

            case 2: // 市级
                return $school->level === 2 &&
                       $this->isSchoolInUserRegion($school, $userOrgId, 2); // 只能管理本市直管学校

            case 3: // 区县级
                return $this->isSchoolInUserRegion($school, $userOrgId, 3); // 可以管理区县内所有学校

            case 4: // 学区级
                return false; // 学区级无指定权限

            case 5: // 学校级
                return false; // 学校级无指定权限

            default:
                return false;
        }
    }

    /**
     * 获取用户可管理的学校列表
     */
    public function getManageableSchools(User $user): Collection
    {
        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id;

        $query = School::with(['region']);

        switch ($userLevel) {
            case 1: // 省级
                $query->where('level', 1); // 省直学校
                break;

            case 2: // 市级
                $query->where('level', 2) // 市直学校
                      ->whereHas('region', function($q) use ($userOrgId) {
                          $q->where('id', $userOrgId)->orWhere('parent_id', $userOrgId);
                      });
                break;

            case 3: // 区县级
                $query->whereHas('region', function($q) use ($userOrgId) {
                    $q->where('id', $userOrgId)->orWhere('parent_id', $userOrgId);
                });
                break;

            default:
                return collect(); // 学区级和学校级无管理权限
        }

        return $query->where('status', 1)->orderBy('name')->get();
    }

    /**
     * 获取用户可管理的所有学校的教材版本指定记录
     */
    public function getAllManageableAssignments(User $user, array $filters = []): Collection
    {
        // 获取可管理的学校ID列表
        $manageableSchools = $this->getManageableSchools($user);
        $schoolIds = $manageableSchools->pluck('id')->toArray();

        if (empty($schoolIds)) {
            return collect();
        }

        // 构建查询
        $query = TextbookVersionAssignment::with([
            'school:id,name,level,region_id',
            'subject:id,name',
            'textbookVersion:id,name,publisher',
            'assignerUser:id,name'
        ])->whereIn('school_id', $schoolIds);

        // 应用过滤条件
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['grade_level'])) {
            $query->where('grade_level', $filters['grade_level']);
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('status', 1)
                      ->where('effective_date', '<=', now())
                      ->where(function($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            } else {
                $query->where('status', $filters['status']);
            }
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'school_name' => $assignment->school->name ?? '',
                'subject_name' => $assignment->subject->name ?? '',
                'grade_level' => $assignment->grade_level,
                'textbook_version_name' => $assignment->textbookVersion->name ?? '',
                'publisher' => $assignment->textbookVersion->publisher ?? '',
                'assigner_name' => $assignment->assignerUser->name ?? '',
                'effective_date' => $assignment->effective_date,
                'status' => $assignment->status,
                'status_name' => $assignment->status == 1 ? '生效' : '失效',
                'created_at' => $assignment->created_at->format('Y-m-d'),
                'assignment_reason' => $assignment->assignment_reason,
                // 保留原始数据用于操作
                'school_id' => $assignment->school_id,
                'subject_id' => $assignment->subject_id,
                'textbook_version_id' => $assignment->textbook_version_id,
                'assigner_user_id' => $assignment->assigner_user_id,
                'expiry_date' => $assignment->expiry_date,
                'change_reason' => $assignment->change_reason
            ];
        });
    }

    /**
     * 为学校指定教材版本
     */
    public function assignTextbookVersion(array $data): TextbookVersionAssignment
    {
        return DB::transaction(function () use ($data) {
            // 检查是否已有生效的指定，如有则使其失效
            $existingAssignment = TextbookVersionAssignment::active()
                ->forSchool($data['school_id'])
                ->forSubject($data['subject_id'])
                ->forGrade($data['grade_level'])
                ->first();

            if ($existingAssignment) {
                $existingAssignment->deactivate('被新指定替换');
                $data['replaced_assignment_id'] = $existingAssignment->id;
            }

            // 创建新的指定记录
            return TextbookVersionAssignment::create($data);
        });
    }

    /**
     * 批量指定教材版本
     */
    public function batchAssignTextbookVersions(array $assignments, User $assigner): array
    {
        $results = [];
        $errors = [];

        DB::transaction(function () use ($assignments, $assigner, &$results, &$errors) {
            foreach ($assignments as $assignment) {
                try {
                    // 检查权限
                    if (!$this->canAssignTextbookVersion($assigner, $assignment['school_id'])) {
                        $errors[] = "无权限为学校ID {$assignment['school_id']} 指定教材版本";
                        continue;
                    }

                    // 添加指定者信息
                    $assignment['assigner_level'] = $assigner->organization_level ?? 5;
                    $assignment['assigner_org_id'] = $assigner->organization_id;
                    $assignment['assigner_org_type'] = $assigner->organization_type;
                    $assignment['assigner_user_id'] = $assigner->id;
                    $assignment['effective_date'] = $assignment['effective_date'] ?? now();

                    $result = $this->assignTextbookVersion($assignment);
                    $results[] = $result;
                } catch (\Exception $e) {
                    $errors[] = "学校ID {$assignment['school_id']} 指定失败: " . $e->getMessage();
                }
            }
        });

        return [
            'success_count' => count($results),
            'error_count' => count($errors),
            'results' => $results,
            'errors' => $errors
        ];
    }

    /**
     * 使用模板批量指定
     */
    public function assignByTemplate(int $templateId, array $schoolIds, User $assigner): array
    {
        $template = TextbookAssignmentTemplate::active()->find($templateId);
        if (!$template) {
            throw new \Exception('模板不存在或已禁用');
        }

        $assignments = [];
        $config = $template->getSubjectVersionMappings();

        foreach ($schoolIds as $schoolId) {
            $school = School::find($schoolId);
            if (!$school) continue;

            foreach ($template->applicable_grades as $gradeLevel) {
                foreach ($config as $subjectId => $textbookVersionId) {
                    $assignments[] = [
                        'school_id' => $schoolId,
                        'subject_id' => $subjectId,
                        'grade_level' => $gradeLevel,
                        'textbook_version_id' => $textbookVersionId,
                        'assignment_reason' => "使用模板：{$template->name}"
                    ];
                }
            }
        }

        // 更新模板使用统计
        $template->incrementUsage();

        return $this->batchAssignTextbookVersions($assignments, $assigner);
    }

    /**
     * 获取学校的教材版本指定情况
     */
    public function getSchoolAssignments(int $schoolId, array $filters = []): Collection
    {
        $query = TextbookVersionAssignment::with([
            'subject', 'textbookVersion', 'assignerUser'
        ])->forSchool($schoolId);

        if (isset($filters['subject_id'])) {
            $query->forSubject($filters['subject_id']);
        }

        if (isset($filters['grade_level'])) {
            $query->forGrade($filters['grade_level']);
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } else {
                $query->where('status', $filters['status']);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * 获取指定的教材版本（用于实验目录筛选）
     */
    public function getAssignedTextbookVersion(int $schoolId, int $subjectId, string $gradeLevel): ?int
    {
        $assignment = TextbookVersionAssignment::active()
            ->forSchool($schoolId)
            ->forSubject($subjectId)
            ->forGrade($gradeLevel)
            ->first();

        return $assignment ? $assignment->textbook_version_id : null;
    }

    /**
     * 撤销指定
     */
    public function revokeAssignment(int $assignmentId, string $reason, User $revoker): bool
    {
        $assignment = TextbookVersionAssignment::find($assignmentId);
        if (!$assignment) {
            throw new \Exception('指定记录不存在');
        }

        // 检查撤销权限
        if (!$this->canAssignTextbookVersion($revoker, $assignment->school_id)) {
            throw new \Exception('无权限撤销此指定');
        }

        return $assignment->deactivate($reason);
    }

    /**
     * 检查学校是否在用户管辖区域内
     */
    private function isSchoolInUserRegion(School $school, int $userOrgId, int $userLevel): bool
    {
        if (!$school->region) {
            return false;
        }

        // 检查学校所在区域是否在用户管辖范围内
        $region = $school->region;

        // 如果学校直接在用户管辖区域内
        if ($region->id === $userOrgId) {
            return true;
        }

        // 检查学校区域的父级是否是用户管辖区域
        while ($region->parent_id) {
            $region = $region->parent;
            if ($region && $region->id === $userOrgId) {
                return true;
            }
        }

        return false;
    }
}

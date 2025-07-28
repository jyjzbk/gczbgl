<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\DataScopeMiddleware;

class SchoolTeacherController extends Controller
{
    /**
     * 获取学校教师列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = SchoolTeacher::with(['school', 'user']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'school_teachers');

        // 按学校筛选
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // 按学科筛选
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('real_name', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderBy('created_at', 'desc');

        // 如果请求参数中包含 all=true，则返回所有数据不分页
        if ($request->get('all') === 'true') {
            $teachers = $query->get();
            return response()->json([
                'success' => true,
                'data' => $teachers
            ]);
        }

        // 默认分页返回
        $teachers = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    /**
     * 创建学校教师
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'user_id' => 'required|exists:users,id',
            'employee_number' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:50',
            'teaching_grades' => 'nullable|array',
            'teaching_grades.*' => 'integer|between:1,9',
            'title' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:50',
            'join_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '数据验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 检查该用户是否已经是该学校的教师
        if (SchoolTeacher::where('school_id', $data['school_id'])
                         ->where('user_id', $data['user_id'])
                         ->exists()) {
            return response()->json([
                'success' => false,
                'message' => '该用户已经是本校教师'
            ], 422);
        }

        $teacher = SchoolTeacher::create($data);

        return response()->json([
            'success' => true,
            'message' => '教师添加成功',
            'data' => $teacher->load(['school', 'user'])
        ]);
    }

    /**
     * 获取教师详情
     */
    public function show(string $id): JsonResponse
    {
        $teacher = SchoolTeacher::with(['school', 'user'])->find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => '教师不存在'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    /**
     * 更新教师信息
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $teacher = SchoolTeacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => '教师不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'employee_number' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:50',
            'teaching_grades' => 'nullable|array',
            'teaching_grades.*' => 'integer|between:1,9',
            'title' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:50',
            'join_date' => 'nullable|date',
            'status' => 'integer|in:0,1,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '数据验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => '教师信息更新成功',
            'data' => $teacher->load(['school', 'user'])
        ]);
    }

    /**
     * 删除教师
     */
    public function destroy(string $id): JsonResponse
    {
        $teacher = SchoolTeacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => '教师不存在'
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => '教师删除成功'
        ]);
    }

    /**
     * 获取可添加的用户列表（未绑定到该学校的用户）
     */
    public function getAvailableUsers(Request $request): JsonResponse
    {
        $schoolId = $request->get('school_id');
        
        if (!$schoolId) {
            return response()->json([
                'success' => false,
                'message' => '学校ID不能为空'
            ], 422);
        }

        // 获取未绑定到该学校的用户
        $existingUserIds = SchoolTeacher::where('school_id', $schoolId)
                                      ->pluck('user_id')
                                      ->toArray();

        $query = User::whereNotIn('id', $existingUserIds)
                    ->where('status', User::STATUS_ACTIVE);

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('real_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->select(['id', 'username', 'real_name', 'email'])
                      ->orderBy('real_name')
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * 批量导入教师
     */
    public function batchImport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'teachers' => 'required|array',
            'teachers.*.user_id' => 'required|exists:users,id',
            'teachers.*.employee_number' => 'nullable|string|max:50',
            'teachers.*.subject' => 'nullable|string|max:50',
            'teachers.*.teaching_grades' => 'nullable|array',
            'teachers.*.title' => 'nullable|string|max:50',
            'teachers.*.education' => 'nullable|string|max:50',
            'teachers.*.join_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '数据验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $schoolId = $request->school_id;
        $teachersData = $request->teachers;
        $created = [];
        $skipped = [];

        foreach ($teachersData as $teacherData) {
            $teacherData['school_id'] = $schoolId;

            // 检查是否已存在
            if (SchoolTeacher::where('school_id', $schoolId)
                            ->where('user_id', $teacherData['user_id'])
                            ->exists()) {
                $skipped[] = $teacherData['user_id'];
                continue;
            }

            $teacher = SchoolTeacher::create($teacherData);
            $created[] = $teacher;
        }

        return response()->json([
            'success' => true,
            'message' => '批量导入完成',
            'data' => [
                'created_count' => count($created),
                'skipped_count' => count($skipped),
                'teachers' => $created
            ]
        ]);
    }
}

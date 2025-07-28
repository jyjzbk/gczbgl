<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\DataScopeMiddleware;

class SchoolClassController extends Controller
{
    /**
     * 获取班级列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = SchoolClass::with(['school', 'headTeacher']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'school_classes');

        // 按学校筛选
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // 按年级筛选
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $query->orderBy('grade')->orderBy('class_number');

        // 如果请求参数中包含 all=true，则返回所有数据不分页
        if ($request->get('all') === 'true') {
            $classes = $query->get();
            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
        }

        // 默认分页返回
        $classes = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * 创建班级
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'grade' => 'required|integer|between:1,9',
            'class_number' => 'required|integer|min:1',
            'student_count' => 'integer|min:0',
            'head_teacher_id' => 'nullable|exists:users,id',
            'classroom_location' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '数据验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 自动生成班级名称和代码
        $data['name'] = $this->generateClassName($data['grade'], $data['class_number']);
        $data['code'] = SchoolClass::generateCode($data['school_id'], $data['grade'], $data['class_number']);

        // 检查班级代码是否重复
        if (SchoolClass::where('school_id', $data['school_id'])->where('code', $data['code'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => '该年级班级号已存在'
            ], 422);
        }

        $class = SchoolClass::create($data);

        return response()->json([
            'success' => true,
            'message' => '班级创建成功',
            'data' => $class->load(['school', 'headTeacher'])
        ]);
    }

    /**
     * 获取班级详情
     */
    public function show(string $id): JsonResponse
    {
        $class = SchoolClass::with(['school', 'headTeacher'])->find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => '班级不存在'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * 更新班级
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => '班级不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'grade' => 'integer|between:1,9',
            'class_number' => 'integer|min:1',
            'student_count' => 'integer|min:0',
            'head_teacher_id' => 'nullable|exists:users,id',
            'classroom_location' => 'nullable|string|max:100',
            'status' => 'integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '数据验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 如果修改了年级或班级号，重新生成名称和代码
        if (isset($data['grade']) || isset($data['class_number'])) {
            $grade = $data['grade'] ?? $class->grade;
            $classNumber = $data['class_number'] ?? $class->class_number;
            
            $data['name'] = $this->generateClassName($grade, $classNumber);
            $data['code'] = SchoolClass::generateCode($class->school_id, $grade, $classNumber);

            // 检查新代码是否重复
            if (SchoolClass::where('school_id', $class->school_id)
                          ->where('code', $data['code'])
                          ->where('id', '!=', $id)
                          ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => '该年级班级号已存在'
                ], 422);
            }
        }

        $class->update($data);

        return response()->json([
            'success' => true,
            'message' => '班级更新成功',
            'data' => $class->load(['school', 'headTeacher'])
        ]);
    }

    /**
     * 删除班级
     */
    public function destroy(string $id): JsonResponse
    {
        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => '班级不存在'
            ], 404);
        }

        // 检查是否有关联的实验预约
        if ($class->reservations()->exists()) {
            return response()->json([
                'success' => false,
                'message' => '该班级存在实验预约记录，无法删除'
            ], 422);
        }

        $class->delete();

        return response()->json([
            'success' => true,
            'message' => '班级删除成功'
        ]);
    }

    /**
     * 生成班级名称
     */
    private function generateClassName(int $grade, int $classNumber): string
    {
        $grades = [
            1 => '一年级', 2 => '二年级', 3 => '三年级',
            4 => '四年级', 5 => '五年级', 6 => '六年级',
            7 => '七年级', 8 => '八年级', 9 => '九年级'
        ];
        
        $gradeName = $grades[$grade] ?? '未知年级';
        return $gradeName . '（' . $classNumber . '）';
    }

    /**
     * 批量创建班级
     */
    public function batchCreate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'grades' => 'required|array',
            'grades.*.grade' => 'required|integer|between:1,9',
            'grades.*.class_count' => 'required|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '数据验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $schoolId = $request->school_id;
        $grades = $request->grades;
        $created = [];

        foreach ($grades as $gradeData) {
            $grade = $gradeData['grade'];
            $classCount = $gradeData['class_count'];

            for ($i = 1; $i <= $classCount; $i++) {
                $code = SchoolClass::generateCode($schoolId, $grade, $i);
                
                // 跳过已存在的班级
                if (SchoolClass::where('school_id', $schoolId)->where('code', $code)->exists()) {
                    continue;
                }

                $class = SchoolClass::create([
                    'school_id' => $schoolId,
                    'name' => $this->generateClassName($grade, $i),
                    'code' => $code,
                    'grade' => $grade,
                    'class_number' => $i,
                    'student_count' => 0,
                    'status' => SchoolClass::STATUS_ACTIVE
                ]);

                $created[] = $class;
            }
        }

        return response()->json([
            'success' => true,
            'message' => '批量创建班级成功',
            'data' => [
                'created_count' => count($created),
                'classes' => $created
            ]
        ]);
    }
}

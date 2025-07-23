<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TextbookChapter;
use App\Models\Subject;
use App\Models\TextbookVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TextbookChapterController extends Controller
{
    /**
     * 获取章节列表（树形结构）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TextbookChapter::with(['subject', 'textbookVersion', 'parent']);

            // 筛选条件
            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('textbook_version_id')) {
                $query->where('textbook_version_id', $request->textbook_version_id);
            }

            if ($request->filled('grade_level')) {
                $query->where('grade_level', $request->grade_level);
            }

            if ($request->filled('volume')) {
                $query->where('volume', $request->volume);
            }

            if ($request->has('status')) {
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

            // 是否返回树形结构
            if ($request->boolean('tree', true)) {
                // 获取根节点（level = 1）
                $rootChapters = $query->where('level', 1)
                    ->ordered()
                    ->get();

                // 递归加载子节点
                $this->loadChildren($rootChapters);

                return response()->json([
                    'success' => true,
                    'data' => $rootChapters
                ]);
            } else {
                // 返回平铺列表
                $chapters = $query->ordered()->get();
                
                return response()->json([
                    'success' => true,
                    'data' => $chapters
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取章节列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取章节树形结构
     */
    public function tree(Request $request): JsonResponse
    {
        try {
            $query = TextbookChapter::with(['subject', 'textbookVersion', 'parent']);

            // 筛选条件
            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('textbook_version_id')) {
                $query->where('textbook_version_id', $request->textbook_version_id);
            }

            if ($request->filled('grade_level')) {
                $query->where('grade_level', $request->grade_level);
            }

            if ($request->filled('volume')) {
                $query->where('volume', $request->volume);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                // 默认只显示启用的章节
                $query->where('status', 1);
            }

            // 搜索
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // 获取根节点（level = 1）
            $rootChapters = $query->where('level', 1)
                ->ordered()
                ->get();

            // 递归加载子节点
            $this->loadChildren($rootChapters);

            return response()->json([
                'success' => true,
                'data' => $rootChapters
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取章节树形结构失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 递归加载子节点
     */
    private function loadChildren($chapters)
    {
        foreach ($chapters as $chapter) {
            $children = TextbookChapter::with(['subject', 'textbookVersion', 'parent'])
                ->where('parent_id', $chapter->id)
                ->ordered()
                ->get();
            
            if ($children->isNotEmpty()) {
                $this->loadChildren($children);
                $chapter->children = $children;
            }
        }
    }

    /**
     * 创建章节
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'textbook_version_id' => 'required|exists:textbook_versions,id',
            'grade_level' => 'required|string|max:20',
            'volume' => 'required|string|max:20',
            'parent_id' => 'nullable|exists:textbook_chapters,id',
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1'
        ], [
            'subject_id.required' => '学科不能为空',
            'textbook_version_id.required' => '教材版本不能为空',
            'grade_level.required' => '年级不能为空',
            'volume.required' => '册次不能为空',
            'name.required' => '章节名称不能为空',
            'code.required' => '章节编码不能为空'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 确定层级
            $level = 1;
            if ($request->filled('parent_id')) {
                $parent = TextbookChapter::findOrFail($request->parent_id);
                $level = $parent->level + 1;
            }

            $chapter = TextbookChapter::create([
                'subject_id' => $request->subject_id,
                'textbook_version_id' => $request->textbook_version_id,
                'grade_level' => $request->grade_level,
                'volume' => $request->volume,
                'parent_id' => $request->parent_id,
                'level' => $level,
                'code' => $request->code,
                'name' => $request->name,
                'sort_order' => $request->input('sort_order', 0),
                'status' => $request->input('status', 1)
            ]);

            $chapter->load(['subject', 'textbookVersion', 'parent']);

            return response()->json([
                'success' => true,
                'message' => '章节创建成功',
                'data' => $chapter
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '创建章节失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取章节详情
     */
    public function show($id): JsonResponse
    {
        try {
            $chapter = TextbookChapter::with([
                'subject', 
                'textbookVersion', 
                'parent', 
                'children',
                'experimentCatalogs'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $chapter
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取章节详情失败：' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * 更新章节
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'textbook_version_id' => 'required|exists:textbook_versions,id',
            'grade_level' => 'required|string|max:20',
            'volume' => 'required|string|max:20',
            'parent_id' => 'nullable|exists:textbook_chapters,id',
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chapter = TextbookChapter::findOrFail($id);

            // 检查是否会形成循环引用
            if ($request->filled('parent_id') && $request->parent_id != $chapter->parent_id) {
                if ($this->wouldCreateCircularReference($id, $request->parent_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => '不能将章节移动到其子章节下，这会形成循环引用'
                    ], 400);
                }
            }

            // 更新层级
            $level = 1;
            if ($request->filled('parent_id')) {
                $parent = TextbookChapter::findOrFail($request->parent_id);
                $level = $parent->level + 1;
            }

            $chapter->update([
                'subject_id' => $request->subject_id,
                'textbook_version_id' => $request->textbook_version_id,
                'grade_level' => $request->grade_level,
                'volume' => $request->volume,
                'parent_id' => $request->parent_id,
                'level' => $level,
                'code' => $request->code,
                'name' => $request->name,
                'sort_order' => $request->input('sort_order', $chapter->sort_order),
                'status' => $request->input('status', $chapter->status)
            ]);

            // 如果父级改变了，需要更新所有子节点的层级
            if ($request->filled('parent_id') && $request->parent_id != $chapter->getOriginal('parent_id')) {
                $this->updateChildrenLevels($chapter);
            }

            $chapter->load(['subject', 'textbookVersion', 'parent']);

            return response()->json([
                'success' => true,
                'message' => '章节更新成功',
                'data' => $chapter
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新章节失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查是否会形成循环引用
     */
    private function wouldCreateCircularReference($chapterId, $newParentId): bool
    {
        if ($chapterId == $newParentId) {
            return true;
        }

        $parent = TextbookChapter::find($newParentId);
        while ($parent) {
            if ($parent->id == $chapterId) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * 递归更新子节点层级
     */
    private function updateChildrenLevels($chapter)
    {
        $children = TextbookChapter::where('parent_id', $chapter->id)->get();
        
        foreach ($children as $child) {
            $child->update(['level' => $chapter->level + 1]);
            $this->updateChildrenLevels($child);
        }
    }

    /**
     * 删除章节
     */
    public function destroy($id): JsonResponse
    {
        try {
            $chapter = TextbookChapter::findOrFail($id);

            // 检查是否有子章节
            $childrenCount = $chapter->children()->count();
            if ($childrenCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => '该章节下还有子章节，请先删除子章节'
                ], 400);
            }

            // 检查是否有关联的实验目录
            $catalogsCount = $chapter->experimentCatalogs()->count();
            if ($catalogsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => '该章节下还有实验目录，无法删除'
                ], 400);
            }

            $chapter->delete();

            return response()->json([
                'success' => true,
                'message' => '章节删除成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '删除章节失败：' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentWork;
use App\Models\ExperimentRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ExperimentWorkController extends Controller
{
    /**
     * 获取实验作品列表
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'record_id' => 'nullable|exists:experiment_records,id',
            'student_id' => 'nullable|exists:users,id',
            'type' => 'nullable|in:photo,video,document,other',
            'is_featured' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = ExperimentWork::with([
                'experimentRecord.catalog',
                'experimentRecord.laboratory',
                'student',
                'uploader'
            ]);

            // 筛选条件
            if ($request->record_id) {
                $query->where('record_id', $request->record_id);
            }

            if ($request->student_id) {
                $query->where('student_id', $request->student_id);
            }

            if ($request->type) {
                $query->where('type', $request->type);
            }

            if ($request->has('is_featured')) {
                $query->where('is_featured', $request->is_featured);
            }

            if ($request->has('is_public')) {
                $query->where('is_public', $request->is_public);
            }

            // 权限控制：只能查看自己学校的作品
            $user = auth()->user();
            if ($user->data_scope !== 'all') {
                $query->whereHas('experimentRecord', function ($q) use ($user) {
                    $q->where('school_id', $user->school_id);
                });
            }

            $works = $query->orderByDesc('created_at')
                ->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'data' => $works->items(),
                'pagination' => [
                    'current_page' => $works->currentPage(),
                    'last_page' => $works->lastPage(),
                    'per_page' => $works->perPage(),
                    'total' => $works->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取作品列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 上传实验作品
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'record_id' => 'required|exists:experiment_records,id',
            'student_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'file' => 'required|file|max:50240', // 50MB
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $record = ExperimentRecord::findOrFail($request->record_id);
            
            // 权限检查：只能为自己学校的实验记录上传作品
            $user = auth()->user();
            if ($user->data_scope !== 'all' && $record->school_id !== $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限操作此实验记录'
                ], 403);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // 确定文件类型
            $type = $this->determineFileType($mimeType);

            // 生成文件路径
            $directory = 'experiment-works/' . date('Y/m');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $directory . '/' . $filename;

            // 存储文件
            $file->storeAs('public/' . $directory, $filename);

            // 如果是图片，生成缩略图
            if ($type === ExperimentWork::TYPE_PHOTO) {
                $this->generateThumbnail($filePath);
            }

            // 创建作品记录
            $work = ExperimentWork::create([
                'record_id' => $request->record_id,
                'student_id' => $request->student_id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => $type,
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'metadata' => $this->extractMetadata($file, $type),
                'is_public' => $request->is_public ?? false,
                'uploaded_by' => auth()->id()
            ]);

            // 更新实验记录的作品数量
            $record->increment('work_count');

            return response()->json([
                'success' => true,
                'message' => '作品上传成功',
                'data' => [
                    'id' => $work->id,
                    'title' => $work->title,
                    'type' => $work->type,
                    'type_name' => $work->type_name,
                    'file_url' => $work->file_url,
                    'thumbnail_url' => $work->thumbnail_url,
                    'file_size' => $work->formatted_file_size,
                    'created_at' => $work->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '作品上传失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取作品详情
     */
    public function show($id): JsonResponse
    {
        try {
            $work = ExperimentWork::with([
                'experimentRecord.catalog',
                'experimentRecord.laboratory',
                'student',
                'uploader'
            ])->findOrFail($id);

            // 权限检查
            $user = auth()->user();
            if ($user->data_scope !== 'all' && $work->experimentRecord->school_id !== $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看此作品'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $work
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取作品详情失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新作品信息
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:200',
            'description' => 'nullable|string',
            'quality_score' => 'nullable|integer|min:1|max:5',
            'teacher_comment' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $work = ExperimentWork::findOrFail($id);

            // 权限检查
            $user = auth()->user();
            if ($user->data_scope !== 'all' && $work->experimentRecord->school_id !== $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限修改此作品'
                ], 403);
            }

            $work->update($request->only([
                'title', 'description', 'quality_score', 
                'teacher_comment', 'is_featured', 'is_public'
            ]));

            return response()->json([
                'success' => true,
                'message' => '作品更新成功',
                'data' => $work
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '作品更新失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除作品
     */
    public function destroy($id): JsonResponse
    {
        try {
            $work = ExperimentWork::findOrFail($id);

            // 权限检查
            $user = auth()->user();
            if ($user->data_scope !== 'all' && $work->experimentRecord->school_id !== $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限删除此作品'
                ], 403);
            }

            // 删除文件和记录
            $work->delete();

            // 更新实验记录的作品数量
            $work->experimentRecord->decrement('work_count');

            return response()->json([
                'success' => true,
                'message' => '作品删除成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '作品删除失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 确定文件类型
     */
    private function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return ExperimentWork::TYPE_PHOTO;
        } elseif (str_starts_with($mimeType, 'video/')) {
            return ExperimentWork::TYPE_VIDEO;
        } elseif (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ])) {
            return ExperimentWork::TYPE_DOCUMENT;
        } else {
            return ExperimentWork::TYPE_OTHER;
        }
    }

    /**
     * 生成缩略图
     */
    private function generateThumbnail(string $filePath): void
    {
        try {
            $fullPath = storage_path('app/public/' . $filePath);
            $pathInfo = pathinfo($fullPath);
            $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
            $thumbnailPath = $thumbnailDir . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

            // 创建缩略图目录
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // 生成缩略图
            Image::make($fullPath)
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save($thumbnailPath, 80);

        } catch (\Exception $e) {
            // 缩略图生成失败不影响主流程
            \Log::warning('缩略图生成失败：' . $e->getMessage());
        }
    }

    /**
     * 提取文件元数据
     */
    private function extractMetadata($file, string $type): array
    {
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];

        if ($type === ExperimentWork::TYPE_PHOTO) {
            try {
                $imageInfo = getimagesize($file->getPathname());
                if ($imageInfo) {
                    $metadata['width'] = $imageInfo[0];
                    $metadata['height'] = $imageInfo[1];
                }
            } catch (\Exception $e) {
                // 忽略获取图片信息失败的情况
            }
        }

        return $metadata;
    }
}

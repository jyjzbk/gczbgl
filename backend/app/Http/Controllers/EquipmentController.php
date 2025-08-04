<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentAttachment;
use App\Models\EquipmentOperationLog;
use App\Models\School;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EquipmentExport;
use App\Imports\EquipmentImport;
use App\Http\Requests\EquipmentRequest;
use App\Services\PermissionService;
use App\Http\Middleware\DataScopeMiddleware;

class EquipmentController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;

        // 在Laravel 12中，中间件应该在路由中定义，而不是在控制器构造函数中
        // 权限控制已在路由文件中通过data.scope中间件处理
    }

    /**
     * 获取设备列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Equipment::with(['category', 'school', 'laboratory', 'manager']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'equipments');

        // 筛选条件
        if ($request->filled('school_id')) {
            // 验证用户是否可以访问指定学校
            if (DataScopeMiddleware::canAccess($request, 'school', $request->school_id)) {
                $query->where('school_id', $request->school_id);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定学校的数据'
                ], 403);
            }
        }

        if ($request->filled('laboratory_id')) {
            $query->where('laboratory_id', $request->laboratory_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // 日期范围筛选
        if ($request->filled('purchase_date_start')) {
            $query->where('purchase_date', '>=', $request->purchase_date_start);
        }

        if ($request->filled('purchase_date_end')) {
            $query->where('purchase_date', '<=', $request->purchase_date_end);
        }

        // 价格范围筛选
        if ($request->filled('price_min')) {
            $query->where('purchase_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('purchase_price', '<=', $request->price_max);
        }

        // 排序
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // 分页
        $perPage = $request->get('per_page', 15);
        $equipments = $query->paginate($perPage);

        // 添加计算字段
        $equipments->getCollection()->transform(function ($equipment) {
            $equipment->available_quantity = $equipment->available_quantity;
            $equipment->borrowed_quantity = $equipment->borrowed_quantity;
            $equipment->warranty_end_date = $equipment->warranty_end_date;
            return $equipment;
        });

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => [
                'items' => $equipments->items(),
                'pagination' => [
                    'current_page' => $equipments->currentPage(),
                    'last_page' => $equipments->lastPage(),
                    'per_page' => $equipments->perPage(),
                    'total' => $equipments->total()
                ]
            ]
        ]);
    }

    /**
     * 创建设备
     */
    public function store(EquipmentRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        // 验证创建权限
        if (!DataScopeMiddleware::canCreate($request, $data)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限为指定学校创建设备'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // 如果没有提供设备编号，自动生成
            if (empty($data['code'])) {
                $data['code'] = $this->generateEquipmentCode($data['category_id'], $data['school_id']);
            }

            $equipment = Equipment::create($data);
            $equipment->load(['category', 'school', 'laboratory', 'manager']);

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                $equipment->id,
                $user->id,
                'create',
                'equipment',
                '创建设备',
                null,
                $data
            );

            DB::commit();

            return response()->json([
                'code' => 201,
                'message' => '创建成功',
                'data' => $equipment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '创建失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取设备详情
     */
    public function show(Request $request, Equipment $equipment): JsonResponse
    {
        // 验证访问权限
        if (!DataScopeMiddleware::canAccess($request, 'school', $equipment->school_id)) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问该设备信息'
            ], 403);
        }

        $equipment->load([
            'category', 'school', 'laboratory', 'manager',
            'borrows' => function($query) {
                $query->with(['borrower', 'approver'])->latest();
            },
            'maintenances' => function($query) {
                $query->with(['reporter', 'maintainer'])->latest();
            }
        ]);

        // 添加计算字段
        $equipment->available_quantity = $equipment->available_quantity;
        $equipment->borrowed_quantity = $equipment->borrowed_quantity;
        $equipment->warranty_end_date = $equipment->warranty_end_date;
        $equipment->is_under_warranty = $equipment->isUnderWarranty();

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $equipment
        ]);
    }

    /**
     * 更新设备
     */
    public function update(Request $request, Equipment $equipment): JsonResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'laboratory_id' => 'nullable|exists:laboratories,id',
            'category_id' => 'required|exists:equipment_categories,id',
            'name' => 'required|string|max:200',
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipments', 'code')->ignore($equipment->id)
            ],
            'model' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:200',
            'supplier_phone' => 'nullable|string|max:20',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:20',
            'warranty_period' => 'nullable|integer|min:0',
            'service_life' => 'nullable|integer|min:0',
            'funding_source' => 'nullable|string|max:100',
            'storage_location' => 'nullable|string|max:200',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'nullable|integer|in:0,1,2,3',
            'remark' => 'nullable|string'
        ]);

        $data = $request->only([
            'school_id', 'laboratory_id', 'category_id', 'name', 'code',
            'model', 'brand', 'supplier', 'supplier_phone', 'purchase_date',
            'purchase_price', 'quantity', 'unit', 'warranty_period',
            'service_life', 'funding_source', 'storage_location',
            'manager_id', 'status', 'remark'
        ]);

        // 验证更新权限
        if (!DataScopeMiddleware::canUpdate($request, $equipment, $data)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限更新该设备或修改其归属'
            ], 403);
        }

        // 检查数量变更是否会影响借用
        if ($data['quantity'] < $equipment->quantity) {
            $borrowedQuantity = $equipment->borrowed_quantity;
            if ($data['quantity'] < $borrowedQuantity) {
                return response()->json([
                    'code' => 422,
                    'message' => "设备数量不能少于已借用数量({$borrowedQuantity})"
                ], 422);
            }
        }

        $equipment->update($data);
        $equipment->load(['category', 'school', 'laboratory', 'manager']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $equipment
        ]);
    }

    /**
     * 删除设备
     */
    public function destroy(Request $request, Equipment $equipment): JsonResponse
    {
        // 验证删除权限
        if (!DataScopeMiddleware::canAccess($request, 'school', $equipment->school_id)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限删除该设备'
            ], 403);
        }

        // 检查是否有借用记录
        if ($equipment->borrows()->where('status', '!=', 2)->count() > 0) {
            return response()->json([
                'code' => 422,
                'message' => '该设备还有未归还的借用记录，无法删除'
            ], 422);
        }

        // 检查是否有未完成的维修记录
        if ($equipment->maintenances()->whereIn('status', [1, 2])->count() > 0) {
            return response()->json([
                'code' => 422,
                'message' => '该设备还有未完成的维修记录，无法删除'
            ], 422);
        }

        $equipment->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 生成设备二维码
     */
    public function generateQrCode(Equipment $equipment): JsonResponse
    {
        $qrCode = $equipment->generateQrCode();

        return response()->json([
            'code' => 200,
            'message' => '二维码生成成功',
            'data' => [
                'qr_code' => $qrCode,
                'qr_url' => url("/equipment/{$equipment->id}/qr/{$qrCode}")
            ]
        ]);
    }

    /**
     * 通过二维码查询设备
     */
    public function findByQrCode(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $equipment = Equipment::where('qr_code', $request->qr_code)
                              ->with(['category', 'school', 'laboratory', 'manager'])
                              ->first();

        if (!$equipment) {
            return response()->json([
                'code' => 404,
                'message' => '设备不存在'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $equipment
        ]);
    }

    /**
     * 生成设备编号
     */
    private function generateEquipmentCode($categoryId, $schoolId): string
    {
        $category = EquipmentCategory::find($categoryId);
        $school = School::find($schoolId);
        
        $prefix = strtoupper(substr($category->code ?? 'EQ', 0, 3));
        $schoolCode = str_pad($schoolId, 3, '0', STR_PAD_LEFT);
        
        // 获取当前分类下的最大编号
        $lastEquipment = Equipment::where('category_id', $categoryId)
                                 ->where('school_id', $schoolId)
                                 ->where('code', 'like', "{$prefix}{$schoolCode}%")
                                 ->orderBy('code', 'desc')
                                 ->first();
        
        $sequence = 1;
        if ($lastEquipment && preg_match('/(\d+)$/', $lastEquipment->code, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }
        
        return $prefix . $schoolCode . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 批量导入设备（文件上传方式）
     */
    public function batchImport(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB
        ]);

        try {
            DB::beginTransaction();

            $import = new EquipmentImport();
            Excel::import($import, $request->file('file'));

            // 批量导入不记录到设备操作日志，因为没有特定的设备ID
            \Log::info('设备文件批量导入完成', [
                'user_id' => auth()->id(),
                'imported_count' => $import->getImportedCount(),
                'failed_count' => $import->getFailedCount(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => '导入成功',
                'data' => [
                    'imported_count' => $import->getImportedCount(),
                    'failed_count' => $import->getFailedCount(),
                    'errors' => $import->getErrors()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '导入失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量导入设备（JSON数据方式）
     */
    public function batchImportJson(Request $request): JsonResponse
    {
        $request->validate([
            'equipments' => 'required|array|min:1',
            'equipments.*.school_id' => 'required|integer|exists:schools,id',
            'equipments.*.category_id' => 'required|integer|exists:equipment_categories,id',
            'equipments.*.name' => 'required|string|max:200',
            'equipments.*.code' => 'nullable|string|max:100',
            'equipments.*.model' => 'nullable|string|max:100',
            'equipments.*.brand' => 'nullable|string|max:100',
            'equipments.*.supplier' => 'nullable|string|max:200',
            'equipments.*.supplier_phone' => 'nullable|string|max:20',
            'equipments.*.purchase_date' => 'nullable|date|before_or_equal:today',
            'equipments.*.purchase_price' => 'nullable|numeric|min:0|max:999999999.99',
            'equipments.*.quantity' => 'required|integer|min:1',
            'equipments.*.unit' => 'required|string|max:20',
            'equipments.*.warranty_period' => 'nullable|integer|min:0|max:120',
            'equipments.*.service_life' => 'nullable|integer|min:0|max:50',
            'equipments.*.funding_source' => 'nullable|string|max:100',
            'equipments.*.storage_location' => 'nullable|string|max:200',
            'equipments.*.manager_id' => 'nullable|integer|exists:users,id',
            'equipments.*.status' => 'required|integer|in:1,2,3,4',
            'equipments.*.remark' => 'nullable|string|max:2000',
        ]);

        try {
            DB::beginTransaction();

            $equipments = $request->equipments;
            $successCount = 0;
            $failureCount = 0;
            $errors = [];

            foreach ($equipments as $index => $equipmentData) {
                try {
                    // 检查设备编号是否重复
                    if (!empty($equipmentData['code'])) {
                        $exists = Equipment::where('code', $equipmentData['code'])->exists();
                        if ($exists) {
                            $errors[] = "第" . ($index + 1) . "行：设备编号 {$equipmentData['code']} 已存在";
                            $failureCount++;
                            continue;
                        }
                    }

                    Equipment::create($equipmentData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "第" . ($index + 1) . "行：" . $e->getMessage();
                    $failureCount++;
                }
            }

            // 批量导入不记录到设备操作日志，因为没有特定的设备ID
            // 可以考虑记录到系统操作日志或单独的批量操作日志表
            \Log::info('设备批量导入完成', [
                'user_id' => auth()->id(),
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'total_count' => count($equipments),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => $successCount > 0 ? '导入完成' : '导入失败',
                'data' => [
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                    'total_count' => count($equipments),
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '导入失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 导出设备数据
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filename = 'equipments_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = 'exports/' . $filename;

            Excel::store(new EquipmentExport($request->all()), $filePath, 'public');

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                0,
                auth()->id(),
                'export',
                'equipment',
                '导出设备数据',
                null,
                ['filename' => $filename]
            );

            return response()->json([
                'code' => 200,
                'message' => '导出成功',
                'data' => [
                    'download_url' => Storage::url($filePath),
                    'filename' => $filename
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '导出失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 上传设备照片
     */
    public function uploadPhoto(Request $request, $id): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'attachment_type' => 'string|in:photo,manual,certificate,warranty,invoice,other',
            'description' => 'string|max:500',
            'is_primary' => 'boolean'
        ]);

        $equipment = Equipment::findOrFail($id);

        try {
            DB::beginTransaction();

            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('equipment_photos', $filename, 'public');

            // 如果设置为主图片，先取消其他主图片
            if ($request->get('is_primary', false)) {
                EquipmentAttachment::where('equipment_id', $id)
                    ->where('file_type', 'image')
                    ->update(['is_primary' => false]);
            }

            $attachment = EquipmentAttachment::create([
                'equipment_id' => $id,
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => 'image',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_extension' => $file->getClientOriginalExtension(),
                'attachment_type' => $request->get('attachment_type', 'photo'),
                'description' => $request->get('description'),
                'is_primary' => $request->get('is_primary', false),
                'uploaded_by' => auth()->id(),
            ]);

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                $id,
                auth()->id(),
                'photo',
                'attachment',
                '上传设备照片',
                null,
                ['attachment_id' => $attachment->id, 'filename' => $filename]
            );

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => '照片上传成功',
                'data' => [
                    'id' => $attachment->id,
                    'file_url' => $attachment->file_url,
                    'file_name' => $attachment->file_name,
                    'is_primary' => $attachment->is_primary
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '照片上传失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除设备照片
     */
    public function deletePhoto(Request $request, $id, $photoId): JsonResponse
    {
        $equipment = Equipment::findOrFail($id);
        $attachment = EquipmentAttachment::where('equipment_id', $id)
            ->where('id', $photoId)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                $id,
                auth()->id(),
                'delete',
                'attachment',
                '删除设备照片',
                ['attachment_id' => $attachment->id, 'filename' => $attachment->file_name],
                null
            );

            $attachment->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => '照片删除成功'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '照片删除失败：' . $e->getMessage()
            ], 500);
        }
    }
}

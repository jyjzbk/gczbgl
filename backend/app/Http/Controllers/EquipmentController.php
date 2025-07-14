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

class EquipmentController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;

        // 应用权限中间件
        $this->middleware('auth:api');
        $this->middleware('equipment.permission:equipment.view')->only(['index', 'show']);
        $this->middleware('equipment.permission:equipment.create')->only(['store']);
        $this->middleware('equipment.permission:equipment.edit')->only(['update']);
        $this->middleware('equipment.permission:equipment.delete')->only(['destroy']);
        $this->middleware('equipment.permission:equipment.import')->only(['batchImport']);
        $this->middleware('equipment.permission:equipment.export')->only(['export']);
        $this->middleware('equipment.permission:equipment.photo.upload')->only(['uploadPhoto']);
        $this->middleware('equipment.permission:equipment.photo.delete')->only(['deletePhoto']);
    }

    /**
     * 获取设备列表
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = Equipment::with(['category', 'school', 'laboratory', 'manager']);

        // 数据访问权限控制
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            // 非管理员只能查看自己学校的设备
            $query->where('school_id', $user->school_id);
        }

        // 筛选条件
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
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

        // 数据访问权限检查
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            if ($request->school_id !== $user->school_id) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权限为其他学校创建设备'
                ], 403);
            }
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
    public function show(Equipment $equipment): JsonResponse
    {
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
    public function destroy(Equipment $equipment): JsonResponse
    {
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
     * 批量导入设备
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

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                0, // 批量导入没有特定设备ID
                auth()->id(),
                'import',
                'equipment',
                '批量导入设备数据',
                null,
                ['imported_count' => $import->getImportedCount()]
            );

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

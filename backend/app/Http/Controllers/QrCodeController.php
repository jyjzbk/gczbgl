<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * 生成设备二维码
     */
    public function generateEquipmentQrCode(Request $request, Equipment $equipment): JsonResponse
    {
        $request->validate([
            'size' => 'nullable|integer|min:100|max:1000',
            'format' => 'nullable|in:png,svg',
            'include_label' => 'nullable|boolean',
            'regenerate' => 'nullable|boolean'
        ]);

        try {
            // 如果设备没有二维码或者要求重新生成，则生成新的二维码
            if (!$equipment->qr_code || $request->boolean('regenerate')) {
                $equipment->generateQrCode();
            }

            $options = [
                'size' => $request->get('size', 300),
                'format' => $request->get('format', 'png'),
                'include_label' => $request->boolean('include_label', true)
            ];

            $result = $this->qrCodeService->generateEquipmentQrCode(
                $equipment->qr_code,
                $equipment->name,
                $options
            );

            return response()->json([
                'code' => 200,
                'message' => '二维码生成成功',
                'data' => [
                    'equipment' => [
                        'id' => $equipment->id,
                        'name' => $equipment->name,
                        'code' => $equipment->code
                    ],
                    'qr_code' => $result
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '二维码生成失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量生成设备二维码
     */
    public function batchGenerateQrCodes(Request $request): JsonResponse
    {
        $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipments,id',
            'size' => 'nullable|integer|min:100|max:1000',
            'format' => 'nullable|in:png,svg',
            'include_label' => 'nullable|boolean',
            'regenerate' => 'nullable|boolean'
        ]);

        try {
            $equipments = Equipment::whereIn('id', $request->equipment_ids)->get();
            
            // 确保所有设备都有二维码
            foreach ($equipments as $equipment) {
                if (!$equipment->qr_code || $request->boolean('regenerate')) {
                    $equipment->generateQrCode();
                }
            }

            $options = [
                'size' => $request->get('size', 300),
                'format' => $request->get('format', 'png'),
                'include_label' => $request->boolean('include_label', true)
            ];

            $results = $this->qrCodeService->generateBatchEquipmentQrCodes(
                $equipments->toArray(),
                $options
            );

            $successCount = collect($results)->where('success', true)->count();
            $failureCount = collect($results)->where('success', false)->count();

            return response()->json([
                'code' => 200,
                'message' => "批量生成完成，成功：{$successCount}，失败：{$failureCount}",
                'data' => [
                    'results' => $results,
                    'summary' => [
                        'total' => count($results),
                        'success' => $successCount,
                        'failure' => $failureCount
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '批量生成失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 通过二维码查询设备信息
     */
    public function getEquipmentByQrCode(string $qrCode): JsonResponse
    {
        try {
            // 验证二维码格式
            if (!$this->qrCodeService->validateQrCode($qrCode)) {
                return response()->json([
                    'code' => 400,
                    'message' => '无效的二维码格式'
                ], 400);
            }

            // 查找设备
            $equipment = Equipment::where('qr_code', $qrCode)
                                 ->with(['category', 'school', 'laboratory', 'manager'])
                                 ->first();

            if (!$equipment) {
                return response()->json([
                    'code' => 404,
                    'message' => '设备不存在'
                ], 404);
            }

            // 解析二维码信息
            $qrInfo = $this->qrCodeService->parseQrCode($qrCode);

            return response()->json([
                'code' => 200,
                'message' => '查询成功',
                'data' => [
                    'equipment' => $equipment,
                    'qr_info' => $qrInfo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '查询失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 下载二维码文件
     */
    public function downloadQrCode(Request $request, Equipment $equipment): Response
    {
        $format = $request->get('format', 'png');
        
        if (!$equipment->qr_code) {
            abort(404, '设备二维码不存在');
        }

        $fileInfo = $this->qrCodeService->getQrCodeFileInfo($equipment->qr_code, $format);
        
        if (!$fileInfo) {
            // 如果文件不存在，尝试生成
            try {
                $this->qrCodeService->generateEquipmentQrCode(
                    $equipment->qr_code,
                    $equipment->name,
                    ['format' => $format]
                );
                $fileInfo = $this->qrCodeService->getQrCodeFileInfo($equipment->qr_code, $format);
            } catch (\Exception $e) {
                abort(500, '二维码文件生成失败');
            }
        }

        if (!$fileInfo) {
            abort(404, '二维码文件不存在');
        }

        $fileName = "设备二维码_{$equipment->code}_{$equipment->name}.{$format}";
        
        return response()->download($fileInfo['file_path'], $fileName);
    }

    /**
     * 生成设备标签
     */
    public function generateEquipmentLabel(Request $request, Equipment $equipment): JsonResponse
    {
        $request->validate([
            'width' => 'nullable|integer|min:200|max:800',
            'height' => 'nullable|integer|min:100|max:400',
            'qr_size' => 'nullable|integer|min:50|max:200',
            'include_fields' => 'nullable|array',
            'include_fields.*' => 'in:name,code,category,school,laboratory,manager,purchase_date',
            'format' => 'nullable|in:png,svg'
        ]);

        try {
            if (!$equipment->qr_code) {
                $equipment->generateQrCode();
            }

            $equipmentInfo = [
                'name' => $equipment->name,
                'code' => $equipment->code,
                'category' => $equipment->category ? $equipment->category->name : '',
                'school' => $equipment->school ? $equipment->school->name : '',
                'laboratory' => $equipment->laboratory ? $equipment->laboratory->name : '',
                'manager' => $equipment->manager ? $equipment->manager->real_name : '',
                'purchase_date' => $equipment->purchase_date ? $equipment->purchase_date->format('Y-m-d') : ''
            ];

            $options = [
                'width' => $request->get('width', 400),
                'height' => $request->get('height', 200),
                'qr_size' => $request->get('qr_size', 120),
                'include_fields' => $request->get('include_fields', ['name', 'code', 'category']),
                'format' => $request->get('format', 'png')
            ];

            $result = $this->qrCodeService->generateEquipmentLabel(
                $equipment->qr_code,
                $equipmentInfo,
                $options
            );

            return response()->json([
                'code' => 200,
                'message' => '设备标签生成成功',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '标签生成失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取二维码统计信息
     */
    public function getQrCodeStatistics(): JsonResponse
    {
        try {
            $statistics = $this->qrCodeService->getQrCodeStatistics();
            
            // 添加数据库统计
            $dbStats = [
                'total_equipments' => Equipment::count(),
                'equipments_with_qr' => Equipment::whereNotNull('qr_code')->count(),
                'equipments_without_qr' => Equipment::whereNull('qr_code')->count()
            ];

            return response()->json([
                'code' => 200,
                'message' => '统计信息获取成功',
                'data' => [
                    'file_statistics' => $statistics,
                    'database_statistics' => $dbStats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '获取统计信息失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 清理过期的二维码文件
     */
    public function cleanupExpiredQrCodes(Request $request): JsonResponse
    {
        $request->validate([
            'days_old' => 'nullable|integer|min:1|max:365'
        ]);

        try {
            $daysOld = $request->get('days_old', 30);
            $result = $this->qrCodeService->cleanupExpiredQrCodes($daysOld);

            return response()->json([
                'code' => 200,
                'message' => "清理完成，删除了 {$result['deleted']} 个文件",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '清理失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 验证二维码
     */
    public function validateQrCode(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        try {
            $isValid = $this->qrCodeService->validateQrCode($request->qr_code);
            
            $result = ['is_valid' => $isValid];
            
            if ($isValid) {
                $result['qr_info'] = $this->qrCodeService->parseQrCode($request->qr_code);
            }

            return response()->json([
                'code' => 200,
                'message' => $isValid ? '二维码格式有效' : '二维码格式无效',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '验证失败：' . $e->getMessage()
            ], 500);
        }
    }
}

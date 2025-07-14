<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentQrcode;
use App\Models\EquipmentOperationLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class EquipmentQrcodeController extends Controller
{
    /**
     * 生成设备二维码
     */
    public function generate(Request $request, $equipmentId): JsonResponse
    {
        $request->validate([
            'qr_type' => 'required|in:basic,detailed,url',
            'size' => 'integer|min:100|max:500',
            'format' => 'string|in:png,jpg,svg',
            'include_info' => 'array',
            'include_info.*' => 'string|in:name,code,location,contact'
        ]);

        $equipment = Equipment::with(['category', 'school'])->findOrFail($equipmentId);

        try {
            DB::beginTransaction();

            // 生成二维码内容
            $qrContent = $this->generateQrContent($equipment, $request->qr_type, $request->include_info ?? []);
            
            // 生成二维码图片
            $size = $request->get('size', 200);
            $format = $request->get('format', 'png');
            
            $qrCode = QrCode::format($format)
                ->size($size)
                ->margin(1)
                ->generate($qrContent);

            // 保存二维码文件
            $filename = 'qrcode_' . $equipment->code . '_' . time() . '.' . $format;
            $filePath = 'qrcodes/' . $filename;
            Storage::disk('public')->put($filePath, $qrCode);

            // 停用之前的二维码
            EquipmentQrcode::where('equipment_id', $equipmentId)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // 创建新的二维码记录
            $qrcodeRecord = EquipmentQrcode::create([
                'equipment_id' => $equipmentId,
                'qr_code_url' => Storage::url($filePath),
                'qr_code_content' => $qrContent,
                'qr_type' => $request->qr_type,
                'qr_options' => [
                    'size' => $size,
                    'format' => $format,
                    'include_info' => $request->include_info ?? []
                ],
                'size' => $size,
                'format' => $format,
                'is_active' => true,
            ]);

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                $equipmentId,
                auth()->id(),
                'create',
                'qrcode',
                '生成设备二维码',
                null,
                ['qrcode_id' => $qrcodeRecord->id, 'qr_type' => $request->qr_type]
            );

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => '二维码生成成功',
                'data' => [
                    'id' => $qrcodeRecord->id,
                    'qr_code_url' => $qrcodeRecord->qr_code_url,
                    'qr_type' => $qrcodeRecord->qr_type,
                    'size' => $qrcodeRecord->size,
                    'format' => $qrcodeRecord->format,
                    'equipment_name' => $equipment->name,
                    'equipment_code' => $equipment->code,
                    'created_at' => $qrcodeRecord->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '二维码生成失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取设备二维码
     */
    public function show($equipmentId): JsonResponse
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $qrcode = EquipmentQrcode::where('equipment_id', $equipmentId)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$qrcode) {
            return response()->json([
                'code' => 404,
                'message' => '设备二维码不存在'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'id' => $qrcode->id,
                'qr_code_url' => $qrcode->qr_code_url,
                'qr_type' => $qrcode->qr_type,
                'size' => $qrcode->size,
                'format' => $qrcode->format,
                'download_count' => $qrcode->download_count,
                'scan_count' => $qrcode->scan_count,
                'last_scanned_at' => $qrcode->last_scanned_at?->format('Y-m-d H:i:s'),
                'equipment_name' => $equipment->name,
                'equipment_code' => $equipment->code,
                'created_at' => $qrcode->created_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * 批量生成二维码
     */
    public function batchGenerate(Request $request): JsonResponse
    {
        $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'integer|exists:equipments,id',
            'qr_type' => 'required|in:basic,detailed,url',
            'size' => 'integer|min:100|max:500',
            'format' => 'string|in:png,jpg,svg',
            'output_format' => 'required|in:pdf,images,excel',
            'template' => 'string|in:standard,simple,detailed',
            'columns' => 'integer|min:1|max:6',
            'rows' => 'integer|min:1|max:10',
            'page_size' => 'string|in:A4,A3,Letter'
        ]);

        try {
            DB::beginTransaction();

            $equipmentIds = $request->equipment_ids;
            $equipments = Equipment::whereIn('id', $equipmentIds)->get();
            $results = [];
            $successCount = 0;

            foreach ($equipments as $equipment) {
                try {
                    // 生成二维码内容
                    $qrContent = $this->generateQrContent($equipment, $request->qr_type, []);
                    
                    // 生成二维码图片
                    $size = $request->get('size', 200);
                    $format = $request->get('format', 'png');
                    
                    $qrCode = QrCode::format($format)
                        ->size($size)
                        ->margin(1)
                        ->generate($qrContent);

                    // 保存二维码文件
                    $filename = 'qrcode_' . $equipment->code . '_' . time() . '.' . $format;
                    $filePath = 'qrcodes/' . $filename;
                    Storage::disk('public')->put($filePath, $qrCode);

                    // 停用之前的二维码
                    EquipmentQrcode::where('equipment_id', $equipment->id)
                        ->where('is_active', true)
                        ->update(['is_active' => false]);

                    // 创建新的二维码记录
                    $qrcodeRecord = EquipmentQrcode::create([
                        'equipment_id' => $equipment->id,
                        'qr_code_url' => Storage::url($filePath),
                        'qr_code_content' => $qrContent,
                        'qr_type' => $request->qr_type,
                        'qr_options' => [
                            'size' => $size,
                            'format' => $format,
                            'batch_generated' => true
                        ],
                        'size' => $size,
                        'format' => $format,
                        'is_active' => true,
                    ]);

                    $results[] = [
                        'equipment_id' => $equipment->id,
                        'equipment_name' => $equipment->name,
                        'equipment_code' => $equipment->code,
                        'qr_code_url' => $qrcodeRecord->qr_code_url,
                        'success' => true
                    ];

                    $successCount++;
                } catch (\Exception $e) {
                    $results[] = [
                        'equipment_id' => $equipment->id,
                        'equipment_name' => $equipment->name,
                        'equipment_code' => $equipment->code,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // 根据输出格式生成文件
            $outputFile = null;
            switch ($request->output_format) {
                case 'pdf':
                    $outputFile = $this->generatePdfOutput($results, $request->all());
                    break;
                case 'images':
                    $outputFile = $this->generateZipOutput($results);
                    break;
                case 'excel':
                    $outputFile = $this->generateExcelOutput($results);
                    break;
            }

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                0,
                auth()->id(),
                'batch_generate',
                'qrcode',
                '批量生成设备二维码',
                null,
                [
                    'equipment_count' => count($equipmentIds),
                    'success_count' => $successCount,
                    'output_format' => $request->output_format
                ]
            );

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => "批量生成完成，成功生成 {$successCount} 个二维码",
                'data' => [
                    'results' => $results,
                    'success_count' => $successCount,
                    'total_count' => count($equipmentIds),
                    'output_file' => $outputFile
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '批量生成失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 扫码查询设备信息
     */
    public function scan($code): JsonResponse
    {
        try {
            // 解析二维码内容，提取设备编号
            $equipmentCode = $this->parseQrCode($code);
            
            if (!$equipmentCode) {
                return response()->json([
                    'code' => 404,
                    'message' => '无效的二维码'
                ], 404);
            }

            $equipment = Equipment::with(['category', 'school', 'activeQrcode'])
                ->where('code', $equipmentCode)
                ->first();

            if (!$equipment) {
                return response()->json([
                    'code' => 404,
                    'message' => '设备不存在'
                ], 404);
            }

            // 增加扫描次数
            if ($equipment->activeQrcode) {
                $equipment->activeQrcode->incrementScanCount();
            }

            return response()->json([
                'code' => 200,
                'message' => '扫码成功',
                'data' => [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'code' => $equipment->code,
                    'model' => $equipment->model,
                    'brand' => $equipment->brand,
                    'category' => $equipment->category->name ?? '',
                    'location' => $equipment->location,
                    'status' => $equipment->status,
                    'status_text' => $equipment->status_text,
                    'condition_status' => $equipment->condition_status,
                    'condition_status_text' => $equipment->condition_status_text,
                    'responsible_person' => $equipment->responsible_person,
                    'contact_phone' => $equipment->contact_phone,
                    'description' => $equipment->description,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '扫码失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除设备二维码
     */
    public function destroy($equipmentId): JsonResponse
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $qrcode = EquipmentQrcode::where('equipment_id', $equipmentId)
            ->where('is_active', true)
            ->first();

        if (!$qrcode) {
            return response()->json([
                'code' => 404,
                'message' => '设备二维码不存在'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // 删除文件
            $filePath = str_replace('/storage/', '', $qrcode->qr_code_url);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // 记录操作日志
            EquipmentOperationLog::logOperation(
                $equipmentId,
                auth()->id(),
                'delete',
                'qrcode',
                '删除设备二维码',
                ['qrcode_id' => $qrcode->id],
                null
            );

            $qrcode->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => '二维码删除成功'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '二维码删除失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 生成二维码内容
     */
    private function generateQrContent(Equipment $equipment, string $type, array $includeInfo = []): string
    {
        $baseUrl = config('app.url');

        switch ($type) {
            case 'basic':
                return json_encode([
                    'type' => 'equipment',
                    'id' => $equipment->id,
                    'code' => $equipment->code,
                    'name' => $equipment->name,
                ]);

            case 'detailed':
                $data = [
                    'type' => 'equipment',
                    'id' => $equipment->id,
                    'code' => $equipment->code,
                    'name' => $equipment->name,
                    'model' => $equipment->model,
                    'brand' => $equipment->brand,
                    'category' => $equipment->category->name ?? '',
                    'status' => $equipment->status_text,
                ];

                if (in_array('location', $includeInfo)) {
                    $data['location'] = $equipment->location;
                }

                if (in_array('contact', $includeInfo)) {
                    $data['contact'] = $equipment->contact_phone;
                }

                return json_encode($data);

            case 'url':
                return $baseUrl . "/equipment/{$equipment->id}";

            default:
                return $equipment->code;
        }
    }

    /**
     * 解析二维码内容
     */
    private function parseQrCode(string $code): ?string
    {
        // 尝试解析JSON格式
        $decoded = json_decode($code, true);
        if ($decoded && isset($decoded['code'])) {
            return $decoded['code'];
        }

        // 尝试解析URL格式
        if (strpos($code, '/equipment/') !== false) {
            preg_match('/\/equipment\/(\d+)/', $code, $matches);
            if (isset($matches[1])) {
                $equipment = Equipment::find($matches[1]);
                return $equipment ? $equipment->code : null;
            }
        }

        // 直接作为设备编号
        return $code;
    }

    /**
     * 生成PDF输出
     */
    private function generatePdfOutput(array $results, array $options): string
    {
        $successResults = array_filter($results, function($result) {
            return $result['success'];
        });

        $pdf = Pdf::loadView('qrcode.pdf', [
            'results' => $successResults,
            'options' => $options,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);

        $filename = 'qrcodes_' . date('Y-m-d_H-i-s') . '.pdf';
        $filePath = 'exports/' . $filename;

        Storage::disk('public')->put($filePath, $pdf->output());

        return Storage::url($filePath);
    }

    /**
     * 生成ZIP输出
     */
    private function generateZipOutput(array $results): string
    {
        $zip = new \ZipArchive();
        $filename = 'qrcodes_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/public/exports/' . $filename);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($results as $result) {
                if ($result['success']) {
                    $qrCodePath = str_replace('/storage/', '', $result['qr_code_url']);
                    $qrCodeFullPath = storage_path('app/public/' . $qrCodePath);

                    if (file_exists($qrCodeFullPath)) {
                        $zip->addFile($qrCodeFullPath, $result['equipment_code'] . '.png');
                    }
                }
            }
            $zip->close();
        }

        return Storage::url('exports/' . $filename);
    }

    /**
     * 生成Excel输出
     */
    private function generateExcelOutput(array $results): string
    {
        // 这里需要实现Excel导出逻辑
        // 可以使用Laravel Excel包
        $filename = 'qrcodes_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = 'exports/' . $filename;

        // 简化实现，实际应该使用Excel导出
        $csvContent = "设备编号,设备名称,二维码URL,生成状态\n";
        foreach ($results as $result) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s\n",
                $result['equipment_code'],
                $result['equipment_name'],
                $result['qr_code_url'] ?? '',
                $result['success'] ? '成功' : '失败'
            );
        }

        Storage::disk('public')->put($filePath, $csvContent);

        return Storage::url($filePath);
    }
}

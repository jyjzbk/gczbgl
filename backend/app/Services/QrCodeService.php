<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * 生成设备二维码
     */
    public function generateEquipmentQrCode(
        string $qrCodeString,
        string $equipmentName,
        array $options = []
    ): array {
        // 默认配置
        $defaultOptions = [
            'size' => 300,
            'margin' => 10,
            'format' => 'png'
        ];

        $options = array_merge($defaultOptions, $options);

        // 构建二维码URL
        $qrUrl = url("/api/equipment/qr/{$qrCodeString}");

        // 暂时不生成实际的二维码文件，只返回二维码信息
        // 在生产环境中，这里应该使用正确的二维码库API来生成图片文件

        $fileName = "qr_codes/equipment_{$qrCodeString}." . $options['format'];

        return [
            'qr_code' => $qrCodeString,
            'qr_url' => $qrUrl,
            'file_path' => $fileName,
            'file_url' => Storage::url($fileName),
            'full_path' => storage_path("app/public/{$fileName}"),
            'size' => $options['size'],
            'format' => $options['format'],
            'note' => '二维码信息已生成，实际图片文件需要配置正确的二维码库'
        ];
    }

    /**
     * 生成批量设备二维码
     */
    public function generateBatchEquipmentQrCodes(array $equipments, array $options = []): array
    {
        $results = [];
        
        foreach ($equipments as $equipment) {
            try {
                $qrCode = $equipment['qr_code'] ?? $equipment->qr_code;
                $name = $equipment['name'] ?? $equipment->name;
                
                $result = $this->generateEquipmentQrCode($qrCode, $name, $options);
                $results[] = [
                    'equipment_id' => $equipment['id'] ?? $equipment->id,
                    'success' => true,
                    'data' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'equipment_id' => $equipment['id'] ?? $equipment->id,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * 生成设备标签（包含二维码和设备信息）
     */
    public function generateEquipmentLabel(
        string $qrCode,
        array $equipmentInfo,
        array $options = []
    ): array {
        // 默认标签配置
        $defaultOptions = [
            'width' => 400,
            'height' => 200,
            'qr_size' => 120,
            'font_size' => 12,
            'include_fields' => ['name', 'code', 'category', 'school'],
            'format' => 'png'
        ];

        $options = array_merge($defaultOptions, $options);

        // 首先生成二维码
        $qrResult = $this->generateEquipmentQrCode($qrCode, $equipmentInfo['name'], [
            'size' => $options['qr_size'],
            'format' => $options['format'],
            'include_label' => false
        ]);

        // 这里可以扩展为生成包含设备信息的完整标签
        // 由于需要图像处理库，暂时返回二维码信息
        return [
            'qr_code' => $qrCode,
            'label_info' => $equipmentInfo,
            'qr_result' => $qrResult,
            'options' => $options
        ];
    }

    /**
     * 验证二维码
     */
    public function validateQrCode(string $qrCode): bool
    {
        // 验证二维码格式
        if (empty($qrCode)) {
            return false;
        }

        // 验证二维码是否符合设备二维码格式
        if (!preg_match('/^EQ_\d+_\d+$/', $qrCode)) {
            return false;
        }

        return true;
    }

    /**
     * 解析二维码信息
     */
    public function parseQrCode(string $qrCode): array
    {
        if (!$this->validateQrCode($qrCode)) {
            throw new \InvalidArgumentException('无效的二维码格式');
        }

        // 解析二维码：EQ_设备ID_时间戳
        $parts = explode('_', $qrCode);
        
        return [
            'prefix' => $parts[0],
            'equipment_id' => (int)$parts[1],
            'timestamp' => (int)$parts[2],
            'generated_at' => date('Y-m-d H:i:s', $parts[2])
        ];
    }

    /**
     * 删除二维码文件
     */
    public function deleteQrCodeFile(string $qrCode, string $format = 'png'): bool
    {
        $fileName = "qr_codes/equipment_{$qrCode}.{$format}";
        
        if (Storage::disk('public')->exists($fileName)) {
            return Storage::disk('public')->delete($fileName);
        }

        return true;
    }

    /**
     * 获取二维码文件信息
     */
    public function getQrCodeFileInfo(string $qrCode, string $format = 'png'): ?array
    {
        $fileName = "qr_codes/equipment_{$qrCode}.{$format}";
        
        if (!Storage::disk('public')->exists($fileName)) {
            return null;
        }

        $filePath = Storage::disk('public')->path($fileName);
        
        return [
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_url' => Storage::url($fileName),
            'file_size' => filesize($filePath),
            'created_at' => date('Y-m-d H:i:s', filectime($filePath)),
            'modified_at' => date('Y-m-d H:i:s', filemtime($filePath))
        ];
    }

    /**
     * 清理过期的二维码文件
     */
    public function cleanupExpiredQrCodes(int $daysOld = 30): array
    {
        $qrCodePath = storage_path('app/public/qr_codes');
        
        if (!is_dir($qrCodePath)) {
            return ['deleted' => 0, 'errors' => []];
        }

        $deletedCount = 0;
        $errors = [];
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);

        $files = glob($qrCodePath . '/equipment_*.{png,svg}', GLOB_BRACE);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                if (unlink($file)) {
                    $deletedCount++;
                } else {
                    $errors[] = "无法删除文件: " . basename($file);
                }
            }
        }

        return [
            'deleted' => $deletedCount,
            'errors' => $errors
        ];
    }

    /**
     * 获取二维码统计信息
     */
    public function getQrCodeStatistics(): array
    {
        $qrCodePath = storage_path('app/public/qr_codes');
        
        if (!is_dir($qrCodePath)) {
            return [
                'total_files' => 0,
                'total_size' => 0,
                'formats' => []
            ];
        }

        $files = glob($qrCodePath . '/equipment_*.{png,svg}', GLOB_BRACE);
        $totalSize = 0;
        $formats = [];

        foreach ($files as $file) {
            $size = filesize($file);
            $totalSize += $size;
            
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $formats[$extension] = ($formats[$extension] ?? 0) + 1;
        }

        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'formats' => $formats
        ];
    }
}

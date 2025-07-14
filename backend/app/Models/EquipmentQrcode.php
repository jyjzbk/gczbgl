<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentQrcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'qr_code_url',
        'qr_code_content',
        'qr_type',
        'qr_options',
        'size',
        'format',
        'download_count',
        'scan_count',
        'last_scanned_at',
        'is_active',
    ];

    protected $casts = [
        'qr_options' => 'array',
        'download_count' => 'integer',
        'scan_count' => 'integer',
        'last_scanned_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 关联设备
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * 增加下载次数
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * 增加扫描次数
     */
    public function incrementScanCount(): void
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    /**
     * 获取二维码类型文本
     */
    public function getQrTypeTextAttribute(): string
    {
        $types = [
            'basic' => '基础信息',
            'detailed' => '详细信息',
            'url' => '链接地址',
        ];

        return $types[$this->qr_type] ?? '未知';
    }

    /**
     * 获取格式化的文件大小
     */
    public function getFormattedSizeAttribute(): string
    {
        return $this->size . 'x' . $this->size;
    }

    /**
     * 作用域：活跃的二维码
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeByType($query, $type)
    {
        return $query->where('qr_type', $type);
    }

    /**
     * 作用域：按设备筛选
     */
    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }
}

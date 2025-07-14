<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EquipmentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'file_extension',
        'attachment_type',
        'description',
        'sort_order',
        'is_primary',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
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
     * 关联上传者
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * 获取文件URL
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * 获取格式化的文件大小
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * 获取文件类型文本
     */
    public function getFileTypeTextAttribute(): string
    {
        $types = [
            'image' => '图片',
            'document' => '文档',
            'video' => '视频',
            'audio' => '音频',
            'other' => '其他',
        ];

        return $types[$this->file_type] ?? '未知';
    }

    /**
     * 获取附件类型文本
     */
    public function getAttachmentTypeTextAttribute(): string
    {
        $types = [
            'photo' => '设备照片',
            'manual' => '使用手册',
            'certificate' => '合格证书',
            'warranty' => '保修卡',
            'invoice' => '发票',
            'other' => '其他',
        ];

        return $types[$this->attachment_type] ?? '未知';
    }

    /**
     * 检查是否为图片
     */
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    /**
     * 检查是否为文档
     */
    public function isDocument(): bool
    {
        return $this->file_type === 'document';
    }

    /**
     * 作用域：按设备筛选
     */
    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }

    /**
     * 作用域：按文件类型筛选
     */
    public function scopeByFileType($query, $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    /**
     * 作用域：按附件类型筛选
     */
    public function scopeByAttachmentType($query, $attachmentType)
    {
        return $query->where('attachment_type', $attachmentType);
    }

    /**
     * 作用域：主图片
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * 作用域：图片类型
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    /**
     * 作用域：按排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * 删除文件
     */
    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }
        return true;
    }

    /**
     * 模型删除时删除文件
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            $attachment->deleteFile();
        });
    }
}

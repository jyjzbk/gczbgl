<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ExperimentWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_id',
        'student_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'metadata',
        'quality_score',
        'teacher_comment',
        'is_featured',
        'is_public',
        'uploaded_by'
    ];

    protected $casts = [
        'record_id' => 'integer',
        'student_id' => 'integer',
        'quality_score' => 'integer',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'uploaded_by' => 'integer',
        'metadata' => 'array'
    ];

    // 作品类型常量
    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';
    const TYPE_DOCUMENT = 'document';
    const TYPE_OTHER = 'other';

    /**
     * 关联实验记录
     */
    public function experimentRecord(): BelongsTo
    {
        return $this->belongsTo(ExperimentRecord::class, 'record_id');
    }

    /**
     * 关联学生
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * 关联上传人
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
     * 获取文件大小（格式化）
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * 获取作品类型名称
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            self::TYPE_PHOTO => '照片',
            self::TYPE_VIDEO => '视频',
            self::TYPE_DOCUMENT => '文档',
            self::TYPE_OTHER => '其他',
            default => '未知'
        };
    }

    /**
     * 是否为图片类型
     */
    public function isImage(): bool
    {
        return $this->type === self::TYPE_PHOTO || 
               str_starts_with($this->mime_type, 'image/');
    }

    /**
     * 是否为视频类型
     */
    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO || 
               str_starts_with($this->mime_type, 'video/');
    }

    /**
     * 获取缩略图URL（如果是图片）
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->isImage()) {
            return null;
        }

        // 生成缩略图路径
        $pathInfo = pathinfo($this->file_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        
        if (Storage::exists($thumbnailPath)) {
            return Storage::url($thumbnailPath);
        }

        return $this->file_url;
    }

    /**
     * 作用域：按实验记录筛选
     */
    public function scopeByRecord($query, $recordId)
    {
        return $query->where('record_id', $recordId);
    }

    /**
     * 作用域：按学生筛选
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：精选作品
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * 作用域：公开作品
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * 作用域：按质量评分筛选
     */
    public function scopeByQualityScore($query, $minScore)
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    /**
     * 删除文件
     */
    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        // 删除缩略图
        if ($this->isImage()) {
            $pathInfo = pathinfo($this->file_path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            if (Storage::exists($thumbnailPath)) {
                Storage::delete($thumbnailPath);
            }
        }

        return true;
    }

    /**
     * 模型删除时自动删除文件
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($work) {
            $work->deleteFile();
        });
    }
}

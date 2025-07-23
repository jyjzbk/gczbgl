<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExperimentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'school_id',
        'catalog_id',
        'laboratory_id',
        'teacher_id',
        'class_name',
        'student_count',
        'start_time',
        'end_time',
        'completion_rate',
        'quality_score',
        'photos',
        'videos',
        'summary',
        'problems',
        'suggestions',
        'work_count',
        'attendance_data',
        'equipment_usage_rate',
        'safety_incidents',
        'status'
    ];

    protected $casts = [
        'reservation_id' => 'integer',
        'school_id' => 'integer',
        'catalog_id' => 'integer',
        'laboratory_id' => 'integer',
        'teacher_id' => 'integer',
        'student_count' => 'integer',
        'completion_rate' => 'decimal:2',
        'quality_score' => 'integer',
        'photos' => 'array',
        'videos' => 'array',
        'work_count' => 'integer',
        'attendance_data' => 'array',
        'equipment_usage_rate' => 'decimal:2',
        'status' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    // 状态常量
    const STATUS_IN_PROGRESS = 1; // 进行中
    const STATUS_COMPLETED = 2;   // 已完成
    const STATUS_ABNORMAL = 3;    // 异常结束

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_IN_PROGRESS => '进行中',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_ABNORMAL => '异常结束'
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_ABNORMAL => 'danger'
        ];
        return $colors[$this->status] ?? 'default';
    }

    /**
     * 关联预约
     */
    public function reservation()
    {
        return $this->belongsTo(ExperimentReservation::class, 'reservation_id');
    }

    /**
     * 关联学校
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联实验目录
     */
    public function catalog()
    {
        return $this->belongsTo(ExperimentCatalog::class, 'catalog_id');
    }

    /**
     * 关联实验室
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * 关联教师
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按教师筛选
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }

    /**
     * 作用域：已完成
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * 获取实验持续时间（分钟）
     */
    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInMinutes($this->end_time);
        }
        return 0;
    }

    /**
     * 获取实验持续时间文本
     */
    public function getDurationTextAttribute()
    {
        $duration = $this->duration;
        if ($duration < 60) {
            return $duration . '分钟';
        } else {
            $hours = intval($duration / 60);
            $minutes = $duration % 60;
            return $hours . '小时' . ($minutes > 0 ? $minutes . '分钟' : '');
        }
    }

    /**
     * 获取质量评分文本
     */
    public function getQualityScoreTextAttribute()
    {
        if (!$this->quality_score) {
            return '未评分';
        }

        $scores = [
            1 => '很差',
            2 => '较差',
            3 => '一般',
            4 => '良好',
            5 => '优秀'
        ];
        return $scores[$this->quality_score] ?? '未知';
    }

    /**
     * 检查是否可以编辑
     */
    public function canEdit()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * 检查是否可以完成
     */
    public function canComplete()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * 获取照片数量
     */
    public function getPhotosCountAttribute()
    {
        return is_array($this->photos) ? count($this->photos) : 0;
    }

    /**
     * 获取视频数量
     */
    public function getVideosCountAttribute()
    {
        return is_array($this->videos) ? count($this->videos) : 0;
    }

    /**
     * 自动完成预约状态更新
     */
    protected static function booted()
    {
        static::created(function ($record) {
            // 创建记录时，更新预约状态为已完成
            if ($record->reservation) {
                $record->reservation->update([
                    'status' => ExperimentReservation::STATUS_COMPLETED
                ]);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperimentReservationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'subject_id',
        'grade',
        'semester',
        'template_data',
        'created_by',
        'is_active',
        'description',
        'use_count'
    ];

    protected $casts = [
        'school_id' => 'integer',
        'subject_id' => 'integer',
        'grade' => 'integer',
        'semester' => 'integer',
        'created_by' => 'integer',
        'is_active' => 'boolean',
        'use_count' => 'integer',
        'template_data' => 'array'
    ];

    /**
     * 关联学校
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联学科
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * 关联创建人
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 关联预约批次
     */
    public function reservationBatches(): HasMany
    {
        return $this->hasMany(ReservationBatch::class, 'template_id');
    }

    /**
     * 学期名称
     */
    public function getSemesterNameAttribute(): string
    {
        return $this->semester == 1 ? '上学期' : '下学期';
    }

    /**
     * 年级名称
     */
    public function getGradeNameAttribute(): string
    {
        return $this->grade . '年级';
    }

    /**
     * 增加使用次数
     */
    public function incrementUseCount(): void
    {
        $this->increment('use_count');
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按学科筛选
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * 作用域：按年级筛选
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * 作用域：按学期筛选
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * 作用域：只显示启用的模板
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

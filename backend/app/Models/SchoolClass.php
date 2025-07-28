<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'grade',
        'class_number',
        'student_count',
        'head_teacher_id',
        'classroom_location',
        'status'
    ];

    protected $casts = [
        'school_id' => 'integer',
        'grade' => 'integer',
        'class_number' => 'integer',
        'student_count' => 'integer',
        'head_teacher_id' => 'integer',
        'status' => 'integer'
    ];

    // 状态常量
    const STATUS_ACTIVE = 1;   // 正常
    const STATUS_INACTIVE = 0; // 停用

    /**
     * 关联学校
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联班主任
     */
    public function headTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_teacher_id');
    }

    /**
     * 关联实验预约
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(ExperimentReservation::class, 'class_id');
    }

    /**
     * 获取年级名称
     */
    public function getGradeNameAttribute(): string
    {
        $grades = [
            1 => '一年级', 2 => '二年级', 3 => '三年级',
            4 => '四年级', 5 => '五年级', 6 => '六年级',
            7 => '七年级', 8 => '八年级', 9 => '九年级'
        ];
        return $grades[$this->grade] ?? '未知年级';
    }

    /**
     * 获取完整班级名称
     */
    public function getFullNameAttribute(): string
    {
        return $this->grade_name . '（' . $this->class_number . '）';
    }

    /**
     * 自动生成班级代码
     */
    public static function generateCode(int $schoolId, int $grade, int $classNumber): string
    {
        return 'G' . $grade . 'C' . $classNumber;
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按年级筛选
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * 作用域：正常状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}

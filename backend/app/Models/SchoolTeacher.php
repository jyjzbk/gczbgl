<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'employee_number',
        'subject',
        'teaching_grades',
        'title',
        'education',
        'join_date',
        'status'
    ];

    protected $casts = [
        'school_id' => 'integer',
        'user_id' => 'integer',
        'teaching_grades' => 'array',
        'join_date' => 'date',
        'status' => 'integer'
    ];

    // 状态常量
    const STATUS_ACTIVE = 1;   // 在职
    const STATUS_RESIGNED = 2; // 离职
    const STATUS_INACTIVE = 0; // 停用

    /**
     * 关联学校
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取状态名称
     */
    public function getStatusNameAttribute(): string
    {
        $statuses = [
            self::STATUS_ACTIVE => '在职',
            self::STATUS_RESIGNED => '离职',
            self::STATUS_INACTIVE => '停用'
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 获取任教年级名称
     */
    public function getTeachingGradeNamesAttribute(): array
    {
        if (!$this->teaching_grades) {
            return [];
        }

        $grades = [
            1 => '一年级', 2 => '二年级', 3 => '三年级',
            4 => '四年级', 5 => '五年级', 6 => '六年级',
            7 => '七年级', 8 => '八年级', 9 => '九年级'
        ];

        return array_map(function($grade) use ($grades) {
            return $grades[$grade] ?? '未知年级';
        }, $this->teaching_grades);
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
    public function scopeBySubject($query, $subject)
    {
        return $query->where('subject', $subject);
    }

    /**
     * 作用域：在职状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}

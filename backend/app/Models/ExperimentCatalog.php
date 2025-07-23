<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperimentCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'textbook_version_id',
        'chapter_id',
        'grade_level',
        'volume',
        'management_level',
        'experiment_type',
        'parent_catalog_id',
        'original_catalog_id',
        'version',
        'is_deleted_by_lower',
        'delete_reason',
        'created_by_level',
        'created_by_org_id',
        'created_by_org_type',
        'name',
        'code',
        'type',
        'grade',
        'semester',
        'chapter',
        'duration',
        'student_count',
        'objective',
        'materials',
        'procedure',
        'safety_notes',
        'difficulty_level',
        'is_standard',
        'status'
    ];

    protected $casts = [
        'subject_id' => 'integer',
        'type' => 'integer',
        'grade' => 'integer',
        'semester' => 'integer',
        'duration' => 'integer',
        'student_count' => 'integer',
        'difficulty_level' => 'integer',
        'is_standard' => 'integer',
        'status' => 'integer'
    ];

    // 实验类型常量
    const TYPE_REQUIRED = 1;    // 必做实验
    const TYPE_OPTIONAL = 2;    // 选做实验
    const TYPE_DEMO = 3;        // 演示实验
    const TYPE_GROUP = 4;       // 分组实验

    // 学期常量
    const SEMESTER_FIRST = 1;   // 上学期
    const SEMESTER_SECOND = 2;  // 下学期

    // 状态常量
    const STATUS_ACTIVE = 1;    // 启用
    const STATUS_INACTIVE = 0;  // 禁用

    /**
     * 获取实验类型文本
     */
    public function getTypeTextAttribute()
    {
        $types = [
            self::TYPE_REQUIRED => '必做实验',
            self::TYPE_OPTIONAL => '选做实验',
            self::TYPE_DEMO => '演示实验',
            self::TYPE_GROUP => '分组实验'
        ];
        return $types[$this->type] ?? '未知';
    }

    /**
     * 获取学期文本
     */
    public function getSemesterTextAttribute()
    {
        $semesters = [
            self::SEMESTER_FIRST => '上学期',
            self::SEMESTER_SECOND => '下学期'
        ];
        return $semesters[$this->semester] ?? '未知';
    }

    /**
     * 获取是否标准实验文本
     */
    public function getIsStandardTextAttribute()
    {
        return $this->is_standard ? '标准实验' : '自定义实验';
    }

    /**
     * 关联学科
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * 关联教材版本
     */
    public function textbookVersion()
    {
        return $this->belongsTo(TextbookVersion::class);
    }

    /**
     * 关联章节
     */
    public function chapter()
    {
        return $this->belongsTo(TextbookChapter::class, 'chapter_id');
    }

    /**
     * 关联上级实验目录
     */
    public function parentCatalog()
    {
        return $this->belongsTo(ExperimentCatalog::class, 'parent_catalog_id');
    }

    /**
     * 关联下级实验目录
     */
    public function childCatalogs()
    {
        return $this->hasMany(ExperimentCatalog::class, 'parent_catalog_id');
    }

    /**
     * 关联原始实验目录
     */
    public function originalCatalog()
    {
        return $this->belongsTo(ExperimentCatalog::class, 'original_catalog_id');
    }

    /**
     * 关联删除记录
     */
    public function deletionRecords()
    {
        return $this->hasMany(ExperimentCatalogDeletion::class, 'catalog_id');
    }

    /**
     * 关联实验预约
     */
    public function reservations()
    {
        return $this->hasMany(ExperimentReservation::class, 'catalog_id');
    }

    /**
     * 关联实验记录
     */
    public function records()
    {
        return $this->hasMany(ExperimentRecord::class, 'catalog_id');
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
     * 作用域：按类型筛选
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：标准实验
     */
    public function scopeStandard($query)
    {
        return $query->where('is_standard', 1);
    }

    /**
     * 作用域：启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * 关联器材需求配置
     */
    public function equipmentRequirements(): HasMany
    {
        return $this->hasMany(ExperimentEquipmentRequirement::class, 'catalog_id');
    }

    /**
     * 关联启用的器材需求配置
     */
    public function activeEquipmentRequirements(): HasMany
    {
        return $this->hasMany(ExperimentEquipmentRequirement::class, 'catalog_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * 获取器材配置数量
     */
    public function getEquipmentCountAttribute(): int
    {
        return $this->equipmentRequirements()->count();
    }

    /**
     * 检查是否已配置器材
     */
    public function hasEquipmentConfig(): bool
    {
        return $this->equipmentRequirements()->exists();
    }
}


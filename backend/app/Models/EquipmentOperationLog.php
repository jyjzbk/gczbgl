<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentOperationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'operation_type',
        'operation_module',
        'operation_description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'extra_data',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'extra_data' => 'array',
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
     * 关联用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取操作类型文本
     */
    public function getOperationTypeTextAttribute(): string
    {
        $types = [
            'create' => '创建',
            'update' => '更新',
            'delete' => '删除',
            'borrow' => '借用',
            'return' => '归还',
            'maintenance' => '维修',
            'qrcode' => '二维码操作',
            'photo' => '照片操作',
            'export' => '导出',
            'import' => '导入',
        ];

        return $types[$this->operation_type] ?? '未知操作';
    }

    /**
     * 获取操作模块文本
     */
    public function getOperationModuleTextAttribute(): string
    {
        $modules = [
            'equipment' => '设备档案',
            'borrow' => '设备借用',
            'maintenance' => '设备维修',
            'qrcode' => '二维码管理',
            'attachment' => '附件管理',
        ];

        return $modules[$this->operation_module] ?? '未知模块';
    }

    /**
     * 作用域：按设备筛选
     */
    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }

    /**
     * 作用域：按用户筛选
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 作用域：按操作类型筛选
     */
    public function scopeByOperationType($query, $type)
    {
        return $query->where('operation_type', $type);
    }

    /**
     * 作用域：按操作模块筛选
     */
    public function scopeByOperationModule($query, $module)
    {
        return $query->where('operation_module', $module);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * 静态方法：记录操作日志
     */
    public static function logOperation(
        int $equipmentId,
        int $userId,
        string $operationType,
        string $operationModule,
        string $description,
        array $oldData = null,
        array $newData = null,
        array $extraData = null
    ): self {
        return self::create([
            'equipment_id' => $equipmentId,
            'user_id' => $userId,
            'operation_type' => $operationType,
            'operation_module' => $operationModule,
            'operation_description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'extra_data' => $extraData,
        ]);
    }
}

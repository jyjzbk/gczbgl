<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'school_id',
        'category_id',
        'name',
        'code',
        'model',
        'brand',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'supplier',
        'warranty_period',
        'location',
        'status',
        'condition_status',
        'description',
        'specifications',
        'photos',
        'qr_code',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'warranty_period' => 'integer',
        'status' => 'integer',
        'condition_status' => 'integer',
        'photos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'status_text',
        'condition_text',
        'warranty_status',
        'is_available'
    ];

    // 设备状态常量
    const STATUS_NORMAL = 1;      // 正常
    const STATUS_BORROWED = 2;    // 借出
    const STATUS_MAINTENANCE = 3; // 维修
    const STATUS_SCRAPPED = 4;    // 报废

    // 设备状况常量
    const CONDITION_EXCELLENT = 1; // 优
    const CONDITION_GOOD = 2;      // 良
    const CONDITION_FAIR = 3;      // 中
    const CONDITION_POOR = 4;      // 差

    /**
     * 关联学校
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联设备分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    /**
     * 关联借用记录
     */
    public function borrows(): HasMany
    {
        return $this->hasMany(EquipmentBorrow::class);
    }

    /**
     * 关联维修记录
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    /**
     * 关联操作日志
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(EquipmentOperationLog::class);
    }

    /**
     * 关联二维码
     */
    public function qrcodes(): HasMany
    {
        return $this->hasMany(EquipmentQrcode::class);
    }

    /**
     * 关联附件
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(EquipmentAttachment::class);
    }

    /**
     * 获取当前借用记录
     */
    public function currentBorrow(): HasMany
    {
        return $this->hasMany(EquipmentBorrow::class)
            ->whereIn('status', [EquipmentBorrow::STATUS_APPROVED, EquipmentBorrow::STATUS_BORROWED])
            ->latest();
    }

    /**
     * 获取当前维修记录
     */
    public function currentMaintenance(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class)
            ->whereIn('status', [EquipmentMaintenance::STATUS_PENDING, EquipmentMaintenance::STATUS_PROCESSING])
            ->latest();
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute(): string
    {
        $statusMap = [
            self::STATUS_NORMAL => '正常',
            self::STATUS_BORROWED => '借出',
            self::STATUS_MAINTENANCE => '维修',
            self::STATUS_SCRAPPED => '报废',
        ];

        return $statusMap[$this->status] ?? '未知';
    }

    /**
     * 获取状况文本
     */
    public function getConditionTextAttribute(): string
    {
        $conditionMap = [
            self::CONDITION_EXCELLENT => '优',
            self::CONDITION_GOOD => '良',
            self::CONDITION_FAIR => '中',
            self::CONDITION_POOR => '差',
        ];

        return $conditionMap[$this->condition_status] ?? '未知';
    }

    /**
     * 获取保修状态
     */
    public function getWarrantyStatusAttribute(): array
    {
        $purchaseDate = $this->purchase_date;
        $warrantyEndDate = $purchaseDate->addMonths($this->warranty_period);
        $now = now();

        $isInWarranty = $now->lte($warrantyEndDate);
        $remainingDays = $isInWarranty ? $now->diffInDays($warrantyEndDate) : 0;
        $expiredDays = !$isInWarranty ? $warrantyEndDate->diffInDays($now) : 0;

        return [
            'is_in_warranty' => $isInWarranty,
            'warranty_end_date' => $warrantyEndDate->format('Y-m-d'),
            'remaining_days' => $remainingDays,
            'expired_days' => $expiredDays,
        ];
    }

    /**
     * 检查设备是否可用
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === self::STATUS_NORMAL;
    }

    /**
     * 检查设备在指定时间段是否可用
     */
    public function isAvailableForPeriod(string $startDate, string $endDate): array
    {
        // 检查设备状态
        if (!$this->is_available) {
            return [
                'available' => false,
                'reasons' => ['设备当前状态不可借用：' . $this->status_text]
            ];
        }

        // 检查是否有冲突的借用记录
        $conflictBorrows = $this->borrows()
            ->whereIn('status', [EquipmentBorrow::STATUS_APPROVED, EquipmentBorrow::STATUS_BORROWED])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('borrow_date', [$startDate, $endDate])
                    ->orWhereBetween('expected_return_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('borrow_date', '<=', $startDate)
                          ->where('expected_return_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($conflictBorrows) {
            return [
                'available' => false,
                'reasons' => ['该时间段设备已被借用']
            ];
        }

        return [
            'available' => true,
            'reasons' => []
        ];
    }

    /**
     * 获取已借用数量
     */
    public function getBorrowedQuantityAttribute()
    {
        return $this->borrows()
            ->where('status', EquipmentBorrow::STATUS_BORROWED)
            ->sum('quantity');
    }

    /**
     * 检查是否在保修期内
     */
    public function isUnderWarranty()
    {
        if (!$this->purchase_date || !$this->warranty_period) {
            return false;
        }

        $warrantyEndDate = $this->purchase_date->addMonths($this->warranty_period);
        return now() <= $warrantyEndDate;
    }

    /**
     * 获取保修到期日期
     */
    public function getWarrantyEndDateAttribute()
    {
        if (!$this->purchase_date || !$this->warranty_period) {
            return null;
        }

        return $this->purchase_date->addMonths($this->warranty_period);
    }

    /**
     * 检查是否可以借用
     */
    public function canBorrow($quantity = 1)
    {
        return $this->status === self::STATUS_NORMAL && 
               $this->available_quantity >= $quantity;
    }

    /**
     * 作用域：正常状态
     */
    public function scopeNormal($query)
    {
        return $query->where('status', self::STATUS_NORMAL);
    }

    /**
     * 作用域：可借用
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_NORMAL);
    }







    /**
     * 生成设备二维码
     */
    public function generateQrCode(array $options = []): string
    {
        $qrContent = [
            'type' => 'equipment',
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'url' => url("/equipment/{$this->id}")
        ];

        // 根据选项添加额外信息
        if (isset($options['include_info'])) {
            $includeInfo = $options['include_info'];
            if (in_array('location', $includeInfo)) {
                $qrContent['location'] = $this->location;
            }
            if (in_array('contact', $includeInfo)) {
                $qrContent['contact'] = config('app.contact_phone');
            }
        }

        $qrCodeContent = json_encode($qrContent);

        // 这里应该调用二维码生成服务
        // 暂时返回模拟的二维码URL
        $qrCodeUrl = "/storage/qrcodes/equipment_{$this->id}_" . time() . ".png";

        // 更新设备的二维码字段
        $this->update(['qr_code' => $qrCodeUrl]);

        return $qrCodeUrl;
    }

    /**
     * 记录操作日志
     */
    public function logOperation(string $operationType, string $description, array $oldData = null, array $newData = null): void
    {
        if (class_exists('App\Models\EquipmentOperationLog')) {
            $this->operationLogs()->create([
                'user_id' => auth()->id(),
                'operation_type' => $operationType,
                'operation_description' => $description,
                'old_data' => $oldData,
                'new_data' => $newData,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * 搜索范围
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    /**
     * 按学校筛选
     */
    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 按分类筛选
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * 按状态筛选
     */
    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 按状况筛选
     */
    public function scopeByCondition($query, int $condition)
    {
        return $query->where('condition_status', $condition);
    }
}

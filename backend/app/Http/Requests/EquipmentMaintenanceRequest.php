<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentMaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // 权限检查在中间件中处理
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'equipment_id' => 'required|integer|exists:equipments,id',
            'reporter_id' => 'required|integer|exists:users,id',
            'reporter_name' => 'required|string|max:100',
            'fault_description' => 'required|string|max:2000',
            'fault_type' => 'required|integer|in:1,2,3,4', // 1:硬件故障 2:软件故障 3:使用损坏 4:自然老化
            'priority' => 'required|integer|in:1,2,3,4', // 1:低 2:中 3:高 4:紧急
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'string|url',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'equipment_id.required' => '设备ID不能为空',
            'equipment_id.exists' => '设备不存在',
            'reporter_id.required' => '报修人ID不能为空',
            'reporter_id.exists' => '报修人不存在',
            'reporter_name.required' => '报修人姓名不能为空',
            'reporter_name.max' => '报修人姓名不能超过100个字符',
            'fault_description.required' => '故障描述不能为空',
            'fault_description.max' => '故障描述不能超过2000个字符',
            'fault_type.required' => '故障类型不能为空',
            'fault_type.in' => '故障类型值无效',
            'priority.required' => '优先级不能为空',
            'priority.in' => '优先级值无效',
            'photos.array' => '照片必须是数组格式',
            'photos.max' => '照片数量不能超过10张',
            'photos.*.url' => '照片URL格式不正确',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'equipment_id' => '设备',
            'reporter_id' => '报修人',
            'reporter_name' => '报修人姓名',
            'fault_description' => '故障描述',
            'fault_type' => '故障类型',
            'priority' => '优先级',
            'photos' => '故障照片',
        ];
    }
}

class UpdateEquipmentMaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|integer|in:1,2,3,4', // 1:待处理 2:处理中 3:已完成 4:已取消
            'repair_start_date' => 'sometimes|date|before_or_equal:today',
            'repair_end_date' => 'sometimes|date|after_or_equal:repair_start_date',
            'repair_cost' => 'sometimes|numeric|min:0|max:999999.99',
            'repair_description' => 'sometimes|string|max:2000',
            'parts_used' => 'sometimes|string|max:1000',
            'technician_id' => 'sometimes|integer|exists:users,id',
            'technician_name' => 'sometimes|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => '状态值无效',
            'repair_start_date.date' => '维修开始日期格式不正确',
            'repair_start_date.before_or_equal' => '维修开始日期不能晚于今天',
            'repair_end_date.date' => '维修结束日期格式不正确',
            'repair_end_date.after_or_equal' => '维修结束日期不能早于开始日期',
            'repair_cost.numeric' => '维修费用必须是数字',
            'repair_cost.min' => '维修费用不能小于0',
            'repair_cost.max' => '维修费用不能超过999999.99',
            'repair_description.max' => '维修描述不能超过2000个字符',
            'parts_used.max' => '使用配件不能超过1000个字符',
            'technician_id.exists' => '技师不存在',
            'technician_name.max' => '技师姓名不能超过100个字符',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => '状态',
            'repair_start_date' => '维修开始日期',
            'repair_end_date' => '维修结束日期',
            'repair_cost' => '维修费用',
            'repair_description' => '维修描述',
            'parts_used' => '使用配件',
            'technician_id' => '技师',
            'technician_name' => '技师姓名',
        ];
    }
}

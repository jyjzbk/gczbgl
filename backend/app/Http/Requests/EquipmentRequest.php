<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends FormRequest
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
        $equipmentId = $this->route('equipment') ? $this->route('equipment')->id : null;

        return [
            'school_id' => 'required|integer|exists:schools,id',
            'laboratory_id' => 'nullable|integer|exists:laboratories,id',
            'category_id' => 'required|integer|exists:equipment_categories,id',
            'name' => 'required|string|max:200',
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipments', 'code')->ignore($equipmentId)
            ],
            'model' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:200',
            'supplier_phone' => 'nullable|string|max:20',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0|max:999999999.99',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:20',
            'warranty_period' => 'nullable|integer|min:0|max:120', // 最大10年保修
            'service_life' => 'nullable|integer|min:0|max:50', // 最大50年使用寿命
            'funding_source' => 'nullable|string|max:100',
            'storage_location' => 'nullable|string|max:200',
            'manager_id' => 'nullable|integer|exists:users,id',
            'status' => 'required|integer|in:1,2,3,4', // 1:正常 2:借出 3:维修 4:报废
            'remark' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'school_id.required' => '学校ID不能为空',
            'school_id.exists' => '学校不存在',
            'laboratory_id.exists' => '实验室不存在',
            'category_id.required' => '设备分类不能为空',
            'category_id.exists' => '设备分类不存在',
            'name.required' => '设备名称不能为空',
            'name.max' => '设备名称不能超过200个字符',
            'code.unique' => '设备编号已存在',
            'code.max' => '设备编号不能超过100个字符',
            'model.max' => '设备型号不能超过100个字符',
            'brand.max' => '设备品牌不能超过100个字符',
            'supplier.max' => '供应商名称不能超过200个字符',
            'supplier_phone.max' => '供应商电话不能超过20个字符',
            'purchase_date.date' => '采购日期格式不正确',
            'purchase_date.before_or_equal' => '采购日期不能晚于今天',
            'purchase_price.numeric' => '采购价格必须是数字',
            'purchase_price.min' => '采购价格不能小于0',
            'purchase_price.max' => '采购价格不能超过999999999.99',
            'quantity.required' => '数量不能为空',
            'quantity.integer' => '数量必须是整数',
            'quantity.min' => '数量不能小于1',
            'unit.required' => '单位不能为空',
            'unit.max' => '单位不能超过20个字符',
            'warranty_period.integer' => '保修期必须是整数',
            'warranty_period.min' => '保修期不能小于0',
            'warranty_period.max' => '保修期不能超过120个月',
            'service_life.integer' => '使用寿命必须是整数',
            'service_life.min' => '使用寿命不能小于0',
            'service_life.max' => '使用寿命不能超过50年',
            'funding_source.max' => '资金来源不能超过100个字符',
            'storage_location.max' => '存放位置不能超过200个字符',
            'manager_id.exists' => '管理员不存在',
            'status.required' => '设备状态不能为空',
            'status.in' => '设备状态值无效',
            'remark.max' => '备注不能超过2000个字符',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'school_id' => '学校',
            'category_id' => '设备分类',
            'name' => '设备名称',
            'code' => '设备编号',
            'model' => '设备型号',
            'brand' => '设备品牌',
            'serial_number' => '序列号',
            'purchase_date' => '采购日期',
            'purchase_price' => '采购价格',
            'supplier' => '供应商',
            'warranty_period' => '保修期',
            'location' => '存放位置',
            'status' => '设备状态',
            'condition_status' => '设备状况',
            'description' => '设备描述',
            'specifications' => '技术规格',
            'photos' => '设备照片',
            'responsible_person' => '责任人',
            'contact_phone' => '联系电话',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 自定义验证逻辑
            if ($this->status == 2 && !$this->hasActiveBorrow()) {
                $validator->errors()->add('status', '设备状态为借出时必须有有效的借用记录');
            }

            if ($this->status == 3 && !$this->hasActiveMaintenance()) {
                $validator->errors()->add('status', '设备状态为维修时必须有有效的维修记录');
            }
        });
    }

    /**
     * 检查是否有有效的借用记录
     */
    private function hasActiveBorrow(): bool
    {
        if (!$this->route('equipment')) {
            return true; // 新建设备时不检查
        }

        $equipment = $this->route('equipment');
        return $equipment->borrows()
            ->whereIn('status', [1, 2, 3]) // 申请中、已批准、已借出
            ->exists();
    }

    /**
     * 检查是否有有效的维修记录
     */
    private function hasActiveMaintenance(): bool
    {
        if (!$this->route('equipment')) {
            return true; // 新建设备时不检查
        }

        $equipment = $this->route('equipment');
        return $equipment->maintenances()
            ->whereIn('status', [1, 2]) // 待维修、维修中
            ->exists();
    }
}

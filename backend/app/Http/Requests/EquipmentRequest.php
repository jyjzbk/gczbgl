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
            'category_id' => 'required|integer|exists:equipment_categories,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipments', 'code')->ignore($equipmentId)
            ],
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'serial_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('equipments', 'serial_number')->ignore($equipmentId)
            ],
            'purchase_date' => 'required|date|before_or_equal:today',
            'purchase_price' => 'required|numeric|min:0|max:999999999.99',
            'supplier' => 'required|string|max:255',
            'warranty_period' => 'required|integer|min:0|max:120', // 最大10年保修
            'location' => 'required|string|max:255',
            'status' => 'required|integer|in:1,2,3,4', // 1:正常 2:借出 3:维修 4:报废
            'condition' => 'required|integer|in:1,2,3,4', // 1:优 2:良 3:中 4:差
            'description' => 'nullable|string|max:2000',
            'specifications' => 'nullable|string|max:2000',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'string|url',
            'responsible_person' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
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
            'category_id.required' => '设备分类不能为空',
            'category_id.exists' => '设备分类不存在',
            'name.required' => '设备名称不能为空',
            'name.max' => '设备名称不能超过255个字符',
            'code.required' => '设备编号不能为空',
            'code.unique' => '设备编号已存在',
            'code.max' => '设备编号不能超过100个字符',
            'model.required' => '设备型号不能为空',
            'model.max' => '设备型号不能超过255个字符',
            'brand.required' => '设备品牌不能为空',
            'brand.max' => '设备品牌不能超过255个字符',
            'serial_number.required' => '序列号不能为空',
            'serial_number.unique' => '序列号已存在',
            'serial_number.max' => '序列号不能超过255个字符',
            'purchase_date.required' => '采购日期不能为空',
            'purchase_date.date' => '采购日期格式不正确',
            'purchase_date.before_or_equal' => '采购日期不能晚于今天',
            'purchase_price.required' => '采购价格不能为空',
            'purchase_price.numeric' => '采购价格必须是数字',
            'purchase_price.min' => '采购价格不能小于0',
            'purchase_price.max' => '采购价格不能超过999999999.99',
            'supplier.required' => '供应商不能为空',
            'supplier.max' => '供应商名称不能超过255个字符',
            'warranty_period.required' => '保修期不能为空',
            'warranty_period.integer' => '保修期必须是整数',
            'warranty_period.min' => '保修期不能小于0',
            'warranty_period.max' => '保修期不能超过120个月',
            'location.required' => '存放位置不能为空',
            'location.max' => '存放位置不能超过255个字符',
            'status.required' => '设备状态不能为空',
            'status.in' => '设备状态值无效',
            'condition.required' => '设备状况不能为空',
            'condition.in' => '设备状况值无效',
            'description.max' => '设备描述不能超过2000个字符',
            'specifications.max' => '技术规格不能超过2000个字符',
            'photos.array' => '照片必须是数组格式',
            'photos.max' => '照片数量不能超过10张',
            'photos.*.url' => '照片URL格式不正确',
            'responsible_person.max' => '责任人姓名不能超过100个字符',
            'contact_phone.max' => '联系电话不能超过20个字符',
            'contact_phone.regex' => '联系电话格式不正确',
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
            'condition' => '设备状况',
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

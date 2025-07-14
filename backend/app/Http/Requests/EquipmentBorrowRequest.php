<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Equipment;
use App\Models\EquipmentBorrow;

class EquipmentBorrowRequest extends FormRequest
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
            'borrower_id' => 'required|integer|exists:users,id',
            'borrower_name' => 'required|string|max:100',
            'borrower_phone' => 'required|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'borrow_date' => 'required|date|after_or_equal:today',
            'expected_return_date' => 'required|date|after:borrow_date',
            'purpose' => 'required|string|max:1000',
            'remark' => 'nullable|string|max:500',
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
            'borrower_id.required' => '借用人ID不能为空',
            'borrower_id.exists' => '借用人不存在',
            'borrower_name.required' => '借用人姓名不能为空',
            'borrower_name.max' => '借用人姓名不能超过100个字符',
            'borrower_phone.required' => '借用人电话不能为空',
            'borrower_phone.max' => '借用人电话不能超过20个字符',
            'borrower_phone.regex' => '借用人电话格式不正确',
            'borrow_date.required' => '借用日期不能为空',
            'borrow_date.date' => '借用日期格式不正确',
            'borrow_date.after_or_equal' => '借用日期不能早于今天',
            'expected_return_date.required' => '预期归还日期不能为空',
            'expected_return_date.date' => '预期归还日期格式不正确',
            'expected_return_date.after' => '预期归还日期必须晚于借用日期',
            'purpose.required' => '借用目的不能为空',
            'purpose.max' => '借用目的不能超过1000个字符',
            'remark.max' => '备注不能超过500个字符',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'equipment_id' => '设备',
            'borrower_id' => '借用人',
            'borrower_name' => '借用人姓名',
            'borrower_phone' => '借用人电话',
            'borrow_date' => '借用日期',
            'expected_return_date' => '预期归还日期',
            'purpose' => '借用目的',
            'remark' => '备注',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 检查设备是否可借用
            if ($this->equipment_id) {
                $equipment = Equipment::find($this->equipment_id);
                if ($equipment && !$equipment->canBorrow()) {
                    $validator->errors()->add('equipment_id', '设备当前状态不可借用');
                }

                // 检查时间冲突
                if ($this->borrow_date && $this->expected_return_date) {
                    $availability = $equipment->isAvailableForPeriod(
                        $this->borrow_date,
                        $this->expected_return_date
                    );
                    
                    if (!$availability['available']) {
                        $validator->errors()->add('borrow_date', implode(', ', $availability['reasons']));
                    }
                }
            }

            // 检查借用期限是否合理（不超过30天）
            if ($this->borrow_date && $this->expected_return_date) {
                $borrowDate = \Carbon\Carbon::parse($this->borrow_date);
                $returnDate = \Carbon\Carbon::parse($this->expected_return_date);
                $daysDiff = $borrowDate->diffInDays($returnDate);
                
                if ($daysDiff > 30) {
                    $validator->errors()->add('expected_return_date', '借用期限不能超过30天');
                }
            }
        });
    }
}

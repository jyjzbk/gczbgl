<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Equipment;
use App\Models\EquipmentBorrow;
use App\Models\EquipmentCategory;
use App\Models\School;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class EquipmentBorrowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $school;
    protected $equipment;
    protected $borrow;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建测试数据
        $this->school = School::factory()->create();
        $category = EquipmentCategory::factory()->create();
        
        // 创建角色
        $adminRole = Role::factory()->create(['code' => 'admin', 'name' => '管理员']);
        $teacherRole = Role::factory()->create(['code' => 'teacher', 'name' => '教师']);

        // 创建用户
        $this->admin = User::factory()->create(['school_id' => $this->school->id]);
        $this->admin->roles()->attach($adminRole);

        $this->user = User::factory()->create(['school_id' => $this->school->id]);
        $this->user->roles()->attach($teacherRole);

        // 创建设备
        $this->equipment = Equipment::factory()->create([
            'school_id' => $this->school->id,
            'category_id' => $category->id,
            'status' => Equipment::STATUS_NORMAL,
        ]);

        // 创建借用记录
        $this->borrow = EquipmentBorrow::factory()->create([
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'status' => EquipmentBorrow::STATUS_PENDING,
        ]);
    }

    /**
     * 测试获取借用记录列表
     */
    public function test_can_get_borrow_list()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/equipment-borrows');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'code',
                    'message',
                    'data' => [
                        'items' => [
                            '*' => [
                                'id',
                                'equipment_id',
                                'borrower_id',
                                'borrow_date',
                                'expected_return_date',
                                'status',
                                'equipment',
                                'borrower'
                            ]
                        ],
                        'pagination'
                    ]
                ]);
    }

    /**
     * 测试创建借用申请
     */
    public function test_can_create_borrow_request()
    {
        $token = JWTAuth::fromUser($this->user);

        $borrowData = [
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'borrower_name' => $this->user->real_name,
            'borrower_phone' => '13800138000',
            'borrow_date' => Carbon::tomorrow()->format('Y-m-d'),
            'expected_return_date' => Carbon::tomorrow()->addDays(7)->format('Y-m-d'),
            'purpose' => '教学实验使用',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipment-borrows', $borrowData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'code',
                    'message',
                    'data' => [
                        'id',
                        'equipment_id',
                        'borrower_id',
                        'borrow_date',
                        'expected_return_date',
                        'purpose',
                        'status'
                    ]
                ]);

        $this->assertDatabaseHas('equipment_borrows', [
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'purpose' => '教学实验使用'
        ]);
    }

    /**
     * 测试管理员审批借用申请
     */
    public function test_admin_can_review_borrow_request()
    {
        $token = JWTAuth::fromUser($this->admin);

        $reviewData = [
            'status' => EquipmentBorrow::STATUS_APPROVED,
            'remark' => '审批通过'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/equipment-borrows/{$this->borrow->id}/review", $reviewData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment_borrows', [
            'id' => $this->borrow->id,
            'status' => EquipmentBorrow::STATUS_APPROVED,
            'approver_id' => $this->admin->id
        ]);
    }

    /**
     * 测试普通用户无法审批借用申请
     */
    public function test_user_cannot_review_borrow_request()
    {
        $token = JWTAuth::fromUser($this->user);

        $reviewData = [
            'status' => EquipmentBorrow::STATUS_APPROVED,
            'remark' => '审批通过'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/equipment-borrows/{$this->borrow->id}/review", $reviewData);

        $response->assertStatus(403);
    }

    /**
     * 测试归还设备
     */
    public function test_can_return_equipment()
    {
        // 先将借用状态设为已借出
        $this->borrow->update([
            'status' => EquipmentBorrow::STATUS_BORROWED,
            'approver_id' => $this->admin->id,
            'approved_at' => now()
        ]);

        $token = JWTAuth::fromUser($this->admin);

        $returnData = [
            'actual_return_date' => Carbon::today()->format('Y-m-d'),
            'remark' => '设备完好归还'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/equipment-borrows/{$this->borrow->id}/return", $returnData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment_borrows', [
            'id' => $this->borrow->id,
            'status' => EquipmentBorrow::STATUS_RETURNED,
            'actual_return_date' => Carbon::today()->format('Y-m-d')
        ]);
    }

    /**
     * 测试检查设备可用性
     */
    public function test_can_check_equipment_availability()
    {
        $token = JWTAuth::fromUser($this->user);

        $startDate = Carbon::tomorrow()->format('Y-m-d');
        $endDate = Carbon::tomorrow()->addDays(7)->format('Y-m-d');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/equipments/{$this->equipment->id}/availability?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'code',
                    'message',
                    'data' => [
                        'available',
                        'reasons'
                    ]
                ]);
    }

    /**
     * 测试借用数据验证
     */
    public function test_borrow_validation()
    {
        $token = JWTAuth::fromUser($this->user);

        // 测试必填字段验证
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipment-borrows', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'equipment_id',
                    'borrower_id',
                    'borrower_name',
                    'borrower_phone',
                    'borrow_date',
                    'expected_return_date',
                    'purpose'
                ]);
    }

    /**
     * 测试借用日期验证
     */
    public function test_borrow_date_validation()
    {
        $token = JWTAuth::fromUser($this->user);

        $borrowData = [
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'borrower_name' => $this->user->real_name,
            'borrower_phone' => '13800138000',
            'borrow_date' => Carbon::yesterday()->format('Y-m-d'), // 过去的日期
            'expected_return_date' => Carbon::tomorrow()->format('Y-m-d'),
            'purpose' => '教学实验使用',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipment-borrows', $borrowData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['borrow_date']);
    }

    /**
     * 测试归还日期验证
     */
    public function test_return_date_validation()
    {
        $token = JWTAuth::fromUser($this->user);

        $borrowData = [
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'borrower_name' => $this->user->real_name,
            'borrower_phone' => '13800138000',
            'borrow_date' => Carbon::tomorrow()->format('Y-m-d'),
            'expected_return_date' => Carbon::today()->format('Y-m-d'), // 早于借用日期
            'purpose' => '教学实验使用',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipment-borrows', $borrowData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['expected_return_date']);
    }

    /**
     * 测试借用不可用设备
     */
    public function test_cannot_borrow_unavailable_equipment()
    {
        // 将设备状态设为维修中
        $this->equipment->update(['status' => Equipment::STATUS_MAINTENANCE]);

        $token = JWTAuth::fromUser($this->user);

        $borrowData = [
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'borrower_name' => $this->user->real_name,
            'borrower_phone' => '13800138000',
            'borrow_date' => Carbon::tomorrow()->format('Y-m-d'),
            'expected_return_date' => Carbon::tomorrow()->addDays(7)->format('Y-m-d'),
            'purpose' => '教学实验使用',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipment-borrows', $borrowData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['equipment_id']);
    }

    /**
     * 测试批量操作
     */
    public function test_admin_can_batch_approve()
    {
        // 创建多个借用申请
        $borrows = EquipmentBorrow::factory()->count(3)->create([
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $this->user->id,
            'status' => EquipmentBorrow::STATUS_PENDING,
        ]);

        $token = JWTAuth::fromUser($this->admin);

        $batchData = [
            'ids' => $borrows->pluck('id')->toArray(),
            'action' => 'approve',
            'remark' => '批量审批通过'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipment-borrows/batch-action', $batchData);

        $response->assertStatus(200);

        foreach ($borrows as $borrow) {
            $this->assertDatabaseHas('equipment_borrows', [
                'id' => $borrow->id,
                'status' => EquipmentBorrow::STATUS_APPROVED,
                'approver_id' => $this->admin->id
            ]);
        }
    }
}

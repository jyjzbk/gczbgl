<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\School;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class EquipmentManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $school;
    protected $category;
    protected $equipment;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建测试数据
        $this->school = School::factory()->create();
        $this->category = EquipmentCategory::factory()->create();
        
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
            'category_id' => $this->category->id,
        ]);
    }

    /**
     * 测试获取设备列表
     */
    public function test_can_get_equipment_list()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/equipments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'code',
                    'message',
                    'data' => [
                        'items' => [
                            '*' => [
                                'id',
                                'name',
                                'code',
                                'model',
                                'brand',
                                'status',
                                'category',
                                'school'
                            ]
                        ],
                        'pagination'
                    ]
                ]);
    }

    /**
     * 测试创建设备
     */
    public function test_admin_can_create_equipment()
    {
        $token = JWTAuth::fromUser($this->admin);

        $equipmentData = [
            'school_id' => $this->school->id,
            'category_id' => $this->category->id,
            'name' => '测试设备',
            'code' => 'TEST001',
            'model' => '测试型号',
            'brand' => '测试品牌',
            'serial_number' => 'SN123456',
            'purchase_date' => '2024-01-01',
            'purchase_price' => 1000.00,
            'supplier' => '测试供应商',
            'warranty_period' => 12,
            'location' => '测试位置',
            'status' => 1,
            'condition' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipments', $equipmentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'code',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'code',
                        'model',
                        'brand'
                    ]
                ]);

        $this->assertDatabaseHas('equipments', [
            'name' => '测试设备',
            'code' => 'TEST001'
        ]);
    }

    /**
     * 测试普通用户无法创建设备
     */
    public function test_teacher_cannot_create_equipment()
    {
        $token = JWTAuth::fromUser($this->user);

        $equipmentData = [
            'school_id' => $this->school->id,
            'category_id' => $this->category->id,
            'name' => '测试设备',
            'code' => 'TEST002',
            'model' => '测试型号',
            'brand' => '测试品牌',
            'serial_number' => 'SN123457',
            'purchase_date' => '2024-01-01',
            'purchase_price' => 1000.00,
            'supplier' => '测试供应商',
            'warranty_period' => 12,
            'location' => '测试位置',
            'status' => 1,
            'condition' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipments', $equipmentData);

        $response->assertStatus(403);
    }

    /**
     * 测试获取设备详情
     */
    public function test_can_get_equipment_details()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/equipments/{$this->equipment->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'code',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'code',
                        'model',
                        'brand',
                        'status',
                        'category',
                        'school'
                    ]
                ]);
    }

    /**
     * 测试更新设备
     */
    public function test_admin_can_update_equipment()
    {
        $token = JWTAuth::fromUser($this->admin);

        $updateData = [
            'name' => '更新后的设备名称',
            'model' => '更新后的型号',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/equipments/{$this->equipment->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('equipments', [
            'id' => $this->equipment->id,
            'name' => '更新后的设备名称',
            'model' => '更新后的型号'
        ]);
    }

    /**
     * 测试删除设备
     */
    public function test_admin_can_delete_equipment()
    {
        $token = JWTAuth::fromUser($this->admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/equipments/{$this->equipment->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('equipments', [
            'id' => $this->equipment->id
        ]);
    }

    /**
     * 测试数据验证
     */
    public function test_equipment_validation()
    {
        $token = JWTAuth::fromUser($this->admin);

        // 测试必填字段验证
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipments', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
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
                    'condition'
                ]);
    }

    /**
     * 测试设备编号唯一性验证
     */
    public function test_equipment_code_uniqueness()
    {
        $token = JWTAuth::fromUser($this->admin);

        $equipmentData = [
            'school_id' => $this->school->id,
            'category_id' => $this->category->id,
            'name' => '测试设备',
            'code' => $this->equipment->code, // 使用已存在的编号
            'model' => '测试型号',
            'brand' => '测试品牌',
            'serial_number' => 'SN123458',
            'purchase_date' => '2024-01-01',
            'purchase_price' => 1000.00,
            'supplier' => '测试供应商',
            'warranty_period' => 12,
            'location' => '测试位置',
            'status' => 1,
            'condition' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/equipments', $equipmentData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['code']);
    }

    /**
     * 测试未认证访问
     */
    public function test_unauthenticated_access_denied()
    {
        $response = $this->getJson('/api/equipments');
        $response->assertStatus(401);
    }

    /**
     * 测试跨学校数据访问限制
     */
    public function test_cross_school_data_access_restriction()
    {
        // 创建另一个学校的用户
        $otherSchool = School::factory()->create();
        $otherUser = User::factory()->create(['school_id' => $otherSchool->id]);
        $teacherRole = Role::where('code', 'teacher')->first();
        $otherUser->roles()->attach($teacherRole);

        $token = JWTAuth::fromUser($otherUser);

        // 尝试访问其他学校的设备
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/equipments/{$this->equipment->id}");

        // 应该返回404或403，因为设备不属于该用户的学校
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Laboratory;
use App\Models\ExperimentCatalog;
use App\Models\ExperimentReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SmartReservationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $school;
    protected $laboratory;
    protected $catalog;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建测试数据
        $this->school = School::factory()->create();
        $this->user = User::factory()->create([
            'school_id' => $this->school->id,
            'role' => 'teacher'
        ]);
        $this->laboratory = Laboratory::factory()->create([
            'school_id' => $this->school->id,
            'capacity' => 40
        ]);
        $this->catalog = ExperimentCatalog::factory()->create([
            'school_id' => $this->school->id,
            'duration' => 90
        ]);
    }

    /** @test */
    public function it_can_get_laboratory_schedule()
    {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/smart-reservations/laboratories/{$this->laboratory->id}/schedule", [
            'date_start' => now()->format('Y-m-d'),
            'date_end' => now()->addDays(7)->format('Y-m-d'),
            'view_type' => 'week'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'laboratory',
                        'schedule',
                        'date_range'
                    ]
                ]);
    }

    /** @test */
    public function it_can_create_smart_reservation()
    {
        $this->actingAs($this->user);

        $reservationData = [
            'catalog_id' => $this->catalog->id,
            'laboratory_id' => $this->laboratory->id,
            'reservation_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'class_name' => '高一(1)班',
            'student_count' => 35,
            'priority' => 'normal',
            'auto_borrow_equipment' => true,
            'preparation_notes' => '测试实验预约'
        ];

        $response = $this->postJson('/api/smart-reservations/create', $reservationData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'reservation',
                        'conflicts',
                        'has_conflicts'
                    ]
                ]);

        $this->assertDatabaseHas('experiment_reservations', [
            'catalog_id' => $this->catalog->id,
            'laboratory_id' => $this->laboratory->id,
            'teacher_id' => $this->user->id,
            'class_name' => '高一(1)班'
        ]);
    }

    /** @test */
    public function it_can_detect_conflicts()
    {
        $this->actingAs($this->user);

        // 先创建一个预约
        ExperimentReservation::factory()->create([
            'laboratory_id' => $this->laboratory->id,
            'reservation_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'status' => ExperimentReservation::STATUS_APPROVED
        ]);

        // 检测冲突的预约
        $conflictData = [
            'laboratory_id' => $this->laboratory->id,
            'reservation_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:30',
            'end_time' => '11:00',
            'student_count' => 30
        ];

        $response = $this->postJson('/api/smart-reservations/check-conflicts', $conflictData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'has_conflicts',
                        'conflicts'
                    ]
                ]);

        $this->assertTrue($response->json('data.has_conflicts'));
    }

    /** @test */
    public function it_validates_reservation_data()
    {
        $this->actingAs($this->user);

        $invalidData = [
            'catalog_id' => 999, // 不存在的实验
            'laboratory_id' => $this->laboratory->id,
            'reservation_date' => now()->subDays(1)->format('Y-m-d'), // 过去的日期
            'start_time' => '09:00',
            'end_time' => '08:00', // 结束时间早于开始时间
            'class_name' => '',
            'student_count' => 0
        ];

        $response = $this->postJson('/api/smart-reservations/create', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'catalog_id',
                    'reservation_date',
                    'end_time',
                    'class_name',
                    'student_count'
                ]);
    }

    /** @test */
    public function it_checks_laboratory_capacity()
    {
        $this->actingAs($this->user);

        $reservationData = [
            'catalog_id' => $this->catalog->id,
            'laboratory_id' => $this->laboratory->id,
            'reservation_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'class_name' => '高一(1)班',
            'student_count' => 50, // 超过实验室容量
            'priority' => 'normal'
        ];

        $response = $this->postJson('/api/smart-reservations/create', $reservationData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => '学生人数超过实验室容量限制'
                ]);
    }

    /** @test */
    public function it_can_get_personal_stats()
    {
        $this->actingAs($this->user);

        // 创建一些测试预约
        ExperimentReservation::factory()->count(5)->create([
            'teacher_id' => $this->user->id,
            'status' => ExperimentReservation::STATUS_COMPLETED
        ]);

        ExperimentReservation::factory()->count(3)->create([
            'teacher_id' => $this->user->id,
            'status' => ExperimentReservation::STATUS_PENDING
        ]);

        $response = $this->getJson('/api/personal/experiment-stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_reservations',
                        'completed_experiments',
                        'completion_rate',
                        'total_works'
                    ]
                ]);

        $data = $response->json('data');
        $this->assertEquals(8, $data['total_reservations']);
        $this->assertEquals(5, $data['completed_experiments']);
        $this->assertEquals(62.5, $data['completion_rate']);
    }
}

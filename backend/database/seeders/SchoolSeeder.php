<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\AdministrativeRegion;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取学区
        $district1 = AdministrativeRegion::where('code', '41010201')->first();
        $district2 = AdministrativeRegion::where('code', '41010202')->first();
        $district3 = AdministrativeRegion::where('code', '41010301')->first();

        $schools = [
            // 中原区第一学区的学校
            [
                'code' => 'ZY001',
                'name' => '郑州市中原区实验小学',
                'type' => School::TYPE_PRIMARY,
                'level' => School::LEVEL_COUNTY,
                'region_id' => $district1->id,
                'address' => '河南省郑州市中原区建设路123号',
                'contact_person' => '张校长',
                'contact_phone' => '0371-12345678',
                'student_count' => 1200,
                'class_count' => 24,
                'teacher_count' => 80
            ],
            [
                'code' => 'ZY002',
                'name' => '郑州市第一中学',
                'type' => School::TYPE_SENIOR,
                'level' => School::LEVEL_CITY,
                'region_id' => $district1->id,
                'address' => '河南省郑州市中原区中原路456号',
                'contact_person' => '李校长',
                'contact_phone' => '0371-23456789',
                'student_count' => 2400,
                'class_count' => 48,
                'teacher_count' => 180
            ],

            // 中原区第二学区的学校
            [
                'code' => 'ZY003',
                'name' => '郑州市中原区第二小学',
                'type' => School::TYPE_PRIMARY,
                'level' => School::LEVEL_COUNTY,
                'region_id' => $district2->id,
                'address' => '河南省郑州市中原区桐柏路789号',
                'contact_person' => '王校长',
                'contact_phone' => '0371-34567890',
                'student_count' => 800,
                'class_count' => 18,
                'teacher_count' => 60
            ],
            [
                'code' => 'ZY004',
                'name' => '郑州市中原区实验中学',
                'type' => School::TYPE_JUNIOR,
                'level' => School::LEVEL_COUNTY,
                'region_id' => $district2->id,
                'address' => '河南省郑州市中原区秦岭路321号',
                'contact_person' => '赵校长',
                'contact_phone' => '0371-45678901',
                'student_count' => 1500,
                'class_count' => 30,
                'teacher_count' => 120
            ],

            // 二七区第一学区的学校
            [
                'code' => 'EQ001',
                'name' => '郑州市二七区实验小学',
                'type' => School::TYPE_PRIMARY,
                'level' => School::LEVEL_COUNTY,
                'region_id' => $district3->id,
                'address' => '河南省郑州市二七区大学路654号',
                'contact_person' => '陈校长',
                'contact_phone' => '0371-56789012',
                'student_count' => 1000,
                'class_count' => 20,
                'teacher_count' => 70
            ],
            [
                'code' => 'EQ002',
                'name' => '郑州市二七区九年制学校',
                'type' => School::TYPE_NINE_YEAR,
                'level' => School::LEVEL_COUNTY,
                'region_id' => $district3->id,
                'address' => '河南省郑州市二七区航海路987号',
                'contact_person' => '刘校长',
                'contact_phone' => '0371-67890123',
                'student_count' => 1800,
                'class_count' => 36,
                'teacher_count' => 140
            ]
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}

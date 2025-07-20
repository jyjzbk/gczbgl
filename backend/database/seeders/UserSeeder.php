<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use App\Models\AdministrativeRegion;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建系统管理员
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'real_name' => '系统管理员',
            'status' => 1, // 1
            'role' => 'admin',
            'organization_type' => 'system',
            'organization_id' => null,
        ]);

        // 获取行政区域和学校
        $provinces = AdministrativeRegion::where('level', 1)->get();
        $cities = AdministrativeRegion::where('level', 2)->get();
        $counties = AdministrativeRegion::where('level', 3)->get();
        $schools = School::all();

        // 创建省级管理员
        foreach ($provinces->take(2) as $index => $province) {
            User::create([
                'username' => 'province_admin_' . ($index + 1),
                'email' => "province_admin_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $province->name . '管理员',
                'status' => 1,
                'role' => 'admin',
                'organization_type' => 'province',
                'organization_id' => $province->id,
            ]);

            User::create([
                'username' => 'province_researcher_' . ($index + 1),
                'email' => "province_researcher_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $province->name . '教研员',
                'status' => 1,
                'role' => 'researcher',
                'organization_type' => 'province',
                'organization_id' => $province->id,
            ]);
        }

        // 创建市级管理员
        foreach ($cities->take(3) as $index => $city) {
            User::create([
                'username' => 'city_admin_' . ($index + 1),
                'email' => "city_admin_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $city->name . '管理员',
                'status' => 1,
                'role' => 'admin',
                'organization_type' => 'city',
                'organization_id' => $city->id,
            ]);

            User::create([
                'username' => 'city_researcher_' . ($index + 1),
                'email' => "city_researcher_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $city->name . '教研员',
                'status' => 1,
                'role' => 'researcher',
                'organization_type' => 'city',
                'organization_id' => $city->id,
            ]);
        }

        // 创建区县管理员
        foreach ($counties->take(5) as $index => $county) {
            User::create([
                'username' => 'county_admin_' . ($index + 1),
                'email' => "county_admin_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $county->name . '管理员',
                'status' => 1,
                'role' => 'admin',
                'organization_type' => 'county',
                'organization_id' => $county->id,
            ]);

            User::create([
                'username' => 'county_researcher_' . ($index + 1),
                'email' => "county_researcher_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $county->name . '教研员',
                'status' => 1,
                'role' => 'researcher',
                'organization_type' => 'county',
                'organization_id' => $county->id,
            ]);
        }

        // 创建学校管理员和教师
        foreach ($schools->take(10) as $index => $school) {
            // 学校管理员
            User::create([
                'username' => 'school_admin_' . ($index + 1),
                'email' => "school_admin_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $school->name . '管理员',
                'status' => 1,
                'role' => 'admin',
                'organization_type' => 'school',
                'organization_id' => $school->id,
            ]);

            // 校长
            User::create([
                'username' => 'principal_' . ($index + 1),
                'email' => "principal_{$index}@example.com",
                'password' => Hash::make('password'),
                'real_name' => $school->name . '校长',
                'status' => 1,
                'role' => 'principal',
                'organization_type' => 'school',
                'organization_id' => $school->id,
            ]);

            // 实验教师
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'username' => "teacher_{$index}_{$i}",
                    'email' => "teacher_{$index}_{$i}@example.com",
                    'password' => Hash::make('password'),
                    'real_name' => $school->name . "实验教师{$i}",
                    'status' => 1,
                    'role' => 'teacher',
                    'organization_type' => 'school',
                    'organization_id' => $school->id,
                ]);
            }
        }

        echo "用户种子数据创建完成！\n";
        echo "总用户数: " . User::count() . "\n";
    }
}

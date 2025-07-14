<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // 省级角色
            [
                'name' => '省级管理员',
                'code' => 'province_admin',
                'level' => Role::LEVEL_PROVINCE,
                'description' => '省级系统管理员，拥有全省数据管理权限'
            ],
            [
                'name' => '省级教研员',
                'code' => 'province_researcher',
                'level' => Role::LEVEL_PROVINCE,
                'description' => '省级教研员，负责全省实验教学研究和指导'
            ],

            // 市级角色
            [
                'name' => '市级管理员',
                'code' => 'city_admin',
                'level' => Role::LEVEL_CITY,
                'description' => '市级系统管理员，管理本市实验教学数据'
            ],
            [
                'name' => '市级教研员',
                'code' => 'city_researcher',
                'level' => Role::LEVEL_CITY,
                'description' => '市级教研员，负责本市实验教学研究和指导'
            ],

            // 区县级角色
            [
                'name' => '区县管理员',
                'code' => 'county_admin',
                'level' => Role::LEVEL_COUNTY,
                'description' => '区县级系统管理员，管理本区县实验教学数据'
            ],
            [
                'name' => '区县教研员',
                'code' => 'county_researcher',
                'level' => Role::LEVEL_COUNTY,
                'description' => '区县级教研员，负责本区县实验教学研究和指导'
            ],

            // 学区级角色
            [
                'name' => '学区管理员',
                'code' => 'district_admin',
                'level' => Role::LEVEL_DISTRICT,
                'description' => '学区管理员，管理学区内学校实验教学数据'
            ],

            // 学校级角色
            [
                'name' => '校长',
                'code' => 'school_principal',
                'level' => Role::LEVEL_SCHOOL,
                'description' => '学校校长，拥有本校所有数据查看权限'
            ],
            [
                'name' => '教务主任',
                'code' => 'school_dean',
                'level' => Role::LEVEL_SCHOOL,
                'description' => '教务主任，负责学校实验教学管理'
            ],
            [
                'name' => '实验员',
                'code' => 'school_experimenter',
                'level' => Role::LEVEL_SCHOOL,
                'description' => '实验员，负责实验室管理和实验教学记录'
            ],
            [
                'name' => '任课教师',
                'code' => 'school_teacher',
                'level' => Role::LEVEL_SCHOOL,
                'description' => '任课教师，可查看和录入实验教学数据'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

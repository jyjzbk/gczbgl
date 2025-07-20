<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdministrativeRegionSeeder::class,
            SchoolSeeder::class,
            SubjectSeeder::class,
            ExperimentCatalogSeeder::class,
            EquipmentCategorySeeder::class,
            EquipmentSeeder::class,
            EquipmentBorrowSeeder::class,
            EquipmentMaintenanceSeeder::class,
            EquipmentRequirementSeeder::class, // 新增器材需求配置数据
        ]);

        // 创建测试用户
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'real_name' => '系统管理员',
            'status' => User::STATUS_ACTIVE
        ]);
    }
}


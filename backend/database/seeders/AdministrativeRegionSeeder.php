<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdministrativeRegion;

class AdministrativeRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建示例省份
        $province = AdministrativeRegion::create([
            'code' => '410000',
            'name' => '河南省',
            'level' => AdministrativeRegion::LEVEL_PROVINCE,
            'parent_id' => null,
            'sort_order' => 1
        ]);

        // 创建示例城市
        $city = AdministrativeRegion::create([
            'code' => '410100',
            'name' => '郑州市',
            'level' => AdministrativeRegion::LEVEL_CITY,
            'parent_id' => $province->id,
            'sort_order' => 1
        ]);

        // 创建示例区县
        $county1 = AdministrativeRegion::create([
            'code' => '410102',
            'name' => '中原区',
            'level' => AdministrativeRegion::LEVEL_COUNTY,
            'parent_id' => $city->id,
            'sort_order' => 1
        ]);

        $county2 = AdministrativeRegion::create([
            'code' => '410103',
            'name' => '二七区',
            'level' => AdministrativeRegion::LEVEL_COUNTY,
            'parent_id' => $city->id,
            'sort_order' => 2
        ]);

        // 创建示例学区
        $district1 = AdministrativeRegion::create([
            'code' => '41010201',
            'name' => '中原区第一学区',
            'level' => AdministrativeRegion::LEVEL_DISTRICT,
            'parent_id' => $county1->id,
            'sort_order' => 1
        ]);

        $district2 = AdministrativeRegion::create([
            'code' => '41010202',
            'name' => '中原区第二学区',
            'level' => AdministrativeRegion::LEVEL_DISTRICT,
            'parent_id' => $county1->id,
            'sort_order' => 2
        ]);

        $district3 = AdministrativeRegion::create([
            'code' => '41010301',
            'name' => '二七区第一学区',
            'level' => AdministrativeRegion::LEVEL_DISTRICT,
            'parent_id' => $county2->id,
            'sort_order' => 1
        ]);
    }
}

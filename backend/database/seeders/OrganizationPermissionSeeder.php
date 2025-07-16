<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdministrativeRegion;
use App\Models\School;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class OrganizationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建河北省组织架构
        $this->createHebeiOrganization();

        // 创建测试用户
        $this->createTestUsers();
    }

    private function createHebeiOrganization()
    {
        // 1. 创建河北省
        $hebei = AdministrativeRegion::firstOrCreate(
            ['code' => '130000'],
            [
                'name' => '河北省教育厅',
                'level' => 1, // 省级
                'parent_id' => null,
                'sort_order' => 1,
                'status' => 1
            ]
        );

        // 2. 创建省直属中小学
        $provinceSchools = $this->createProvinceSchools($hebei->id);

        // 3. 创建市级教育局
        $cityRegions = $this->createCityRegions($hebei->id);

        // 存储创建的组织ID供后续使用
        $this->organizationIds = [
            'province' => $hebei->id,
            'city' => $cityRegions['shijiazhuang']->id,
            'county' => $cityRegions['shijiazhuang_counties']['gaocheng']->id,
            'district' => $cityRegions['shijiazhuang_counties']['gaocheng_districts']['lianzhou']->id,
            'province_school' => $provinceSchools['shijiazhuang_elite']->id,
            'city_school' => $cityRegions['shijiazhuang_schools']['first']->id,
            'county_school' => $cityRegions['shijiazhuang_counties']['gaocheng_schools']['tongan']->id,
            'district_school' => $cityRegions['shijiazhuang_counties']['gaocheng_districts']['lianzhou_schools']['dongcheng']->id
        ];
    }

    /**
     * 创建省直属中小学
     */
    private function createProvinceSchools($provinceId)
    {
        $schools = [];

        // 石家庄精英中学
        $schools['shijiazhuang_elite'] = School::firstOrCreate(
            ['code' => 'HB001'],
            [
                'name' => '石家庄精英中学',
                'type' => 3, // 高中
                'level' => 1, // 省直
                'region_id' => $provinceId,
                'address' => '石家庄市长安区',
                'contact_person' => '张校长',
                'contact_phone' => '0311-12345678',
                'student_count' => 2000,
                'class_count' => 40,
                'teacher_count' => 150,
                'status' => 1
            ]
        );

        // 衡水中学
        $schools['hengshui'] = School::firstOrCreate(
            ['code' => 'HB002'],
            [
                'name' => '衡水中学',
                'type' => 3, // 高中
                'level' => 1, // 省直
                'region_id' => $provinceId,
                'address' => '衡水市桃城区',
                'contact_person' => '李校长',
                'contact_phone' => '0318-12345678',
                'student_count' => 3000,
                'class_count' => 60,
                'teacher_count' => 200,
                'status' => 1
            ]
        );

        // 保定七中
        $schools['baoding'] = School::firstOrCreate(
            ['code' => 'HB003'],
            [
                'name' => '保定七中',
                'type' => 3, // 高中
                'level' => 1, // 省直
                'region_id' => $provinceId,
                'address' => '保定市莲池区',
                'contact_person' => '王校长',
                'contact_phone' => '0312-12345678',
                'student_count' => 1800,
                'class_count' => 36,
                'teacher_count' => 120,
                'status' => 1
            ]
        );

        // 邢台一中
        $schools['xingtai'] = School::firstOrCreate(
            ['code' => 'HB004'],
            [
                'name' => '邢台一中',
                'type' => 3, // 高中
                'level' => 1, // 省直
                'region_id' => $provinceId,
                'address' => '邢台市桥东区',
                'contact_person' => '赵校长',
                'contact_phone' => '0319-12345678',
                'student_count' => 1600,
                'class_count' => 32,
                'teacher_count' => 110,
                'status' => 1
            ]
        );

        return $schools;
    }

    /**
     * 创建市级教育局及下属组织
     */
    private function createCityRegions($provinceId)
    {
        $cityRegions = [];

        // 1. 创建石家庄市教育局
        $shijiazhuang = AdministrativeRegion::firstOrCreate(
            ['code' => '130100'],
            [
                'name' => '石家庄市教育局',
                'level' => 2, // 市级
                'parent_id' => $provinceId,
                'sort_order' => 1,
                'status' => 1
            ]
        );
        $cityRegions['shijiazhuang'] = $shijiazhuang;

        // 创建石家庄市直中小学
        $cityRegions['shijiazhuang_schools'] = $this->createShijiazhuangCitySchools($shijiazhuang->id);

        // 创建石家庄市下属区县
        $cityRegions['shijiazhuang_counties'] = $this->createShijiazhuangCounties($shijiazhuang->id);

        // 2. 创建其他市教育局
        $otherCities = [
            ['code' => '130200', 'name' => '唐山市教育局', 'sort_order' => 2],
            ['code' => '130900', 'name' => '沧州市教育局', 'sort_order' => 3],
            ['code' => '131100', 'name' => '衡水市教育局', 'sort_order' => 4],
        ];

        foreach ($otherCities as $cityData) {
            $city = AdministrativeRegion::firstOrCreate(
                ['code' => $cityData['code']],
                [
                    'name' => $cityData['name'],
                    'level' => 2, // 市级
                    'parent_id' => $provinceId,
                    'sort_order' => $cityData['sort_order'],
                    'status' => 1
                ]
            );
            $cityRegions[strtolower(str_replace(['市教育局'], [''], $cityData['name']))] = $city;
        }

        return $cityRegions;
    }

    /**
     * 创建石家庄市直中小学
     */
    private function createShijiazhuangCitySchools($cityId)
    {
        $schools = [];

        $citySchoolsData = [
            ['code' => 'SJZ001', 'name' => '石家庄市第一中学', 'type' => 3, 'address' => '石家庄市裕华区'],
            ['code' => 'SJZ002', 'name' => '石家庄市第二中学', 'type' => 3, 'address' => '石家庄市新华区'],
            ['code' => 'SJZ003', 'name' => '石家庄外国语学校', 'type' => 3, 'address' => '石家庄市桥西区'],
            ['code' => 'SJZ004', 'name' => '石家庄实验中学', 'type' => 2, 'address' => '石家庄市长安区'],
        ];

        foreach ($citySchoolsData as $index => $schoolData) {
            $school = School::firstOrCreate(
                ['code' => $schoolData['code']],
                [
                    'name' => $schoolData['name'],
                    'type' => $schoolData['type'],
                    'level' => 2, // 市直
                    'region_id' => $cityId,
                    'address' => $schoolData['address'],
                    'contact_person' => '校长' . ($index + 1),
                    'contact_phone' => '0311-8765432' . $index,
                    'student_count' => 1500 + $index * 100,
                    'class_count' => 30 + $index * 2,
                    'teacher_count' => 100 + $index * 10,
                    'status' => 1
                ]
            );
            // 生成简化的键名
            $keyName = '';
            if (strpos($schoolData['name'], '第一中学') !== false) {
                $keyName = 'first';
            } elseif (strpos($schoolData['name'], '第二中学') !== false) {
                $keyName = 'second';
            } elseif (strpos($schoolData['name'], '外国语学校') !== false) {
                $keyName = 'foreign';
            } elseif (strpos($schoolData['name'], '实验中学') !== false) {
                $keyName = 'experimental';
            }
            $schools[$keyName] = $school;
        }

        return $schools;
    }

    /**
     * 创建石家庄市下属区县
     */
    private function createShijiazhuangCounties($cityId)
    {
        $counties = [];

        // 1. 创建藁城区
        $gaocheng = AdministrativeRegion::firstOrCreate(
            ['code' => '130182'],
            [
                'name' => '藁城区',
                'level' => 3, // 区县级
                'parent_id' => $cityId,
                'sort_order' => 1,
                'status' => 1
            ]
        );
        $counties['gaocheng'] = $gaocheng;

        // 创建藁城区下属学区
        $counties['gaocheng_districts'] = $this->createGaochengDistricts($gaocheng->id);

        // 创建藁城区直中小学
        $counties['gaocheng_schools'] = $this->createGaochengCountySchools($gaocheng->id);

        // 2. 创建其他区县
        $otherCounties = [
            ['code' => '130183', 'name' => '栾城区', 'sort_order' => 2],
            ['code' => '130102', 'name' => '长安区', 'sort_order' => 3],
            ['code' => '130107', 'name' => '井陉矿区', 'sort_order' => 4],
            ['code' => '130185', 'name' => '鹿泉区', 'sort_order' => 5],
        ];

        foreach ($otherCounties as $countyData) {
            $county = AdministrativeRegion::firstOrCreate(
                ['code' => $countyData['code']],
                [
                    'name' => $countyData['name'],
                    'level' => 3, // 区县级
                    'parent_id' => $cityId,
                    'sort_order' => $countyData['sort_order'],
                    'status' => 1
                ]
            );
            $counties[strtolower(str_replace('区', '', $countyData['name']))] = $county;
        }

        return $counties;
    }

    /**
     * 创建藁城区下属学区
     */
    private function createGaochengDistricts($countyId)
    {
        $districts = [];

        // 1. 创建廉州学区
        $lianzhou = AdministrativeRegion::firstOrCreate(
            ['code' => '13018201'],
            [
                'name' => '廉州学区',
                'level' => 4, // 学区级
                'parent_id' => $countyId,
                'sort_order' => 1,
                'status' => 1
            ]
        );
        $districts['lianzhou'] = $lianzhou;

        // 创建廉州学区下属学校
        $districts['lianzhou_schools'] = $this->createLianZhouDistrictSchools($lianzhou->id);

        // 2. 创建其他学区
        $otherDistricts = [
            ['code' => '13018202', 'name' => '南董学区', 'sort_order' => 2],
            ['code' => '13018203', 'name' => '南营学区', 'sort_order' => 3],
            ['code' => '13018204', 'name' => '兴安学区', 'sort_order' => 4],
        ];

        foreach ($otherDistricts as $districtData) {
            $district = AdministrativeRegion::firstOrCreate(
                ['code' => $districtData['code']],
                [
                    'name' => $districtData['name'],
                    'level' => 4, // 学区级
                    'parent_id' => $countyId,
                    'sort_order' => $districtData['sort_order'],
                    'status' => 1
                ]
            );
            $districts[strtolower(str_replace('学区', '', $districtData['name']))] = $district;
        }

        return $districts;
    }

    /**
     * 创建藁城区直中小学
     */
    private function createGaochengCountySchools($countyId)
    {
        $schools = [];

        $countySchoolsData = [
            ['code' => 'GC001', 'name' => '通安小学', 'type' => 1],
            ['code' => 'GC002', 'name' => '实验小学', 'type' => 1],
            ['code' => 'GC003', 'name' => '石家庄第八中学', 'type' => 2],
        ];

        foreach ($countySchoolsData as $index => $schoolData) {
            $school = School::firstOrCreate(
                ['code' => $schoolData['code']],
                [
                    'name' => $schoolData['name'],
                    'type' => $schoolData['type'],
                    'level' => 3, // 区县直
                    'region_id' => $countyId,
                    'address' => '藁城区' . $schoolData['name'] . '地址',
                    'contact_person' => $schoolData['name'] . '校长',
                    'contact_phone' => '0311-8888888' . $index,
                    'student_count' => 600 + $index * 100,
                    'class_count' => 18 + $index * 3,
                    'teacher_count' => 40 + $index * 10,
                    'status' => 1
                ]
            );
            // 生成简化的键名
            $keyName = '';
            if (strpos($schoolData['name'], '通安小学') !== false) {
                $keyName = 'tongan';
            } elseif (strpos($schoolData['name'], '实验小学') !== false) {
                $keyName = 'experimental';
            } elseif (strpos($schoolData['name'], '石家庄第八中学') !== false) {
                $keyName = 'eighth';
            }
            $schools[$keyName] = $school;
        }

        return $schools;
    }

    /**
     * 创建廉州学区下属学校
     */
    private function createLianZhouDistrictSchools($districtId)
    {
        $schools = [];

        $districtSchoolsData = [
            ['code' => 'LZ001', 'name' => '廉州东城小学', 'type' => 1],
            ['code' => 'LZ002', 'name' => '廉州北街小学', 'type' => 1],
            ['code' => 'LZ003', 'name' => '廉州第四中学', 'type' => 2],
            ['code' => 'LZ004', 'name' => '廉州第一中学', 'type' => 2],
        ];

        foreach ($districtSchoolsData as $index => $schoolData) {
            $school = School::firstOrCreate(
                ['code' => $schoolData['code']],
                [
                    'name' => $schoolData['name'],
                    'type' => $schoolData['type'],
                    'level' => 4, // 学区
                    'region_id' => $districtId,
                    'address' => '藁城区廉州镇' . $schoolData['name'] . '地址',
                    'contact_person' => $schoolData['name'] . '校长',
                    'contact_phone' => '0311-9999999' . $index,
                    'student_count' => 200 + $index * 50,
                    'class_count' => 8 + $index * 2,
                    'teacher_count' => 15 + $index * 5,
                    'status' => 1
                ]
            );
            // 生成简化的键名
            $keyName = '';
            if (strpos($schoolData['name'], '东城小学') !== false) {
                $keyName = 'dongcheng';
            } elseif (strpos($schoolData['name'], '北街小学') !== false) {
                $keyName = 'beijie';
            } elseif (strpos($schoolData['name'], '第四中学') !== false) {
                $keyName = 'fourth';
            } elseif (strpos($schoolData['name'], '第一中学') !== false) {
                $keyName = 'first';
            }
            $schools[$keyName] = $school;
        }

        return $schools;
    }

    private function createTestUsers()
    {
        // 创建角色（如果不存在）
        $roles = [
            ['name' => '省级管理员', 'code' => 'province_admin', 'level' => 1],
            ['name' => '市级管理员', 'code' => 'city_admin', 'level' => 2],
            ['name' => '区县管理员', 'code' => 'county_admin', 'level' => 3],
            ['name' => '学区管理员', 'code' => 'district_admin', 'level' => 4],
            ['name' => '学校管理员', 'code' => 'school_admin', 'level' => 5],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['code' => $roleData['code']], $roleData);
        }

        // 1. 创建省级管理员
        $provinceAdmin = User::firstOrCreate(
            ['username' => 'province_admin_test'],
            [
                'email' => 'province_test@test.com',
                'password' => Hash::make('password'),
                'real_name' => '河北省教育厅管理员',
                'organization_id' => $this->organizationIds['province'],
                'organization_type' => 'region',
                'organization_level' => 1,
                'status' => 1
            ]
        );

        // 2. 创建市级管理员
        $cityAdmin = User::firstOrCreate(
            ['username' => 'city_admin_test'],
            [
                'email' => 'city_test@test.com',
                'password' => Hash::make('password'),
                'real_name' => '石家庄市教育局管理员',
                'organization_id' => $this->organizationIds['city'],
                'organization_type' => 'region',
                'organization_level' => 2,
                'status' => 1
            ]
        );

        // 3. 创建区县管理员
        $countyAdmin = User::firstOrCreate(
            ['username' => 'county_admin_test'],
            [
                'email' => 'county_test@test.com',
                'password' => Hash::make('password'),
                'real_name' => '藁城区教育局管理员',
                'organization_id' => $this->organizationIds['county'],
                'organization_type' => 'region',
                'organization_level' => 3,
                'status' => 1
            ]
        );

        // 4. 创建学区管理员
        $districtAdmin = User::firstOrCreate(
            ['username' => 'district_admin_test'],
            [
                'email' => 'district_test@test.com',
                'password' => Hash::make('password'),
                'real_name' => '廉州学区管理员',
                'organization_id' => $this->organizationIds['district'],
                'organization_type' => 'region',
                'organization_level' => 4,
                'status' => 1
            ]
        );

        // 5. 创建学校管理员
        $schoolAdmin = User::firstOrCreate(
            ['username' => 'school_admin_test'],
            [
                'email' => 'school_test@test.com',
                'password' => Hash::make('password'),
                'real_name' => '廉州东城小学管理员',
                'organization_id' => $this->organizationIds['district_school'],
                'organization_type' => 'school',
                'organization_level' => 5,
                'school_id' => $this->organizationIds['district_school'], // 兼容字段
                'status' => 1
            ]
        );

        // 6. 创建省直学校管理员
        $provinceSchoolAdmin = User::firstOrCreate(
            ['username' => 'province_school_admin'],
            [
                'email' => 'province_school@test.com',
                'password' => Hash::make('password'),
                'real_name' => '石家庄精英中学管理员',
                'organization_id' => $this->organizationIds['province_school'],
                'organization_type' => 'school',
                'organization_level' => 5,
                'school_id' => $this->organizationIds['province_school'], // 兼容字段
                'status' => 1
            ]
        );

        // 7. 创建市直学校管理员
        $citySchoolAdmin = User::firstOrCreate(
            ['username' => 'city_school_admin'],
            [
                'email' => 'city_school@test.com',
                'password' => Hash::make('password'),
                'real_name' => '石家庄市第一中学管理员',
                'organization_id' => $this->organizationIds['city_school'],
                'organization_type' => 'school',
                'organization_level' => 5,
                'school_id' => $this->organizationIds['city_school'], // 兼容字段
                'status' => 1
            ]
        );

        // 8. 创建区县直学校管理员
        $countySchoolAdmin = User::firstOrCreate(
            ['username' => 'county_school_admin'],
            [
                'email' => 'county_school@test.com',
                'password' => Hash::make('password'),
                'real_name' => '通安小学管理员',
                'organization_id' => $this->organizationIds['county_school'],
                'organization_type' => 'school',
                'organization_level' => 5,
                'school_id' => $this->organizationIds['county_school'], // 兼容字段
                'status' => 1
            ]
        );

        // 分配角色
        $this->assignUserRoles($provinceAdmin, 'province_admin', 'region', $this->organizationIds['province']);
        $this->assignUserRoles($cityAdmin, 'city_admin', 'region', $this->organizationIds['city']);
        $this->assignUserRoles($countyAdmin, 'county_admin', 'region', $this->organizationIds['county']);
        $this->assignUserRoles($districtAdmin, 'district_admin', 'region', $this->organizationIds['district']);
        $this->assignUserRoles($schoolAdmin, 'school_admin', 'school', $this->organizationIds['district_school']);
        $this->assignUserRoles($provinceSchoolAdmin, 'school_admin', 'school', $this->organizationIds['province_school']);
        $this->assignUserRoles($citySchoolAdmin, 'school_admin', 'school', $this->organizationIds['city_school']);
        $this->assignUserRoles($countySchoolAdmin, 'school_admin', 'school', $this->organizationIds['county_school']);
    }

    private function assignUserRoles($user, $roleCode, $scopeType, $scopeId)
    {
        $role = Role::where('code', $roleCode)->first();
        if ($role) {
            UserRole::firstOrCreate([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'scope_type' => $scopeType,
                'scope_id' => $scopeId
            ]);
        }
    }

    private $organizationIds = [];
}

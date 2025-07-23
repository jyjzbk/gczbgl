<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TextbookVersion;

class TextbookVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建教材版本数据...');

        $versions = [
            [
                'name' => '人教版',
                'code' => 'PEP',
                'publisher' => '人民教育出版社',
                'description' => '人民教育出版社出版的教材',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'name' => '苏教版',
                'code' => 'JSEP',
                'publisher' => '江苏教育出版社',
                'description' => '江苏教育出版社出版的教材',
                'status' => 1,
                'sort_order' => 2
            ],
            [
                'name' => '北师大版',
                'code' => 'BNU',
                'publisher' => '北京师范大学出版社',
                'description' => '北京师范大学出版社出版的教材',
                'status' => 1,
                'sort_order' => 3
            ],
            [
                'name' => '沪科版',
                'code' => 'HKEP',
                'publisher' => '上海科学技术出版社',
                'description' => '上海科学技术出版社出版的教材',
                'status' => 1,
                'sort_order' => 4
            ],
            [
                'name' => '粤教版',
                'code' => 'GDEP',
                'publisher' => '广东教育出版社',
                'description' => '广东教育出版社出版的教材',
                'status' => 1,
                'sort_order' => 5
            ]
        ];

        foreach ($versions as $version) {
            TextbookVersion::firstOrCreate(
                ['code' => $version['code']],
                $version
            );
        }

        $this->command->info('教材版本数据创建完成！');
    }
}

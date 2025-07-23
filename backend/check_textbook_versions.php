<?php

require_once 'vendor/autoload.php';

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "📚 当前数据库中的教材版本:\n";
echo str_repeat("=", 50) . "\n";

try {
    $versions = DB::table('textbook_versions')->orderBy('sort_order')->get();
    
    if ($versions->count() > 0) {
        foreach ($versions as $version) {
            echo "• {$version->name} ({$version->code})\n";
            echo "  出版社: {$version->publisher}\n";
            echo "  状态: " . ($version->status ? '启用' : '禁用') . "\n";
            echo "  排序: {$version->sort_order}\n";
            echo "  创建时间: {$version->created_at}\n";
            echo "\n";
        }
        
        echo "总计: " . $versions->count() . " 个教材版本\n\n";
        
        echo "💡 建议使用的新版本代码:\n";
        echo "• SDEP (鲁教版 - 山东教育出版社)\n";
        echo "• HEBEP (冀教版 - 河北教育出版社)\n";
        echo "• ZJEP (浙教版 - 浙江教育出版社)\n";
        echo "• XJEP (湘教版 - 湖南教育出版社)\n";
        echo "• CQEP (渝教版 - 重庆出版社)\n";
        
    } else {
        echo "❌ 数据库中没有教材版本数据\n";
        echo "💡 您可以运行种子文件来创建初始数据:\n";
        echo "php artisan db:seed --class=TextbookVersionSeeder\n";
    }
    
} catch (Exception $e) {
    echo "❌ 查询失败: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ 检查完成\n";

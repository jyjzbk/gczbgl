<?php
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Http\Controllers\Api\OrganizationController;
use Illuminate\Http\Request;

// 创建Laravel应用实例
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 获取用户
$user = User::find(1);

// 创建控制器和请求
$controller = new OrganizationController();
$request = new Request();
$request->setUserResolver(function() use ($user) { 
    return $user; 
});

// 调用API
$response = $controller->getOrganizationTree($request);
$data = json_decode($response->getContent(), true);

// 查找学校节点
function findSchool($nodes) {
    foreach ($nodes as $node) {
        if ($node['type'] === 'school') {
            echo "School found: " . $node['name'] . "\n";
            echo "School type field: " . (isset($node['school_type']) ? $node['school_type'] : 'NOT SET') . "\n";
            echo "Type field: " . $node['type'] . "\n";
            echo "Full node keys: " . implode(', ', array_keys($node)) . "\n";
            return true;
        }
        if (isset($node['children']) && !empty($node['children'])) {
            if (findSchool($node['children'])) {
                return true;
            }
        }
    }
    return false;
}

if (isset($data['data'])) {
    echo "API Response successful\n";
    if (findSchool($data['data'])) {
        echo "School node found and analyzed\n";
    } else {
        echo "No school nodes found\n";
    }
} else {
    echo "No data in API response\n";
    echo "Response: " . json_encode($data) . "\n";
}

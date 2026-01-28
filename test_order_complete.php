<?php
namespace think;

/**
 * 手动触发订单完成事件 - 用于测试
 * 使用方法: php test_order_complete.php <order_id>
 */

// 检查参数
if ($argc < 2) {
    die("用法: php test_order_complete.php <order_id>\n");
}

$order_id = intval($argv[1]);

if ($order_id <= 0) {
    die("错误: 订单ID必须大于0\n");
}

// 加载ThinkPHP框架
require __DIR__ . '/vendor/autoload.php';

// 启动应用
$app = (new App())->http;
$response = $app->run();

// 手动调用订单完成
use app\model\order\OrderCommon;

echo "准备完成订单: order_id = {$order_id}\n";

$order_common = new OrderCommon();
$result = $order_common->orderComplete($order_id);

echo "执行结果:\n";
print_r($result);
echo "\n";

// 检查日志
if (file_exists('/tmp/order_complete_debug.log')) {
    echo "\n调试日志内容:\n";
    echo file_get_contents('/tmp/order_complete_debug.log');
}

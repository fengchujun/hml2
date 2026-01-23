<?php
/**
 * 检查订单配置和定时任务
 */

// 使用默认数据库配置
$hostname = '127.0.0.1';
$database = 'hml';
$username = 'root';
$password = '123456';
$prefix = 'hml_';
$charset = 'utf8mb4';

// 连接数据库
$dsn = "mysql:host={$hostname};dbname={$database};charset={$charset}";
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== 订单配置 (site_id=1) ===\n";
    $stmt = $pdo->prepare("SELECT value FROM {$prefix}config WHERE site_id = 1 AND app_module = 'shop' AND config_key = 'ORDER_CONFIG'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $order_config = json_decode($result['value'], true);
        echo "自动完成天数 (auto_complete): " . ($order_config['auto_complete'] ?? '未设置') . " 天\n";
        echo "自动收货天数 (auto_take_delivery): " . ($order_config['auto_take_delivery'] ?? '未设置') . " 天\n";
        echo "\n完整配置:\n";
        print_r($order_config);
    } else {
        echo "未找到订单配置\n";
    }

    echo "\n=== 最近的订单完成定时任务 ===\n";
    $stmt = $pdo->prepare("SELECT * FROM {$prefix}system_cron WHERE type = 'CronOrderComplete' ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $crons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($crons) {
        foreach ($crons as $cron) {
            echo "ID: {$cron['id']}, 订单ID: {$cron['relate_id']}, ";
            echo "执行时间: " . date('Y-m-d H:i:s', $cron['execute_time']);
            echo " (状态: {$cron['status']})\n";
        }
    } else {
        echo "没有找到订单完成定时任务\n";
    }

    echo "\n=== 最近的订单 (order_status=4 已收货) ===\n";
    $stmt = $pdo->prepare("SELECT order_id, order_no, order_status, sign_time, distribution_complete_coupons FROM {$prefix}order WHERE order_status = 4 ORDER BY order_id DESC LIMIT 3");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($orders) {
        foreach ($orders as $order) {
            echo "订单ID: {$order['order_id']}, 订单号: {$order['order_no']}, ";
            echo "收货时间: " . ($order['sign_time'] ? date('Y-m-d H:i:s', $order['sign_time']) : '未收货');
            echo ", 完成优惠券: " . ($order['distribution_complete_coupons'] ?: '无') . "\n";
        }
    } else {
        echo "没有找到已收货的订单\n";
    }

} catch (PDOException $e) {
    die("数据库连接错误: " . $e->getMessage() . "\n");
}

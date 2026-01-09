#!/bin/bash
# Workerman 诊断脚本

echo "=========================================="
echo " Workerman 诊断工具"
echo "=========================================="
echo ""

# 1. 检查 PHP 版本
echo "1. PHP 版本检查"
/www/server/php/74/bin/php -v
echo ""

# 2. 检查必需扩展
echo "2. PHP 扩展检查"
/www/server/php/74/bin/php -m | grep -E "pcntl|posix|pdo_mysql" || echo "缺少必需扩展！"
echo ""

# 3. 检查端口占用
echo "3. 端口占用检查 (7001)"
netstat -tunlp | grep 7001 || echo "端口 7001 未被占用"
echo ""

# 4. 检查语法错误
echo "4. PHP 语法检查"
/www/server/php/74/bin/php -l workerman_server.php
echo ""

# 5. 测试基础启动（不启动 Worker，只加载类）
echo "5. 测试 Workerman 类加载"
/www/server/php/74/bin/php -r "
require_once './vendor/autoload.php';
use Workerman\Worker;
echo 'Workerman 类加载成功: ' . Worker::VERSION . PHP_EOL;
"
echo ""

# 6. 测试 ThinkPHP 初始化
echo "6. 测试 ThinkPHP 初始化"
/www/server/php/74/bin/php -r "
require_once './vendor/autoload.php';
try {
    \$app = new \think\App();
    echo 'ThinkPHP 初始化成功' . PHP_EOL;
} catch (\Throwable \$e) {
    echo 'ThinkPHP 初始化失败: ' . \$e->getMessage() . PHP_EOL;
    echo '文件: ' . \$e->getFile() . ':' . \$e->getLine() . PHP_EOL;
}
"
echo ""

# 7. 查看错误日志
echo "7. 查看最近的错误日志"
if [ -f "runtime/workerman.log" ]; then
    echo "=== runtime/workerman.log ==="
    tail -50 runtime/workerman.log
else
    echo "没有找到 runtime/workerman.log"
fi
echo ""

if [ -d "runtime/log" ]; then
    echo "=== ThinkPHP 错误日志 ==="
    find runtime/log -name "*.log" -type f -mtime -1 -exec tail -20 {} \;
fi

echo ""
echo "=========================================="
echo " 诊断完成"
echo "=========================================="
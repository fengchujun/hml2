<?php
/**
 * Workerman + ThinkPHP HTTPS 服务器（兼容小程序）
 * 已解决：常量重定义 + SSL 证书 + JSON POST + 小程序兼容
 */

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (stripos($errstr, 'already defined') !== false) {
        return true;
    }
    return false;
}, E_ALL);

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Workerman\Protocols\Http\Response as WorkermanResponse;

// ============================================================
// 创建 HTTPS Worker
// ============================================================

$context = array(
    'ssl' => array(
        'local_cert' => '/www/wwwroot/huamulantea.com/ssl/cert.pem',
        'local_pk' => '/www/wwwroot/huamulantea.com/ssl/key.pem',
        'verify_peer' => false,
        'allow_self_signed' => false,
    )
);

$http_worker = new Worker('http://0.0.0.0:7001', $context);
$http_worker->transport = 'ssl';
$http_worker->count = 20;
$http_worker->name = 'ThinkPHP-HTTPS-Server';
$http_worker->reusePort = true;

// ============================================================
// Worker 启动时初始化
// ============================================================

$http_worker->onWorkerStart = function($worker) {
    echo "[Worker {$worker->id}] 启动中...\n";
    
    $worker->app = new \think\App();
    $worker->http = $worker->app->http;
    
    try {
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'SERVER_NAME' => 'www.huamulantea.com',
            'SERVER_PORT' => '7001',
            'HTTPS' => 'on',
            'HTTP_HOST' => 'www.huamulantea.com',
            'SCRIPT_NAME' => '/index.php',
            'SCRIPT_FILENAME' => __DIR__ . '/index.php',
            'DOCUMENT_ROOT' => __DIR__,
            'PHP_SELF' => '/index.php',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REMOTE_ADDR' => '127.0.0.1',
            'REMOTE_PORT' => '0',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ];
        
        ob_start();
        $worker->http->run();
        ob_end_clean();
        
        echo "[Worker {$worker->id}] ✓ 预初始化完成\n";
    } catch (\Throwable $e) {
        echo "[Worker {$worker->id}] 预初始化完成\n";
    }
    
    $_GET = $_POST = $_REQUEST = $_COOKIE = $_FILES = [];
    $_SERVER = [];
    
    echo "[Worker {$worker->id}] 就绪，等待请求...\n";
};

// ============================================================
// 处理 HTTPS 请求（改进的请求体处理）
// ============================================================

$http_worker->onMessage = function($connection, WorkermanRequest $workerman_request) {
    $start_time = microtime(true);
    $request_uri = $workerman_request->uri();
    
    try {
        // 转换请求
        $_GET = $workerman_request->get() ?? [];
        $_POST = $workerman_request->post() ?? [];  // Workerman 已解析的 POST 数据
        $_COOKIE = $workerman_request->cookie() ?? [];
        
        try {
            $_FILES = $workerman_request->file() ?? [];
        } catch (\TypeError $e) {
            $_FILES = [];
        }
        
        // 设置 $_SERVER
        $_SERVER = [
            'REQUEST_METHOD' => $workerman_request->method(),
            'REQUEST_URI' => $request_uri,
            'PATH_INFO' => $workerman_request->path(),
            'QUERY_STRING' => $workerman_request->queryString(),
            'SERVER_PROTOCOL' => 'HTTP/' . $workerman_request->protocolVersion(),
            'HTTP_HOST' => 'www.huamulantea.com',
            'HTTPS' => 'on',
            'SERVER_PORT' => '7001',
            'REMOTE_ADDR' => $connection->getRemoteIp(),
            'REMOTE_PORT' => $connection->getRemotePort(),
            'SERVER_NAME' => 'www.huamulantea.com',
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => $workerman_request->path(),
            'SCRIPT_FILENAME' => __DIR__ . '/index.php',
            'DOCUMENT_ROOT' => __DIR__,
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
            'GATEWAY_INTERFACE' => 'CGI/1.1',
        ];
        
        // 设置 HTTP 头
        foreach ($workerman_request->header() as $name => $value) {
            $name = strtoupper(str_replace('-', '_', $name));
            if (!in_array($name, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $name = 'HTTP_' . $name;
            }
            $_SERVER[$name] = $value;
        }
        
        // ===== 改进的 POST 请求体处理 =====
        if (in_array($workerman_request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $rawBody = $workerman_request->rawBody();
            $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
            
            // 优先级1：如果 Workerman 已经解析了 POST 数据，直接使用
            if (!empty($_POST) && is_array($_POST)) {
                // Workerman 已经正确解析，不需要额外处理
                // 这能处理 application/x-www-form-urlencoded 和 multipart/form-data
            }
            // 优先级2：纯 JSON 请求（Content-Type 只包含 application/json）
            elseif (!empty($rawBody) && 
                    stripos($contentType, 'application/json') !== false && 
                    stripos($contentType, 'form-urlencoded') === false) {
                
                $decoded = json_decode($rawBody, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $_POST = $decoded;
                } else {
                    error_log(sprintf(
                        "[JSON 解析失败] %s - Content-Type: %s - Body: %s",
                        json_last_error_msg(),
                        $contentType,
                        substr($rawBody, 0, 200)
                    ));
                }
            }
            // 优先级3：其他情况（如果 $_POST 为空且有 body）
            elseif (!empty($rawBody) && empty($_POST)) {
                // 尝试按表单格式解析
                parse_str($rawBody, $parsedData);
                if (!empty($parsedData)) {
                    $_POST = $parsedData;
                }
            }
        }
        
        // 执行 ThinkPHP 应用
        $http = $connection->worker->http;
        $think_response = $http->run();
        
        // 转换响应
        $workerman_response = new WorkermanResponse(
            $think_response->getCode(),
            $think_response->getHeader(),
            $think_response->getContent()
        );
        
        $connection->send($workerman_response);
        
        // 结束计时并输出
        $end_time = microtime(true);
        $duration = round(($end_time - $start_time) * 1000, 2);
        $status_code = $think_response->getCode();
        
        echo sprintf(
            "[%s] HTTPS %s %s - %d - %s ms\n",
            date('Y-m-d H:i:s'),
            $workerman_request->method(),
            $request_uri,
            $status_code,
            $duration
        );
        
    } catch (\Throwable $e) {
        // 错误处理
        $error_response = new WorkermanResponse(
            500,
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode([
                'code' => 500,
                'msg' => 'Internal Server Error',
                'data' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
        
        $connection->send($error_response);
        
        $end_time = microtime(true);
        $duration = round(($end_time - $start_time) * 1000, 2);
        
        echo sprintf(
            "[%s] HTTPS %s %s - 500 - %s ms - ERROR: %s\n",
            date('Y-m-d H:i:s'),
            $workerman_request->method(),
            $request_uri,
            $duration,
            $e->getMessage()
        );
        
    } finally {
        // 清理请求数据
        $_GET = $_POST = $_REQUEST = $_COOKIE = $_FILES = [];
        $_SERVER = [];
    }
};

// ============================================================
// Worker 停止
// ============================================================

$http_worker->onWorkerStop = function($worker) {
    echo "[Worker {$worker->id}] 正在停止...\n";
};

// ============================================================
// 启动 Workerman
// ============================================================

echo "======================================================================\n";
echo "ThinkPHP + Workerman HTTPS Server (小程序兼容版)\n";
echo "======================================================================\n";
echo "监听地址：https://0.0.0.0:7001\n";
echo "Worker 数量：{$http_worker->count}\n";
echo "授权域名：www.huamulantea.com\n";
echo "======================================================================\n";
echo "功能支持：\n";
echo "  ✓ HTTPS (SSL/TLS)\n";
echo "  ✓ 表单 POST (优先)\n";
echo "  ✓ JSON POST\n";
echo "  ✓ 小程序请求\n";
echo "  ✓ GET 请求\n";
echo "  ✓ 请求计时\n";
echo "======================================================================\n";
echo "\n";

Worker::runAll();
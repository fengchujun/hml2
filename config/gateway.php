<?php

$name = 'b2c';//同一服务器不同站点名称不能相同
$register_port = 1342;//同一服务器不同站点注册端口不能相同
$gateway_port = 8283;//同一服务器不同站点gateway端口不能相同

return [
    'database' => [
        // 连接地址
        'host'   => 'localhost',
        // 数据库名称
        'dbname' =>  'test_ayatimes_co',
        // 用户名
        'user'   => 'test_ayatimes_co',
        // 密码
        'passwd' => 'TaW8Di1MhXD2',
        // 端口
        'port'   => '3306',
        // 表前缀
        'prefix' => 'hml_',
    ],
    'register' => [
        'name' => $name.'_register',
        'socket_name' => 'text://0.0.0.0:'.$register_port,
    ],
    'worker' => [
        'name' => $name.'_worker',
        'count' => 4,
        'register_address' => '127.0.0.1:'.$register_port,
    ],
    'gateway' => [
        'name' => $name.'_gateway',
        'count' => 4,
        'register_address' => '127.0.0.1:'.$register_port,
        'socket_name' => "websocket://0.0.0.0:".$gateway_port,
        'lan_ip' => '127.0.0.1',
        'start_port' => 4100,//同一服务器不同站点端口号不能相同，且要相差最少4个数字，如4104,4108,4200,4300
        'ping_interval' => 60,
        'ping_not_response_limit' => 1,
        'ping_data' => '',
    ],
    'ssl' => [
        'cert' => 'server.pem', // 无需设置
        'key' => 'server.key', // 无需设置
        'enable' => false
    ],
];

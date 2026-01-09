<?php
// Workerman 持久化模式：防止重复执行
// 在 Worker 进程中，这个事件只需要执行一次
if (defined('HTTPRUN_EXECUTED')) {
    return; // 已经执行过，跳过
}

get_funcs('HttpRun');

// 标记为已执行
define('HTTPRUN_EXECUTED', true);
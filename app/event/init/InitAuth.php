<?php
// Workerman 持久化模式：防止重复执行
if (defined('INITAUTH_EXECUTED')) {
    return;
}

get_funcs('InitAuth');

define('INITAUTH_EXECUTED', true);
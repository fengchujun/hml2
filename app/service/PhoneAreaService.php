<?php
/**
 * 手机号归属地查询服务
 * 数据来源：https://github.com/dannyhu926/phone_location
 * 数据更新时间：2025年03月
 * 原始记录数：515,729条
 */

namespace app\service;

class PhoneAreaService
{
    /**
     * 手机号段和区号对应关系（懒加载）
     * @var array
     */
    private static $phoneAreaMap = null;

    /**
     * 加载手机号归属地数据
     * @return array
     */
    private static function loadData()
    {
        if (self::$phoneAreaMap === null) {
            self::$phoneAreaMap = require __DIR__ . '/PhoneAreaData.php';
        }
        return self::$phoneAreaMap;
    }

    /**
     * 根据手机号获取区号
     * @param string $mobile 手机号
     * @return string 区号（去掉前导0），如果查询不到返回 86
     */
    public static function getAreaCode($mobile)
    {
        if (empty($mobile) || strlen($mobile) < 7) {
            return '86'; // 默认返回中国区号
        }

        // 加载数据
        $areaMap = self::loadData();

        // 取手机号前3位
        $prefix = substr($mobile, 0, 3);

        // 查询映射表
        if (isset($areaMap[$prefix])) {
            return $areaMap[$prefix];
        }

        // 如果查询不到，返回默认值
        return '86';
    }

    /**
     * 批量添加手机号段映射（用于扩展）
     * @param string $areaCode 区号（去掉前导0）
     * @param array $prefixes 手机号前缀数组
     */
    public static function addAreaMapping($areaCode, array $prefixes)
    {
        // 确保数据已加载
        self::loadData();

        foreach ($prefixes as $prefix) {
            self::$phoneAreaMap[$prefix] = $areaCode;
        }
    }

    /**
     * 获取所有支持的号段
     * @return array
     */
    public static function getAllPrefixes()
    {
        return array_keys(self::loadData());
    }

    /**
     * 检查手机号段是否在数据库中
     * @param string $mobile 手机号
     * @return bool
     */
    public static function isSupported($mobile)
    {
        if (empty($mobile) || strlen($mobile) < 3) {
            return false;
        }

        $areaMap = self::loadData();
        $prefix = substr($mobile, 0, 3);

        return isset($areaMap[$prefix]);
    }
}

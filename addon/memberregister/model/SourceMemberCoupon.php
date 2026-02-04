<?php

namespace addon\memberregister\model;

use app\model\BaseModel;

/**
 * 推荐注册赠送优惠券配置
 */
class SourceMemberCoupon extends BaseModel
{
    /**
     * 获取启用的优惠券配置列表
     * @param int $site_id
     * @return array
     */
    public function getEnabledList($site_id)
    {
        $condition = [
            ['site_id', '=', $site_id],
            ['is_enabled', '=', 1]
        ];
        $list = model('source_member_coupon_config')->getList($condition, '*', 'id asc');
        return $this->success($list);
    }

    /**
     * 获取全部配置列表
     * @param int $site_id
     * @return array
     */
    public function getList($site_id)
    {
        $condition = [
            ['site_id', '=', $site_id]
        ];
        $list = model('source_member_coupon_config')->getList($condition, '*', 'id asc');
        return $this->success($list);
    }

    /**
     * 添加配置
     * @param array $data
     * @return array
     */
    public function addConfig($data)
    {
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = model('source_member_coupon_config')->add($data);
        return $this->success($res);
    }

    /**
     * 编辑配置
     * @param array $data
     * @param int $id
     * @return array
     */
    public function editConfig($data, $id)
    {
        $data['update_time'] = time();
        $condition = [['id', '=', $id]];
        $res = model('source_member_coupon_config')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 删除配置
     * @param int $id
     * @return array
     */
    public function deleteConfig($id)
    {
        $condition = [['id', '=', $id]];
        $res = model('source_member_coupon_config')->delete($condition);
        return $this->success($res);
    }

    /**
     * 批量保存配置（先删后增）
     * @param int $site_id
     * @param array $coupon_list [['coupon_type_id' => X, 'num' => Y], ...]
     * @return array
     */
    public function saveConfig($site_id, $coupon_list)
    {
        // 删除旧配置
        model('source_member_coupon_config')->delete([['site_id', '=', $site_id]]);

        // 添加新配置
        $time = time();
        foreach ($coupon_list as $item) {
            if (empty($item['coupon_type_id'])) continue;
            model('source_member_coupon_config')->add([
                'site_id' => $site_id,
                'coupon_type_id' => $item['coupon_type_id'],
                'num' => $item['num'] ?? 1,
                'is_enabled' => 1,
                'create_time' => $time,
                'update_time' => $time
            ]);
        }
        return $this->success();
    }
}

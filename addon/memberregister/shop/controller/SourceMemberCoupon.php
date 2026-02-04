<?php

namespace addon\memberregister\shop\controller;

use addon\memberregister\model\SourceMemberCoupon as SourceMemberCouponModel;
use app\shop\controller\BaseShop;

/**
 * 推荐注册赠送优惠券配置
 */
class SourceMemberCoupon extends BaseShop
{
    /**
     * 获取配置列表 / 保存配置
     */
    public function index()
    {
        $model = new SourceMemberCouponModel();

        if (request()->isJson()) {
            $coupon_list = input('coupon_list', []);
            if (is_string($coupon_list)) {
                $coupon_list = json_decode($coupon_list, true);
            }
            $res = $model->saveConfig($this->site_id, $coupon_list);
            $this->addLog('设置推荐注册赠送优惠券');
            return $res;
        } else {
            $list = $model->getList($this->site_id);
            return $list;
        }
    }

    /**
     * 添加单条配置
     */
    public function add()
    {
        $model = new SourceMemberCouponModel();
        $data = [
            'site_id' => $this->site_id,
            'coupon_type_id' => input('coupon_type_id', 0),
            'num' => input('num', 1),
            'is_enabled' => input('is_enabled', 1)
        ];
        $res = $model->addConfig($data);
        $this->addLog('添加推荐注册优惠券配置');
        return $res;
    }

    /**
     * 编辑配置
     */
    public function edit()
    {
        $model = new SourceMemberCouponModel();
        $id = input('id', 0);
        $data = [
            'coupon_type_id' => input('coupon_type_id', 0),
            'num' => input('num', 1),
            'is_enabled' => input('is_enabled', 1)
        ];
        $res = $model->editConfig($data, $id);
        $this->addLog('编辑推荐注册优惠券配置');
        return $res;
    }

    /**
     * 删除配置
     */
    public function delete()
    {
        $model = new SourceMemberCouponModel();
        $id = input('id', 0);
        $res = $model->deleteConfig($id);
        $this->addLog('删除推荐注册优惠券配置');
        return $res;
    }
}

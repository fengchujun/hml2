<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 杭州牛之云科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * =========================================================
 */

namespace app\event\order;

use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\member\MemberLevel;
use app\model\order\OrderCommon;

/**
 * 订单完成事件
 */
class OrderComplete
{

    // 行为扩展的执行入口必须是run
    public function handle($data)
    {
        //订单返还积分
        $order_model = new OrderCommon();
        $condition = [
            [ 'order_id', '=', $data[ 'order_id' ] ]
        ];
        $order_info = $order_model->getOrderInfo($condition, 'order_money,order_status,site_id,member_id')[ 'data' ] ?? [];
        //如果缺失已完成
        if ($order_info[ 'order_status' ] == 10) {
            //会员等级 计算积分返还比率
            $site_id = $order_info[ 'site_id' ];
            $member_id = $order_info[ 'member_id' ];
            //存在散客的情况
            if ($member_id > 0) {
                $member_model = new Member();
                $member_info_result = $member_model->getMemberInfo([ [ 'member_id', '=', $member_id ], [ 'site_id', '=', $site_id ] ], 'member_level');
                $member_info = $member_info_result[ 'data' ];
                if ($member_info[ 'member_level' ] > 0) {
                    $member_level_model = new MemberLevel();
                    $member_level_info_result = $member_level_model->getMemberLevelInfo([ [ 'level_id', '=', $member_info[ 'member_level' ] ], [ 'site_id', '=', $site_id ] ], 'point_feedback');
                    $member_level_info = $member_level_info_result[ 'data' ];
                    if ($member_level_info[ 'point_feedback' ] > 0) {
                        //计算返还的积分
                        $point = round($order_info[ 'order_money' ] * $member_level_info[ 'point_feedback' ]);
                        $member_account_model = new MemberAccount();
                        $result = $member_account_model->addMemberAccount($site_id, $member_id, 'point', $point, 'order', '会员消费回馈积分', '会员消费奖励发放');
                        if ($result[ 'code' ] < 0) {
                            return $result;
                        }
                    }
                }
            }

            // 发放分销完成优惠券
            $this->sendDistributionCompleteCoupon($data['order_id'], $member_id);

        }

        return $order_model->success();
    }

    /**
     * 发放分销完成优惠券
     * @param int $order_id 订单ID
     * @param int $member_id 会员ID
     */
    private function sendDistributionCompleteCoupon($order_id, $member_id)
    {
        try {
            // 从订单表读取完成优惠券数据
            $order_info = model('order')->getInfo([['order_id', '=', $order_id]], 'distribution_complete_coupons,site_id');

            if (!$order_info || empty($order_info['distribution_complete_coupons'])) {
                \think\facade\Log::write('订单完成优惠券发放 - 订单无完成优惠券配置: order_id=' . $order_id);
                return;
            }

            // 解析完成优惠券数据（JSON格式：{goods_id: coupon_type_id}）
            $complete_coupons = json_decode($order_info['distribution_complete_coupons'], true);

            if (!$complete_coupons || !is_array($complete_coupons)) {
                \think\facade\Log::write('订单完成优惠券发放 - 完成优惠券数据格式错误: order_id=' . $order_id);
                return;
            }

            \think\facade\Log::write('订单完成优惠券发放 - 开始发放: order_id=' . $order_id . ', member_id=' . $member_id . ', coupons=' . json_encode($complete_coupons));

            // 检查优惠券插件是否存在
            if (!class_exists('\addon\coupon\model\Coupon')) {
                \think\facade\Log::write('订单完成优惠券发放 - Coupon类不存在');
                return;
            }

            $coupon_model = new \addon\coupon\model\Coupon();
            $site_id = $order_info['site_id'];

            // 遍历每个商品的完成优惠券
            foreach ($complete_coupons as $goods_id => $coupon_type_id) {
                if ($coupon_type_id > 0) {
                    \think\facade\Log::write('订单完成优惠券发放 - 发放优惠券: goods_id=' . $goods_id . ', coupon_type_id=' . $coupon_type_id);

                    // 发放优惠券
                    $coupon_data = [
                        ['coupon_type_id' => $coupon_type_id, 'num' => 1]
                    ];
                    $result = $coupon_model->giveCoupon(
                        $coupon_data,
                        $site_id,
                        $member_id,
                        \addon\coupon\model\Coupon::GET_TYPE_ACTIVITY_GIVE
                    );

                    if ($result && isset($result['code'])) {
                        if ($result['code'] >= 0) {
                            \think\facade\Log::write('订单完成优惠券发放 - 发放成功: goods_id=' . $goods_id . ', coupon_type_id=' . $coupon_type_id);
                        } else {
                            \think\facade\Log::write('订单完成优惠券发放 - 发放失败: goods_id=' . $goods_id . ', error=' . $result['message']);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \think\facade\Log::error('发放分销完成优惠券异常: ' . $e->getMessage() . ', trace: ' . $e->getTraceAsString());
        }
    }

}
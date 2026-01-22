<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 杭州牛之云科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * =========================================================
 */

namespace app\model\member;

use app\model\BaseModel;
use app\model\Model;
use think\facade\Db;
use think\facade\Log;

/**
 * 会员分销商品访问记录管理
 */
class MemberSourceGoods extends Model
{
    protected $table = 'member_source_goods';

    /**
     * 获取会员对某商品的访问记录
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @return array|null
     */
    public function getRecord($member_id, $goods_id)
    {
        $record = $this->getInfo([
            ['member_id', '=', $member_id],
            ['goods_id', '=', $goods_id]
        ]);

        return $record;
    }

    /**
     * 检查会员是否对某商品有权限（通过分销链接访问过）
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @return bool
     */
    public function checkPermission($member_id, $goods_id)
    {
        $count = $this->getCount([
            ['member_id', '=', $member_id],
            ['goods_id', '=', $goods_id]
        ]);

        return $count > 0;
    }

    /**
     * 创建商品访问记录
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @param int $distributor_id 分销员ID
     * @param int $distributor_level 分销员等级
     * @param int $site_id 站点ID
     * @return int
     */
    public function createRecord($member_id, $goods_id, $distributor_id, $distributor_level, $site_id = 0)
    {
        // 检查是否已存在记录
        $exists = $this->getRecord($member_id, $goods_id);
        if ($exists) {
            // 更新最后访问时间
            return $this->update([
                'last_visit_time' => time()
            ], [
                ['member_id', '=', $member_id],
                ['goods_id', '=', $goods_id]
            ]);
        }

        $data = [
            'site_id' => $site_id ?: request()->siteId(),
            'member_id' => $member_id,
            'goods_id' => $goods_id,
            'distributor_id' => $distributor_id,
            'distributor_level' => $distributor_level,
            'first_visit_time' => time(),
            'last_visit_time' => time(),
            'create_time' => time()
        ];

        return $this->add($data);
    }

    /**
     * 注册时创建商品访问记录（从前端缓存传入）
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @param int $distributor_id 分销员ID
     * @param int $site_id 站点ID
     * @return int
     */
    public function createRecordOnRegister($member_id, $goods_id, $distributor_id, $site_id = 0)
    {
        // 获取分销员信息
        $distributor = model('member')->getInfo([['member_id', '=', $distributor_id]], 'member_level,fx_level');

        if (!$distributor || $distributor['member_level'] != 6) {
            return 0;
        }

        return $this->createRecord($member_id, $goods_id, $distributor_id, $distributor['fx_level'], $site_id);
    }

    /**
     * 发放首次优惠券
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @param int $distributor_level 分销员等级
     * @return bool
     */
    public function sendFirstCoupon($member_id, $goods_id, $distributor_level)
    {
        try {
            // 获取商品的首次优惠券配置
            $goods_info = model('goods')->getInfo([['goods_id', '=', $goods_id]], 'fx_level'.$distributor_level.'_first_coupon');

            if (!$goods_info) {
                return false;
            }

            $coupon_type_id = $goods_info['fx_level'.$distributor_level.'_first_coupon'] ?? 0;

            // 如果未配置优惠券，跳过
            if ($coupon_type_id <= 0) {
                return false;
            }

            // 发放优惠券（使用优惠券插件的发放逻辑）
            if (class_exists('\addon\coupon\model\Coupon')) {
                $coupon_model = new \addon\coupon\model\Coupon();
                $result = $coupon_model->giveCoupon([
                    'member_id' => $member_id,
                    'coupon_type_id' => $coupon_type_id,
                    'get_type' => 'distribution' // 分销赠送
                ]);

                // 更新发放时间
                $this->update([
                    'first_coupon_last_time' => time()
                ], [
                    ['member_id', '=', $member_id],
                    ['goods_id', '=', $goods_id]
                ]);

                return $result !== false;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('发放首次优惠券失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 发放完成优惠券（订单完成后）
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @param int $distributor_level 分销员等级
     * @return bool
     */
    public function sendCompleteCoupon($member_id, $goods_id, $distributor_level)
    {
        try {
            // 获取商品的完成优惠券配置
            $goods_info = model('goods')->getInfo([['goods_id', '=', $goods_id]], 'fx_level'.$distributor_level.'_complete_coupon');

            if (!$goods_info) {
                return false;
            }

            $coupon_type_id = $goods_info['fx_level'.$distributor_level.'_complete_coupon'] ?? 0;

            // 如果未配置优惠券，跳过
            if ($coupon_type_id <= 0) {
                return false;
            }

            // 发放优惠券
            if (class_exists('\addon\coupon\model\Coupon')) {
                $coupon_model = new \addon\coupon\model\Coupon();
                $result = $coupon_model->giveCoupon([
                    'member_id' => $member_id,
                    'coupon_type_id' => $coupon_type_id,
                    'get_type' => 'distribution_complete' // 分销完成赠送
                ]);

                return $result !== false;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('发放完成优惠券失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 检查优惠券是否可用（未使用且未过期）
     * @param int $member_id 会员ID
     * @param int $goods_id 商品ID
     * @param int $distributor_level 分销员等级
     * @return bool 返回true表示需要重新发放
     */
    public function checkCouponExpired($member_id, $goods_id, $distributor_level)
    {
        try {
            // 获取商品的首次优惠券配置
            $goods_info = model('goods')->getInfo([['goods_id', '=', $goods_id]], 'fx_level'.$distributor_level.'_first_coupon');

            if (!$goods_info) {
                return false;
            }

            $coupon_type_id = $goods_info['fx_level'.$distributor_level.'_first_coupon'] ?? 0;

            if ($coupon_type_id <= 0) {
                return false;
            }

            // 检查会员账户中该优惠券是否可用
            $available_coupon = Db::name('member_coupon')->where([
                ['member_id', '=', $member_id],
                ['coupon_type_id', '=', $coupon_type_id],
                ['state', '=', 1], // 未使用
                ['end_time', '>', time()] // 未过期
            ])->find();

            // 如果没有可用优惠券，需要重新发放
            return empty($available_coupon);

            return false;
        } catch (\Exception $e) {
            Log::error('检查优惠券可用性失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取会员通过分销访问的所有商品
     * @param int $member_id 会员ID
     * @return array
     */
    public function getMemberGoodsList($member_id)
    {
        return $this->getList([
            ['member_id', '=', $member_id]
        ]);
    }

    /**
     * 获取分销员推广的会员商品记录
     * @param int $distributor_id 分销员ID
     * @return array
     */
    public function getDistributorRecords($distributor_id)
    {
        return $this->getList([
            ['distributor_id', '=', $distributor_id]
        ]);
    }
}

<?php

namespace app\api\controller;

use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsService;
use app\model\order\OrderCommon;
use app\model\system\Poster;
use app\model\goods\Config as GoodsConfigModel;
use app\model\web\Config as ConfigModel;

class Goods extends BaseApi
{

    /**
     * 修改商品点击量
     * @return string
     */
    public function modifyclicks()
    {
        $sku_id = $this->params['sku_id'] ?? 0;

        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }

        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $goods_model = new GoodsModel();
        $res = $goods_model->modifyClick($sku_id, $this->site_id);
        return $this->response($res);
    }

    /**
     * 获取商品海报
     */
    public function poster()
    {
        $this->checkToken();
        $promotion_type = 'null';
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $this->member_id;
        $poster = new Poster();
        $res = $poster->goods($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param, $promotion_type, $this->site_id, $this->store_id);
        return $this->response($res);
    }

    /**
     * 售后保障
     * @return false|string
     */
    public function aftersale()
    {
        $goods_config_model = new GoodsConfigModel();
        $res = $goods_config_model->getAfterSaleConfig($this->site_id);
        return $this->response($res);
    }

    /**
     * 获取热门搜索关键词
     */
    public function hotSearchWords()
    {
        $config_model = new ConfigModel();
        $info = $config_model->getHotSearchWords($this->site_id, $this->app_module);
        return $this->response($this->success($info[ 'data' ][ 'value' ]));
    }

    /**
     * 获取默认搜索关键词
     */
    public function defaultSearchWords()
    {
        $config_model = new ConfigModel();
        $info = $config_model->getDefaultSearchWords($this->site_id, $this->app_module);
        return $this->response($this->success($info[ 'data' ][ 'value' ]));
    }

    /**
     * 商品服务
     * @return false|string
     */
    public function service()
    {
        $goods_service = new GoodsService();
        $data = $goods_service->getServiceList([ [ 'site_id', '=', $this->site_id ] ], 'service_name,desc,icon');
        foreach ($data[ 'data' ] as $key => $val) {
            $data[ 'data' ][ $key ][ 'icon' ] = json_decode($val[ 'icon' ], true);
        }

        return $this->response($data);
    }

    /**
     * 商品弹幕
     * @return false|string
     */
    public function goodsBarrage()
    {
        $goods_id = $this->params['goods_id'] ?? 0;
        $order = new OrderCommon();
        $field = 'm.headimg as img, m.nickname as title';
        $join = [
            [
                'member m',
                'm.member_id=og.member_id',
                'left'
            ],
            [
                'order o',
                'o.order_id=og.order_id',
                'left'
            ]
        ];
        $data = $order->getOrderGoodsPageList([ [ 'og.site_id', '=', $this->site_id ], [ 'og.goods_id', '=', $goods_id ], [ 'o.pay_status', '=', 1 ] ], 1, 20, 'og.order_goods_id desc', $field, 'og', $join, 'o.member_id');
        return $this->response($data);
    }

    /**
     * 小程序分享图
     * @return false|string
     */
    public function shareImg()
    {
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ] ?? '{}', true);

        $poster = new Poster();
        $res = $poster->shareImg($this->params[ 'page' ] ?? '', $qrcode_param, $this->site_id, $this->store_id);
        return $this->response($res);
    }

    /**
     * 处理分销商品访问（用户点击分销链接访问商品时调用）
     * @return false|string
     */
    public function handleDistributorVisit()
    {
        // 需要登录
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $distributor_id = $this->params['distributor_id'] ?? 0;
        $goods_id = $this->params['goods_id'] ?? 0;

        if (!$distributor_id || !$goods_id) {
            return $this->response($this->error('', '参数错误'));
        }

        try {
            // 1. 检查分销员是否是 member_level=6
            $distributor = model('member')->getInfo([['member_id', '=', $distributor_id]], 'member_level,fx_level');
            if (!$distributor || $distributor['member_level'] != 6) {
                return $this->response($this->error('', '无效的分销链接'));
            }

            // 2. 检查是否已经有推荐人
            $member = model('member')->getInfo([['member_id', '=', $this->member_id]], 'source_member');

            // 如果该用户已经有推荐人，且推荐人不是当前分销员，不做任何操作
            if ($member['source_member'] != 0 && $member['source_member'] != $distributor_id) {
                return $this->response($this->success('', ''));
            }

            // 3. 如果没有推荐人，设置推荐人
            if ($member['source_member'] == 0) {
                model('member')->update([
                    'source_member' => $distributor_id
                ], [['member_id', '=', $this->member_id]]);
            }

            // 4. 查询或创建商品访问记录
            $member_source_goods_model = new \app\model\member\MemberSourceGoods();
            $record = $member_source_goods_model->getRecord($this->member_id, $goods_id);

            \think\facade\Log::write('handleDistributorVisit - 查询记录: member_id='.$this->member_id.', goods_id='.$goods_id.', record='.json_encode($record).', distributor_fx_level='.$distributor['fx_level']);

            if (!$record) {
                // 首次访问，创建记录并发放首次优惠券
                \think\facade\Log::write('handleDistributorVisit - 首次访问，创建记录');
                $member_source_goods_model->createRecord(
                    $this->member_id,
                    $goods_id,
                    $distributor_id,
                    $distributor['fx_level'],
                    $this->site_id
                );
                \think\facade\Log::write('handleDistributorVisit - 准备发放首次优惠券');
                $coupon_sent = $member_source_goods_model->sendFirstCoupon(
                    $this->member_id,
                    $goods_id,
                    $distributor['fx_level']
                );
                \think\facade\Log::write('handleDistributorVisit - 优惠券发放结果: '.($coupon_sent ? 'true' : 'false'));
                return $this->response($this->success(['coupon_sent' => $coupon_sent], ''));
            } else {
                // 已访问过，检查优惠券是否可用
                \think\facade\Log::write('handleDistributorVisit - 已访问过，检查优惠券是否需要重新发放');
                $need_resend = $member_source_goods_model->checkCouponExpired(
                    $this->member_id,
                    $goods_id,
                    $distributor['fx_level']
                );
                \think\facade\Log::write('handleDistributorVisit - 是否需要重新发放: '.($need_resend ? 'true' : 'false'));
                if ($need_resend) {
                    $coupon_sent = $member_source_goods_model->sendFirstCoupon(
                        $this->member_id,
                        $goods_id,
                        $distributor['fx_level']
                    );
                    \think\facade\Log::write('handleDistributorVisit - 优惠券发放结果: '.($coupon_sent ? 'true' : 'false'));
                    return $this->response($this->success(['coupon_sent' => $coupon_sent], ''));
                }
            }

            return $this->response($this->success('', ''));

        } catch (\Exception $e) {
            return $this->response($this->error('', $e->getMessage()));
        }
    }
}
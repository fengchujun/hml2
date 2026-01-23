<?php

/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 杭州牛之云科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * =========================================================
 */

namespace app\model\order;

use addon\store\model\Config as StoreConfig;
use app\model\BaseModel;
use app\model\goods\Goods;
use app\model\system\Pay;
use Exception;

/**
 * 订单创建(普通订单)
 *
 */
class OrderCreate extends BaseModel
{
    use OrderCreateTool;

    /**
     * @var int|mixed
     */
    public $jielong_id;
    public $jielong_info = [];

    public function __construct()
    {
        $this->modules = [ 'coupon' ];
    }

    public function create()
    {
        //读取之前的标识缓存
        $this->confirm();
        $error_result = $this->checkError();
        if ($error_result !== true) {
            return $error_result;
        }
        model('order')->startTrans();
        try {
            //订单创建数据
            $order_insert_data = $this->getOrderInsertData([ 'promotion' ], 'invert');
            $order_insert_data[ 'store_id' ] = $this->store_id;
            $order_insert_data[ 'create_time' ] = time();
            $order_insert_data[ 'is_enable_refund' ] = 0;
            //订单类型以及状态
            $this->orderType();
            $order_insert_data[ 'order_type' ] = $this->order_type[ 'order_type_id' ];
            $order_insert_data[ 'order_type_name' ] = $this->order_type[ 'order_type_name' ];
            $order_insert_data[ 'order_status_name' ] = $this->order_type[ 'order_status' ][ 'name' ];
            $order_insert_data[ 'order_status_action' ] = json_encode($this->order_type[ 'order_status' ], JSON_UNESCAPED_UNICODE);

            // 计算分销佣金、完成优惠券和关联仓库
            $commission_data = $this->calculateDistributionCommission();
            \think\facade\Log::write('OrderCreate - 计算分销数据结果: '.json_encode($commission_data));
            $order_insert_data[ 'distributor_id' ] = $commission_data['distributor_id'];
            $order_insert_data[ 'commission_amount' ] = $commission_data['commission_amount'];
            $order_insert_data[ 'commission_settled' ] = 0;
            $order_insert_data[ 'warehouse_id' ] = $commission_data['warehouse_id'];
            // 保存完成优惠券数据（JSON格式）
            $order_insert_data[ 'distribution_complete_coupons' ] = !empty($commission_data['complete_coupons'])
                ? json_encode($commission_data['complete_coupons'], JSON_UNESCAPED_UNICODE)
                : '';
            \think\facade\Log::write('OrderCreate - 完成优惠券数据: '.$order_insert_data['distribution_complete_coupons']);

            $this->order_id = model('order')->add($order_insert_data);


            $order_goods_insert_data = [];
            //订单项目表
            foreach ($this->goods_list as $order_goods_v) {
                $order_goods_insert_data[] = $this->getOrderGoodsInsertData($order_goods_v);
            }
            model('order_goods')->addList($order_goods_insert_data);
            //todo 满减送
            $this->createManjian();
            //todo 优惠券(新)
            $this->useCoupon();
            //扣除余额
            $this->useBalance();
            //扣除抵现积分
            $this->usePoint();
            //是否同时开通会员卡
            $this->createMemberCard();
            //使用次卡
            $this->useCard();

            //库存转换处理
            $this->batchGoodsStockTransform();
            //库存处理(卡密商品支付后在扣出库存)//todo  可以再商品中设置扣除库存步骤
            $this->batchDecOrderGoodsStock();
            model('order')->commit();

            //订单创建后事件
            $this->orderCreateAfter();
            //生成整体付费支付单据
            $pay_model = new Pay();
            $pay_model->addPay($this->site_id, $this->out_trade_no, $this->pay_type, $this->order_name, $this->order_name, $this->pay_money, '', 'OrderPayNotify', '', $this->order_id, $this->member_id);
            return $this->success($this->out_trade_no);
        } catch (Exception $e) {
            model('order')->rollback();
            return $this->error([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ], $e->getMessage());
        }
    }

    /**
     * 计算后的进一步计算(不存缓存,每次都是重新计算)
     * @return array
     */
    public function confirm()
    {
        $order_key = $this->param[ 'order_key' ];
        $this->getOrderCache($order_key);
        //初始化地址
        $this->initMemberAddress();
        //初始化门店信息
        $this->initStore();
        //优惠运费计算
        $this->shopOrderCalculate();
        // 会员卡开卡金额
        $this->calculateMemberCardMoney();
        //配送计算
        $this->calculateDelivery();
        //批量校验配送方式
        $this->batchCheckDeliveryType();
        //优惠券活动(采用站点id:coupon_id) **
        $this->couponPromotion();

        // 积分抵现
        if ($this->member_account[ 'point' ] > 0 && addon_is_exit('pointcash', $this->site_id)) {
            $this->getMaxUsablePoint();
            //计算积分
            $this->calculatePoint();
        }
        //计算发票相关
        $this->calculateInvoice();
        //计算余额
        $this->calculateBalcnce();
        $this->pay_money = $this->order_money - $this->balance_money;
        //设置过的商品项信息
        return get_object_vars($this);
    }

    /**
     * 待付款订单
     * @return array
     */
    public function orderPayment()
    {
        $this->calculate();
        $this->getDeliveryData();
        //todo  这儿认为只有自定义表单会用此钩子

        return get_object_vars($this);
    }

    /**
     * 订单计算
     * @return true
     */
    public function calculate()
    {
        $this->initMemberAddress(); //初始化地址
        $this->initMemberAccount(); //初始化会员账户
        $this->getRecommendMemberCard();//查询推荐会员卡
        //初始化门店信息
        $this->initStore();
        //商品列表信息
        $this->getOrderGoodsCalculate();
        //优惠运费计算
        $this->shopOrderCalculate();
        //订单初始项
        event('OrderPayment', [ 'order_object' => $this ]);
        //获取发票相关
        $this->getInovice();
        //todo  统一检测库存(创建订单操作时扣除库存同理)
        // 商品限购判断
        $this->checkLimitPurchase();
        $this->order_key = create_no();
        $this->setOrderCache(get_object_vars($this), $this->order_key);
        return true;
    }

    /**
     * 获取商品的计算信息
     * @return true
     */
    public function getOrderGoodsCalculate()
    {
        $this->getCartGoodsList();

        // 会员卡项抵扣
        $this->getMemberGoodsCardPromotion();
        //满减优惠
        $this->manjianPromotion();
        return true;
    }


    /**
     * 获取购物车商品列表信息
     * @return array|mixed|void
     */
    public function getCartGoodsList()
    {
        $cart_ids = $this->param[ 'cart_ids' ] ?? [];
        $condition = [
            [ 'ngs.site_id', '=', $this->site_id ],
            [ 'ngs.is_delete', '=', 0 ],
            [ 'ngs.goods_state', '=', 1],
            [ 'ng.sale_channel', 'in', $this->sale_channel],
        ];


        //组装商品列表
        $field = 'ngs.sku_id,ngs.is_limit, ngs.limit_type, ngs.sku_name, ngs.member_price, ngs.is_consume_discount,  ngs.discount_config, ngs.discount_method, ngs.sku_no,
            ngs.price, ngs.discount_price, ngs.cost_price, ngs.stock, ngs.weight, ngs.volume, ngs.sku_image,
            ngs.site_id, ngs.goods_state, ngs.is_virtual, ngs.supplier_id,ngs.form_id,
            ngs.is_free_shipping, ngs.shipping_template, ngs.goods_class, ngs.goods_class_name, ngs.goods_id,ns.site_name,ngs.sku_spec_format,ngs.goods_name,ngs.max_buy,ngs.min_buy,ngs.support_trade_type, ngs.is_unify_price,
            ngs.sale_channel, ngs.sale_store, ng.category_id, ng.is_unify_price, ng.virtual_deliver_type, ng.brand_id';
        $alias = 'ngs';
        $join = [
//            [
//                'goods_sku ngs',
//                'ngc.sku_id = ngs.sku_id',
//                'inner'
//            ],
            [
                'site ns',
                'ngs.site_id = ns.site_id',
                'inner'
            ],
            [
                'goods ng',
                'ngs.goods_id = ng.goods_id',
                'inner',
            ],
        ];

        //门店独立售价处理
        if (addon_is_exit('store', $this->site_id)) {
            $store_config = (new StoreConfig())->getStoreBusinessConfig($this->site_id)['data']['value'];
            $join[] = ['store_goods_sku sgs', 'sgs.sku_id = ngs.sku_id and sgs.store_id = '.$this->store_id, 'left'];
            if($store_config['store_business'] == 'store'){
                $field = str_replace('ngs.price', 'IFNULL(IF(ng.is_unify_price = 1,ngs.price,sgs.price), ngs.price) as price', $field);
                $field = str_replace('ngs.discount_price', 'IFNULL(IF(ng.is_unify_price = 1,ngs.discount_price,sgs.price), ngs.discount_price) as discount_price', $field);
            }
            //与门店相关且门店的库存为独立库存则成本价取门店商品的成本价
            $delivery_type = $this->param['delivery']['delivery_type'] ?? 'express';
            if($store_config['store_business'] == 'shop' && $delivery_type == 'express'){
                $delivery_store_id = 0;
            }else{
                $delivery_store_id = $this->param['delivery']['store_id'] ?? 0;
            }
            if(!empty($delivery_store_id)){
                $store_model = new \app\model\store\Store();
                $delivery_store_stock_type = $store_model->getStoreInfo([['store_id', '=', $delivery_store_id]], 'stock_type')['data']['stock_type'];
                if($delivery_store_stock_type == 'store'){
                    $field = str_replace('ngs.cost_price', 'IFNULL(sgs.cost_price, ngs.cost_price) as cost_price', $field);
                }
            }
        }

        $this->jielong_id = $this->param[ 'jielong_id' ] ?? 0;
        if (!empty($cart_ids)) {
            $this->cart_ids = $cart_ids;

            $field .= ',ngc.member_id, ngc.sku_id, ngc.num';
            if ($this->jielong_id > 0) {
                $join[] = [
                    'promotion_jielong_cart ngc',
                    'ngc.sku_id = ngs.sku_id',
                    'inner'
                ];

            } else {
                $field .= ',ngc.form_data';
                $join[] = [
                    'goods_cart ngc',
                    'ngc.sku_id = ngs.sku_id',
                    'inner'
                ];
            }
            $condition[] = [ 'ngc.cart_id', 'in', $cart_ids ];
            $condition[] = [ 'ngc.member_id', '=', $this->member_id ];
        } else {
            $sku_id = $this->param[ 'sku_id' ];
            $num = $this->param[ 'num' ];
            $field .= ',' . $num . ' as num';
            $condition[] = [
                'ngs.sku_id', '=', $sku_id
            ];
        }

        //只有存在收银插件的情况下才会进行吧此项业务(todo  钩子实现)
        $delivery_array = $this->param[ 'delivery' ] ?? [];
        $goods_list = model('goods_sku')->getList($condition, $field, '', $alias, $join);

        if (!empty($goods_list)) {
            $this->goods_list = $goods_list;
            $available_store_ids = [];
            $available_store_is_all = true;
            //计算商品扩展业务相关项
            event('OrderGoodsCalculate', [ 'order_object' => $this ], true);

            // 会员等级商品购买权限控制
            $member_info = model("member")->getInfo([['member_id', '=', $this->member_id]], 'member_level');
            $member_level = !empty($member_info) ? $member_info['member_level'] : 0;

            foreach ($this->goods_list as $k => &$v) {
                // 检查商品购买权限
                if (isset($v['brand_id'])) {
                    // brand_id=1 只有特邀会员(member_level=2)或分销员(member_level=6)可以购买
                    if ($v['brand_id'] == 1 && !in_array($member_level, [2, 6])) {
                        // 检查是否通过分销链接访问过该商品（获得永久权限）
                        $member_source_goods_model = new \app\model\member\MemberSourceGoods();
                        $has_permission = $member_source_goods_model->checkPermission($this->member_id, $v['goods_id']);
                        if (!$has_permission) {
                            $this->setError(1, '商品【' . $v['goods_name'] . '】仅限特邀会员购买');
                        }
                    }
                    // brand_id=2 普通会员不能购买（除非通过分销链接访问过）
                    if ($v['brand_id'] == 2 && $member_level == 1) {
                        // 检查是否通过分销链接访问过该商品
                        $member_source_goods_model = new \app\model\member\MemberSourceGoods();
                        $has_permission = $member_source_goods_model->checkPermission($this->member_id, $v['goods_id']);
                        if (!$has_permission) {
                            $this->setError(1, '商品【' . $v['goods_name'] . '】已售罄');
                        }
                    }
                }

                if($v['sale_store'] != 'all'){
                    $available_store_is_all = false;
                    $sale_store = explode(',', trim($v['sale_store'], ','));
                    if(empty($available_store_ids)){
                        $available_store_ids = $sale_store;
                    }else{
                        $available_store_ids = array_intersect($available_store_ids, $sale_store);
                    }
                }
                $this->site_name = $v[ 'site_name' ];
                $goods_id = $v[ 'goods_id' ];
                if ($v[ 'num' ] < 1) {
                    $this->setError(1, '商品项的购买数量不能小于1');
                }
                $this->is_virtual = $v[ 'is_virtual' ];
                if($v['goods_class'] == \app\dict\goods\GoodsDict::virtual && $v['virtual_deliver_type'] == 'artificial_deliver'){
                    $this->is_virtual_delivery = 1;
                }
                $goods_item = $v;
                $goods_item[ 'delivery' ] = $delivery_array;
                $goods_item[ 'store_id' ] = $this->store_id;
//                //整理创建订单时商品的相关价格库存, 有错误的话还回记录错误['error' => [''error_code' => 1, 'message' => '']]
//                $goods_calculate = event('OrderGoodsCalculate', $goods_item, true);
//                if (!empty($goods_calculate)) {
//                    if ($goods_calculate['code'] < 0) {
//                        return $goods_calculate;
//                    }
//                    $v = $goods_calculate['data'];
//                }
                //todo  要核验  当前门店  当前产品是否已经配置上架
                //todo  未上架未配置  要记录原因,并且不能生成订单
                $price = $this->getGoodsPrice($v)[ 'data' ] ?? 0;

                // 是否存在推荐会员卡
                if (!empty($this->recommend_member_card)) {
                    //todo  当前业务门店不存在,所以这儿的价格不作处理
                    $card_price_info = $this->getMemberCardGoodsPrice($v)[ 'data' ] ?? [];
                    $card_price = $card_price_info[ 'price' ];
                    if ($card_price > 0 && $card_price < $price) {
                        $this->recommend_member_card[ 'discount_money' ] += ($price - $card_price) * $v[ 'num' ];
                        if ($this->recommend_member_card_data[ 'is_open_card' ]) $price = $card_price;//todo  这儿应该把discount_price  也同步一下的
                    }
                }

                $v[ 'form_data' ] = !empty($v[ 'form_data' ]) ? json_decode($v[ 'form_data' ], true) : '';
                $v[ 'price' ] = $price;
                //实现要注意 discount_price 字段只存在显示作用
                if ($this->store_id > 0) {
                    $v[ 'discount_price' ] = $price;
                }
                $v[ 'goods_money' ] = $price * $v[ 'num' ];
                $v[ 'real_goods_money' ] = $v[ 'goods_money' ];
                $v[ 'coupon_money' ] = 0; //优惠券金额
                $v[ 'promotion_money' ] = 0; //优惠金额

//                $this->goods_list[] = $v;
                $order_name = $this->order_name ?? '';
                if ($order_name) {
                    $len = strlen_mb($order_name);
                    if ($len > 200) {
                        $this->order_name = str_sub($order_name, 200);
                    } else {
                        $this->order_name = string_split($order_name, ',', $v[ 'sku_name' ]);
                    }
                } else {
                    $this->order_name = string_split('', ',', $v[ 'sku_name' ]);
                }
                $this->goods_num += $v[ 'num' ];
                $this->goods_money += $v[ 'goods_money' ];
                //以;隔开的商品项
                $goods_list_str = $this->goods_list_str ?? '';
                if ($goods_list_str) {
                    $this->goods_list_str = $goods_list_str . ';' . $v[ 'sku_id' ] . ':' . $v[ 'num' ];
                } else {
                    $this->goods_list_str = $v[ 'sku_id' ] . ':' . $v[ 'num' ];
                }

                // 商品限购处理
                if (isset($this->limit_purchase[ 'goods_' . $goods_id ])) {
                    $this->limit_purchase[ 'goods_' . $goods_id ][ 'num' ] += $v[ 'num' ];
                } else {
                    $this->limit_purchase[ 'goods_' . $goods_id ] = [
                        'goods_id' => $v[ 'goods_id' ],
                        'goods_name' => $v[ 'sku_name' ],
                        'num' => $v[ 'num' ],
                        'is_limit' => $v[ 'is_limit' ],
                        'limit_type' => $v[ 'limit_type' ],
                        'max_buy' => $v[ 'max_buy' ],
                        'min_buy' => $v[ 'min_buy' ]
                    ];
                }
                //校验商品和配送方式
//                $this->checkDeliveryType($v);
                //有错误也会导致商品无法购买
                $item_error = $v[ 'error' ] ?? [];
                if (!empty($item_error)) {
                    $this->setError(1, $item_error[ 'message' ]);
                }
            }
            if($available_store_is_all === false){
                $this->available_store_ids = join(',', $available_store_ids);
            }
        }else{
            $this->setError(1, '您要购买的商品已删除或已下架');
        }
        return true;
    }


    /**
     * 获取店铺订单计算
     * @return true
     */
    public function shopOrderCalculate()
    {
        //满额包邮插件
        $this->freeShippingCalculate();
        //会员等级包邮权益
        $this->memberLevelCalculate();
        //重新计算订单总额
        $this->getOrderMoney();
        //理论上是多余的操作
        if ($this->order_money < 0) {
            $this->order_money = 0;
        }
        //总结计算
        $this->pay_money = $this->order_money;

        return true;
    }

    /**
     * 计算分销佣金、完成优惠券和关联仓库
     * @return array
     */
    public function calculateDistributionCommission()
    {
        $total_commission = 0;
        $distributor_id = 0;
        $warehouse_id = 0;
        $complete_coupons = []; // 存储商品ID和对应的完成优惠券ID

        try {
            \think\facade\Log::write('calculateDistributionCommission - 开始计算，member_id='.$this->member_id.', goods_list='.json_encode($this->goods_list));

            $member_source_goods_model = new \app\model\member\MemberSourceGoods();
            foreach ($this->goods_list as $goods) {
                \think\facade\Log::write('calculateDistributionCommission - 检查商品: goods_id='.$goods['goods_id']);

                // 检查该商品是否通过分销员访问
                $record = $member_source_goods_model->getRecord($this->member_id, $goods['goods_id']);
                \think\facade\Log::write('calculateDistributionCommission - 分销记录: '.json_encode($record));

                if ($record) {
                    $distributor_id = $record['distributor_id'];
                    $fx_level = $record['distributor_level'];

                    // 获取商品的佣金和完成优惠券配置
                    $commission_field = 'fx_level' . $fx_level . '_commission';
                    $complete_coupon_field = 'fx_level' . $fx_level . '_complete_coupon';
                    \think\facade\Log::write('calculateDistributionCommission - 查询字段: commission_field='.$commission_field.', complete_coupon_field='.$complete_coupon_field);

                    $goods_info = model('goods')->getInfo(
                        [['goods_id', '=', $goods['goods_id']]],
                        $commission_field . ',' . $complete_coupon_field
                    );
                    \think\facade\Log::write('calculateDistributionCommission - 商品信息: '.json_encode($goods_info));

                    if ($goods_info) {
                        // 计算佣金
                        if (isset($goods_info[$commission_field]) && $goods_info[$commission_field] > 0) {
                            // 佣金 = 单品佣金 × 数量
                            $total_commission += $goods_info[$commission_field] * $goods['num'];
                            \think\facade\Log::write('calculateDistributionCommission - 累加佣金: '.$goods_info[$commission_field].' × '.$goods['num'].' = '.($goods_info[$commission_field] * $goods['num']));
                        }

                        // 记录完成优惠券
                        if (isset($goods_info[$complete_coupon_field]) && $goods_info[$complete_coupon_field] > 0) {
                            $complete_coupons[$goods['goods_id']] = $goods_info[$complete_coupon_field];
                            \think\facade\Log::write('calculateDistributionCommission - 记录完成优惠券: goods_id='.$goods['goods_id'].', coupon_type_id='.$goods_info[$complete_coupon_field]);
                        } else {
                            \think\facade\Log::write('calculateDistributionCommission - 未配置完成优惠券或为0: complete_coupon_field='.$complete_coupon_field.', value='.($goods_info[$complete_coupon_field] ?? 'null'));
                        }
                    }
                } else {
                    \think\facade\Log::write('calculateDistributionCommission - 无分销记录，跳过此商品');
                }
            }

            // 如果订单来自分销员推荐，获取分销员的仓库ID
            if ($distributor_id > 0) {
                $distributor = model('member')->getInfo([['member_id', '=', $distributor_id]], 'warehouse_id');
                $warehouse_id = $distributor['warehouse_id'] ?? 0;
            }

            \think\facade\Log::write('calculateDistributionCommission - 最终结果: distributor_id='.$distributor_id.', commission='.$total_commission.', warehouse_id='.$warehouse_id.', complete_coupons='.json_encode($complete_coupons));

        } catch (\Exception $e) {
            \think\facade\Log::error('计算分销佣金和完成优惠券失败: ' . $e->getMessage() . ', trace: ' . $e->getTraceAsString());
        }

        return [
            'distributor_id' => $distributor_id,
            'commission_amount' => $total_commission,
            'warehouse_id' => $warehouse_id,
            'complete_coupons' => $complete_coupons // 返回完成优惠券数据
        ];
    }


}
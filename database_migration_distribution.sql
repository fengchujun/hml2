-- =============================================
-- 分销功能数据库迁移脚本
-- 创建时间: 2026-01-21
-- 说明: 添加分销员功能、仓库管理、佣金结算
-- =============================================

-- 1. 修改会员表，增加分销等级和仓库ID
ALTER TABLE `hml_member`
ADD COLUMN `fx_level` tinyint DEFAULT 0 COMMENT '分销等级0/1/2/3，0为非分销' AFTER `member_level`,
ADD COLUMN `warehouse_id` int DEFAULT 0 COMMENT '所属仓库ID（仅分销员）' AFTER `fx_level`;

-- 2. 创建会员分销商品访问记录表
CREATE TABLE IF NOT EXISTS `hml_member_source_goods` (
    `id` int PRIMARY KEY AUTO_INCREMENT,
    `site_id` int NOT NULL DEFAULT 0,
    `member_id` int NOT NULL COMMENT '访问商品的会员ID',
    `goods_id` int NOT NULL COMMENT '商品ID',
    `distributor_id` int NOT NULL COMMENT '分销员ID（谁分享的链接）',
    `distributor_level` tinyint DEFAULT 0 COMMENT '分销员当时的等级',
    `first_visit_time` int DEFAULT 0 COMMENT '首次访问时间',
    `last_visit_time` int DEFAULT 0 COMMENT '最后访问时间',
    `first_coupon_last_time` int DEFAULT 0 COMMENT '最后一次发放首次优惠券的时间',
    `create_time` int DEFAULT 0,
    KEY `idx_member_goods` (`member_id`, `goods_id`),
    KEY `idx_distributor` (`distributor_id`),
    KEY `idx_site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='会员分销商品访问记录表';

-- 3. 修改商品表，增加分销配置字段（9个字段）
ALTER TABLE `hml_goods`
ADD COLUMN `fx_level1_first_coupon` int DEFAULT 0 COMMENT '1级分销首次优惠券类型ID' AFTER `brand_id`,
ADD COLUMN `fx_level1_complete_coupon` int DEFAULT 0 COMMENT '1级分销完成优惠券类型ID' AFTER `fx_level1_first_coupon`,
ADD COLUMN `fx_level1_commission` decimal(10,2) DEFAULT 0.00 COMMENT '1级分销佣金' AFTER `fx_level1_complete_coupon`,

ADD COLUMN `fx_level2_first_coupon` int DEFAULT 0 COMMENT '2级分销首次优惠券类型ID' AFTER `fx_level1_commission`,
ADD COLUMN `fx_level2_complete_coupon` int DEFAULT 0 COMMENT '2级分销完成优惠券类型ID' AFTER `fx_level2_first_coupon`,
ADD COLUMN `fx_level2_commission` decimal(10,2) DEFAULT 0.00 COMMENT '2级分销佣金' AFTER `fx_level2_complete_coupon`,

ADD COLUMN `fx_level3_first_coupon` int DEFAULT 0 COMMENT '3级分销首次优惠券类型ID' AFTER `fx_level2_commission`,
ADD COLUMN `fx_level3_complete_coupon` int DEFAULT 0 COMMENT '3级分销完成优惠券类型ID' AFTER `fx_level3_first_coupon`,
ADD COLUMN `fx_level3_commission` decimal(10,2) DEFAULT 0.00 COMMENT '3级分销佣金' AFTER `fx_level3_complete_coupon`;

-- 4. 修改订单表，增加分销和仓库字段
ALTER TABLE `hml_order`
ADD COLUMN `distributor_id` int DEFAULT 0 COMMENT '分销员ID' AFTER `member_id`,
ADD COLUMN `commission_amount` decimal(10,2) DEFAULT 0.00 COMMENT '佣金金额' AFTER `distributor_id`,
ADD COLUMN `commission_settled` tinyint DEFAULT 0 COMMENT '是否已结算 0未结算 1已结算' AFTER `commission_amount`,
ADD COLUMN `warehouse_id` int DEFAULT 0 COMMENT '发货仓库ID' AFTER `commission_settled`;

-- 5. 修改管理员表，增加仓库ID
ALTER TABLE `hml_user`
ADD COLUMN `warehouse_id` int DEFAULT 0 COMMENT '管理员所属仓库ID' AFTER `group_name`;

-- 6. 创建仓库表
CREATE TABLE IF NOT EXISTS `hml_warehouse` (
    `warehouse_id` int PRIMARY KEY AUTO_INCREMENT,
    `site_id` int NOT NULL DEFAULT 0,
    `warehouse_name` varchar(50) NOT NULL COMMENT '仓库名称',
    `warehouse_code` varchar(50) DEFAULT '' COMMENT '仓库编码',
    `contact_name` varchar(50) DEFAULT '' COMMENT '联系人',
    `contact_phone` varchar(20) DEFAULT '' COMMENT '联系电话',
    `address` varchar(255) DEFAULT '' COMMENT '仓库地址',
    `remark` varchar(500) DEFAULT '' COMMENT '备注',
    `status` tinyint DEFAULT 1 COMMENT '状态 1启用 0禁用',
    `create_time` int DEFAULT 0,
    `update_time` int DEFAULT 0,
    KEY `idx_site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='仓库表';

-- =============================================
-- 迁移完成
-- =============================================

-- 优惠券类型表新增 是否可叠加 字段
-- 仅 type=reward 且 is_limit=0(无门槛) 时可设置为叠加使用
ALTER TABLE `hml_promotion_coupon_type` ADD COLUMN `is_stackable` tinyint NOT NULL DEFAULT 0 COMMENT '是否可叠加使用 0-否 1-是（仅满减无门槛券可设置）' AFTER `is_forbid_preference`;

-- 优惠券（已领取）表新增 is_stackable 字段，领取时从 coupon_type 同步
ALTER TABLE `hml_promotion_coupon` ADD COLUMN `is_stackable` tinyint NOT NULL DEFAULT 0 COMMENT '是否可叠加使用 0-否 1-是' AFTER `is_forbid_preference`;

-- 订单表新增 coupon_ids 字段，支持叠加优惠券（存储逗号分隔的多个优惠券ID）
ALTER TABLE `hml_order` ADD COLUMN `coupon_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '使用的优惠券ID列表（逗号分隔，支持叠加）' AFTER `coupon_id`;

-- 优惠券类型表新增 是否可叠加 字段
-- 仅 type=reward 且 is_limit=0(无门槛) 时可设置为叠加使用
ALTER TABLE `hml_promotion_coupon_type` ADD COLUMN `is_stackable` tinyint NOT NULL DEFAULT 0 COMMENT '是否可叠加使用 0-否 1-是（仅满减无门槛券可设置）' AFTER `is_forbid_preference`;

CREATE TABLE IF NOT EXISTS `hml_source_member_coupon_config` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `site_id` int NOT NULL DEFAULT '1' COMMENT '站点ID',
  `coupon_type_id` int NOT NULL DEFAULT '0' COMMENT '优惠券类型ID',
  `num` int NOT NULL DEFAULT '1' COMMENT '每次发放数量',
  `is_enabled` tinyint NOT NULL DEFAULT '1' COMMENT '是否启用 0-否 1-是',
  `create_time` int NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `IDX_site_id` (`site_id`),
  KEY `IDX_coupon_type_id` (`coupon_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='推荐注册赠送优惠券配置表';

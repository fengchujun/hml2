-- 为会员表添加会员编号字段
ALTER TABLE `ns_member`
ADD COLUMN `member_code` VARCHAR(20) DEFAULT NULL COMMENT '会员编号' AFTER `member_id`,
ADD UNIQUE INDEX `idx_member_code` (`member_code`);

-- 创建会员编号序号管理表
CREATE TABLE IF NOT EXISTS `ns_member_code_sequence` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `area_code` VARCHAR(10) NOT NULL COMMENT '区号（如755, 10, 86）',
  `level_flag` TINYINT(1) NOT NULL COMMENT '等级标识（0=普通会员，8=特邀会员）',
  `current_seq` INT(11) NOT NULL DEFAULT 0 COMMENT '当前自增序号',
  `create_time` INT(11) NOT NULL COMMENT '创建时间',
  `update_time` INT(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_area_level` (`area_code`, `level_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员编号序号管理表';

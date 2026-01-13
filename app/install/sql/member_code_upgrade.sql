-- 会员编号功能数据库升级SQL
-- 创建时间：2026-01-13

-- 1. 修改会员表，添加区号和会员类型字段
ALTER TABLE `ns_member`
ADD COLUMN `area_code` varchar(10) NOT NULL DEFAULT '' COMMENT '手机号区号（去0后）' AFTER `member_code`,
ADD COLUMN `member_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '会员类型 0普通 8特邀' AFTER `area_code`;

-- 2. 创建会员编号自增序列表
CREATE TABLE IF NOT EXISTS `ns_member_code_sequence` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `site_id` int(11) NOT NULL DEFAULT 0 COMMENT '站点ID',
  `area_code` varchar(10) NOT NULL DEFAULT '' COMMENT '区号（去0后）',
  `member_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '会员类型 0普通 8特邀',
  `current_seq` int(11) NOT NULL DEFAULT 0 COMMENT '当前自增序号',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`site_id`, `area_code`, `member_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员编号自增序列表';

-- 3. 创建会员编号变更历史表
CREATE TABLE IF NOT EXISTS `ns_member_code_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `site_id` int(11) NOT NULL DEFAULT 0 COMMENT '站点ID',
  `old_member_code` varchar(50) DEFAULT NULL COMMENT '旧会员编号',
  `new_member_code` varchar(50) DEFAULT NULL COMMENT '新会员编号',
  `old_member_type` tinyint(1) DEFAULT NULL COMMENT '旧会员类型',
  `new_member_type` tinyint(1) DEFAULT NULL COMMENT '新会员类型',
  `change_reason` varchar(100) DEFAULT NULL COMMENT '变更原因（升级/降级）',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员编号变更历史表';

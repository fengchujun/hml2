-- =====================================================
-- PC Web English Version - Database Field Updates
-- =====================================================

-- 1. hml_goods_category: Add English category name field
ALTER TABLE `hml_goods_category`
ADD COLUMN `category_name_en` varchar(50) NOT NULL DEFAULT '' COMMENT 'Category name (English)' AFTER `category_name`;

-- 2. hml_goods: Add English goods name and content fields
ALTER TABLE `hml_goods`
ADD COLUMN `goods_name_en` varchar(255) NOT NULL DEFAULT '' COMMENT 'Goods name (English)' AFTER `goods_name`,
ADD COLUMN `goods_content_en` text COMMENT 'Goods content (English)' AFTER `goods_content`;

-- 3. hml_article: Add English title and content fields
ALTER TABLE `hml_article`
ADD COLUMN `article_title_en` varchar(255) NOT NULL DEFAULT '' COMMENT 'Article title (English)' AFTER `article_title`,
ADD COLUMN `article_content_en` text COMMENT 'Article content (English)' AFTER `article_content`;

-- =====================================================
-- Sample data updates (optional)
-- =====================================================

-- Update category English names
UPDATE `hml_goods_category` SET `category_name_en` = 'Red Tea' WHERE `category_id` = 1;
UPDATE `hml_goods_category` SET `category_name_en` = 'Ripe Pu-erh' WHERE `category_id` = 2;
UPDATE `hml_goods_category` SET `category_name_en` = 'White Tea' WHERE `category_id` = 3;
UPDATE `hml_goods_category` SET `category_name_en` = 'Raw Pu-erh' WHERE `category_id` = 4;
UPDATE `hml_goods_category` SET `category_name_en` = 'Tea Sets' WHERE `category_id` = 5;
UPDATE `hml_goods_category` SET `category_name_en` = 'Teaware' WHERE `category_id` = 6;
UPDATE `hml_goods_category` SET `category_name_en` = 'Tea Space' WHERE `category_id` = 8;

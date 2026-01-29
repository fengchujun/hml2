-- =====================================================
-- SQL: Add English fields for i18n support
-- Date: 2026-01-29
-- =====================================================

-- 1. hml_goods_category: Add category_name_en
ALTER TABLE `hml_goods_category`
ADD COLUMN `category_name_en` VARCHAR(50) DEFAULT '' COMMENT 'Category name (English)' AFTER `category_name`;

-- 2. hml_goods: Add goods_name_en, goods_content_en
ALTER TABLE `hml_goods`
ADD COLUMN `goods_name_en` VARCHAR(255) DEFAULT '' COMMENT 'Product name (English)' AFTER `goods_name`,
ADD COLUMN `goods_content_en` TEXT COMMENT 'Product content (English)' AFTER `goods_content`;

-- 3. hml_goods_sku: Add sku_name_en, goods_name_en, goods_content_en
ALTER TABLE `hml_goods_sku`
ADD COLUMN `sku_name_en` VARCHAR(500) DEFAULT '' COMMENT 'SKU name (English)' AFTER `sku_name`,
ADD COLUMN `goods_name_en` VARCHAR(255) DEFAULT '' COMMENT 'Product name (English)' AFTER `goods_name`,
ADD COLUMN `goods_content_en` TEXT COMMENT 'Product content (English)' AFTER `goods_content`;

-- 4. hml_article: Add article_title_en, article_content_en
ALTER TABLE `hml_article`
ADD COLUMN `article_title_en` VARCHAR(255) DEFAULT '' COMMENT 'Article title (English)' AFTER `article_title`,
ADD COLUMN `article_content_en` TEXT COMMENT 'Article content (English)' AFTER `article_content`;

-- =====================================================
-- Note: Run this SQL on your database to add the English fields
-- =====================================================

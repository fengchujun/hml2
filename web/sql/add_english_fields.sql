-- =====================================================
-- SQL: Add English fields for i18n support
-- Date: 2026-01-29
-- =====================================================

-- 1. hml_goods_category: Add category_name_en, image_en
ALTER TABLE `hml_goods_category`
ADD COLUMN `category_name_en` VARCHAR(50) DEFAULT '' COMMENT 'Category name (English)' AFTER `category_name`,
ADD COLUMN `image_en` VARCHAR(255) DEFAULT '' COMMENT 'Category image (English)' AFTER `image`;

-- 2. hml_goods: Add goods_name_en, goods_image_en, goods_content_en
ALTER TABLE `hml_goods`
ADD COLUMN `goods_name_en` VARCHAR(255) DEFAULT '' COMMENT 'Product name (English)' AFTER `goods_name`,
ADD COLUMN `goods_image_en` VARCHAR(2000) DEFAULT '' COMMENT 'Product image (English)' AFTER `goods_image`,
ADD COLUMN `goods_content_en` TEXT COMMENT 'Product content (English)' AFTER `goods_content`;

-- 3. hml_goods_sku: Add sku_name_en, goods_name_en, goods_content_en
ALTER TABLE `hml_goods_sku`
ADD COLUMN `sku_name_en` VARCHAR(500) DEFAULT '' COMMENT 'SKU name (English)' AFTER `sku_name`,
ADD COLUMN `goods_name_en` VARCHAR(255) DEFAULT '' COMMENT 'Product name (English)' AFTER `goods_name`,
ADD COLUMN `goods_content_en` TEXT COMMENT 'Product content (English)' AFTER `goods_content`;

-- 4. hml_article: Add article_title_en, article_content_en, cover_img_en
ALTER TABLE `hml_article`
ADD COLUMN `article_title_en` VARCHAR(255) DEFAULT '' COMMENT 'Article title (English)' AFTER `article_title`,
ADD COLUMN `article_content_en` TEXT COMMENT 'Article content (English)' AFTER `article_content`,
ADD COLUMN `cover_img_en` VARCHAR(2000) DEFAULT '' COMMENT 'Cover image (English)' AFTER `cover_img`;

-- 5. hml_notes: Add note_title_en, cover_img_en, note_content_en
ALTER TABLE `hml_notes`
ADD COLUMN `note_title_en` VARCHAR(255) DEFAULT '' COMMENT 'Note title (English)' AFTER `note_title`,
ADD COLUMN `note_content_en` TEXT COMMENT 'Note content (English)' AFTER `note_content`,
ADD COLUMN `cover_img_en` VARCHAR(2000) DEFAULT '' COMMENT 'Cover image (English)' AFTER `cover_img`;

-- =====================================================
-- IMPORTANT: After running this SQL, you MUST clear ThinkPHP's schema cache:
-- Delete all files in: runtime/schema/
-- Otherwise ThinkPHP will not recognize the new fields (fields_cache=true)
-- =====================================================

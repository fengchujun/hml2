<?php
/**
 * Initialization file
 * Include this file at the beginning of each page
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/config.php';

// Get current language
$lang = getCurrentLang();

// Load language pack
$langFile = __DIR__ . '/lang/' . $lang . '.php';
if (file_exists($langFile)) {
    $L = require $langFile;
} else {
    $L = require __DIR__ . '/lang/zh.php';
}

/**
 * Translation function
 */
function __($key, $default = '') {
    global $L;
    return $L[$key] ?? ($default ?: $key);
}

/**
 * Echo translation
 */
function _e($key, $default = '') {
    echo __($key, $default);
}

/**
 * Check if current language is English
 */
function isEnglish() {
    global $lang;
    return $lang === 'en';
}

/**
 * Check if current language is Chinese
 */
function isChinese() {
    global $lang;
    return $lang === 'zh';
}

/**
 * Get site categories
 */
function getCategories($siteId = 1) {
    $pdo = getDB();
    $lang = getCurrentLang();

    $sql = "SELECT * FROM " . DB_PREFIX . "goods_category
            WHERE site_id = ? AND is_show = 0 AND pid = 0
            ORDER BY sort DESC, category_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$siteId]);

    $categories = $stmt->fetchAll();

    // Process language fields
    foreach ($categories as &$cat) {
        $cat['name'] = getLangValue($cat, 'category_name', $lang);
    }

    return $categories;
}

/**
 * Get products
 */
function getProducts($categoryId = 0, $limit = 12, $page = 1, $siteId = 1) {
    $pdo = getDB();
    $lang = getCurrentLang();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM " . DB_PREFIX . "goods
            WHERE site_id = ? AND is_delete = 0 AND goods_state = 1";
    $params = [$siteId];

    if ($categoryId > 0) {
        $sql .= " AND FIND_IN_SET(?, category_id)";
        $params[] = $categoryId;
    }

    $sql .= " ORDER BY sort DESC, goods_id DESC LIMIT " . (int)$offset . ", " . (int)$limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $products = $stmt->fetchAll();

    // Process language fields
    foreach ($products as &$prod) {
        $prod['name'] = getLangValue($prod, 'goods_name', $lang);
        $prod['content'] = getLangValue($prod, 'goods_content', $lang);
    }

    return $products;
}

/**
 * Get single product
 */
function getProduct($goodsId) {
    $pdo = getDB();
    $lang = getCurrentLang();

    $sql = "SELECT * FROM " . DB_PREFIX . "goods WHERE goods_id = ? AND is_delete = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$goodsId]);

    $product = $stmt->fetch();

    if ($product) {
        $product['name'] = getLangValue($product, 'goods_name', $lang);
        $product['content'] = getLangValue($product, 'goods_content', $lang);
    }

    return $product;
}

/**
 * Get articles
 */
function getArticles($categoryId = 0, $limit = 10, $page = 1, $siteId = 1) {
    $pdo = getDB();
    $lang = getCurrentLang();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM " . DB_PREFIX . "article
            WHERE site_id = ? AND status = 1";
    $params = [$siteId];

    if ($categoryId > 0) {
        $sql .= " AND category_id = ?";
        $params[] = $categoryId;
    }

    $sql .= " ORDER BY sort DESC, article_id DESC LIMIT " . (int)$offset . ", " . (int)$limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $articles = $stmt->fetchAll();

    // Process language fields
    foreach ($articles as &$art) {
        $art['title'] = getLangValue($art, 'article_title', $lang);
        $art['content'] = getLangValue($art, 'article_content', $lang);
    }

    return $articles;
}

/**
 * Get single article
 */
function getArticle($articleId) {
    $pdo = getDB();
    $lang = getCurrentLang();

    $sql = "SELECT * FROM " . DB_PREFIX . "article WHERE article_id = ? AND status = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$articleId]);

    $article = $stmt->fetch();

    if ($article) {
        $article['title'] = getLangValue($article, 'article_title', $lang);
        $article['content'] = getLangValue($article, 'article_content', $lang);

        // Increase view count
        $pdo->prepare("UPDATE " . DB_PREFIX . "article SET read_num = read_num + 1 WHERE article_id = ?")
            ->execute([$articleId]);
    }

    return $article;
}

/**
 * Format date
 */
function formatDate($timestamp, $format = null) {
    global $lang;
    if ($format === null) {
        $format = ($lang === 'en') ? 'M d, Y' : 'Y-m-d';
    }
    return date($format, $timestamp);
}

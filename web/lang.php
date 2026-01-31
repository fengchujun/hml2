<?php
/**
 * Language detection and translation helper
 * Include this file at the top of each page
 */

// Detect and set language
function detectLanguage() {
    // 1. Check URL parameter
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh', 'en'])) {
        $lang = $_GET['lang'];
        // Save to cookie for 30 days
        setcookie('site_lang', $lang, time() + 30 * 24 * 60 * 60, '/');
        return $lang;
    }

    // 2. Check cookie
    if (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['zh', 'en'])) {
        return $_COOKIE['site_lang'];
    }

    // 3. Check browser language
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if ($browserLang === 'en') {
            return 'en';
        }
    }

    // 4. Default to Chinese
    return 'zh';
}

// Initialize language
$current_lang = detectLanguage();
$is_english = ($current_lang === 'en');

// Generate language switch URL
function getLangSwitchUrl($targetLang) {
    $url = $_SERVER['REQUEST_URI'];
    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'] ?? '';
    $query = [];

    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $query);
    }

    $query['lang'] = $targetLang;
    return $path . '?' . http_build_query($query);
}

// Translation dictionary
$translations = [
    // Navigation
    'nav_home' => ['zh' => '首页', 'en' => 'Home'],
    'nav_enterprise' => ['zh' => '企业理念', 'en' => 'Philosophy'],
    'nav_quality' => ['zh' => '品质观念', 'en' => 'Quality'],
    'nav_teahouse' => ['zh' => '会客厅', 'en' => 'Tea Room'],
    'nav_products' => ['zh' => '产品中心', 'en' => 'Products'],
    'nav_news' => ['zh' => '企业资讯', 'en' => 'News'],
    'nav_contact' => ['zh' => '联系我们', 'en' => 'Contact'],

    // Index page
    'hero_title' => ['zh' => '茶祖千万年·濮茶华木兰', 'en' => 'Ancient Tea Heritage · Pu Tea Hua Mulan'],
    'hero_subtitle' => ['zh' => '传承千年茶文化 品味匠心茶之道', 'en' => 'Inheriting millennium tea culture, savoring the artisan way of tea'],
    'btn_order' => ['zh' => '立即选购', 'en' => 'Shop Now'],
    'btn_reserve' => ['zh' => '预约会客厅', 'en' => 'Reserve Tea Room'],
    'section_intro' => ['zh' => '企业介绍', 'en' => 'About Us'],
    'section_teahouse' => ['zh' => '会客厅', 'en' => 'Tea Room'],
    'section_products' => ['zh' => '产品中心', 'en' => 'Products'],

    // Products page
    'product_category' => ['zh' => '产品分类', 'en' => 'Categories'],
    'view_detail' => ['zh' => '查看详情', 'en' => 'View Details'],
    'no_products' => ['zh' => '该分类暂无商品', 'en' => 'No products in this category'],
    'no_category' => ['zh' => '暂无产品分类', 'en' => 'No categories available'],
    'coming_soon' => ['zh' => '敬请期待', 'en' => 'Coming Soon'],

    // Product detail page
    'breadcrumb_home' => ['zh' => '首页', 'en' => 'Home'],
    'breadcrumb_products' => ['zh' => '产品中心', 'en' => 'Products'],
    'product_detail' => ['zh' => '产品详情', 'en' => 'Product Details'],
    'price' => ['zh' => '价格', 'en' => 'Price'],
    'spec' => ['zh' => '产品规格', 'en' => 'Specifications'],
    'unit' => ['zh' => '计量单位', 'en' => 'Unit'],
    'stock' => ['zh' => '库存', 'en' => 'Stock'],
    'sales' => ['zh' => '销量', 'en' => 'Sales'],
    'pieces' => ['zh' => '件', 'en' => 'pcs'],
    'buy_now' => ['zh' => '立即购买', 'en' => 'Buy Now'],
    'consult' => ['zh' => '咨询客服', 'en' => 'Contact Us'],
    'product_not_found' => ['zh' => '产品不存在', 'en' => 'Product Not Found'],
    'product_not_found_desc' => ['zh' => '抱歉,未找到该产品信息', 'en' => 'Sorry, the product information was not found'],
    'back_to_list' => ['zh' => '返回产品列表', 'en' => 'Back to Products'],

    // News page
    'news_title' => ['zh' => '企业资讯', 'en' => 'Company News'],
    'news_subtitle' => ['zh' => '了解茶祖源·木兰阁最新动态', 'en' => 'Stay updated with our latest news'],
    'no_news' => ['zh' => '暂无资讯', 'en' => 'No News'],
    'no_news_desc' => ['zh' => '敬请期待更多企业资讯', 'en' => 'More news coming soon'],
    'prev_page' => ['zh' => '上一页', 'en' => 'Previous'],
    'next_page' => ['zh' => '下一页', 'en' => 'Next'],

    // Concept detail page
    'core_concept' => ['zh' => '核心理念', 'en' => 'Core Values'],
    'brand_intro' => ['zh' => '品牌介绍', 'en' => 'Brand Story'],
    'product_manual' => ['zh' => '产品手册', 'en' => 'Product Manual'],
    'scan_more' => ['zh' => '了解更多详情,请扫码进入小程序', 'en' => 'Scan QR code to learn more in our mini program'],
    'view_more' => ['zh' => '查看详情', 'en' => 'View Details'],
    'loading' => ['zh' => '内容加载中...', 'en' => 'Loading...'],
    'please_wait' => ['zh' => '请稍候', 'en' => 'Please wait'],

    // Teahouse page
    'contact_phone' => ['zh' => '联系电话', 'en' => 'Phone'],
    'reservation_service' => ['zh' => '预约服务', 'en' => 'Reservation'],
    'support_reservation' => ['zh' => '支持线上预约,扫码进入小程序即可预约', 'en' => 'Online reservation available, scan QR code to book'],
    'reserve_teahouse' => ['zh' => '预约', 'en' => 'Reserve'],
    'reserve_desc' => ['zh' => '扫码进入小程序,选择您方便的时间,我们将为您准备最佳的品茗体验', 'en' => 'Scan QR code to select your preferred time, we will prepare the best tea experience for you'],
    'book_now' => ['zh' => '立即预约', 'en' => 'Book Now'],
    'no_teahouse' => ['zh' => '暂无会客厅信息', 'en' => 'No tea room information'],

    // Footer
    'footer_desc' => ['zh' => '致力于为您提供高品质的茶叶产品和专业的茶文化服务', 'en' => 'Dedicated to providing high-quality tea products and professional tea culture services'],
    'scan_miniprogram' => ['zh' => '扫码进入小程序', 'en' => 'Scan to open mini program'],
    'quick_nav' => ['zh' => '快速导航', 'en' => 'Quick Links'],
    'contact_info' => ['zh' => '联系方式', 'en' => 'Contact'],
    'hotline' => ['zh' => '客服热线', 'en' => 'Hotline'],
    'business_hours' => ['zh' => '营业时间', 'en' => 'Hours'],
    'business_hours_value' => ['zh' => '周一至周日 9:00-20:30', 'en' => 'Mon-Sun 9:00-20:30'],
    'email' => ['zh' => '联系邮箱', 'en' => 'Email'],
    'address' => ['zh' => '公司地址', 'en' => 'Address'],
    'hq_address' => ['zh' => '总部地址', 'en' => 'Headquarters'],
    'hq_address_value' => ['zh' => '深圳市龙岗区天安数码城3栋B座1002', 'en' => 'Room 1002, Building 3B, Tianan Cyber Park, Longgang, Shenzhen'],

    // QR Modal
    'qr_order_title' => ['zh' => '扫码立即选购', 'en' => 'Scan to Shop'],
    'qr_order_hint' => ['zh' => '扫描二维码进入小程序,选购您喜爱的茶品', 'en' => 'Scan QR code to enter mini program and shop your favorite tea'],
    'qr_reserve_title' => ['zh' => '扫码预约会客厅', 'en' => 'Scan to Reserve'],
    'qr_reserve_hint' => ['zh' => '扫描二维码进入小程序,预约专属会客空间', 'en' => 'Scan QR code to reserve your private tea room'],
    'qr_contact_title' => ['zh' => '扫码联系我们', 'en' => 'Scan to Contact'],
    'qr_contact_hint' => ['zh' => '扫描二维码进入小程序,获取联系方式', 'en' => 'Scan QR code to get contact information'],
    'qr_product_title' => ['zh' => '扫码查看产品详情', 'en' => 'Scan for Details'],
    'qr_product_hint' => ['zh' => '扫描二维码进入小程序,了解产品详情并下单', 'en' => 'Scan QR code to view product details and order'],
    'qr_hint' => ['zh' => '请使用微信扫描二维码', 'en' => 'Please scan with WeChat'],
];

// Translation function
function __($key, $lang = null) {
    global $translations, $current_lang;
    $lang = $lang ?? $current_lang;

    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    }

    // Fallback to Chinese if translation not found
    if (isset($translations[$key]['zh'])) {
        return $translations[$key]['zh'];
    }

    return $key;
}

// Get localized field from data array
function getLocalizedField($data, $fieldName, $lang = null) {
    global $current_lang;
    $lang = $lang ?? $current_lang;

    if ($lang === 'en') {
        $enField = $fieldName . '_en';
        if (!empty($data[$enField])) {
            return $data[$enField];
        }
    }

    return $data[$fieldName] ?? '';
}

// Get site name based on language
function getSiteName() {
    global $is_english;
    return $is_english ? SITE_NAME_EN : SITE_NAME;
}

// Get site slogan based on language
function getSiteSlogan() {
    global $is_english;
    return $is_english ? SITE_SLOGAN_EN : SITE_SLOGAN;
}

// Get site copyright based on language
function getSiteCopyright() {
    global $is_english;
    return $is_english ? SITE_COPYRIGHT_EN : SITE_COPYRIGHT;
}

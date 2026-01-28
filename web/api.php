<?php
/**
 * API调用函数
 */

require_once 'config.php';

/**
 * 发送POST请求
 */
function apiPost($url, $data = []) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['code' => -1, 'message' => $error, 'data' => null];
    }

    return json_decode($response, true);
}

/**
 * 获取产品分类
 */
function getGoodsCategory() {
    $url = API_BASE_URL . '/api/goodscategory/tree';
    $data = [
        'app_type' => APP_TYPE,
        'app_type_name' => APP_TYPE_NAME,
        'level' => 3,
        'store_id' => STORE_ID
    ];

    return apiPost($url, $data);
}

/**
 * 获取产品详情
 */
function getGoodsDetail($goods_id, $sku_id = 0) {
    $url = API_BASE_URL . '/api/goodssku/detail';
    $data = [
        'app_type' => APP_TYPE,
        'app_type_name' => APP_TYPE_NAME,
        'goods_id' => $goods_id,
        'sku_id' => $sku_id,
        'store_id' => STORE_ID
    ];

    return apiPost($url, $data);
}

/**
 * 获取商品列表(按分类)
 */
function getGoodsList($category_id, $page = 1, $page_size = 10, $order = '', $sort = '') {
    $url = API_BASE_URL . '/api/goodssku/pageByCategory';
    $data = [
        'app_type' => APP_TYPE,
        'app_type_name' => APP_TYPE_NAME,
        'category_id' => $category_id,
        'order' => $order,
        'page' => $page,
        'page_size' => $page_size,
        'sort' => $sort,
        'store_id' => STORE_ID
    ];

    return apiPost($url, $data);
}

/**
 * 获取会客厅列表
 */
function getTeahouseList($num = 10) {
    $url = API_BASE_URL . '/notes/api/notes/lists';
    $data = [
        'app_type' => APP_TYPE,
        'app_type_name' => APP_TYPE_NAME,
        'num' => $num,
        'store_id' => STORE_ID
    ];

    return apiPost($url, $data);
}

/**
 * 获取文章列表
 */
function getArticleList($category_id = '', $page = 1, $page_size = 10) {
    $url = API_BASE_URL . '/api/article/page';
    $data = [
        'app_type' => APP_TYPE,
        'app_type_name' => APP_TYPE_NAME,
        'category_id' => $category_id,
        'page' => $page,
        'page_size' => $page_size,
        'store_id' => STORE_ID
    ];

    return apiPost($url, $data);
}

/**
 * 获取文章详情
 */
function getArticleDetail($article_id) {
    $url = API_BASE_URL . '/api/article/info';
    $data = [
        'app_type' => APP_TYPE,
        'app_type_name' => APP_TYPE_NAME,
        'article_id' => $article_id,
        'store_id' => STORE_ID
    ];

    return apiPost($url, $data);
}

/**
 * 格式化价格
 */
function formatPrice($price) {
    return '¥' . number_format($price, 2);
}

/**
 * 安全输出HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

<?php
require_once 'api.php';

// 获取goods_id参数，默认为3
$goods_id = isset($_GET['id']) ? intval($_GET['id']) : 3;

// 获取产品详情
$productResult = getGoodsDetail($goods_id);
$product = $productResult['data']['goods_sku_detail'] ?? [];

// 设置页面标题
$page_title = $product['goods_name'] ?? '产品详情';

// 包含头部
include 'templates/header.php';
?>

<style>
    /* 面包屑导航 */
    .breadcrumb {
        margin-top: 100px;
        padding: 20px 60px;
        background: var(--bg-section-1);
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .breadcrumb a {
        color: #666;
        text-decoration: none;
        margin-right: 10px;
    }

    .breadcrumb a:hover {
        color: var(--title-color);
    }

    .breadcrumb span {
        margin: 0 10px;
        color: #999;
    }

    /* 产品详情主容器 */
    .product-detail-container {
        max-width: 1400px;
        margin: 60px auto;
        padding: 0 60px;
    }

    .product-main {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        margin-bottom: 80px;
    }

    /* 产品图片区 */
    .product-images {
        position: sticky;
        top: 120px;
        height: fit-content;
    }

    .main-image {
        width: 100%;
        height: 600px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* 产品信息区 */
    .product-info {
        padding-top: 20px;
    }

    .product-title {
        font-size: 38px;
        color: var(--title-color);
        margin-bottom: 20px;
        font-weight: bold;
    }

    .product-subtitle {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.8;
    }

    .product-price-section {
        background: var(--bg-section-1);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 40px;
    }

    .price-label {
        font-size: 16px;
        color: #666;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 42px;
        color: var(--accent-line);
        font-weight: bold;
    }

    .product-specs {
        margin-bottom: 40px;
    }

    .spec-item {
        display: flex;
        padding: 20px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .spec-label {
        width: 120px;
        color: #666;
        font-size: 16px;
    }

    .spec-value {
        flex: 1;
        color: var(--title-color);
        font-size: 16px;
        font-weight: 500;
    }

    .buy-section {
        display: flex;
        gap: 20px;
        margin-top: 40px;
    }

    .buy-btn {
        flex: 1;
        padding: 18px;
        background: var(--accent-line);
        color: #fff;
        border: none;
        border-radius: 30px;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: bold;
    }

    .buy-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(212, 175, 118, 0.3);
    }

    /* 产品详细描述 */
    .product-description {
        margin-top: 80px;
        padding: 40px;
        background: var(--card-bg);
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .description-title {
        font-size: 32px;
        color: var(--title-color);
        text-align: center;
        margin-bottom: 40px;
        font-weight: bold;
    }

    .description-content {
        font-size: 17px;
        line-height: 2;
        color: #555;
    }

    .description-content p {
        margin-bottom: 20px;
        text-indent: 2em;
    }

    .description-content img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
        border-radius: 10px;
    }

    /* 响应式 */
    @media (max-width: 1200px) {
        .product-main {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .product-images {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .product-detail-container {
            padding: 0 20px;
        }

        .buy-section {
            flex-direction: column;
        }
    }
</style>

<!-- 面包屑导航 -->
<div class="breadcrumb">
    <a href="index.php">首页</a>
    <span>></span>
    <a href="products.php">产品中心</a>
    <span>></span>
    <span style="color: var(--title-color);"><?php echo e($product['goods_name'] ?? '产品详情'); ?></span>
</div>

<!-- 产品详情主容器 -->
<div class="product-detail-container">
    <?php if (!empty($product)): ?>
    <div class="product-main">
        <!-- 产品图片区 -->
        <div class="product-images">
            <div class="main-image">
                <?php
                $main_image = !empty($product['sku_image']) ? $product['sku_image'] : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG';
                ?>
                <img src="<?php echo e($main_image); ?>" alt="<?php echo e($product['goods_name']); ?>">
            </div>
        </div>

        <!-- 产品信息区 -->
        <div class="product-info">
            <h1 class="product-title"><?php echo e($product['goods_name']); ?></h1>
            <?php if (!empty($product['introduction'])): ?>
            <p class="product-subtitle"><?php echo e($product['introduction']); ?></p>
            <?php endif; ?>

            <div class="product-price-section">
                <div class="price-label">价格</div>
                <span class="product-price">¥<?php echo number_format($product['price'], 2); ?></span>
                <?php if ($product['market_price'] > 0 && $product['market_price'] > $product['price']): ?>
                <span style="font-size: 20px; color: #999; text-decoration: line-through; margin-left: 20px;">
                    ¥<?php echo number_format($product['market_price'], 2); ?>
                </span>
                <?php endif; ?>
            </div>

            <div class="product-specs">
                <?php if (!empty($product['sku_name'])): ?>
                <div class="spec-item">
                    <div class="spec-label">产品规格</div>
                    <div class="spec-value"><?php echo e($product['sku_name']); ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($product['unit'])): ?>
                <div class="spec-item">
                    <div class="spec-label">计量单位</div>
                    <div class="spec-value"><?php echo e($product['unit']); ?></div>
                </div>
                <?php endif; ?>

                <?php if ($product['stock_show'] == 1): ?>
                <div class="spec-item">
                    <div class="spec-label">库存</div>
                    <div class="spec-value"><?php echo e($product['stock']); ?> 件</div>
                </div>
                <?php endif; ?>

                <?php if ($product['sale_show'] == 1): ?>
                <div class="spec-item">
                    <div class="spec-label">销量</div>
                    <div class="spec-value"><?php echo e($product['sale_num']); ?> 件</div>
                </div>
                <?php endif; ?>
            </div>

            <div class="buy-section">
                <button class="buy-btn" onclick="showQRCode('order')">立即购买</button>
                <button class="buy-btn" style="background: transparent; border: 2px solid var(--accent-line); color: var(--accent-line);" onclick="showQRCode('product')">咨询客服</button>
            </div>
        </div>
    </div>

    <!-- 产品详细描述 -->
    <?php if (!empty($product['goods_content'])): ?>
    <div class="product-description">
        <h2 class="description-title">产品详情</h2>
        <div class="description-content">
            <?php echo $product['goods_content']; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align: center; padding: 100px 0;">
        <h2 style="font-size: 32px; color: var(--title-color); margin-bottom: 20px;">产品不存在</h2>
        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">抱歉,未找到该产品信息</p>
        <a href="products.php" style="display: inline-block; padding: 15px 40px; background: var(--accent-line); color: #fff; text-decoration: none; border-radius: 25px;">返回产品列表</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>

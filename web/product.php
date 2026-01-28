<?php
/**
 * Product Detail Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Get product ID
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: ' . langUrl('/web/products.php'));
    exit;
}

// Get product data
$product = getProduct($productId);

if (!$product) {
    header('Location: ' . langUrl('/web/products.php'));
    exit;
}

// Page settings
$currentPage = 'products';
$pageTitle = $product['name'];

// Get related products (same category)
$categoryIds = explode(',', $product['category_id']);
$relatedProducts = [];
if (!empty($categoryIds[0])) {
    $relatedProducts = getProducts((int)$categoryIds[0], 4);
    // Remove current product from related
    $relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
        return $p['goods_id'] != $productId;
    });
    $relatedProducts = array_slice($relatedProducts, 0, 4);
}

includeHeader();
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <a href="<?php echo langUrl('/web/products.php'); ?>"><?php _e('nav_products'); ?></a>
        <span>/</span>
        <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="product-detail" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl);">
            <!-- Product Images -->
            <div class="product-gallery">
                <?php
                $images = array_filter(explode(',', $product['goods_image']));
                $mainImage = $images[0] ?? '/web/assets/images/no-image.jpg';
                ?>
                <div class="main-image" style="border-radius: var(--radius-md); overflow: hidden;">
                    <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainProductImage" style="width: 100%;">
                </div>
                <?php if (count($images) > 1): ?>
                <div class="thumbnail-list" style="display: flex; gap: 10px; margin-top: var(--spacing-md);">
                    <?php foreach ($images as $index => $img): ?>
                    <div class="thumbnail" style="width: 80px; height: 80px; cursor: pointer; border: 2px solid <?php echo $index === 0 ? 'var(--color-primary)' : 'transparent'; ?>; border-radius: var(--radius-sm); overflow: hidden;"
                         onclick="document.getElementById('mainProductImage').src='<?php echo htmlspecialchars($img); ?>'; this.parentElement.querySelectorAll('.thumbnail').forEach(t => t.style.borderColor = 'transparent'); this.style.borderColor = 'var(--color-primary)';">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info-detail">
                <h1 style="font-size: 1.8rem; margin-bottom: var(--spacing-md);"><?php echo htmlspecialchars($product['name']); ?></h1>

                <?php if (!empty($product['introduction'])): ?>
                <p style="color: var(--color-text-light); margin-bottom: var(--spacing-md);"><?php echo htmlspecialchars($product['introduction']); ?></p>
                <?php endif; ?>

                <div class="product-price-box" style="background: var(--color-bg-light); padding: var(--spacing-md); border-radius: var(--radius-md); margin-bottom: var(--spacing-lg);">
                    <span style="color: var(--color-text-light);"><?php _e('price'); ?>:</span>
                    <span style="font-size: 2rem; color: var(--color-secondary); font-weight: bold; margin-left: 10px;">
                        &yen;<?php echo number_format($product['price'], 2); ?>
                    </span>
                    <?php if ($product['market_price'] > $product['price']): ?>
                    <span style="text-decoration: line-through; color: var(--color-text-lighter); margin-left: 10px;">
                        &yen;<?php echo number_format($product['market_price'], 2); ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: var(--spacing-lg);">
                    <p><strong><?php _e('product_stock'); ?>:</strong>
                        <?php if ($product['goods_stock'] > 0): ?>
                        <span style="color: green;"><?php _e('product_in_stock'); ?></span>
                        <?php else: ?>
                        <span style="color: red;"><?php _e('product_out_of_stock'); ?></span>
                        <?php endif; ?>
                    </p>
                    <p><strong><?php _e('product_sales'); ?>:</strong> <?php echo (int)($product['sale_num'] + $product['virtual_sale']); ?></p>
                </div>

                <!-- Mini Program QR Code Placeholder -->
                <div style="background: var(--color-bg-light); padding: var(--spacing-lg); border-radius: var(--radius-md); text-align: center;">
                    <p style="margin-bottom: var(--spacing-sm);">
                        <?php echo isEnglish() ? 'Scan to purchase on WeChat Mini Program' : '扫码进入小程序购买'; ?>
                    </p>
                    <div style="width: 150px; height: 150px; background: #eee; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm);">
                        <span style="color: var(--color-text-lighter);">QR Code</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="product-description" style="margin-top: var(--spacing-xl);">
            <h2 style="font-size: 1.5rem; margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-sm); border-bottom: 2px solid var(--color-primary);">
                <?php _e('product_desc'); ?>
            </h2>
            <div class="content" style="line-height: 2;">
                <?php echo $product['content']; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="related-products" style="margin-top: var(--spacing-xl);">
            <h2 style="font-size: 1.5rem; margin-bottom: var(--spacing-lg);"><?php _e('product_related'); ?></h2>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $related): ?>
                <a href="<?php echo langUrl('/web/product.php?id=' . $related['goods_id']); ?>" class="product-card">
                    <div class="product-image">
                        <?php
                        $relImages = explode(',', $related['goods_image']);
                        $relMainImage = $relImages[0] ?? '/web/assets/images/no-image.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($relMainImage); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($related['name']); ?></h3>
                        <div class="product-price"><?php echo number_format($related['price'], 2); ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php includeFooter(); ?>

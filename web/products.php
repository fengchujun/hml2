<?php
/**
 * Products Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Page settings
$currentPage = 'products';
$pageTitle = __('nav_products');

// Get category filter
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;

// Get data
$categories = getCategories();
$products = getProducts($categoryId, $limit, $page);

// Get current category name
$currentCategory = null;
if ($categoryId > 0) {
    foreach ($categories as $cat) {
        if ($cat['category_id'] == $categoryId) {
            $currentCategory = $cat;
            $pageTitle = $cat['name'];
            break;
        }
    }
}

includeHeader();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php _e('nav_products'); ?></h1>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <a href="<?php echo langUrl('/web/products.php'); ?>"><?php _e('nav_products'); ?></a>
        <?php if ($currentCategory): ?>
        <span>/</span>
        <span><?php echo htmlspecialchars($currentCategory['name']); ?></span>
        <?php endif; ?>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Category Filter -->
        <div class="category-filter" style="margin-bottom: var(--spacing-xl);">
            <a href="<?php echo langUrl('/web/products.php'); ?>"
               class="btn <?php echo $categoryId == 0 ? 'btn-primary' : 'btn-outline'; ?>"
               style="margin-right: 10px; margin-bottom: 10px;">
                <?php _e('category_all'); ?>
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo langUrl('/web/products.php?category_id=' . $cat['category_id']); ?>"
               class="btn <?php echo $categoryId == $cat['category_id'] ? 'btn-primary' : 'btn-outline'; ?>"
               style="margin-right: 10px; margin-bottom: 10px;">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <a href="<?php echo langUrl('/web/product.php?id=' . $product['goods_id']); ?>" class="product-card">
                <div class="product-image">
                    <?php
                    $images = explode(',', $product['goods_image']);
                    $mainImage = $images[0] ?? '/web/assets/images/no-image.jpg';
                    ?>
                    <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-price"><?php echo number_format($product['price'], 2); ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: var(--spacing-xl);">
            <p style="color: var(--color-text-light);"><?php _e('no_data'); ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php includeFooter(); ?>

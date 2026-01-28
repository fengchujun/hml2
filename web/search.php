<?php
/**
 * Search Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Page settings
$currentPage = 'search';
$pageTitle = __('search');

// Get search keyword
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$products = [];

if (!empty($keyword)) {
    $pdo = getDB();
    $lang = getCurrentLang();

    // Search in goods
    $searchField = $lang === 'en' ? 'goods_name_en' : 'goods_name';
    $sql = "SELECT * FROM " . DB_PREFIX . "goods
            WHERE site_id = 1 AND is_delete = 0 AND goods_state = 1
            AND (goods_name LIKE ? OR goods_name_en LIKE ?)
            ORDER BY sort DESC, goods_id DESC
            LIMIT 20";

    $searchTerm = '%' . $keyword . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm]);
    $products = $stmt->fetchAll();

    // Process language fields
    foreach ($products as &$prod) {
        $prod['name'] = getLangValue($prod, 'goods_name', $lang);
    }
}

includeHeader();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php _e('search'); ?></h1>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <span><?php _e('search'); ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Search Form -->
        <div style="max-width: 600px; margin: 0 auto var(--spacing-xl);">
            <form action="/web/search.php" method="get" style="display: flex; gap: 10px;">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>"
                       placeholder="<?php _e('search_placeholder'); ?>"
                       style="flex: 1; padding: 12px 16px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 1rem;">
                <button type="submit" class="btn btn-primary"><?php _e('search'); ?></button>
            </form>
        </div>

        <!-- Search Results -->
        <?php if (!empty($keyword)): ?>
            <?php if (!empty($products)): ?>
            <p style="margin-bottom: var(--spacing-lg); color: var(--color-text-light);">
                <?php echo isEnglish()
                    ? 'Found ' . count($products) . ' results for "' . htmlspecialchars($keyword) . '"'
                    : '搜索 "' . htmlspecialchars($keyword) . '" 找到 ' . count($products) . ' 个结果'; ?>
            </p>
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
                <p style="color: var(--color-text-light);">
                    <?php echo isEnglish()
                        ? 'No results found for "' . htmlspecialchars($keyword) . '"'
                        : '未找到与 "' . htmlspecialchars($keyword) . '" 相关的产品'; ?>
                </p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php includeFooter(); ?>

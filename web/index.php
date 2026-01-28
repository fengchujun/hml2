<?php
/**
 * Homepage - HuaMuLan Tea PC Website
 */

// Initialize
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Page settings
$currentPage = 'home';
$pageTitle = __('nav_home');

// Get data
$categories = getCategories();
$featuredProducts = getProducts(0, 8);
$latestArticles = getArticles(0, 3);

// Include header
includeHeader();
?>

<!-- Banner Section -->
<section class="banner" style="background-image: url('/web/assets/images/banner.jpg');">
    <div class="banner-content">
        <h1><?php _e('home_banner_title'); ?></h1>
        <p><?php _e('home_banner_subtitle'); ?></p>
        <a href="<?php echo langUrl('/web/products.php'); ?>" class="btn btn-primary"><?php _e('all_products'); ?></a>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php _e('home_categories'); ?></h2>
            <p class="section-subtitle"><?php _e('category_title'); ?></p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
            <a href="<?php echo langUrl('/web/products.php?category_id=' . $category['category_id']); ?>" class="category-card">
                <?php if (!empty($category['image'])): ?>
                <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                <?php endif; ?>
                <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section" style="background: var(--color-bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php _e('home_featured'); ?></h2>
            <p class="section-subtitle"><?php _e('product_desc'); ?></p>
        </div>

        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
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

        <div style="text-align: center; margin-top: var(--spacing-xl);">
            <a href="<?php echo langUrl('/web/products.php'); ?>" class="btn btn-outline"><?php _e('view_details'); ?></a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php _e('home_about_title'); ?></h2>
        </div>

        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <p style="font-size: 1.1rem; line-height: 2; color: var(--color-text-light);">
                <?php _e('home_about_desc'); ?>
            </p>
            <a href="<?php echo langUrl('/web/about.php'); ?>" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
                <?php _e('learn_more'); ?>
            </a>
        </div>
    </div>
</section>

<!-- News Section -->
<?php if (!empty($latestArticles)): ?>
<section class="section" style="background: var(--color-bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php _e('article_title'); ?></h2>
        </div>

        <div class="articles-grid">
            <?php foreach ($latestArticles as $article): ?>
            <a href="<?php echo langUrl('/web/article.php?id=' . $article['article_id']); ?>" class="article-card">
                <?php if (!empty($article['cover_img'])): ?>
                <div class="article-image">
                    <img src="<?php echo htmlspecialchars($article['cover_img']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                </div>
                <?php endif; ?>
                <div class="article-content">
                    <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <div class="article-meta">
                        <span><?php _e('article_time'); ?>: <?php echo formatDate($article['create_time']); ?></span>
                        <span> | </span>
                        <span><?php _e('article_views'); ?>: <?php echo $article['read_num'] + $article['initial_read_num']; ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: var(--spacing-xl);">
            <a href="<?php echo langUrl('/web/news.php'); ?>" class="btn btn-outline"><?php _e('learn_more'); ?></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Include footer
includeFooter();
?>

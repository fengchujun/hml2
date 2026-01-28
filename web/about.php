<?php
/**
 * About Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Page settings
$currentPage = 'about';
$pageTitle = __('nav_about');

// Try to get about article (category_id = 1 typically for brand/about)
$aboutArticles = getArticles(1, 10);

includeHeader();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php _e('about_title'); ?></h1>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <span><?php _e('nav_about'); ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Company Introduction -->
        <div class="about-intro" style="max-width: 900px; margin: 0 auto var(--spacing-xl); text-align: center;">
            <h2 style="font-size: 1.8rem; color: var(--color-primary); margin-bottom: var(--spacing-lg);">
                <?php _e('home_about_title'); ?>
            </h2>
            <p style="font-size: 1.1rem; line-height: 2; color: var(--color-text-light);">
                <?php _e('home_about_desc'); ?>
            </p>
        </div>

        <!-- About Articles -->
        <?php if (!empty($aboutArticles)): ?>
        <div class="about-articles">
            <?php foreach ($aboutArticles as $index => $article): ?>
            <div class="about-section" style="margin-bottom: var(--spacing-xl); <?php echo $index % 2 ? 'background: var(--color-bg-light);' : ''; ?> padding: var(--spacing-xl); border-radius: var(--radius-lg);">
                <h3 style="font-size: 1.5rem; color: var(--color-primary); margin-bottom: var(--spacing-lg); text-align: center;">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h3>
                <div class="article-content" style="line-height: 2;">
                    <?php echo $article['content']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-sm);
    margin: var(--spacing-sm) 0;
}
</style>

<?php includeFooter(); ?>

<?php
/**
 * News Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Page settings
$currentPage = 'news';
$pageTitle = __('nav_news');

// Get articles
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 9;
$articles = getArticles(0, $limit, $page);

includeHeader();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php _e('article_title'); ?></h1>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <span><?php _e('nav_news'); ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (!empty($articles)): ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
            <a href="<?php echo langUrl('/web/article.php?id=' . $article['article_id']); ?>" class="article-card">
                <?php if (!empty($article['cover_img'])): ?>
                <div class="article-image">
                    <img src="<?php echo htmlspecialchars($article['cover_img']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                </div>
                <?php endif; ?>
                <div class="article-content">
                    <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <?php if (!empty($article['article_abstract'])): ?>
                    <p style="color: var(--color-text-light); font-size: 0.9rem; margin-bottom: var(--spacing-sm); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?php echo htmlspecialchars($article['article_abstract']); ?>
                    </p>
                    <?php endif; ?>
                    <div class="article-meta">
                        <span><?php _e('article_time'); ?>: <?php echo formatDate($article['create_time']); ?></span>
                        <span> | </span>
                        <span><?php _e('article_views'); ?>: <?php echo $article['read_num'] + $article['initial_read_num']; ?></span>
                    </div>
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

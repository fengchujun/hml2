<?php
/**
 * Article Detail Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Get article ID
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($articleId <= 0) {
    header('Location: ' . langUrl('/web/news.php'));
    exit;
}

// Get article data
$article = getArticle($articleId);

if (!$article) {
    header('Location: ' . langUrl('/web/news.php'));
    exit;
}

// Page settings
$currentPage = 'news';
$pageTitle = $article['title'];

// Get other articles
$otherArticles = getArticles(0, 5);
$otherArticles = array_filter($otherArticles, function($a) use ($articleId) {
    return $a['article_id'] != $articleId;
});
$otherArticles = array_slice($otherArticles, 0, 4);

includeHeader();
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <a href="<?php echo langUrl('/web/news.php'); ?>"><?php _e('nav_news'); ?></a>
        <span>/</span>
        <span><?php echo htmlspecialchars($article['title']); ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="article-detail" style="display: grid; grid-template-columns: 1fr 300px; gap: var(--spacing-xl);">
            <!-- Main Content -->
            <div class="article-main">
                <h1 style="font-size: 2rem; margin-bottom: var(--spacing-md);"><?php echo htmlspecialchars($article['title']); ?></h1>

                <div class="article-meta" style="margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-md); border-bottom: 1px solid var(--color-border);">
                    <?php if ($article['is_show_release_time']): ?>
                    <span><?php _e('article_time'); ?>: <?php echo formatDate($article['create_time']); ?></span>
                    <?php endif; ?>
                    <?php if ($article['is_show_read_num']): ?>
                    <span style="margin-left: var(--spacing-md);"><?php _e('article_views'); ?>: <?php echo $article['read_num'] + $article['initial_read_num']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="article-content" style="line-height: 2; font-size: 1.05rem;">
                    <?php echo $article['content']; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="article-sidebar">
                <div style="background: var(--color-bg-light); padding: var(--spacing-md); border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.1rem; margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-sm); border-bottom: 2px solid var(--color-primary);">
                        <?php echo isEnglish() ? 'Other Articles' : '其他文章'; ?>
                    </h3>
                    <ul style="list-style: none;">
                        <?php foreach ($otherArticles as $other): ?>
                        <li style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--color-border);">
                            <a href="<?php echo langUrl('/web/article.php?id=' . $other['article_id']); ?>" style="display: block; color: var(--color-text);">
                                <span style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($other['title']); ?>
                                </span>
                                <small style="color: var(--color-text-lighter);"><?php echo formatDate($other['create_time']); ?></small>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
@media (max-width: 768px) {
    .article-detail {
        grid-template-columns: 1fr !important;
    }
}
.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-sm);
    margin: var(--spacing-md) 0;
}
</style>

<?php includeFooter(); ?>

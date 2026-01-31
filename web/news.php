<?php
require_once 'api.php';
require_once 'lang.php';

// 设置页面标题
$page_title = $is_english ? 'Company News' : '企业资讯';

// 获取分页参数
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page_size = 12;

// 获取企业资讯列表
$newsResult = getArticleList(NEWS_CATEGORY_ID, $page, $page_size);
$newsList = $newsResult['data']['list'] ?? [];
$page_count = $newsResult['data']['page_count'] ?? 1;
$total_count = $newsResult['data']['count'] ?? 0;

// 包含头部
if ($is_english) {
    include 'templates/header_en.php';
} else {
    include 'templates/header.php';
}
?>

<style>
    /* 主容器 */
    .news-container {
        margin-top: 100px;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        padding: 60px;
        min-height: calc(100vh - 300px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 60px;
        padding-bottom: 30px;
        border-bottom: 3px solid var(--accent-line);
    }

    .page-title {
        font-size: 42px;
        color: var(--title-color);
        margin-bottom: 20px;
        font-weight: bold;
    }

    .page-subtitle {
        font-size: 18px;
        color: #666;
    }

    /* 新闻列表 */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 60px;
    }

    .news-card {
        background: var(--card-bg);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        cursor: pointer;
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .news-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: var(--bg-section-1);
    }

    .news-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .news-card:hover .news-image img {
        transform: scale(1.1);
    }

    .news-info {
        padding: 25px;
    }

    .news-title {
        font-size: 20px;
        color: var(--title-color);
        margin-bottom: 15px;
        font-weight: bold;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
    }

    .news-abstract {
        font-size: 14px;
        color: #666;
        line-height: 1.8;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
        font-size: 13px;
        color: #999;
    }

    .news-date {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .news-views {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* 分页 */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 40px;
    }

    .pagination a,
    .pagination span {
        padding: 10px 20px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        text-decoration: none;
        color: var(--title-color);
        transition: all 0.3s;
    }

    .pagination a:hover {
        background: var(--accent-line);
        color: #fff;
        border-color: var(--accent-line);
    }

    .pagination .current {
        background: var(--accent-line);
        color: #fff;
        border-color: var(--accent-line);
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* 空状态 */
    .empty-state {
        text-align: center;
        padding: 100px 0;
    }

    .empty-state h3 {
        font-size: 24px;
        color: var(--title-color);
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 16px;
        color: #666;
    }

    /* 响应式 */
    @media (max-width: 1200px) {
        .news-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .news-container {
            padding: 30px 20px;
        }

        .news-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="news-container">
    <div class="page-header">
        <h1 class="page-title"><?php echo __('news_title'); ?></h1>
        <p class="page-subtitle"><?php echo __('news_subtitle'); ?></p>
    </div>

    <?php if (!empty($newsList)): ?>
    <div class="news-grid">
        <?php foreach ($newsList as $news): ?>
        <?php $articleTitle = $is_english && !empty($news['article_title_en']) ? $news['article_title_en'] : $news['article_title']; ?>
        <div class="news-card" onclick="location.href='concept-detail.php?id=<?php echo $news['article_id']; ?>&lang=<?php echo $current_lang; ?>'">
            <div class="news-image">
                <?php $newsCover = getLocalizedField($news, 'cover_img') ?: ($news['cover_img'] ?? ''); ?>
                <?php if (!empty($newsCover)): ?>
                <img src="<?php echo e($newsCover); ?>" alt="<?php echo e($articleTitle); ?>">
                <?php else: ?>
                <img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG" alt="<?php echo e($articleTitle); ?>">
                <?php endif; ?>
            </div>
            <div class="news-info">
                <h3 class="news-title"><?php echo e($articleTitle); ?></h3>
                <?php if (!empty($news['article_abstract'])): ?>
                <p class="news-abstract"><?php echo e($news['article_abstract']); ?></p>
                <?php endif; ?>
                <div class="news-meta">
                    <span class="news-date">
                        <?php echo date('Y-m-d', $news['create_time']); ?>
                    </span>
                    <?php if ($news['is_show_read_num'] == 1): ?>
                    <span class="news-views">
                        <?php echo $news['read_num']; ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 分页 -->
    <?php if ($page_count > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&lang=<?php echo $current_lang; ?>"><?php echo __('prev_page'); ?></a>
        <?php else: ?>
            <span class="disabled"><?php echo __('prev_page'); ?></span>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $page_count; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="current"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>&lang=<?php echo $current_lang; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $page_count): ?>
            <a href="?page=<?php echo $page + 1; ?>&lang=<?php echo $current_lang; ?>"><?php echo __('next_page'); ?></a>
        <?php else: ?>
            <span class="disabled"><?php echo __('next_page'); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="empty-state">
        <h3><?php echo __('no_news'); ?></h3>
        <p><?php echo __('no_news_desc'); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php
if ($is_english) {
    include 'templates/footer_en.php';
} else {
    include 'templates/footer.php';
}
?>

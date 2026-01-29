<?php
require_once 'api.php';
require_once 'lang.php';

// 获取article_id参数,默认为企业理念
$article_id = isset($_GET['id']) ? intval($_GET['id']) : ARTICLE_ID_ENTERPRISE;

// 获取文章详情
$articleResult = getArticleDetail($article_id);
$article = $articleResult['data'] ?? [];

// 如果没有获取到文章,使用默认ID重试
if (empty($article)) {
    $article_id = ARTICLE_ID_ENTERPRISE;
    $articleResult = getArticleDetail($article_id);
    $article = $articleResult['data'] ?? [];
}

// 获取文章标题和内容（多语言）
$articleTitle = $is_english && !empty($article['article_title_en']) ? $article['article_title_en'] : ($article['article_title'] ?? '');
$articleContent = $is_english && !empty($article['article_content_en']) ? $article['article_content_en'] : ($article['article_content'] ?? '');

// 设置页面标题
$page_title = $articleTitle ?: ($is_english ? 'Article' : '文章详情');

// 定义导航菜单(仅当是核心理念相关文章时显示侧边栏)
$show_sidebar = in_array($article_id, [ARTICLE_ID_ENTERPRISE, ARTICLE_ID_QUALITY, ARTICLE_ID_BRAND, ARTICLE_ID_MANUAL]);
$nav_items = [
    ['id' => ARTICLE_ID_ENTERPRISE, 'title' => __('nav_enterprise'), 'title_zh' => '企业理念'],
    ['id' => ARTICLE_ID_QUALITY, 'title' => __('nav_quality'), 'title_zh' => '品质观念'],
    ['id' => ARTICLE_ID_BRAND, 'title' => __('brand_intro'), 'title_zh' => '品牌介绍'],
    ['id' => ARTICLE_ID_MANUAL, 'title' => __('product_manual'), 'title_zh' => '产品手册']
];

// 包含头部
if ($is_english) {
    include 'templates/header_en.php';
} else {
    include 'templates/header.php';
}
?>

<style>
    /* 主容器 */
    .main-container {
        margin-top: 80px;
        display: flex;
        min-height: calc(100vh - 80px);
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* 左侧导航 */
    .sidebar {
        width: 280px;
        background: var(--sidebar-bg);
        padding: 40px 20px;
        position: sticky;
        top: 80px;
        height: calc(100vh - 80px);
        overflow-y: auto;
        border-right: 2px solid var(--border-color);
    }

    .sidebar-title {
        font-size: 24px;
        color: var(--title-color);
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--accent-line);
        font-weight: bold;
    }

    .sidebar-nav {
        list-style: none;
    }

    .sidebar-nav li {
        margin-bottom: 15px;
    }

    .sidebar-nav a {
        display: block;
        padding: 15px 20px;
        color: #666;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s;
        font-size: 16px;
    }

    .sidebar-nav a:hover {
        background: #fff;
        color: var(--title-color);
        transform: translateX(5px);
    }

    .sidebar-nav a.active {
        background: var(--sidebar-active);
        color: #fff;
        font-weight: bold;
    }

    /* 内容区域 */
    .content-area {
        flex: 1;
        padding: 60px;
    }

    .concept-section {
        margin-bottom: 80px;
        padding: 50px;
        background: var(--card-bg);
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
    }

    .concept-header {
        text-align: center;
        margin-bottom: 50px;
        padding-bottom: 30px;
        border-bottom: 3px solid var(--accent-line);
    }

    .concept-title {
        font-size: 42px;
        color: var(--title-color);
        margin-bottom: 20px;
        font-weight: bold;
    }

    .concept-subtitle {
        font-size: 20px;
        color: #666;
        line-height: 1.8;
    }

    .concept-content {
       /* font-size: 18px;
        line-height: 2.2;*/
        color: #555;
    }

    .concept-content p {
       /* margin-bottom: 25px;
        text-indent: 2em;
        text-align: justify; */
    }

    .concept-content img {
        max-width: 100%;
        height: auto;
        margin: -2px 0;
        /* border-radius: 10px;*/
    }

    .concept-highlight {
        background: var(--bg-section-1);
        padding: 30px;
        border-radius: 10px;
        margin: 30px 0;
        border-left: 5px solid var(--accent-line);
    }

    .qr-action {
        text-align: center;
        margin-top: 40px;
        padding-top: 40px;
        border-top: 2px solid var(--border-color);
    }

    .qr-action button {
        padding: 15px 40px;
        background: var(--accent-line);
        color: #fff;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .qr-action button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(212, 175, 118, 0.3);
    }

    /* 响应式 */
    @media (max-width: 1024px) {
        .sidebar {
            width: 220px;
        }

        .content-area {
            padding: 40px 30px;
        }
    }

    @media (max-width: 768px) {
        .main-container {
            flex-direction: column;
        }

        .sidebar {
            position: static;
            width: 100%;
            height: auto;
        }

        .content-area {
            padding: 30px 20px;
        }

        .concept-section {
            padding: 30px 20px;
        }
    }
</style>

<!-- 主容器 -->
<div class="main-container">
    <?php if ($show_sidebar): ?>
    <!-- 左侧导航 -->
    <aside class="sidebar">
        <h2 class="sidebar-title"><?php echo __('core_concept'); ?></h2>
        <ul class="sidebar-nav">
            <?php foreach ($nav_items as $item): ?>
            <li>
                <a href="?id=<?php echo $item['id']; ?>&lang=<?php echo $current_lang; ?>" class="<?php echo $article_id == $item['id'] ? 'active' : ''; ?>">
                    <?php echo e($item['title']); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>
    <?php endif; ?>

    <!-- 内容区域 -->
    <main class="content-area" style="<?php echo !$show_sidebar ? 'max-width: 1200px; margin: 0 auto;' : ''; ?>">
        <?php if (!empty($article)): ?>
        <section class="concept-section">
            <div class="concept-header">
                <h1 class="concept-title"><?php echo e($articleTitle); ?></h1>
                <?php if (!empty($article['article_abstract'])): ?>
                <p class="concept-subtitle"><?php echo e($article['article_abstract']); ?></p>
                <?php endif; ?>
            </div>
            <div class="concept-content">
                <?php echo $articleContent; ?>
            </div>
            <div class="qr-action">
                <p style="font-size: 20px; color: var(--title-color); margin-bottom: 20px;">
                    <em><?php echo __('scan_more'); ?></em>
                </p>
                <button onclick="showQRCode('product')"><?php echo __('view_more'); ?></button>
            </div>
        </section>
        <?php else: ?>
        <section class="concept-section">
            <div class="concept-header">
                <h1 class="concept-title"><?php echo __('loading'); ?></h1>
                <p class="concept-subtitle"><?php echo __('please_wait'); ?></p>
            </div>
        </section>
        <?php endif; ?>
    </main>
</div>

<?php
if ($is_english) {
    include 'templates/footer_en.php';
} else {
    include 'templates/footer.php';
}
?>

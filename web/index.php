<?php
require_once 'api.php';

// 设置页面标题
$page_title = '首页';

// 获取产品分类
$categoryResult = getGoodsCategory();
$categories = $categoryResult['data'] ?? [];

// 获取会客厅列表
$teahouseResult = getTeahouseList(3);
$teahouses = $teahouseResult['data'] ?? [];

// 获取企业介绍相关文章
$conceptArticles = [];
$conceptIds = [
    'enterprise' => ARTICLE_ID_ENTERPRISE,
    'quality' => ARTICLE_ID_QUALITY,
    'manual' => ARTICLE_ID_MANUAL,
    'brand' => ARTICLE_ID_BRAND
];

foreach ($conceptIds as $key => $articleId) {
    $articleResult = getArticleDetail($articleId);
    if (isset($articleResult['data'])) {
        $conceptArticles[$key] = $articleResult['data'];
    }
}

// 包含头部
include 'templates/header.php';
?>

<style>
    /* 视频背景英雄区域 */
    .hero {
        height: 100vh;
        position: relative;
        overflow: hidden;
        margin-top: 0;
    }

    .hero-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 2;
    }

    .hero-content {
        position: relative;
        z-index: 3;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #ffffff;
    }

    .hero-title {
        font-size: 72px;
        font-weight: bold;
        margin-bottom: 20px;
        letter-spacing: 8px;
        color: var(--nav-text);
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
        animation: fadeInDown 1s ease-out;
    }

    .hero-subtitle {
        font-size: 28px;
        margin-bottom: 40px;
        color: #ffffff;
        text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);
        animation: fadeInUp 1s ease-out 0.3s both;
    }

    .cta-buttons {
        display: flex;
        gap: 30px;
        justify-content: center;
        animation: fadeInUp 1s ease-out 0.6s both;
    }

    .btn {
        padding: 15px 40px;
        font-size: 18px;
        border: 2px solid var(--nav-text);
        background: transparent;
        color: var(--nav-text);
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        font-weight: bold;
    }

    .btn:hover {
        background: var(--nav-text);
        color: var(--nav-bg);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(212, 175, 118, 0.4);
    }

    /* 内容模块 */
    .content-section {
        padding: 80px 50px;
    }

    .content-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 80px;
        background: var(--card-bg);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        text-align: center;
        font-size: 42px;
        margin-bottom: 60px;
        letter-spacing: 4px;
        color: var(--title-color);
        font-weight: bold;
        position: relative;
    }

    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--accent-line), transparent);
        margin: 20px auto 0;
    }

    /* 企业介绍区域 */
    #concept .content-wrapper {
        background: var(--bg-section-1);
    }

    .concept-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
    }

    .concept-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid var(--border-color);
        overflow: hidden;
        background: var(--card-bg);
    }

    .concept-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(45, 31, 26, 0.15);
        border-color: var(--accent-line);
    }

    .concept-title {
        background: var(--content-bar);
        color: var(--content-text);
        text-align: center;
        padding: 35px;
        font-size: 28px;
        font-weight: bold;
        letter-spacing: 3px;
    }

    .concept-image {
        width: 100%;
        height: 400px;
        overflow: hidden;
    }

    .concept-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .concept-card:hover .concept-image img {
        transform: scale(1.05);
    }

    /* 会客厅区域 */
    #teahouse .content-wrapper {
        background: var(--bg-section-2);
    }

    .teahouse-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    .teahouse-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid var(--border-color);
        overflow: hidden;
        background: var(--card-bg);
    }

    .teahouse-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(45, 31, 26, 0.15);
        border-color: var(--accent-line);
    }

    .teahouse-title {
        background: var(--content-bar);
        color: var(--content-text);
        text-align: center;
        padding: 20px;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 2px;
    }

    .teahouse-image {
        width: 100%;
        height: 290px;
        overflow: hidden;
    }

    .teahouse-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .teahouse-card:hover .teahouse-image img {
        transform: scale(1.05);
    }

    /* 产品中心区域 */
    #products .content-wrapper {
        background: var(--bg-section-3);
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    .product-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid var(--border-color);
        overflow: hidden;
        background: var(--card-bg);
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(45, 31, 26, 0.15);
        border-color: var(--accent-line);
    }

    .product-title {
        background: var(--content-bar);
        color: var(--content-text);
        text-align: center;
        padding: 20px;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 2px;
    }

    .product-image {
        width: 100%;
        height: 300px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    /* 动画 */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* 响应式 */
    @media (max-width: 1200px) {
        .concept-grid {
            grid-template-columns: 1fr;
        }
        .teahouse-grid,
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 42px;
        }
        .hero-subtitle {
            font-size: 20px;
        }
        .teahouse-grid,
        .products-grid {
            grid-template-columns: 1fr;
        }
        .nav-menu {
            gap: 20px;
            font-size: 14px;
        }
        .content-wrapper {
                    padding: 0px 0px;
        }
        /* 移动端图片高度调整 */
        .concept-image {
            height: 280px;
        }
        .teahouse-image,
        .product-image {
            height: 220px;
        }

        /* 移动端标题字体调整 */
        .concept-title,
        .teahouse-title,
        .product-title {
            font-size: 20px;
            padding: 15px;
        }
    }

    @media (max-width: 480px) {
        /* 小屏幕手机进一步优化 */
        .concept-image {
            height: 240px;
        }
        .teahouse-image,
        .product-image {
            height: 200px;
 }
    }
</style>

<!-- 视频背景英雄区域 -->
<section class="hero" id="home">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="<?php echo HERO_VIDEO_URL; ?>" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">茶祖千万年·濮茶华木兰</h1>
        <p class="hero-subtitle">传承千年茶文化 品味匠心茶之道</p>
        <div class="cta-buttons">
            <button class="btn" onclick="showQRCode('order')">立即选购</button>
            <button class="btn" onclick="showQRCode('reserve')">预约会客厅</button>
        </div>
    </div>
</section>

<!-- 企业介绍区域 -->
<section class="content-section" id="concept">
    <div class="content-wrapper">
        <h2 class="section-title">企业介绍</h2>
        <div class="concept-grid">
            <div class="concept-card" onclick="location.href='concept-detail.php?id=<?php echo ARTICLE_ID_ENTERPRISE; ?>'">
                <div class="concept-title"></div>
                <div class="concept-image">
                    <?php
                    $enterpriseImg = isset($conceptArticles['enterprise']['cover_img']) && !empty($conceptArticles['enterprise']['cover_img'])
                        ? $conceptArticles['enterprise']['cover_img']
                        : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG';
                    ?>
                    <img src="<?php echo e($enterpriseImg); ?>" alt="企业理念">
                </div>
            </div>
            <div class="concept-card" onclick="location.href='concept-detail.php?id=<?php echo ARTICLE_ID_QUALITY; ?>'">
                <div class="concept-title"></div>
                <div class="concept-image">
                    <?php
                    $qualityImg = isset($conceptArticles['quality']['cover_img']) && !empty($conceptArticles['quality']['cover_img'])
                        ? $conceptArticles['quality']['cover_img']
                        : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG';
                    ?>
                    <img src="<?php echo e($qualityImg); ?>" alt="品质观念">
                </div>
            </div>
            <div class="concept-card" onclick="location.href='concept-detail.php?id=<?php echo ARTICLE_ID_MANUAL; ?>'">
                <div class="concept-title"></div>
                <div class="concept-image">
                    <?php
                    $manualImg = isset($conceptArticles['manual']['cover_img']) && !empty($conceptArticles['manual']['cover_img'])
                        ? $conceptArticles['manual']['cover_img']
                        : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG';
                    ?>
                    <img src="<?php echo e($manualImg); ?>" alt="产品手册">
                </div>
            </div>
            <div class="concept-card" onclick="location.href='concept-detail.php?id=<?php echo ARTICLE_ID_BRAND; ?>'">
                <div class="concept-title"></div>
                <div class="concept-image">
                    <?php
                    $brandImg = isset($conceptArticles['brand']['cover_img']) && !empty($conceptArticles['brand']['cover_img'])
                        ? $conceptArticles['brand']['cover_img']
                        : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG';
                    ?>
                    <img src="<?php echo e($brandImg); ?>" alt="品牌介绍">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 会客厅区域 -->
<section class="content-section" id="teahouse">
    <div class="content-wrapper">
        <h2 class="section-title">会客厅</h2>
        <div class="teahouse-grid">
            <?php foreach (array_slice($teahouses, 0, 3) as $teahouse): ?>
            <div class="teahouse-card" onclick="location.href='teahouse-detail.php'">
                <div class="teahouse-title"><?php echo e($teahouse['note_title']); ?></div>
                <div class="teahouse-image">
                    <img src="<?php echo e($teahouse['cover_img']); ?>" alt="<?php echo e($teahouse['note_title']); ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 产品中心 -->
<section class="content-section" id="products">
    <div class="content-wrapper">
        <h2 class="section-title">产品中心</h2>
        <div class="products-grid">
            <?php foreach (array_slice($categories, 0, 6) as $category): ?>
            <div class="product-card" onclick="location.href='products.php#category<?php echo $category['category_id']; ?>'">
                <div class="product-title"><?php echo e($category['category_name']); ?></div>
                <div class="product-image">
                    <img src="<?php echo $category['image'] ? e($category['image']) : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG'; ?>" alt="<?php echo e($category['category_name']); ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && !this.getAttribute('onclick')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
</script>

<?php include 'templates/footer.php'; ?>
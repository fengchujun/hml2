<?php
require_once 'api.php';
require_once 'lang.php';

// 设置页面标题
$page_title = $is_english ? 'Products' : '产品中心';

// 获取产品分类
$categoryResult = getGoodsCategory();
$categories = $categoryResult['data'] ?? [];

// 为每个分类获取商品列表(前6个商品)
$categoryGoods = [];
foreach ($categories as $category) {
    $goodsResult = getGoodsList($category['category_id'], 1, 6);
    $categoryGoods[$category['category_id']] = $goodsResult['data']['list'] ?? [];
}

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

    .category-section {
        margin-bottom: 80px;
    }

    .category-header {
        text-align: center;
        margin-bottom: 50px;
        padding-bottom: 30px;
        border-bottom: 3px solid var(--accent-line);
    }

    .category-title {
        font-size: 42px;
        color: var(--title-color);
        margin-bottom: 20px;
        font-weight: bold;
    }

    .category-subtitle {
        font-size: 18px;
        color: #666;
        line-height: 1.8;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 40px;
    }

    .product-card {
        background: var(--card-bg);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        cursor: pointer;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        width: 100%;
        /*height: 280px;*/
        overflow: hidden;
        position: relative;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .product-info {
        padding: 25px;
    }

    .product-name {
        font-size: 22px;
        color: var(--title-color);
        margin-bottom: 12px;
        font-weight: bold;
    }

    .product-desc {
        font-size: 15px;
        color: #666;
        line-height: 1.8;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 24px;
        color: var(--accent-line);
        font-weight: bold;
        margin-bottom: 10px;
    }

    .product-label {
        display: inline-block;
        padding: 5px 12px;
        background: var(--bg-section-1);
        color: var(--title-color);
        font-size: 12px;
        border-radius: 15px;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .view-detail-btn {
        width: 100%;
        padding: 12px;
        background: var(--accent-line);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 15px;
    }

    .view-detail-btn:hover {
        background: var(--title-color);
    }

    .empty-category {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-section-1);
        border-radius: 15px;
    }

    .empty-category p {
        font-size: 16px;
        color: #666;
    }

    /* 响应式 */
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

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

        .products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- 主容器 -->
<div class="main-container">
    <!-- 左侧导航 -->
    <aside class="sidebar">
        <h2 class="sidebar-title"><?php echo __('product_category'); ?></h2>
        <ul class="sidebar-nav">
            <?php foreach ($categories as $index => $category): ?>
            <?php $catName = $is_english && !empty($category['category_name_en']) ? $category['category_name_en'] : $category['category_name']; ?>
            <li>
                <a href="#category<?php echo $category['category_id']; ?>" class="<?php echo $index == 0 ? 'active' : ''; ?>">
                    <?php echo e($catName); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- 内容区域 -->
    <main class="content-area">
        <?php foreach ($categories as $category): ?>
        <?php $goods = $categoryGoods[$category['category_id']] ?? []; ?>
        <?php $catName = $is_english && !empty($category['category_name_en']) ? $category['category_name_en'] : $category['category_name']; ?>
        <section id="category<?php echo $category['category_id']; ?>" class="category-section">
            <div class="category-header">
                <h1 class="category-title"><?php echo e($catName); ?></h1>
                <?php if (!empty($category['short_name'])): ?>
                <p class="category-subtitle"><?php echo e($category['short_name']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($goods)): ?>
            <div class="products-grid">
                <?php foreach ($goods as $product): ?>
                <?php $productName = $is_english && !empty($product['goods_name_en']) ? $product['goods_name_en'] : $product['goods_name']; ?>
                <div class="product-card" onclick="location.href='product-detail.php?id=<?php echo $product['goods_id']; ?>&lang=<?php echo $current_lang; ?>'">
                    <div class="product-image">
                        <?php
                        $image_url = getLocalizedField($product, 'goods_image') ?: (!empty($product['goods_image']) ? $product['goods_image'] : 'https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20251215/20251215054821176579210185871.JPG');
                        ?>
                        <img src="<?php echo e($image_url); ?>" alt="<?php echo e($productName); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo e($productName); ?></h3>

                        <?php if (!empty($product['label_name'])): ?>
                        <div>
                            <span class="product-label"><?php echo e($product['label_name']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="product-price">¥<?php echo number_format($product['price'], 2); ?></div>
                        <button class="view-detail-btn" onclick="event.stopPropagation(); location.href='product-detail.php?id=<?php echo $product['goods_id']; ?>&lang=<?php echo $current_lang; ?>'"><?php echo __('view_detail'); ?></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-category">
                <p><?php echo __('no_products'); ?></p>
            </div>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>

        <?php if (empty($categories)): ?>
        <section class="category-section">
            <div class="category-header">
                <h1 class="category-title"><?php echo __('no_category'); ?></h1>
                <p class="category-subtitle"><?php echo __('coming_soon'); ?></p>
            </div>
        </section>
        <?php endif; ?>
    </main>
</div>

<script>
    // 侧边栏导航激活状态
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.category-section');
        const navLinks = document.querySelectorAll('.sidebar-nav a');

        window.addEventListener('scroll', function() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    });

    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && !this.getAttribute('onclick')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offsetTop = target.offsetTop - 100;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
</script>

<?php
if ($is_english) {
    include 'templates/footer_en.php';
} else {
    include 'templates/footer.php';
}
?>

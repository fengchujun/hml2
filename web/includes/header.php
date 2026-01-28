<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="华木兰,茶叶,普洱茶,红茶,白茶,古树茶">
    <meta name="description" content="华木兰茶业 - 传承茶文化，品味好生活">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>华木兰茶业</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/web/assets/css/style.css">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="header-container">
            <!-- Logo -->
            <div class="logo">
                <a href="/web/index.php?lang=zh">
                    <img src="/web/assets/images/logo.png" alt="华木兰茶业">
                    <span class="logo-text">华木兰茶业</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="/web/index.php?lang=zh" class="<?php echo ($currentPage ?? '') === 'home' ? 'active' : ''; ?>">首页</a></li>
                    <li class="has-dropdown">
                        <a href="/web/products.php?lang=zh" class="<?php echo ($currentPage ?? '') === 'products' ? 'active' : ''; ?>">产品中心</a>
                        <ul class="dropdown-menu">
                            <?php
                            $navCategories = getCategories();
                            foreach ($navCategories as $cat):
                            ?>
                            <li><a href="/web/products.php?lang=zh&category_id=<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="/web/about.php?lang=zh" class="<?php echo ($currentPage ?? '') === 'about' ? 'active' : ''; ?>">关于我们</a></li>
                    <li><a href="/web/news.php?lang=zh" class="<?php echo ($currentPage ?? '') === 'news' ? 'active' : ''; ?>">新闻动态</a></li>
                    <li><a href="/web/contact.php?lang=zh" class="<?php echo ($currentPage ?? '') === 'contact' ? 'active' : ''; ?>">联系我们</a></li>
                </ul>
            </nav>

            <!-- Right side: Search & Language -->
            <div class="header-right">
                <!-- Search -->
                <div class="search-box">
                    <form action="/web/search.php" method="get">
                        <input type="hidden" name="lang" value="zh">
                        <input type="text" name="keyword" placeholder="搜索产品...">
                        <button type="submit"><i class="icon-search"></i></button>
                    </form>
                </div>

                <!-- Language Switcher -->
                <div class="lang-switcher">
                    <span class="current-lang">中文</span>
                    <div class="lang-dropdown">
                        <a href="<?php echo getSwitchLangUrl('zh'); ?>" class="active">中文</a>
                        <a href="<?php echo getSwitchLangUrl('en'); ?>">English</a>
                    </div>
                </div>

                <!-- Mobile menu toggle -->
                <button class="mobile-menu-toggle" aria-label="菜单">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-nav-overlay"></div>
    <nav class="mobile-nav">
        <div class="mobile-nav-header">
            <span>导航菜单</span>
            <button class="mobile-nav-close">&times;</button>
        </div>
        <ul class="mobile-nav-list">
            <li><a href="/web/index.php?lang=zh">首页</a></li>
            <li><a href="/web/products.php?lang=zh">产品中心</a></li>
            <li><a href="/web/about.php?lang=zh">关于我们</a></li>
            <li><a href="/web/news.php?lang=zh">新闻动态</a></li>
            <li><a href="/web/contact.php?lang=zh">联系我们</a></li>
        </ul>
        <div class="mobile-lang-switch">
            <a href="<?php echo getSwitchLangUrl('zh'); ?>" class="active">中文</a>
            <a href="<?php echo getSwitchLangUrl('en'); ?>">English</a>
        </div>
    </nav>

    <!-- Main Content Start -->
    <main class="main-content">

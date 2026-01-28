<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="HuaMuLan,Tea,Pu-erh,Black Tea,White Tea,Ancient Tree Tea">
    <meta name="description" content="HuaMuLan Tea - Inheriting Tea Culture, Enjoying the Good Life">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>HuaMuLan Tea</title>
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
                <a href="/web/index.php?lang=en">
                    <img src="/web/assets/images/logo.png" alt="HuaMuLan Tea">
                    <span class="logo-text">HuaMuLan Tea</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="/web/index.php?lang=en" class="<?php echo ($currentPage ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li class="has-dropdown">
                        <a href="/web/products.php?lang=en" class="<?php echo ($currentPage ?? '') === 'products' ? 'active' : ''; ?>">Products</a>
                        <ul class="dropdown-menu">
                            <?php
                            $navCategories = getCategories();
                            foreach ($navCategories as $cat):
                            ?>
                            <li><a href="/web/products.php?lang=en&category_id=<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="/web/about.php?lang=en" class="<?php echo ($currentPage ?? '') === 'about' ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="/web/news.php?lang=en" class="<?php echo ($currentPage ?? '') === 'news' ? 'active' : ''; ?>">News</a></li>
                    <li><a href="/web/contact.php?lang=en" class="<?php echo ($currentPage ?? '') === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
            </nav>

            <!-- Right side: Search & Language -->
            <div class="header-right">
                <!-- Search -->
                <div class="search-box">
                    <form action="/web/search.php" method="get">
                        <input type="hidden" name="lang" value="en">
                        <input type="text" name="keyword" placeholder="Search products...">
                        <button type="submit"><i class="icon-search"></i></button>
                    </form>
                </div>

                <!-- Language Switcher -->
                <div class="lang-switcher">
                    <span class="current-lang">English</span>
                    <div class="lang-dropdown">
                        <a href="<?php echo getSwitchLangUrl('zh'); ?>">中文</a>
                        <a href="<?php echo getSwitchLangUrl('en'); ?>" class="active">English</a>
                    </div>
                </div>

                <!-- Mobile menu toggle -->
                <button class="mobile-menu-toggle" aria-label="Menu">
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
            <span>Navigation</span>
            <button class="mobile-nav-close">&times;</button>
        </div>
        <ul class="mobile-nav-list">
            <li><a href="/web/index.php?lang=en">Home</a></li>
            <li><a href="/web/products.php?lang=en">Products</a></li>
            <li><a href="/web/about.php?lang=en">About Us</a></li>
            <li><a href="/web/news.php?lang=en">News</a></li>
            <li><a href="/web/contact.php?lang=en">Contact</a></li>
        </ul>
        <div class="mobile-lang-switch">
            <a href="<?php echo getSwitchLangUrl('zh'); ?>">中文</a>
            <a href="<?php echo getSwitchLangUrl('en'); ?>" class="active">English</a>
        </div>
    </nav>

    <!-- Main Content Start -->
    <main class="main-content">

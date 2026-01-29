<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? e($page_title) . ' - ' : ''; ?><?php echo SITE_NAME_EN; ?></title>
    <?php include __DIR__ . '/styles.php'; ?>
    <style>
        .lang-switch {
            margin-left: 20px;
            padding: 6px 12px;
            background: transparent;
            border: 1px solid var(--nav-text);
            color: var(--nav-text);
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .lang-switch:hover {
            background: var(--nav-text);
            color: var(--nav-bg);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php?lang=en" class="logo"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106054727176769284701495.png" alt="<?php echo SITE_NAME_EN; ?>"></a>

            <ul class="nav-menu">
                <li><a href="index.php?lang=en"><?php echo __('nav_home'); ?></a></li>
                <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_ENTERPRISE; ?>&lang=en"><?php echo __('nav_enterprise'); ?></a></li>
                <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_QUALITY; ?>&lang=en"><?php echo __('nav_quality'); ?></a></li>
                <li><a href="teahouse-detail.php?lang=en"><?php echo __('nav_teahouse'); ?></a></li>
                <li><a href="products.php?lang=en"><?php echo __('nav_products'); ?></a></li>
                <li><a href="news.php?lang=en"><?php echo __('nav_news'); ?></a></li>
                <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_CONTACT; ?>&lang=en"><?php echo __('nav_contact'); ?></a></li>
                <li><a href="<?php echo getLangSwitchUrl('zh'); ?>" class="lang-switch">中文</a></li>
            </ul>
        </div>
    </nav>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? e($page_title) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
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
    <!-- 导航栏 -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106054727176769284701495.png" alt="<?php echo SITE_NAME; ?>"></a>

            <ul class="nav-menu">
                <li><a href="index.php">首页</a></li>
                <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_ENTERPRISE; ?>">企业理念</a></li>
                <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_QUALITY; ?>">品质观念</a></li>
                <li><a href="teahouse-detail.php">会客厅</a></li>
                <li><a href="products.php">产品中心</a></li>
                <li><a href="news.php">企业资讯</a></li>
                <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_CONTACT; ?>">联系我们</a></li>
                <li><a href="<?php echo getLangSwitchUrl('en'); ?>" class="lang-switch">EN</a></li>
            </ul>
        </div>
    </nav>

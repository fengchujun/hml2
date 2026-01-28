<?php
require_once 'api.php';

// 设置页面标题
$page_title = '会客厅';

// 获取会客厅列表
$teahouseResult = getTeahouseList(10);
$teahouses = $teahouseResult['data'] ?? [];

// 包含头部
include 'templates/header.php';
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

    .teahouse-section {
        margin-bottom: 80px;
        padding: 50px;
        background: var(--card-bg);
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
    }

    .teahouse-header {
        text-align: center;
        margin-bottom: 50px;
        padding-bottom: 30px;
        border-bottom: 3px solid var(--accent-line);
    }

    .teahouse-title {
        font-size: 42px;
        color: var(--title-color);
        margin-bottom: 20px;
        font-weight: bold;
    }

    .teahouse-subtitle {
        font-size: 20px;
        color: #666;
        line-height: 1.8;
    }

    .teahouse-image-section {
        margin: 40px 0;
        text-align: center;
    }

    .teahouse-image-section img {
        max-width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .teahouse-content {
        font-size: 18px;
        line-height: 2.2;
        color: #555;
    }

    .teahouse-content p {
        margin-bottom: 25px;
        text-indent: 2em;
        text-align: justify;
    }

    .teahouse-content img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
        border-radius: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        margin: 40px 0;
    }

    .info-card {
        background: var(--bg-section-1);
        padding: 30px;
        border-radius: 12px;
        border-left: 5px solid var(--accent-line);
    }

    .info-card h3 {
        font-size: 20px;
        color: var(--title-color);
        margin-bottom: 15px;
    }

    .info-card p {
        font-size: 16px;
        line-height: 1.8;
        color: #666;
        text-indent: 0;
    }

    .reserve-section {
        background: var(--bg-section-1);
        padding: 40px;
        border-radius: 15px;
        text-align: center;
        margin-top: 50px;
    }

    .reserve-btn {
        padding: 18px 50px;
        background: var(--accent-line);
        color: #fff;
        border: none;
        border-radius: 30px;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 20px;
    }

    .reserve-btn:hover {
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

        .info-grid {
            grid-template-columns: 1fr;
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

        .teahouse-section {
            padding: 30px 20px;
        }
    }
</style>

<!-- 主容器 -->
<div class="main-container">
    <!-- 左侧导航 -->
    <aside class="sidebar">
        <h2 class="sidebar-title">会客厅</h2>
        <ul class="sidebar-nav">
            <?php foreach ($teahouses as $index => $teahouse): ?>
            <li>
                <a href="#teahouse<?php echo $index + 1; ?>" class="<?php echo $index == 0 ? 'active' : ''; ?>">
                    <?php echo e($teahouse['note_title']); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- 内容区域 -->
    <main class="content-area">
        <?php foreach ($teahouses as $index => $teahouse): ?>
        <section id="teahouse<?php echo $index + 1; ?>" class="teahouse-section">
            <div class="teahouse-header">
                <h1 class="teahouse-title"><?php echo e($teahouse['note_title']); ?></h1>
                <?php if (!empty($teahouse['note_abstract'])): ?>
                <p class="teahouse-subtitle"><?php echo e($teahouse['note_abstract']); ?></p>
                <?php endif; ?>
            </div>

            <div class="teahouse-image-section">
                <img src="<?php echo e($teahouse['cover_img']); ?>" alt="<?php echo e($teahouse['note_title']); ?>">
            </div>

            <div class="teahouse-content">
                <?php echo $teahouse['note_content']; ?>

                <div class="info-grid">


                    <?php if (!empty($teahouse['phone'])): ?>
                    <div class="info-card">
                        <h3>📞 联系电话</h3>
                        <p><?php echo e($teahouse['phone']); ?></p>
                    </div>
                    <?php endif; ?>



                    <?php if ($teahouse['support_reservation'] == 1): ?>
                    <div class="info-card">
                        <h3>✅ 预约服务</h3>
                        <p>支持线上预约,扫码进入小程序即可预约</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($teahouse['support_reservation'] == 1): ?>
            <div class="reserve-section">
                <h3 style="font-size: 24px; color: var(--title-color); margin-bottom: 15px;">预约<?php echo e($teahouse['note_title']); ?></h3>
                <p style="color: #666; font-size: 16px;">扫码进入小程序,选择您方便的时间,我们将为您准备最佳的品茗体验</p>
                <button class="reserve-btn" onclick="showQRCode('reserve')">立即预约</button>
            </div>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>

        <?php if (empty($teahouses)): ?>
        <section class="teahouse-section">
            <div class="teahouse-header">
                <h1 class="teahouse-title">暂无会客厅信息</h1>
                <p class="teahouse-subtitle">敬请期待</p>
            </div>
        </section>
        <?php endif; ?>
    </main>
</div>

<script>
    // 侧边栏导航激活状态
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.teahouse-section');
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

<?php include 'templates/footer.php'; ?>

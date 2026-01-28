    </main>
    <!-- Main Content End -->

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-container">
                <!-- About Section -->
                <div class="footer-section">
                    <h4>关于华木兰</h4>
                    <p>华木兰茶业，专注于云南古树茶的研发与推广，致力于将优质的普洱茶、红茶、白茶带给每一位茶友。</p>
                    <div class="footer-logo">
                        <img src="/web/assets/images/logo-white.png" alt="华木兰茶业">
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h4>快速链接</h4>
                    <ul class="footer-links">
                        <li><a href="/web/index.php?lang=zh">首页</a></li>
                        <li><a href="/web/products.php?lang=zh">产品中心</a></li>
                        <li><a href="/web/about.php?lang=zh">关于我们</a></li>
                        <li><a href="/web/news.php?lang=zh">新闻动态</a></li>
                        <li><a href="/web/contact.php?lang=zh">联系我们</a></li>
                    </ul>
                </div>

                <!-- Product Categories -->
                <div class="footer-section">
                    <h4>产品分类</h4>
                    <ul class="footer-links">
                        <?php
                        $footerCategories = getCategories();
                        foreach ($footerCategories as $cat):
                        ?>
                        <li><a href="/web/products.php?lang=zh&category_id=<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h4>联系方式</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="icon-phone"></i>
                            <span>18948789993</span>
                        </li>
                        <li>
                            <i class="icon-email"></i>
                            <span>zypc18948789993@163.com</span>
                        </li>
                        <li>
                            <i class="icon-clock"></i>
                            <span>9:30 - 20:30</span>
                        </li>
                        <li>
                            <i class="icon-location"></i>
                            <span>深圳市龙岗区天安数码城3栋B座1002</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-container">
                <p>&copy; <?php echo date('Y'); ?> 华木兰茶业 版权所有</p>
                <p class="icp">粤ICP备xxxxxxxx号</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button class="back-to-top" aria-label="返回顶部">
        <i class="icon-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="/web/assets/js/main.js"></script>
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

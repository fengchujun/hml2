    </main>
    <!-- Main Content End -->

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-container">
                <!-- About Section -->
                <div class="footer-section">
                    <h4>About HuaMuLan</h4>
                    <p>HuaMuLan Tea is dedicated to the research and promotion of Yunnan ancient tree tea, committed to bringing high-quality Pu-erh, Black Tea, and White Tea to every tea lover.</p>
                    <div class="footer-logo">
                        <img src="/web/assets/images/logo-white.png" alt="HuaMuLan Tea">
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/web/index.php?lang=en">Home</a></li>
                        <li><a href="/web/products.php?lang=en">Products</a></li>
                        <li><a href="/web/about.php?lang=en">About Us</a></li>
                        <li><a href="/web/news.php?lang=en">News</a></li>
                        <li><a href="/web/contact.php?lang=en">Contact</a></li>
                    </ul>
                </div>

                <!-- Product Categories -->
                <div class="footer-section">
                    <h4>Product Categories</h4>
                    <ul class="footer-links">
                        <?php
                        $footerCategories = getCategories();
                        foreach ($footerCategories as $cat):
                        ?>
                        <li><a href="/web/products.php?lang=en&category_id=<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="icon-phone"></i>
                            <span>+86 189-4878-9993</span>
                        </li>
                        <li>
                            <i class="icon-email"></i>
                            <span>zypc18948789993@163.com</span>
                        </li>
                        <li>
                            <i class="icon-clock"></i>
                            <span>9:30 AM - 8:30 PM</span>
                        </li>
                        <li>
                            <i class="icon-location"></i>
                            <span>Room 1002, Building 3B, Tianan Cyber Park, Longgang, Shenzhen</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-container">
                <p>&copy; <?php echo date('Y'); ?> HuaMuLan Tea. All Rights Reserved.</p>
                <p class="icp">ICP License: xxxxxxxx</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button class="back-to-top" aria-label="Back to Top">
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

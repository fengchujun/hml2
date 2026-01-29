    <!-- Footer -->
    <footer class="footer">
        <div class="footer-main">
            <div class="footer-section">
                <h3 class="footer-section-title"><?php echo SITE_NAME_EN; ?></h3>
                <p class="footer-section-desc"><?php echo SITE_SLOGAN_EN; ?></p>
                <p class="footer-section-desc"><?php echo __('footer_desc'); ?></p>
                <div class="footer-qr">
                    <img src="<?php echo MINIPROGRAM_QR_CODE; ?>" alt="Mini Program QR Code">
                    <p><?php echo __('scan_miniprogram'); ?></p>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="footer-section-title"><?php echo __('quick_nav'); ?></h3>
                <ul class="footer-links">
                    <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_ENTERPRISE; ?>&lang=en"><?php echo __('nav_enterprise'); ?></a></li>
                    <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_QUALITY; ?>&lang=en"><?php echo __('nav_quality'); ?></a></li>
                    <li><a href="teahouse-detail.php?lang=en"><?php echo __('nav_teahouse'); ?></a></li>
                    <li><a href="products.php?lang=en"><?php echo __('nav_products'); ?></a></li>
                    <li><a href="news.php?lang=en"><?php echo __('nav_news'); ?></a></li>
                    <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_CONTACT; ?>&lang=en"><?php echo __('nav_contact'); ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-section-title"><?php echo __('contact_info'); ?></h3>
                <ul class="footer-contact">
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369553561.png"></span>
                        <div>
                            <strong><?php echo __('hotline'); ?></strong>
                            <p>18948789993</p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369555272.png"></span>
                        <div>
                            <strong><?php echo __('business_hours'); ?></strong>
                            <p><?php echo __('business_hours_value'); ?></p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369556172.png"></span>
                        <div>
                            <strong><?php echo __('email'); ?></strong>
                            <p>zypc18948789993@163.com</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-section-title"><?php echo __('address'); ?></h3>
                <ul class="footer-address">
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369554583.png"></span>
                        <div>
                            <strong><?php echo __('hq_address'); ?></strong>
                            <p><?php echo __('hq_address_value'); ?></p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p><?php echo SITE_COPYRIGHT_EN; ?></p>
            <p>ICP: 2026002417</p>
        </div>
    </footer>

    <!-- QR Code Modal -->
    <div class="modal" id="qrModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeQRCode()">×</span>
            <h2 class="qr-title" id="qrTitle"><?php echo __('scan_miniprogram'); ?></h2>
            <div class="qr-code">
                <img src="<?php echo MINIPROGRAM_QR_CODE; ?>" alt="Mini Program QR Code">
            </div>
            <p class="qr-hint" id="qrHint"><?php echo __('qr_hint'); ?></p>
        </div>
    </div>

    <script>
        // Show QR Code Modal
        function showQRCode(type) {
            const modal = document.getElementById('qrModal');
            const title = document.getElementById('qrTitle');
            const hint = document.getElementById('qrHint');

            const messages = {
                'order': {
                    title: '<?php echo __('qr_order_title'); ?>',
                    hint: '<?php echo __('qr_order_hint'); ?>'
                },
                'reserve': {
                    title: '<?php echo __('qr_reserve_title'); ?>',
                    hint: '<?php echo __('qr_reserve_hint'); ?>'
                },
                'contact': {
                    title: '<?php echo __('qr_contact_title'); ?>',
                    hint: '<?php echo __('qr_contact_hint'); ?>'
                },
                'product': {
                    title: '<?php echo __('qr_product_title'); ?>',
                    hint: '<?php echo __('qr_product_hint'); ?>'
                }
            };

            const message = messages[type] || messages['order'];
            title.textContent = message.title;
            hint.textContent = message.hint;

            modal.classList.add('active');
        }

        // Close QR Code Modal
        function closeQRCode() {
            const modal = document.getElementById('qrModal');
            modal.classList.remove('active');
        }

        // Close modal on outside click
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRCode();
            }
        });
    </script>
</body>
</html>

    <!-- 页脚 -->
    <footer class="footer">
        <div class="footer-main">
            <div class="footer-section">
                <h3 class="footer-section-title"><?php echo SITE_NAME; ?></h3>
                <p class="footer-section-desc"><?php echo SITE_SLOGAN; ?></p>
                <p class="footer-section-desc">致力于为您提供高品质的茶叶产品和专业的茶文化服务</p>
                <div class="footer-qr">
                    <img src="<?php echo MINIPROGRAM_QR_CODE; ?>" alt="小程序二维码">
                    <p>扫码进入小程序</p>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="footer-section-title">快速导航</h3>
                <ul class="footer-links">
                    <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_ENTERPRISE; ?>">企业理念</a></li>
                    <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_QUALITY; ?>">品质观念</a></li>
                    <li><a href="teahouse-detail.php">会客厅</a></li>
                    <li><a href="products.php">产品中心</a></li>
                    <li><a href="news.php">企业资讯</a></li>
                    <li><a href="concept-detail.php?id=<?php echo ARTICLE_ID_CONTACT; ?>">联系我们</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-section-title">联系方式</h3>
                <ul class="footer-contact">
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369553561.png"></span>
                        <div>
                            <strong>客服热线</strong>
                            <p>18948789993</p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369555272.png"></span>
                        <div>
                            <strong>营业时间</strong>
                            <p>周一至周日 9:00-20:30</p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369556172.png"></span>
                        <div>
                            <strong>联系邮箱</strong>
                            <p>zypc18948789993@163.com</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-section-title">公司地址</h3>
                <ul class="footer-address">
                    <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369554583.png"></span>
                        <div>
                            <strong>总部地址</strong>
                            <p>深圳市龙岗区天安数码城3栋B座1002</p>
                        </div>
                    </li>
                     <!-- <li>
                        <span class="contact-icon"><img src="https://hmlimg.oss-cn-shenzhen.aliyuncs.com/upload/1/common/images/20260106/20260106060135176769369556746.png"></span>
                        <div>
                            <strong>体验店</strong>
                            <p>深圳市南山区XXX商业中心X栋X层</p>
                        </div>
                    </li> -->
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p><?php echo SITE_COPYRIGHT; ?></p>
            <p>粤ICP备2026002417号 </p>
        </div>
    </footer>

    <!-- 二维码弹窗 -->
    <div class="modal" id="qrModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeQRCode()">×</span>
            <h2 class="qr-title" id="qrTitle">扫码进入小程序</h2>
            <div class="qr-code">
                <img src="<?php echo MINIPROGRAM_QR_CODE; ?>" alt="小程序二维码">
            </div>
            <p class="qr-hint" id="qrHint">请使用微信扫描二维码</p>
        </div>
    </div>

    <script>
        // 显示二维码弹窗
        function showQRCode(type) {
            const modal = document.getElementById('qrModal');
            const title = document.getElementById('qrTitle');
            const hint = document.getElementById('qrHint');

            const messages = {
                'order': {
                    title: '扫码立即选购',
                    hint: '扫描二维码进入小程序,选购您喜爱的茶品'
                },
                'reserve': {
                    title: '扫码预约会客厅',
                    hint: '扫描二维码进入小程序,预约专属会客空间'
                },
                'contact': {
                    title: '扫码联系我们',
                    hint: '扫描二维码进入小程序,获取联系方式'
                },
                'product': {
                    title: '扫码查看产品详情',
                    hint: '扫描二维码进入小程序,了解产品详情并下单'
                }
            };

            const message = messages[type] || messages['order'];
            title.textContent = message.title;
            hint.textContent = message.hint;

            modal.classList.add('active');
        }

        // 关闭二维码弹窗
        function closeQRCode() {
            const modal = document.getElementById('qrModal');
            modal.classList.remove('active');
        }

        // 点击弹窗外部关闭
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRCode();
            }
        });
    </script>
</body>
</html>

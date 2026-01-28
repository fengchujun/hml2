<?php
/**
 * Contact Page - HuaMuLan Tea PC Website
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/includes/template.php';

// Page settings
$currentPage = 'contact';
$pageTitle = __('nav_contact');

includeHeader();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php _e('contact_title'); ?></h1>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo langUrl('/web/index.php'); ?>"><?php _e('nav_home'); ?></a>
        <span>/</span>
        <span><?php _e('nav_contact'); ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-wrapper" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl);">
            <!-- Contact Information -->
            <div class="contact-info">
                <h2 style="font-size: 1.5rem; margin-bottom: var(--spacing-lg); color: var(--color-primary);">
                    <?php _e('footer_contact'); ?>
                </h2>

                <div class="contact-items">
                    <div class="contact-item" style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-lg); padding: var(--spacing-md); background: var(--color-bg-light); border-radius: var(--radius-md);">
                        <div style="width: 50px; height: 50px; background: var(--color-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="icon-phone"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php _e('contact_phone'); ?></h4>
                            <p style="color: var(--color-text-light);">+86 189-4878-9993</p>
                        </div>
                    </div>

                    <div class="contact-item" style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-lg); padding: var(--spacing-md); background: var(--color-bg-light); border-radius: var(--radius-md);">
                        <div style="width: 50px; height: 50px; background: var(--color-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="icon-email"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php _e('contact_email'); ?></h4>
                            <p style="color: var(--color-text-light);">zypc18948789993@163.com</p>
                        </div>
                    </div>

                    <div class="contact-item" style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-lg); padding: var(--spacing-md); background: var(--color-bg-light); border-radius: var(--radius-md);">
                        <div style="width: 50px; height: 50px; background: var(--color-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="icon-clock"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php _e('contact_hours'); ?></h4>
                            <p style="color: var(--color-text-light);">
                                <?php echo isEnglish() ? '9:30 AM - 8:30 PM' : '9:30 - 20:30'; ?>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item" style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-lg); padding: var(--spacing-md); background: var(--color-bg-light); border-radius: var(--radius-md);">
                        <div style="width: 50px; height: 50px; background: var(--color-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="icon-location"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php _e('contact_address'); ?></h4>
                            <p style="color: var(--color-text-light);">
                                <?php echo isEnglish()
                                    ? 'Room 1002, Building 3B, Tianan Cyber Park, Longgang District, Shenzhen, China'
                                    : '深圳市龙岗区天安数码城3栋B座1002'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WeChat QR Code / Contact Form -->
            <div class="contact-qr" style="text-align: center;">
                <h2 style="font-size: 1.5rem; margin-bottom: var(--spacing-lg); color: var(--color-primary);">
                    <?php echo isEnglish() ? 'Scan to Contact' : '扫码联系我们'; ?>
                </h2>

                <div style="background: var(--color-bg-light); padding: var(--spacing-xl); border-radius: var(--radius-lg);">
                    <div style="width: 200px; height: 200px; background: #fff; margin: 0 auto var(--spacing-md); display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <span style="color: var(--color-text-lighter);">
                            <?php echo isEnglish() ? 'WeChat QR Code' : '微信二维码'; ?>
                        </span>
                    </div>
                    <p style="color: var(--color-text-light);">
                        <?php echo isEnglish()
                            ? 'Scan with WeChat to get in touch'
                            : '使用微信扫一扫，添加客服'; ?>
                    </p>
                </div>

                <!-- Mini Program -->
                <div style="background: var(--color-bg-light); padding: var(--spacing-xl); border-radius: var(--radius-lg); margin-top: var(--spacing-lg);">
                    <h3 style="margin-bottom: var(--spacing-md);">
                        <?php echo isEnglish() ? 'Visit Our Mini Program' : '访问我们的小程序'; ?>
                    </h3>
                    <div style="width: 150px; height: 150px; background: #fff; margin: 0 auto var(--spacing-md); display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <span style="color: var(--color-text-lighter);">
                            <?php echo isEnglish() ? 'Mini Program QR' : '小程序码'; ?>
                        </span>
                    </div>
                    <p style="color: var(--color-text-light); font-size: 0.9rem;">
                        <?php echo isEnglish()
                            ? 'Scan to visit our WeChat Mini Program'
                            : '扫码进入华木兰茶业小程序'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@media (max-width: 768px) {
    .contact-wrapper {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php includeFooter(); ?>

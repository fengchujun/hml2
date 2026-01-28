<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Microsoft YaHei', 'SimSun', serif;
        background-color: #ffffff;
        color: #333;
        overflow-x: hidden;
    }

    /* CSS变量定义 */
    :root {
        --nav-bg: #2d1f1a;
        --nav-text: #d4af76;
        --nav-hover: #e8d4a8;
        --content-bar: linear-gradient(135deg, #3a2a24 0%, #2d1f1a 100%);
        --content-text: #d4af76;
        --bg-section-1: linear-gradient(135deg, #f9f7f4 0%, #f5f2ed 100%);
        --bg-section-2: linear-gradient(135deg, #f7f5f0 0%, #ebe8e0 100%);
        --bg-section-3: linear-gradient(135deg, #faf8f3 0%, #f0ede5 100%);
        --title-color: #2d1f1a;
        --border-color: #e8e5df;
        --card-bg: #ffffff;
        --accent-line: #d4af76;
        --qr-bg: #faf8f5;
        --sidebar-bg: #faf8f5;
        --sidebar-active: #d4af76;
    }

    /* 导航栏 */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background: var(--nav-bg);
        padding: 20px 0;
        z-index: 1000;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 50px;
    }

    .logo {
        display: inline-block;
        height: 50px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .logo img {
        height: 100%;
        width: auto;
        display: block;
        object-fit: contain;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 40px;
    }

    .nav-menu a {
        color: var(--nav-text);
        text-decoration: none;
        font-size: 16px;
        transition: all 0.3s;
        position: relative;
    }

    .nav-menu a:hover {
        color: var(--nav-hover);
    }

    .nav-menu a::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--nav-text);
        transition: width 0.3s;
    }

    .nav-menu a:hover::after {
        width: 100%;
    }

    /* 二维码弹窗 */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: var(--card-bg);
        padding: 50px;
        border: 3px solid var(--accent-line);
        text-align: center;
        position: relative;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border-radius: 15px;
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 30px;
        cursor: pointer;
        color: var(--nav-bg);
        transition: all 0.3s;
    }

    .close-btn:hover {
        transform: rotate(90deg);
        color: var(--accent-line);
    }

    .qr-title {
        font-size: 28px;
        margin-bottom: 20px;
        letter-spacing: 3px;
        color: var(--title-color);
    }

    .qr-code {
        width: 250px;
        height: 250px;
        margin: 30px auto;
        background: var(--qr-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--accent-line);
        border-radius: 10px;
    }

    .qr-code img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .qr-hint {
        font-size: 16px;
        color: #666;
        margin-top: 20px;
    }

    /* 页脚 */
    .footer {
        background: var(--content-bar);
        color: var(--content-text);
        padding: 60px 50px 20px;
    }

    .footer-main {
        max-width: 1400px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 50px;
        padding-bottom: 40px;
        border-bottom: 1px solid rgba(212, 175, 118, 0.3);
    }

    .footer-section {
        text-align: left;
    }

    .footer-section-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        letter-spacing: 2px;
        color: var(--nav-text);
    }

    .footer-section-desc {
        font-size: 14px;
        line-height: 1.8;
        color: rgba(212, 175, 118, 0.8);
        margin-bottom: 10px;
    }

    .footer-qr {
        margin-top: 20px;
        text-align: center;
    }

    .footer-qr img {
        width: 120px;
        height: 120px;
        border: 2px solid var(--accent-line);
        border-radius: 8px;
        padding: 5px;
        background: #fff;
    }

    .footer-qr p {
        font-size: 13px;
        margin-top: 10px;
        color: rgba(212, 175, 118, 0.9);
    }

    .footer-links {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: rgba(212, 175, 118, 0.8);
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }

    .footer-links a:hover {
        color: var(--nav-text);
        padding-left: 5px;
    }

    .footer-contact,
    .footer-address {
        list-style: none;
    }

    .footer-contact li,
    .footer-address li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 12px;
    }

    .contact-icon {
        font-size: 20px;
        margin-top: 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        min-height: 24px;
    }

    .contact-icon img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        display: block;
    }

    .footer-contact strong,
    .footer-address strong {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
        color: var(--nav-text);
    }

    .footer-contact p,
    .footer-address p {
        font-size: 13px;
        color: rgba(212, 175, 118, 0.8);
        line-height: 1.6;
    }

    .footer-bottom {
        max-width: 1400px;
        margin: 0 auto;
        text-align: center;
        padding-top: 30px;
    }

    .footer-bottom p {
        font-size: 13px;
        color: rgba(212, 175, 118, 0.7);
        margin: 5px 0;
    }

    /* 响应式 */
    @media (max-width: 1200px) {
        .footer-main {
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }
    }

    @media (max-width: 768px) {
        .nav-menu {
            gap: 20px;
            font-size: 14px;
        }

        .nav-container {
            padding: 0 20px;
        }

        .footer {
            padding: 40px 20px 20px;
        }

        .footer-main {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .footer-section {
            text-align: center;
        }

        .footer-contact li,
        .footer-address li {
            justify-content: center;
        }
    }
</style>
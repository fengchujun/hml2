/**
 * HuaMuLan Tea - Main JavaScript
 */

(function() {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initMobileMenu();
        initBackToTop();
        initLanguageDetection();
        initSmoothScroll();
    });

    /**
     * Mobile Menu
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileNav = document.querySelector('.mobile-nav');
        const overlay = document.querySelector('.mobile-nav-overlay');
        const closeBtn = document.querySelector('.mobile-nav-close');

        if (!menuToggle || !mobileNav) return;

        function openMenu() {
            mobileNav.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            mobileNav.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        const backToTop = document.querySelector('.back-to-top');
        if (!backToTop) return;

        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        // Scroll to top on click
        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Language Detection - Auto redirect based on browser language
     * Only for first-time visitors (no cookie set)
     */
    function initLanguageDetection() {
        // Check if language cookie exists
        if (getCookie('site_lang')) {
            return; // Already has language preference
        }

        // Check URL for lang parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('lang')) {
            return; // Language specified in URL
        }

        // Detect browser language
        const browserLang = navigator.language || navigator.userLanguage;
        const isEnglish = browserLang.toLowerCase().startsWith('en');
        const isChinese = browserLang.toLowerCase().startsWith('zh');

        // Determine target language
        let targetLang = 'zh'; // Default to Chinese
        if (isEnglish) {
            targetLang = 'en';
        }

        // Check if current page language matches detected language
        const htmlLang = document.documentElement.lang;
        const currentLang = htmlLang.startsWith('en') ? 'en' : 'zh';

        // Redirect if needed
        if (targetLang !== currentLang) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('lang', targetLang);
            window.location.href = currentUrl.toString();
        }
    }

    /**
     * Smooth Scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Cookie Helper Functions
     */
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }

    /**
     * Language Switch Function (can be called from onclick)
     */
    window.switchLanguage = function(lang) {
        setCookie('site_lang', lang, 365);
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('lang', lang);
        window.location.href = currentUrl.toString();
    };

})();

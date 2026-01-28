<?php
/**
 * Template Helper Functions
 */

/**
 * Include the appropriate header based on language
 */
function includeHeader() {
    global $lang;
    if ($lang === 'en') {
        include __DIR__ . '/header_en.php';
    } else {
        include __DIR__ . '/header.php';
    }
}

/**
 * Include the appropriate footer based on language
 */
function includeFooter() {
    global $lang;
    if ($lang === 'en') {
        include __DIR__ . '/footer_en.php';
    } else {
        include __DIR__ . '/footer.php';
    }
}

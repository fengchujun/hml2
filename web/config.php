<?php
/**
 * PC Web Configuration
 * Database and general settings
 */

// Database configuration (modify according to your environment)
define('DB_HOST', 'localhost');
define('DB_NAME', 'hml3');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PREFIX', 'hml_');

// Site configuration
define('SITE_NAME', 'HuaMuLan Tea');
define('SITE_NAME_CN', '华木兰茶业');
define('SITE_URL', '');  // Set your site URL

// Default language
define('DEFAULT_LANG', 'zh');
define('SUPPORTED_LANGS', ['zh', 'en']);

// Upload path
define('UPLOAD_URL', '/upload/');

/**
 * Get database connection
 */
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

/**
 * Detect browser language
 */
function detectBrowserLanguage() {
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($langs as $lang) {
            $lang = strtolower(trim(explode(';', $lang)[0]));
            // Check for English
            if (strpos($lang, 'en') === 0) {
                return 'en';
            }
            // Check for Chinese
            if (strpos($lang, 'zh') === 0) {
                return 'zh';
            }
        }
    }
    return DEFAULT_LANG;
}

/**
 * Get current language
 * Priority: URL param > Cookie > Browser > Default
 */
function getCurrentLang() {
    // 1. Check URL parameter
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS)) {
        $lang = $_GET['lang'];
        // Save to cookie
        setcookie('site_lang', $lang, time() + 365 * 24 * 60 * 60, '/');
        return $lang;
    }

    // 2. Check cookie
    if (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], SUPPORTED_LANGS)) {
        return $_COOKIE['site_lang'];
    }

    // 3. Detect browser language and save to cookie
    $lang = detectBrowserLanguage();
    setcookie('site_lang', $lang, time() + 365 * 24 * 60 * 60, '/');
    return $lang;
}

/**
 * Get field name based on current language
 */
function getLangField($field, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLang();
    }
    return $lang === 'en' ? $field . '_en' : $field;
}

/**
 * Get value with fallback
 * If English value is empty, fallback to Chinese
 */
function getLangValue($row, $field, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLang();
    }

    if ($lang === 'en') {
        $enField = $field . '_en';
        if (!empty($row[$enField])) {
            return $row[$enField];
        }
    }

    return $row[$field] ?? '';
}

/**
 * Build URL with language parameter
 */
function langUrl($url, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLang();
    }

    $separator = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $separator . 'lang=' . $lang;
}

/**
 * Get switch language URL
 */
function getSwitchLangUrl($targetLang) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    // Remove existing lang parameter
    $currentUrl = preg_replace('/([?&])lang=[^&]+(&|$)/', '$1', $currentUrl);
    $currentUrl = rtrim($currentUrl, '?&');

    $separator = (strpos($currentUrl, '?') !== false) ? '&' : '?';
    return $currentUrl . $separator . 'lang=' . $targetLang;
}

// Initialize language
$GLOBALS['current_lang'] = getCurrentLang();

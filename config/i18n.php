<?php
/**
 * config/i18n.php
 * Internationalization (i18n) Support
 * Supports: English (EN), Español (ES), العربية (AR), हिन्दी (HI), Français (FR)
 */

// Supported languages
$SUPPORTED_LANGUAGES = [
    'en' => ['name' => 'English', 'flag' => '🇬🇧', 'dir' => 'ltr', 'code' => 'GB'],
    'es' => ['name' => 'Español', 'flag' => '🇪🇸', 'dir' => 'ltr', 'code' => 'ES'],
    'ar' => ['name' => 'العربية', 'flag' => '🇸🇦', 'dir' => 'rtl', 'code' => 'SA'],
    'hi' => ['name' => 'हिन्दी', 'flag' => '🇮🇳', 'dir' => 'ltr', 'code' => 'IN'],
    'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'dir' => 'ltr', 'code' => 'FR']
];

// Get current language from URL, session, or browser
$current_lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';

// Validate language
if (!in_array($current_lang, array_keys($SUPPORTED_LANGUAGES))) {
    $current_lang = 'en';
}

// Store in session
$_SESSION['lang'] = $current_lang;

// Load translation file
$lang_file = __DIR__ . "/lang/{$current_lang}.json";
$translations = [];

if (file_exists($lang_file)) {
    $json_content = file_get_contents($lang_file);
    $translations = json_decode($json_content, true) ?? [];
}

// Translation helper function
function t($key, $default = '') {
    global $translations;
    return $translations[$key] ?? $default ?? $key;
}

// Get all supported languages
function getSupportedLanguages() {
    global $SUPPORTED_LANGUAGES;
    return $SUPPORTED_LANGUAGES;
}

// Get current language
function getCurrentLanguage() {
    global $current_lang;
    return $current_lang;
}

// Get current language info
function getCurrentLanguageInfo() {
    global $SUPPORTED_LANGUAGES, $current_lang;
    return $SUPPORTED_LANGUAGES[$current_lang] ?? $SUPPORTED_LANGUAGES['en'];
}

// Get language URL
function getLanguageUrl($lang) {
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Remove existing lang parameter
    $url = preg_replace('/[?&]lang=[a-z]{2}/', '', $current_url);
    
    // Add new lang parameter
    $separator = strpos($url, '?') === false ? '?' : '&';
    return $url . $separator . 'lang=' . $lang;
}

// Format date based on language
function formatDate($date, $format = 'short') {
    $lang = getCurrentLanguage();
    $timestamp = strtotime($date);
    
    if ($format === 'short') {
        if ($lang === 'am' || $lang === 'ti') {
            return date('d/m/Y', $timestamp);
        }
        return date('m/d/Y', $timestamp);
    }
    
    if ($format === 'long') {
        if ($lang === 'am' || $lang === 'ti') {
            return date('d F Y', $timestamp);
        }
        return date('F d, Y', $timestamp);
    }
    
    return date($format, $timestamp);
}

// Format number based on language
function formatNumber($number, $decimals = 0) {
    $lang = getCurrentLanguage();
    
    if ($lang === 'fr') {
        return number_format($number, $decimals, ',', ' ');
    }
    
    return number_format($number, $decimals, '.', ',');
}

?>

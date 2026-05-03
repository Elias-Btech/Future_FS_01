/**
 * js/language-switcher.js
 * Language Switching System with RTL Support
 * Handles dynamic language switching and text updates
 */

console.log('🌐 Language Switcher loaded');

// Language configuration
const LANGUAGE_CONFIG = {
  supported: ['en', 'es', 'ar', 'hi', 'fr'],
  rtlLanguages: ['ar'],
  defaultLanguage: 'en',
  storageKey: 'portfolio_language'
};

// Cache for loaded language files
const languageCache = {};

/**
 * Load language JSON file
 * @param {string} lang - Language code (e.g., 'en', 'es', 'ar')
 * @returns {Promise<Object>} Language translations object
 */
async function loadLanguageFile(lang) {
  // Return from cache if already loaded
  if (languageCache[lang]) {
    console.log(`📦 Using cached language: ${lang}`);
    return languageCache[lang];
  }

  try {
    const response = await fetch(`/config/lang/${lang}.json`);
    
    if (!response.ok) {
      throw new Error(`Failed to load language file: ${response.status}`);
    }
    
    const translations = await response.json();
    languageCache[lang] = translations;
    console.log(`✅ Loaded language: ${lang}`);
    return translations;
  } catch (error) {
    console.error(`❌ Error loading language file for ${lang}:`, error);
    // Fallback to English if load fails
    if (lang !== LANGUAGE_CONFIG.defaultLanguage) {
      console.log(`⚠️ Falling back to ${LANGUAGE_CONFIG.defaultLanguage}`);
      return loadLanguageFile(LANGUAGE_CONFIG.defaultLanguage);
    }
    return {};
  }
}

/**
 * Update HTML direction and body class based on language
 * @param {string} lang - Language code
 */
function updatePageDirection(lang) {
  const htmlElement = document.documentElement;
  const bodyElement = document.body;
  const isRTL = LANGUAGE_CONFIG.rtlLanguages.includes(lang);

  if (isRTL) {
    // Set RTL for Arabic
    htmlElement.setAttribute('dir', 'rtl');
    htmlElement.setAttribute('lang', lang);
    bodyElement.classList.add('rtl');
    bodyElement.classList.remove('ltr');
    console.log('📝 RTL mode enabled for Arabic');
  } else {
    // Set LTR for all other languages
    htmlElement.setAttribute('dir', 'ltr');
    htmlElement.setAttribute('lang', lang);
    bodyElement.classList.add('ltr');
    bodyElement.classList.remove('rtl');
    console.log('📝 LTR mode enabled');
  }
}

/**
 * Update all text elements on the page with translations
 * @param {Object} translations - Language translations object
 */
function updatePageText(translations) {
  console.log('🔄 Updating page text...');
  
  // Get all elements with data-i18n attribute
  const elements = document.querySelectorAll('[data-i18n]');
  let updatedCount = 0;

  elements.forEach(element => {
    const key = element.getAttribute('data-i18n');
    
    if (translations[key]) {
      // Update text content
      element.textContent = translations[key];
      updatedCount++;
    } else {
      console.warn(`⚠️ Missing translation key: ${key}`);
    }
  });

  // Update page title
  if (translations.site_title) {
    document.title = translations.site_title;
  }

  // Update meta description
  const metaDescription = document.querySelector('meta[name="description"]');
  if (metaDescription && translations.site_description) {
    metaDescription.setAttribute('content', translations.site_description);
  }

  console.log(`✅ Updated ${updatedCount} text elements`);
}

/**
 * Update language switcher UI to show active language
 * @param {string} lang - Language code
 */
function updateLanguageSwitcherUI(lang) {
  const langButtons = document.querySelectorAll('.language-flag-btn');
  
  langButtons.forEach(button => {
    const buttonLang = button.getAttribute('data-lang');
    
    if (buttonLang === lang) {
      button.classList.add('active');
      button.setAttribute('aria-current', 'page');
    } else {
      button.classList.remove('active');
      button.removeAttribute('aria-current');
    }
  });

  console.log(`🎯 Language switcher UI updated for: ${lang}`);
}

/**
 * Switch language and update entire page
 * @param {string} lang - Language code to switch to
 */
async function switchLanguage(lang) {
  // Validate language code
  if (!LANGUAGE_CONFIG.supported.includes(lang)) {
    console.error(`❌ Unsupported language: ${lang}`);
    return false;
  }

  try {
    console.log(`🌍 Switching to language: ${lang}`);
    
    // Load language file
    const translations = await loadLanguageFile(lang);
    
    // Update page direction (RTL/LTR)
    updatePageDirection(lang);
    
    // Update all text elements
    updatePageText(translations);
    
    // Update language switcher UI
    updateLanguageSwitcherUI(lang);
    
    // Save language preference to localStorage
    localStorage.setItem(LANGUAGE_CONFIG.storageKey, lang);
    
    // Dispatch custom event for other scripts to listen to
    window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    
    console.log(`✅ Language switched successfully to: ${lang}`);
    return true;
  } catch (error) {
    console.error(`❌ Error switching language:`, error);
    return false;
  }
}

/**
 * Get current language
 * @returns {string} Current language code
 */
function getCurrentLanguage() {
  return localStorage.getItem(LANGUAGE_CONFIG.storageKey) || LANGUAGE_CONFIG.defaultLanguage;
}

/**
 * Initialize language switcher
 * Sets up event listeners and loads saved language preference
 */
function initLanguageSwitcher() {
  console.log('⚙️ Initializing language switcher...');

  // Get saved language or use default
  const savedLanguage = getCurrentLanguage();
  
  // Set initial page direction and language
  updatePageDirection(savedLanguage);
  updateLanguageSwitcherUI(savedLanguage);

  // Load and apply saved language
  loadLanguageFile(savedLanguage).then(translations => {
    updatePageText(translations);
  });

  // Attach click handlers to language flag buttons (not links)
  const langButtons = document.querySelectorAll('.language-flag-btn');
  
  langButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      
      // Extract language code from button
      let lang = button.getAttribute('data-lang');

      if (lang && LANGUAGE_CONFIG.supported.includes(lang)) {
        switchLanguage(lang);
      }
    });
  });

  console.log(`✅ Language switcher initialized with language: ${savedLanguage}`);
}

/**
 * Get translation for a specific key
 * Useful for JavaScript-generated content
 * @param {string} key - Translation key
 * @returns {string} Translated text or key if not found
 */
async function getTranslation(key) {
  const lang = getCurrentLanguage();
  const translations = await loadLanguageFile(lang);
  return translations[key] || key;
}

/**
 * Batch get translations for multiple keys
 * @param {Array<string>} keys - Array of translation keys
 * @returns {Promise<Object>} Object with key-value pairs
 */
async function getTranslations(keys) {
  const lang = getCurrentLanguage();
  const translations = await loadLanguageFile(lang);
  const result = {};
  
  keys.forEach(key => {
    result[key] = translations[key] || key;
  });
  
  return result;
}

/**
 * Listen for language changes from other sources
 * Useful for syncing language across multiple tabs
 */
window.addEventListener('storage', (e) => {
  if (e.key === LANGUAGE_CONFIG.storageKey && e.newValue) {
    console.log('🔄 Language changed in another tab, syncing...');
    switchLanguage(e.newValue);
  }
});

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initLanguageSwitcher);
} else {
  initLanguageSwitcher();
}

// Export functions for use in other scripts
window.LanguageSwitcher = {
  switchLanguage,
  getCurrentLanguage,
  getTranslation,
  getTranslations,
  loadLanguageFile,
  LANGUAGE_CONFIG
};

console.log('✅ Language Switcher ready');

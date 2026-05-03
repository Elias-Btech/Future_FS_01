/**
 * PWA Initialization Script
 * Phase 3: PWA Enhancement & Optimization
 * 
 * Features:
 * - Service worker registration
 * - Update notifications
 * - Install prompts
 * - Offline detection
 * - Cache management
 */

class PWAManager {
  constructor() {
    this.swRegistration = null;
    this.deferredPrompt = null;
    this.isOnline = navigator.onLine;
    this.init();
  }

  /**
   * Initialize PWA features
   */
  init() {
    console.log('[PWA] Initializing PWA Manager...');
    
    // Register service worker
    this.registerServiceWorker();
    
    // Listen for online/offline events
    window.addEventListener('online', () => this.handleOnline());
    window.addEventListener('offline', () => this.handleOffline());
    
    // Listen for install prompt
    window.addEventListener('beforeinstallprompt', (e) => this.handleInstallPrompt(e));
    
    // Listen for app installed
    window.addEventListener('appinstalled', () => this.handleAppInstalled());
    
    // Check for updates periodically
    setInterval(() => this.checkForUpdates(), 60000); // Every minute
    
    console.log('[PWA] PWA Manager initialized');
  }

  /**
   * Register service worker
   */
  registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
      console.warn('[PWA] Service Workers not supported');
      return;
    }

    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js', { scope: '/' })
        .then(registration => {
          console.log('[PWA] Service Worker registered:', registration);
          this.swRegistration = registration;
          
          // Check for updates
          registration.addEventListener('updatefound', () => {
            this.handleUpdateFound(registration);
          });
        })
        .catch(error => {
          console.error('[PWA] Service Worker registration failed:', error);
        });
    });
  }

  /**
   * Handle update found
   */
  handleUpdateFound(registration) {
    const newWorker = registration.installing;
    
    newWorker.addEventListener('statechange', () => {
      if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
        // New service worker available
        console.log('[PWA] New service worker available');
        this.showUpdateNotification();
      }
    });
  }

  /**
   * Show update notification
   */
  showUpdateNotification() {
    // Create notification element
    const notification = document.createElement('div');
    notification.id = 'pwa-update-notification';
    notification.style.cssText = `
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: linear-gradient(135deg, #2563EB, #14B8A6);
      color: white;
      padding: 16px 24px;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      z-index: 9999;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 12px;
      animation: slideIn 0.3s ease;
    `;
    
    notification.innerHTML = `
      <span>✨ New version available!</span>
      <button id="pwa-update-btn" style="
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s;
      ">Update</button>
      <button id="pwa-dismiss-btn" style="
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
      ">✕</button>
    `;
    
    document.body.appendChild(notification);
    
    // Add animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    `;
    document.head.appendChild(style);
    
    // Handle update button
    document.getElementById('pwa-update-btn').addEventListener('click', () => {
      this.updateServiceWorker();
      notification.remove();
    });
    
    // Handle dismiss button
    document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
      notification.remove();
    });
    
    // Auto-dismiss after 10 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove();
      }
    }, 10000);
  }

  /**
   * Update service worker
   */
  updateServiceWorker() {
    if (!this.swRegistration) return;
    
    console.log('[PWA] Updating service worker...');
    
    // Tell service worker to skip waiting
    const newWorker = this.swRegistration.waiting;
    if (newWorker) {
      newWorker.postMessage({ type: 'SKIP_WAITING' });
      
      // Reload page when new service worker takes over
      let refreshing = false;
      navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (!refreshing) {
          refreshing = true;
          window.location.reload();
        }
      });
    }
  }

  /**
   * Check for updates
   */
  checkForUpdates() {
    if (!this.swRegistration) return;
    
    this.swRegistration.update().catch(error => {
      console.warn('[PWA] Update check failed:', error);
    });
  }

  /**
   * Handle install prompt
   */
  handleInstallPrompt(e) {
    console.log('[PWA] Install prompt triggered');
    
    // Prevent the mini-infobar from appearing
    e.preventDefault();
    
    // Store the event for later use
    this.deferredPrompt = e;
    
    // Show install button if not already installed
    this.showInstallButton();
  }

  /**
   * Show install button
   */
  showInstallButton() {
    // Check if app is already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
      console.log('[PWA] App is already installed');
      return;
    }
    
    // Create install button
    const installBtn = document.createElement('button');
    installBtn.id = 'pwa-install-btn';
    installBtn.textContent = '⬇️ Install App';
    installBtn.style.cssText = `
      position: fixed;
      bottom: 20px;
      left: 20px;
      background: linear-gradient(135deg, #2563EB, #14B8A6);
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 10px;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      font-size: 14px;
      box-shadow: 0 4px 14px rgba(37,99,235,0.3);
      transition: all 0.3s;
      z-index: 9998;
    `;
    
    installBtn.addEventListener('mouseover', () => {
      installBtn.style.transform = 'translateY(-2px)';
      installBtn.style.boxShadow = '0 6px 20px rgba(37,99,235,0.4)';
    });
    
    installBtn.addEventListener('mouseout', () => {
      installBtn.style.transform = 'translateY(0)';
      installBtn.style.boxShadow = '0 4px 14px rgba(37,99,235,0.3)';
    });
    
    installBtn.addEventListener('click', () => this.promptInstall());
    
    document.body.appendChild(installBtn);
  }

  /**
   * Prompt install
   */
  promptInstall() {
    if (!this.deferredPrompt) return;
    
    console.log('[PWA] Prompting install...');
    
    // Show the install prompt
    this.deferredPrompt.prompt();
    
    // Wait for user response
    this.deferredPrompt.userChoice.then(choiceResult => {
      if (choiceResult.outcome === 'accepted') {
        console.log('[PWA] User accepted install');
      } else {
        console.log('[PWA] User dismissed install');
      }
      
      this.deferredPrompt = null;
    });
  }

  /**
   * Handle online event
   */
  handleOnline() {
    console.log('[PWA] Connection restored');
    this.isOnline = true;
    
    // Remove offline indicator if present
    const offlineIndicator = document.getElementById('pwa-offline-indicator');
    if (offlineIndicator) {
      offlineIndicator.remove();
    }
    
    // Reload page to get fresh content
    if (document.hidden === false) {
      // Only reload if page is visible
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }
  }

  /**
   * Handle offline event
   */
  handleOffline() {
    console.log('[PWA] Connection lost');
    this.isOnline = false;
    
    // Show offline indicator
    this.showOfflineIndicator();
  }

  /**
   * Show offline indicator
   */
  showOfflineIndicator() {
    // Check if indicator already exists
    if (document.getElementById('pwa-offline-indicator')) {
      return;
    }
    
    const indicator = document.createElement('div');
    indicator.id = 'pwa-offline-indicator';
    indicator.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: #EF4444;
      color: white;
      padding: 12px 16px;
      text-align: center;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 600;
      z-index: 9997;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    `;
    
    indicator.textContent = '📡 You are offline - Some features may not be available';
    document.body.insertBefore(indicator, document.body.firstChild);
  }

  /**
   * Clear cache
   */
  clearCache() {
    if (!this.swRegistration) return;
    
    console.log('[PWA] Clearing cache...');
    
    const sw = this.swRegistration.active;
    if (sw) {
      sw.postMessage({ type: 'CLEAR_CACHE' });
    }
    
    // Also clear caches directly
    caches.keys().then(keys => {
      Promise.all(keys.map(key => caches.delete(key)));
    });
  }

  /**
   * Get cache info
   */
  async getCacheInfo() {
    const cacheNames = await caches.keys();
    const cacheInfo = {};
    
    for (const name of cacheNames) {
      const cache = await caches.open(name);
      const keys = await cache.keys();
      cacheInfo[name] = keys.length;
    }
    
    return cacheInfo;
  }

  /**
   * Get status
   */
  getStatus() {
    return {
      online: this.isOnline,
      swRegistered: !!this.swRegistration,
      swActive: !!this.swRegistration?.active,
      installPromptAvailable: !!this.deferredPrompt
    };
  }
}

// Initialize PWA Manager when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager = new PWAManager();
  });
} else {
  window.pwaManager = new PWAManager();
}

// Export for use in console
window.PWA = {
  getStatus: () => window.pwaManager?.getStatus(),
  getCacheInfo: () => window.pwaManager?.getCacheInfo(),
  clearCache: () => window.pwaManager?.clearCache(),
  checkForUpdates: () => window.pwaManager?.checkForUpdates(),
  updateServiceWorker: () => window.pwaManager?.updateServiceWorker()
};

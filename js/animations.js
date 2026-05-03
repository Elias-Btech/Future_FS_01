/**
 * js/animations.js
 * Premium Animation & Micro-interactions
 * Elevates portfolio to world-class level
 */

console.log('🎬 Animations.js loaded');

// ===== STAGGERED LOAD ANIMATIONS =====

/**
 * Initialize staggered load animations
 */
function initStaggeredAnimations() {
  console.log('🎬 Initializing staggered animations...');
  
  // Profile image pop-in with bounce
  const profileImage = document.querySelector('.hero-image');
  if (profileImage) {
    profileImage.style.animation = 'popInBounce 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    console.log('✅ Profile image animation applied');
  } else {
    console.warn('⚠️ Profile image not found');
  }
  
  // Hero text slide-up fade
  const heroTitle = document.querySelector('.hero h1');
  const heroSubtitle = document.querySelector('.hero .subtitle');
  const heroTagline = document.querySelector('.hero-tagline');
  
  if (heroTitle) {
    heroTitle.style.animation = 'slideUpFade 0.8s ease-out 0.2s forwards';
    heroTitle.style.opacity = '0';
    console.log('✅ Hero title animation applied');
  } else {
    console.warn('⚠️ Hero title not found');
  }
  
  if (heroSubtitle) {
    heroSubtitle.style.animation = 'slideUpFade 0.8s ease-out 0.3s forwards';
    heroSubtitle.style.opacity = '0';
    console.log('✅ Hero subtitle animation applied');
  } else {
    console.warn('⚠️ Hero subtitle not found');
  }
  
  if (heroTagline) {
    heroTagline.style.animation = 'slideUpFade 0.8s ease-out 0.4s forwards';
    heroTagline.style.opacity = '0';
    console.log('✅ Hero tagline animation applied');
  } else {
    console.warn('⚠️ Hero tagline not found');
  }
  
  // CTA buttons fade-in delayed
  const ctaButtons = document.querySelectorAll('.cta-buttons .btn');
  if (ctaButtons.length > 0) {
    ctaButtons.forEach((btn, index) => {
      btn.style.animation = `fadeIn 0.8s ease-out ${0.6 + index * 0.1}s forwards`;
      btn.style.opacity = '0';
    });
    console.log(`✅ ${ctaButtons.length} CTA buttons animation applied`);
  } else {
    console.warn('⚠️ CTA buttons not found');
  }
  
  // Social links fade-in
  const socialLinks = document.querySelectorAll('.social-links a');
  if (socialLinks.length > 0) {
    socialLinks.forEach((link, index) => {
      link.style.animation = `fadeIn 0.8s ease-out ${0.8 + index * 0.05}s forwards`;
      link.style.opacity = '0';
    });
    console.log(`✅ ${socialLinks.length} social links animation applied`);
  } else {
    console.warn('⚠️ Social links not found');
  }
}

// ===== MAGNETIC BUTTON EFFECT =====

/**
 * Add magnetic effect to buttons
 */
function initMagneticButtons() {
  const buttons = document.querySelectorAll('.btn, .sni, .tbtn, .theme-toggle');
  
  buttons.forEach(button => {
    button.addEventListener('mousemove', (e) => {
      const rect = button.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      
      const distX = e.clientX - centerX;
      const distY = e.clientY - centerY;
      
      const distance = Math.sqrt(distX * distX + distY * distY);
      const maxDistance = 100;
      
      if (distance < maxDistance) {
        const strength = (1 - distance / maxDistance) * 15;
        const moveX = (distX / distance) * strength;
        const moveY = (distY / distance) * strength;
        
        button.style.transform = `translate(${moveX}px, ${moveY}px)`;
      }
    });
    
    button.addEventListener('mouseleave', () => {
      button.style.transform = 'translate(0, 0)';
      button.style.transition = 'transform 0.3s ease-out';
    });
    
    button.addEventListener('mouseenter', () => {
      button.style.transition = 'none';
    });
  });
}

// ===== THEME TOGGLE ANIMATION =====

/**
 * Add rotation animation to theme toggle
 */
function initThemeToggleAnimation() {
  const themeToggle = document.getElementById('themeToggle');
  
  if (themeToggle) {
    themeToggle.addEventListener('click', function() {
      this.style.animation = 'none';
      // Trigger reflow to restart animation
      void this.offsetWidth;
      this.style.animation = 'spin360 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
    });
  }
}

// ===== LOGO DOT PULSE ANIMATION =====

/**
 * Add pulse animation to logo dot
 */
function initLogoPulse() {
  const logoDot = document.querySelector('.logo span');
  
  if (logoDot) {
    logoDot.style.animation = 'pulse 2s ease-in-out infinite';
  }
}

// ===== LANGUAGE SWITCHER ENHANCEMENTS =====

/**
 * Add hover and active state animations to language switcher
 */
function initLanguageSwitcherAnimations() {
  const langLinks = document.querySelectorAll('.language-switcher a');
  
  langLinks.forEach(link => {
    // Hover glow effect
    link.addEventListener('mouseenter', function() {
      this.style.boxShadow = '0 0 20px rgba(37, 99, 235, 0.5)';
      this.style.transform = 'scale(1.1)';
    });
    
    link.addEventListener('mouseleave', function() {
      if (!this.classList.contains('active')) {
        this.style.boxShadow = 'none';
        this.style.transform = 'scale(1)';
      }
    });
  });
}

// ===== SMOOTH ACTIVE STATE TRANSITION =====

/**
 * Smooth transition for active language selector
 */
function initActiveStateTransition() {
  const langLinks = document.querySelectorAll('.language-switcher a');
  
  langLinks.forEach(link => {
    link.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
  });
}

// ===== INTERSECTION OBSERVER FOR SCROLL REVEAL =====

/**
 * Initialize Intersection Observer for fade-in-up animations
 */
function initScrollReveal() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  // Observe all sections and cards
  const elementsToObserve = document.querySelectorAll(
    'section, .project-card, .skill-category, .cert-card, .testimonial-card, .about-card'
  );
  
  elementsToObserve.forEach(el => {
    observer.observe(el);
  });
}

// ===== VISUAL HIERARCHY IMPROVEMENTS =====

/**
 * Enhance visual hierarchy
 */
function enhanceVisualHierarchy() {
  const heroTitle = document.querySelector('.hero h1');
  const heroSubtitle = document.querySelector('.hero .subtitle');
  
  if (heroTitle && heroSubtitle) {
    // Make name the strongest element
    heroTitle.style.fontSize = 'clamp(2.5rem, 8vw, 3.5rem)';
    heroTitle.style.fontWeight = '900';
    heroTitle.style.letterSpacing = '-0.03em';
    
    // Reduce subtitle weight
    heroSubtitle.style.fontWeight = '600';
    heroSubtitle.style.fontSize = 'clamp(1rem, 4vw, 1.3rem)';
    heroSubtitle.style.opacity = '0.8';
  }
}

// ===== LANGUAGE SWITCHER CONTRAST FIX =====

/**
 * Improve language switcher contrast and hover states
 */
function improveLanguageSwitcherContrast() {
  const style = document.createElement('style');
  style.textContent = `
    .language-switcher a {
      position: relative;
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 700;
      text-decoration: none;
      color: var(--text-light);
      background: transparent;
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      border: 2px solid transparent;
    }
    
    [data-theme="dark"] .language-switcher a {
      color: var(--text-dark);
    }
    
    .language-switcher a:hover {
      background: rgba(37, 99, 235, 0.1);
      border-color: var(--primary);
      color: var(--primary);
      box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
      transform: translateY(-2px);
    }
    
    .language-switcher a.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
    }
  `;
  document.head.appendChild(style);
}

// ===== INITIALIZE ALL ANIMATIONS =====

/**
 * Initialize all animations on page load
 */
function initializeAllAnimations() {
  console.log('🚀 Starting animation initialization...');
  
  // Always run immediately - script is loaded at end of body
  initStaggeredAnimations();
  initMagneticButtons();
  initThemeToggleAnimation();
  initLogoPulse();
  initLanguageSwitcherAnimations();
  initActiveStateTransition();
  initScrollReveal();
  enhanceVisualHierarchy();
  improveLanguageSwitcherContrast();
  
  console.log('✅ All animations initialized successfully!');
}

// Start animations immediately
console.log('📍 Document ready state:', document.readyState);

if (document.readyState === 'loading') {
  console.log('⏳ DOM still loading, waiting for DOMContentLoaded...');
  document.addEventListener('DOMContentLoaded', () => {
    console.log('📍 DOMContentLoaded fired');
    // Small delay to ensure CSS is loaded
    setTimeout(initializeAllAnimations, 50);
  });
} else {
  console.log('✅ DOM already loaded, initializing immediately...');
  // Small delay to ensure CSS is loaded
  setTimeout(initializeAllAnimations, 50);
}

// Also run on window load to catch any late-loading elements
window.addEventListener('load', () => {
  console.log('📍 Window load event fired');
  setTimeout(initializeAllAnimations, 100);
});

// ===== EXPOSE FUNCTIONS GLOBALLY =====
// Make functions accessible from other scripts
window.initStaggeredAnimations = initStaggeredAnimations;
window.initMagneticButtons = initMagneticButtons;
window.initThemeToggleAnimation = initThemeToggleAnimation;
window.initLogoPulse = initLogoPulse;
window.initLanguageSwitcherAnimations = initLanguageSwitcherAnimations;
window.initActiveStateTransition = initActiveStateTransition;
window.initScrollReveal = initScrollReveal;
window.enhanceVisualHierarchy = enhanceVisualHierarchy;
window.improveLanguageSwitcherContrast = improveLanguageSwitcherContrast;
window.initializeAllAnimations = initializeAllAnimations;

console.log('✅ All animation functions exposed globally');

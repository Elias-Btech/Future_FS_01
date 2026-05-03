<?php
// Start session safely (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/i18n.php';

$dbProjects = [];
try {
    $dbProjects = getDB()->query('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll();
} catch (Exception $e) {
    $dbProjects = [];
}
?><!DOCTYPE html>
<html lang="<?=getCurrentLanguage()?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,viewport-fit=cover"/>
<meta name="description" content="<?=t('site_description')?>"/>
<meta property="og:title" content="<?=t('site_title')?>"/>
<meta property="og:description" content="<?=t('site_description')?>"/>
<meta property="og:image" content="/assets/elias-nobg.png"/>
<meta name="theme-color" content="#2563EB"/>
<link rel="manifest" href="/manifest.json"/>
<link rel="stylesheet" href="/css/animations.css"/>
<title><?=t('site_title')?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
<style>
:root {
  /* Primary Colors */
  --primary: #2563EB;
  --primary-dark: #1D4ED8;
  --accent: #14B8A6;
  --accent-orange: #f97316;
  --accent-purple: #a78bfa;
  
  /* Light Mode - Backgrounds */
  --bg-primary: #FFFFFF;
  --bg-secondary: #F8FAFC;
  --bg-tertiary: #F1F5F9;
  --bg-card: #FFFFFF;
  
  /* Light Mode - Text */
  --text-primary: #0F172A;
  --text-secondary: #475569;
  --text-tertiary: #64748B;
  --text-muted: #94A3B8;
  
  /* Light Mode - Borders & Dividers */
  --border-color: #E2E8F0;
  --border-light: #F1F5F9;
  
  /* Shadows */
  --shadow: 0 4px 20px rgba(0,0,0,0.08);
  --shadow-lg: 0 12px 40px rgba(0,0,0,0.12);
  
  /* Utilities */
  --radius: 12px;
  --transition: 0.3s ease;
}

/* Dark Mode Variables */
body.dark-mode {
  /* Dark Mode - Backgrounds */
  --bg-primary: #0a0a0a;
  --bg-secondary: #0F172A;
  --bg-tertiary: #1E293B;
  --bg-card: #1E293B;
  
  /* Dark Mode - Text */
  --text-primary: #F1F5F9;
  --text-secondary: #CBD5E1;
  --text-tertiary: #94A3B8;
  --text-muted: #64748B;
  
  /* Dark Mode - Borders & Dividers */
  --border-color: #334155;
  --border-light: #475569;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
  font-size: 16px;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 50%, var(--bg-tertiary) 100%);
  color: var(--text-primary);
  line-height: 1.6;
  transition: background-color 0.3s ease, color 0.3s ease, background 0.3s ease;
  position: relative;
  overflow-x: hidden;
}

body::before {
  content: '';
  position: fixed;
  top: -50%;
  right: -10%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  z-index: 0;
}

body::after {
  content: '';
  position: fixed;
  bottom: -20%;
  left: -5%;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(20, 184, 166, 0.06) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  z-index: 0;
}

body.dark-mode {
  background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 50%, var(--bg-tertiary) 100%);
  color: var(--text-primary);
}

/* ===== HEADER ===== */
header {
  position: fixed;
  inset-block-start: 0;
  inset-inline: 0;
  height: 70px;
  background: rgba(10, 10, 10, 0.95);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-block-end: 1px solid rgba(59, 130, 246, 0.2);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-inline: 40px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

body.dark-mode header {
  background: rgba(10, 10, 10, 0.95);
  border-bottom-color: rgba(59, 130, 246, 0.2);
}

.logo {
  font-size: 1.5rem;
  font-weight: 900;
  color: var(--text-primary);
  text-decoration: none;
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, var(--primary) 0%, var(--accent-purple) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

body.dark-mode .logo {
  color: var(--text-primary);
}

.logo span {
  color: var(--primary);
}

.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.desktop-nav {
  display: none;
  gap: 32px;
  align-items: center;
  flex: 1;
  justify-content: center;
}

.desktop-nav a {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-decoration: none;
  transition: color var(--transition);
  position: relative;
}

body.dark-mode .desktop-nav a {
  color: var(--text-secondary);
}

.desktop-nav a::after {
  content: '';
  position: absolute;
  block-end: -4px;
  inset-inline-start: 0;
  width: 0;
  height: 2px;
  background: var(--primary);
  transition: width var(--transition);
}

.desktop-nav a:hover {
  color: var(--primary);
}

.desktop-nav a:hover::after {
  width: 100%;
}

/* Navbar Buttons */
.btn-outline {
  padding: 10px 20px;
  border: 2px solid rgba(59, 130, 246, 0.5);
  background: transparent;
  color: var(--primary);
  border-radius: 8px;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 700;
  cursor: pointer;
  transition: all var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-outline:hover {
  background: rgba(59, 130, 246, 0.1);
  border-color: var(--primary);
  box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
}

.theme-toggle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: var(--bg-secondary);
  cursor: pointer;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform var(--transition);
}

body.dark-mode .theme-toggle {
  background: var(--bg-tertiary);
}

.theme-toggle:active {
  transform: scale(0.95);
}

/* Mobile Header Layout */
@media (max-width: 639px) {
  header {
    padding-inline: 16px;
    justify-content: space-between;
  }

  .logo {
    font-size: 1.3rem;
    order: 1;
  }

  .header-right {
    order: 3;
    gap: 12px;
  }

  .btn-solid {
    padding: 8px 16px;
    font-size: 0.85rem;
    order: 2;
  }

  .theme-toggle {
    display: none;
  }

  .menu-toggle {
    display: flex;
  }

  .btn-outline {
    display: none;
  }
}

/* Desktop Header Layout */
@media (min-width: 1024px) {
  .desktop-nav {
    display: flex;
  }

  .menu-toggle {
    display: none;
  }
}

.menu-toggle {
  width: 40px;
  height: 40px;
  border: none;
  background: none;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  padding: 8px;
}

.menu-toggle span {
  width: 24px;
  height: 2px;
  background: var(--text-primary);
  border-radius: 2px;
  transition: all var(--transition);
}

body.dark-mode .menu-toggle span {
  background: var(--text-primary);
}

/* ===== MAIN CONTENT ===== */
main {
  margin-top: 0;
  padding-bottom: 40px;
}

/* ===== HERO ===== */
.hero {
  padding-block: 100px 60px;
  padding-inline: 40px;
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
  position: relative;
  overflow: hidden;
  min-height: 100vh;
  display: flex;
  align-items: center;
  margin-top: 70px;
}

/* Grid Pattern Overlay */
.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
  background-size: 50px 50px;
  pointer-events: none;
  z-index: 1;
}

.hero::after {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  z-index: 1;
}

.hero-container {
  display: flex;
  flex-direction: column-reverse;
  align-items: center;
  justify-content: space-around;
  gap: 60px;
  max-width: 1400px;
  margin: 0 auto;
  position: relative;
  z-index: 10;
  width: 100%;
}

.hero-image-wrapper {
  flex-shrink: 0;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-image-glow {
  position: absolute;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  z-index: 0;
  filter: blur(40px);
}

.hero-image {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  object-fit: contain;
  border: 3px solid #3b82f6;
  box-shadow: 
    0 0 0 8px rgba(59, 130, 246, 0.2),
    0 0 0 16px rgba(59, 130, 246, 0.1),
    0 0 60px rgba(59, 130, 246, 0.4),
    0 20px 60px rgba(0, 0, 0, 0.5);
  position: relative;
  z-index: 2;
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

.hero-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  max-width: 600px;
}

.hero .subtitle {
  font-size: 1.1rem;
  color: #60a5fa;
  font-weight: 700;
  margin-bottom: 16px;
  text-transform: capitalize;
  letter-spacing: 0.08em;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-shadow: 0 0 20px rgba(96, 165, 250, 0.5);
}

.hero .subtitle::before {
  content: '';
  width: 0;
  height: 0;
  border-left: 0;
  border-right: 0;
}

.hero h1 {
  font-size: 3.5rem;
  font-weight: 900;
  margin-bottom: 20px;
  letter-spacing: -0.02em;
  line-height: 1.1;
  color: var(--text-primary);
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 25%, #ec4899 50%, #f97316 75%, #3b82f6 100%);
  background-size: 200% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: gradientShift 4s ease-in-out infinite;
  text-shadow: 0 0 40px rgba(59, 130, 246, 0.3);
}

@keyframes gradientShift {
  0%, 100% {
    background-position: 0% center;
  }
  50% {
    background-position: 100% center;
  }
}

.hero-tagline {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.95);
  margin-bottom: 36px;
  line-height: 1.8;
  font-weight: 500;
  max-width: 500px;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.hero-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
  width: 100%;
}

.stat-card {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(139, 92, 246, 0.15));
  border: 2px solid rgba(59, 130, 246, 0.4);
  border-radius: 16px;
  padding: 20px 16px;
  text-align: center;
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
}

.stat-card:hover {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(139, 92, 246, 0.25));
  border-color: rgba(59, 130, 246, 0.6);
  transform: translateY(-6px) scale(1.05);
  box-shadow: 0 12px 40px rgba(59, 130, 246, 0.4);
}

.stat-label {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 10px;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 900;
  background: linear-gradient(135deg, #f97316 0%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.02em;
}

.cta-buttons {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
  width: 100%;
  max-width: 400px;
}

.btn {
  padding: 16px 32px;
  border-radius: 12px;
  border: none;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  transition: all var(--transition);
  font-family: inherit;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  width: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, var(--accent-orange) 0%, #ea580c 100%);
  color: white;
  box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
  position: relative;
  z-index: 10;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 40px rgba(249, 115, 22, 0.5);
}

.btn-primary:active {
  transform: scale(0.98);
}

.btn-secondary {
  background: transparent;
  color: var(--primary);
  border: 2px solid var(--primary);
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
}

.btn-secondary:hover {
  background: rgba(59, 130, 246, 0.1);
  box-shadow: 0 0 30px rgba(59, 130, 246, 0.4);
  transform: translateY(-2px);
}

.btn-secondary:active {
  transform: scale(0.98);
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 32px;
}

.social-links a {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  text-decoration: none;
  font-size: 1.5rem;
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  border: 2px solid rgba(59, 130, 246, 0.4);
  box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
  position: relative;
}

.social-links a::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.2), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.social-links a:hover::before {
  opacity: 1;
}

.social-links a:hover {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(139, 92, 246, 0.4));
  border-color: rgba(59, 130, 246, 0.8);
  transform: translateY(-8px) scale(1.15);
  box-shadow: 0 12px 32px rgba(59, 130, 246, 0.5);
}

.social-links a:active {
  transform: translateY(-6px) scale(1.1);
}

/* Mobile Layout - Stack Vertically */
@media (max-width: 639px) {
  .hero {
    padding-block: 80px 40px;
    padding-inline: 16px;
    min-height: auto;
  }

  .hero-container {
    flex-direction: column;
    gap: 40px;
    align-items: center;
  }

  .hero-image-wrapper {
    order: -1;
    margin-top: 20px;
  }

  .hero-image {
    width: 280px;
    height: 280px;
    border: 5px solid #3b82f6;
    object-fit: contain;
    box-shadow: 
      0 0 0 16px rgba(167, 139, 250, 0.35),
      0 0 0 32px rgba(167, 139, 250, 0.18),
      0 0 100px rgba(167, 139, 250, 0.6),
      0 40px 100px rgba(0, 0, 0, 0.7);
  }

  .hero-image-glow {
    width: 340px;
    height: 340px;
    background: radial-gradient(circle, rgba(167, 139, 250, 0.5) 0%, transparent 70%);
    filter: blur(60px);
  }

  .hero-content {
    text-align: center;
    align-items: center;
  }

  .hero .subtitle {
    font-size: 0.95rem;
    margin-bottom: 12px;
  }

  .hero h1 {
    font-size: 2rem;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #ffffff 0%, var(--accent-purple) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-tagline {
    font-size: 0.95rem;
    margin-bottom: 24px;
    line-height: 1.6;
    max-width: 100%;
  }

  .hero-stats {
    display: none;
  }

  .cta-buttons {
    max-width: 100%;
    gap: 12px;
    margin-bottom: 24px;
  }

  .btn {
    padding: 14px 24px;
    font-size: 0.95rem;
  }

  .social-links {
    gap: 12px;
    margin-top: 24px;
  }

  .social-links a {
    width: 44px;
    height: 44px;
    font-size: 1.1rem;
  }
}

/* Tablet Layout */
@media (min-width: 640px) and (max-width: 767px) {
  .hero h1 {
    font-size: 3rem;
  }

  .hero-tagline {
    font-size: 1.1rem;
  }

  .cta-buttons {
    flex-direction: row;
  }

  .btn {
    flex: 1;
  }
}

/* Desktop Side-by-Side Layout */
@media (min-width: 768px) {
  .hero {
    min-height: 100vh;
    padding-block: 100px 80px;
  }

  .hero-container {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 80px;
  }

  .hero-image-wrapper {
    flex-shrink: 0;
    order: 2;
    flex: 0 0 45%;
  }

  .hero-image {
    width: 320px;
    height: 320px;
    border: 4px solid #3b82f6;
    object-fit: contain;
    box-shadow: 
      0 0 0 14px rgba(59, 130, 246, 0.25),
      0 0 0 28px rgba(59, 130, 246, 0.12),
      0 0 80px rgba(59, 130, 246, 0.5),
      0 30px 80px rgba(0, 0, 0, 0.6);
  }

  .hero-image-glow {
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.35) 0%, transparent 70%);
    filter: blur(50px);
  }

  .hero-content {
    align-items: flex-start;
    text-align: start;
    order: 1;
    flex: 0 0 55%;
  }

  .hero .subtitle {
    justify-content: flex-start;
  }

  .hero h1 {
    font-size: 4.5rem;
  }

  .hero-tagline {
    font-size: 1.2rem;
    text-align: start;
  }

  .hero-stats {
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 48px;
    width: 100%;
  }

  .cta-buttons {
    flex-direction: row;
    max-width: 100%;
    width: 100%;
  }

  .btn {
    flex: 1;
  }

  /* RTL Support - Automatic flip */
  html[dir="rtl"] .hero-image-wrapper {
    order: 1;
  }

  html[dir="rtl"] .hero-content {
    order: 2;
    align-items: flex-end;
    text-align: end;
  }

  html[dir="rtl"] .hero .subtitle {
    justify-content: flex-end;
  }

  html[dir="rtl"] .hero .subtitle::before {
    order: 2;
  }

  html[dir="rtl"] .hero .subtitle {
    flex-direction: row-reverse;
  }

  html[dir="rtl"] .hero-tagline {
    text-align: end;
  }
}

/* ===== EXPERIENCE TIMELINE ===== */
.experience-timeline {
  position: relative;
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 20px;
}

.timeline-line {
  position: absolute;
  left: 40px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: linear-gradient(180deg, #3b82f6 0%, #a78bfa 50%, #f97316 100%);
  z-index: 1;
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.4), 0 0 40px rgba(167, 139, 250, 0.2);
}

.experience-item {
  margin-bottom: 60px;
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: 60px;
  padding-left: 0;
}

.experience-item:last-child {
  margin-bottom: 0;
}

.experience-item:nth-child(odd) .experience-content {
  margin-left: 80px;
  margin-right: 0;
  width: auto;
  text-align: left;
}

.experience-item:nth-child(even) .experience-content {
  margin-left: 80px;
  margin-right: 0;
  width: auto;
  text-align: left;
}

.timeline-node {
  position: absolute;
  left: 40px;
  top: 20px;
  transform: translateX(-50%);
  width: 24px;
  height: 24px;
  background: linear-gradient(135deg, #3b82f6 0%, #a78bfa 100%);
  border: 3px solid #0a0a0a;
  border-radius: 50%;
  z-index: 10;
  box-shadow: 
    0 0 0 6px rgba(59, 130, 246, 0.2),
    0 0 20px rgba(59, 130, 246, 0.6),
    0 0 40px rgba(167, 139, 250, 0.3),
    inset 0 0 10px rgba(255, 255, 255, 0.2);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.experience-item:hover .timeline-node {
  width: 32px;
  height: 32px;
  transform: translateX(-50%) scale(1.3);
  box-shadow: 
    0 0 0 8px rgba(59, 130, 246, 0.3),
    0 0 30px rgba(59, 130, 246, 0.8),
    0 0 60px rgba(167, 139, 250, 0.5),
    inset 0 0 15px rgba(255, 255, 255, 0.3);
}

.experience-content {
  position: relative;
  flex: 1;
}

.experience-item::before {
  content: '';
  position: absolute;
  left: 40px;
  top: 52px;
  width: 40px;
  height: 2px;
  background: linear-gradient(90deg, rgba(59, 130, 246, 0.6), rgba(59, 130, 246, 0.2));
  z-index: 2;
}

.experience-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
  border: 2px solid transparent;
  background-clip: padding-box;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
}

.experience-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
  z-index: 1;
}

.experience-card::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 16px;
  padding: 2px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(139, 92, 246, 0.3), rgba(236, 72, 153, 0.3));
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  opacity: 0;
  transition: opacity 0.4s ease;
}

body.dark-mode .experience-card {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

.experience-card:hover {
  transform: translateX(12px) translateY(-8px) scale(1.02);
  box-shadow: 0 20px 60px rgba(59, 130, 246, 0.25);
}

.experience-card:hover::after {
  opacity: 1;
}

body.dark-mode .experience-card:hover {
  box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
}

.experience-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 12px;
}

.experience-header h3 {
  font-size: 1.4rem;
  font-weight: 800;
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0;
  line-height: 1.3;
  letter-spacing: -0.02em;
}

.experience-date {
  font-size: 0.85rem;
  font-weight: 800;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  white-space: nowrap;
  padding: 8px 16px;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  border-radius: 24px;
  border: 2px solid rgba(249, 115, 22, 0.3);
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.experience-item:hover .experience-date {
  background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
  border-color: rgba(249, 115, 22, 0.6);
  box-shadow: 0 6px 20px rgba(249, 115, 22, 0.5);
  transform: scale(1.05);
}

.experience-company {
  font-size: 1.1rem;
  font-weight: 700;
  color: #3b82f6;
  margin: 12px 0 20px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.experience-company::before {
  content: '🏢';
  font-size: 1.3rem;
  filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.3));
}

.experience-bullets {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.experience-bullets li {
  font-size: 1rem;
  color: #475569;
  line-height: 1.7;
  padding-left: 28px;
  position: relative;
  transition: all 0.3s ease;
  font-weight: 500;
}

.experience-bullets li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #3b82f6;
  font-weight: 900;
  font-size: 1.2rem;
  transition: all 0.3s ease;
}

.experience-item:hover .experience-bullets li {
  color: #1e293b;
  padding-left: 32px;
}

.experience-item:hover .experience-bullets li::before {
  color: #8b5cf6;
  transform: scale(1.2);
}

body.dark-mode .experience-bullets li {
  color: #cbd5e1;
}

body.dark-mode .experience-item:hover .experience-bullets li {
  color: #f1f5f9;
}

/* Desktop Timeline - Left-aligned with right-expanding cards */
@media (min-width: 768px) {
  .experience-timeline {
    padding: 60px 40px;
  }

  .experience-item {
    margin-bottom: 70px;
    gap: 60px;
  }

  .experience-content {
    margin-left: 80px;
  }

  .experience-card {
    padding: 32px;
  }

  .experience-header h3 {
    font-size: 1.35rem;
  }
}

/* Tablet Timeline */
@media (min-width: 640px) and (max-width: 767px) {
  .experience-timeline {
    padding: 40px 20px;
  }

  .timeline-line {
    left: 30px;
  }

  .experience-item {
    margin-bottom: 50px;
    gap: 40px;
  }

  .experience-item::before {
    left: 30px;
    width: 30px;
  }

  .timeline-node {
    left: 30px;
  }

  .experience-content {
    margin-left: 60px;
  }

  .experience-card {
    padding: 24px;
  }

  .experience-header h3 {
    font-size: 1.2rem;
  }
}

/* Mobile Timeline */
@media (max-width: 639px) {
  .experience-timeline {
    padding: 40px 16px;
  }

  .timeline-line {
    left: 20px;
  }

  .experience-item {
    flex-direction: column;
    gap: 0;
    margin-bottom: 40px;
    padding-left: 0;
  }

  .experience-item::before {
    display: none;
  }

  .experience-content {
    margin-left: 60px;
    width: 100%;
  }

  .timeline-node {
    left: 20px;
    top: 0;
  }

  .experience-card {
    padding: 20px;
  }

  .experience-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .experience-header h3 {
    font-size: 1.1rem;
  }

  .experience-date {
    align-self: flex-start;
    font-size: 0.75rem;
  }

  .experience-company {
    font-size: 0.95rem;
    margin: 6px 0 12px 0;
  }

  .experience-bullets li {
    font-size: 0.9rem;
    padding-left: 20px;
  }
}

/* RTL Support */
html[dir="rtl"] .timeline-line {
  left: auto;
  right: 40px;
}

html[dir="rtl"] .experience-item::before {
  left: auto;
  right: 40px;
}

html[dir="rtl"] .timeline-node {
  left: auto;
  right: 40px;
}

html[dir="rtl"] .experience-item:hover .timeline-node {
  transform: translateX(50%) scale(1.3);
}

html[dir="rtl"] .experience-header {
  flex-direction: row-reverse;
}

html[dir="rtl"] .experience-bullets li {
  padding-left: 0;
  padding-right: 24px;
}

html[dir="rtl"] .experience-bullets li::before {
  left: auto;
  right: 0;
}

html[dir="rtl"] .experience-card::after {
  left: auto;
  right: -2px;
  border-radius: 0 3px 3px 0;
}

/* ===== SKILLS WITH PROGRESS BARS ===== */
.skills-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 40px;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
}

.skills-column {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
  border: 2px solid transparent;
  background-clip: padding-box;
  border-radius: 20px;
  padding: 36px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
}

.skills-column::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
  z-index: 1;
}

.skills-column::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 20px;
  padding: 2px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(139, 92, 246, 0.3), rgba(236, 72, 153, 0.3));
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  opacity: 0;
  transition: opacity 0.4s ease;
  pointer-events: none;
}

body.dark-mode .skills-column {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

@media (hover: hover) and (pointer: fine) {
  .skills-column:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 60px rgba(59, 130, 246, 0.25);
  }
  
  .skills-column:hover::after {
    opacity: 1;
  }
}

@media (hover: none) and (pointer: coarse) {
  .skills-column:active {
    transform: scale(0.98);
  }
}

@media (hover: hover) and (pointer: fine) {
  body.dark-mode .skills-column:hover {
    box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
  }
}

.column-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 32px;
  padding-bottom: 20px;
  border-bottom: 2px solid rgba(59, 130, 246, 0.2);
  position: relative;
  z-index: 2;
}

.column-icon {
  font-size: 2.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
  filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.3));
}

@media (hover: hover) and (pointer: fine) {
  .skills-column:hover .column-icon {
    transform: scale(1.2) rotate(8deg);
  }
}

.column-header h3 {
  font-size: 1.5rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.skills-list {
  display: flex;
  flex-direction: column;
  gap: 26px;
  position: relative;
  z-index: 2;
}

.skill-item {
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: transform 0.3s ease;
}

@media (hover: hover) and (pointer: fine) {
  .skills-column:hover .skill-item {
    transform: translateX(6px);
  }
}

.skill-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.skill-name {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1e293b;
  letter-spacing: 0.01em;
}

body.dark-mode .skill-name {
  color: #f1f5f9;
}

.skill-percentage {
  font-size: 0.9rem;
  font-weight: 800;
  color: #ffffff;
  background: linear-gradient(135deg, #f97316 0%, #ec4899 100%);
  padding: 6px 14px;
  border-radius: 16px;
  border: 2px solid rgba(249, 115, 22, 0.3);
  min-width: 55px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.skill-item:hover .skill-percentage {
  background: linear-gradient(135deg, #ea580c 0%, #db2777 100%);
  border-color: rgba(249, 115, 22, 0.6);
  box-shadow: 0 6px 20px rgba(249, 115, 22, 0.5);
  transform: scale(1.08);
}

.progress-bar {
  width: 100%;
  height: 12px;
  background: rgba(59, 130, 246, 0.1);
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  border: 2px solid rgba(59, 130, 246, 0.2);
  transition: all 0.3s ease;
}

.skill-item:hover .progress-bar {
  background: rgba(59, 130, 246, 0.15);
  border-color: rgba(59, 130, 246, 0.4);
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
  border-radius: 12px;
  width: 0%;
  transition: width 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  box-shadow: 0 0 25px rgba(59, 130, 246, 0.6), inset 0 2px 8px rgba(255, 255, 255, 0.3);
}

.progress-fill::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
  animation: shimmer 2s infinite;
  animation-play-state: paused;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.skill-item:hover .progress-fill::after {
  animation-play-state: running;
}

.progress-fill.animated {
  animation: fillProgress 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes fillProgress {
  from {
    width: 0%;
  }
  to {
    width: var(--percentage, 100%);
  }
}

/* Responsive Skills Grid */
@media (max-width: 1200px) {
  .skills-container {
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    padding: 0 30px;
  }
}

@media (max-width: 1023px) {
  .skills-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 28px;
    padding: 0 24px;
  }

  .skills-column {
    padding: 24px;
  }

  .column-header h3 {
    font-size: 1.2rem;
  }
}

@media (max-width: 767px) {
  .skills-container {
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 0 16px;
  }

  .skills-column {
    padding: 20px;
  }

  .column-header {
    margin-bottom: 20px;
  }

  .column-icon {
    font-size: 1.8rem;
  }

  .column-header h3 {
    font-size: 1.1rem;
  }

  .skills-list {
    gap: 18px;
  }

  .skill-name {
    font-size: 0.9rem;
  }

  .progress-bar {
    height: 8px;
  }
}

/* Dark Mode Progress Bar */
body.dark-mode .progress-bar {
  background: rgba(59, 130, 246, 0.1);
  border-color: rgba(59, 130, 246, 0.25);
}

body.dark-mode .progress-fill {
  box-shadow: 0 0 30px rgba(167, 139, 250, 0.7), 0 0 50px rgba(59, 130, 246, 0.5), inset 0 0 15px rgba(255, 255, 255, 0.15);
}

/* RTL Support */
html[dir="rtl"] .column-header {
  flex-direction: row-reverse;
}

html[dir="rtl"] .skill-header {
  flex-direction: row-reverse;
}

html[dir="rtl"] .progress-fill::after {
  animation: shimmerRTL 2s infinite;
}

@keyframes shimmerRTL {
  0% {
    transform: translateX(100%);
  }
  100% {
    transform: translateX(-100%);
  }
}

/* ===== SECTION ===== */
section {
  padding-block: 80px;
  padding-inline: 16px;
  border-block-end: 1px solid var(--border-color);
}

body.dark-mode section {
  border-bottom-color: var(--border-color);
}

section h2 {
  font-size: 2.8rem;
  font-weight: 900;
  margin-bottom: 16px;
  text-align: center;
  letter-spacing: -0.02em;
  color: var(--text-primary);
  background: linear-gradient(135deg, var(--primary) 0%, var(--accent-purple) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  text-align: center;
  margin-bottom: 56px;
  font-size: 1.15rem;
  color: var(--text-secondary);
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.7;
  font-weight: 500;
}

body.dark-mode .section-subtitle {
  color: var(--text-secondary);
}

section p {
  font-size: 1.05rem;
  line-height: 1.8;
  margin-bottom: 20px;
  color: var(--text-secondary);
}

body.dark-mode section p {
  color: var(--text-secondary);
}

/* ===== ABOUT ===== */
.about-content {
  display: flex;
  flex-direction: column;
  gap: 48px;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
}

.about-narrative {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
  border: 2px solid transparent;
  background-clip: padding-box;
  border-radius: 20px;
  padding: 48px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  max-width: 900px;
  margin: 0 auto 48px;
  position: relative;
  overflow: hidden;
}

.about-narrative::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 6px;
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 25%, #ec4899 50%, #f97316 75%, #3b82f6 100%);
  background-size: 200% auto;
  animation: gradientShift 4s linear infinite;
}

.about-narrative::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 20px;
  padding: 2px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.2));
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  pointer-events: none;
}

body.dark-mode .about-narrative {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

.about-narrative p {
  font-size: 1.15rem;
  line-height: 2;
  margin-bottom: 24px;
  color: #475569;
  max-width: 75ch;
  font-weight: 500;
  position: relative;
  z-index: 1;
}

.about-narrative p strong {
  background: linear-gradient(135deg, #1e40af 0%, #6d28d9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 800;
  font-size: 1.2rem;
  letter-spacing: -0.01em;
}

body.dark-mode .about-narrative p {
  color: #cbd5e1;
}

.about-narrative p:last-child {
  margin-bottom: 0;
}

.about-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.about-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(249, 250, 251, 0.95) 100%);
  border: 2px solid transparent;
  background-clip: padding-box;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
}

.about-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
  opacity: 1;
}

.about-card::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 16px;
  padding: 2px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(139, 92, 246, 0.3), rgba(236, 72, 153, 0.3));
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  opacity: 0;
  transition: opacity 0.4s ease;
}

.about-card:hover::after {
  opacity: 1;
}

body.dark-mode .about-card {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
}

.about-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 60px rgba(59, 130, 246, 0.25);
}

body.dark-mode .about-card:hover {
  box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
}

.about-card h3 {
  font-size: 1.3rem;
  font-weight: 800;
  margin-bottom: 18px;
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  display: flex;
  align-items: center;
  gap: 12px;
  letter-spacing: -0.01em;
}

.about-card h3::before {
  content: attr(data-icon);
  font-size: 2.2rem;
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.4));
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
  box-shadow: 0 4px 16px rgba(59, 130, 246, 0.15);
}

.about-card:hover h3::before {
  transform: scale(1.15) rotate(5deg);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
}

.about-card p {
  font-size: 1.05rem;
  margin: 0;
  line-height: 1.8;
  color: #475569;
  font-weight: 500;
}

body.dark-mode .about-card p {
  color: #cbd5e1;
}

.about-card a {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 700;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  display: inline-block;
  padding: 2px 4px;
  border-radius: 4px;
}

.about-card a::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
  border-radius: 4px;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: -1;
}

.about-card a::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 3px;
  background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
  transition: width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  border-radius: 2px;
}

.about-card a:hover {
  color: #8b5cf6;
  transform: translateY(-2px) scale(1.05);
}

.about-card a:hover::before {
  opacity: 1;
}

.about-card a:hover::after {
  width: 100%;
}

/* Special styling for email and phone links */
.about-card a[href^="mailto"],
.about-card a[href^="tel"] {
  font-weight: 800;
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.about-card a[href^="mailto"]:hover,
.about-card a[href^="tel"]:hover {
  background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  transform: translateY(-3px) scale(1.08);
}

/* Responsive About Section */
@media (max-width: 1023px) {
  .about-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .about-narrative {
    padding: 32px;
  }
}

@media (max-width: 767px) {
  .about-content {
    padding: 0 16px;
    gap: 32px;
  }
  
  .about-narrative {
    padding: 24px;
  }
  
  .about-narrative p {
    font-size: 1rem;
  }
  
  .about-card {
    padding: 20px;
  }
  
  .about-card h3 {
    font-size: 1.1rem;
  }
  
  .about-card p {
    font-size: 0.95rem;
  }
}

/* ===== SKILLS ===== */
/* ===== PROJECTS ===== */
#projects {
  background: linear-gradient(135deg, rgba(237, 233, 254, 0.3) 0%, rgba(219, 234, 254, 0.3) 100%);
  padding: 80px 0;
}

body.dark-mode #projects {
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.5) 0%, rgba(30, 41, 59, 0.5) 100%);
}

/* CRITICAL: Force grid layout with maximum specificity */
section#projects .projects-grid,
#projects > .projects-grid {
  display: grid !important;
  grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
  gap: 24px !important;
  max-width: 1400px !important;
  margin: 0 auto !important;
  padding: 0 40px !important;
  width: 100% !important;
}

/* Desktop: 4 columns for large screens (1200px+) */
@media (min-width: 1200px) {
  section#projects .projects-grid,
  #projects > .projects-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 24px !important;
  }
}

/* Desktop: 3 columns for medium screens (900-1199px) */
@media (min-width: 900px) and (max-width: 1199px) {
  section#projects .projects-grid,
  #projects > .projects-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 20px !important;
  }
}

/* Tablet: 2 columns (600-899px) */
@media (min-width: 600px) and (max-width: 899px) {
  section#projects .projects-grid,
  #projects > .projects-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 18px !important;
  }
}

/* Enhanced Project Card - READABLE */
.project-card-enhanced {
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(59, 130, 246, 0.15);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  display: flex !important;
  flex-direction: column !important;
  height: 100%;
  max-height: 440px;
  position: relative;
  width: 100% !important;
  max-width: 100% !important;
}

.project-card-enhanced::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), transparent);
  z-index: 1;
}

body.dark-mode .project-card-enhanced {
  background: rgba(30, 41, 59, 0.95);
  border-color: rgba(59, 130, 246, 0.25);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.project-card-enhanced:hover {
  transform: translateY(-8px) scale(1.02);
  border-color: rgba(59, 130, 246, 0.6);
  box-shadow: 0 12px 32px rgba(59, 130, 246, 0.3);
}

body.dark-mode .project-card-enhanced:hover {
  box-shadow: 0 12px 32px rgba(59, 130, 246, 0.45);
}

/* Project Image Wrapper - GRADIENT VARIATIONS */
.project-image-wrapper {
  position: relative;
  width: 100%;
  height: 110px;
  overflow: hidden;
  background: linear-gradient(135deg, #2563eb 0%, #7c3aed 50%, #f97316 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Gradient variation for cards 5-8 (second row) */
.project-card-enhanced:nth-child(n+5) .project-image-wrapper {
  background: linear-gradient(135deg, #7c3aed 0%, #ec4899 50%, #f97316 100%);
}

/* Gradient variation for cards 9-12 (third row) */
.project-card-enhanced:nth-child(n+9) .project-image-wrapper {
  background: linear-gradient(135deg, #ec4899 0%, #f97316 50%, #fbbf24 100%);
}

/* Gradient variation for cards 13+ (fourth row) */
.project-card-enhanced:nth-child(n+13) .project-image-wrapper {
  background: linear-gradient(135deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%);
}

.project-image {
  font-size: 2.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  position: relative;
  z-index: 2;
  transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.project-card-enhanced:hover .project-image {
  transform: scale(1.15) rotate(5deg);
}

/* GitHub Sync Badge - PROMINENT */
.github-sync-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 5px 10px;
  background: rgba(0, 0, 0, 0.8);
  border: 1.5px solid rgba(59, 130, 246, 0.6);
  border-radius: 20px;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  z-index: 10;
  font-size: 0.75rem;
  font-weight: 700;
  color: #60a5fa;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
  transition: all 0.3s ease;
}

.github-sync-badge:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: #60a5fa;
  box-shadow: 0 6px 24px rgba(59, 130, 246, 0.5);
  transform: scale(1.05);
}

.sync-icon {
  font-size: 0.9rem;
  animation: spin 3s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.github-sync-badge:hover .sync-icon {
  animation: spin 1s linear infinite;
}

/* Project Overlay */
.project-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  z-index: 3;
}

.project-card-enhanced:hover .project-overlay {
  opacity: 1;
}

.overlay-buttons {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  justify-content: center;
}

.overlay-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: rgba(59, 130, 246, 0.95);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  border: 2px solid rgba(59, 130, 246, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

/* Change GitHub button text styling */
.overlay-btn.github-btn .btn-text::before {
  content: 'View ';
}

.overlay-btn.github-btn .btn-text {
  font-weight: 700;
}

.overlay-btn:hover {
  background: rgba(59, 130, 246, 1);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
}

.overlay-btn.live-btn {
  background: rgba(249, 115, 22, 0.9);
  border-color: rgba(249, 115, 22, 0.9);
}

.overlay-btn.live-btn:hover {
  background: rgba(249, 115, 22, 1);
  box-shadow: 0 8px 24px rgba(249, 115, 22, 0.4);
}

.btn-icon {
  font-size: 1.2rem;
}

.btn-text {
  font-weight: 700;
}

/* Project Content - SPACIOUS */
.project-content {
  padding: 18px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
  position: relative;
  z-index: 2;
  min-height: 0;
}

/* Tech Stack Icons - LARGER & MORE VISIBLE */
.tech-stack {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  padding: 8px 0;
  border-top: 1px solid rgba(59, 130, 246, 0.15);
  border-bottom: 1px solid rgba(59, 130, 246, 0.15);
  order: -1;
}

.tech-icon {
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: rgba(59, 130, 246, 0.08);
  border-radius: 8px;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  cursor: pointer;
  border: 1px solid rgba(59, 130, 246, 0.15);
  position: relative;
}

.tech-icon:hover {
  background: rgba(59, 130, 246, 0.2);
  transform: translateY(-3px) scale(1.1);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
  border-color: rgba(59, 130, 246, 0.4);
}

/* Tooltip for tech icons */
.tech-icon::after {
  content: attr(title);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%) translateY(-4px);
  background: rgba(0, 0, 0, 0.9);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease, transform 0.3s ease;
  z-index: 100;
}

.tech-icon:hover::after {
  opacity: 1;
  transform: translateX(-50%) translateY(-8px);
}

/* Project Title - PROMINENT */
.project-title {
  font-size: 1.1rem;
  font-weight: 800;
  line-height: 1.3;
  margin: 0;
  color: var(--text-primary);
  letter-spacing: -0.01em;
  background: linear-gradient(135deg, #1e40af 0%, #6d28d9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  transition: all 0.3s ease;
}

.project-card-enhanced:hover .project-title {
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Project Description - READABLE, 3 LINES MAX */
.project-description {
  font-size: 0.95rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  max-height: 4.8em;
}

body.dark-mode .project-description {
  color: var(--text-secondary);
}

/* View Details Button */
.view-details-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  border: none;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(10px);
  pointer-events: none;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.project-card-enhanced:hover .view-details-btn {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.view-details-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
}

/* Project Footer */
.project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  gap: 12px;
}

.project-links-footer {
  display: flex;
  gap: 12px;
}

.footer-link {
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 700;
  transition: all 0.3s ease;
  border: 1px solid rgba(59, 130, 246, 0.3);
  color: #3b82f6;
  background: rgba(59, 130, 246, 0.05);
}

.footer-link:hover {
  background: rgba(59, 130, 246, 0.15);
  border-color: rgba(59, 130, 246, 0.5);
  transform: translateY(-1px);
}

.footer-link.live-link {
  color: #f97316;
  border-color: rgba(249, 115, 22, 0.3);
  background: rgba(249, 115, 22, 0.05);
}

.footer-link.live-link:hover {
  background: rgba(249, 115, 22, 0.15);
  border-color: rgba(249, 115, 22, 0.5);
}

/* Responsive Projects Grid */

/* Mobile: 1 column (below 600px) */
@media (max-width: 599px) {
  section#projects .projects-grid,
  #projects > .projects-grid {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
    padding: 0 16px !important;
  }

  .project-card-enhanced {
    border-radius: 12px;
  }

  .project-image-wrapper {
    height: 200px;
  }

  .project-image {
    font-size: 4rem;
  }

  .project-content {
    padding: 20px;
    gap: 12px;
  }

  .project-title {
    font-size: 1.2rem;
  }

  .project-description {
    font-size: 0.9rem;
  }

  .overlay-btn {
    padding: 10px 18px;
    font-size: 0.85rem;
  }

  .tech-icon {
    width: 36px;
    height: 36px;
    font-size: 1.1rem;
  }

  .view-details-btn {
    padding: 10px 16px;
    font-size: 0.85rem;
  }
}

/* ===== SECTION ===== */

/* Old Project Card Styles (kept for compatibility) */
.project-card {
  background: var(--card-light);
  border: 1px solid var(--border-light);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: all var(--transition);
}

body.dark-mode .project-card {
  background: var(--card-dark);
  border-color: var(--border-dark);
}

.project-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary);
}

.project-thumb {
  height: 160px;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3.5rem;
  position: relative;
  overflow: hidden;
}

.project-thumb::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.1));
}

.project-body {
  padding: 20px;
}

.project-body h3 {
  font-size: 1.25rem;
  font-weight: 800;
  margin-bottom: 12px;
  color: var(--text-light);
}

body.dark-mode .project-body h3 {
  color: var(--text-dark);
}

.project-desc {
  font-size: 0.95rem;
  margin-bottom: 16px;
  line-height: 1.6;
  color: var(--text-light);
}

body.dark-mode .project-desc {
  color: var(--text-dark);
}

.project-meta {
  margin-bottom: 16px;
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.meta-label {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.project-tech {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.project-tech span {
  font-size: 0.8rem;
  padding: 5px 12px;
  background: rgba(37,99,235,0.1);
  color: var(--primary);
  border-radius: 20px;
  font-weight: 600;
  border: 1px solid rgba(37,99,235,0.2);
}

.project-links {
  display: flex;
  gap: 10px;
}

.project-link {
  flex: 1;
  padding: 11px 14px;
  background: var(--primary);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 700;
  text-align: center;
  transition: all var(--transition);
  border: 2px solid var(--primary);
}

.project-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

.project-link.github {
  background: var(--card-light);
  color: var(--primary);
  border-color: var(--border-light);
}

body.dark-mode .project-link.github {
  background: var(--card-dark);
  border-color: var(--border-dark);
}

.project-link.github:hover {
  background: var(--primary);
  color: white;
}

.case-study-details {
  background: rgba(37,99,235,0.05);
  border-left: 4px solid var(--primary);
  border-radius: 6px;
  padding: 16px;
  margin: 16px 0;
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
}

body.dark-mode .case-study-details {
  background: rgba(37,99,235,0.1);
}

.case-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.case-label {
  font-size: 0.8rem;
  font-weight: 800;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.case-text {
  font-size: 0.9rem;
  line-height: 1.5;
  color: var(--text-light);
  margin: 0;
}

body.dark-mode .case-text {
  color: var(--text-dark);
}

/* ===== CERTIFICATIONS ===== */
/* ===== CERTIFICATIONS ===== */
.certifications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
}

.cert-card {
  background: rgba(10, 10, 10, 0.6);
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 16px;
  padding: 28px 24px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 10px;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

.cert-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--accent-purple), var(--accent-orange));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.cert-card:hover::before { opacity: 1; }

body.dark-mode .cert-card {
  background: rgba(10, 10, 10, 0.7);
  border-color: rgba(59, 130, 246, 0.3);
}

.cert-card:hover {
  transform: translateY(-10px);
  border-color: rgba(59, 130, 246, 0.5);
  box-shadow: 0 20px 60px rgba(59, 130, 246, 0.2), 0 0 40px rgba(59, 130, 246, 0.1);
}

.cert-icon {
  font-size: 2.8rem;
  margin-bottom: 4px;
  display: block;
  transition: transform 0.3s ease;
}

.cert-card:hover .cert-icon {
  transform: scale(1.15) rotate(5deg);
}

.cert-card h3 {
  font-size: 1.05rem;
  font-weight: 800;
  margin: 0;
  color: var(--text-primary);
  letter-spacing: -0.01em;
  line-height: 1.3;
}

.cert-issuer {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--accent-orange);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  background: rgba(249, 115, 22, 0.1);
  border: 1px solid rgba(249, 115, 22, 0.25);
  border-radius: 20px;
  padding: 4px 12px;
  display: inline-block;
  align-self: center;
}

.cert-year {
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--text-tertiary);
  letter-spacing: 0.05em;
}

.cert-desc {
  font-size: 0.88rem;
  line-height: 1.55;
  color: var(--text-secondary);
  margin: 0;
  flex: 1;
}

.cert-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-top: 4px;
  padding: 9px 18px;
  background: linear-gradient(135deg, rgba(37,99,235,0.15) 0%, rgba(167,139,250,0.15) 100%);
  border: 1px solid rgba(59, 130, 246, 0.35);
  border-radius: 8px;
  color: var(--primary);
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 700;
  transition: all 0.3s ease;
}

.cert-link:hover {
  background: linear-gradient(135deg, rgba(37,99,235,0.3) 0%, rgba(167,139,250,0.3) 100%);
  border-color: var(--primary);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.25);
  color: #fff;
}

@media (max-width: 639px) {
  .certifications-grid {
    padding: 0 16px;
    grid-template-columns: 1fr;
  }
}

/* ===== TESTIMONIALS ===== */
.testimonials-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

.testimonial-card {
  background: var(--card-light);
  border: 2px solid var(--border-light);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: var(--shadow);
  transition: all var(--transition);
  position: relative;
}

body.dark-mode .testimonial-card {
  background: var(--card-dark);
  border-color: var(--border-dark);
}

.testimonial-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary);
}

.testimonial-card::before {
  content: '"';
  position: absolute;
  top: 8px;
  left: 16px;
  font-size: 3rem;
  color: var(--primary);
  opacity: 0.2;
}

.testimonial-stars {
  font-size: 1rem;
  margin-bottom: 12px;
  letter-spacing: 2px;
}

.testimonial-text {
  font-size: 0.95rem;
  line-height: 1.7;
  color: var(--text-light);
  margin-bottom: 16px;
  font-style: italic;
}

body.dark-mode .testimonial-text {
  color: var(--text-dark);
}

.testimonial-author {
  border-block-start: 1px solid var(--border-light);
  padding-block-start: 12px;
}

body.dark-mode .testimonial-author {
  border-block-start-color: var(--border-dark);
}

.author-name {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--primary);
  margin-bottom: 2px;
}

.author-role {
  font-size: 0.8rem;
  color: var(--text-light);
  font-weight: 500;
}

body.dark-mode .author-role {
  color: var(--text-dark);
}

/* ===== CONTACT SECTION LAYOUT ===== */
#contact {
  padding-block: 50px 60px;
  padding-inline: 16px;
  border-block-end: 1px solid var(--border-color);
}

#contact h2 {
  font-size: 2rem;
  font-weight: 900;
  margin-bottom: 16px;
  text-align: center;
  letter-spacing: -0.02em;
  color: var(--text-primary);
}

#contact > p {
  font-size: 0.95rem;
  text-align: center;
  margin-bottom: 48px;
  color: var(--text-secondary);
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* Full Terminal Container - Single Container */
.terminal-container-full {
  max-width: 85%;
  width: 100%;
  margin: 0 auto;
  background: #0a0a0a;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 20px 80px rgba(0, 0, 0, 0.6), 0 0 60px rgba(59, 130, 246, 0.15);
  border: 1px solid rgba(59, 130, 246, 0.25);
  display: flex;
  flex-direction: column;
}

/* Social Links Section */
.social-commits-section {
  margin-top: 80px;
  padding-top: 60px;
  border-top: 1px solid rgba(59, 130, 246, 0.2);
  text-align: center;
}

.social-commits-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 40px;
  letter-spacing: -0.01em;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.social-commits-title::before {
  content: '🔗';
  font-size: 1.8rem;
}

/* Social Icons Grid */
.social-commits-grid {
  display: flex;
  justify-content: center;
  gap: 32px;
  flex-wrap: wrap;
}

/* Minimalist Social Icon */
.social-commit-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(167, 139, 250, 0.1) 100%);
  border: 2px solid rgba(59, 130, 246, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  text-decoration: none;
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
}

.social-commit-icon::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(167, 139, 250, 0.2) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: -1;
}

.social-commit-icon:hover {
  transform: translateY(-8px) scale(1.15);
  border-color: rgba(59, 130, 246, 0.6);
  box-shadow: 0 12px 40px rgba(59, 130, 246, 0.3), 0 0 30px rgba(59, 130, 246, 0.2);
}

.social-commit-icon:hover::before {
  opacity: 1;
}

/* GitHub Icon */
.social-commit-icon.github:hover {
  border-color: rgba(255, 255, 255, 0.5);
  box-shadow: 0 12px 40px rgba(255, 255, 255, 0.2), 0 0 30px rgba(255, 255, 255, 0.1);
}

/* LinkedIn Icon */
.social-commit-icon.linkedin:hover {
  border-color: rgba(0, 119, 181, 0.6);
  box-shadow: 0 12px 40px rgba(0, 119, 181, 0.3), 0 0 30px rgba(0, 119, 181, 0.2);
}

/* Twitter Icon */
.social-commit-icon.twitter:hover {
  border-color: rgba(29, 155, 240, 0.6);
  box-shadow: 0 12px 40px rgba(29, 155, 240, 0.3), 0 0 30px rgba(29, 155, 240, 0.2);
}
/* Terminal Header */
.terminal-header {
  background: linear-gradient(to bottom, #2a2a2a, #1a1a1a);
  padding: 12px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid rgba(59, 130, 246, 0.2);
}

.terminal-buttons {
  display: flex;
  gap: 8px;
}

.terminal-btn {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.close-btn {
  background: #ff5f56;
}

.close-btn:hover {
  background: #ff6b63;
  box-shadow: 0 0 10px rgba(255, 95, 86, 0.5);
}

.minimize-btn {
  background: #ffbd2e;
}

.minimize-btn:hover {
  background: #ffc93d;
  box-shadow: 0 0 10px rgba(255, 189, 46, 0.5);
}

.maximize-btn {
  background: #27c93f;
}

.maximize-btn:hover {
  background: #3dd94f;
  box-shadow: 0 0 10px rgba(39, 201, 63, 0.5);
}

.terminal-title {
  flex: 1;
  text-align: center;
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  color: #888;
  font-weight: 500;
}

/* Terminal Body - 2 Column Layout */
.terminal-body-full {
  background: #0a0a0a;
  padding: 24px;
  font-family: 'Courier New', monospace;
  color: #00ff00;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  align-items: start;
  flex: 1;
  overflow: hidden;
}

@media (max-width: 1023px) {
  .terminal-body-full {
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 20px;
  }
}

@media (max-width: 767px) {
  .terminal-body-full {
    padding: 16px;
    gap: 20px;
  }
}

/* Left Column - Contact Info */
.terminal-left-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
  min-width: 0;
}

@media (max-width: 1023px) {
  .terminal-left-column {
    gap: 14px;
  }
}

.terminal-info-section {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

@media (max-width: 1023px) {
  .terminal-info-section {
    gap: 6px;
  }
}

.terminal-info-title {
  font-size: 0.9rem;
  font-weight: bold;
  color: #00ff00;
  display: flex;
  gap: 8px;
  align-items: center;
}

@media (max-width: 1023px) {
  .terminal-info-title {
    font-size: 0.85rem;
  }
}

.terminal-info-title::before {
  content: '→';
  color: #3b82f6;
  font-weight: bold;
}

.terminal-info-content {
  font-size: 0.85rem;
  color: #00ff00;
  line-height: 1.5;
  padding-left: 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

@media (max-width: 1023px) {
  .terminal-info-content {
    font-size: 0.8rem;
    line-height: 1.4;
    gap: 3px;
  }
}

.terminal-info-item {
  display: flex;
  gap: 6px;
  align-items: center;
  font-size: 0.8rem;
}

@media (max-width: 1023px) {
  .terminal-info-item {
    font-size: 0.75rem;
    gap: 5px;
  }
}

.terminal-info-icon {
  color: #3b82f6;
  font-weight: bold;
  min-width: 16px;
}

.terminal-info-link {
  color: #00ff00;
  text-decoration: none;
  transition: all 0.3s ease;
  border-bottom: 1px dotted rgba(0, 255, 0, 0.3);
}

.terminal-info-link:hover {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
}

/* Right Column - Git Status + Form */
.terminal-right-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
  flex: 1;
  min-width: 0;
}

@media (max-width: 1023px) {
  .terminal-right-column {
    gap: 14px;
  }
}

/* Git Status Output */
.terminal-git-status {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(0, 255, 0, 0.2);
}

@media (max-width: 1023px) {
  .terminal-git-status {
    gap: 3px;
    padding-bottom: 10px;
  }
}

.terminal-line {
  font-size: 0.9rem;
  line-height: 1.6;
  display: flex;
  gap: 8px;
  align-items: flex-start;
}

@media (max-width: 1023px) {
  .terminal-line {
    font-size: 0.85rem;
    line-height: 1.5;
  }
}

.terminal-prompt {
  color: #00ff00;
  font-weight: bold;
  min-width: 20px;
}

.terminal-command {
  color: #00ff00;
  font-weight: bold;
}

.terminal-text {
  color: #00ff00;
  opacity: 0.8;
}

/* Terminal Form - Inline */
.terminal-form-inline {
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
  min-width: 0;
}

@media (max-width: 1023px) {
  .terminal-form-inline {
    gap: 8px;
  }
}

.terminal-input-group-inline {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

@media (max-width: 1023px) {
  .terminal-input-group-inline {
    gap: 2px;
  }
}

.terminal-label-inline {
  display: flex;
  gap: 6px;
  align-items: center;
  font-size: 0.8rem;
  color: #00ff00;
  font-weight: bold;
}

@media (max-width: 1023px) {
  .terminal-label-inline {
    font-size: 0.75rem;
    gap: 5px;
  }
}

.terminal-label-text {
  color: #00ff00;
}

.terminal-input-inline {
  background: rgba(0, 255, 0, 0.05);
  border: 1px solid rgba(0, 255, 0, 0.3);
  border-radius: 4px;
  padding: 6px 8px;
  font-family: 'Courier New', monospace;
  font-size: 0.8rem;
  color: #00ff00;
  transition: all var(--transition);
  outline: none;
}

@media (max-width: 1023px) {
  .terminal-input-inline {
    padding: 5px 7px;
    font-size: 0.75rem;
  }
}

.terminal-input-inline::placeholder {
  color: rgba(0, 255, 0, 0.4);
}

.terminal-input-inline:focus {
  background: rgba(0, 255, 0, 0.1);
  border-color: rgba(0, 255, 0, 0.6);
  box-shadow: 0 0 10px rgba(0, 255, 0, 0.2), inset 0 0 5px rgba(0, 255, 0, 0.1);
}

.terminal-textarea-inline {
  resize: vertical;
  min-height: 50px;
  max-height: 80px;
}

@media (max-width: 1023px) {
  .terminal-textarea-inline {
    min-height: 45px;
    max-height: 70px;
  }
}

/* Terminal Submit Button */
.terminal-submit-btn-inline {
  background: linear-gradient(135deg, #00ff00 0%, #00cc00 100%);
  color: #000;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.8rem;
  font-weight: bold;
  cursor: pointer;
  transition: all var(--transition);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
  margin-top: 4px;
}

@media (max-width: 1023px) {
  .terminal-submit-btn-inline {
    padding: 6px 12px;
    font-size: 0.75rem;
    margin-top: 2px;
  }
}

.terminal-submit-btn-inline:hover {
  background: linear-gradient(135deg, #00ff00 0%, #00dd00 100%);
  box-shadow: 0 0 30px rgba(0, 255, 0, 0.5), 0 0 60px rgba(0, 255, 0, 0.2);
  transform: translateY(-2px);
}

.terminal-submit-btn-inline:active {
  transform: scale(0.98);
}

/* Terminal Footer */
.terminal-footer-full {
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(0, 255, 0, 0.2);
  animation: blink 1s infinite;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  color: #00ff00;
}

@media (max-width: 1023px) {
  .terminal-footer-full {
    margin-top: 6px;
    padding-top: 6px;
    font-size: 0.85rem;
  }
}

@keyframes blink {
  0%, 49% {
    opacity: 1;
  }
  50%, 100% {
    opacity: 0.5;
  }
}
/* Responsive Terminal Container */
@media (max-width: 1200px) {
  .terminal-container-full {
    max-width: 90%;
  }
}

@media (max-width: 1023px) {
  .terminal-container-full {
    max-width: 95%;
  }

  .terminal-body-full {
    grid-template-columns: 1fr;
    gap: 20px;
    padding: 20px;
  }
}

@media (max-width: 767px) {
  #contact {
    padding-block: 32px 40px;
  }

  #contact h2 {
    font-size: 1.6rem;
    margin-bottom: 12px;
  }

  #contact > p {
    margin-bottom: 24px;
    font-size: 0.9rem;
  }

  .terminal-container-full {
    max-width: 100%;
    border-radius: 8px;
  }

  .terminal-body-full {
    padding: 16px;
    gap: 16px;
    grid-template-columns: 1fr;
  }

  .terminal-title {
    font-size: 0.75rem;
  }

  .terminal-left-column {
    gap: 12px;
  }

  .terminal-right-column {
    gap: 12px;
  }

  .terminal-info-content {
    font-size: 0.8rem;
  }

  .terminal-input-inline {
    font-size: 0.8rem;
    padding: 6px 8px;
  }

  .terminal-submit-btn-inline {
    font-size: 0.8rem;
    padding: 8px 14px;
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  #contact {
    padding-block: 36px 45px;
  }

  #contact h2 {
    font-size: 1.8rem;
    margin-bottom: 14px;
  }

  #contact > p {
    margin-bottom: 28px;
  }

  .contact-cards-row {
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
    margin-bottom: 40px;
  }

  .contact-card-light {
    padding: 24px 22px;
    min-height: 180px;
  }

  .contact-card-icon {
    width: 65px;
    height: 65px;
    font-size: 2.6rem;
    margin-bottom: 12px;
  }

  .contact-main-wrapper {
    gap: 28px;
    padding: 0 18px;
  }
}
@media (max-width: 1200px) {
  .terminal-container {
    width: 100%;
    max-height: none;
  }

  .contact-info-grid {
    width: 100%;
  }
}

@media (max-width: 1023px) {
  .contact-section-wrapper {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .terminal-body {
    grid-template-columns: 1fr;
    gap: 24px;
    max-height: none;
    overflow: hidden;
    padding: 20px;
  }

  .terminal-container {
    width: 100%;
    max-height: none;
  }

  .contact-info-grid {
    width: 100%;
  }
}

@media (min-width: 640px) and (max-width: 1023px) {
  .contact-info-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}

@media (max-width: 767px) {
  .terminal-container {
    width: 100%;
    border-radius: 8px;
  }

  .terminal-body {
    padding: 20px;
    gap: 24px;
    grid-template-columns: 1fr;
  }

  .terminal-input {
    font-size: 0.85rem;
    padding: 10px 12px;
  }

  .terminal-submit-btn {
    font-size: 0.9rem;
    padding: 12px 20px;
  }

  .contact-info-grid {
    width: 100%;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-top: 32px;
  }

  .terminal-title {
    font-size: 0.75rem;
  }

  .terminal-contact-info {
    gap: 20px;
  }

  .terminal-info-content {
    font-size: 0.85rem;
  }
}

  .terminal-input {
    font-size: 0.85rem;
    padding: 8px 10px;
  }

  .terminal-submit-btn {
    font-size: 0.9rem;
    padding: 10px 20px;
  }

  .contact-info-grid {
    grid-template-columns: 1fr;
    gap: 16px;
    margin-top: 32px;
  }

  .terminal-title {
    font-size: 0.75rem;
  }
}

/* Old Contact Styles (kept for compatibility) */
.contact-wrapper {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--text-light);
}

body.dark-mode .form-group label {
  color: var(--text-dark);
}

.form-group input,
.form-group textarea {
  padding: 14px;
  border: 1.5px solid var(--border-light);
  border-radius: 8px;
  background: var(--bg-light);
  color: var(--text-light);
  font-family: inherit;
  font-size: 1rem;
  transition: all var(--transition);
}

body.dark-mode .form-group input,
body.dark-mode .form-group textarea {
  background: var(--card-dark);
  border-color: var(--border-dark);
  color: var(--text-dark);
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.contact-info {
  background: var(--card-light);
  border: 1px solid var(--border-light);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: var(--shadow);
}

body.dark-mode .contact-info {
  background: var(--card-dark);
  border-color: var(--border-dark);
}

.contact-item {
  margin-block-end: 20px;
  padding-block-end: 20px;
  border-block-end: 1px solid var(--border-light);
}

body.dark-mode .contact-item {
  border-block-end-color: var(--border-dark);
}

.contact-item:last-child {
  margin-block-end: 0;
  padding-block-end: 0;
  border-block-end: none;
}

.contact-item h4 {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: var(--primary);
}

.contact-item a {
  color: var(--text-light);
  text-decoration: none;
  font-weight: 600;
  transition: color var(--transition);
}

body.dark-mode .contact-item a {
  color: var(--text-dark);
}

.contact-item a:hover {
  color: var(--primary);
}

.contact-item p {
  font-size: 0.95rem;
  line-height: 1.6;
  color: var(--text-light);
  margin: 0;
}

body.dark-mode .contact-item p {
  color: var(--text-dark);
}

.contact-socials {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}

.contact-socials a {
  display: inline-block;
  padding: 8px 16px;
  background: rgba(37,99,235,0.1);
  color: var(--primary);
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 700;
  text-decoration: none;
  transition: all var(--transition);
}

.contact-socials a:hover {
  background: var(--primary);
  color: white;
}

.contact-socials {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}

.contact-socials a {
  display: inline-block;
  padding: 8px 16px;
  background: rgba(37,99,235,0.1);
  color: var(--primary);
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 700;
  text-decoration: none;
  transition: all var(--transition);
}

.contact-socials a:hover {
  background: var(--primary);
  color: white;
}

/* ===== FOOTER ===== */
.footer {
  background: linear-gradient(180deg, #0a0a0a 0%, #050505 100%);
  border-top: 1px solid rgba(59, 130, 246, 0.2);
  padding: 60px 16px 32px;
  color: #d1d5db;
  position: relative;
  overflow: hidden;
}

.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), transparent);
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Footer Top Section */
.footer-top {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 40px;
  margin-bottom: 40px;
}

/* Footer Section */
.footer-section {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.footer-section h4 {
  font-size: 1rem;
  font-weight: 800;
  color: rgba(255, 255, 255, 0.95);
  text-transform: uppercase;
  letter-spacing: 0.12em;
  margin: 0;
  background: linear-gradient(135deg, #ffffff, rgba(255, 255, 255, 0.7));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  padding-bottom: 8px;
}

.footer-section h4::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 40px;
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--accent-purple));
  border-radius: 2px;
}

/* Branding Section */
.footer-branding {
  gap: 20px;
}

.footer-logo {
  font-size: 1.8rem;
  font-weight: 900;
  color: var(--text-primary);
  letter-spacing: -0.02em;
}

.logo-dot {
  color: var(--primary);
}

.footer-tagline {
  font-size: 0.9rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
}

/* Status Widget */
.status-widget {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  background: rgba(34, 197, 94, 0.08);
  border: 1px solid rgba(34, 197, 94, 0.35);
  border-radius: 8px;
  width: fit-content;
}

.status-dot {
  width: 9px;
  height: 9px;
  background: #22c55e;
  border-radius: 50%;
  animation: pulse-dot 2s infinite;
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
}

@keyframes pulse-dot {
  0%, 100% {
    opacity: 1;
    box-shadow: 0 0 10px rgba(0, 255, 0, 0.6);
  }
  50% {
    opacity: 0.5;
    box-shadow: 0 0 5px rgba(0, 255, 0, 0.3);
  }
}

.status-text {
  font-size: 0.82rem;
  font-weight: 700;
  color: #22c55e;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Quick Links */
.footer-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: inline-block;
  position: relative;
  padding: 6px 0;
  font-weight: 500;
}

.footer-links a::before {
  content: '→';
  position: absolute;
  left: -20px;
  opacity: 0;
  transition: all 0.3s ease;
  color: var(--primary);
}

.footer-links a::after {
  content: '';
  position: absolute;
  bottom: 2px;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #2563eb, #7c3aed, #f97316);
  transition: width 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.footer-links a:hover {
  color: #ffffff;
  transform: translateX(8px);
}

.footer-links a:hover::before {
  opacity: 1;
  left: -18px;
}

.footer-links a:hover::after {
  width: 100%;
}

/* Social Icons */
.social-icons {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
}

.social-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.08);
  border: 2px solid rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  color: rgba(255, 255, 255, 0.7);
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.social-icon svg {
  width: 24px;
  height: 24px;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}

.social-icon::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.social-icon:hover::before {
  opacity: 1;
}

.social-icon:hover {
  transform: translateY(-6px) scale(1.1);
  border-color: currentColor;
}

.social-icon:hover svg {
  transform: scale(1.1);
}

/* GitHub - White/Gray */
.social-icon.github-icon {
  background: linear-gradient(135deg, rgba(51, 51, 51, 0.3), rgba(0, 0, 0, 0.5));
  border-color: rgba(255, 255, 255, 0.3);
  color: #ffffff;
}

.social-icon.github-icon:hover {
  background: linear-gradient(135deg, #333333, #000000);
  border-color: #ffffff;
  box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
}

/* LinkedIn - Blue */
.social-icon.linkedin-icon {
  background: linear-gradient(135deg, rgba(0, 119, 181, 0.3), rgba(0, 102, 153, 0.5));
  border-color: rgba(0, 119, 181, 0.5);
  color: #0077b5;
}

.social-icon.linkedin-icon:hover {
  background: linear-gradient(135deg, #0077b5, #006699);
  border-color: #0077b5;
  color: #ffffff;
  box-shadow: 0 10px 30px rgba(0, 119, 181, 0.5);
}

/* Twitter/X - White */
.social-icon.twitter-icon {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(200, 200, 200, 0.3));
  border-color: rgba(255, 255, 255, 0.3);
  color: #ffffff;
}

.social-icon.twitter-icon:hover {
  background: linear-gradient(135deg, #ffffff, #e0e0e0);
  border-color: #ffffff;
  color: #000000;
  box-shadow: 0 10px 30px rgba(255, 255, 255, 0.4);
}

/* Telegram - Blue */
.social-icon.telegram-icon {
  background: linear-gradient(135deg, rgba(0, 136, 204, 0.3), rgba(0, 119, 181, 0.5));
  border-color: rgba(0, 136, 204, 0.5);
  color: #0088cc;
}

.social-icon.telegram-icon:hover {
  background: linear-gradient(135deg, #0088cc, #0077b5);
  border-color: #0088cc;
  color: #ffffff;
  box-shadow: 0 10px 30px rgba(0, 136, 204, 0.5);
}

/* YouTube - Red */
.social-icon.youtube-icon {
  background: linear-gradient(135deg, rgba(255, 0, 0, 0.3), rgba(204, 0, 0, 0.5));
  border-color: rgba(255, 0, 0, 0.5);
  color: #ff0000;
}

.social-icon.youtube-icon:hover {
  background: linear-gradient(135deg, #ff0000, #cc0000);
  border-color: #ff0000;
  color: #ffffff;
  box-shadow: 0 10px 30px rgba(255, 0, 0, 0.5);
}

/* Brand Links */
.brand-links {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(124, 58, 237, 0.1));
  border: 2px solid rgba(59, 130, 246, 0.3);
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  color: rgba(255, 255, 255, 0.9);
  font-weight: 700;
  font-size: 1rem;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.brand-link::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--primary);
  transform: scaleY(0);
  transition: transform 0.3s ease;
}

.brand-link:hover::before {
  transform: scaleY(1);
}

.brand-link:hover {
  transform: translateX(8px) scale(1.02);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
}

.brand-icon {
  width: 28px;
  height: 28px;
  flex-shrink: 0;
  transition: transform 0.3s ease;
}

.brand-link:hover .brand-icon {
  transform: scale(1.2) rotate(5deg);
}

.brand-name {
  flex: 1;
  font-weight: 700;
  letter-spacing: 0.02em;
}

/* Tech Pulse - Orange/Telegram */
.brand-link.tech-pulse {
  background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.2));
  border-color: rgba(249, 115, 22, 0.4);
}

.brand-link.tech-pulse .brand-icon {
  color: #0088cc;
}

.brand-link.tech-pulse:hover {
  background: linear-gradient(135deg, rgba(249, 115, 22, 0.25), rgba(234, 88, 12, 0.3));
  border-color: #f97316;
  box-shadow: 0 8px 24px rgba(249, 115, 22, 0.4);
}

.brand-link.tech-pulse:hover::before {
  background: #f97316;
}

.brand-link.tech-pulse:hover .brand-icon {
  color: #0088cc;
}

/* Elias Tube - Red/YouTube */
.brand-link.elias-tube {
  background: linear-gradient(135deg, rgba(255, 0, 0, 0.15), rgba(204, 0, 0, 0.2));
  border-color: rgba(255, 0, 0, 0.4);
}

.brand-link.elias-tube .brand-icon {
  color: #ff0000;
}

.brand-link.elias-tube:hover {
  background: linear-gradient(135deg, rgba(255, 0, 0, 0.25), rgba(204, 0, 0, 0.3));
  border-color: #ff0000;
  box-shadow: 0 8px 24px rgba(255, 0, 0, 0.4);
}

.brand-link.elias-tube:hover::before {
  background: #ff0000;
}

.brand-link.elias-tube:hover .brand-icon {
  color: #ff0000;
}

.brand-arrow {
  width: 14px;
  height: 14px;
  color: rgba(255, 255, 255, 0.6);
  margin-left: auto;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.brand-link:hover .brand-arrow {
  transform: translate(4px, -4px);
  color: rgba(255, 255, 255, 1);
}

/* Footer Divider */
.footer-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
  margin: 40px 0;
}

/* Footer Bottom */
.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  padding-top: 20px;
  border-top: 1px solid rgba(59, 130, 246, 0.1);
}

.footer-copyright {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.footer-copyright p {
  margin: 0;
}

.footer-meta {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.footer-meta-item {
  font-size: 0.85rem;
  color: var(--text-muted);
  display: flex;
  align-items: center;
}

.footer-meta-item::before {
  content: '•';
  margin-right: 10px;
  color: var(--primary);
}

.footer-meta-item:first-child::before {
  content: '';
  margin-right: 0;
}

/* Responsive Footer */
@media (max-width: 767px) {
  .footer {
    padding: 40px 16px 24px;
  }

  .footer-top {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }

  .footer-meta {
    justify-content: center;
  }

  .social-icons {
    justify-content: center;
  }

  .footer-links ul {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 16px;
  }

  .footer-links a {
    font-size: 0.85rem;
  }
}

/* ===== RESPONSIVE ===== */
@media (min-width: 640px) {
  .hero h1 {
    font-size: 3rem;
  }
  
  .hero .subtitle {
    font-size: 1.4rem;
  }
  
  .hero p {
    font-size: 1.2rem;
  }
  
  .cta-buttons {
    flex-direction: row;
  }
  
  .skills-grid {
    grid-template-columns: 1fr 1fr;
  }
  
  .testimonials-grid {
    grid-template-columns: 1fr 1fr;
  }
  
  section {
    padding-block: 90px;
    padding-inline: 24px;
  }
  
  section h2 {
    font-size: 3.2rem;
  }
  
  .section-subtitle {
    font-size: 1.2rem;
    margin-bottom: 64px;
  }
}

@media (min-width: 1024px) {
  .hero h1 {
    font-size: 3.5rem;
  }
  
  .hero .subtitle {
    font-size: 1.5rem;
  }
  
  .hero p {
    font-size: 1.3rem;
  }
  
  .desktop-nav {
    display: flex;
  }
  
  .menu-toggle {
    display: none;
  }
  
  .skills-grid {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .testimonials-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  
  section {
    padding-block: 100px;
    padding-inline: 40px;
  }
  
  section h2 {
    font-size: 3.5rem;
  }
  
  .section-subtitle {
    font-size: 1.25rem;
    margin-bottom: 72px;
    max-width: 800px;
  }
}

/* ===== RTL SUPPORT ===== */
html[dir="rtl"] {
  direction: rtl;
}

body.rtl {
  direction: rtl;
}

/* Logical properties automatically handle RTL - no additional CSS needed */
/* The following are kept for specific RTL enhancements only */

body.rtl .logo {
  letter-spacing: 0.02em;
}

body.rtl .header-right {
  flex-direction: row-reverse;
}

body.rtl .cta-buttons {
  flex-direction: row-reverse;
}

body.rtl .social-links {
  flex-direction: row-reverse;
}

body.rtl .project-links {
  flex-direction: row-reverse;
}

body.rtl .contact-socials {
  flex-direction: row-reverse;
}

/* RTL animations */
body.rtl [style*="animation: float"] {
  animation-direction: normal;
}

body.rtl .hero::after {
  clip-path: polygon(0 0%, 100% 50%, 100% 100%, 0 100%);
}
</style>
</head>
<body>

<!-- HEADER -->
<header>
  <a href="#" class="logo">Elias<span>.dev</span></a>
  
  <!-- Desktop Navigation -->
  <nav class="desktop-nav">
    <a href="#home">Home</a>
    <a href="#about">About</a>
    <a href="#skills-advanced">Skills</a>
    <a href="#projects">Projects</a>
    <a href="#contact">Contact</a>
  </nav>
  
  <div class="header-right">
    <!-- Certifications Button -->
    <a href="#certifications" class="btn-outline">📜 Certifications</a>
    
    
    <button class="theme-toggle" id="themeToggle">🌙</button>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</header>

<!-- HERO -->
<main>
  <section class="hero">
    <!-- Floating decorative elements -->
    <div style="position: absolute; top: 10%; left: 5%; font-size: 2rem; opacity: 0.1; animation: float 6s ease-in-out infinite;">{'{'}</div>
    <div style="position: absolute; top: 20%; right: 8%; font-size: 2rem; opacity: 0.1; animation: float 8s ease-in-out infinite 1s;">{'}'}</div>
    <div style="position: absolute; bottom: 15%; left: 10%; font-size: 1.5rem; opacity: 0.08; animation: float 7s ease-in-out infinite 2s;"><</div>
    <div style="position: absolute; bottom: 20%; right: 5%; font-size: 1.5rem; opacity: 0.08; animation: float 9s ease-in-out infinite 1.5s;">></div>
    
    <div class="hero-container">
      <!-- Image Wrapper -->
      <div class="hero-image-wrapper">
        <div class="hero-image-glow"></div>
        <img src="assets/personal-photo-removebg-preview.png" alt="Elias Araya" class="hero-image"/>
      </div>
      
      <!-- Content Wrapper -->
      <div class="hero-content">
        <div class="subtitle">Hi, I'm Elias</div>
        <h1 data-i18n="hero_title">Full Stack Developer</h1>
        <p class="hero-tagline" data-i18n="hero_tagline">Crafting <span style="color: #3b82f6;">digital excellence</span> through innovative code and cutting-edge technology solutions.</p>
        
        <!-- Social Proof Stats -->
        <div class="hero-stats">
          <div class="stat-card">
            <div class="stat-label">Education</div>
            <div class="stat-value">3rd Year</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Experience</div>
            <div class="stat-value">Full Stack</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Projects</div>
            <div class="stat-value">10+</div>
          </div>
        </div>
        
        <div class="cta-buttons">
          <a href="#projects" class="btn btn-primary">👨‍💻 Explore My Work</a>
          <a href="assets/EliasResume.pdf" download="Elias_Araya_Resume.pdf" class="btn btn-secondary">📥 Download CV</a>
        </div>
        
        <div class="social-links">
          <a href="https://github.com" target="_blank" rel="noopener" title="GitHub">🐙</a>
          <a href="https://linkedin.com" target="_blank" rel="noopener" title="LinkedIn">💼</a>
          <a href="mailto:eliasaraya142@gmail.com" title="Email">✉️</a>
        </div>
      </div>
    </div>
  </section>

  <!-- EXPERIENCE -->
  <section id="experience">
    <h2 data-i18n="experience_title"><?=t('experience_title')?></h2>
    <p style="text-align:center;margin-bottom:48px;font-size:1rem" data-i18n="experience_subtitle"><?=t('experience_subtitle')?></p>
    
    <div class="experience-timeline">
      <!-- Timeline Line -->
      <div class="timeline-line"></div>
      
      <!-- Experience Item 1 -->
      <div class="experience-item">
        <div class="timeline-node"></div>
        <div class="experience-content">
          <div class="experience-card">
            <div class="experience-header">
              <h3 data-i18n="exp_title_1"><?=t('exp_title_1')?></h3>
              <span class="experience-date" data-i18n="exp_date_1"><?=t('exp_date_1')?></span>
            </div>
            <p class="experience-company" data-i18n="exp_company_1"><?=t('exp_company_1')?></p>
            <ul class="experience-bullets">
              <li data-i18n="exp_bullet_1_1"><?=t('exp_bullet_1_1')?></li>
              <li data-i18n="exp_bullet_1_2"><?=t('exp_bullet_1_2')?></li>
              <li data-i18n="exp_bullet_1_3"><?=t('exp_bullet_1_3')?></li>
            </ul>
          </div>
        </div>
      </div>
      
      <!-- Experience Item 2 -->
      <div class="experience-item">
        <div class="timeline-node"></div>
        <div class="experience-content">
          <div class="experience-card">
            <div class="experience-header">
              <h3 data-i18n="exp_title_2"><?=t('exp_title_2')?></h3>
              <span class="experience-date" data-i18n="exp_date_2"><?=t('exp_date_2')?></span>
            </div>
            <p class="experience-company" data-i18n="exp_company_2"><?=t('exp_company_2')?></p>
            <ul class="experience-bullets">
              <li data-i18n="exp_bullet_2_1"><?=t('exp_bullet_2_1')?></li>
              <li data-i18n="exp_bullet_2_2"><?=t('exp_bullet_2_2')?></li>
              <li data-i18n="exp_bullet_2_3"><?=t('exp_bullet_2_3')?></li>
            </ul>
          </div>
        </div>
      </div>
      
      <!-- Experience Item 3 -->
      <div class="experience-item">
        <div class="timeline-node"></div>
        <div class="experience-content">
          <div class="experience-card">
            <div class="experience-header">
              <h3 data-i18n="exp_title_3"><?=t('exp_title_3')?></h3>
              <span class="experience-date" data-i18n="exp_date_3"><?=t('exp_date_3')?></span>
            </div>
            <p class="experience-company" data-i18n="exp_company_3"><?=t('exp_company_3')?></p>
            <ul class="experience-bullets">
              <li data-i18n="exp_bullet_3_1"><?=t('exp_bullet_3_1')?></li>
              <li data-i18n="exp_bullet_3_2"><?=t('exp_bullet_3_2')?></li>
              <li data-i18n="exp_bullet_3_3"><?=t('exp_bullet_3_3')?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SKILLS WITH PROGRESS BARS -->
  <section id="skills-advanced">
    <h2 data-i18n="skills_title"><?=t('skills_title')?></h2>
    <p style="text-align:center;margin-bottom:48px;font-size:1rem" data-i18n="skills_subtitle"><?=t('skills_subtitle')?></p>
    
    <div class="skills-container">
      <!-- Frontend Skills -->
      <div class="skills-column">
        <div class="column-header">
          <div class="column-icon">🎨</div>
          <h3 data-i18n="skills_frontend"><?=t('skills_frontend')?></h3>
        </div>
        <div class="skills-list">
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">HTML5</span>
              <span class="skill-percentage">95%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" aria-label="HTML5 proficiency">
              <div class="progress-fill" data-percentage="95"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">CSS3</span>
              <span class="skill-percentage">92%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="92" aria-valuemin="0" aria-valuemax="100" aria-label="CSS3 proficiency">
              <div class="progress-fill" data-percentage="92"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">JavaScript (ES6+)</span>
              <span class="skill-percentage">88%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100" aria-label="JavaScript proficiency">
              <div class="progress-fill" data-percentage="88"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">React</span>
              <span class="skill-percentage">80%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" aria-label="React proficiency">
              <div class="progress-fill" data-percentage="80"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Responsive Design</span>
              <span class="skill-percentage">90%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" aria-label="Responsive Design proficiency">
              <div class="progress-fill" data-percentage="90"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Tailwind CSS</span>
              <span class="skill-percentage">85%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" aria-label="Tailwind CSS proficiency">
              <div class="progress-fill" data-percentage="85"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Backend Skills -->
      <div class="skills-column">
        <div class="column-header">
          <div class="column-icon">⚙️</div>
          <h3 data-i18n="skills_backend"><?=t('skills_backend')?></h3>
        </div>
        <div class="skills-list">
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">PHP 8</span>
              <span class="skill-percentage">90%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" aria-label="PHP 8 proficiency">
              <div class="progress-fill" data-percentage="90"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Node.js</span>
              <span class="skill-percentage">82%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="82" aria-valuemin="0" aria-valuemax="100" aria-label="Node.js proficiency">
              <div class="progress-fill" data-percentage="82"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Express.js</span>
              <span class="skill-percentage">80%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" aria-label="Express.js proficiency">
              <div class="progress-fill" data-percentage="80"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">REST APIs</span>
              <span class="skill-percentage">88%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100" aria-label="REST APIs proficiency">
              <div class="progress-fill" data-percentage="88"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Authentication</span>
              <span class="skill-percentage">85%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" aria-label="Authentication proficiency">
              <div class="progress-fill" data-percentage="85"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Git & GitHub</span>
              <span class="skill-percentage">92%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="92" aria-valuemin="0" aria-valuemax="100" aria-label="Git and GitHub proficiency">
              <div class="progress-fill" data-percentage="92"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Database Skills -->
      <div class="skills-column">
        <div class="column-header">
          <div class="column-icon">🗄️</div>
          <h3 data-i18n="skills_database"><?=t('skills_database')?></h3>
        </div>
        <div class="skills-list">
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">MySQL</span>
              <span class="skill-percentage">90%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" aria-label="MySQL proficiency">
              <div class="progress-fill" data-percentage="90"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Database Design</span>
              <span class="skill-percentage">87%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="87" aria-valuemin="0" aria-valuemax="100" aria-label="Database Design proficiency">
              <div class="progress-fill" data-percentage="87"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">SQL Queries</span>
              <span class="skill-percentage">89%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100" aria-label="SQL Queries proficiency">
              <div class="progress-fill" data-percentage="89"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">MongoDB</span>
              <span class="skill-percentage">75%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" aria-label="MongoDB proficiency">
              <div class="progress-fill" data-percentage="75"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Data Modeling</span>
              <span class="skill-percentage">85%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" aria-label="Data Modeling proficiency">
              <div class="progress-fill" data-percentage="85"></div>
            </div>
          </div>
          
          <div class="skill-item">
            <div class="skill-header">
              <span class="skill-name">Optimization</span>
              <span class="skill-percentage">80%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" aria-label="Database Optimization proficiency">
              <div class="progress-fill" data-percentage="80"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about">
    <h2 data-i18n="about_title"><?=t('about_title')?></h2>
    <div class="about-content">
      <div class="about-narrative">
        <p><strong data-i18n="about_intro"><?=t('about_intro')?></strong></p>
        
        <p data-i18n="about_journey"><?=t('about_journey')?></p>
        
        <p data-i18n="about_passion"><?=t('about_passion')?></p>
      </div>
      
      <div class="about-grid">
        <div class="about-card">
          <h3>🎓 <span data-i18n="about_education"><?=t('about_education')?></span></h3>
          <p><strong>B.Sc. Computer Science</strong><br>Aksum University (Expected 2027)</p>
        </div>
        
        <div class="about-card">
          <h3>💼 <span data-i18n="about_role"><?=t('about_role')?></span></h3>
          <p><strong>Full-Stack Web Development Intern</strong><br>Future Interns (2025 – Present)</p>
        </div>
        
        <div class="about-card">
          <h3>📍 <span data-i18n="about_location"><?=t('about_location')?></span></h3>
          <p><strong>Aksum, Tigray, Ethiopia</strong><br>Available for remote opportunities worldwide</p>
        </div>
        
        <div class="about-card">
          <h3>🔗 <span data-i18n="about_connect"><?=t('about_connect')?></span></h3>
          <p><strong>Email:</strong> <a href="mailto:eliasaraya142@gmail.com" style="color:var(--primary);font-weight:700">eliasaraya142@gmail.com</a><br><strong>Phone:</strong> +251 977 118 144</p>
        </div>
      </div>
    </div>
  </section>

  <!-- PROJECTS -->
  <section id="projects">
    <h2 data-i18n="projects_title"><?=t('projects_title')?></h2>
    <p class="section-subtitle" data-i18n="projects_subtitle"><?=t('projects_subtitle')?></p>
    <div class="projects-grid">
      <?php if(empty($dbProjects)): ?>
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#94a3b8">
          <div style="font-size:3rem;margin-bottom:16px">📂</div>
          <p style="font-size:1.1rem;margin-bottom:20px"><?=t('projects_empty')?></p>
          <p style="font-size:0.95rem;margin-bottom:24px"><?=t('projects_add')?></p>
          <a href="dashboard.php" style="display:inline-block;padding:12px 28px;background:var(--primary);color:white;border-radius:8px;font-weight:700;text-decoration:none"><?=t('projects_add')?></a>
        </div>
      <?php else: ?>
        <?php foreach($dbProjects as $p): ?>
        <div class="project-card-enhanced">
          <!-- Project Image -->
          <div class="project-image-wrapper">
            <!-- GitHub Sync Badge -->
            <div class="github-sync-badge">
              <span class="sync-icon">🔄</span>
              <span>GitHub Sync</span>
            </div>
            
            <div class="project-image"><?=htmlspecialchars($p['emoji'])?></div>
            <div class="project-overlay">
              <div class="overlay-buttons">
                <?php if($p['github_url']): ?>
                  <a href="<?=htmlspecialchars($p['github_url'])?>" target="_blank" rel="noopener" class="overlay-btn github-btn" title="View Code">
                    <span class="btn-icon">⌥</span>
                    <span class="btn-text">View Code</span>
                  </a>
                <?php endif; ?>
                <?php if($p['live_url']): ?>
                  <a href="<?=htmlspecialchars($p['live_url'])?>" target="_blank" rel="noopener" class="overlay-btn live-btn" title="Live Demo">
                    <span class="btn-icon">↗</span>
                    <span class="btn-text">Live Demo</span>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- Project Content -->
          <div class="project-content">
            <!-- Tech Stack Icons (moved to top) -->
            <div class="tech-stack">
              <?php 
                $techs = array_map('trim', explode(',', $p['tech']));
                $techIcons = [
                  'React' => '⚛️',
                  'Vue' => '💚',
                  'Angular' => '🅰️',
                  'Node.js' => '🟢',
                  'Express' => '⚡',
                  'PHP' => '🐘',
                  'Python' => '🐍',
                  'MySQL' => '🗄️',
                  'MongoDB' => '🍃',
                  'PostgreSQL' => '🐘',
                  'JavaScript' => '⚙️',
                  'TypeScript' => '📘',
                  'HTML' => '🏗️',
                  'CSS' => '🎨',
                  'Tailwind' => '🌊',
                  'Bootstrap' => '📦',
                  'Docker' => '🐳',
                  'Git' => '🔀',
                  'AWS' => '☁️',
                  'Firebase' => '🔥'
                ];
              ?>
              <?php foreach($techs as $tech): ?>
                <span class="tech-icon" title="<?=htmlspecialchars($tech)?>">
                  <?php 
                    $icon = $techIcons[$tech] ?? '💻';
                    echo $icon;
                  ?>
                </span>
              <?php endforeach; ?>
            </div>
            
            <!-- Title -->
            <h3 class="project-title"><?=htmlspecialchars($p['title'])?></h3>
            
            <!-- Description -->
            <p class="project-description"><?=htmlspecialchars($p['description'])?></p>
            
            <!-- View Details Button (appears on hover) -->
            <a href="#project-<?=htmlspecialchars($p['id'])?>" class="view-details-btn">
              <span>📖</span>
              <span>View Details</span>
            </a>
          </div>
          
          <!-- Footer with Links -->
          <div class="project-footer">
            <div class="project-links-footer">
              <?php if($p['github_url']): ?>
                <a href="<?=htmlspecialchars($p['github_url'])?>" target="_blank" rel="noopener" class="footer-link github-link">
                  <span>GitHub</span>
                </a>
              <?php endif; ?>
              <?php if($p['live_url']): ?>
                <a href="<?=htmlspecialchars($p['live_url'])?>" target="_blank" rel="noopener" class="footer-link live-link">
                  <span>Live</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <!-- CERTIFICATIONS & ACHIEVEMENTS -->
  <section id="certifications">
    <h2 data-i18n="certs_title"><?=t('certs_title')?></h2>
    <p style="text-align:center;margin-bottom:32px;font-size:1rem" data-i18n="certs_subtitle"><?=t('certs_subtitle')?></p>
    
    <div class="certifications-grid">

      <div class="cert-card">
        <span class="cert-icon">🏆</span>
        <h3>AI Social Impact</h3>
        <span class="cert-issuer">Google &amp; Coursera</span>
        <span class="cert-year">2024</span>
        <p class="cert-desc">Comprehensive training on AI applications for social good and ethical AI development.</p>
        <a href="/assets/certificates/cert-ai-social-impact.pdf" target="_blank" rel="noopener" class="cert-link">
          <span>📄</span> View Certificate
        </a>
      </div>

      <div class="cert-card">
        <span class="cert-icon">💻</span>
        <h3>Computer Skills</h3>
        <span class="cert-issuer">Microsoft Academy</span>
        <span class="cert-year">2023</span>
        <p class="cert-desc">Proficiency in modern computing tools, productivity software, and digital literacy.</p>
        <a href="/assets/certificates/cert-computer-skills.jpg" target="_blank" rel="noopener" class="cert-link">
          <span>📄</span> View Certificate
        </a>
      </div>

      <div class="cert-card">
        <span class="cert-icon">🔒</span>
        <h3>Cybersecurity Fundamentals</h3>
        <span class="cert-issuer">CompTIA</span>
        <span class="cert-year">2024</span>
        <p class="cert-desc">Security principles, threat detection, and secure coding practices.</p>
        <a href="/assets/certificates/cert-cybersecurity.pdf" target="_blank" rel="noopener" class="cert-link">
          <span>📄</span> View Certificate
        </a>
      </div>

      <div class="cert-card">
        <span class="cert-icon">📊</span>
        <h3>Advanced Excel</h3>
        <span class="cert-issuer">LinkedIn Learning</span>
        <span class="cert-year">2023</span>
        <p class="cert-desc">Data analysis, visualization, and automation using advanced Excel functions and VBA.</p>
        <a href="/assets/certificates/cert-excel.jpg" target="_blank" rel="noopener" class="cert-link">
          <span>📄</span> View Certificate
        </a>
      </div>

      <div class="cert-card">
        <span class="cert-icon">🤖</span>
        <h3>Generative AI Mastery</h3>
        <span class="cert-issuer">OpenAI &amp; Udacity</span>
        <span class="cert-year">2024</span>
        <p class="cert-desc">Practical applications of generative AI, prompt engineering, and AI-assisted development.</p>
        <a href="/assets/certificates/cert-generative-ai.jpg" target="_blank" rel="noopener" class="cert-link">
          <span>📄</span> View Certificate
        </a>
      </div>

      <div class="cert-card">
        <span class="cert-icon">🖥️</span>
        <h3>IT Basics</h3>
        <span class="cert-issuer">CompTIA</span>
        <span class="cert-year">2023</span>
        <p class="cert-desc">Foundational IT knowledge including hardware, software, networking, and troubleshooting.</p>
        <a href="/assets/certificates/cert-it-basics.jpg" target="_blank" rel="noopener" class="cert-link">
          <span>📄</span> View Certificate
        </a>
      </div>

    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section id="testimonials">
    <h2 data-i18n="testimonials_title"><?=t('testimonials_title')?></h2>
    <p style="text-align:center;margin-bottom:32px;font-size:1rem" data-i18n="testimonials_subtitle"><?=t('testimonials_subtitle')?></p>
    
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
        <p class="testimonial-text">"Elias demonstrates exceptional problem-solving skills and a genuine passion for clean code. His ability to bridge frontend and backend development is remarkable for someone at his level."</p>
        <div class="testimonial-author">
          <div class="author-name">Sarah Johnson</div>
          <div class="author-role">Senior Developer, Future Interns</div>
        </div>
      </div>
      
      <div class="testimonial-card">
        <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
        <p class="testimonial-text">"Working with Elias on the portfolio project was seamless. He communicates clearly, meets deadlines, and isn't afraid to ask questions when needed. A true team player."</p>
        <div class="testimonial-author">
          <div class="author-name">Dr. Tekle Assefa</div>
          <div class="author-role">Computer Science Professor, Aksum University</div>
        </div>
      </div>
      
      <div class="testimonial-card">
        <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
        <p class="testimonial-text">"Elias has a rare combination of technical depth and user empathy. He doesn't just build features—he builds solutions that users actually want to use."</p>
        <div class="testimonial-author">
          <div class="author-name">Michael Chen</div>
          <div class="author-role">UX Lead, Tech Startup</div>
        </div>
      </div>
    </div>
  </section>
  <section id="contact">
    <h2 data-i18n="contact_title"><?=t('contact_title')?></h2>
    <p style="text-align:center;margin-bottom:48px;font-size:0.95rem" data-i18n="contact_subtitle"><?=t('contact_subtitle')?></p>
    
    <!-- Single Terminal Container with Everything Inside -->
    <div class="terminal-container-full">
      <!-- Terminal Header -->
      <div class="terminal-header">
        <div class="terminal-buttons">
          <div class="terminal-btn close-btn"></div>
          <div class="terminal-btn minimize-btn"></div>
          <div class="terminal-btn maximize-btn"></div>
        </div>
        <div class="terminal-title">elias@portfolio:~$ contact --help</div>
      </div>
      
      <!-- Terminal Body - 2 Column Grid -->
      <div class="terminal-body-full">
        <!-- LEFT COLUMN: Contact Information -->
        <div class="terminal-left-column">
          <!-- Contact Information Section -->
          <div class="terminal-info-section">
            <div class="terminal-info-title">Contact Information</div>
            <div class="terminal-info-content">
              <div class="terminal-info-item">
                <span class="terminal-info-icon">📧</span>
                <a href="mailto:eliasaraya142@gmail.com" class="terminal-info-link">eliasaraya142@gmail.com</a>
              </div>
              <div class="terminal-info-item">
                <span class="terminal-info-icon">📍</span>
                <span>Aksum, Ethiopia</span>
              </div>
              <div class="terminal-info-item">
                <span class="terminal-info-icon">📱</span>
                <a href="tel:+251977118144" class="terminal-info-link">+251 977 118 144</a>
              </div>
            </div>
          </div>
          
          <!-- Social Links Section -->
          <div class="terminal-info-section">
            <div class="terminal-info-title">Social Links</div>
            <div class="terminal-info-content">
              <div class="terminal-info-item">
                <span class="terminal-info-icon">🐙</span>
                <a href="https://github.com" target="_blank" rel="noopener" class="terminal-info-link">GitHub</a>
              </div>
              <div class="terminal-info-item">
                <span class="terminal-info-icon">💼</span>
                <a href="https://linkedin.com" target="_blank" rel="noopener" class="terminal-info-link">LinkedIn</a>
              </div>
              <div class="terminal-info-item">
                <span class="terminal-info-icon">✈️</span>
                <a href="https://t.me/TechPulse_Labs" target="_blank" rel="noopener" class="terminal-info-link">Telegram</a>
              </div>
            </div>
          </div>
          
          <!-- Availability Section -->
          <div class="terminal-info-section">
            <div class="terminal-info-title">Availability</div>
            <div class="terminal-info-content">
              <div class="terminal-info-item">
                <span class="terminal-info-icon">✓</span>
                <span>Open for Work</span>
              </div>
              <div class="terminal-info-item">
                <span class="terminal-info-icon">⏰</span>
                <span>Response: 24hrs</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- RIGHT COLUMN: Git Status + Contact Form -->
        <div class="terminal-right-column">
          <!-- Git Status Output -->
          <div class="terminal-git-status">
            <div class="terminal-line">
              <span class="terminal-prompt">$</span>
              <span class="terminal-command">git status --contact</span>
            </div>
            <div class="terminal-line">
              <span class="terminal-text">On branch: connect-with-me</span>
            </div>
            <div class="terminal-line">
              <span class="terminal-text">Changes to be committed:</span>
            </div>
            <div class="terminal-line">
              <span class="terminal-text">  ✓ Your message</span>
            </div>
            <div class="terminal-line">
              <span class="terminal-text">  ✓ Your contact info</span>
            </div>
            <div class="terminal-line">
              <span class="terminal-text">  ✓ Connection established</span>
            </div>
          </div>
          
          <!-- Contact Form -->
          <form class="terminal-form-inline" action="php/contact.php" method="POST">
            <!-- Name Input -->
            <div class="terminal-input-group-inline">
              <label class="terminal-label-inline">
                <span class="terminal-prompt">$</span>
                <span class="terminal-label-text" data-i18n="contact_name"><?=t('contact_name')?></span>
              </label>
              <input 
                type="text" 
                id="name" 
                name="name" 
                class="terminal-input-inline"
                placeholder="your_name" 
                required
              />
            </div>
            
            <!-- Email Input -->
            <div class="terminal-input-group-inline">
              <label class="terminal-label-inline">
                <span class="terminal-prompt">$</span>
                <span class="terminal-label-text" data-i18n="contact_email"><?=t('contact_email')?></span>
              </label>
              <input 
                type="email" 
                id="email" 
                name="email" 
                class="terminal-input-inline"
                placeholder="your@email.com" 
                required
              />
            </div>
            
            <!-- Message Input -->
            <div class="terminal-input-group-inline">
              <label class="terminal-label-inline">
                <span class="terminal-prompt">$</span>
                <span class="terminal-label-text" data-i18n="contact_message"><?=t('contact_message')?></span>
              </label>
              <textarea 
                id="message" 
                name="message" 
                class="terminal-input-inline terminal-textarea-inline"
                placeholder="your_message_here..." 
                required
              ></textarea>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="terminal-submit-btn-inline" data-i18n="contact_send"><?=t('contact_send')?></button>
          </form>
        </div>
      </div>
      
      <!-- Terminal Footer -->
      <div class="terminal-footer-full">
        <div class="terminal-line">
          <span class="terminal-prompt">$</span>
          <span class="terminal-text">_</span>
        </div>
      </div>
    </div>
    
  </section>
</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-container">
    <!-- Footer Top Section -->
    <div class="footer-top">
      <!-- Branding & Status -->
      <div class="footer-section footer-branding">
        <div class="footer-logo">
          <span class="logo-text">EA</span>
          <span class="logo-dot">.</span>
        </div>
        <p class="footer-tagline" data-i18n="footer_tagline"><?=t('footer_tagline')?></p>
        
        <!-- Status Widget -->
        <div class="status-widget">
          <div class="status-dot"></div>
          <span class="status-text" data-i18n="footer_status"><?=t('footer_status')?></span>
        </div>
      </div>
      
      <!-- Quick Links -->
      <div class="footer-section footer-links">
        <h4 data-i18n="footer_links_title"><?=t('footer_links_title')?></h4>
        <ul>
          <li><a href="#about" data-i18n="nav_about"><?=t('nav_about')?></a></li>
          <li><a href="#skills-advanced" data-i18n="nav_skills"><?=t('nav_skills')?></a></li>
          <li><a href="#projects" data-i18n="nav_projects"><?=t('nav_projects')?></a></li>
          <li><a href="#contact" data-i18n="nav_contact"><?=t('nav_contact')?></a></li>
        </ul>
      </div>
      
      <!-- Social Media -->
      <div class="footer-section footer-socials">
        <h4 data-i18n="footer_socials_title"><?=t('footer_socials_title')?></h4>
        <div class="social-icons">
          <!-- GitHub -->
          <a href="https://github.com/elias-araya" target="_blank" rel="noopener" title="GitHub" class="social-icon github-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
          </a>
          
          <!-- LinkedIn -->
          <a href="https://www.linkedin.com/in/elias-araya-cs" target="_blank" rel="noopener" title="LinkedIn" class="social-icon linkedin-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
          </a>
          
          <!-- Twitter/X -->
          <a href="https://twitter.com/elias_araya" target="_blank" rel="noopener" title="Twitter/X" class="social-icon twitter-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
          </a>
          
          <!-- Telegram -->
          <a href="https://t.me/TechPulse_Labs" target="_blank" rel="noopener" title="Telegram" class="social-icon telegram-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
            </svg>
          </a>
          
          <!-- YouTube -->
          <a href="https://www.youtube.com/@Elias_tube_official" target="_blank" rel="noopener" title="YouTube" class="social-icon youtube-icon">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- My Channels -->
      <div class="footer-section footer-brands">
        <h4 data-i18n="footer_brands_title"><?=t('footer_brands_title')?></h4>
        <div class="brand-links">
          <a href="https://t.me/TechPulse_Labs" target="_blank" rel="noopener" class="brand-link tech-pulse">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
            </svg>
            <span class="brand-name">Tech Pulse</span>
            <svg class="brand-arrow" viewBox="0 0 24 24" fill="currentColor" width="12" height="12">
              <path d="M7 7h8.586L5.293 17.293l1.414 1.414L17 8.414V17h2V5H7v2z"/>
            </svg>
          </a>
          <a href="https://www.youtube.com/@Elias_tube_official" target="_blank" rel="noopener" class="brand-link elias-tube">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
            <span class="brand-name">Elias Tube</span>
            <svg class="brand-arrow" viewBox="0 0 24 24" fill="currentColor" width="12" height="12">
              <path d="M7 7h8.586L5.293 17.293l1.414 1.414L17 8.414V17h2V5H7v2z"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
    
    <!-- Footer Divider -->
    <div class="footer-divider"></div>
    
    <!-- Footer Bottom Section -->
    <div class="footer-bottom">
      <div class="footer-copyright">
        <p>&copy; <?=date('Y')?> Elias Araya. Built with HTML, CSS, JavaScript &amp; PHP.</p>
      </div>
      
      <div class="footer-meta">
        <span class="footer-meta-item">
          <span data-i18n="footer_built_with"><?=t('footer_built_with')?></span>
        </span>
        <span class="footer-meta-item">
          <span data-i18n="footer_location"><?=t('footer_location')?></span>
        </span>
      </div>
    </div>
  </div>
</footer>

<script>
// ===== LANGUAGE SWITCHER =====
// ===== INITIALIZATION =====
function initializeApp() {
  console.log('🚀 Initializing app...');
  
  // Theme toggle
  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.classList.toggle('dark-mode', savedTheme === 'dark');
    themeToggle.textContent = savedTheme === 'dark' ? '☀️' : '🌙';

    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      const isDark = document.body.classList.contains('dark-mode');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      themeToggle.textContent = isDark ? '☀️' : '🌙';
    });
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
  
  console.log('✅ App initialized');
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeApp);
} else {
  initializeApp();
}

// PWA Initialization
const script = document.createElement('script');
script.src = '/js/pwa-init.js';
script.async = true;
document.head.appendChild(script);
</script>

<!-- Animation Initialization -->
<script>
// INLINE ANIMATION SYSTEM - Self-contained
console.log('🎬 Animations loaded - Inline system');

// Define ALL keyframes and classes
const animationStyles = `
@keyframes popInBounce {
  0% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes slideUpFade {
  0% { transform: translateY(20px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

@keyframes fadeIn {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

@keyframes fadeInUp {
  0% { transform: translateY(30px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

@keyframes spin360 {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.15); opacity: 0.8; }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.anim-pop-in { animation: popInBounce 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards !important; }
.anim-slide-up { animation: slideUpFade 0.8s ease-out forwards !important; }
.anim-fade-in { animation: fadeIn 0.8s ease-out forwards !important; }
.anim-fade-in-up { animation: fadeInUp 0.8s ease-out forwards !important; }
.anim-spin { animation: spin360 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) !important; }
.anim-pulse { animation: pulse 2s ease-in-out infinite !important; }
`;

// Inject styles
const style = document.createElement('style');
style.textContent = animationStyles;
document.head.appendChild(style);
console.log('✅ Animation styles injected');

// Apply animations
function applyAnimations() {
  console.log('🚀 Applying animations...');
  
  // Profile Image
  const profileImage = document.querySelector('.hero-image');
  if (profileImage) {
    profileImage.classList.add('anim-pop-in');
    console.log('✅ Profile image');
  }
  
  // Hero Title
  const heroTitle = document.querySelector('.hero h1');
  if (heroTitle) {
    heroTitle.style.animationDelay = '0.2s';
    heroTitle.classList.add('anim-slide-up');
    console.log('✅ Hero title');
  }
  
  // Hero Subtitle
  const heroSubtitle = document.querySelector('.hero .subtitle');
  if (heroSubtitle) {
    heroSubtitle.style.animationDelay = '0.3s';
    heroSubtitle.classList.add('anim-slide-up');
    console.log('✅ Hero subtitle');
  }
  
  // Hero Tagline
  const heroTagline = document.querySelector('.hero-tagline');
  if (heroTagline) {
    heroTagline.style.animationDelay = '0.4s';
    heroTagline.classList.add('anim-slide-up');
    console.log('✅ Hero tagline');
  }
  
  // CTA Buttons
  const ctaButtons = document.querySelectorAll('.cta-buttons .btn');
  ctaButtons.forEach((btn, index) => {
    btn.style.animationDelay = `${0.6 + index * 0.1}s`;
    btn.classList.add('anim-fade-in');
  });
  if (ctaButtons.length > 0) console.log(`✅ ${ctaButtons.length} CTA buttons`);
  
  // Social Links
  const socialLinks = document.querySelectorAll('.social-links a');
  socialLinks.forEach((link, index) => {
    link.style.animationDelay = `${0.8 + index * 0.05}s`;
    link.classList.add('anim-fade-in');
  });
  if (socialLinks.length > 0) console.log(`✅ ${socialLinks.length} social links`);
  
  // Magnetic Buttons
  const buttons = document.querySelectorAll('.btn');
  buttons.forEach(button => {
    button.addEventListener('mousemove', (e) => {
      const rect = button.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      const distX = e.clientX - centerX;
      const distY = e.clientY - centerY;
      const distance = Math.sqrt(distX * distX + distY * distY);
      
      if (distance < 100) {
        const strength = (1 - distance / 100) * 15;
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
  console.log('✅ Magnetic buttons');
  
  // Theme Toggle Spin
  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', function() {
      this.classList.remove('anim-spin');
      void this.offsetWidth;
      this.classList.add('anim-spin');
    });
    console.log('✅ Theme toggle');
  }
  
  // Logo Pulse
  const logoDot = document.querySelector('.logo span');
  if (logoDot) {
    logoDot.classList.add('anim-pulse');
    console.log('✅ Logo pulse');
  }
  
  // Scroll Reveal
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('anim-fade-in-up');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  const elementsToObserve = document.querySelectorAll(
    'section, .project-card, .skill-category, .cert-card, .testimonial-card, .about-card'
  );
  
  elementsToObserve.forEach(el => {
    observer.observe(el);
  });
  console.log(`✅ Scroll reveal (${elementsToObserve.length} elements)`);
  
  // ===== SKILLS PROGRESS BAR ANIMATION =====
  const skillsSection = document.getElementById('skills-advanced');
  if (skillsSection) {
    const progressFills = document.querySelectorAll('.progress-fill');
    
    const skillsObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
          // Animate all progress bars in this section
          const sectionProgressFills = entry.target.querySelectorAll('.progress-fill');
          sectionProgressFills.forEach((fill) => {
            const percentage = fill.getAttribute('data-percentage');
            fill.style.setProperty('--percentage', percentage + '%');
            fill.classList.add('animated');
            fill.style.width = percentage + '%';
          });
          skillsObserver.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.3,
      rootMargin: '0px 0px -100px 0px'
    });
    
    skillsObserver.observe(skillsSection);
    console.log('✅ Skills progress bars');
  }
  
  console.log('🎉 All animations applied!');
}

// Start animations
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', applyAnimations);
} else {
  applyAnimations();
}

window.addEventListener('load', applyAnimations);
</script>
</body>
</html>

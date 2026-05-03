-- COMPLETE SETUP: Creates table AND adds all 8 projects
-- Copy and paste this ENTIRE file into phpMyAdmin SQL tab

-- ═══════════════════════════════════════════════════════════
-- STEP 1: Create Database and Table
-- ═══════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- Create projects table
CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  tech VARCHAR(300) NOT NULL,
  category VARCHAR(50) NOT NULL,
  emoji VARCHAR(10) DEFAULT '💻',
  github_url VARCHAR(500),
  live_url VARCHAR(500),
  sort_order INT DEFAULT 0,
  published BOOLEAN DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ═══════════════════════════════════════════════════════════
-- STEP 2: Add All 8 Projects (Organized by Complexity)
-- ═══════════════════════════════════════════════════════════

-- Clear any existing projects
DELETE FROM projects;

-- Insert projects
INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

-- 1. Inventory Management (Production-Ready)
('Inventory Management System', 
'A production-ready inventory management system for tracking and managing stock levels, products, and warehouse operations. Features real-time inventory tracking, automated low-stock alerts, barcode scanning, comprehensive reporting, and multi-user access control.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'fullstack',
'📦',
'https://github.com/Elias-Btech/inventory-management-system',
NULL,
0),

-- 2. Personal Portfolio (This Website!)
('Professional Portfolio Website',
'A modern, feature-rich portfolio website with dark mode, 7-language support (English, Amharic, Tigrinya, Arabic, French, Spanish, Hindi), progressive web app capabilities, dynamic project management, contact system, and responsive design.',
'PHP, MySQL, JavaScript, HTML5, CSS3, PWA, i18n',
'fullstack',
'🎨',
'https://github.com/Elias-Btech/Elias-Btech',
NULL,
1),

-- 3. Population Growth Analysis
('Population Growth Analysis & Forecasting',
'Advanced data analysis project analyzing population growth trends with statistical modeling, trend forecasting, interactive visualizations, and predictive analytics. Demonstrates data science skills with real-world demographic data.',
'Python, Pandas, Matplotlib, NumPy, Statistical Analysis',
'backend',
'📊',
'https://github.com/Elias-Btech/population_growth',
NULL,
2),

-- 4. Knapsack Investment
('Investment Portfolio Optimization',
'Algorithmic solution implementing dynamic programming to solve the knapsack problem for investment portfolio optimization. Maximizes returns within budget constraints using multiple optimization strategies and performance benchmarking.',
'Python, Dynamic Programming, Algorithms, Optimization',
'backend',
'💰',
'https://github.com/Elias-Btech/knapsack-investment',
NULL,
3),

-- 5. ATmega32 FreeRTOS
('Real-Time Embedded System',
'Embedded systems project implementing FreeRTOS on ATmega32 microcontroller. Features multi-task scheduling, interrupt handling, priority management, and real-time LED control demonstrating low-level programming expertise.',
'C, Embedded C, FreeRTOS, ATmega32, RTOS',
'backend',
'💡',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED',
NULL,
4),

-- 6. Enterprise Web Application
('Enterprise-Level Web Application',
'Advanced full-stack application with MVC architecture, secure authentication, role-based access control, database optimization, and scalable design patterns for enterprise business solutions.',
'PHP, MySQL, JavaScript, MVC, Security, Scalability',
'fullstack',
'⚡',
'https://github.com/Elias-Btech/Future_FS_03',
NULL,
5),

-- 7. Full Stack Web Application 02
('Full Stack Web Application',
'Comprehensive web application with user authentication, RESTful API, database integration, responsive UI, and secure data handling demonstrating modern full-stack development practices.',
'PHP, MySQL, JavaScript, Bootstrap, REST API',
'fullstack',
'🚀',
'https://github.com/Elias-Btech/Future_FS_02',
NULL,
6),

-- 8. Full Stack Web Application 01
('Dynamic Web Application',
'Full-stack web project featuring dynamic content management, user interaction, database operations, form validation, and responsive design showcasing core web development fundamentals.',
'PHP, MySQL, JavaScript, HTML5, CSS3',
'fullstack',
'🌐',
'https://github.com/Elias-Btech/Future_FS_01',
NULL,
7);

-- ═══════════════════════════════════════════════════════════
-- STEP 3: Verify Everything
-- ═══════════════════════════════════════════════════════════

-- Check table exists
SHOW TABLES;

-- Check table structure
DESCRIBE projects;

-- Count projects
SELECT COUNT(*) as total_projects FROM projects;

-- View all projects
SELECT 
  sort_order,
  emoji,
  title,
  category
FROM projects 
ORDER BY sort_order;

-- ═══════════════════════════════════════════════════════════
-- SUCCESS! You should see:
-- ✓ Table 'projects' created
-- ✓ 8 rows inserted
-- ✓ All projects listed
-- ═══════════════════════════════════════════════════════════

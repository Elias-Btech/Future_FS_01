-- ORGANIZED PROJECTS BY CATEGORY
-- Projects arranged logically: Full Stack → Data/Algorithms → Embedded
-- This creates a better visual flow on desktop

-- Clear existing projects first (optional)
-- DELETE FROM projects;

INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

-- ═══════════════════════════════════════════════════════════
-- SECTION 1: FULL STACK WEB APPLICATIONS (Most Impressive First)
-- ═══════════════════════════════════════════════════════════

-- 1. Inventory Management (Most Complex)
('Inventory Management System', 
'A comprehensive inventory management system designed to track and manage stock levels, products, and warehouse operations. Features include real-time inventory tracking, automated alerts for low stock, barcode scanning, and detailed reporting capabilities for better inventory control.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'fullstack',
'📦',
'https://github.com/Elias-Btech/inventory-management-system',
NULL,
0),

-- 2. Personal Portfolio (This Website!)
('Personal Portfolio Website',
'A modern, responsive portfolio website showcasing my projects, skills, and experience. Built with clean code architecture, featuring dark mode, multilingual support (7 languages), dynamic project management, and progressive web app capabilities.',
'PHP, MySQL, JavaScript, HTML5, CSS3, PWA',
'fullstack',
'🎨',
'https://github.com/Elias-Btech/Elias-Btech',
NULL,
1),

-- 3. Future Full Stack 03 (Advanced)
('Enterprise Web Application',
'An advanced full-stack web development project showcasing enterprise-level architecture. Implements MVC pattern, secure authentication, database optimization, role-based access control, and modern frontend frameworks for scalable business solutions.',
'PHP, MySQL, JavaScript, MVC, Security Best Practices',
'fullstack',
'⚡',
'https://github.com/Elias-Btech/Future_FS_03',
NULL,
2),

-- ═══════════════════════════════════════════════════════════
-- SECTION 2: DATA SCIENCE & ALGORITHMS
-- ═══════════════════════════════════════════════════════════

-- 4. Population Growth Analysis
('Population Growth Analysis',
'A data analysis and visualization project that analyzes population growth trends and patterns. Includes statistical analysis, trend forecasting, interactive visualizations, and predictive modeling to understand demographic changes and population dynamics over time.',
'Python, Pandas, Matplotlib, NumPy, Data Analysis',
'backend',
'📊',
'https://github.com/Elias-Btech/population_growth',
NULL,
3),

-- 5. Knapsack Investment
('Knapsack Investment Optimization',
'An algorithmic solution to the knapsack problem applied to investment portfolio optimization. Demonstrates dynamic programming techniques to maximize returns while staying within budget constraints. Includes multiple optimization strategies and comprehensive performance analysis.',
'Python, Algorithms, Dynamic Programming, Optimization',
'backend',
'💰',
'https://github.com/Elias-Btech/knapsack-investment',
NULL,
4),

-- ═══════════════════════════════════════════════════════════
-- SECTION 3: EMBEDDED SYSTEMS & HARDWARE
-- ═══════════════════════════════════════════════════════════

-- 6. ATmega32 FreeRTOS
('ATmega32 FreeRTOS LED Control System',
'An embedded systems project implementing real-time operating system (FreeRTOS) on ATmega32 microcontroller for LED control. Features task scheduling, interrupt handling, priority management, and real-time task coordination for embedded applications.',
'C, Embedded C, FreeRTOS, ATmega32, Microcontroller',
'backend',
'💡',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED',
NULL,
5),

-- ═══════════════════════════════════════════════════════════
-- SECTION 4: ADDITIONAL FULL STACK PROJECTS
-- ═══════════════════════════════════════════════════════════

-- 7. Future Full Stack 02
('Full Stack Web Application',
'A comprehensive full-stack web application demonstrating modern development practices. Features include user authentication, database integration, RESTful API design, responsive user interface, and secure data handling.',
'PHP, MySQL, JavaScript, Bootstrap, REST API',
'fullstack',
'🚀',
'https://github.com/Elias-Btech/Future_FS_02',
NULL,
6),

-- 8. Future Full Stack 01
('Web Development Project',
'A full-stack web application demonstrating core web development skills. Features include dynamic content management, user interaction, database operations, form validation, and responsive design principles.',
'PHP, MySQL, JavaScript, HTML5, CSS3',
'fullstack',
'🌐',
'https://github.com/Elias-Btech/Future_FS_01',
NULL,
7);

-- ═══════════════════════════════════════════════════════════
-- VERIFICATION QUERIES
-- ═══════════════════════════════════════════════════════════

-- Check total projects
SELECT COUNT(*) as total_projects FROM projects;

-- View organized projects
SELECT 
  sort_order,
  emoji,
  title,
  category,
  SUBSTRING(tech, 1, 30) as tech_preview
FROM projects 
ORDER BY sort_order;

-- Projects by category
SELECT 
  category,
  COUNT(*) as count,
  GROUP_CONCAT(emoji SEPARATOR ' ') as emojis
FROM projects 
GROUP BY category;

-- ═══════════════════════════════════════════════════════════
-- EXPECTED DESKTOP LAYOUT (3 columns on large screens)
-- ═══════════════════════════════════════════════════════════
-- 
-- Row 1: [📦 Inventory]     [🎨 Portfolio]      [⚡ Enterprise]
-- Row 2: [📊 Population]    [💰 Knapsack]       [💡 ATmega32]
-- Row 3: [🚀 Future FS 02]  [🌐 Future FS 01]
--
-- This creates a logical flow:
-- - Top row: Most impressive full-stack projects
-- - Middle row: Data science & algorithms + embedded
-- - Bottom row: Additional full-stack projects
-- ═══════════════════════════════════════════════════════════

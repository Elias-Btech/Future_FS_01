-- ORGANIZED PROJECTS BY COMPLEXITY & IMPACT
-- Showcase your best work first, then supporting projects
-- Creates maximum impact for recruiters/employers

-- Clear existing projects first (optional)
-- DELETE FROM projects;

INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

-- ═══════════════════════════════════════════════════════════
-- TOP ROW: FLAGSHIP PROJECTS (Most Impressive)
-- ═══════════════════════════════════════════════════════════

-- 1. Inventory Management (Production-Ready System)
('Inventory Management System', 
'A production-ready inventory management system for tracking and managing stock levels, products, and warehouse operations. Features real-time inventory tracking, automated low-stock alerts, barcode scanning, comprehensive reporting, and multi-user access control.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'fullstack',
'📦',
'https://github.com/Elias-Btech/inventory-management-system',
NULL,
0),

-- 2. Personal Portfolio (This Website - Shows Full Stack Skills)
('Professional Portfolio Website',
'A modern, feature-rich portfolio website with dark mode, 7-language support (English, Amharic, Tigrinya, Arabic, French, Spanish, Hindi), progressive web app capabilities, dynamic project management, contact system, and responsive design.',
'PHP, MySQL, JavaScript, HTML5, CSS3, PWA, i18n',
'fullstack',
'🎨',
'https://github.com/Elias-Btech/Elias-Btech',
NULL,
1),

-- 3. Population Growth Analysis (Data Science)
('Population Growth Analysis & Forecasting',
'Advanced data analysis project analyzing population growth trends with statistical modeling, trend forecasting, interactive visualizations, and predictive analytics. Demonstrates data science skills with real-world demographic data.',
'Python, Pandas, Matplotlib, NumPy, Statistical Analysis',
'backend',
'📊',
'https://github.com/Elias-Btech/population_growth',
NULL,
2),

-- ═══════════════════════════════════════════════════════════
-- MIDDLE ROW: SPECIALIZED SKILLS
-- ═══════════════════════════════════════════════════════════

-- 4. Knapsack Investment (Algorithms & Optimization)
('Investment Portfolio Optimization',
'Algorithmic solution implementing dynamic programming to solve the knapsack problem for investment portfolio optimization. Maximizes returns within budget constraints using multiple optimization strategies and performance benchmarking.',
'Python, Dynamic Programming, Algorithms, Optimization',
'backend',
'💰',
'https://github.com/Elias-Btech/knapsack-investment',
NULL,
3),

-- 5. ATmega32 FreeRTOS (Embedded Systems)
('Real-Time Embedded System',
'Embedded systems project implementing FreeRTOS on ATmega32 microcontroller. Features multi-task scheduling, interrupt handling, priority management, and real-time LED control demonstrating low-level programming expertise.',
'C, Embedded C, FreeRTOS, ATmega32, RTOS',
'backend',
'💡',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED',
NULL,
4),

-- 6. Enterprise Web Application (Advanced Architecture)
('Enterprise-Level Web Application',
'Advanced full-stack application with MVC architecture, secure authentication, role-based access control, database optimization, and scalable design patterns for enterprise business solutions.',
'PHP, MySQL, JavaScript, MVC, Security, Scalability',
'fullstack',
'⚡',
'https://github.com/Elias-Btech/Future_FS_03',
NULL,
5),

-- ═══════════════════════════════════════════════════════════
-- BOTTOM ROW: FOUNDATIONAL PROJECTS
-- ═══════════════════════════════════════════════════════════

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
-- VERIFICATION
-- ═══════════════════════════════════════════════════════════

SELECT 
  sort_order,
  emoji,
  title,
  category,
  SUBSTRING(description, 1, 50) as preview
FROM projects 
ORDER BY sort_order;

-- ═══════════════════════════════════════════════════════════
-- DESKTOP LAYOUT (3 columns, 1400px+)
-- ═══════════════════════════════════════════════════════════
--
-- TOP ROW (Flagship Projects):
-- ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
-- │ 📦 Inventory    │ │ 🎨 Portfolio    │ │ 📊 Population   │
-- │ Management      │ │ Website         │ │ Analysis        │
-- │ (Production)    │ │ (This Site!)    │ │ (Data Science)  │
-- └─────────────────┘ └─────────────────┘ └─────────────────┘
--
-- MIDDLE ROW (Specialized Skills):
-- ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
-- │ 💰 Knapsack     │ │ 💡 ATmega32     │ │ ⚡ Enterprise   │
-- │ Investment      │ │ FreeRTOS        │ │ Web App         │
-- │ (Algorithms)    │ │ (Embedded)      │ │ (MVC)           │
-- └─────────────────┘ └─────────────────┘ └─────────────────┘
--
-- BOTTOM ROW (Foundational):
-- ┌─────────────────┐ ┌─────────────────┐
-- │ 🚀 Full Stack   │ │ 🌐 Web Dev      │
-- │ App 02          │ │ Project 01      │
-- └─────────────────┘ └─────────────────┘
--
-- ═══════════════════════════════════════════════════════════
-- RATIONALE:
-- - Top row shows your BEST work (production system, this portfolio, data science)
-- - Middle row shows SPECIALIZED skills (algorithms, embedded, architecture)
-- - Bottom row shows FOUNDATIONAL skills (solid but less complex)
-- - Creates a "wow factor" with strongest projects first
-- ═══════════════════════════════════════════════════════════

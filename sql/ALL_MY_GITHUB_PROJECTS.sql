-- ALL Elias-Btech's GitHub Projects
-- Complete list from your GitHub repository
-- Run this to populate your portfolio with all your real projects

-- Clear existing projects (optional - comment out if you want to keep existing)
-- DELETE FROM projects;

INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

-- Project 1: Knapsack Investment
('Knapsack Investment Optimization', 
'An algorithmic solution to the knapsack problem applied to investment portfolio optimization. Demonstrates dynamic programming techniques to maximize returns while staying within budget constraints. Includes multiple optimization strategies and performance analysis.',
'Python, Algorithms, Dynamic Programming, Optimization',
'backend',
'💰',
'https://github.com/Elias-Btech/knapsack-investment',
NULL,
0),

-- Project 2: Population Growth Analysis
('Population Growth Analysis',
'A data analysis and visualization project that analyzes population growth trends and patterns. Includes statistical analysis, trend forecasting, and interactive visualizations to understand demographic changes and population dynamics over time.',
'Python, Pandas, Matplotlib, NumPy, Data Analysis',
'backend',
'📊',
'https://github.com/Elias-Btech/population_growth',
NULL,
1),

-- Project 3: Personal Portfolio (Elias-Btech)
('Personal Portfolio Website',
'A modern, responsive portfolio website showcasing my projects, skills, and experience. Built with clean code architecture, featuring dark mode, multilingual support, and dynamic project management system.',
'PHP, MySQL, JavaScript, HTML5, CSS3, Responsive Design',
'fullstack',
'🎨',
'https://github.com/Elias-Btech/Elias-Btech',
NULL,
2),

-- Project 4: Future Full Stack Project 02
('Future Full Stack Application 02',
'A comprehensive full-stack web application demonstrating modern development practices. Features include user authentication, database integration, RESTful API design, and responsive user interface.',
'PHP, MySQL, JavaScript, Bootstrap, REST API',
'fullstack',
'🚀',
'https://github.com/Elias-Btech/Future_FS_02',
NULL,
3),

-- Project 5: ATmega32 FreeRTOS LED Control
('ATmega32 FreeRTOS LED Control System',
'An embedded systems project implementing real-time operating system (FreeRTOS) on ATmega32 microcontroller for LED control. Features task scheduling, interrupt handling, and real-time task management for embedded applications.',
'C, Embedded C, FreeRTOS, ATmega32, Microcontroller Programming',
'backend',
'💡',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED',
NULL,
4),

-- Project 6: Inventory Management System
('Inventory Management System', 
'A comprehensive inventory management system designed to track and manage stock levels, products, and warehouse operations. Features include real-time inventory tracking, automated alerts for low stock, barcode scanning, and detailed reporting capabilities for better inventory control.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'fullstack',
'📦',
'https://github.com/Elias-Btech/inventory-management-system',
NULL,
5),

-- Project 7: Future Full Stack Project 03
('Future Full Stack Application 03',
'An advanced full-stack web development project showcasing enterprise-level architecture. Implements MVC pattern, secure authentication, database optimization, and modern frontend frameworks.',
'PHP, MySQL, JavaScript, MVC Architecture, Security',
'fullstack',
'⚡',
'https://github.com/Elias-Btech/Future_FS_03',
NULL,
6),

-- Project 8: Future Full Stack Project 01
('Future Full Stack Application 01',
'A full-stack web application demonstrating core web development skills. Features include dynamic content management, user interaction, database operations, and responsive design principles.',
'PHP, MySQL, JavaScript, HTML5, CSS3',
'fullstack',
'🌐',
'https://github.com/Elias-Btech/Future_FS_01',
NULL,
7);

-- Verify the insert
SELECT COUNT(*) as total_projects FROM projects;
SELECT id, title, category, emoji, github_url FROM projects ORDER BY sort_order;

-- Check projects by category
SELECT category, COUNT(*) as count FROM projects GROUP BY category;

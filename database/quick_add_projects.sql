-- Quick Add: Elias's 4 Real Projects
-- Run this in phpMyAdmin SQL tab or command line

-- First, clear any existing projects (optional - remove if you want to keep existing)
-- DELETE FROM projects;

-- Add your 4 real projects
INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

('Inventory Management System', 
'A comprehensive inventory management system designed to track and manage stock levels, products, and warehouse operations. Features include real-time inventory tracking, automated alerts for low stock, barcode scanning, and detailed reporting capabilities for better inventory control.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'fullstack',
'📦',
'https://github.com/Elias-Btech/inventory-management-system',
NULL,
0),

('Population Growth Analysis',
'A data analysis and visualization project that analyzes population growth trends and patterns. Includes statistical analysis, trend forecasting, and interactive visualizations to understand demographic changes and population dynamics over time.',
'Python, Pandas, Matplotlib, NumPy, Data Analysis',
'backend',
'📊',
'https://github.com/Elias-Btech/population_growth',
NULL,
1),

('Knapsack Investment Optimization',
'An algorithmic solution to the knapsack problem applied to investment portfolio optimization. Demonstrates dynamic programming techniques to maximize returns while staying within budget constraints. Includes multiple optimization strategies and performance analysis.',
'Python, Algorithms, Dynamic Programming, Optimization',
'backend',
'💰',
'https://github.com/Elias-Btech/knapsack-investment',
NULL,
2),

('ATmega32 FreeRTOS LED Control System',
'An embedded systems project implementing real-time operating system (FreeRTOS) on ATmega32 microcontroller for LED control. Features task scheduling, interrupt handling, and real-time task management for embedded applications.',
'C, Embedded C, FreeRTOS, ATmega32, Microcontroller Programming',
'backend',
'💡',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED',
NULL,
3);

-- Verify the insert
SELECT COUNT(*) as total_projects FROM projects;
SELECT title, category, emoji FROM projects ORDER BY sort_order;

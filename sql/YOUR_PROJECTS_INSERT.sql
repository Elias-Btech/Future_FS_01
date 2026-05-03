-- Elias Btech's Real Projects
-- Insert these into your database to populate the portfolio with your actual projects

INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

-- Project 1: Inventory Management System
('Inventory Management System', 
'A comprehensive inventory management system designed to track and manage stock levels, products, and warehouse operations. Features include real-time inventory tracking, automated alerts for low stock, barcode scanning, and detailed reporting capabilities for better inventory control.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'fullstack',
'📦',
'https://github.com/Elias-Btech/inventory-management-system',
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

-- Project 3: Knapsack Investment Problem
('Knapsack Investment Optimization',
'An algorithmic solution to the knapsack problem applied to investment portfolio optimization. Demonstrates dynamic programming techniques to maximize returns while staying within budget constraints. Includes multiple optimization strategies and performance analysis.',
'Python, Algorithms, Dynamic Programming, Optimization',
'backend',
'💰',
'https://github.com/Elias-Btech/knapsack-investment',
NULL,
2),

-- Project 4: ATmega32 FreeRTOS LED Control
('ATmega32 FreeRTOS LED Control System',
'An embedded systems project implementing real-time operating system (FreeRTOS) on ATmega32 microcontroller for LED control. Features task scheduling, interrupt handling, and real-time task management for embedded applications.',
'C, Embedded C, FreeRTOS, ATmega32, Microcontroller Programming',
'backend',
'💡',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED',
NULL,
3);

-- Note: These are your real projects from GitHub
-- To insert these, run: mysql -u root -p portfolio_db < YOUR_PROJECTS_INSERT.sql
-- Or copy and paste the INSERT statements into your database management tool

-- Extended database schema for blog and case studies
-- Run this AFTER config/setup.sql to add new tables

USE portfolio_db;

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS blog_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) UNIQUE NOT NULL,
  content LONGTEXT NOT NULL,
  excerpt VARCHAR(500),
  category VARCHAR(50),
  featured_image VARCHAR(500),
  author_id INT UNSIGNED,
  views INT UNSIGNED DEFAULT 0,
  published BOOLEAN DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog Comments Table
CREATE TABLE IF NOT EXISTS blog_comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  author_name VARCHAR(100) NOT NULL,
  author_email VARCHAR(254) NOT NULL,
  content TEXT NOT NULL,
  approved BOOLEAN DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Case Studies Table
CREATE TABLE IF NOT EXISTS case_studies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) UNIQUE NOT NULL,
  description TEXT NOT NULL,
  challenge TEXT NOT NULL,
  solution TEXT NOT NULL,
  results TEXT NOT NULL,
  tech_stack VARCHAR(300),
  project_url VARCHAR(500),
  github_url VARCHAR(500),
  featured_image VARCHAR(500),
  published BOOLEAN DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Blog Posts
INSERT IGNORE INTO blog_posts (title, slug, content, excerpt, category, published) VALUES
('Getting Started with PHP 8', 'getting-started-php-8', 
'PHP 8 brings significant improvements to the language including named arguments, match expressions, and attributes. In this post, we explore the key features that make PHP 8 a game-changer for web developers.',
'Explore the key features of PHP 8 and how they improve web development.',
'Backend', 1),

('Building Secure Web Applications', 'building-secure-web-apps',
'Security is paramount in web development. Learn about CSRF protection, SQL injection prevention, XSS mitigation, and best practices for building secure applications.',
'Essential security practices for modern web applications.',
'Security', 1),

('Database Optimization Techniques', 'database-optimization',
'Optimize your database queries and improve application performance. Learn about indexing, query optimization, and caching strategies.',
'Improve database performance with proven optimization techniques.',
'Backend', 1);

-- Sample Case Studies
INSERT IGNORE INTO case_studies (title, slug, description, challenge, solution, results, tech_stack, github_url, published) VALUES
('Inventory Management System', 'inventory-management-system',
'A comprehensive inventory management system for tracking and managing stock levels.',
'Manual inventory tracking was error-prone and time-consuming, leading to stock discrepancies and lost sales opportunities.',
'Built a comprehensive web-based system with real-time inventory tracking, automated low-stock alerts, barcode scanning, and detailed reporting capabilities.',
'Reduced inventory errors by 95%, improved stock accuracy, and enabled data-driven decision making through comprehensive analytics.',
'PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX',
'https://github.com/Elias-Btech/inventory-management-system', 1),

('Population Growth Analysis', 'population-growth-analysis',
'Data analysis and visualization project analyzing population growth trends.',
'Understanding complex demographic trends required manual data analysis and static visualizations that didn\'t reveal actionable insights.',
'Developed an automated data analysis pipeline with interactive visualizations, trend forecasting, and statistical analysis of population dynamics.',
'Identified key demographic trends, enabled predictive modeling, and provided actionable insights for policy makers.',
'Python, Pandas, Matplotlib, NumPy, Data Analysis',
'https://github.com/Elias-Btech/population_growth', 1),

('Knapsack Investment Optimization', 'knapsack-investment',
'Algorithmic solution to the knapsack problem for investment portfolio optimization.',
'Portfolio optimization required selecting the best investments within budget constraints—a computationally complex problem.',
'Implemented dynamic programming algorithms to solve the knapsack problem, maximizing returns while respecting budget constraints.',
'Achieved optimal portfolio allocation, demonstrated algorithmic efficiency, and provided multiple optimization strategies.',
'Python, Algorithms, Dynamic Programming, Optimization',
'https://github.com/Elias-Btech/knapsack-investment', 1),

('ATmega32 FreeRTOS LED Control', 'atmega32-freertos-led',
'Embedded systems project implementing real-time operating system on microcontroller.',
'Managing multiple concurrent tasks on a microcontroller required sophisticated real-time scheduling and interrupt handling.',
'Implemented FreeRTOS on ATmega32 with task scheduling, interrupt handlers, and real-time task management for LED control.',
'Successfully managed concurrent tasks, demonstrated embedded systems expertise, and created a scalable real-time system.',
'C, Embedded C, FreeRTOS, ATmega32, Microcontroller Programming',
'https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED', 1);

-- Sample Projects for Elias Araya Portfolio
-- Insert these into your database to populate the portfolio with example projects

INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order) VALUES

-- Project 1: E-Commerce Platform
('ShopFlow E-Commerce Platform', 
'A full-stack e-commerce solution with product catalog, shopping cart, payment integration, and admin dashboard. Features include user authentication, order management, inventory tracking, and real-time notifications.',
'PHP, MySQL, JavaScript, Bootstrap, Stripe API, PDO',
'fullstack',
'🛍️',
'https://github.com/eliasaraya/shopflow',
'https://shopflow-demo.com',
0),

-- Project 2: Task Management App
('TaskMaster - Collaborative Task Manager',
'A real-time task management application with team collaboration features. Users can create projects, assign tasks, set deadlines, track progress, and communicate with team members. Includes Kanban board view and timeline visualization.',
'React, Node.js, Express, MongoDB, Socket.io, JWT',
'fullstack',
'✅',
'https://github.com/eliasaraya/taskmaster',
'https://taskmaster-app.com',
1),

-- Project 3: Weather Dashboard
('WeatherHub - Real-time Weather Dashboard',
'A responsive weather application that displays current weather, forecasts, and historical data. Features include location search, multiple weather metrics, interactive maps, and weather alerts. Built with modern web technologies.',
'HTML5, CSS3, JavaScript (ES6+), OpenWeatherMap API, Chart.js',
'frontend',
'🌤️',
'https://github.com/eliasaraya/weatherhub',
'https://weatherhub-app.com',
2),

-- Project 4: Blog Platform
('BlogVerse - Content Management System',
'A feature-rich blogging platform with markdown support, categories, tags, search functionality, and comment system. Includes admin panel for content management, SEO optimization, and social media integration.',
'PHP 8, MySQL, JavaScript, TinyMCE, Composer',
'fullstack',
'📝',
'https://github.com/eliasaraya/blogverse',
'https://blogverse-cms.com',
3),

-- Project 5: Portfolio Website
('Portfolio Generator',
'An automated portfolio website generator that allows developers to create professional portfolios without coding. Features include customizable templates, project showcase, skill display, and contact forms.',
'React, Node.js, Express, PostgreSQL, Tailwind CSS',
'fullstack',
'🎨',
'https://github.com/eliasaraya/portfolio-generator',
'https://portfolio-gen.com',
4),

-- Project 6: Chat Application
('ChatSync - Real-time Messaging App',
'A real-time chat application with user authentication, group chats, file sharing, and message encryption. Features include typing indicators, read receipts, user presence, and notification system.',
'Node.js, Express, Socket.io, MongoDB, React, JWT',
'fullstack',
'💬',
'https://github.com/eliasaraya/chatsync',
'https://chatsync-app.com',
5),

-- Project 7: API Development
('RESTful API - User Management System',
'A comprehensive REST API for user management with authentication, authorization, role-based access control, and comprehensive documentation. Includes rate limiting, input validation, and security best practices.',
'Node.js, Express, MongoDB, JWT, Swagger',
'backend',
'⚙️',
'https://github.com/eliasaraya/user-api',
NULL,
6),

-- Project 8: Data Visualization
('Analytics Dashboard',
'An interactive analytics dashboard displaying real-time data with charts, graphs, and metrics. Features include data filtering, export functionality, and customizable widgets.',
'React, D3.js, Chart.js, Axios, Material-UI',
'frontend',
'📊',
'https://github.com/eliasaraya/analytics-dashboard',
'https://analytics-demo.com',
7),

-- Project 9: Mobile App
('FitTrack - Fitness Tracking App',
'A mobile fitness tracking application with workout logging, progress tracking, nutrition monitoring, and social features. Includes push notifications and data synchronization.',
'React Native, Firebase, Redux, Expo',
'fullstack',
'💪',
'https://github.com/eliasaraya/fittrack',
NULL,
8),

-- Project 10: Database Design
('E-Learning Platform Database',
'A comprehensive database design for an e-learning platform with course management, student enrollment, progress tracking, and assessment systems. Includes optimization and indexing strategies.',
'MySQL, SQL, Database Design, Normalization',
'backend',
'🎓',
'https://github.com/eliasaraya/elearning-db',
NULL,
9);

-- Note: Update the github_url and live_url with your actual links
-- To insert these, run: mysql -u root portfolio_db < SAMPLE_PROJECTS_INSERT.sql

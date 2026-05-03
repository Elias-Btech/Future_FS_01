<?php
/**
 * config/analytics.php
 * Analytics & Statistics Module
 * Tracks portfolio metrics and user interactions
 */

/**
 * Get dashboard statistics
 * 
 * @return array Statistics data
 */
function getDashboardStats() {
    try {
        $db = getDB();
        
        // Total projects
        $projects = $db->query('SELECT COUNT(*) as count FROM projects')->fetch();
        $totalProjects = $projects['count'] ?? 0;
        
        // Total messages
        $messages = $db->query('SELECT COUNT(*) as count FROM contact_messages')->fetch();
        $totalMessages = $messages['count'] ?? 0;
        
        // Messages this month
        $thisMonth = $db->query(
            "SELECT COUNT(*) as count FROM contact_messages 
             WHERE MONTH(created_at) = MONTH(NOW()) 
             AND YEAR(created_at) = YEAR(NOW())"
        )->fetch();
        $messagesThisMonth = $thisMonth['count'] ?? 0;
        
        // Messages this week
        $thisWeek = $db->query(
            "SELECT COUNT(*) as count FROM contact_messages 
             WHERE WEEK(created_at) = WEEK(NOW()) 
             AND YEAR(created_at) = YEAR(NOW())"
        )->fetch();
        $messagesThisWeek = $thisWeek['count'] ?? 0;
        
        // Latest messages
        $latest = $db->query(
            "SELECT name, email, created_at FROM contact_messages 
             ORDER BY created_at DESC LIMIT 5"
        )->fetchAll();
        
        // Messages by date (last 7 days)
        $byDate = $db->query(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM contact_messages 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(created_at)
             ORDER BY date ASC"
        )->fetchAll();
        
        // Top senders (most messages)
        $topSenders = $db->query(
            "SELECT email, COUNT(*) as count 
             FROM contact_messages 
             GROUP BY email 
             ORDER BY count DESC 
             LIMIT 5"
        )->fetchAll();
        
        // Total users
        $users = $db->query('SELECT COUNT(*) as count FROM users')->fetch();
        $totalUsers = $users['count'] ?? 0;
        
        return [
            'projects' => [
                'total' => $totalProjects,
                'icon' => '🗂️'
            ],
            'messages' => [
                'total' => $totalMessages,
                'thisMonth' => $messagesThisMonth,
                'thisWeek' => $messagesThisWeek,
                'latest' => $latest,
                'byDate' => $byDate,
                'topSenders' => $topSenders,
                'icon' => '💬'
            ],
            'users' => [
                'total' => $totalUsers,
                'icon' => '👤'
            ]
        ];
    } catch (Exception $e) {
        error_log('Analytics error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get project statistics
 * 
 * @return array Project stats
 */
function getProjectStats() {
    try {
        $db = getDB();
        
        // Projects by category
        $byCategory = $db->query(
            "SELECT category, COUNT(*) as count 
             FROM projects 
             GROUP BY category 
             ORDER BY count DESC"
        )->fetchAll();
        
        // Most recent projects
        $recent = $db->query(
            "SELECT id, title, category, created_at 
             FROM projects 
             ORDER BY created_at DESC 
             LIMIT 5"
        )->fetchAll();
        
        // Total tech stack count
        $allProjects = $db->query('SELECT tech FROM projects')->fetchAll();
        $techCount = [];
        foreach ($allProjects as $p) {
            $techs = array_map('trim', explode(',', $p['tech']));
            foreach ($techs as $tech) {
                $techCount[$tech] = ($techCount[$tech] ?? 0) + 1;
            }
        }
        arsort($techCount);
        $topTechs = array_slice($techCount, 0, 10, true);
        
        return [
            'byCategory' => $byCategory,
            'recent' => $recent,
            'topTechs' => $topTechs
        ];
    } catch (Exception $e) {
        error_log('Project stats error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get visitor statistics
 * 
 * @return array Visitor stats
 */
function getVisitorStats() {
    try {
        $db = getDB();
        
        // Unique IPs from messages
        $uniqueIPs = $db->query(
            "SELECT COUNT(DISTINCT ip_address) as count 
             FROM contact_messages"
        )->fetch();
        
        // Messages by IP (top senders)
        $byIP = $db->query(
            "SELECT ip_address, COUNT(*) as count 
             FROM contact_messages 
             GROUP BY ip_address 
             ORDER BY count DESC 
             LIMIT 10"
        )->fetchAll();
        
        return [
            'uniqueIPs' => $uniqueIPs['count'] ?? 0,
            'byIP' => $byIP
        ];
    } catch (Exception $e) {
        error_log('Visitor stats error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Log page view
 * 
 * @param string $page Page name
 * @param string $ip IP address
 */
function logPageView($page, $ip) {
    try {
        $logFile = __DIR__ . '/../logs/pageviews.log';
        
        // Create logs directory if not exists
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] Page: $page | IP: $ip\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    } catch (Exception $e) {
        error_log('Page view logging error: ' . $e->getMessage());
    }
}

/**
 * Get system health status
 * 
 * @return array Health status
 */
function getSystemHealth() {
    $health = [
        'database' => 'unknown',
        'files' => 'unknown',
        'cache' => 'unknown',
        'email' => 'unknown'
    ];
    
    // Check database
    try {
        $db = getDB();
        $db->query('SELECT 1');
        $health['database'] = 'healthy';
    } catch (Exception $e) {
        $health['database'] = 'error';
    }
    
    // Check file permissions
    $criticalFiles = [
        __DIR__ . '/../logs',
        __DIR__ . '/../config',
        __DIR__ . '/../php'
    ];
    
    $filesOk = true;
    foreach ($criticalFiles as $file) {
        if (!is_writable($file)) {
            $filesOk = false;
            break;
        }
    }
    $health['files'] = $filesOk ? 'healthy' : 'warning';
    
    // Check cache
    if (function_exists('apcu_cache_info')) {
        $health['cache'] = 'healthy';
    } else {
        $health['cache'] = 'unavailable';
    }
    
    // Check email
    if (function_exists('mail')) {
        $health['email'] = 'available';
    } else {
        $health['email'] = 'unavailable';
    }
    
    return $health;
}

?>

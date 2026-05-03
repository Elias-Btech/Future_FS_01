<?php
/**
 * config/backup.php
 * Backup & Restore System
 * Handles database and file backups
 */

define('BACKUP_DIR', __DIR__ . '/../backups');
define('MAX_BACKUPS', 10);

/**
 * Create database backup
 * 
 * @return array Result with status and filename
 */
function createDatabaseBackup() {
    try {
        // Create backup directory if not exists
        if (!is_dir(BACKUP_DIR)) {
            mkdir(BACKUP_DIR, 0755, true);
        }
        
        $db = getDB();
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup_db_{$timestamp}.sql";
        $filepath = BACKUP_DIR . '/' . $filename;
        
        // Get database name
        $dbName = getenv('DB_NAME') ?: 'portfolio';
        
        // Get all tables
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        $sql = "-- Database Backup\n";
        $sql .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: $dbName\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $createTable = $db->query("SHOW CREATE TABLE `$table`")->fetch();
            $sql .= $createTable['Create Table'] . ";\n\n";
            
            // Get table data
            $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($rows as $row) {
                $columns = implode('`, `', array_keys($row));
                $values = implode("', '", array_map(function($v) {
                    return addslashes($v);
                }, array_values($row)));
                
                $sql .= "INSERT INTO `$table` (`$columns`) VALUES ('$values');\n";
            }
            
            $sql .= "\n";
        }
        
        // Write to file
        if (file_put_contents($filepath, $sql)) {
            // Clean old backups
            cleanOldBackups();
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => filesize($filepath),
                'timestamp' => $timestamp
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to write backup file'
            ];
        }
    } catch (Exception $e) {
        error_log('Backup error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Restore database from backup
 * 
 * @param string $filename Backup filename
 * @return array Result with status
 */
function restoreDatabaseBackup($filename) {
    try {
        // Validate filename
        if (!preg_match('/^backup_db_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $filename)) {
            return [
                'success' => false,
                'error' => 'Invalid backup filename'
            ];
        }
        
        $filepath = BACKUP_DIR . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'error' => 'Backup file not found'
            ];
        }
        
        $db = getDB();
        $sql = file_get_contents($filepath);
        
        // Execute SQL statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !str_starts_with($statement, '--')) {
                $db->exec($statement);
            }
        }
        
        return [
            'success' => true,
            'message' => 'Database restored successfully',
            'filename' => $filename
        ];
    } catch (Exception $e) {
        error_log('Restore error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Get list of backups
 * 
 * @return array List of backup files
 */
function getBackupList() {
    try {
        if (!is_dir(BACKUP_DIR)) {
            return [];
        }
        
        $files = scandir(BACKUP_DIR, SCANDIR_SORT_DESCENDING);
        $backups = [];
        
        foreach ($files as $file) {
            if (preg_match('/^backup_db_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $file)) {
                $filepath = BACKUP_DIR . '/' . $file;
                $backups[] = [
                    'filename' => $file,
                    'size' => filesize($filepath),
                    'created' => filemtime($filepath),
                    'created_formatted' => date('Y-m-d H:i:s', filemtime($filepath))
                ];
            }
        }
        
        return $backups;
    } catch (Exception $e) {
        error_log('Backup list error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Delete backup file
 * 
 * @param string $filename Backup filename
 * @return array Result with status
 */
function deleteBackup($filename) {
    try {
        // Validate filename
        if (!preg_match('/^backup_db_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $filename)) {
            return [
                'success' => false,
                'error' => 'Invalid backup filename'
            ];
        }
        
        $filepath = BACKUP_DIR . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'error' => 'Backup file not found'
            ];
        }
        
        if (unlink($filepath)) {
            return [
                'success' => true,
                'message' => 'Backup deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to delete backup'
            ];
        }
    } catch (Exception $e) {
        error_log('Delete backup error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Clean old backups (keep only MAX_BACKUPS)
 */
function cleanOldBackups() {
    try {
        $backups = getBackupList();
        
        if (count($backups) > MAX_BACKUPS) {
            $toDelete = array_slice($backups, MAX_BACKUPS);
            
            foreach ($toDelete as $backup) {
                deleteBackup($backup['filename']);
            }
        }
    } catch (Exception $e) {
        error_log('Clean backups error: ' . $e->getMessage());
    }
}

/**
 * Get backup statistics
 * 
 * @return array Backup stats
 */
function getBackupStats() {
    try {
        $backups = getBackupList();
        $totalSize = 0;
        
        foreach ($backups as $backup) {
            $totalSize += $backup['size'];
        }
        
        $lastBackup = !empty($backups) ? $backups[0] : null;
        
        return [
            'count' => count($backups),
            'totalSize' => $totalSize,
            'totalSizeFormatted' => formatBytes($totalSize),
            'lastBackup' => $lastBackup,
            'maxBackups' => MAX_BACKUPS
        ];
    } catch (Exception $e) {
        error_log('Backup stats error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Format bytes to human readable
 * 
 * @param int $bytes Bytes
 * @return string Formatted size
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

?>

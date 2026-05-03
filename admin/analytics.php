<?php
/**
 * admin/analytics.php
 * Analytics Dashboard
 * Displays portfolio statistics and analytics
 */

session_start();
require_once __DIR__.'/../config/security.php';
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../config/i18n.php';
require_once __DIR__.'/../config/analytics.php';
require_once __DIR__.'/../config/backup.php';

// Initialize security
initializeSecurity();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Enforce session timeout
if (!enforceSessionTimeout(1800)) {
    header('Location: ../login.php?expired=1');
    exit;
}

// Get statistics
$stats = getDashboardStats();
$projectStats = getProjectStats();
$visitorStats = getVisitorStats();
$health = getSystemHealth();
$backupStats = getBackupStats();

?><!DOCTYPE html>
<html lang="<?=getCurrentLanguage()?>" data-theme="light">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Analytics - Elias Araya Portfolio</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
:root{--p:#2563EB;--pd:#1D4ED8;--ac:#14B8A6;--bg:#F8FAFC;--card:#fff;--tx:#0F172A;--tx2:#475569;--tx3:#94A3B8;--br:#E2E8F0;--ok:#10B981;--warn:#F59E0B;--err:#EF4444;--sh:0 2px 12px rgba(0,0,0,.06);--fn:'Inter',sans-serif;--tr:.25s ease}
[data-theme=dark]{--bg:#0F172A;--card:#1E293B;--tx:#F1F5F9;--tx2:#CBD5E1;--tx3:#64748B;--br:#334155;--sh:0 2px 12px rgba(0,0,0,.3)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:16px}
body{font-family:var(--fn);background:var(--bg);color:var(--tx);transition:background var(--tr),color var(--tr)}
.container{max-width:1200px;margin:0 auto;padding:20px}
h1{font-size:2rem;font-weight:900;margin-bottom:24px;letter-spacing:-.02em}
h2{font-size:1.3rem;font-weight:800;margin-bottom:16px;color:var(--p)}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-bottom:24px}
.card{background:var(--card);border:1px solid var(--br);border-radius:12px;padding:20px;box-shadow:var(--sh);transition:all var(--tr)}
.card:hover{transform:translateY(-2px);box-shadow:0 4px 20px rgba(0,0,0,.1)}
.stat-value{font-size:2rem;font-weight:900;color:var(--p);margin-bottom:8px}
.stat-label{font-size:.85rem;color:var(--tx3);font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.chart{background:var(--card);border:1px solid var(--br);border-radius:12px;padding:20px;margin-bottom:24px}
.bar{display:flex;align-items:center;margin-bottom:12px}
.bar-label{width:120px;font-size:.85rem;font-weight:600}
.bar-fill{flex:1;height:24px;background:linear-gradient(90deg,var(--p),var(--ac));border-radius:4px;margin:0 12px;position:relative}
.bar-value{width:60px;text-align:right;font-size:.85rem;font-weight:600}
.health{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px}
.health-item{padding:12px;border-radius:8px;text-align:center;font-size:.85rem;font-weight:600}
.health-healthy{background:rgba(16,185,129,.1);color:#059669}
.health-warning{background:rgba(245,158,11,.1);color:#D97706}
.health-error{background:rgba(239,68,68,.1);color:#DC2626}
.health-unavailable{background:rgba(148,163,184,.1);color:#64748B}
table{width:100%;border-collapse:collapse;background:var(--card);border-radius:12px;overflow:hidden;border:1px solid var(--br)}
th{background:var(--bg);font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--tx3);padding:12px 16px;text-align:left;border-bottom:1px solid var(--br)}
td{padding:12px 16px;border-bottom:1px solid var(--br);font-size:.85rem}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(37,99,235,.03)}
.btn{display:inline-block;padding:10px 16px;background:var(--p);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:.85rem;transition:all var(--tr)}
.btn:hover{background:var(--pd);transform:translateY(-1px)}
@media(max-width:640px){.grid{grid-template-columns:1fr}h1{font-size:1.5rem}}
</style>
</head>
<body>

<div class="container">
    <h1>📊 Analytics Dashboard</h1>
    
    <!-- Key Metrics -->
    <div class="grid">
        <div class="card">
            <div class="stat-value"><?=$stats['projects']['total'] ?? 0?></div>
            <div class="stat-label"><?=$stats['projects']['icon']?> Projects</div>
        </div>
        
        <div class="card">
            <div class="stat-value"><?=$stats['messages']['total'] ?? 0?></div>
            <div class="stat-label"><?=$stats['messages']['icon']?> Total Messages</div>
        </div>
        
        <div class="card">
            <div class="stat-value"><?=$stats['messages']['thisMonth'] ?? 0?></div>
            <div class="stat-label">📅 This Month</div>
        </div>
        
        <div class="card">
            <div class="stat-value"><?=$stats['messages']['thisWeek'] ?? 0?></div>
            <div class="stat-label">📆 This Week</div>
        </div>
        
        <div class="card">
            <div class="stat-value"><?=$stats['users']['total'] ?? 0?></div>
            <div class="stat-label"><?=$stats['users']['icon']?> Users</div>
        </div>
        
        <div class="card">
            <div class="stat-value"><?=$visitorStats['uniqueIPs'] ?? 0?></div>
            <div class="stat-label">🌍 Unique Visitors</div>
        </div>
    </div>
    
    <!-- System Health -->
    <div class="chart">
        <h2>🏥 System Health</h2>
        <div class="health">
            <?php foreach ($health as $service => $status): ?>
                <div class="health-item health-<?=$status?>">
                    <div><?=ucfirst($service)?></div>
                    <div><?=ucfirst($status)?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Backup Status -->
    <div class="chart">
        <h2>💾 Backup Status</h2>
        <div class="grid">
            <div class="card">
                <div class="stat-value"><?=$backupStats['count'] ?? 0?></div>
                <div class="stat-label">Backups</div>
            </div>
            <div class="card">
                <div class="stat-value"><?=$backupStats['totalSizeFormatted'] ?? '0 B'?></div>
                <div class="stat-label">Total Size</div>
            </div>
            <div class="card">
                <div class="stat-value"><?=$backupStats['maxBackups'] ?? 10?></div>
                <div class="stat-label">Max Kept</div>
            </div>
        </div>
        <?php if ($backupStats['lastBackup']): ?>
            <p style="margin-top:12px;font-size:.85rem;color:var(--tx3)">
                Last backup: <?=$backupStats['lastBackup']['created_formatted']?>
            </p>
        <?php endif; ?>
    </div>
    
    <!-- Messages by Date -->
    <?php if (!empty($stats['messages']['byDate'])): ?>
    <div class="chart">
        <h2>📈 Messages by Date (Last 7 Days)</h2>
        <?php foreach ($stats['messages']['byDate'] as $data): ?>
            <div class="bar">
                <div class="bar-label"><?=date('M d', strtotime($data['date']))?></div>
                <div class="bar-fill" style="width:<?=($data['count'] / max(array_column($stats['messages']['byDate'], 'count'), 1) * 100)?>%"></div>
                <div class="bar-value"><?=$data['count']?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Top Technologies -->
    <?php if (!empty($projectStats['topTechs'])): ?>
    <div class="chart">
        <h2>🛠️ Top Technologies Used</h2>
        <?php foreach (array_slice($projectStats['topTechs'], 0, 10) as $tech => $count): ?>
            <div class="bar">
                <div class="bar-label"><?=htmlspecialchars($tech)?></div>
                <div class="bar-fill" style="width:<?=($count / max(array_values($projectStats['topTechs'])) * 100)?>%"></div>
                <div class="bar-value"><?=$count?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Latest Messages -->
    <?php if (!empty($stats['messages']['latest'])): ?>
    <div class="chart">
        <h2>💬 Latest Messages</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['messages']['latest'] as $msg): ?>
                <tr>
                    <td><?=htmlspecialchars($msg['name'])?></td>
                    <td><?=htmlspecialchars($msg['email'])?></td>
                    <td><?=date('M d, Y H:i', strtotime($msg['created_at']))?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <p style="margin-top:24px;text-align:center;font-size:.85rem;color:var(--tx3)">
        Last updated: <?=date('Y-m-d H:i:s')?>
    </p>
</div>

<script>
const h=document.documentElement;
const sv=localStorage.getItem('theme')||'light';
h.setAttribute('data-theme',sv);
</script>

</body>
</html>

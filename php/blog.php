<?php
/**
 * blog.php - Blog listing page
 * Displays all published blog posts with pagination
 */

require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/i18n.php';
require_once __DIR__.'/config/security.php';

// Initialize security
initializeSecureSession();

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

try {
    $db = getDB();
    
    // Get total posts
    $total = $db->query('SELECT COUNT(*) as cnt FROM blog_posts WHERE published = 1')->fetch();
    $total_posts = $total['cnt'] ?? 0;
    $total_pages = ceil($total_posts / $per_page);
    
    // Get posts for current page
    $posts = $db->prepare('
        SELECT id, title, slug, excerpt, category, created_at, featured_image 
        FROM blog_posts 
        WHERE published = 1 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ');
    $posts->execute([$per_page, $offset]);
    $posts = $posts->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $posts = [];
    $total_pages = 1;
}
?><!DOCTYPE html>
<html lang="<?=getCurrentLanguage()?>" data-theme="light">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<meta name="description" content="Blog - Elias Araya. Articles about web development, PHP, JavaScript, and software engineering."/>
<title>Blog - Elias Araya</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<style>
:root{
  --p:#2563EB;--pd:#1D4ED8;--ac:#14B8A6;--pu:#8B5CF6;
  --bg:linear-gradient(160deg,#EFF6FF 0%,#F5F3FF 40%,#ECFDF5 100%);
  --bg2:linear-gradient(135deg,#EDE9FE 0%,#DBEAFE 100%);
  --bg3:#E0E7FF;--card:#ffffff;
  --tx:#1E3A5F;--tx2:#7C3AED;--tx3:#0891B2;
  --br:#C7D2FE;--sh:0 4px 24px rgba(99,102,241,.12);--shh:0 12px 40px rgba(99,102,241,.22);
  --r:12px;--rl:20px;--tr:.3s ease;--fn:'Times New Roman',Times,serif;--nh:68px;
}
[data-theme=dark]{
  --bg:#0F172A;--bg2:#1E293B;--bg3:#334155;--card:#1E293B;
  --tx:#93C5FD;--tx2:#C4B5FD;--tx3:#67E8F9;--br:#334155;
  --sh:0 4px 24px rgba(0,0,0,.4);--shh:0 12px 40px rgba(37,99,235,.3);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;font-size:16px}
body{font-family:var(--fn);background:var(--bg);color:var(--tx);line-height:1.6;overflow-x:hidden}
a{color:inherit;text-decoration:none}
.wrap{max-width:1140px;margin:0 auto;padding:0 24px}
.nav{position:fixed;top:0;left:0;right:0;height:var(--nh);z-index:999;background:rgba(255,255,255,.85);backdrop-filter:blur(16px);border-bottom:1px solid var(--br);transition:all var(--tr)}
[data-theme=dark] .nav{background:rgba(15,23,42,.85)}
.nav-inner{max-width:1140px;margin:0 auto;padding:0 24px;height:100%;display:flex;align-items:center;justify-content:space-between}
.nav-logo{font-size:1.4rem;font-weight:900;letter-spacing:-.04em;color:var(--tx)}
.nav-logo span{color:var(--p)}
.nav-links{display:flex;gap:32px}
.nav-links a{font-size:.875rem;font-weight:500;color:var(--tx2);transition:color var(--tr)}
.nav-links a:hover{color:var(--p)}
.nav-right{display:flex;align-items:center;gap:12px}
.theme-btn{width:36px;height:36px;border-radius:50%;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:1rem;transition:all var(--tr);cursor:pointer;border:none}
.theme-btn:hover{transform:rotate(20deg)}
.section{padding:96px 0}
.section.alt{background:var(--bg2)}
.sec-head{text-align:center;margin-bottom:60px}
.sec-chip{display:inline-block;font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--p);background:rgba(37,99,235,.08);padding:5px 14px;border-radius:100px;margin-bottom:14px;border:1px solid rgba(37,99,235,.12)}
.sec-title{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:900;letter-spacing:-.03em;color:var(--tx)}
.blog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;margin-bottom:48px}
.blog-card{background:var(--card);border:1px solid var(--br);border-radius:var(--rl);overflow:hidden;transition:all var(--tr)}
.blog-card:hover{transform:translateY(-6px);box-shadow:var(--shh)}
.blog-thumb{height:180px;background:linear-gradient(135deg,var(--p),var(--ac));display:flex;align-items:center;justify-content:center;font-size:3rem;position:relative;overflow:hidden}
.blog-body{padding:24px}
.blog-meta{display:flex;align-items:center;gap:12px;margin-bottom:12px;font-size:.75rem;color:var(--tx3)}
.blog-category{background:rgba(37,99,235,.1);color:var(--p);padding:3px 10px;border-radius:100px;font-weight:700}
.blog-title{font-size:1.05rem;font-weight:800;color:var(--tx);margin-bottom:8px}
.blog-excerpt{font-size:.85rem;color:var(--tx2);line-height:1.65;margin-bottom:16px}
.blog-link{display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:700;color:var(--p);transition:all var(--tr)}
.blog-link:hover{gap:10px}
.pagination{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:48px}
.page-link{padding:8px 12px;border-radius:6px;border:1px solid var(--br);color:var(--tx2);transition:all var(--tr);cursor:pointer;font-family:var(--fn)}
.page-link:hover{background:var(--p);color:#fff;border-color:var(--p)}
.page-link.active{background:var(--p);color:#fff;border-color:var(--p)}
.footer{padding:28px 0;border-top:1px solid var(--br);margin-top:48px}
.footer-in{display:flex;align-items:center;justify-content:space-between}
.footer-copy{font-size:.82rem;color:var(--tx3)}
@media(max-width:640px){
  .nav-links{display:none}
  .section{padding:64px 0}
  .blog-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<header class="nav">
  <div class="nav-inner">
    <a href="index.html" class="nav-logo">EA<span>.</span></a>
    <ul class="nav-links">
      <li><a href="index.html#about">About</a></li>
      <li><a href="index.html#projects">Projects</a></li>
      <li><a href="blog.php">Blog</a></li>
      <li><a href="index.html#contact">Contact</a></li>
    </ul>
    <div class="nav-right">
      <button class="theme-btn" id="themeBtn" aria-label="Toggle theme">🌙</button>
    </div>
  </div>
</header>

<section class="section alt" style="padding-top:calc(96px + var(--nh))">
  <div class="wrap">
    <div class="sec-head">
      <span class="sec-chip">Latest Articles</span>
      <h1 class="sec-title">Blog</h1>
      <p style="font-size:1rem;color:var(--tx2);margin-top:10px;max-width:520px;margin-left:auto;margin-right:auto">Thoughts on web development, software engineering, and technology.</p>
    </div>

    <?php if (!empty($posts)): ?>
    <div class="blog-grid">
      <?php foreach ($posts as $post): ?>
      <article class="blog-card">
        <div class="blog-thumb">📝</div>
        <div class="blog-body">
          <div class="blog-meta">
            <span class="blog-category"><?= htmlspecialchars($post['category'] ?? 'General') ?></span>
            <span><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
          </div>
          <h2 class="blog-title"><?= htmlspecialchars($post['title']) ?></h2>
          <p class="blog-excerpt"><?= htmlspecialchars($post['excerpt'] ?? substr($post['title'], 0, 100)) ?></p>
          <a href="blog-post.php?slug=<?= urlencode($post['slug']) ?>" class="blog-link">Read More →</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=1" class="page-link">« First</a>
        <a href="?page=<?= $page - 1 ?>" class="page-link">‹ Prev</a>
      <?php endif; ?>
      
      <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
        <a href="?page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
      
      <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>" class="page-link">Next ›</a>
        <a href="?page=<?= $total_pages ?>" class="page-link">Last »</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:60px 24px">
      <p style="font-size:1.1rem;color:var(--tx2)">No blog posts yet. Check back soon!</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<footer class="footer">
  <div class="wrap">
    <div class="footer-in">
      <p class="footer-copy">© 2026 Elias Araya. Built with HTML, CSS, JS & PHP.</p>
      <a href="index.html" style="color:var(--p);font-weight:700">← Back to Portfolio</a>
    </div>
  </div>
</footer>

<script>
const html=document.documentElement,tb=document.getElementById('themeBtn');
const th=localStorage.getItem('theme')||'light';
html.setAttribute('data-theme',th);tb.textContent=th==='dark'?'☀️':'🌙';
tb.addEventListener('click',()=>{const n=html.getAttribute('data-theme')==='dark'?'light':'dark';html.setAttribute('data-theme',n);localStorage.setItem('theme',n);tb.textContent=n==='dark'?'☀️':'🌙'});
</script>
</body>
</html>

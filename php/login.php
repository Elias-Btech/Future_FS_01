<?php
// Load configuration BEFORE session operations
require_once __DIR__.'/config/i18n.php';
require_once __DIR__.'/config/security.php';

// Start session safely (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if(isset($_SESSION['user_id'])){
    header('Location: dashboard.php');
    exit;
}

$err=$_SESSION['auth_error']??'';
$suc=$_SESSION['auth_success']??'';
unset($_SESSION['auth_error'],$_SESSION['auth_success']);

// Generate CSRF token
$csrf=bin2hex(random_bytes(32));
$_SESSION['csrf']=$csrf;
?><!DOCTYPE html>
<html lang="<?=getCurrentLanguage()?>" data-theme="light">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?=t('auth_login')?> – Elias Araya</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
:root{--p:#2563EB;--pd:#1D4ED8;--ac:#14B8A6;--bg:#F8FAFC;--card:#fff;--tx:#0F172A;--tx2:#475569;--tx3:#94A3B8;--br:#E2E8F0;--err:#EF4444;--ok:#10B981;--sh:0 8px 40px rgba(0,0,0,.1);--r:14px;--tr:.25s ease;--fn:'Inter',sans-serif}
[data-theme=dark]{--bg:#0F172A;--card:#1E293B;--tx:#F1F5F9;--tx2:#CBD5E1;--tx3:#64748B;--br:#334155;--sh:0 8px 40px rgba(0,0,0,.4)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:16px}
body{font-family:var(--fn);background:var(--bg);color:var(--tx);min-height:100vh;display:flex;flex-direction:column;transition:background var(--tr),color var(--tr)}
a{color:var(--p);text-decoration:none}a:hover{text-decoration:underline}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 28px;background:var(--card);border-bottom:1px solid var(--br)}
.logo{font-size:1.3rem;font-weight:900;color:var(--tx);letter-spacing:-.04em;text-decoration:none}.logo span{color:var(--p)}
.tbtn{width:34px;height:34px;border-radius:50%;border:none;background:var(--bg);cursor:pointer;font-size:.95rem;display:flex;align-items:center;justify-content:center;transition:transform var(--tr)}.tbtn:hover{transform:rotate(20deg)}
.main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
.card{background:var(--card);border:1px solid var(--br);border-radius:var(--r);box-shadow:var(--sh);width:100%;max-width:420px;padding:40px 36px}
.card-icon{width:54px;height:54px;border-radius:14px;background:linear-gradient(135deg,var(--p),var(--ac));display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 16px}
.card-title{font-size:1.5rem;font-weight:900;text-align:center;letter-spacing:-.03em;margin-bottom:6px}
.card-sub{font-size:.85rem;color:var(--tx2);text-align:center;margin-bottom:28px}
.alert{padding:11px 14px;border-radius:8px;font-size:.83rem;font-weight:600;display:flex;align-items:center;gap:8px;margin-bottom:18px}
.alert-err{background:rgba(239,68,68,.1);color:#DC2626;border:1px solid rgba(239,68,68,.2)}
.alert-ok{background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2)}
.form{display:flex;flex-direction:column;gap:16px}
.fg{display:flex;flex-direction:column;gap:5px}
.fg label{font-size:.78rem;font-weight:700;color:var(--tx)}
.iw{position:relative;display:flex;align-items:center}
.ii{position:absolute;left:13px;font-size:.95rem;pointer-events:none;color:var(--tx3);line-height:1}
.fg input{width:100%;padding:11px 13px 11px 38px;border:1.5px solid var(--br);border-radius:8px;background:var(--bg);color:var(--tx);font-family:var(--fn);font-size:.88rem;outline:none;transition:all var(--tr)}
.fg input:focus{border-color:var(--p);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.fg input.e{border-color:var(--err)}
.tpw{position:absolute;right:11px;background:none;border:none;cursor:pointer;font-size:.9rem;color:var(--tx3);padding:3px;transition:color var(--tr)}.tpw:hover{color:var(--p)}
.fm{font-size:.73rem;color:var(--err);min-height:13px}
.sbtn{width:100%;padding:13px;border:none;border-radius:8px;background:var(--p);color:#fff;font-family:var(--fn);font-size:.92rem;font-weight:800;cursor:pointer;margin-top:4px;transition:all var(--tr);box-shadow:0 4px 14px rgba(37,99,235,.3)}
.sbtn:hover{background:var(--pd);transform:translateY(-2px);box-shadow:0 6px 20px rgba(37,99,235,.4)}
.sbtn:disabled{opacity:.6;cursor:not-allowed;transform:none}
.divider{text-align:center;font-size:.8rem;color:var(--tx3);margin-top:18px}
.divider a{font-weight:700;color:var(--p)}
.footer{text-align:center;padding:18px;font-size:.76rem;color:var(--tx3);border-top:1px solid var(--br)}
@media(max-width:460px){.card{padding:28px 18px}.topbar{padding:12px 18px}}
</style>
</head>
<body>
<header class="topbar">
  <a href="index.html" class="logo">EA<span>.</span></a>
  <button class="tbtn" id="tbtn" aria-label="Toggle theme">🌙</button>
</header>
<main class="main">
  <div class="card">
    <div class="card-icon">🔐</div>
    <h1 class="card-title"><?=t('auth_login')?></h1>
    <p class="card-sub"><?=t('auth_login')?></p>
    <?php if($err):?><div class="alert alert-err">⚠️ <?=htmlspecialchars($err)?></div><?php endif;?>
    <?php if($suc):?><div class="alert alert-ok">✅ <?=htmlspecialchars($suc)?></div><?php endif;?>
    <form class="form" id="lf" action="login_process.php" method="POST" novalidate>
      <input type="hidden" name="csrf" value="<?=$csrf?>"/>
      <div class="fg">
        <label for="em"><?=t('auth_email')?></label>
        <div class="iw"><span class="ii">📧</span><input type="email" id="em" name="email" placeholder="you@example.com" autocomplete="email" required/></div>
        <div class="fm" id="emE"></div>
      </div>
      <div class="fg">
        <label for="pw"><?=t('auth_password')?></label>
        <div class="iw"><span class="ii">🔒</span><input type="password" id="pw" name="password" placeholder="<?=t('auth_password')?>" autocomplete="current-password" required/><button type="button" class="tpw" id="tpw">👁️</button></div>
        <div class="fm" id="pwE"></div>
      </div>
      <button type="submit" class="sbtn" id="sb"><?=t('auth_login')?></button>
    </form>
    <p class="divider"><?=t('auth_dont_have_account')?> <a href="register.php"><?=t('auth_register')?></a></p>
    <p class="divider" style="margin-top:8px"><a href="index.php">← <?=t('nav_home')?></a></p>
  </div>
</main>
<footer class="footer">© 2026 Elias Araya. <?=t('footer_copyright')?></footer>
<script>
const h=document.documentElement,t=document.getElementById('tbtn');
const sv=localStorage.getItem('theme')||'light';h.setAttribute('data-theme',sv);t.textContent=sv==='dark'?'☀️':'🌙';
t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);t.textContent=n==='dark'?'☀️':'🌙'});
const pw=document.getElementById('pw'),tp=document.getElementById('tpw');
if(tp)tp.addEventListener('click',()=>{pw.type=pw.type==='text'?'password':'text';tp.textContent=pw.type==='text'?'🙈':'👁️'});
const ER=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
document.getElementById('lf').addEventListener('submit',e=>{
  let ok=true;
  const em=document.getElementById('em'),emE=document.getElementById('emE');
  const pw2=document.getElementById('pw'),pwE=document.getElementById('pwE');
  em.classList.remove('e');emE.textContent='';pw2.classList.remove('e');pwE.textContent='';
  if(!em.value.trim()||!ER.test(em.value.trim())){em.classList.add('e');emE.textContent='Enter a valid email.';ok=false}
  if(!pw2.value){pw2.classList.add('e');pwE.textContent='Password is required.';ok=false}
  if(!ok)e.preventDefault();
});
</script>
</body>
</html>

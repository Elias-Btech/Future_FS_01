<?php
/**
 * dashboard.php - ENHANCED WITH SECURITY
 * Phase 1 Implementation: Session Timeout + Security Headers
 */

// Load configuration files BEFORE session operations
require_once __DIR__.'/config/security.php';
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/i18n.php';

// Initialize security (includes session_start if not already started)
initializeSecurity();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Enforce session timeout (30 minutes)
if (!enforceSessionTimeout(1800)) {
    header('Location: login.php?expired=1');
    exit;
}

// Get user data
$uname = htmlspecialchars($_SESSION['user_name']);
$uemail = htmlspecialchars($_SESSION['user_email']);
$msgs = [];
$projs = [];

try {
    $db = getDB();
    $msgs = $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 30')->fetchAll();
    $projs = $db->query('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?><!DOCTYPE html>
<html lang="<?=getCurrentLanguage()?>" data-theme="light">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?=t('dashboard_title')?> – Elias Araya</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
:root{--p:#2563EB;--pd:#1D4ED8;--ac:#14B8A6;--bg:#F8FAFC;--card:#fff;--tx:#0F172A;--tx2:#475569;--tx3:#94A3B8;--br:#E2E8F0;--err:#EF4444;--ok:#10B981;--sh:0 2px 12px rgba(0,0,0,.06);--fn:'Inter',sans-serif;--tr:.25s ease}
[data-theme=dark]{--bg:#0F172A;--card:#1E293B;--tx:#F1F5F9;--tx2:#CBD5E1;--tx3:#64748B;--br:#334155;--sh:0 2px 12px rgba(0,0,0,.3)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:16px}
body{font-family:var(--fn);background:var(--bg);color:var(--tx);transition:background var(--tr),color var(--tr)}
a{color:var(--p);text-decoration:none}
/* TOPBAR */
.topbar{position:fixed;top:0;left:0;right:0;height:62px;background:var(--card);border-bottom:1px solid var(--br);display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:200;box-shadow:var(--sh)}
.logo{font-size:1.25rem;font-weight:900;color:var(--tx);letter-spacing:-.04em}.logo span{color:var(--p)}
.tb-r{display:flex;align-items:center;gap:12px}
.av{width:32px;height:32px;border-radius:50%;overflow:hidden;border:2px solid var(--br)}
.uname{font-size:.85rem;font-weight:600;color:var(--tx)}
.tbtn{width:32px;height:32px;border-radius:50%;border:none;background:var(--bg);cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;transition:transform var(--tr)}.tbtn:hover{transform:rotate(20deg)}
.lo-btn{padding:6px 14px;border-radius:7px;background:rgba(239,68,68,.1);color:#DC2626;font-size:.78rem;font-weight:700;transition:background var(--tr)}.lo-btn:hover{background:rgba(239,68,68,.2)}
/* LAYOUT */
.layout{display:flex;min-height:100vh;padding-top:62px}
.sidebar{width:215px;flex-shrink:0;background:var(--card);border-right:1px solid var(--br);padding:20px 10px;position:fixed;top:62px;bottom:0;left:0;overflow-y:auto}
.snav{display:flex;flex-direction:column;gap:3px}
.sni{display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:8px;font-size:.85rem;font-weight:600;color:var(--tx2);cursor:pointer;transition:all var(--tr);text-decoration:none;border:none;background:none;font-family:var(--fn);width:100%;text-align:left}
.sni:hover,.sni.on{background:rgba(37,99,235,.08);color:var(--p)}
.sni.lo:hover{background:rgba(239,68,68,.08);color:#DC2626}
.main{flex:1;margin-left:215px;padding:32px 36px}
/* TABS */
.tab{display:none}.tab.on{display:block}
/* WELCOME */
.welcome{margin-bottom:24px}.welcome h1{font-size:1.5rem;font-weight:900;letter-spacing:-.03em;margin-bottom:4px}.welcome p{font-size:.88rem;color:var(--tx2)}
/* STATS */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:24px}
.sc{background:var(--card);border:1px solid var(--br);border-radius:12px;padding:20px;display:flex;align-items:center;gap:12px;transition:all var(--tr)}.sc:hover{transform:translateY(-3px);box-shadow:0 6px 24px rgba(37,99,235,.1)}
.sc-ic{font-size:1.7rem}.sc-n{font-size:1.6rem;font-weight:900;color:var(--p);letter-spacing:-.04em;line-height:1}.sc-l{font-size:.7rem;color:var(--tx3);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-top:3px}
/* PROFILE CARD */
.pcard{background:var(--card);border:1px solid var(--br);border-radius:12px;padding:22px;margin-bottom:20px}
.pcard h2{font-size:.92rem;font-weight:800;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--br)}
.pr{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--br);font-size:.84rem}.pr:last-child{border-bottom:none}.pr span{color:var(--tx2)}.pr strong{color:var(--tx)}
/* SECTION TITLE */
.stitle{font-size:1.1rem;font-weight:800;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between}
/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;border:none;font-family:var(--fn);transition:all var(--tr)}
.btn-p{background:var(--p);color:#fff;box-shadow:0 3px 10px rgba(37,99,235,.3)}.btn-p:hover{background:var(--pd);transform:translateY(-1px)}
.btn-g{background:var(--bg);border:1.5px solid var(--br);color:var(--tx2)}.btn-g:hover{border-color:var(--p);color:var(--p)}
.btn-r{background:rgba(239,68,68,.1);color:#DC2626;border:1px solid rgba(239,68,68,.2)}.btn-r:hover{background:rgba(239,68,68,.2)}
.btn-sm{padding:6px 12px;font-size:.75rem}
/* PROJECT TABLE */
.ptable{width:100%;border-collapse:collapse;background:var(--card);border-radius:12px;overflow:hidden;border:1px solid var(--br)}
.ptable th{background:var(--bg);font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--tx3);padding:12px 16px;text-align:left;border-bottom:1px solid var(--br)}
.ptable td{padding:14px 16px;border-bottom:1px solid var(--br);font-size:.84rem;color:var(--tx2);vertical-align:middle}
.ptable tr:last-child td{border-bottom:none}
.ptable tr:hover td{background:rgba(37,99,235,.03)}
.ptable .ptitle{font-weight:700;color:var(--tx)}
.ptable .pcat{display:inline-block;padding:2px 10px;border-radius:100px;font-size:.7rem;font-weight:700;background:rgba(37,99,235,.08);color:var(--p)}
.ptable .pemoji{font-size:1.4rem}
.td-actions{display:flex;gap:6px}
/* MODAL */
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:500;align-items:center;justify-content:center;padding:20px}
.overlay.open{display:flex}
.modal{background:var(--card);border-radius:16px;padding:32px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.2);position:relative}
.modal h2{font-size:1.15rem;font-weight:900;margin-bottom:22px;letter-spacing:-.02em}
.modal .close{position:absolute;top:16px;right:16px;width:30px;height:30px;border-radius:50%;background:var(--bg);border:none;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;color:var(--tx2);transition:all var(--tr)}.modal .close:hover{background:var(--br)}
/* FORM */
.fg{margin-bottom:14px}
.fg label{display:block;font-size:.78rem;font-weight:700;color:var(--tx);margin-bottom:5px}
.fg input,.fg textarea,.fg select{width:100%;padding:10px 13px;border:1.5px solid var(--br);border-radius:8px;background:var(--bg);color:var(--tx);font-family:var(--fn);font-size:.86rem;outline:none;transition:all var(--tr)}
.fg input:focus,.fg textarea:focus,.fg select:focus{border-color:var(--p);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.fg textarea{resize:vertical;min-height:80px}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-fb{margin-top:12px;padding:10px 13px;border-radius:8px;font-size:.82rem;font-weight:600;display:none}
.form-fb.ok{display:block;background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2)}
.form-fb.fail{display:block;background:rgba(239,68,68,.1);color:#DC2626;border:1px solid rgba(239,68,68,.2)}
/* MESSAGES */
.mlist{display:flex;flex-direction:column;gap:12px}
.mc{background:var(--card);border:1px solid var(--br);border-radius:12px;padding:16px 20px;transition:box-shadow var(--tr)}.mc:hover{box-shadow:0 4px 20px rgba(0,0,0,.07)}
.mc-head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.mc-av{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--p),var(--ac));color:#fff;font-size:.78rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mc-info{flex:1}.mc-info strong{font-size:.86rem;color:var(--tx);display:block}.mc-info a{font-size:.74rem;color:var(--p)}
.mc-date{font-size:.7rem;color:var(--tx3);white-space:nowrap}
.mc-body{font-size:.82rem;color:var(--tx2);line-height:1.65;padding-left:42px}
.empty{text-align:center;padding:48px 20px;color:var(--tx3)}.empty-ic{font-size:2.5rem;margin-bottom:8px}
/* ALERT */
.alert{padding:10px 14px;border-radius:8px;font-size:.82rem;font-weight:600;margin-bottom:16px;display:none}
.alert.ok{display:flex;background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2)}
.alert.fail{display:flex;background:rgba(239,68,68,.1);color:#DC2626;border:1px solid rgba(239,68,68,.2)}
@media(max-width:720px){.sidebar{display:none}.main{margin-left:0;padding:20px 16px}.uname{display:none}.fg-row{grid-template-columns:1fr}}
</style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <a href="index.php" class="logo">EA<span>.</span></a>
  <div class="tb-r">
    <button class="tbtn" id="tbtn">🌙</button>
    <div class="av"><img src="assets/elias.jpg" alt="Elias Araya" style="width:100%;height:100%;object-fit:cover;object-position:center top;border-radius:50%"/></div>
    <span class="uname"><?=$uname?></span>
    <a href="logout.php" class="lo-btn">Sign out</a>
  </div>
</header>

<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <nav class="snav">
      <button class="sni on" data-tab="overview">📊 <?=t('dashboard_title')?></button>
      <button class="sni" data-tab="projects">🗂️ <?=t('dashboard_projects')?></button>
      <button class="sni" data-tab="messages">💬 <?=t('dashboard_messages')?></button>
      <a class="sni" href="index.php">🌐 <?=t('nav_home')?></a>
      <a class="sni lo" href="logout.php">🚪 <?=t('nav_logout')?></a>
    </nav>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- OVERVIEW TAB -->
    <div class="tab on" id="tab-overview">
      <div class="welcome"><h1><?=sprintf(t('dashboard_welcome'), $uname)?> 👋</h1><p><?=t('dashboard_title')?></p></div>
      <div class="stats">
        <div class="sc"><div class="sc-ic">🗂️</div><div><div class="sc-n"><?=count($projs)?></div><div class="sc-l"><?=t('dashboard_projects')?></div></div></div>
        <div class="sc"><div class="sc-ic">💬</div><div><div class="sc-n"><?=count($msgs)?></div><div class="sc-l"><?=t('dashboard_messages')?></div></div></div>
        <div class="sc"><div class="sc-ic">👤</div><div><div class="sc-n">1</div><div class="sc-l">Account</div></div></div>
      </div>
      <div class="pcard">
        <h2><?=t('about_role')?></h2>
        <div class="pr"><span>Name</span><strong><?=$uname?></strong></div>
        <div class="pr"><span>Email</span><strong><?=$uemail?></strong></div>
        <div class="pr"><span>Role</span><strong>Portfolio Owner</strong></div>
      </div>
    </div>

    <!-- PROJECTS TAB -->
    <div class="tab" id="tab-projects">
      <div class="stitle">
        <span>🗂️ <?=t('dashboard_projects')?></span>
        <button class="btn btn-p" id="addProjBtn">+ <?=t('dashboard_add_project')?></button>
      </div>
      <div class="alert" id="projAlert"></div>

      <?php if(empty($projs)): ?>
        <div class="empty"><div class="empty-ic">📂</div><p><?=t('projects_empty')?></p></div>
      <?php else: ?>
      <table class="ptable" id="projTable">
        <thead><tr><th>Icon</th><th><?=t('dashboard_title_field')?></th><th><?=t('dashboard_category')?></th><th><?=t('dashboard_tech_stack')?></th><th>Links</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($projs as $p): ?>
          <tr id="row-<?=(int)$p['id']?>">
            <td class="pemoji"><?=htmlspecialchars($p['emoji'])?></td>
            <td class="ptitle"><?=htmlspecialchars($p['title'])?></td>
            <td><span class="pcat"><?=htmlspecialchars($p['category'])?></span></td>
            <td><?=htmlspecialchars(substr($p['tech'],0,40)).(strlen($p['tech'])>40?'…':'')?></td>
            <td>
              <?php if($p['github_url']):?><a href="<?=htmlspecialchars($p['github_url'])?>" target="_blank" style="margin-right:6px;font-size:.75rem">GitHub</a><?php endif;?>
              <?php if($p['live_url']):?><a href="<?=htmlspecialchars($p['live_url'])?>" target="_blank" style="font-size:.75rem">Live</a><?php endif;?>
            </td>
            <td>
              <div class="td-actions">
                <button class="btn btn-g btn-sm edit-btn"
                  data-id="<?=(int)$p['id']?>"
                  data-title="<?=htmlspecialchars($p['title'],ENT_QUOTES)?>"
                  data-desc="<?=htmlspecialchars($p['description'],ENT_QUOTES)?>"
                  data-tech="<?=htmlspecialchars($p['tech'],ENT_QUOTES)?>"
                  data-cat="<?=htmlspecialchars($p['category'],ENT_QUOTES)?>"
                  data-emoji="<?=htmlspecialchars($p['emoji'],ENT_QUOTES)?>"
                  data-gh="<?=htmlspecialchars($p['github_url']??'',ENT_QUOTES)?>"
                  data-live="<?=htmlspecialchars($p['live_url']??'',ENT_QUOTES)?>"
                  data-sort="<?=(int)$p['sort_order']?>">✏️ Edit</button>
                <button class="btn btn-r btn-sm del-btn" data-id="<?=(int)$p['id']?>" data-title="<?=htmlspecialchars($p['title'],ENT_QUOTES)?>">🗑️</button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

    <!-- MESSAGES TAB -->
    <div class="tab" id="tab-messages">
      <div class="stitle"><span>💬 <?=t('dashboard_messages')?></span></div>
      <?php if(empty($msgs)): ?>
        <div class="empty"><div class="empty-ic">📭</div><p><?=t('dashboard_no_messages')?></p></div>
      <?php else: ?>
        <div class="mlist">
          <?php foreach($msgs as $m): ?>
          <div class="mc">
            <div class="mc-head">
              <div class="mc-av"><?=strtoupper(substr($m['name'],0,1))?></div>
              <div class="mc-info"><strong><?=htmlspecialchars($m['name'])?></strong><a href="mailto:<?=htmlspecialchars($m['email'])?>"><?=htmlspecialchars($m['email'])?></a></div>
              <span class="mc-date"><?=date('M j, Y',strtotime($m['created_at']))?></span>
            </div>
            <p class="mc-body"><?=nl2br(htmlspecialchars($m['message']))?></p>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<!-- ADD / EDIT MODAL -->
<div class="overlay" id="projOverlay">
  <div class="modal">
    <button class="close" id="modalClose">✕</button>
    <h2 id="modalTitle"><?=t('dashboard_add_project')?></h2>
    <form id="projForm">
      <input type="hidden" id="pId" name="id" value=""/>
      <div class="fg"><label><?=t('dashboard_title_field')?> *</label><input type="text" id="pTitle" name="title" placeholder="e.g. ShopFlow E-Commerce" required/></div>
      <div class="fg"><label><?=t('dashboard_description')?> *</label><textarea id="pDesc" name="description" placeholder="What problem does it solve? What did you build?" required></textarea></div>
      <div class="fg"><label><?=t('dashboard_tech_stack')?> * <small style="font-weight:400;color:var(--tx3)">(comma separated)</small></label><input type="text" id="pTech" name="tech" placeholder="PHP, MySQL, JavaScript, CSS3" required/></div>
      <div class="fg-row">
        <div class="fg"><label><?=t('dashboard_category')?> *</label>
          <select id="pCat" name="category">
            <option value="fullstack">Full Stack</option>
            <option value="frontend">Frontend</option>
            <option value="backend">Backend</option>
          </select>
        </div>
        <div class="fg"><label>Emoji Icon</label><input type="text" id="pEmoji" name="emoji" placeholder="🚀" maxlength="4"/></div>
      </div>
      <div class="fg"><label><?=t('dashboard_github_url')?></label><input type="url" id="pGh" name="github_url" placeholder="https://github.com/you/project"/></div>
      <div class="fg"><label><?=t('dashboard_live_url')?></label><input type="url" id="pLive" name="live_url" placeholder="https://yourproject.com"/></div>
      <div class="fg"><label>Sort Order <small style="font-weight:400;color:var(--tx3)">(lower = first)</small></label><input type="number" id="pSort" name="sort_order" value="0" min="0"/></div>
      <div style="display:flex;gap:10px;margin-top:4px">
        <button type="submit" class="btn btn-p" id="projSaveBtn"><?=t('dashboard_save')?></button>
        <button type="button" class="btn btn-g" id="modalCancelBtn"><?=t('dashboard_cancel')?></button>
      </div>
      <div class="form-fb" id="projFormFb"></div>
    </form>
  </div>
</div>

<script>
/* ── THEME ── */
const H=document.documentElement,TB=document.getElementById('tbtn');
const ts=localStorage.getItem('theme')||'light';H.setAttribute('data-theme',ts);TB.textContent=ts==='dark'?'☀️':'🌙';
TB.addEventListener('click',()=>{const n=H.getAttribute('data-theme')==='dark'?'light':'dark';H.setAttribute('data-theme',n);localStorage.setItem('theme',n);TB.textContent=n==='dark'?'☀️':'🌙'});

/* ── SIDEBAR TABS ── */
document.querySelectorAll('.sni[data-tab]').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.sni[data-tab]').forEach(x=>x.classList.remove('on'));
  document.querySelectorAll('.tab').forEach(x=>x.classList.remove('on'));
  b.classList.add('on');
  document.getElementById('tab-'+b.dataset.tab)?.classList.add('on');
}));

/* ── MODAL HELPERS ── */
const overlay=document.getElementById('projOverlay');
const modalTitle=document.getElementById('modalTitle');
const projForm=document.getElementById('projForm');
const projFb=document.getElementById('projFormFb');

function openModal(title){modalTitle.textContent=title;overlay.classList.add('open');projFb.className='form-fb'}
function closeModal(){overlay.classList.remove('open');projForm.reset();projFb.className='form-fb'}

document.getElementById('modalClose').addEventListener('click',closeModal);
document.getElementById('modalCancelBtn').addEventListener('click',closeModal);
overlay.addEventListener('click',e=>{if(e.target===overlay)closeModal()});

/* ── OPEN ADD FORM ── */
document.getElementById('addProjBtn').addEventListener('click',()=>{
  projForm.reset();
  document.getElementById('pId').value='';
  document.getElementById('pEmoji').value='🚀';
  document.getElementById('pSort').value='0';
  openModal('<?=t('dashboard_add_project')?>');
});

/* ── OPEN EDIT FORM ── */
document.addEventListener('click',e=>{
  const btn=e.target.closest('.edit-btn');
  if(!btn)return;
  const d=btn.dataset;
  document.getElementById('pId').value=d.id;
  document.getElementById('pTitle').value=d.title;
  document.getElementById('pDesc').value=d.desc;
  document.getElementById('pTech').value=d.tech;
  document.getElementById('pCat').value=d.cat;
  document.getElementById('pEmoji').value=d.emoji;
  document.getElementById('pGh').value=d.gh;
  document.getElementById('pLive').value=d.live;
  document.getElementById('pSort').value=d.sort;
  openModal('<?=t('dashboard_edit_project')?>');
});

/* ── SAVE (ADD or EDIT) ── */
projForm.addEventListener('submit',async e=>{
  e.preventDefault();
  const id=document.getElementById('pId').value;
  const action=id?'edit':'add';
  const saveBtn=document.getElementById('projSaveBtn');
  saveBtn.disabled=true;saveBtn.textContent='Saving...';
  projFb.className='form-fb';

  try{
    const fd=new FormData(projForm);
    fd.set('action',action);
    const r=await fetch('php/projects.php',{method:'POST',body:fd});
    const j=await r.json();
    projFb.textContent=j.message;
    projFb.className='form-fb '+(j.success?'ok':'fail');
    if(j.success){
      showAlert(j.message,'ok');
      setTimeout(()=>{closeModal();location.reload()},800);
    }
  }catch{projFb.textContent='Network error.';projFb.className='form-fb fail'}
  finally{saveBtn.disabled=false;saveBtn.textContent='Save Project'}
});

/* ── DELETE ── */
document.addEventListener('click',async e=>{
  const btn=e.target.closest('.del-btn');
  if(!btn)return;
  if(!confirm(`Delete "${btn.dataset.title}"? This cannot be undone.`))return;
  btn.disabled=true;
  try{
    const fd=new FormData();fd.append('action','delete');fd.append('id',btn.dataset.id);
    const r=await fetch('php/projects.php',{method:'POST',body:fd});
    const j=await r.json();
    if(j.success){
      document.getElementById('row-'+btn.dataset.id)?.remove();
      showAlert(j.message,'ok');
    }else{showAlert(j.message,'fail')}
  }catch{showAlert('Network error.','fail')}
  finally{btn.disabled=false}
});

/* ── ALERT BANNER ── */
function showAlert(msg,type){
  const a=document.getElementById('projAlert');
  a.textContent=msg;a.className='alert '+type;
  setTimeout(()=>a.className='alert',3500);
}
</script>
</body>
</html>

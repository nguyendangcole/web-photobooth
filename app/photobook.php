
<?php
// 1) Require login BEFORE rendering HTML
$GUARD_PAGE = 'photobook';                            // ← current page name
require __DIR__ . '/includes/auth_guard.php';        // create this file at app/includes/auth_guard.php

// 2) Block cache for private content (HTML page)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// 3) Load config
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/S.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Photobook</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">


<style>
/* Compact header - Light theme - compact but complete - Override menu.php */
.main-nav {
  padding: 6px 0 !important;
  background: #ffffff !important;
  border-bottom: 1px solid #e0e0e0 !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.nav-wrapper {
  padding: 0 15px !important;
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1400px;
  margin: 0 auto;
  position: relative;
}
.logo {
  font-size: 13px !important;
  display: flex;
  align-items: center;
  gap: 4px;
}
.logo-icon {
  font-size: 16px !important;
  color: #c1ff72 !important;
}
.logo-text {
  color: #0a0a0a !important;
  font-weight: 700;
}
.logo-badge {
  font-size: 8px !important;
  padding: 1px 4px !important;
  background: #c1ff72 !important;
  color: #0a0a0a !important;
  border-radius: 2px;
  font-weight: 700;
}
.nav-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}
.nav-link {
  font-size: 11px !important;
  font-weight: 700 !important;
  color: #333333 !important;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s;
  text-transform: uppercase;
}
.nav-link:hover {
  color: #c1ff72 !important;
  background: rgba(193, 255, 114, 0.15);
}
.nav-link.active {
  border-bottom: 2px solid #c1ff72 !important;
  padding-bottom: 2px;
}
.nav-user {
  display: flex;
  align-items: center;
  margin-left: 15px;
}
.nav-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
  border: 1.5px solid #c1ff72;
}
.nav-avatar-fallback,
.nav-avatar-guest {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #c1ff72;
  color: #0a0a0a;
  font-weight: 700;
  font-size: 12px;
  border: 1.5px solid #c1ff72;
  text-decoration: none;
}
.nav-avatar-guest {
  background: #999;
  color: #ffffff;
  border-color: #999;
}
.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 3px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
}
.menu-toggle span {
  width: 20px;
  height: 2px;
  background: #333333 !important;
  transition: all 0.3s ease;
}
.menu-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(4px, 4px);
}
.menu-toggle.active span:nth-child(2) {
  opacity: 0;
}
@media (max-width: 768px) {
  .menu-toggle {
    display: flex;
  }
  .nav-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #ffffff !important;
    border-top: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    flex-direction: column;
    align-items: flex-start;
    padding: 12px 15px;
    gap: 0.5rem;
    display: none;
  }
  .nav-menu.active {
    display: flex;
  }
  .nav-user {
    margin-left: 0;
    margin-top: 8px;
  }
}

/* Compact footer - Light theme - compact but complete - Override footer.php */
.footer {
  background: #ffffff !important;
  color: #333333 !important;
  padding: 6px 15px !important;
  border-top: 1px solid #e0e0e0 !important;
  margin-top: auto !important;
  box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05) !important;
}
.footer-content {
  max-width: 1400px !important;
  margin: 0 auto !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  flex-wrap: wrap !important;
  gap: 12px !important;
}
.footer-links {
  display: flex !important;
  align-items: center !important;
  gap: 1rem !important;
  flex-wrap: wrap !important;
}
.footer-links a {
  color: #666666 !important;
  text-decoration: none !important;
  font-size: 9px !important;
  font-weight: 700 !important;
  opacity: 0.8 !important;
  transition: opacity 0.2s, color 0.2s !important;
  text-transform: uppercase;
}
.footer-links a:hover {
  opacity: 1 !important;
  color: #c1ff72 !important;
}
.footer-copyright {
  color: #999999 !important;
  font-size: 8px !important;
  font-weight: 700 !important;
  margin: 0 !important;
}
.footer-copyright strong {
  color: #333333 !important;
  font-weight: 700 !important;
}
@media (max-width: 768px) {
  .footer-content {
    flex-direction: column !important;
    text-align: center !important;
    gap: 6px !important;
  }
  .footer-links {
    justify-content: center !important;
    gap: 0.75rem !important;
  }
  .footer {
    padding: 8px 15px !important;
  }
}

body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ================= Flipbook core ================= */
:root{
  /* SIZE AND COVER (change here) */
  --book-w: 900px;       /* max book width */
  --book-h: 520px;       /* book height */
  --page-pad: 12px;      /* empty border inside each page */
  --img-fit: contain;    /* 'contain' or 'cover' */
  --cover-left:  url('images/Cover1.png');   /* or 'none' */
  --cover-right: url('images/Cover1.png');   /* or 'none' */
}

.pb-stage{
  perspective: 1800px;
  -webkit-perspective: 1800px;
  width: min(920px, 100%);
  margin: 0 auto;
}
.book{
  position: relative;
  width: 100%;
  max-width: var(--book-w);
  margin: 0 auto;
  height: clamp(320px, 60vw, var(--book-h));
  background: #efefef;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,.12);
  overflow: visible;
}
.sheet{
  position: absolute;
  top: 0; left: 50%;
  width: 50%;
  height: 100%;
  transform-style: preserve-3d;
  transform-origin: left center;
  transition: transform 800ms cubic-bezier(.2,.8,.2,1);
  z-index: 5;
}
.sheet .page{
  position: absolute;
  inset: 0;
  padding: var(--page-pad);
  border-left: 1px solid rgba(0,0,0,.06);
  background: white;
  overflow: hidden;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  display: flex; align-items: center; justify-content: center;
}
.sheet .front{ transform: rotateY(0deg) translateZ(1px); }
.sheet .back { transform: rotateY(180deg) translateZ(1px); }
.sheet.flipped{ transform: rotateY(-180deg); }

.page img{
  max-width: 100%;
  max-height: 100%;
  object-fit: var(--img-fit);
  display: block;
}

/* Covers: set image via CSS var in :root */
.book .left-cover,
.book .right-cover{
  position:absolute; top:0; width:50%; height:100%;
  background-size: cover;
  background-position: center;
  pointer-events:none;
}
.book .left-cover{
  left:0; border-right:1px solid rgba(0,0,0,.06);
  background-image: var(--cover-left);
}
.book .right-cover{
  right:0; border-left:1px solid rgba(0,0,0,.06);
  background-image: var(--cover-right);
}

.pb-meta{ font-size:.9rem; color:#6c757d; }

/* ================= Side arrows ================= */
.pb-nav-btn{
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 2000;
  width: 42px; height: 42px;
  border-radius: 50%;
  border: 0;
  display: inline-flex;
  align-items: center; justify-content: center;
  background: rgba(0,0,0,.55);
  color: #fff;
  cursor: pointer;
  transition: background .15s ease, transform .1s ease;
  outline: none;
}
.pb-nav-btn:hover{ background: rgba(0,0,0,.7); }
.pb-nav-btn:active{ transform: translateY(-50%) scale(.98); }
.pb-nav-left{ left: -20px; }
.pb-nav-right{ right: -20px; }

@media (max-width: 576px){
  .pb-nav-left{ left: -10px; }
  .pb-nav-right{ right: -10px; }
  .pb-nav-btn{ width: 38px; height: 38px; }
}

.pb-nav-left[disabled], .pb-nav-right[disabled]{ opacity:.35; cursor: default; }

/* ===== Per-right-page tiny actions ===== */
.page-actions{
  position: absolute;
  right: 10px; bottom: 10px;
  display: flex; gap: 6px;
}
.page-actions .mini-btn{
  border: 0;
  padding: 6px 10px;
  border-radius: 10px;
  font-size: 12px;
  line-height: 1;
  color: #fff;
  background: rgba(0,0,0,.55);
  transition: background .15s ease;
  cursor: pointer;
}
.page-actions .mini-btn:hover{ background: rgba(0,0,0,.75); }
.page-actions .mini-btn.danger{ background: #dc3545; }
.page-actions .mini-btn.danger:hover{ background: #bb2d3b; }
</style>
</head>
<body>

<!-- Navigation -->
<nav class="main-nav">
  <div class="nav-wrapper">
    <div class="logo">
      <span class="logo-icon">✦</span>
      <span class="logo-text">SPACE PHOTOBOOTH</span>
      <span class="logo-badge">2025</span>
    </div>
    <div class="nav-menu">
      <a href="?p=landing" class="nav-link">HOME</a>
      <a href="?p=studio" class="nav-link">STUDIO</a>
      <a href="?p=photobook" class="nav-link active">GALLERY</a>
      <a href="?p=photobooth" class="nav-link">PHOTOBOOTH</a>
      <a href="?p=frame" class="nav-link">FRAME</a>
    </div>
    <div class="nav-user">
      <?php if ($isLoggedIn): ?>
        <div class="dropdown">
          <button class="btn btn-link p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php
            // Always ensure avatar URL (Gravatar if not available)
            $avatarUrl = $user['avatar_url'] ?? null;
            if (empty($avatarUrl) && !empty($user['email'])) {
              $emailHash = md5(strtolower(trim($user['email'])));
              $avatarUrl = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
            }
            
            if (!empty($avatarUrl)):
            ?>
              <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="nav-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="nav-avatar-fallback" style="display:none;"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            <?php else: ?>
              <div class="nav-avatar-fallback"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            <?php endif; ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="?p=studio">Studio</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=change-avatar">Change Avatar</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=logout">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="?p=login" class="nav-avatar-guest">?</a>
      <?php endif; ?>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
   
    <!-- No more select filter / summary button -->
  </div>

  <div class="pb-stage mb-2 position-relative">
    <div id="book" class="book">
      <!-- Navigation buttons on both sides -->
      <button id="btnPrev" class="pb-nav-btn pb-nav-left" aria-label="Previous">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button id="btnNext" class="pb-nav-btn pb-nav-right" aria-label="Next">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <!-- Left/right covers -->
      <div class="left-cover"></div>
      <div class="right-cover"></div>

      <!-- Sheets will be rendered by JS -->
    </div>
  </div>

  <p id="pbMeta" class="pb-meta mb-0"></p>
</div>

<script>
(function(){
  const LIST_URL   = '../ajax/photobook_list.php';
  const DELETE_URL = '../ajax/photobook_delete.php';

  let pages = [];    // [{id, layout, displayUrl, created_at, ...}]
  let sheets = [];   // [{left, right}]
  let turned = 0;    // number of sheets flipped (0 = cover closed)

  function webBase() {
    const m = location.pathname.match(/^(.*\/public)\/?/);
    return (m ? m[1] : '') + '/';
  }
  function toWebUrl(p) {
    if (!p) return '';
    p = String(p).replace(/^public\//,'').replace(/^\/+/,'');
    return webBase() + p;
  }

  function groupIntoSheets(list){
    const arr = [];
    for (let i=0; i<list.length; i+=2){
      arr.push({ left: list[i] || null, right: list[i+1] || null });
    }
    return arr;
  }

  async function loadPages() {
    const res = await fetch(LIST_URL, { cache: 'no-store' });
    const text = await res.text();
    let json;
    try { json = JSON.parse(text); } catch(e) {
      console.error('photobook_list TEXT:', text);
      throw e;
    }
    if (!json.success) throw new Error(json.error || 'Load failed');

    pages = (json.data || []).map(r => {
      const rel = (r.url || r.image_path || '').replace(/^public\//,'').replace(/^\/+/,'');
      return { ...r, displayUrl: toWebUrl(rel) + '?v=' + Date.now() };
    });
  }

  function renderBook(){
    const book = document.getElementById('book');
    book.querySelectorAll('.sheet').forEach(n => n.remove());

    sheets = groupIntoSheets(pages);

    for (let i=0; i<sheets.length; i++){
      const s = sheets[i];
      const sheet = document.createElement('div');
      sheet.className = 'sheet';
      sheet.style.zIndex = String(100 - i);

      // FRONT = right page
      const front = document.createElement('div');
      front.className = 'page front';
      if (s.right){
        const img = document.createElement('img');
        img.src = s.right.displayUrl;
        img.alt = `#${s.right.id}`;
        front.appendChild(img);
        front.dataset.pid = s.right.id;

        // small button for each right page
        const actions = document.createElement('div');
        actions.className = 'page-actions';
        actions.innerHTML = `
          <button class="mini-btn" data-action="download" data-id="${s.right.id}">Download</button>
          <button class="mini-btn danger" data-action="delete" data-id="${s.right.id}">Delete</button>
        `;
        front.appendChild(actions);
      }else{
        front.innerHTML = '<div class="text-muted">Empty</div>';
      }

      // BACK = left page
      const back = document.createElement('div');
      back.className = 'page back';
      if (s.left){
        const img2 = document.createElement('img');
        img2.src = s.left.displayUrl;
        img2.alt = `#${s.left.id}`;
        back.appendChild(img2);
        back.dataset.pid = s.left.id;

        // small button for each left page (visible when page is flipped)
        const actionsLeft = document.createElement('div');
        actionsLeft.className = 'page-actions';
        actionsLeft.innerHTML = `
          <button class="mini-btn" data-action="download" data-id="${s.left.id}">Download</button>
          <button class="mini-btn danger" data-action="delete" data-id="${s.left.id}">Delete</button>
        `;
        back.appendChild(actionsLeft);
      }else{
        back.innerHTML = '<div class="text-muted">Empty</div>';
      }

      sheet.appendChild(front);
      sheet.appendChild(back);
      book.appendChild(sheet);
    }

    turned = Math.min(turned, sheets.length);
    applyFlipState();
    updateMeta();
    updateNavDisabled();
  }

  function applyFlipState(){
    const all = Array.from(document.querySelectorAll('#book .sheet'));
    all.forEach((node, idx) => {
      node.classList.toggle('flipped', idx < turned);
    });
  }

  function updateMeta(){
  const meta = document.getElementById('pbMeta');

  if (!sheets.length){
    meta.textContent = '0/0';
    return;
  }

  // Total number of PAGES (not spreads)
  const totalPages = pages.length;

  // Currently displayed page: get right page of current sheet
  let currentPage = 0;
  if (turned < sheets.length) {
    const right = sheets[turned].right;
    if (right) {
      const idx = pages.findIndex(p => String(p.id) === String(right.id));
      currentPage = (idx >= 0) ? (idx + 1) : 0; // convert to 1-based
    } else {
      // if current sheet has no right page (odd case), consider as last page
      currentPage = totalPages;
    }
  } else {
    // flipped all → at end of book
    currentPage = totalPages;
  }

  // Only display "x/y" by PAGE
  meta.textContent = `${currentPage}/${totalPages}`;
}

  function updateNavDisabled(){
    const prevBtn = document.getElementById('btnPrev');
    const nextBtn = document.getElementById('btnNext');
    prevBtn.disabled = (turned <= 0);
    nextBtn.disabled = (turned >= sheets.length);
  }

  function nextSheet(){
    if (turned >= sheets.length) return;
    turned++;
    applyFlipState();
    updateMeta();
    updateNavDisabled();
  }
  function prevSheet(){
    if (turned <= 0) return;
    turned--;
    applyFlipState();
    updateMeta();
    updateNavDisabled();
  }

  function jumpToOpenId(wantId){
    if (!wantId) return;
    const pageIdx = pages.findIndex(p => String(p.id) === String(wantId));
    if (pageIdx < 0) return;
    const sheetIndex = Math.floor(pageIdx / 2);
    turned = sheetIndex;
    applyFlipState();
    updateMeta();
    updateNavDisabled();
  }

  // Event delegation for each right page (download/delete)
  document.addEventListener('click', async (e)=>{
    const btn = e.target.closest('.mini-btn');
    if (!btn) return;
    const action = btn.dataset.action;
    const id = btn.dataset.id;
    if (!action || !id) return;

    const page = pages.find(p => String(p.id) === String(id));
    if (!page) return;

    if (action === 'download'){
      const a = document.createElement('a');
      a.href = page.displayUrl;
      a.download = `photobook_${page.id}.png`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      return;
    }

    if (action === 'delete'){
      if (!confirm('Delete this page?')) return;
      const r = await fetch(DELETE_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ id })
      });
      const j = await r.json();
      if (!j.success) { alert(j.error || 'Delete failed'); return; }

      const prevTurned = turned;
      await loadPages();     // load all (no filter)
      renderBook();
      turned = Math.min(prevTurned, sheets.length);
      applyFlipState();
      updateMeta();
      updateNavDisabled();
      return;
    }
  });

  // Arrow buttons + keyboard shortcuts
  document.getElementById('btnNext').addEventListener('click', nextSheet);
  document.getElementById('btnPrev').addEventListener('click', prevSheet);
  window.addEventListener('keydown', (e)=>{
    if (e.key === 'ArrowRight') nextSheet();
    if (e.key === 'ArrowLeft')  prevSheet();
  });

  // Init
  document.addEventListener('DOMContentLoaded', async ()=>{
    const qs = new URLSearchParams(location.search);
    const wantId = qs.get('openId') || '';

    await loadPages();   // always load all
    renderBook();
    if (wantId) jumpToOpenId(wantId);
  });
})();
</script>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-links">
      <a href="?p=studio">Studio</a>
      <a href="?p=info">Info</a>
      <a href="?p=service">Service</a>
      <a href="?p=qa">Q&A</a>
      <a href="?p=contact">Contact</a>
    </div>
    <p class="footer-copyright">© <?= date('Y') ?> <strong>Space Photobooth</strong> | Show your style</p>
  </div>
</footer>

<!-- Mobile Menu Toggle -->
<script>
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.querySelector('.nav-menu');

if (menuToggle) {
  menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
  });
}
</script>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>

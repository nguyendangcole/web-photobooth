
<?php
// 1) Bắt buộc đăng nhập TRƯỚC KHI render HTML
$GUARD_PAGE = 'photobook';                            // ← tên trang hiện tại
require __DIR__ . '/includes/auth_guard.php';        // tạo file này ở app/includes/auth_guard.php

// 2) Chặn cache cho nội dung riêng tư (HTML page)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// 3) Sau đó mới load header/layout
require __DIR__ . '/header.php';
?>


<style>
/* ================= Flipbook core ================= */
:root{
  /* KÍCH THƯỚC VÀ COVER (đổi ở đây) */
  --book-w: 900px;       /* max chiều rộng quyển */
  --book-h: 520px;       /* chiều cao quyển */
  --page-pad: 12px;      /* viền trống bên trong mỗi trang */
  --img-fit: contain;    /* 'contain' hoặc 'cover' */
  --cover-left:  url('images/Cover1.png');   /* hoặc 'none' */
  --cover-right: url('images/Cover1.png');   /* hoặc 'none' */
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

/* Covers: set ảnh qua CSS var ở :root */
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

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
   
    <!-- Không còn select filter / nút tổng -->
  </div>

  <div class="pb-stage mb-2 position-relative">
    <div id="book" class="book">
      <!-- Nút điều hướng hai bên -->
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

      <!-- Bìa trái/phải -->
      <div class="left-cover"></div>
      <div class="right-cover"></div>

      <!-- Sheets sẽ render bằng JS -->
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
  let turned = 0;    // số tờ đã lật (0 = bìa đóng)

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

      // FRONT = trang phải
      const front = document.createElement('div');
      front.className = 'page front';
      if (s.right){
        const img = document.createElement('img');
        img.src = s.right.displayUrl;
        img.alt = `#${s.right.id}`;
        front.appendChild(img);
        front.dataset.pid = s.right.id;

        // nút nhỏ mỗi trang phải
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

      // BACK = trang trái
      const back = document.createElement('div');
      back.className = 'page back';
      if (s.left){
        const img2 = document.createElement('img');
        img2.src = s.left.displayUrl;
        img2.alt = `#${s.left.id}`;
        back.appendChild(img2);
        back.dataset.pid = s.left.id;

        // nút nhỏ mỗi trang trái (khi lật trang sẽ thấy)
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

  // Tổng số TRANG (không phải spread)
  const totalPages = pages.length;

  // Trang đang hiển thị: lấy trang bên phải của tờ hiện tại
  let currentPage = 0;
  if (turned < sheets.length) {
    const right = sheets[turned].right;
    if (right) {
      const idx = pages.findIndex(p => String(p.id) === String(right.id));
      currentPage = (idx >= 0) ? (idx + 1) : 0; // chuyển về 1-based
    } else {
      // nếu tờ hiện tại không có trang phải (trường hợp lẻ), coi như trang cuối
      currentPage = totalPages;
    }
  } else {
    // đã lật hết → đang ở cuối sách
    currentPage = totalPages;
  }

  // Chỉ hiển thị "x/y" theo TRANG
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

  // Event delegation cho mỗi trang phải (download/delete)
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
      await loadPages();     // load all (không filter)
      renderBook();
      turned = Math.min(prevTurned, sheets.length);
      applyFlipState();
      updateMeta();
      updateNavDisabled();
      return;
    }
  });

  // Nút mũi tên + phím tắt
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

    await loadPages();   // luôn load tất cả
    renderBook();
    if (wantId) jumpToOpenId(wantId);
  });
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>

<?php
// app/frame.php
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
$uid = $isLoggedIn ? (int)$user['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Frame Composer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
  <style>
  /* Dark compact header - same as main_menu.php */
  .main-nav {
    padding: 12px 0 !important;
    background: #0a0a0a !important;
    border-bottom: 2px solid #c1ff72 !important;
  }
  .nav-wrapper {
    padding: 0 20px !important;
  }
  .logo {
    font-size: 16px !important;
  }
  .logo-icon {
    font-size: 20px !important;
    color: #c1ff72 !important;
  }
  .logo-text {
    color: #ffffff !important;
  }
  .logo-badge {
    font-size: 9px !important;
    padding: 1px 5px !important;
    background: #c1ff72 !important;
    color: #0a0a0a !important;
  }
  .nav-link {
    font-size: 13px !important;
    color: #ffffff !important;
  }
  .nav-link:hover {
    color: #c1ff72 !important;
  }
  .nav-user {
    display: flex;
    align-items: center;
    margin-left: 20px;
  }
  .nav-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #c1ff72;
  }
  .nav-avatar-fallback,
  .nav-avatar-guest {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #c1ff72;
    color: #0a0a0a;
    font-weight: 700;
    font-size: 16px;
    border: 2px solid #c1ff72;
    text-decoration: none;
  }
  .nav-avatar-guest {
    background: #999;
    color: #ffffff;
  }
  .menu-toggle span {
    background: #ffffff !important;
  }
  @media (max-width: 768px) {
    .nav-user {
      margin-left: 0;
      margin-top: 10px;
    }
    .nav-menu {
      background: #0a0a0a !important;
      border-top: 2px solid #c1ff72 !important;
    }
  }
  
  /* Compact footer - same as main_menu.php */
  .footer {
    background: var(--black);
    color: var(--white);
    padding: 10px 20px;
    border-top: 2px solid var(--black);
    margin-top: auto;
  }
  .footer-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
  }
  .footer-links {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
  }
  .footer-links a {
    color: var(--white);
    text-decoration: none;
    font-size: 11px;
    font-weight: 500;
    opacity: 0.8;
    transition: opacity 0.2s, color 0.2s;
  }
  .footer-links a:hover {
    opacity: 1;
    color: var(--c1ff72);
  }
  .footer-copyright {
    color: rgba(255, 255, 255, 0.6);
    font-size: 10px;
    margin: 0;
  }
  .footer-copyright strong {
    color: var(--c1ff72);
  }
  @media (max-width: 768px) {
    .footer-content {
      flex-direction: column;
      text-align: center;
      gap: 10px;
    }
    .footer-links {
      justify-content: center;
      gap: 1rem;
    }
  }
  
  /* Adjust page content for compact header */
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
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
      <a href="?p=photobook" class="nav-link">GALLERY</a>
      <a href="?p=photobooth" class="nav-link">PHOTOBOOTH</a>
      <a href="?p=frame" class="nav-link">FRAME</a>
    </div>
    <div class="nav-user">
      <?php if ($isLoggedIn): ?>
        <div class="dropdown">
          <button class="btn btn-link p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php
            // Luôn đảm bảo có avatar URL (Gravatar nếu chưa có)
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
<script>
// ==== Auth flags & localStorage namespace (per-user) ====
const IS_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;
const USER_NS      = "user:<?= $uid ?>:";        // namespace per user
const PHOTOS_KEY   = USER_NS + "selectedPhotos"; // key per user

// If not logged in, wipe old traces (including legacy non-namespaced key)
if (!IS_LOGGED_IN) {
  try {
    // legacy key
    localStorage.removeItem("selectedPhotos");

    // (Optional) deep-clean any namespaced keys that look like selectedPhotos
    // This prevents old frames from showing up across users in shared browsers.
    const toDelete = [];
    for (let i = 0; i < localStorage.length; i++) {
      const k = localStorage.key(i);
      if (k && k.endsWith(":selectedPhotos")) toDelete.push(k);
    }
    toDelete.forEach(k => localStorage.removeItem(k));
  } catch (_) {}
}
</script>

<!-- Dialog thông báo -->
<div class="modal fade" id="infoDialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Thông báo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body" id="dialogMessage"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container mt-5">
  <div class="row justify-content-center align-items-start">

    <!-- Controls -->
    <div id="leftControls" class="col-12 col-md-3 mb-3 mb-md-0">
      <!-- Container bọc nút -->
      <div class="controls-box border rounded p-3 shadow-sm d-grid gap-2 floaty-ctrl controls-reveal">
        <div class="controls-header">
          <h3 class="controls-title">✦ CONTROLS</h3>
          <p class="controls-subtitle">Craft your frame</p>
        </div>

        <!-- NHÓM: FRAME -->
        <div class="controls-top d-grid gap-2">
          <div class="section-label">FRAME OPTIONS</div>
          <button class="btn btn-success btn-sm w-100"
                  style="--bg:#E9B3FB; --hover:#4d0e62; --bd:black"
                  data-bs-toggle="offcanvas"
                  data-bs-target="#frameSidebar"
                  aria-controls="frameSidebar">
            Choose Frame
          </button>

          <button id="clearFrameBtn" class="btn btn-outline-secondary btn-sm w-100"
                  style="--bg:#E9B3FB; --hover:#4d0e62; --bd:black">
            Remove frame
          </button>

          <div class="btn-group w-100">
            <!-- Gọi setFrameLayout để sync với sidebar -->
            <button class="btn btn-primary btn-sm w-50 layout-btn-square"
                    style="--bg:#ffde59; --hover:#f4c60c; --bd:black"
                    onclick="setFrameLayout('square')">
              2×2 Grid
            </button>
            <button class="btn btn-secondary btn-sm w-50 layout-btn-vertical"
                    style="--bg:#FF2DF1; --hover:#A5158C; --bd:black"
                    onclick="setFrameLayout('vertical')">
              1×4 Strip
            </button>
          </div>
        </div>

        <hr class="divider-strong my-2">

        <!-- NHÓM: PHOTOS -->
        <div class="controls-bottom d-grid gap-2">
          <div class="section-label">PHOTO ACTIONS</div>
          <button id="uploadBtn" class="btn btn-primary btn-sm w-100"
                  style="--bg:#6F00FF; --hover:#260452; --bd:black">
            Upload pics
          </button>
          <input id="uploadInput" type="file" accept="image/*" class="d-none" multiple>

          <button id="clearAllBtn" class="btn btn-outline-secondary btn-sm w-100"
                  style="--bg:#00FFDE; --hover:#00CAFF; --txt:#fff; --txt-hover:#ffffff; --bd:black">
            Delete all
          </button>

          <button id="saveBtn" class="btn btn-outline-danger btn-sm w-100"
                  style="--bg:#FF0B55; --hover:#e18585; --txt:#fff; --txt-hover:#ffffff; --bd:black">
            SAVE
          </button>
        </div>

        <!-- Photobook -->
        <hr class="my-2">
        <div class="text-center mt-1 d-grid gap-2">
          <div class="section-label">PHOTOBOOK</div>
          <button id="pbAddBtn" class="btn btn-warning">
            Add to Photobook
          </button>
          <a href="?p=photobook" class="btn btn-outline-dark">
            Open Photobook
          </a>
        </div>

        <!-- tăng version nếu vừa sửa file JS để phá cache -->
        <script src="<?= asset('js/frame-share.js') ?>?v=4"></script>
        <script src="<?= asset('js/locket-autoshare.js') ?>?v=1"></script>
      </div>
    </div>

    <!-- Preview -->
    <div class="col-12 col-md-6 text-center">
      <div class="canvas-wrapper">
        <div class="canvas-header">
          <h3 class="canvas-title">YOUR CANVAS</h3>
          <span class="canvas-badge">Live Preview</span>
        </div>
        <div id="frame-preview"
             class="border p-2 bg-white shadow position-relative mx-auto floaty preview-reveal"
             style="max-width:100%;">
          <!-- ảnh của 4 ô sẽ được render bằng JS -->
          <img id="overlayImg" alt=""
               style="position:absolute; inset:0; width:100%; height:100%; pointer-events:none; display:none;">
        </div>
        <p class="canvas-hint">✨ Your artistic vision comes to life here</p>
      </div>
    </div>

  </div>
</div>

<?php include 'frame_sidebar.php'; ?>

<script>
// ====== LAYOUT BRIDGE (Control block ↔ Sidebar) ======
function setFrameLayout(layout) {
  window.currentFrameLayout = layout; // 'vertical' | 'square'
  if (typeof renderGrid === 'function') renderGrid(layout);
  window.dispatchEvent(new CustomEvent('frame-layout-change', { detail: { layout } }));
}

// Hiệu ứng xuất hiện
document.addEventListener('DOMContentLoaded', () => {
  const pv = document.getElementById('frame-preview');
  if (pv) requestAnimationFrame(() => pv.classList.add('show'));
  const cbox = document.querySelector('#leftControls .controls-box');
  if (cbox) requestAnimationFrame(() => cbox.classList.add('show'));
});

// =================== STATE ===================
const framePreview   = document.getElementById("frame-preview");
const overlayImgEl   = document.getElementById("overlayImg");
const saveBtn        = document.getElementById("saveBtn");
const clearFrameBtn  = document.getElementById("clearFrameBtn");
const uploadBtn      = document.getElementById("uploadBtn");
const uploadInput    = document.getElementById("uploadInput");
const clearAllBtn    = document.getElementById("clearAllBtn");

const MAX_PHOTOS = 4;

// Load from per-user key only if logged in
let photos = [];
try {
  if (IS_LOGGED_IN) {
    photos = JSON.parse(localStorage.getItem(PHOTOS_KEY) || "[]");
  } else {
    // Not logged in → do not load from storage
    photos = [];
  }
  if (!Array.isArray(photos)) photos = [];
} catch(_) { photos = []; }

let currentLayout = "square";                // mặc định 2x2
window.currentFrameLayout = currentLayout;   // đồng bộ cho sidebar
let currentFrameSrc = null;
let currentFrameLayout = null;

// map layout cho một số overlay cũ (nếu còn dùng)
const FRAME_LAYOUT = {
  "images/6.png":  "vertical",
  "images/7.png":  "vertical",
  "images/8.png":  "vertical",
  "images/11.png": "vertical",
  "images/10.png": "square",
  "images/12.png": "square",
  "images/13.png": "square",
  "images/14.png": "square",
  "images/20.png": "vertical",
  "images/21.png": "vertical",
  "images/22.png": "vertical",
  "images/23.png": "vertical",
  "images/24.png": "vertical",
  "images/25.png": "vertical",
  "images/26.png": "vertical",
  "images/27.png": "vertical"
};

// =================== DIALOG ===================
function showDialog(message) {
  const box = document.getElementById('dialogMessage');
  if (box) box.textContent = message;
  const modalEl = document.getElementById('infoDialog');
  if (modalEl && window.bootstrap) {
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  } else {
    alert(message);
  }
}

// =================== HELPERS ===================
function persistPhotos() {
  // Only persist if logged in
  if (!IS_LOGGED_IN) return;
  try {
    localStorage.setItem(PHOTOS_KEY, JSON.stringify(photos));
  } catch (_) {}
}
function updateCounters() {}

// =================== RENDER GRID ===================
function filterTemplatesFor(layout) {
  const tplWrap = document.querySelector("#frame-templates");
  if (!tplWrap) return;
  tplWrap.querySelectorAll(".template").forEach(card => {
    const allow = card.dataset.layout || "both";
    card.style.display = (allow === layout || allow === "both") ? "" : "none";
  });
}

function renderGrid(type) {
  currentLayout = type;
  window.currentFrameLayout = type;

  if (currentFrameSrc && currentFrameLayout && currentFrameLayout !== type) {
    currentFrameSrc = null;
    currentFrameLayout = null;
    overlayImgEl.removeAttribute("src");
    overlayImgEl.style.display = "none";
  }

  filterTemplatesFor(type);
  Array.from(framePreview.querySelectorAll(".cell-wrap")).forEach(n => n.remove());

  if (type === "square") {
    framePreview.style.width  = "500px";
    framePreview.style.height = "500px";
    framePreview.style.display = "grid";
    framePreview.style.gridTemplateColumns = "repeat(2, 1fr)";
    framePreview.style.gridTemplateRows    = "repeat(2, 1fr)";
    framePreview.style.gap = "10px";
  } else {
    framePreview.style.width  = "220px";
    framePreview.style.height = "820px";
    framePreview.style.display = "grid";
    framePreview.style.gridTemplateColumns = "1fr";
    framePreview.style.gridTemplateRows    = "repeat(4, 1fr)";
    framePreview.style.gap = "11px";
  }

  const toShow = photos.slice(0, MAX_PHOTOS);
  for (let i = 0; i < MAX_PHOTOS; i++) {
    const wrap = document.createElement("div");
    wrap.className = "cell-wrap border shadow position-relative";
    wrap.style.overflow = "hidden";
    framePreview.appendChild(wrap);

    if (toShow[i]) {
      const img = document.createElement("img");
      img.src = toShow[i];
      img.className = "photo-cell";
      img.style.width = "100%";
      img.style.height = "100%";
      img.style.objectFit = "cover";
      wrap.appendChild(img);
    } else {
      const ph = document.createElement("div");
      ph.style.width = "100%";
      ph.style.height = "100%";
      ph.style.display = "flex";
      ph.style.alignItems = "center";
      ph.style.justifyContent = "center";
      ph.style.color = "#999";
      ph.textContent = "Chưa có ảnh";
      wrap.appendChild(ph);
    }
  }
  overlayImgEl.style.zIndex = 5;
  updateCounters();
}

// =================== APPLY FRAME ===================
function applyTemplate(templateSrc, layout) {
  const needLayout = layout || FRAME_LAYOUT[templateSrc] || null;
  if (needLayout && needLayout !== currentLayout) {
    showDialog(`Khung này dành cho layout ${needLayout}. Hãy chọn đúng layout ở trên nhé.`);
    return;
  }
  currentFrameSrc = templateSrc;
  currentFrameLayout = needLayout;
  overlayImgEl.src = templateSrc;
  overlayImgEl.style.display = "block";

  const off = bootstrap.Offcanvas.getInstance(document.getElementById("frameSidebar"));
  if (off) off.hide();
}
window.applyTemplate = applyTemplate;
window.renderGrid = renderGrid;

// Clear frame
clearFrameBtn.addEventListener("click", () => {
  currentFrameSrc = null;
  currentFrameLayout = null;
  overlayImgEl.removeAttribute("src");
  overlayImgEl.style.display = "none";
});

// =================== UPLOAD FLOW ===================
uploadBtn.addEventListener('click', () => uploadInput.click());

uploadInput.addEventListener('change', async (e) => {
  const files = Array.from(e.target.files || []).filter(f => f.type.startsWith('image/'));
  uploadInput.value = '';
  if (!files.length) return;

  const remain = MAX_PHOTOS - photos.length;
  if (remain <= 0) {
    showDialog("Bạn đã đủ 4 ảnh. Hãy xoá bớt nếu muốn đổi ảnh.");
    return;
  }
  if (files.length > remain) {
    showDialog(`Bạn chỉ còn chỗ cho ${remain} ảnh.`);
    return;
  }

  try {
    const compressed = [];
    for (const f of files) {
      const dataUrl = await fileToCompressedDataURL(f);
      compressed.push(dataUrl);
    }
    photos = photos.concat(compressed).slice(0, MAX_PHOTOS);
    persistPhotos(); // will only save if logged in
    renderGrid(currentLayout);
    const need = MAX_PHOTOS - photos.length;
    if (need > 0) showDialog(`Còn thiếu ${need} ảnh nữa để xuất frame.`);
  } catch (err) {
    console.error(err);
    showDialog('Không thể xử lý ảnh: ' + err.message);
  }
});

clearAllBtn.addEventListener('click', () => {
  photos = [];
  try { localStorage.removeItem(PHOTOS_KEY); } catch(_) {}
  renderGrid(currentLayout);
  showDialog('Đã xoá tất cả ảnh.');
});

// =================== SAVE (Canvas lớn + PNG lossless) ===================
saveBtn.addEventListener("click", () => {
  composeCurrentFrame()
    .then(dataURL => {
      const link = document.createElement("a");
      link.href = dataURL;
      link.download = "photobooth.png";

      const isIOS = /iP(ad|hone|od)/.test(navigator.userAgent);
      const supportsDownload = 'download' in HTMLAnchorElement.prototype;

      if (!supportsDownload || isIOS) {
        window.open(dataURL, "_blank", "noopener");
      } else {
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    })
    .catch(err => {
      console.error(err);
      showDialog(err.message || 'Lỗi khi xuất ảnh');
    });
});

// ========== COMPRESS UTILS ==========
const MAX_EDGE = 1800;
const OUTPUT_MIME = 'image/jpeg';
const OUTPUT_QUALITY = 0.88;

function loadImageFromURL(url) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.onerror = () => reject(new Error('Không giải mã được ảnh.'));
    img.src = url;
  });
}

async function fileToCompressedDataURL(file) {
  const blobURL = URL.createObjectURL(file);
  try {
    const img = await loadImageFromURL(blobURL);
    const long0 = Math.max(img.width, img.height);
    const scale = long0 > MAX_EDGE ? (MAX_EDGE / long0) : 1;
    const w = Math.round(img.width * scale);
    const h = Math.round(img.height * scale);
    const c = document.createElement('canvas');
    c.width = w; c.height = h;
    const ctx = c.getContext('2d');
    ctx.drawImage(img, 0, 0, w, h);
    return c.toDataURL(OUTPUT_MIME, OUTPUT_QUALITY);
  } finally {
    URL.revokeObjectURL(blobURL);
  }
}

/* =======================================================================
   >>>>>  Photobook server: composeCurrentFrame() + Add & Jump  <<<<<
   ======================================================================= */

// Gom logic SAVE thành 1 hàm trả về dataURL PNG để dùng lại (save & upload)
async function composeCurrentFrame() {
  const MAX_PHOTOS = 4;
  if (photos.length !== MAX_PHOTOS) {
    const need = MAX_PHOTOS - photos.length;
    throw new Error(need > 0
      ? `Bạn cần thêm ${need} ảnh nữa để xuất frame.`
      : `Bạn có nhiều hơn ${MAX_PHOTOS} ảnh. Vui lòng chỉ giữ đúng ${MAX_PHOTOS} ảnh.`);
  }

  let cols, rows, w, h, gap;
  if (currentLayout === "square") { cols = 2; rows = 2; w = 2000; h = 2000; gap = 40; }
  else                            { cols = 1; rows = 4; w = 1000; h = 3600; gap = 40; }

  const canvas = document.createElement("canvas");
  canvas.width = w; canvas.height = h;
  const ctx = canvas.getContext("2d");
  ctx.fillStyle = "#fff";
  ctx.fillRect(0, 0, w, h);

  const imgs = await Promise.all(photos.map(src => new Promise((resolve, reject) => {
    const image = new Image();
    image.onload = () => resolve(image);
    image.onerror = () => reject(new Error("Không tải được ảnh người dùng."));
    image.src = src;
  })));

  const cellW = (w - (cols + 1) * gap) / cols;
  const cellH = (h - (rows + 1) * gap) / rows;

  imgs.forEach((img, i) => {
    const col = i % cols;
    const row = Math.floor(i / cols);
    const x = gap + col * (cellW + gap);
    const y = gap + row * (cellH + gap);

    const imgRatio  = img.width / img.height;
    const cellRatio = cellW / cellH;

    let sx, sy, sWidth, sHeight;
    if (imgRatio > cellRatio) {
      sHeight = img.height;
      sWidth  = img.height * cellRatio;
      sx = (img.width - sWidth) / 2; sy = 0;
    } else {
      sWidth  = img.width;
      sHeight = img.width / cellRatio;
      sx = 0; sy = (img.height - sHeight) / 2;
    }
    ctx.drawImage(img, sx, sy, sWidth, sHeight, x, y, cellW, cellH);
  });

  if (currentFrameSrc) {
    const overlay = await new Promise((resolve) => {
      const o = new Image();
      o.crossOrigin = 'anonymous';
      o.onload = () => resolve(o);
      o.onerror = () => resolve(null);
      o.src = currentFrameSrc;
    });
    if (overlay) ctx.drawImage(overlay, 0, 0, w, h);
  }

  return canvas.toDataURL("image/png");
}

// ===== Photobook: gửi lên server & mở đúng trang
const PB_ADD_URL = '../ajax/photobook_add.php';

document.getElementById('pbAddBtn')?.addEventListener('click', async () => {
  try {
    if (!IS_LOGGED_IN) {
      showDialog('Vui lòng đăng nhập để thêm vào Photobook.');
      return;
    }
    const dataURL = await composeCurrentFrame();
    const layout  = (window.currentFrameLayout || window.currentLayout || 'square');

    const res = await fetch(PB_ADD_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ image: dataURL, layout })
    });
    
    // Kiểm tra xem response có phải là JSON không
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch (e) {
      console.error('Invalid JSON response:', text);
      throw new Error('Server returned invalid response. Please check if you are logged in.');
    }
    
    if (!json.success) throw new Error(json.error || 'Upload failed');

    window.location.href = '?p=photobook&openId=' + encodeURIComponent(json.id);
  } catch (err) {
    console.error(err);
    showDialog(err.message || 'Cannot add to Photobook.');
  }
});

// =================== INIT ===================
renderGrid(currentLayout);
// báo layout 1 lần để sidebar lọc frame đúng loại
window.dispatchEvent(new CustomEvent('frame-layout-change', { detail: { layout: currentLayout } }));
if (photos.length && photos.length !== MAX_PHOTOS) {
  if (typeof showMissingIfAny === 'function') showMissingIfAny();
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap');

:root{
  --page-bg-url: url('images/67.png'); /* ← đổi ảnh nền toàn trang ở đây nếu muốn */
  --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --accent-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --lime-color: #c1ff72;
}

/* Controls Header */
.controls-header {
  text-align: center;
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
  border-bottom: 3px solid #000;
}

.controls-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
  color: #000;
}

.controls-subtitle {
  font-family: 'DM Mono', monospace;
  font-size: 0.85rem;
  color: #666;
  margin: 0;
}

.section-label {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  color: #666;
  text-align: center;
  margin-bottom: 0.5rem;
}


/* Canvas Wrapper */
.canvas-wrapper {
  position: relative;
}

.canvas-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.canvas-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1.8rem;
  font-weight: 700;
  margin: 0;
  color: #000;
}

.canvas-badge {
  background: var(--lime-color);
  color: #000;
  padding: 4px 12px;
  border-radius: 20px;
  font-family: 'Space Grotesk', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  border: 2px solid #000;
}

.canvas-hint {
  font-family: 'DM Mono', monospace;
  color: #666;
  font-size: 0.9rem;
  margin-top: 1rem;
  font-style: italic;
}

/* Làm nút to hơn */
.controls-box .btn {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1.1rem !important;
  padding: 15px 20px !important;
  border-radius: 16px !important;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

/* ==== Container nút ==== */
.controls-box{
  position: relative;
  border: 3px solid #000 !important;
  box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
  background: white;
  border-radius: 24px;
  overflow: visible;
}

/* Mapping màu nút trong #leftControls */
#leftControls .btn{
  background-color: var(--bg, var(--bs-btn-bg));
  border-color:     var(--bd, var(--bg, var(--bs-btn-border-color)));
  color:            var(--txt, #fff);
  transition: background-color .2s ease, border-color .2s ease, color .2s ease, filter .18s ease;
  border-width:     var(--bw, 2px);
  border-style:     var(--bstyle, solid);
}
#leftControls .btn:hover,
#leftControls .btn:focus{
  background-color: var(--hover, var(--bg, var(--bs-btn-hover-bg)));
  border-color:     var(--bd-hover, var(--bd, var(--hover, var(--bg, var(--bs-btn-hover-border-color)))));
  color:            var(--txt-hover, var(--txt, #fff));
  border-width:     var(--bw-hover, var(--bw, 2px));
}

/* Nhấn có glow nhẹ */
#leftControls .btn:active{
  filter: drop-shadow(0 12px 26px #00000033)
          drop-shadow(0 0 18px #c1ff7240);
  transform: scale(0.98);
}

/* Layout button styles */
.layout-btn-square,
.layout-btn-vertical {
  font-size: 0.9rem !important;
  padding: 12px 10px !important;
}

/* Floaty preview */
@keyframes floaty-preview {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  25% { transform: translateY(-8px) rotate(1deg); }
  75% { transform: translateY(-4px) rotate(-1deg); }
}
.floaty{
  animation: floaty-preview 5s ease-in-out infinite;
  filter: drop-shadow(0 15px 40px rgba(0,0,0,.15));
  will-change: transform;
  transition: transform .3s cubic-bezier(.2,.8,.2,1), filter .3s ease;
  border: 4px solid #000 !important;
  border-radius: 20px !important;
}
.floaty:hover{
  animation-play-state: paused;
  transform: translateY(-12px) scale(1.02) rotate(0deg);
  filter:
    drop-shadow(0 25px 50px rgba(0,0,0,.25))
    drop-shadow(0 0 30px rgba(102,126,234,.3));
}
.floaty:active{
  transform: translateY(-6px) scale(1.01);
  filter:
    drop-shadow(0 18px 35px rgba(0,0,0,.2))
    drop-shadow(0 0 20px rgba(102,126,234,.25));
}

/* Hiệu ứng xuất hiện */
.preview-reveal{ 
  opacity:0; 
  transform: translateY(30px) scale(0.9); 
  transition: opacity .7s cubic-bezier(0.34, 1.56, 0.64, 1), 
              transform .7s cubic-bezier(0.34, 1.56, 0.64, 1); 
}
.preview-reveal.show{ 
  opacity:1; 
  transform: translateY(0) scale(1); 
}

/* Reduce motion */
@media (prefers-reduced-motion: reduce){
  .floaty{ animation: none; transition: none; }
  .preview-reveal{ opacity:1; transform:none; transition:none; }
}

/* Floaty block controls */
@keyframes floaty-ctrl {
  0%   { transform: translateY(0) }
  50%  { transform: translateY(-5px) }
  100% { transform: translateY(0) }
}
.floaty-ctrl{
  animation: floaty-ctrl 4.8s ease-in-out infinite;
  will-change: transform, filter;
  transition: transform .3s cubic-bezier(.2,.8,.2,1), filter .3s ease;
  filter: drop-shadow(0 8px 18px rgba(0,0,0,.10));
}
.floaty-ctrl:hover{
  animation-play-state: paused;
  transform: translateY(-6px) scale(1.005);
  filter:
    drop-shadow(0 18px 36px rgba(0,0,0,.18))
    drop-shadow(0 0 18px rgba(193,255,114,.35));
}

/* Hiệu ứng xuất hiện nhẹ */
.controls-reveal{ 
  opacity:0; 
  transform: translateY(20px); 
  transition: opacity .6s ease .2s, transform .6s ease .2s; 
}
.controls-reveal.show{ 
  opacity:1; 
  transform:none; 
}

/* Responsive */
@media (max-width: 768px) {
  .canvas-title {
    font-size: 1.5rem;
  }
}

@media (prefers-reduced-motion: reduce){
  .floaty-ctrl{ animation:none; transition:none; }
  .controls-reveal{ opacity:1; transform:none; transition:none; }
}
</style>

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

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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

</body>
</html>

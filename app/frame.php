<?php
// app/frame.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo_helper.php';

$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
$uid = $isLoggedIn ? (int)$user['id'] : 0;

// SEO data
$seoData = default_seo_data('frame');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php render_seo_meta($seoData); ?>
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/S.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
  <style>
  /* Adjust page content for compact header */
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    overflow-x: hidden;
  }
  
  /* Main content wrapper - scrollable area between header and footer */
  .main-content-wrapper {
    position: relative;
    top: 0;
    left: 0;
    right: auto;
    bottom: auto;
    min-height: calc(100vh - 90px);
    overflow-y: visible;
    overflow-x: hidden;
    transition: none;
    display: flex;
    flex-direction: column;
    padding-top: 70px;
  }
  
  /* Container inside wrapper */
  .main-content-wrapper .container {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 0;
    overflow: visible;
  }
  
  /* Frame Toolbar - Y2K Style */
  .frame-toolbar {
    background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
    border: 3px solid #000;
    border-radius: 12px;
    padding: 8px 16px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: nowrap;
    box-shadow: 4px 4px 0px #000, 0 4px 20px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 100;
    width: fit-content;
    max-width: calc(100% - 20px);
    overflow-x: auto;
    overflow-y: visible;
    min-height: 52px;
  }
  
  .toolbar-group {
    overflow: visible;
    position: relative;
  }
  
  .toolbar-dropdown {
    overflow: visible;
  }
  
  .frame-toolbar::-webkit-scrollbar {
    height: 6px;
  }
  
  .frame-toolbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
  }
  
  .frame-toolbar::-webkit-scrollbar-thumb {
    background: #ff6bcd;
    border-radius: 3px;
  }
  
  .toolbar-separator {
    width: 2px;
    height: 24px;
    background: linear-gradient(180deg, #ff6bcd 0%, #00d4ff 50%, #ffde59 100%);
    margin: 0 5px;
    flex-shrink: 0;
    border-radius: 2px;
  }
  
  .toolbar-group {
    display: flex;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
  }
  
  .toolbar-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 6px 12px;
    border: 2px solid #000;
    background: #fff;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    color: #000;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    font-family: 'Space Grotesk', sans-serif;
    height: 34px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 2px 2px 0px #000;
  }
  
  .toolbar-btn:hover {
    transform: translate(-2px, -2px);
    box-shadow: 4px 4px 0px #000;
  }
  
  .toolbar-btn.active {
    background: linear-gradient(135deg, #ffde59 0%, #ffd700 100%);
    border-color: #000;
    color: #000;
    box-shadow: 3px 3px 0px #000;
  }
  
  .toolbar-btn-primary {
    background: linear-gradient(135deg, #c1ff72 0%, #a8ff5e 100%);
    color: #000;
    border-color: #000;
  }
  
  .toolbar-btn-primary:hover {
    background: linear-gradient(135deg, #d4ff8f 0%, #c1ff72 100%);
    transform: translate(-2px, -2px);
    box-shadow: 4px 4px 0px #000;
  }
  
  .toolbar-btn-success {
    background: linear-gradient(135deg, #c1ff72 0%, #a8ff5e 100%);
    color: #000;
    border-color: #000;
    font-weight: 700;
  }
  
  .toolbar-btn-success:hover {
    background: linear-gradient(135deg, #d4ff8f 0%, #c1ff72 100%);
    transform: translate(-2px, -2px);
    box-shadow: 4px 4px 0px #000;
  }
  
  .toolbar-btn-danger {
    background: linear-gradient(135deg, #ff6bcd 0%, #ff4db8 100%);
    color: #000;
    border-color: #000;
    font-weight: 700;
  }
  
  .toolbar-btn-danger:hover {
    background: linear-gradient(135deg, #ff8fdb 0%, #ff6bcd 100%);
    transform: translate(-2px, -2px);
    box-shadow: 4px 4px 0px #000;
  }
  
  .toolbar-btn-warning {
    background: linear-gradient(135deg, #ffde59 0%, #ffd700 100%);
    color: #000;
    border-color: #000;
    font-weight: 700;
  }
  
  .toolbar-btn-warning:hover {
    background: linear-gradient(135deg, #ffe97a 0%, #ffde59 100%);
    transform: translate(-2px, -2px);
    box-shadow: 4px 4px 0px #000;
  }
  
  
  .toolbar-btn-group {
    display: inline-flex;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    height: 30px;
  }
  
  .toolbar-btn-group .toolbar-btn {
    border: none;
    border-radius: 0;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    height: 100%;
    background: transparent;
  }
  
  .toolbar-btn-group .toolbar-btn:last-child {
    border-right: none;
  }
  
  .toolbar-btn-group .toolbar-btn.active {
    background: linear-gradient(135deg, #c1ff72 0%, #a8e063 100%) !important;
    color: #0a0a0f !important;
    border-color: transparent !important;
    font-weight: 700;
    box-shadow: 0 0 15px rgba(193, 255, 114, 0.4);
  }
  
  .toolbar-btn-group .layout-btn-square.active,
  .toolbar-btn-group .layout-btn-vertical.active {
    background: linear-gradient(135deg, #c1ff72 0%, #a8e063 100%) !important;
    color: #0a0a0f !important;
  }
  
  .toolbar-dropdown {
    position: relative;
    z-index: 1;
  }
  
  .toolbar-dropdown-menu {
    position: fixed;
    background: #fff;
    border: 3px solid #000;
    border-radius: 10px;
    box-shadow: 4px 4px 0px #000;
    padding: 6px;
    min-width: 160px;
    z-index: 10000;
    display: none;
  }
  
  .toolbar-dropdown.show .toolbar-dropdown-menu {
    display: block;
  }
  
  .toolbar-dropdown-item {
    display: block;
    width: 100%;
    padding: 8px 10px;
    border: 2px solid transparent;
    background: #fff;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: #000;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.15s ease;
    font-family: 'Space Grotesk', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 3px;
  }
  
  .toolbar-dropdown-item:last-child {
    margin-bottom: 0;
  }
  
  .toolbar-dropdown-item:hover {
    background: linear-gradient(135deg, #00d4ff 0%, #00c4f0 100%);
    border-color: #000;
    color: #000;
    box-shadow: 2px 2px 0px #000;
    transform: translate(-1px, -1px);
  }
  
  /* Canvas area - keep original style */
  .canvas-container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    padding-bottom: 6rem;
    overflow: auto;
  }
  
  </style>
</head>
<body>

<?php
// Include common header (light theme, frame page active)
$theme = 'light';
$activePage = 'frame';
include __DIR__ . '/includes/page_header.php';
?>
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

<!-- Notification Dialog -->
<div class="modal fade" id="infoDialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Notification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="dialogMessage"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Dialog -->
<div class="modal fade" id="confirmDialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDialogTitle">Confirm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmDialogMessage"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDialogBtn">Confirm</button>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="main-content-wrapper">
<div class="container">

  <!-- Frame Toolbar -->
  <div class="frame-toolbar" data-animate="fade-in" data-animate-on-load>
    <!-- Frame Options -->
    <div class="toolbar-group">
      <button class="toolbar-btn toolbar-btn-success"
              onclick="if(typeof openFrameSidebar === 'function') { openFrameSidebar(); } else { const sidebar = document.getElementById('frameSidebar'); sidebar.classList.add('open'); document.body.classList.add('sidebar-open'); }">
        Frame
          </button>
      <button id="clearFrameBtn" class="toolbar-btn">
        Remove
          </button>
    </div>

    <div class="toolbar-separator"></div>

    <!-- Layout Options -->
    <div class="toolbar-group">
      <div class="toolbar-btn-group">
        <button class="toolbar-btn layout-btn-square"
                    onclick="setFrameLayout('square')">
          2×2
            </button>
        <button class="toolbar-btn layout-btn-vertical"
                    onclick="setFrameLayout('vertical')">
          1x4
            </button>
          </div>
        </div>

    <div class="toolbar-separator"></div>

    <!-- Photo Actions -->
    <div class="toolbar-group">
      <button id="uploadBtn" class="toolbar-btn toolbar-btn-primary">
        Upload
          </button>
          <input id="uploadInput" type="file" accept="image/*" class="d-none" multiple>
      <button id="clearAllBtn" class="toolbar-btn">
        Clear
          </button>
    </div>

    <div class="toolbar-separator"></div>

    <!-- Save Button -->
    <div class="toolbar-group">
      <button id="saveBtn" class="toolbar-btn toolbar-btn-danger">
        Save
          </button>
        </div>

    <div class="toolbar-separator"></div>

    <!-- Photobook Dropdown -->
    <div class="toolbar-group">
      <div class="toolbar-dropdown" id="photobookDropdown">
        <button class="toolbar-btn toolbar-btn-warning" type="button">
          Gallery ▼
          </button>
        <div class="toolbar-dropdown-menu">
          <button id="pbAddBtn" class="toolbar-dropdown-item">
            Add to gallery
          </button>
          <button id="pbOpenBtn" class="toolbar-dropdown-item">
            Open gallery
          </button>
        </div>
      </div>
      </div>
    </div>

  <!-- Canvas Area -->
  <div class="canvas-container" data-animate="zoom-in" data-animate-on-load>
      <div class="canvas-wrapper">
        <div class="canvas-header">
          <h3 class="canvas-title">YOUR CANVAS</h3>
          <span class="canvas-badge">Live Preview</span>
        </div>
        <div id="frame-preview"
             class="border p-2 bg-white shadow position-relative mx-auto floaty preview-reveal"
             style="max-width:100%;">
        <!-- images of 4 cells will be rendered by JS -->
          <img id="overlayImg" alt=""
               style="position:absolute; inset:0; width:100%; height:100%; pointer-events:none; display:none;">
        </div>
        <p class="canvas-hint">✨ Your artistic vision comes to life here</p>
      </div>
    </div>

  <!-- increment version if just edited JS file to bust cache -->
  <!-- frame-share.js removed - file does not exist -->
  </div>
</div>

<?php include 'frame_sidebar.php'; ?>

<script>
// ====== LAYOUT BRIDGE (Control block ↔ Sidebar) ======
function setFrameLayout(layout) {
  window.currentFrameLayout = layout; // 'vertical' | 'square'
  
  // Update active state for toolbar layout buttons
  document.querySelectorAll('.layout-btn-square, .layout-btn-vertical').forEach(btn => {
    btn.classList.remove('active');
  });
  
  if (layout === 'square') {
    document.querySelector('.layout-btn-square')?.classList.add('active');
  } else if (layout === 'vertical') {
    document.querySelector('.layout-btn-vertical')?.classList.add('active');
  }
  
  if (typeof renderGrid === 'function') renderGrid(layout);
  window.dispatchEvent(new CustomEvent('frame-layout-change', { detail: { layout } }));
}

// Appear effect
document.addEventListener('DOMContentLoaded', () => {
  const pv = document.getElementById('frame-preview');
  if (pv) requestAnimationFrame(() => pv.classList.add('show'));
});

// Photobook dropdown positioning - append to body when open
const photobookDropdown = document.getElementById('photobookDropdown');
const photobookDropdownBtn = photobookDropdown?.querySelector('button');
const photobookDropdownMenu = photobookDropdown?.querySelector('.toolbar-dropdown-menu');

// Photobook dropdown positioning function
function updatePhotobookDropdownPosition() {
  if (!photobookDropdown || !photobookDropdown.classList.contains('show')) return;
  if (!photobookDropdownBtn || !photobookDropdownMenu) return;
  
  const rect = photobookDropdownBtn.getBoundingClientRect();
  const menu = photobookDropdownMenu;
  
  // Append to body if not already
  if (menu.parentElement !== document.body) {
    document.body.appendChild(menu);
  }
  
  menu.style.position = 'fixed';
  menu.style.top = (rect.bottom + 6) + 'px';
  menu.style.right = (window.innerWidth - rect.right) + 'px';
  menu.style.left = 'auto';
  menu.style.display = 'block';
}

if (photobookDropdownBtn && photobookDropdownMenu) {
  photobookDropdownBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = photobookDropdown.classList.contains('show');
    
    if (!isOpen) {
      photobookDropdown.classList.add('show');
      updatePhotobookDropdownPosition();
    } else {
      photobookDropdown.classList.remove('show');
      photobookDropdownMenu.style.display = 'none';
      // Return menu to original parent
      if (photobookDropdownMenu.parentElement === document.body) {
        photobookDropdown.appendChild(photobookDropdownMenu);
      }
    }
  });
  
  // Update position on scroll and resize
  window.addEventListener('scroll', updatePhotobookDropdownPosition, true);
  window.addEventListener('resize', updatePhotobookDropdownPosition);
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (photobookDropdown && !photobookDropdown.contains(e.target) && !photobookDropdownMenu?.contains(e.target)) {
    photobookDropdown.classList.remove('show');
    if (photobookDropdownMenu) {
      photobookDropdownMenu.style.display = 'none';
      // Return menu to original parent
      if (photobookDropdownMenu.parentElement === document.body) {
        photobookDropdown.appendChild(photobookDropdownMenu);
      }
    }
  }
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
    // First check if there are selected photos from photobooth
    const selectedPhotosStr = localStorage.getItem('selected_photos');
    if (selectedPhotosStr) {
      // Load selected photos from photobooth
      const selectedPhotos = JSON.parse(selectedPhotosStr);
      if (Array.isArray(selectedPhotos) && selectedPhotos.length > 0) {
        // Limit to MAX_PHOTOS
        photos = selectedPhotos.slice(0, MAX_PHOTOS);
        // Save to user's namespace and clear temporary selection
        localStorage.setItem(PHOTOS_KEY, JSON.stringify(photos));
        localStorage.removeItem('selected_photos');
      } else {
        // No selected photos, load existing photos from user namespace
        photos = JSON.parse(localStorage.getItem(PHOTOS_KEY) || "[]");
      }
    } else {
      // No selected photos, load existing photos from user namespace
      photos = JSON.parse(localStorage.getItem(PHOTOS_KEY) || "[]");
    }
  } else {
    // Not logged in → do not load from storage
    photos = [];
  }
  if (!Array.isArray(photos)) photos = [];
} catch(_) { photos = []; }

let currentLayout = "square";                // default 2x2
window.currentFrameLayout = currentLayout;   // sync for sidebar
let currentFrameSrc = null;
let currentFrameLayout = null;

// map layout for some old overlays (if still used)
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

function showConfirmDialog(title, message, onConfirm) {
  const titleEl = document.getElementById('confirmDialogTitle');
  const messageEl = document.getElementById('confirmDialogMessage');
  const btnEl = document.getElementById('confirmDialogBtn');
  const modalEl = document.getElementById('confirmDialog');
  
  if (titleEl) titleEl.textContent = title || 'Confirm';
  if (messageEl) messageEl.textContent = message || 'Are you sure?';
  
  // Remove previous event listeners by cloning
  const newBtn = btnEl.cloneNode(true);
  btnEl.parentNode.replaceChild(newBtn, btnEl);
  
  // Add new event listener
  newBtn.addEventListener('click', () => {
    if (window.bootstrap && modalEl) {
      bootstrap.Modal.getInstance(modalEl)?.hide();
    }
    if (onConfirm && typeof onConfirm === 'function') {
      onConfirm();
    }
  });
  
  if (modalEl && window.bootstrap) {
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  } else {
    if (confirm(message || 'Are you sure?')) {
      if (onConfirm && typeof onConfirm === 'function') {
        onConfirm();
      }
    }
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
  
  // Update active state for toolbar layout buttons
  document.querySelectorAll('.layout-btn-square, .layout-btn-vertical').forEach(btn => {
    btn.classList.remove('active');
  });
  
  if (type === 'square') {
    document.querySelector('.layout-btn-square')?.classList.add('active');
  } else if (type === 'vertical') {
    document.querySelector('.layout-btn-vertical')?.classList.add('active');
  }

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
      
      // Add delete button for this photo
      const deleteBtn = document.createElement("button");
      deleteBtn.className = "photo-delete-btn";
      deleteBtn.innerHTML = "×";
      deleteBtn.title = "Remove this photo";
      deleteBtn.dataset.index = i;
      deleteBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const idx = parseInt(deleteBtn.dataset.index);
        showConfirmDialog(
          "Remove Photo",
          `Remove photo ${idx + 1}?`,
          () => {
            photos.splice(idx, 1);
            persistPhotos();
            renderGrid(currentLayout);
          }
        );
      });
      wrap.appendChild(deleteBtn);
    } else {
      const ph = document.createElement("div");
      ph.style.width = "100%";
      ph.style.height = "100%";
      ph.style.display = "flex";
      ph.style.alignItems = "center";
      ph.style.justifyContent = "center";
      ph.style.color = "#999";
      ph.textContent = "No photo";
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
    showDialog(`This frame is for ${needLayout} layout. Please select the correct layout above.`);
    return;
  }
  currentFrameSrc = templateSrc;
  currentFrameLayout = needLayout;
  overlayImgEl.src = templateSrc;
  overlayImgEl.style.display = "block";

  // Close sidebar after selecting frame
  const sidebar = document.getElementById("frameSidebar");
  if (sidebar && sidebar.classList.contains("open")) {
    sidebar.classList.remove("open");
    document.body.classList.remove("sidebar-open");
  }
}
window.applyTemplate = applyTemplate;
window.renderGrid = renderGrid;

// Clear frame
clearFrameBtn.addEventListener("click", () => {
  showConfirmDialog(
    "Remove Frame",
    "Are you sure you want to remove the current frame?",
    () => {
      currentFrameSrc = null;
      currentFrameLayout = null;
      overlayImgEl.removeAttribute("src");
      overlayImgEl.style.display = "none";
    }
  );
});

// =================== UPLOAD FLOW ===================
uploadBtn.addEventListener('click', () => uploadInput.click());

uploadInput.addEventListener('change', async (e) => {
  const files = Array.from(e.target.files || []).filter(f => f.type.startsWith('image/'));
  uploadInput.value = '';
  if (!files.length) return;

  const remain = MAX_PHOTOS - photos.length;
  if (remain <= 0) {
    showDialog("You already have 4 photos. Delete some if you want to change photos.");
    return;
  }
  if (files.length > remain) {
    showDialog(`You only have space for ${remain} photo(s).`);
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
    if (need > 0) showDialog(`Still need ${need} more photo(s) to export frame.`);
  } catch (err) {
    console.error(err);
    showDialog('Cannot process image: ' + err.message);
  }
});

clearAllBtn.addEventListener('click', () => {
  photos = [];
  try { localStorage.removeItem(PHOTOS_KEY); } catch(_) {}
  renderGrid(currentLayout);
  showDialog('All photos deleted.');
});

// =================== SAVE (Large Canvas + PNG lossless) ===================
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
      showDialog(err.message || 'Error exporting image');
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
    img.onerror = () => reject(new Error('Cannot decode image.'));
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

// Consolidate SAVE logic into 1 function returning dataURL PNG for reuse (save & upload)
async function composeCurrentFrame() {
  const MAX_PHOTOS = 4;
  if (photos.length !== MAX_PHOTOS) {
    const need = MAX_PHOTOS - photos.length;
    throw new Error(need > 0
      ? `You need ${need} more photo(s) to export frame.`
      : `You have more than ${MAX_PHOTOS} photos. Please keep only ${MAX_PHOTOS} photos.`);
  }

  let cols, rows, w, h, gap;
  // Reduce canvas size to decrease file size (from 2000x2000 to 1200x1200)
  if (currentLayout === "square") { cols = 2; rows = 2; w = 1200; h = 1200; gap = 24; }
  else                            { cols = 1; rows = 4; w = 600; h = 2160; gap = 24; }

  const canvas = document.createElement("canvas");
  canvas.width = w; canvas.height = h;
  const ctx = canvas.getContext("2d");
  ctx.fillStyle = "#fff";
  ctx.fillRect(0, 0, w, h);

  const imgs = await Promise.all(photos.map(src => new Promise((resolve, reject) => {
    const image = new Image();
    image.onload = () => resolve(image);
    image.onerror = () => reject(new Error("Cannot load user photo."));
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

  // Use JPEG with quality 0.85 instead of PNG to reduce file size significantly
  return canvas.toDataURL("image/jpeg", 0.85);
}

// ===== Photobook: upload to server & open correct page
const PB_ADD_URL = '../ajax/photobook_add.php';

// Add to Book - Add current frame to photobook without redirecting
document.getElementById('pbAddBtn')?.addEventListener('click', async (e) => {
  e.stopPropagation();
  
  try {
    // Close dropdown
    if (photobookDropdown) {
      photobookDropdown.classList.remove('show');
      if (photobookDropdownMenu) {
        photobookDropdownMenu.style.display = 'none';
        // Return menu to original parent
        if (photobookDropdownMenu.parentElement === document.body) {
          photobookDropdown.appendChild(photobookDropdownMenu);
        }
      }
    }
    
    if (!IS_LOGGED_IN) {
      showDialog('Please login to add to Photobook.');
      return;
    }
    
    // Get layout: 'square' or 'vertical' (map 'vertical' from UI to 'vertical' for server)
    const uiLayout = currentLayout || window.currentFrameLayout || 'square';
    const layout = uiLayout === 'vertical' ? 'vertical' : 'square';
    
    console.log('Adding to photobook - Layout:', layout, 'UI Layout:', uiLayout);
    
    const dataURL = await composeCurrentFrame();
    console.log('Composed frame dataURL length:', dataURL ? dataURL.length : 0);
    
    if (!dataURL || typeof dataURL !== 'string' || dataURL.length === 0) {
      throw new Error('Failed to compose frame. Please try again.');
    }
    
    // Validate dataURL format
    if (!dataURL.match(/^data:image\/(png|jpeg|jpg);base64,/)) {
      console.error('Invalid dataURL format:', dataURL.substring(0, 50));
      throw new Error('Invalid image format. Please try again.');
    }

    console.log('Sending request to:', PB_ADD_URL);
    console.log('Image data length:', dataURL.length);
    console.log('Layout:', layout);
    
    const res = await fetch(PB_ADD_URL, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin', // Ensure cookies (session) are sent
      body: JSON.stringify({ image: dataURL, layout })
    });
    
    console.log('Response status:', res.status, res.statusText);
    
    // Get response text first
    const text = await res.text();
    console.log('Response text length:', text.length);
    console.log('Response text (first 1000 chars):', text.substring(0, 1000));
    
    // Check if response is OK
    if (!res.ok) {
      console.error('HTTP Error:', res.status, res.statusText);
      console.error('Error response:', text);
      
      // Try to parse as JSON for error details
      try {
        const errorJson = JSON.parse(text);
        if (errorJson.error) {
          throw new Error(errorJson.error);
        }
      } catch (e) {
        // Not JSON or parse failed
        if (res.status === 401 || res.status === 403) {
          throw new Error('Session expired. Please login again.');
        }
        if (res.status === 500) {
          throw new Error('Server error. Please check server logs.');
        }
        throw new Error(`Server error (${res.status}): ${res.statusText}`);
      }
    }
    
    // Parse JSON response
    let json;
    try {
      json = JSON.parse(text);
      console.log('Parsed JSON:', json);
    } catch (e) {
      console.error('JSON parse error:', e);
      console.error('Response text:', text);
      throw new Error('Server returned invalid JSON. Please try again.');
    }
    
    // Check if success
    if (!json || !json.success) {
      const errorMsg = json?.error || 'Upload failed';
      console.error('Upload failed:', errorMsg);
      throw new Error(errorMsg);
    }

    // Success! Redirect to photobook page
    console.log('✅ Photobook add success!', json);
    
    // Navigate to photobook page with the newly added image ID
    const currentUrl = window.location.href;
    const baseUrl = currentUrl.split('?')[0]; // Get base URL without query
    const photobookUrl = baseUrl + '?p=photobook' + (json.id ? '&openId=' + json.id : '');
    
    console.log('Navigating to photobook:', photobookUrl);
    window.location.href = photobookUrl;
  } catch (err) {
    console.error('Photobook add error:', err);
    const errorMsg = err.message || 'Cannot add to Photobook. Please try again.';
    showDialog(errorMsg);
    
    // If it's a "need more photos" error, make it more user-friendly
    if (errorMsg.includes('need') && errorMsg.includes('photo')) {
      console.error('User needs to add more photos before exporting.');
    }
  }
});

// Open Book - Navigate to photobook page
document.getElementById('pbOpenBtn')?.addEventListener('click', (e) => {
  e.preventDefault();
  e.stopPropagation();
  
  console.log('Opening photobook page...');
  
  // Close dropdown immediately
  if (photobookDropdown) {
    photobookDropdown.classList.remove('show');
    if (photobookDropdownMenu) {
      photobookDropdownMenu.style.display = 'none';
      // Return menu to original parent
      if (photobookDropdownMenu.parentElement === document.body) {
        photobookDropdown.appendChild(photobookDropdownMenu);
      }
    }
  }
  
  // Navigate to photobook page
  const currentUrl = window.location.href;
  const baseUrl = currentUrl.split('?')[0]; // Get base URL without query
  const photobookUrl = baseUrl + '?p=photobook';
  
  console.log('Navigating to:', photobookUrl);
  window.location.href = photobookUrl;
});

// =================== INIT ===================
renderGrid(currentLayout);
// notify layout once so sidebar filters correct frame type
window.dispatchEvent(new CustomEvent('frame-layout-change', { detail: { layout: currentLayout } }));
if (photos.length && photos.length !== MAX_PHOTOS) {
  if (typeof showMissingIfAny === 'function') showMissingIfAny();
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap');

:root{
  --page-bg-url: url('images/67.png'); /* ← change full page background image here if desired */
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

/* Make buttons larger */
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

/* ==== Button container ==== */
.controls-box{
  position: relative;
  border: 3px solid #000 !important;
  box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
  background: white;
  border-radius: 24px;
  overflow: visible;
}

/* Button color mapping in #leftControls */
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

/* Click has light glow */
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

/* Appear effect */
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

/* Light appear effect */
.controls-reveal{ 
  opacity:0; 
  transform: translateY(20px); 
  transition: opacity .6s ease .2s, transform .6s ease .2s; 
}
.controls-reveal.show{ 
  opacity:1; 
  transform:none; 
}

/* Photo Delete Button */
.photo-delete-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 28px;
  height: 28px;
  background: #ff4757;
  color: white;
  border: 2px solid #000;
  border-radius: 50%;
  font-size: 20px;
  line-height: 1;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.2s ease;
  box-shadow: 2px 2px 0px #000;
  z-index: 10;
  padding: 0;
}
.cell-wrap:hover .photo-delete-btn {
  opacity: 1;
  transform: scale(1);
}
.photo-delete-btn:hover {
  background: #ff6b7a;
  transform: scale(1.1) rotate(90deg);
  box-shadow: 3px 3px 0px #000;
}
.photo-delete-btn:active {
  transform: scale(0.95) rotate(90deg);
  box-shadow: 1px 1px 0px #000;
}

/* Responsive */
@media (max-width: 768px) {
  /* Adjust main content wrapper for mobile */
  .main-content-wrapper {
    padding-top: 140px !important;
    min-height: calc(100vh - 90px);
  }
  
  /* Frame Toolbar - Mobile optimized with vertical layout */
  .frame-toolbar {
    position: sticky !important;
    top: 0px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: auto !important;
    max-width: 200px !important;
    margin: 0 !important;
    padding: 12px 8px !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    min-height: auto !important;
    box-shadow: 3px 3px 0px #000, 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    border-radius: 12px !important;
    z-index: 999 !important;
    background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%) !important;
    border: 3px solid #000 !important;
  }
  
  .toolbar-separator {
    display: none; /* Hide separators on mobile to save space */
  }
  
  .toolbar-btn {
    padding: 10px 16px !important;
    font-size: 12px !important;
    height: 38px !important;
    width: 140px !important;
    gap: 4px !important;
    box-shadow: 2px 2px 0px #000 !important;
    flex-shrink: 0 !important;
    white-space: nowrap !important;
    margin: 0 !important;
    text-align: center !important;
  }
  
  .toolbar-btn:hover {
    transform: translate(-1px, -1px);
    box-shadow: 2px 2px 0px #000;
  }
  
  .toolbar-group {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 6px !important;
    width: 100% !important;
    margin: 4px 0 !important;
  }
  
  .toolbar-btn-group {
    display: flex !important;
    flex-direction: column !important;
    height: auto !important;
    width: 140px !important;
    border: 2px solid #000 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: rgba(255, 255, 255, 0.9) !important;
  }
  
  .toolbar-btn-group .toolbar-btn {
    padding: 8px 12px !important;
    font-size: 11px !important;
    height: 32px !important;
    width: 100% !important;
    border: none !important;
    border-radius: 0 !important;
    border-bottom: 1px solid #000 !important;
    margin: 0 !important;
    box-shadow: none !important;
  }
  
  .toolbar-btn-group .toolbar-btn:last-child {
    border-bottom: none !important;
  }
  
  /* Canvas area - mobile optimized */
  .canvas-container {
    padding: 1rem 0.5rem;
    padding-bottom: 2rem;
    padding-top: 0.5rem;
  }
  
  .canvas-header {
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
  }
  
  .canvas-title {
    font-size: 1.2rem;
  }
  
  .canvas-badge {
    font-size: 0.65rem;
    padding: 3px 8px;
  }
  
  .canvas-hint {
    font-size: 0.75rem;
    text-align: center;
    margin-top: 0.5rem;
  }
  
  /* Frame preview - scale down for mobile */
  #frame-preview {
    transform: scale(0.45);
    transform-origin: top center;
    margin-bottom: -150px; /* Compensate for scale */
    max-width: 500px !important;
  }
  
  /* Also scale down vertical layout */
  #frame-preview[style*="width: 220px"] {
    transform: scale(0.55);
    margin-bottom: -280px;
  }
  
  /* Dropdown menu positioning for mobile */
  .toolbar-dropdown-menu {
    min-width: 130px;
    padding: 4px;
  }
  
  .toolbar-dropdown-item {
    padding: 6px 8px;
    font-size: 9px;
  }
}

@media (max-width: 480px) {
  /* Extra small screens - main content wrapper inherits from 768px breakpoint */
  
  .frame-toolbar {
    /* Position sticky inherited from 768px breakpoint */
    padding: 5px 6px;
  }
  
  .frame-toolbar {
    padding: 6px;
    gap: 4px;
  }
  
  .toolbar-btn {
    padding: 5px 8px;
    font-size: 9px;
    height: 28px;
  }
  
  .toolbar-btn-group .toolbar-btn {
    padding: 3px 6px;
    font-size: 8px;
  }
  
  .canvas-title {
    font-size: 1rem;
  }
  
  .canvas-badge {
    font-size: 0.6rem;
    padding: 2px 6px;
  }
  
  /* Scale down preview even more on very small screens */
  #frame-preview {
    transform: scale(0.35);
    margin-bottom: -200px; /* Compensate for scale */
  }
  
  /* Also scale down vertical layout even more */
  #frame-preview[style*="width: 220px"] {
    transform: scale(0.42);
    margin-bottom: -380px;
  }
  
  .canvas-container {
    padding: 0.5rem 0.25rem;
    padding-bottom: 1.5rem;
    padding-top: 0.25rem;
  }
  
  .canvas-hint {
    font-size: 0.7rem;
  }
}

@media (prefers-reduced-motion: reduce){
  .floaty-ctrl{ animation:none; transition:none; }
  .controls-reveal{ opacity:1; transform:none; transition:none; }
}
</style>

<?php
// Include common footer (light theme)
include __DIR__ . '/includes/page_footer.php';
?>

</body>
</html>

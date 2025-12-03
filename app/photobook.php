
<?php
// 1) Require login BEFORE rendering HTML
$GUARD_PAGE = 'photobook';
require __DIR__ . '/includes/auth_guard.php';

// 2) Block cache for private content (HTML page)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// 3) Load config
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo_helper.php';

$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';

// SEO data
$seoData = default_seo_data('photobook');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/S.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php render_seo_meta($seoData); ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
  

<style>
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #ffffff;
}

/* Theme colors */
:root{
  --color-lime: #c1ff72;
  --color-pink: #ff6bcd;
  --color-cyan: #00d4ff;
  --color-yellow: #ffde59;
}

/* Header */
.photobook-header {
  text-align: center;
  margin: 40px 0 30px;
  padding: 0 20px;
}

.photobook-header h1 {
  font-family: 'Bebas Neue', cursive;
  font-size: 3.5rem;
  color: #333;
  margin-bottom: 0.5rem;
  letter-spacing: 3px;
  text-shadow: 2px 2px 0px rgba(255,107,205,0.2);
}

.photobook-header p {
  font-family: 'Space Grotesk', sans-serif;
  color: #888;
  font-size: 1rem;
  margin: 0;
}

/* Gallery Container */
.photobook-gallery {
  position: relative;
  min-height: 600px;
  margin: 0 auto;
  padding: 40px 20px;
  max-width: 1400px;
  overflow: visible;
}

/* Photo Item */
.photo-item {
  position: absolute;
  cursor: move;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
              box-shadow 0.3s ease,
              z-index 0s;
  will-change: transform;
  z-index: 1;
}

.photo-item[data-layout="vertical"] {
  max-width: 180px;
}

.photo-item[data-layout="square"] {
  max-width: 300px;
}

.photo-item:hover {
  z-index: 100;
  transform: scale(1.05);
}

.photo-item[data-dragging="true"] {
  z-index: 1000;
  cursor: grabbing;
  transform: scale(1.1);
  box-shadow: 0 20px 60px rgba(0,0,0,0.4);
  opacity: 0.8;
}

/* Photo Frame */
.photo-frame {
  position: relative;
  background: #fff;
  border-radius: 0;
  padding: 0;
  box-shadow: 
    0 8px 24px rgba(0,0,0,0.15),
    0 0 0 1px rgba(0,0,0,0.08);
  transform-origin: center;
  transition: all 0.3s ease;
}

.photo-item:hover .photo-frame {
  box-shadow: 
    0 12px 32px rgba(0,0,0,0.2),
    0 0 0 2px var(--color-pink);
}

/* Photo Image */
.photo-img {
  width: 100%;
  height: auto;
  display: block;
  border-radius: 0;
  background: #f0f0f0;
  object-fit: contain;
}

/* Photo Actions Overlay */
.photo-actions {
  position: absolute;
  top: 12px;
  right: 12px;
  display: flex;
  gap: 8px;
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 10;
}

.photo-item:hover .photo-actions {
  opacity: 1;
  transform: translateY(0);
}

/* Action Buttons */
.action-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  backdrop-filter: blur(10px);
  font-family: 'Space Grotesk', sans-serif;
}

.action-btn:hover {
  transform: scale(1.15) rotate(5deg);
  box-shadow: 0 6px 16px rgba(0,0,0,0.3);
}

.action-btn.download {
  background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.9) 100%);
  color: #fff;
}

.action-btn.download:hover {
  background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,1) 100%);
}

.action-btn.delete {
  background: linear-gradient(135deg, #ff4757 0%, #ff3838 100%);
  color: #fff;
}

.action-btn.delete:hover {
  background: linear-gradient(135deg, #ff6b7a 0%, #ff4757 100%);
  box-shadow: 0 6px 16px rgba(255,71,87,0.5);
}

/* Lightbox Modal */
.photo-lightbox {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.95);
  backdrop-filter: blur(10px);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 40px 20px;
}

.photo-lightbox.active {
  opacity: 1;
  visibility: visible;
}

.photo-lightbox-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  align-items: center;
  justify-content: center;
  transform: scale(0.8);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.photo-lightbox.active .photo-lightbox-content {
  transform: scale(1);
}

.photo-lightbox-img {
  max-width: 100%;
  max-height: 90vh;
  width: auto;
  height: auto;
  display: block;
  border-radius: 0;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5);
  background: #000;
}

.photo-lightbox-close {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 2px solid #fff;
  background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
  color: #fff;
  font-size: 24px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  backdrop-filter: blur(10px);
  z-index: 10001;
  font-family: 'Space Grotesk', sans-serif;
}

.photo-lightbox-close:hover {
  background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.2) 100%);
  transform: scale(1.1) rotate(90deg);
}

.photo-lightbox-close:active {
  transform: scale(0.95) rotate(90deg);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: #999;
}

.empty-state-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
  animation: sparkle 2s ease-in-out infinite;
}

@keyframes sparkle {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.1); }
}

.empty-state h3 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: #666;
}

.empty-state p {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1rem;
  color: #999;
}

/* Meta Counter */
.photobook-meta {
  text-align: center;
  margin-top: 30px;
  padding: 0 20px;
}

.photobook-meta .meta-badge {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 0.95rem;
  color: #666;
  padding: 0.75rem 1.5rem;
  background: rgba(255,255,255,0.8);
  border-radius: 20px;
  display: inline-block;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  font-weight: 600;
}

/* Responsive */
@media (max-width: 768px){
  .photobook-header h1 {
    font-size: 2.5rem;
  }
  
  .photobook-gallery {
    padding: 20px 15px;
  }
  
  .photo-item {
    max-width: 320px !important;
  }
  
  .action-btn {
    width: 36px;
    height: 36px;
    font-size: 14px;
  }
}

@media (max-width: 576px){
  .photobook-header h1 {
    font-size: 2rem;
    letter-spacing: 1px;
  }
  
  .photo-item {
    max-width: 280px !important;
  }
  
  .photo-actions {
    gap: 6px;
    top: 8px;
    right: 8px;
  }
}
</style>
</head>
<body>

<?php
// Include common header (light theme, photobook page active)
$theme = 'light';
$activePage = 'photobook';
include __DIR__ . '/includes/page_header.php';
?>

<div class="container py-4">
  <div class="photobook-header" data-animate="fade-down" data-animate-on-load>
    <h1>MY PHOTOBOOK</h1>
    <p>Drag & arrange your memories ✨</p>
  </div>

  <div class="photobook-gallery" id="photobookGallery" data-animate="zoom-in" data-animate-on-load>
    <!-- Photos will be rendered here -->
  </div>

  <div class="photobook-meta text-center mt-4">
    <span id="photoCount" class="meta-badge">0 photos</span>
  </div>
</div>

<!-- Photo Lightbox -->
<div class="photo-lightbox" id="photoLightbox">
  <button class="photo-lightbox-close" id="lightboxClose">×</button>
  <div class="photo-lightbox-content">
    <img class="photo-lightbox-img" id="lightboxImg" src="" alt="Photo">
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

<!-- Photobook / Gallery User Guide Modal -->
<div class="modal fade" id="photobookGuideModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:3px solid #000;border-radius:16px;">
      <div class="modal-header" style="border-bottom:2px solid #000;background:linear-gradient(135deg,#c1ff72 0%,#00f5ff 100%);">
        <h5 class="modal-title" style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:#000;">
          How to use your Gallery
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="font-family:'Space Grotesk',sans-serif;padding:1.5rem;color:#222;">
        <ol style="margin-left:1rem;padding-left:0.5rem;">
          <li>Your saved frames from the <strong>Frame</strong> page will appear here as photos.</li>
          <li><strong>Drag</strong> photos around to arrange your own layout on the canvas.</li>
          <li><strong>Double‑click</strong> a photo to open it in a large lightbox view.</li>
          <li>Use the small <strong>↓ button</strong> on each photo to download it to your device.</li>
          <li>Use the small <strong>× button</strong> to delete a photo from your gallery (this cannot be undone).</li>
          <li>The system remembers positions, so your layout stays similar on refresh.</li>
          <li>Come back here anytime from the top navigation to revisit your creations.</li>
        </ol>
      </div>
      <div class="modal-footer" style="border-top:2px solid #000;gap:10px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                style="font-family:'Space Grotesk',sans-serif;font-weight:600;border:2px solid #000;border-radius:8px;padding:8px 18px;">
          Close
        </button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                style="font-family:'Space Grotesk',sans-serif;font-weight:700;background:#c1ff72;color:#000;border:2px solid #000;border-radius:8px;padding:8px 18px;">
          Got it!
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Floating Help Button for Gallery page -->
<button id="openPhotobookGuide"
        type="button"
        aria-label="Open Gallery guide"
        style="
          position:fixed;
          right:16px;
          bottom:96px;
          z-index:1060;
          width:42px;
          height:42px;
          border-radius:50%;
          border:2px solid #000;
          background:#fffbe6;
          box-shadow:2px 2px 0px #000;
          display:flex;
          align-items:center;
          justify-content:center;
          font-family:'Space Grotesk',sans-serif;
          font-weight:700;
          font-size:20px;
          color:#000;
        ">
  ?
</button>

<script>
(function(){
  const LIST_URL   = '../ajax/photobook_list.php';
  const DELETE_URL = '../ajax/photobook_delete.php';
  const DRAG_BOUNDS = { overlapX: 12, overlapY: 18 }; // allow 12% overlap horizontally, 18% vertically

  // Confirmation Dialog Function
  function showConfirmDialog(title, message, onConfirm) {
    const titleEl = document.getElementById('confirmDialogTitle');
    const messageEl = document.getElementById('confirmDialogMessage');
    const btnEl = document.getElementById('confirmDialogBtn');
    const modalEl = document.getElementById('confirmDialog');
    
    if (titleEl) titleEl.textContent = title || 'Confirm';
    if (messageEl) messageEl.textContent = message || 'Are you sure?';
    
    const newBtn = btnEl.cloneNode(true);
    btnEl.parentNode.replaceChild(newBtn, btnEl);
    
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

  let photos = [];
  let zIndexCounter = 10;
  let dndContext = null;

  function webBase() {
    const m = location.pathname.match(/^(.*\/public)\/?/);
    return (m ? m[1] : '') + '/';
  }

  function toWebUrl(p) {
    if (!p) return '';
    p = String(p).replace(/^public\//,'').replace(/^\/+/,'');
    return webBase() + p;
  }

  async function loadPhotos() {
    const res = await fetch(LIST_URL, { cache: 'no-store' });
    const text = await res.text();
    let json;
    try { 
      json = JSON.parse(text); 
    } catch(e) {
      console.error('photobook_list TEXT:', text);
      throw e;
    }
    if (!json.success) throw new Error(json.error || 'Load failed');

    photos = (json.data || []).map(r => {
      const layout = (r.layout || 'square').toLowerCase();
      const rel = (r.url || r.image_path || '').replace(/^public\//,'').replace(/^\/+/,'');
      const isVertical = layout === 'vertical';
      const minWidth = isVertical ? 110 : 200;
      const maxWidth = isVertical ? 170 : 280;
      return { 
        ...r, 
        layout,
        displayUrl: toWebUrl(rel) + '?v=' + Date.now(),
        // Load saved position or generate random
        x: r.x || Math.random() * 70 + 5, // 5-75% of container width
        y: r.y || Math.random() * 40 + 5, // 5-45% of container height
        rotation: 0, // No rotation - keep images straight
        size: r.size || (Math.random() * (maxWidth - minWidth) + minWidth) // width based on layout
      };
    });
  }

  function getRandomPosition() {
    const gallery = document.getElementById('photobookGallery');
    if (!gallery) return { x: 20, y: 20 };
    
    const galleryRect = gallery.getBoundingClientRect();
    const padding = 50;
    
    return {
      x: Math.random() * (galleryRect.width - 300) + padding,
      y: Math.random() * (galleryRect.height - 300) + padding
    };
  }

  function createPhotoElement(photo) {
    const item = document.createElement('div');
    item.className = 'photo-item';
    item.dataset.id = photo.id;
    item.dataset.layout = photo.layout || 'square';
    item.style.left = photo.x + '%';
    item.style.top = photo.y + '%';
    item.style.width = photo.size + 'px';
    item.style.transform = 'none'; // No rotation - straight images
    item.style.zIndex = zIndexCounter++;
    item.setAttribute('data-dnd-id', photo.id);

    const frame = document.createElement('div');
    frame.className = 'photo-frame';

    const img = document.createElement('img');
    img.src = photo.displayUrl;
    img.alt = `Photo ${photo.id}`;
    img.className = 'photo-img';
    img.style.width = '100%';
    img.style.height = 'auto';

    const actions = document.createElement('div');
    actions.className = 'photo-actions';

    const downloadBtn = document.createElement('button');
    downloadBtn.className = 'action-btn download';
    downloadBtn.innerHTML = '↓';
    downloadBtn.title = 'Download';
    downloadBtn.onclick = (e) => {
      e.stopPropagation();
      const a = document.createElement('a');
      a.href = photo.displayUrl;
      a.download = `photobook_${photo.id}.png`;
      document.body.appendChild(a);
      a.click();
      a.remove();
    };

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'action-btn delete';
    deleteBtn.innerHTML = '×';
    deleteBtn.title = 'Delete';
    deleteBtn.onclick = (e) => {
      e.stopPropagation();
      showConfirmDialog(
        'Delete Photo',
        'Are you sure you want to delete this photo?',
        async () => {
          const r = await fetch(DELETE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id: photo.id })
          });
          const j = await r.json();
          if (!j.success) { 
            if (typeof window.toast !== 'undefined') {
              window.toast.error(j.error || 'Delete failed');
            } else {
              alert(j.error || 'Delete failed');
            }
            return; 
          }
          
          // Show success toast
          if (typeof window.toast !== 'undefined') {
            window.toast.success('Photo deleted!', 2000);
          }
          await loadPhotos();
          renderGallery();
        }
      );
    };

    actions.appendChild(downloadBtn);
    actions.appendChild(deleteBtn);
    frame.appendChild(img);
    frame.appendChild(actions);
    item.appendChild(frame);

    // Double-click to open lightbox
    item.addEventListener('dblclick', (e) => {
      e.preventDefault();
      e.stopPropagation();
      showLightbox(photo.displayUrl);
    });

    return item;
  }

  function clampToGallery(item) {
    const gallery = document.getElementById('photobookGallery');
    if (!gallery || !item) return;
    const galleryRect = gallery.getBoundingClientRect();
    const itemRect = item.getBoundingClientRect();
    if (!galleryRect.width || !galleryRect.height || !itemRect.width || !itemRect.height) return;

    const widthPercent = (itemRect.width / galleryRect.width) * 100;
    const heightPercent = (itemRect.height / galleryRect.height) * 100;
    const currentLeft = parseFloat(item.style.left) || 0;
    const currentTop = parseFloat(item.style.top) || 0;

    const minX = -DRAG_BOUNDS.overlapX;
    const maxX = Math.max(minX, 100 - widthPercent + DRAG_BOUNDS.overlapX);
    const minY = -DRAG_BOUNDS.overlapY;
    const maxY = Math.max(minY, 100 - heightPercent + DRAG_BOUNDS.overlapY);

    const clampedLeft = Math.max(minX, Math.min(maxX, currentLeft));
    const clampedTop = Math.max(minY, Math.min(maxY, currentTop));

    item.style.left = clampedLeft + '%';
    item.style.top = clampedTop + '%';
  }

  // Lightbox functions
  function showLightbox(imageUrl) {
    const lightbox = document.getElementById('photoLightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    
    if (!lightbox || !lightboxImg) return;
    
    lightboxImg.src = imageUrl;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scroll
  }

  function hideLightbox() {
    const lightbox = document.getElementById('photoLightbox');
    if (!lightbox) return;
    
    lightbox.classList.remove('active');
    document.body.style.overflow = ''; // Restore scroll
  }

  // Make items draggable with smooth pointer-based drag (similar to dnd-kit)
  function makeDraggable(item, photo) {
    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let initialX = 0;
    let initialY = 0;
    let lastClickTime = 0;
    let clickCount = 0;

    // Prevent drag on double-click
    item.addEventListener('click', (e) => {
      const now = Date.now();
      if (now - lastClickTime < 300) {
        clickCount++;
      } else {
        clickCount = 1;
      }
      lastClickTime = now;

      if (clickCount === 2) {
        // Double-click detected - don't drag
        return;
      }
    });

    const startDrag = (e) => {
      // Don't drag if clicking on buttons
      if (e.target.closest('.action-btn')) return;
      
      // Check if double-click (prevent drag)
      const now = Date.now();
      if (now - lastClickTime < 300 && clickCount >= 2) {
        return;
      }

      isDragging = true;
      item.setAttribute('data-dragging', 'true');
      item.style.zIndex = 10000;
      item.style.transition = 'none'; // Disable transition during drag
      item.style.cursor = 'grabbing';

      const gallery = document.getElementById('photobookGallery');
      const galleryRect = gallery.getBoundingClientRect();
      const itemRect = item.getBoundingClientRect();

      // Get starting position
      initialX = (itemRect.left - galleryRect.left) / galleryRect.width * 100;
      initialY = (itemRect.top - galleryRect.top) / galleryRect.height * 100;

      // Get mouse/touch starting position
      startX = e.touches ? e.touches[0].clientX : e.clientX;
      startY = e.touches ? e.touches[0].clientY : e.clientY;

      e.preventDefault();
    };

    let rafId = null;
    const onDrag = (e) => {
      if (!isDragging) return;

      // Use requestAnimationFrame for smooth dragging
      if (rafId) {
        cancelAnimationFrame(rafId);
      }

      rafId = requestAnimationFrame(() => {
        const gallery = document.getElementById('photobookGallery');
        const galleryRect = gallery.getBoundingClientRect();

        const currentX = e.touches ? e.touches[0].clientX : e.clientX;
        const currentY = e.touches ? e.touches[0].clientY : e.clientY;

        // Calculate delta in pixels
        const deltaX = currentX - startX;
        const deltaY = currentY - startY;

        // Use transform for hardware acceleration (smoother)
        item.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
        
        // Calculate final position for bounds checking
        const itemRect = item.getBoundingClientRect();
        const currentLeft = (itemRect.left - galleryRect.left) / galleryRect.width * 100;
        const currentTop = (itemRect.top - galleryRect.top) / galleryRect.height * 100;

        // Constrain to gallery bounds
        const itemWidthPercent = (itemRect.width / galleryRect.width) * 100;
        const itemHeightPercent = (itemRect.height / galleryRect.height) * 100;
        
        const minX = -DRAG_BOUNDS.overlapX;
        const maxX = Math.max(minX, 100 - itemWidthPercent + DRAG_BOUNDS.overlapX);
        const minY = -DRAG_BOUNDS.overlapY;
        const maxY = Math.max(minY, 100 - itemHeightPercent + DRAG_BOUNDS.overlapY);

        let constrainedX = Math.max(minX, Math.min(maxX, currentLeft));
        let constrainedY = Math.max(minY, Math.min(maxY, currentTop));

        // If constrained, update transform and base position
        if (constrainedX !== currentLeft || constrainedY !== currentTop) {
          item.style.transform = 'none';
          item.style.left = constrainedX + '%';
          item.style.top = constrainedY + '%';
          initialX = constrainedX;
          initialY = constrainedY;
          const newItemRect = item.getBoundingClientRect();
          startX = currentX - (newItemRect.left - galleryRect.left - (initialX / 100 * galleryRect.width));
          startY = currentY - (newItemRect.top - galleryRect.top - (initialY / 100 * galleryRect.height));
        }
        
        rafId = null;
      });
    };

    const stopDrag = () => {
      if (!isDragging) return;

      // Cancel any pending animation frame
      if (rafId) {
        cancelAnimationFrame(rafId);
        rafId = null;
      }

      isDragging = false;
      item.removeAttribute('data-dragging');
      item.style.cursor = 'move';
      item.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease, z-index 0s';

      // Save final position (convert transform back to left/top)
      const gallery = document.getElementById('photobookGallery');
      const galleryRect = gallery.getBoundingClientRect();
      const itemRect = item.getBoundingClientRect();
      
      const finalX = (itemRect.left - galleryRect.left) / galleryRect.width * 100;
      const finalY = (itemRect.top - galleryRect.top) / galleryRect.height * 100;
      
      // Update base position, clamp within gallery, and clear transform
      item.style.left = finalX + '%';
      item.style.top = finalY + '%';
      clampToGallery(item);
      item.style.transform = 'none';

      // Save position to photo data
      const photoId = parseInt(item.dataset.id);
      const photo = photos.find(p => p.id === photoId);
      
      if (photo) {
        const clampedX = parseFloat(item.style.left) || finalX;
        const clampedY = parseFloat(item.style.top) || finalY;
        photo.x = clampedX;
        photo.y = clampedY;
      }

      // Clean up
      document.removeEventListener('mousemove', onDrag);
      document.removeEventListener('mouseup', stopDrag);
      document.removeEventListener('touchmove', onDrag);
      document.removeEventListener('touchend', stopDrag);
    };

    // Mouse events
    item.addEventListener('mousedown', (e) => {
      startDrag(e);
      document.addEventListener('mousemove', onDrag);
      document.addEventListener('mouseup', stopDrag);
    });

    // Touch events
    item.addEventListener('touchstart', (e) => {
      startDrag(e);
      document.addEventListener('touchmove', onDrag, { passive: false });
      document.addEventListener('touchend', stopDrag);
    }, { passive: false });
  }

  function renderGallery() {
    const gallery = document.getElementById('photobookGallery');
    gallery.innerHTML = '';

    if (photos.length === 0) {
      gallery.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">✨</div>
          <h3>No photos yet</h3>
          <p>Add photos to your photobook to get started!</p>
        </div>
      `;
      document.getElementById('photoCount').textContent = '0 photos';
      return;
    }

    photos.forEach((photo, index) => {
      const element = createPhotoElement(photo);
      makeDraggable(element, photo);
      gallery.appendChild(element);
      requestAnimationFrame(() => clampToGallery(element));
      
      // Stagger animation
      setTimeout(() => {
        element.style.opacity = '0';
        element.style.transform = 'scale(0.8)';
        element.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        
        requestAnimationFrame(() => {
          element.style.opacity = '1';
          element.style.transform = 'scale(1)'; // No rotation - straight images
        });
      }, index * 50);
    });

    document.getElementById('photoCount').textContent = `${photos.length} ${photos.length === 1 ? 'photo' : 'photos'}`;
  }

  // Handle window resize to maintain positions
  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      if (photos.length > 0) {
        // Adjust positions based on new gallery size
        renderGallery();
      }
    }, 250);
  });

  // Lightbox event listeners
  const lightbox = document.getElementById('photoLightbox');
  const lightboxClose = document.getElementById('lightboxClose');
  
  if (lightboxClose) {
    lightboxClose.addEventListener('click', (e) => {
      e.stopPropagation();
      hideLightbox();
    });
  }
  
  if (lightbox) {
    // Close when clicking background
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox) {
        hideLightbox();
      }
    });
  }
  
  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightbox && lightbox.classList.contains('active')) {
      hideLightbox();
    }
  });

  // Initialize + Gallery User Guide
  document.addEventListener('DOMContentLoaded', async () => {
    try {
      await loadPhotos();
      renderGallery();
    } catch (error) {
      console.error('Failed to load photos:', error);
      document.getElementById('photobookGallery').innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">⚠️</div>
          <h3>Error loading photos</h3>
          <p>${error.message}</p>
        </div>
      `;
    }

    // Photobook / Gallery User Guide Logic
    try {
      const guideBtn = document.getElementById('openPhotobookGuide');
      const modalEl  = document.getElementById('photobookGuideModal');
      if (!modalEl || !window.bootstrap) return;

      const guideModal = bootstrap.Modal.getOrCreateInstance(modalEl);
      const STORAGE_KEY = 'guide_photobook_v1';

      // Open when user clicks help button
      guideBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        guideModal.show();
      });

      // Auto‑show once for new users
      const alreadySeen = (typeof localStorage !== 'undefined') && localStorage.getItem(STORAGE_KEY);
      if (!alreadySeen) {
        setTimeout(() => {
          guideModal.show();
          try {
            localStorage.setItem(STORAGE_KEY, '1');
          } catch (_) {}
        }, 800);
      }
    } catch (_) {
      // Fail silently if anything goes wrong
    }
  });
})();
</script>

<?php
// Include common footer (light theme)
include __DIR__ . '/includes/page_footer.php';
?>

</body>
</html>

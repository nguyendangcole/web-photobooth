<?php
/**
 * app/frame_sidebar.php
 * - Offcanvas for selecting frames
 * - No more tab UI (layout from control block)
 * - Search bar (debounce)
 * - AJAX load by name + layout
 *
 * Requirements:
 * - control block must set: window.currentFrameLayout = 'vertical' or 'square'
 *   and dispatch event: window.dispatchEvent(new CustomEvent('frame-layout-change', { detail: { layout } }));
 */
?>
<style>
/* Frame Sidebar - Left Sidebar (Admin Style - Fixed between header and footer) */
.frame-sidebar {
  position: fixed;
  top: 50px;
  left: 0;
  bottom: 40px;
  width: 320px;
  background: #ffffff;
  border-right: 1px solid #e0e0e0;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transform: translateX(-100%);
  transition: transform 0.3s ease;
  overflow-y: auto;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
}

.frame-sidebar.open {
  transform: translateX(0);
}

/* Adjust main content when sidebar is open */
body.sidebar-open .main-content-wrapper {
  left: 320px;
  transition: left 0.3s ease;
}


.frame-sidebar-header {
  padding: 15px;
  border-bottom: 1px solid #e0e0e0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  flex-shrink: 0;
  z-index: 10;
}

.frame-sidebar-header h5 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #333;
}

.frame-sidebar-close {
  background: transparent;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s;
  line-height: 1;
}

.frame-sidebar-close:hover {
  background: #f0f0f0;
  color: #333;
}

.frame-sidebar-search {
  padding: 15px;
  border-bottom: 1px solid #e0e0e0;
  background: #ffffff;
  flex-shrink: 0;
  z-index: 9;
}

.frame-sidebar-search input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
}

.frame-sidebar-search input:focus {
  outline: none;
  border-color: #c1ff72;
  box-shadow: 0 0 0 2px rgba(193, 255, 114, 0.2);
}

.frame-sidebar-body {
  padding: 15px;
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}

.premium-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(255,107,53,0.4);
  z-index: 10;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.template {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
}

.template:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.15) !important;
}

/* Toggle Button */
.frame-sidebar-toggle {
  position: fixed;
  top: calc(50vh - 20px);
  left: 0;
  z-index: 999;
  background: #ffffff;
  border: 1px solid #e0e0e0;
  border-left: none;
  border-radius: 0 8px 8px 0;
  padding: 12px 8px;
  cursor: pointer;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.frame-sidebar-toggle:hover {
  background: #f8f8f8;
  box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
}

.frame-sidebar-toggle svg {
  width: 20px;
  height: 20px;
  transition: transform 0.3s ease;
}

body.sidebar-open .frame-sidebar-toggle {
  left: 320px;
}

body.sidebar-open .frame-sidebar-toggle svg {
  transform: rotate(180deg);
}

@media (max-width: 768px) {
  .frame-sidebar {
    width: 280px;
    top: 50px;
    bottom: 40px;
  }
  
  body.sidebar-open .main-content-wrapper {
    left: 280px;
  }
  
  body.sidebar-open .frame-sidebar-toggle {
    left: 280px;
  }
}
</style>

<!-- Toggle Button -->
<button class="frame-sidebar-toggle" id="frameSidebarToggle" aria-label="Toggle Frame Sidebar">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
  </svg>
</button>

<!-- Sidebar -->
<div class="frame-sidebar" id="frameSidebar">
  <div class="frame-sidebar-header">
    <h5 id="frameSidebarLabel">Select Frame</h5>
    <button type="button" class="frame-sidebar-close" id="frameSidebarClose" aria-label="Close">
      ×
    </button>
  </div>

  <!-- Search (sticky) -->
  <div class="frame-sidebar-search">
    <input id="frame-search" type="search" class="form-control" placeholder="Search frame by name…">
  </div>

  <!-- List -->
  <div class="frame-sidebar-body" data-animate="fade-in">
    <div id="frame-list" class="d-flex flex-wrap justify-content-center gap-3">
      <p class="text-muted">Loading frame list...</p>
    </div>
  </div>
</div>

<!-- Modal Premium Upgrade -->
<div class="modal fade" id="premiumUpgradeModal" tabindex="-1" aria-labelledby="premiumUpgradeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" id="premiumUpgradeModalLabel">
          <span style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700;">⭐ Premium Frame</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <div class="mb-3">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #ff6b35;">
            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
            <path d="M2 17l10 5 10-5"></path>
            <path d="M2 12l10 5 10-5"></path>
          </svg>
        </div>
        <h4 class="mb-3">This Frame is for Premium Users Only!</h4>
        <p class="text-muted mb-4">
          Upgrade to Premium to use all exclusive frames and many special features.
        </p>
        <div class="d-grid gap-2">
          <a href="?p=premium-upgrade" class="btn btn-warning btn-lg" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); border: none; color: white; font-weight: 600;">
            ⭐ Upgrade to Premium
          </a>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Later</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  const list = document.getElementById('frame-list');
  const searchInput = document.getElementById('frame-search');
  let debounceTimer;

  // Get current layout set by control block; default 'vertical'
  function getCurrentLayout() {
    const v = (window.currentFrameLayout || 'vertical').toLowerCase();
    return (v === 'square' || v === 'vertical') ? v : 'vertical';
  }

  // Build standard image URL when this file is in /app/
  function toImgUrl(src) {
    // If DB saves 'public/images/..' then from /app/ must add '../'
    return (src.startsWith('http') || src.startsWith('/')) ? src : ('../' + src);
  }

  // Save premium status from response
  let userIsPremium = false;

  function buildItemHTML(f) {
    const imgUrl = toImgUrl(f.src);
    const isPremium = f.is_premium == 1;
    const premiumBadge = isPremium ? '<span class="premium-badge">⭐ PREMIUM</span>' : '';
    const borderStyle = isPremium ? 'border: 2px solid #ff6b35; box-shadow: 0 4px 12px rgba(255,107,53,0.3);' : '';
    
    // If premium frame, add data attribute
    const dataAttrs = isPremium ? `data-is-premium="1"` : '';
    
    return `
      <div class="template border p-2 text-center mx-auto position-relative"
           data-layout="${f.layout}"
           data-frame-id="${f.id}"
           data-animate-item="zoom-in"
           ${dataAttrs}
           onclick="handleFrameClick('${imgUrl}','${f.layout}', ${isPremium ? 'true' : 'false'})"
           style="width: 180px; ${borderStyle}">
        ${premiumBadge}
        <img src="${imgUrl}" alt="${f.name}" class="img-fluid d-block mx-auto">
        <p class="small mb-0 mt-1">${f.name}</p>
      </div>
    `;
  }

  // Frame click handler function
  window.handleFrameClick = function(imgUrl, layout, isPremium) {
    // If premium frame and user not premium → show dialog
    if (isPremium && !userIsPremium) {
      const modal = new bootstrap.Modal(document.getElementById('premiumUpgradeModal'));
      modal.show();
      return;
    }
    
    // If not premium or user is premium → apply frame normally
    if (typeof applyTemplate === 'function') {
      applyTemplate(imgUrl, layout);
    }
  };

  function loadFrames() {
    const q = searchInput.value.trim();
    const layout = getCurrentLayout();

    list.innerHTML = '<p class="text-muted">Loading...</p>';

    // use relative path from /app/ → /ajax/
    const url = `../ajax/frames_list.php?layout=${encodeURIComponent(layout)}${q ? '&q=' + encodeURIComponent(q) : ''}`;

    fetch(url)
      .then(r => r.json())
      .then(res => {
        if (!res.success) {
          list.innerHTML = '<p class="text-danger">Error loading data.</p>';
          console.error(res.error);
          return;
        }
        const data = res.data || [];
        // Save premium status
        userIsPremium = res.user_premium || false;
        
        if (data.length === 0) {
          list.innerHTML = '<p class="text-muted">No suitable frames.</p>';
          return;
        }
        list.innerHTML = data.map(buildItemHTML).join('');
        
        // Trigger animation for newly rendered items
        setTimeout(() => {
          const newItems = list.querySelectorAll('[data-animate-item]');
          newItems.forEach((item, index) => {
            setTimeout(() => {
              item.classList.add('animate-visible');
            }, index * 50);
          });
        }, 50);
      })
      .catch(err => {
        list.innerHTML = '<p class="text-danger">Cannot connect to server.</p>';
        console.error(err);
      });
  }

  // Debounce search
  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadFrames, 300);
  });

  // Toggle sidebar
  const sidebar = document.getElementById('frameSidebar');
  const toggleBtn = document.getElementById('frameSidebarToggle');
  const closeBtn = document.getElementById('frameSidebarClose');
  
  function openSidebar() {
    sidebar.classList.add('open');
    document.body.classList.add('sidebar-open');
    searchInput.focus();
    loadFrames();
  }
  
  function closeSidebar() {
    sidebar.classList.remove('open');
    document.body.classList.remove('sidebar-open');
  }
  
  toggleBtn.addEventListener('click', () => {
    if (sidebar.classList.contains('open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });
  
  if (closeBtn) {
    closeBtn.addEventListener('click', closeSidebar);
  }
  
  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });

  // Reload when control block changes layout
  window.addEventListener('frame-layout-change', () => {
    if (sidebar.classList.contains('open')) {
      loadFrames();
    }
  });

  // Expose openSidebar globally so "Choose Frame" button can trigger it
  window.openFrameSidebar = openSidebar;
})();
</script>

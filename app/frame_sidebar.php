<?php
/**
 * app/frame_sidebar.php
 * - Offcanvas chọn khung
 * - Không còn UI tab (layout lấy từ control block)
 * - Thanh search (debounce)
 * - AJAX load theo tên + layout
 *
 * Yêu cầu:
 * - control block phải set: window.currentFrameLayout = 'vertical' hoặc 'square'
 *   và phát sự kiện: window.dispatchEvent(new CustomEvent('frame-layout-change', { detail: { layout } }));
 */
?>
<style>
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
</style>

<!-- Sidebar Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="frameSidebar" aria-labelledby="frameSidebarLabel">
  <div class="offcanvas-header">
    <h5 id="frameSidebarLabel" class="mb-0">Chọn khung ảnh</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <!-- Search only (sticky) -->
  <div class="px-3 pt-2" style="position:sticky;top:0;background:#fff;z-index:6;border-bottom:1px solid #eee">
    <div class="pb-3">
      <input id="frame-search" type="search" class="form-control" placeholder="Tìm khung theo tên…">
    </div>
  </div>

  <!-- List -->
  <div class="offcanvas-body d-flex justify-content-center">
    <div id="frame-list" class="d-flex flex-wrap justify-content-center gap-3" style="max-width:420px;">
      <p class="text-muted">Đang tải danh sách khung...</p>
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
        <h4 class="mb-3">Frame này chỉ dành cho Premium Users!</h4>
        <p class="text-muted mb-4">
          Nâng cấp lên Premium để sử dụng tất cả các frame độc quyền và nhiều tính năng đặc biệt khác.
        </p>
        <div class="d-grid gap-2">
          <a href="?p=premium-upgrade" class="btn btn-warning btn-lg" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); border: none; color: white; font-weight: 600;">
            ⭐ Nâng cấp lên Premium
          </a>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Để sau</button>
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

  // Lấy layout hiện tại do control block set; mặc định 'vertical'
  function getCurrentLayout() {
    const v = (window.currentFrameLayout || 'vertical').toLowerCase();
    return (v === 'square' || v === 'vertical') ? v : 'vertical';
  }

  // Build URL ảnh chuẩn khi file này nằm trong /app/
  function toImgUrl(src) {
    // Nếu DB lưu 'public/images/..' thì từ /app/ phải thêm '../'
    return (src.startsWith('http') || src.startsWith('/')) ? src : ('../' + src);
  }

  // Lưu premium status từ response
  let userIsPremium = false;

  function buildItemHTML(f) {
    const imgUrl = toImgUrl(f.src);
    const isPremium = f.is_premium == 1;
    const premiumBadge = isPremium ? '<span class="premium-badge">⭐ PREMIUM</span>' : '';
    const borderStyle = isPremium ? 'border: 2px solid #ff6b35; box-shadow: 0 4px 12px rgba(255,107,53,0.3);' : '';
    
    // Nếu là premium frame, thêm data attribute
    const dataAttrs = isPremium ? `data-is-premium="1"` : '';
    
    return `
      <div class="template border p-2 text-center mx-auto position-relative"
           data-layout="${f.layout}"
           data-frame-id="${f.id}"
           ${dataAttrs}
           onclick="handleFrameClick('${imgUrl}','${f.layout}', ${isPremium ? 'true' : 'false'})"
           style="width: 180px; ${borderStyle}">
        ${premiumBadge}
        <img src="${imgUrl}" alt="${f.name}" class="img-fluid d-block mx-auto">
        <p class="small mb-0 mt-1">${f.name}</p>
      </div>
    `;
  }

  // Hàm xử lý click frame
  window.handleFrameClick = function(imgUrl, layout, isPremium) {
    // Nếu là premium frame và user chưa premium → hiện dialog
    if (isPremium && !userIsPremium) {
      const modal = new bootstrap.Modal(document.getElementById('premiumUpgradeModal'));
      modal.show();
      return;
    }
    
    // Nếu không phải premium hoặc user đã premium → apply frame bình thường
    if (typeof applyTemplate === 'function') {
      applyTemplate(imgUrl, layout);
    }
  };

  function loadFrames() {
    const q = searchInput.value.trim();
    const layout = getCurrentLayout();

    list.innerHTML = '<p class="text-muted">Đang tải...</p>';

    // dùng đường dẫn tương đối từ /app/ → /ajax/
    const url = `../ajax/frames_list.php?layout=${encodeURIComponent(layout)}${q ? '&q=' + encodeURIComponent(q) : ''}`;

    fetch(url)
      .then(r => r.json())
      .then(res => {
        if (!res.success) {
          list.innerHTML = '<p class="text-danger">Lỗi khi tải dữ liệu.</p>';
          console.error(res.error);
          return;
        }
        const data = res.data || [];
        // Lưu premium status
        userIsPremium = res.user_premium || false;
        
        if (data.length === 0) {
          list.innerHTML = '<p class="text-muted">Không có khung phù hợp.</p>';
          return;
        }
        list.innerHTML = data.map(buildItemHTML).join('');
      })
      .catch(err => {
        list.innerHTML = '<p class="text-danger">Không kết nối được server.</p>';
        console.error(err);
      });
  }

  // Debounce search
  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadFrames, 300);
  });

  // Auto focus & load khi mở offcanvas
  document.getElementById('frameSidebar')
    .addEventListener('shown.bs.offcanvas', () => {
      searchInput.focus();
      loadFrames();
    });

  // Reload khi control block đổi layout
  window.addEventListener('frame-layout-change', () => {
    loadFrames();
  });

  // Load lần đầu (trường hợp sidebar đã mở sẵn)
  loadFrames();
})();
</script>

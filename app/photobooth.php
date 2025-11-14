<?php include __DIR__ . '/header.php'; ?>
<link rel="stylesheet" href="<?= asset('css/photobooth.css') ?>?v=<?= time() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">

<!-- GEN Z PHOTOBOOTH STUDIO -->
<div class="photobooth-studio">
  
  <!-- Header -->
  <div class="studio-header">
    <h1 class="studio-title">
      <span class="title-accent">◆</span> PHOTO<span class="gradient">BOOTH</span> <span class="title-accent">◆</span>
    </h1>
    <p class="studio-subtitle">Create • Capture • Express yourself</p>
  </div>

  <!-- Main Grid -->
  <div class="studio-grid">
    
    <!-- Filters Section (LEFT) -->
    <div class="filters-card">
      <div class="card-label">◆ FILTERS</div>
      
      <div class="filter-selector-wrap">
        <button class="filter-selector" id="filterSelector">
          <span class="selected-filter-icon">◉</span>
          <span class="selected-filter-name">NONE</span>
          <span class="dropdown-arrow">▼</span>
        </button>
        
        <div class="filter-dropdown" id="filterDropdown">
          <button class="filter-option active" data-filter="none" data-icon="◉" data-name="NONE">
            <span class="filter-icon">◉</span>
            <span>NONE</span>
          </button>
          
          <!-- Basic Filters -->
          <div class="filter-group-label">BASIC</div>
          <button class="filter-option" data-filter="grayscale(100%)" data-icon="○" data-name="B&W">
            <span class="filter-icon">○</span>
            <span>B&W</span>
          </button>
          <button class="filter-option" data-filter="sepia(100%)" data-icon="◐" data-name="SEPIA">
            <span class="filter-icon">◐</span>
            <span>SEPIA</span>
          </button>
          <button class="filter-option" data-filter="invert(100%)" data-icon="◑" data-name="INVERT">
            <span class="filter-icon">◑</span>
            <span>INVERT</span>
          </button>
          <button class="filter-option" data-filter="contrast(200%)" data-icon="◆" data-name="CONTRAST">
            <span class="filter-icon">◆</span>
            <span>CONTRAST</span>
          </button>
          <button class="filter-option" data-filter="brightness(150%)" data-icon="◇" data-name="BRIGHT">
            <span class="filter-icon">◇</span>
            <span>BRIGHT</span>
          </button>
          <button class="filter-option" data-filter="saturate(200%)" data-icon="✦" data-name="SATURATE">
            <span class="filter-icon">✦</span>
            <span>SATURATE</span>
          </button>
          <button class="filter-option" data-filter="blur(3px)" data-icon="◎" data-name="BLUR">
            <span class="filter-icon">◎</span>
            <span>BLUR</span>
          </button>
          <button class="filter-option" data-filter="hue-rotate(90deg)" data-icon="◉" data-name="HUE SHIFT">
            <span class="filter-icon">◉</span>
            <span>HUE SHIFT</span>
          </button>
          
          <!-- Preset Filters -->
          <div class="filter-group-label">PRESETS</div>
          <button class="filter-option" data-filter="preset1" data-icon="★" data-name="CYAN DREAM">
            <span class="filter-icon">★</span>
            <span>CYAN DREAM</span>
          </button>
          <button class="filter-option" data-filter="preset2" data-icon="✦" data-name="PINK MAGIC">
            <span class="filter-icon">✦</span>
            <span>PINK MAGIC</span>
          </button>
          <button class="filter-option" data-filter="preset3" data-icon="■" data-name="MONO BLUE">
            <span class="filter-icon">■</span>
            <span>MONO BLUE</span>
          </button>
          <button class="filter-option" data-filter="preset4" data-icon="▲" data-name="SOFT GLOW">
            <span class="filter-icon">▲</span>
            <span>SOFT GLOW</span>
          </button>
          <button class="filter-option" data-filter="preset5" data-icon="●" data-name="RED HEAT">
            <span class="filter-icon">●</span>
            <span>RED HEAT</span>
          </button>
          <button class="filter-option" data-filter="preset6" data-icon="◆" data-name="PURPLE HAZE">
            <span class="filter-icon">◆</span>
            <span>PURPLE HAZE</span>
          </button>
          <button class="filter-option" data-filter="preset7" data-icon="♦" data-name="GOLDEN HOUR">
            <span class="filter-icon">♦</span>
            <span>GOLDEN HOUR</span>
          </button>
          <button class="filter-option" data-filter="preset8" data-icon="✧" data-name="MINT FRESH">
            <span class="filter-icon">✧</span>
            <span>MINT FRESH</span>
          </button>
          <button class="filter-option" data-filter="preset9" data-icon="◈" data-name="NEON NIGHT">
            <span class="filter-icon">◈</span>
            <span>NEON NIGHT</span>
          </button>
          <button class="filter-option" data-filter="preset10" data-icon="⬢" data-name="VINTAGE">
            <span class="filter-icon">⬢</span>
            <span>VINTAGE</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Camera Section (CENTER) -->
    <div class="camera-card">
      <div class="card-label">LIVE FEED</div>
      
      <div class="camera-wrapper">
        <video id="video" autoplay playsinline></video>
        <div id="countdown"></div>
      </div>
    </div>

    <!-- Controls Section (RIGHT) -->
    <div class="controls-card">
      <div class="card-label">CONTROLS</div>
      
      <div class="control-btns">
        <button id="musicToggle" class="ctrl-btn music-btn" data-on="0">
          <span class="btn-icon music-icon"></span>
          <span id="musicText">MUSIC</span>
        </button>
        
        <div class="timer-wrap">
          <button class="ctrl-btn timer-btn" id="timerBtn">
            <span class="btn-icon">⏱</span>
            <span id="timerText">3s</span>
          </button>
          <div class="timer-dropdown">
            <button class="timer-option active" data-time="3">3s</button>
            <button class="timer-option" data-time="5">5s</button>
            <button class="timer-option" data-time="10">10s</button>
          </div>
        </div>
        
        <button id="startBtn" class="ctrl-btn start-btn">
          <span class="btn-icon">●</span>
          <span>CAPTURE</span>
        </button>
        
        <button id="exportBtn" class="ctrl-btn export-btn">
          <span class="btn-icon">↓</span>
          <span>EXPORT</span>
        </button>
      </div>
    </div>

    <!-- Gallery Section -->
    <div class="gallery-card">
      <div class="card-label">YOUR SHOTS</div>
      <div id="captured-images" class="gallery-grid"></div>
    </div>
    
  </div>
</div>

<!-- Modal xem ảnh -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-dark border-0">
      <div class="modal-body p-0 text-center">
        <img id="modalImg" class="img-fluid rounded" alt="Captured photo">
      </div>
    </div>
  </div>
</div>

<?php
// ===== Playlist: quét public/audio/*.mp3 (đặt audio1.mp3 ở đây là nhận) =====
$audioDirFs = dirname(__DIR__) . '/public/audio';
$audioWeb   = BASE_URL . 'audio/';
$tracks = [];
if (is_dir($audioDirFs)) {
  foreach (scandir($audioDirFs) as $f) {
    if ($f === '.' || $f === '..') continue;
    if (preg_match('/\.mp3$/i', $f)) {
      $tracks[] = $audioWeb . rawurlencode($f);
    }
  }
}
// Ví dụ chỉ 1 bài: $tracks = [ BASE_URL . 'audio/audio1.mp3' ];
?>
<script id="pbMusicData" type="application/json"><?= json_encode($tracks, JSON_UNESCAPED_SLASHES) ?></script>

<!-- Thẻ audio đặt cuối (không hiển thị) -->
<audio id="pbMusic" preload="auto"></audio>

<!-- Scripts chính -->
<script src="<?= asset('js/photobooth.js') ?>"></script>

<script>
// ===== Filter Dropdown Handler =====
(function(){
  const filterSelector = document.getElementById('filterSelector');
  const filterDropdown = document.getElementById('filterDropdown');
  const filterWrap = filterSelector?.closest('.filter-selector-wrap');
  const selectedIcon = filterWrap?.querySelector('.selected-filter-icon');
  const selectedName = filterWrap?.querySelector('.selected-filter-name');
  const video = document.getElementById('video');
  const cameraWrapper = document.querySelector('.camera-wrapper');

  function applyFilter(filter){
    if (!cameraWrapper || !video) return;
    // Remove all preset classes
    cameraWrapper.classList.remove('preset1','preset2','preset3','preset4','preset5','preset6','preset7','preset8','preset9','preset10');
    if (!filter || filter === 'none') {
      video.style.filter = '';
      return;
    }
    if (filter.startsWith('preset')) {
      cameraWrapper.classList.add(filter);
      video.style.filter = 'none';
    } else {
      video.style.filter = filter;
    }
    // Update currentFilter for capture function
    if (typeof currentFilter !== 'undefined') {
      currentFilter = filter;
    }
  }

  filterSelector?.addEventListener('click', (e) => {
    e.preventDefault();
    filterDropdown?.classList.toggle('active');
    filterWrap?.classList.toggle('active');
  });

  document.querySelectorAll('.filter-option').forEach(opt => {
    opt.addEventListener('click', function(){
      document.querySelectorAll('.filter-option').forEach(o => o.classList.remove('active'));
      this.classList.add('active');
      if (selectedIcon) selectedIcon.textContent = this.dataset.icon || '◉';
      if (selectedName) selectedName.textContent = this.dataset.name || 'FILTER';
      applyFilter(this.dataset.filter || 'none');
      filterDropdown?.classList.remove('active');
      filterWrap?.classList.remove('active');
    });
  });

  document.addEventListener('click', (e) => {
    if (!filterWrap) return;
    if (!filterWrap.contains(e.target)) {
      filterDropdown?.classList.remove('active');
      filterWrap?.classList.remove('active');
    }
  });
})();

// ===== Timer Dropdown =====
const timerBtn = document.getElementById('timerBtn');
const timerDropdown = document.querySelector('.timer-dropdown');
const timerText = document.getElementById('timerText');

timerBtn?.addEventListener('click', () => {
  timerDropdown.classList.toggle('active');
});

document.querySelectorAll('.timer-option').forEach(opt => {
  opt.addEventListener('click', function() {
    document.querySelectorAll('.timer-option').forEach(o => o.classList.remove('active'));
    this.classList.add('active');
    timerText.textContent = this.dataset.time + 's';
    timerDropdown.classList.remove('active');
    
    // Trigger original dropdown logic
    const event = new Event('click', { bubbles: true });
    const originalOption = document.querySelector('.timer-option[data-time="' + this.dataset.time + '"]');
    if (originalOption) originalOption.dispatchEvent(event);
  });
});

// ===== View image in modal =====
document.addEventListener('click', e => {
  const img = e.target.closest('#captured-images img');
  if (img) {
    const modalImg = document.getElementById('modalImg');
    modalImg.src = img.src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
  }
});

// ===== MUSIC PLAYER (ambience) — nút ở hàng control =====
(function(){
  const audioEl   = document.getElementById('pbMusic');
  const toggleBtn = document.getElementById('musicToggle');
  const textSpan  = document.getElementById('musicText');
  const iconSpan  = toggleBtn?.querySelector('.music-icon');
  const raw       = document.getElementById('pbMusicData')?.textContent || '[]';

  /** Parse playlist */
  let tracks = [];
  try { tracks = JSON.parse(raw) || []; } catch(_) {}

  // Ẩn nút nếu không có nhạc
  if (!tracks.length && toggleBtn) { toggleBtn.style.display = 'none'; return; }

  /** Fisher–Yates shuffle (in-place) */
  function shuffle(arr){
    for (let i = arr.length - 1; i > 0; i--){
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
  }

  /** Queue phát nhạc (đã shuffle) */
  let queue = shuffle([...tracks]);
  let qidx  = 0;                                // con trỏ trong queue
  let enabled = (localStorage.getItem('pb_music_enabled') === '1');
  let userInteracted = false;

  function setIcon(on){
    if (!iconSpan) return;
    toggleBtn.setAttribute('data-on', on ? '1' : '0');
    if (textSpan) textSpan.textContent = on ? 'Music On' : 'Music';
    iconSpan.innerHTML = on
      ? `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5L6 9H3v6h3l5 4V5z"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>`
      : `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 9L5 12H2v6h3l4 3V9z"/><path d="M22 2L2 22"/></svg>`;
  }

  function currentSrc(){
    return queue[qidx];
  }

  function loadCurrent(){
    if (!audioEl) return;
    audioEl.src = currentSrc();
  }

  async function play(){
    if (!queue.length || !audioEl) return;
    try {
      if (!audioEl.src) loadCurrent();
      await audioEl.play();
      enabled = true;
      localStorage.setItem('pb_music_enabled', '1');
      setIcon(true);
    } catch (e) {
      enabled = false; setIcon(false);
    }
  }

  function pause(){
    if (!audioEl) return;
    audioEl.pause();
    enabled = false;
    localStorage.setItem('pb_music_enabled', '0');
    setIcon(false);
  }

  function toggle(){
    userInteracted = true;
    if (enabled) pause(); else play();
  }

  /** Next: tăng qidx; khi hết list → reshuffle, nhưng tránh lặp lại bài vừa phát */
  function next(){
    if (!queue.length) return;
    qidx++;
    if (qidx >= queue.length){
      const last = queue[queue.length - 1];       // bài vừa nghe xong
      queue = shuffle([...tracks]);
      // tránh trùng ngay bài vừa rồi
      if (queue[0] === last && queue.length > 1){
        // đổi chỗ 0 với một vị trí ngẫu nhiên khác
        const j = 1 + Math.floor(Math.random() * (queue.length - 1));
        [queue[0], queue[j]] = [queue[j], queue[0]];
      }
      qidx = 0;
    }
    loadCurrent();
    if (enabled) play();
  }

  toggleBtn?.addEventListener('click', toggle);
  audioEl?.addEventListener('ended', next);

  // Tương tác đầu tiên → nếu đã set bật thì play
  window.addEventListener('click', () => {
    if (!userInteracted){ userInteracted = true; if (enabled) play(); }
  }, { once: true });

  // Khởi tạo UI & nguồn nhạc ban đầu
  setIcon(enabled);
  loadCurrent();
})();
</script>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<?php include __DIR__ . '/footer.php'; ?>

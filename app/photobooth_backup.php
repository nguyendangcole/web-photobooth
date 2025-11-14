<?php include __DIR__ . '/header.php'; ?>
<link rel="stylesheet" href="<?= asset('css/photobooth.css') ?>?v=<?= time() ?>">

<style>
/* Nút nhạc kiểu pill nằm chung hàng với Start/Export */
.music-pill{
  display:inline-flex; align-items:center; gap:8px;
  border:0; border-radius:999px; padding:8px 12px;
  background:#212529; color:#fff; cursor:pointer;
  transition:transform .12s ease, background .15s ease;
}
.music-pill:hover{ background:#0f1113; }
.music-pill:active{ transform:scale(.98); }
.music-pill .music-icon{ width:18px; height:18px; display:inline-block; }
.music-pill[data-on="1"]{ background:#198754; }        /* bật = xanh */
.music-pill[data-on="1"]:hover{ background:#157347; }
</style>

<div class="container py-5">

  <!-- ========== KHU CHỤP ẢNH ========== -->
  <section class="pb-surface capture-section text-center mb-5">

    <div class="camera-frame position-relative mx-auto mb-3">
      <video id="video" autoplay playsinline></video>
      <div id="countdown"></div>
    </div>

    <!-- Bộ lọc -->
    <div class="dropdown d-inline-block mb-3">
      <button class="btn btn-outline-dark dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
        Filter
      </button>
      <ul class="dropdown-menu" aria-labelledby="filterDropdown">
        <li><a class="dropdown-item filter-option active" href="#" data-filter="none">None</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="grayscale(100%)">Grayscale</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="sepia(100%)">Sepia</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="invert(100%)">Invert</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="contrast(200%)">High Contrast</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="brightness(150%)">Bright</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="preset1">Preset Xanh nhạt</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="preset2">Preset Hồng</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="preset3">Preset Xanh + Grayscale</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="preset4">Preset Soft Light</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="preset5">Preset Overlay Đỏ</a></li>
      </ul>
    </div>

    <!-- Nút điều khiển (có Music) -->
    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 mt-2">

      <!-- Nút nhạc nằm chung hàng -->
      <button id="musicToggle" class="music-pill" type="button" title="Music" data-on="0">
        <span class="music-icon" aria-hidden="true"></span>
        <span id="musicText">Music</span>
      </button>

      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="timerDropdown" data-bs-toggle="dropdown">
          3 seconds
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item timer-option active" href="#" data-time="3">3 seconds</a></li>
          <li><a class="dropdown-item timer-option" href="#" data-time="5">5 seconds</a></li>
          <li><a class="dropdown-item timer-option" href="#" data-time="10">10 seconds</a></li>
        </ul>
      </div>

      <button id="startBtn" class="btn btn-primary">Start</button>
      <button id="exportBtn" class="btn btn-success">Export Frame</button>
    </div>
  </section>

  <!-- ========== KHU GALLERY ẢNH ========== -->
  <section class="pb-surface gallery-section">
    <h3 class="text-center fw-bold mb-4">Your Gallery</h3>
    <div id="captured-images" class="row g-4 justify-content-center"></div>
  </section>

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
<script src="<?= asset('js/filter.js') ?>"></script>

<script>
// ===== Xem ảnh lớn trong modal =====
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

<?php include __DIR__ . '/footer.php'; ?>

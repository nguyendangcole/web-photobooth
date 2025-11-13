<?php include 'header.php'; ?>

<?php
$imgCamera  = BASE_URL . 'images/camera.png';
$imgFrame   = BASE_URL . 'images/frame.png';
$imgBook    = BASE_URL . 'images/gallery.png'; // ← THÊM: ảnh dẫn tới trang Photobook

$bgDesktop  = BASE_URL . 'images/55.png';
$bgMobile   = BASE_URL . 'images/56.png';
?>

<style>
:root { --hover-accent: #c1ff72; }

html, body { height: 100%; }
body{
  background-color: #f7f9fc;
  background-image: url('<?= htmlspecialchars($bgDesktop) ?>');
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
  background-attachment: fixed;
}

@media (max-width: 786px){
  html, body { height: auto !important; min-height: 100svh; }
  html{
    background: url('<?= htmlspecialchars($bgMobile) ?>') center top / cover no-repeat !important;
    background-attachment: scroll !important;
  }
  body{ background: transparent !important; }
  body::after{ display:none !important; }
}

body::after{
  content:"";
  position: fixed; inset: 0; pointer-events: none;
  background:
    radial-gradient(60% 40% at 50% 55%, rgba(193,255,114,.12), transparent 60%),
    radial-gradient(120% 90% at 50% 40%, rgba(0,0,0,.06), rgba(0,0,0,.14));
  mix-blend-mode: soft-light;
  z-index: 0;
}

/* Hero container + motion */
.page-hero{
  position: relative; z-index: 1;
  display:flex; flex-wrap:wrap; justify-content:center; align-items:center;
  gap:clamp(18px,6vw,64px); padding:clamp(16px,6vw,72px) 0;
  perspective: 1200px;
  opacity: 0; transform: translateY(30px);
  transition: opacity .6s ease, transform .6s ease;
}
.page-hero.show{
  opacity: 1; transform: translateY(0);
}

.link-img{ display:inline-block; line-height:0; text-decoration:none; }
.link-img img{
  display:block; width:min(92vw, 440px); height:auto; user-select:none; pointer-events:none;
  transform: translateZ(0);
  transition: transform .35s cubic-bezier(.2,.8,.2,1), filter .35s ease;
  animation: floaty 4s ease-in-out infinite;
  filter: drop-shadow(0 8px 18px rgba(0,0,0,.18));
}
.link-img:hover img{
  animation: none;
  transform: translateY(-16px) scale(1.08) rotateX(3deg) rotateY(-2deg) rotate(-.4deg);
  filter:
    drop-shadow(0 22px 50px rgba(0,0,0,.30))
    drop-shadow(0 0 32px rgba(193,255,114,.75));
}
.link-img:active img{
  transform: translateY(-8px) scale(1.03);
  filter:
    drop-shadow(0 16px 34px rgba(0,0,0,.26))
    drop-shadow(0 0 24px rgba(193,255,114,.65));
}
@keyframes floaty{ 0%{transform:translateY(0)} 50%{transform:translateY(-4px)} 100%{transform:translateY(0)} }
@media (prefers-reduced-motion: reduce){
  .link-img img{ animation:none; transition:none; }
}

/* Popup mini */
.modal.modal-mini .modal-dialog{ max-width:320px; }
.modal.modal-mini .modal-content{ border-radius:16px; }
.modal.modal-mini.fade .modal-dialog{
  transform: translateY(10px) scale(.96);
  transition: transform .18s ease, opacity .18s ease;
}
.modal.modal-mini.show .modal-dialog{ transform: translateY(0) scale(1); }
</style>

<!-- Popup thông báo -->
<div class="modal fade modal-mini" id="goModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body py-4 text-center">
        <div class="fw-bold mb-1" id="popMessage">Đang mở…</div>
        <div class="text-muted small">Vui lòng chờ một chút</div>
      </div>
    </div>
  </div>
</div>

<div class="page-hero" id="hero">
  <!-- Photobooth -->
  <a class="link-img nav-pop"
     href="<?= BASE_URL ?>?p=photobooth"
     data-label="Đang mở Photobooth…">
    <img src="<?= htmlspecialchars($imgCamera) ?>" alt="Photobooth">
  </a>

  <!-- Frame composer -->
  <a class="link-img nav-pop"
     href="<?= BASE_URL ?>?p=frame"
     data-label="Đang mở trang Frame…">
    <img src="<?= htmlspecialchars($imgFrame) ?>" alt="Upload Your Photos">
  </a>

  <!-- Photobook (MỚI) -->
  <a class="link-img nav-pop"
     href="<?= BASE_URL ?>?p=photobook"
     data-label="Đang mở Photobook…">
    <img src="<?= htmlspecialchars($imgBook) ?>" alt="Photobook">
  </a>
</div>

<script>
(function(){
  // Motion khi load trang
  window.addEventListener('DOMContentLoaded', ()=>{
    document.getElementById('hero').classList.add('show');
  });

  // Popup chuyển trang
  const links = document.querySelectorAll('.nav-pop');
  const modalEl = document.getElementById('goModal');
  const msgEl   = document.getElementById('popMessage');
  const modal   = (window.bootstrap && modalEl) ? new bootstrap.Modal(modalEl) : null;

  function goWithPopup(e){
    e.preventDefault();
    const href  = this.getAttribute('href');
    const label = this.dataset.label || 'Đang mở…';
    if (msgEl) msgEl.textContent = label;
    if (modal) modal.show();
    setTimeout(()=>{ window.location.href = href; }, 450);
  }

  links.forEach(a=>{
    a.addEventListener('click', goWithPopup);
    a.addEventListener('keydown', (ev)=>{
      if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); goWithPopup.call(a, ev); }
    });
  });
})();
</script>

<?php include 'footer.php'; ?>

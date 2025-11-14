<?php
// app/main_menu.php
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Studio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
  /* Compact header - nhỏ gọn nhưng đầy đủ */
  .main-nav {
    padding: 6px 0 !important;
    background: #0a0a0a !important;
    border-bottom: 1px solid #c1ff72 !important;
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
    color: #ffffff !important;
    font-weight: 600;
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
    color: #ffffff !important;
    text-decoration: none;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s;
  }
  .nav-link:hover {
    color: #c1ff72 !important;
    background: rgba(193, 255, 114, 0.1);
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
    background: #ffffff !important;
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
      background: #0a0a0a !important;
      border-top: 1px solid #c1ff72 !important;
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
  
  /* Compact footer - nhỏ gọn nhưng đầy đủ */
  .footer {
    background: #0a0a0a;
    color: #ffffff;
    padding: 6px 15px;
    border-top: 1px solid #0a0a0a;
    margin-top: auto;
  }
  .footer-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }
  .footer-links {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .footer-links a {
    color: #ffffff;
    text-decoration: none;
    font-size: 9px;
    font-weight: 500;
    opacity: 0.8;
    transition: opacity 0.2s, color 0.2s;
  }
  .footer-links a:hover {
    opacity: 1;
    color: #c1ff72;
  }
  .footer-copyright {
    color: rgba(255, 255, 255, 0.6);
    font-size: 8px;
    margin: 0;
  }
  .footer-copyright strong {
    color: #c1ff72;
  }
  @media (max-width: 768px) {
    .footer-content {
      flex-direction: column;
      text-align: center;
      gap: 6px;
    }
    .footer-links {
      justify-content: center;
      gap: 0.75rem;
    }
    .footer {
      padding: 8px 15px;
    }
  }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
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
      <?php $u = current_user(); ?>
      <?php if ($u): ?>
        <div class="dropdown">
          <button class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false"
                  title="<?= htmlspecialchars($u['name'] ?? 'User') ?>">
            <?php
            // Luôn đảm bảo có avatar URL (Gravatar nếu chưa có)
            $avatarUrl = $u['avatar_url'] ?? null;
            if (empty($avatarUrl) && !empty($u['email'])) {
              $emailHash = md5(strtolower(trim($u['email'])));
              $avatarUrl = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
            }
            
            if (!empty($avatarUrl)):
            ?>
              <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" class="nav-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <span class="nav-avatar nav-avatar-fallback" style="display:none;">
                <?= strtoupper(substr($u['name'] ?: $u['email'], 0, 1)) ?>
              </span>
            <?php else: ?>
              <span class="nav-avatar nav-avatar-fallback">
                <?= strtoupper(substr($u['name'] ?: $u['email'], 0, 1)) ?>
              </span>
            <?php endif; ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2 small text-muted">
              <div class="fw-semibold"><?= htmlspecialchars($u['name']) ?></div>
              <div><?= htmlspecialchars($u['email']) ?></div>
              <?php
              // Kiểm tra premium status
              if (!empty($u['id'])) {
                try {
                  $stmt = db()->prepare("SELECT is_premium, premium_until FROM users WHERE id = ?");
                  $stmt->execute([$u['id']]);
                  $premiumInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                  if ($premiumInfo && $premiumInfo['is_premium']) {
                    $premiumUntil = $premiumInfo['premium_until'];
                    $isActive = true;
                    if ($premiumUntil) {
                      $expiry = new DateTime($premiumUntil);
                      $now = new DateTime();
                      $isActive = $now <= $expiry;
                    }
                    if ($isActive) {
                      echo '<div class="mt-2"><span style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: 700;">⭐ PREMIUM</span></div>';
                      if ($premiumUntil) {
                        echo '<div style="font-size: 10px; margin-top: 4px;">Hết hạn: ' . date('d/m/Y', strtotime($premiumUntil)) . '</div>';
                      }
                    }
                  }
                } catch (Exception $e) {
                  // Silent fail
                }
              }
              ?>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=change-avatar"><i class="bi bi-person-circle me-2"></i> Change Avatar</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=logout">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Guest -->
        <div class="dropdown">
          <button class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false" title="Đăng nhập">
            <span class="nav-avatar nav-avatar-guest">?</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="?p=login">Đăng nhập</a></li>
            <li><a class="dropdown-item" href="?p=register">Đăng ký</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=oauth-google">Login with Google</a></li>
            <li><a class="dropdown-item" href="?p=oauth-facebook">Login with Facebook</a></li>
          </ul>
        </div>
      <?php endif; ?>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<?php
$imgCamera  = BASE_URL . 'images/camera.png';
$imgFrame   = BASE_URL . 'images/frame.png';
$imgBook    = BASE_URL . 'images/gallery.png'; // ← THÊM: ảnh dẫn tới trang Photobook

$bgDesktop  = BASE_URL . 'images/55.png';
$bgMobile   = BASE_URL . 'images/56.png';
?>

<style>
:root { --hover-accent: #c1ff72; }

html, body { 
  height: 100%; 
  display: flex;
  flex-direction: column;
}
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
  display:flex; flex-wrap:nowrap; justify-content:center; align-items:center;
  gap:clamp(12px,3vw,32px); padding:clamp(16px,4vw,48px) 20px;
  perspective: 1200px;
  opacity: 0; transform: translateY(30px);
  transition: opacity .6s ease, transform .6s ease;
  max-width: 1200px;
  margin: 0 auto;
}
.page-hero.show{
  opacity: 1; transform: translateY(0);
}

.link-img{ 
  display:inline-block; 
  line-height:0; 
  text-decoration:none;
  flex: 1;
  max-width: 380px;
}
.link-img img{
  display:block; width:100%; max-width:380px; height:auto; user-select:none; pointer-events:none;
  transform: translateZ(0);
  transition: transform .35s cubic-bezier(.2,.8,.2,1), filter .35s ease;
  animation: floaty 4s ease-in-out infinite;
  filter: drop-shadow(0 8px 18px rgba(0,0,0,.18));
}
.link-img:last-child{
  max-width: 320px;
}
.link-img:last-child img{
  max-width: 320px;
}
@media (max-width: 768px){
  .page-hero{ flex-wrap:wrap; gap:clamp(12px,4vw,24px); }
  .link-img{ max-width: 280px; }
  .link-img img{ max-width: 280px; }
  .link-img:last-child{ max-width: 240px; }
  .link-img:last-child img{ max-width: 240px; }
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

<div class="page-hero" id="hero" style="margin-top: 80px; flex: 1;">
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

// Mobile menu toggle
document.getElementById('menuToggle')?.addEventListener('click', function() {
  document.querySelector('.nav-menu').classList.toggle('active');
  this.classList.toggle('active');
});
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

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
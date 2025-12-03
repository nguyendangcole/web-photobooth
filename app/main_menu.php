<?php
// app/main_menu.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo_helper.php';

$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';

// SEO data
$seoData = default_seo_data('studio');
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
  /* Page-specific styles */
<?php
$imgCamera  = BASE_URL . 'images/camera.png';
$imgFrame   = BASE_URL . 'images/frame.png';
$imgBook    = BASE_URL . 'images/gallery.png'; // ← THÊM: ảnh dẫn tới trang Photobook

$bgDesktop  = BASE_URL . 'images/menu-bg-desktop.png';
$bgMobile   = BASE_URL . 'images/menu-bg-mobile.png';
?>
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
</head>
<body>

<?php
// Include common header (dark theme, studio page active)
$theme = 'dark';
$activePage = 'studio';

// Custom dropdown for studio page (with premium status)
// Use $user from config.php
if ($user) {
  $customDropdownContent = '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">';
  $customDropdownContent .= '<li class="px-3 py-2 small text-muted">';
  $customDropdownContent .= '<div class="fw-semibold">' . htmlspecialchars($user['name']) . '</div>';
  $customDropdownContent .= '<div>' . htmlspecialchars($user['email']) . '</div>';
  
  // Premium status check
  $isAdmin = false;
  if (!empty($user['id'])) {
    try {
      $stmt = db()->prepare("SELECT is_premium, premium_until, is_admin FROM users WHERE id = ?");
      $stmt->execute([$user['id']]);
      $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($userInfo) {
        $isAdmin = !empty($userInfo['is_admin']);
        
        if ($userInfo['is_premium']) {
          $premiumUntil = $userInfo['premium_until'];
          $isActive = true;
          if ($premiumUntil) {
            $expiry = new DateTime($premiumUntil);
            $now = new DateTime();
            $isActive = $now <= $expiry;
          }
          if ($isActive) {
            $customDropdownContent .= '<div class="mt-2"><span style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: 700;">⭐ PREMIUM</span></div>';
            if ($premiumUntil) {
              $customDropdownContent .= '<div style="font-size: 10px; margin-top: 4px;">Hết hạn: ' . date('d/m/Y', strtotime($premiumUntil)) . '</div>';
            }
          }
        }
      }
    } catch (Exception $e) {
      // Silent fail
    }
  }
  
  $customDropdownContent .= '</li>';
  $customDropdownContent .= '<li><hr class="dropdown-divider"></li>';
  
  // Admin Dashboard link (only for admins)
  if ($isAdmin) {
    $customDropdownContent .= '<li><a class="dropdown-item" href="../admin/index.php"><i class="bi bi-shield-check me-2"></i> Admin Dashboard</a></li>';
    $customDropdownContent .= '<li><hr class="dropdown-divider"></li>';
  }
  
  $customDropdownContent .= '<li><a class="dropdown-item" href="?p=change-avatar"><i class="bi bi-person-circle me-2"></i> Change Avatar</a></li>';
  $customDropdownContent .= '<li><hr class="dropdown-divider"></li>';
  $customDropdownContent .= '<li><a class="dropdown-item" href="?p=logout">Logout</a></li>';
  $customDropdownContent .= '</ul>';
} else {
  // Custom guest dropdown for studio
  $customGuestDropdown = '<div class="dropdown">';
  $customGuestDropdown .= '<button class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false" title="Login">';
  $customGuestDropdown .= '<span class="nav-avatar nav-avatar-guest">?</span>';
  $customGuestDropdown .= '</button>';
  $customGuestDropdown .= '<ul class="dropdown-menu dropdown-menu-end">';
  $customGuestDropdown .= '<li><a class="dropdown-item" href="?p=login">Login</a></li>';
  $customGuestDropdown .= '<li><a class="dropdown-item" href="?p=register">Register</a></li>';
  $customGuestDropdown .= '<li><hr class="dropdown-divider"></li>';
  $customGuestDropdown .= '<li><a class="dropdown-item" href="?p=oauth-google">Login with Google</a></li>';
  $customGuestDropdown .= '<li><a class="dropdown-item" href="?p=oauth-facebook">Login with Facebook</a></li>';
  $customGuestDropdown .= '</ul>';
  $customGuestDropdown .= '</div>';
}

include __DIR__ . '/includes/page_header.php';
?>

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

<div class="page-hero" id="hero" style="margin-top: 80px; flex: 1;" data-animate="fade-up">
  <!-- Photobooth -->
  <a class="link-img nav-pop" data-animate-item="zoom-in" data-animate-on-load
     href="<?= BASE_URL ?>?p=photobooth"
     data-label="Đang mở Photobooth…">
    <img src="<?= htmlspecialchars($imgCamera) ?>" alt="Photobooth" loading="lazy">
  </a>

  <!-- Frame composer -->
  <a class="link-img nav-pop" data-animate-item="zoom-in" data-animate-on-load
     href="<?= BASE_URL ?>?p=frame"
     data-label="Đang mở trang Frame…">
    <img src="<?= htmlspecialchars($imgFrame) ?>" alt="Upload Your Photos" loading="lazy">
  </a>

  <!-- Photobook (MỚI) -->
  <a class="link-img nav-pop" data-animate-item="zoom-in" data-animate-on-load
     href="<?= BASE_URL ?>?p=photobook"
     data-label="Đang mở Photobook…">
    <img src="<?= htmlspecialchars($imgBook) ?>" alt="Photobook" loading="lazy">
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

<?php
// Include common footer (dark theme)
include __DIR__ . '/includes/page_footer.php';
?>

</body>
</html>
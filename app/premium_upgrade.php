<?php
// app/premium_upgrade.php
// Trang yêu cầu nâng cấp Premium

$GUARD_PAGE = 'premium-upgrade';
require __DIR__ . '/includes/auth_guard.php';

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
  <title>SPACE PHOTOBOOTH • Premium Upgrade</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
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
      <a href="?p=info" class="nav-link">INFO</a>
      <a href="?p=service" class="nav-link">SERVICE</a>
      <a href="?p=qa" class="nav-link">Q&A</a>
      <a href="?p=contact" class="nav-link">CONTACT</a>
      <?php if ($isLoggedIn): ?>
        <a href="?p=studio" class="nav-btn">STUDIO</a>
      <?php else: ?>
        <a href="?p=login" class="nav-btn">LOGIN</a>
      <?php endif; ?>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<?php
// Kiểm tra xem user đã là premium chưa
$isPremium = false;
$premiumUntil = null;
if (!empty($_SESSION['user']['id'])) {
  $stmt = db()->prepare("SELECT is_premium, premium_until FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user']['id']]);
  $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if ($userInfo && $userInfo['is_premium']) {
    $isPremium = true;
    $premiumUntil = $userInfo['premium_until'];
    
    // Kiểm tra còn hạn không
    if ($premiumUntil) {
      $expiryDate = new DateTime($premiumUntil);
      $now = new DateTime();
      if ($now > $expiryDate) {
        $isPremium = false; // hết hạn
      }
    }
  }
}

// Kiểm tra xem đã có request pending chưa
$hasPendingRequest = false;
if (!empty($_SESSION['user']['id'])) {
  $stmt = db()->prepare("SELECT id FROM premium_requests WHERE user_id = ? AND status = 'pending' LIMIT 1");
  $stmt->execute([$_SESSION['user']['id']]);
  $hasPendingRequest = $stmt->fetch() !== false;
}
?>

<style>
.premium-gradient {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.premium-card {
  border: 2px solid #ff6b35;
  border-radius: 20px;
  background: linear-gradient(135deg, rgba(255,107,53,0.05) 0%, rgba(247,147,30,0.05) 100%);
}
.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 10px;
  background: white;
  margin-bottom: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.feature-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
  font-family: 'DM Mono', monospace;
}

.premium-section {
  background: var(--white);
  color: var(--black);
  min-height: 100vh;
}

.premium-content {
  max-width: 900px;
  margin: 0 auto;
}
</style>

<!-- Premium Section -->
<section class="premium-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="premium-content" style="max-width: 900px; margin: 0 auto;">
      
      <?php if ($isPremium): ?>
        <!-- User đã là Premium -->
        <div class="card premium-card text-center py-5">
          <div class="card-body">
            <div class="mb-4">
              <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #ff6b35;">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <h2 class="premium-gradient mb-3">Bạn đã là Premium User!</h2>
            <?php if ($premiumUntil): ?>
              <p class="text-muted mb-4">
                Premium của bạn có hiệu lực đến: <strong><?= date('d/m/Y H:i', strtotime($premiumUntil)) ?></strong>
              </p>
            <?php else: ?>
              <p class="text-muted mb-4">Premium của bạn không giới hạn thời gian!</p>
            <?php endif; ?>
            <a href="?p=frame" class="btn btn-primary btn-lg">Sử dụng Premium Frames ngay</a>
          </div>
        </div>
      <?php elseif ($hasPendingRequest): ?>
        <!-- Đã có request pending -->
        <div class="card border-warning text-center py-5">
          <div class="card-body">
            <div class="mb-4">
              <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #ffc107;">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <h2 class="mb-3">Yêu cầu của bạn đang được xử lý</h2>
            <p class="text-muted mb-4">
              Chúng tôi đã nhận được yêu cầu nâng cấp Premium của bạn. 
              Admin sẽ xem xét và phê duyệt trong thời gian sớm nhất.
            </p>
            <p class="text-muted">
              Bạn sẽ nhận được thông báo khi yêu cầu được phê duyệt.
            </p>
            <a href="?p=studio" class="btn btn-outline-primary mt-3">Về Studio</a>
          </div>
        </div>
      <?php else: ?>
        <!-- Form request Premium -->
        <div class="card premium-card">
          <div class="card-body p-5">
            <div class="text-center mb-5">
              <h1 class="premium-gradient mb-3">Nâng cấp lên Premium</h1>
              <p class="lead text-muted">Mở khóa tất cả tính năng độc quyền</p>
            </div>

            <!-- Features -->
            <div class="mb-5">
              <h4 class="mb-4">Tính năng Premium bao gồm:</h4>
              
              <div class="feature-item">
                <div class="feature-icon">FR</div>
                <div>
                  <strong>Premium Frames độc quyền</strong>
                  <p class="mb-0 text-muted small">Truy cập vào tất cả các frame premium đẹp mắt</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">SP</div>
                <div>
                  <strong>Ưu tiên hỗ trợ</strong>
                  <p class="mb-0 text-muted small">Được hỗ trợ ưu tiên từ đội ngũ chăm sóc khách hàng</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">NEW</div>
                <div>
                  <strong>Tính năng mới sớm nhất</strong>
                  <p class="mb-0 text-muted small">Trải nghiệm các tính năng mới trước mọi người</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">∞</div>
                <div>
                  <strong>Không giới hạn</strong>
                  <p class="mb-0 text-muted small">Sử dụng không giới hạn tất cả tính năng premium</p>
                </div>
              </div>
            </div>

            <!-- Request Form -->
            <div class="text-center">
              <form id="premiumRequestForm">
                <input type="hidden" name="action" value="request_premium">
                <button type="submit" class="btn btn-warning btn-lg px-5 py-3" 
                        style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); border: none; color: white; font-weight: 600; font-size: 1.2rem;">
                  Gửi yêu cầu nâng cấp Premium
                </button>
              </form>
              <p class="text-muted mt-3 small">
                Admin sẽ xem xét và phê duyệt yêu cầu của bạn trong thời gian sớm nhất.
              </p>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center py-5">
        <div class="mb-4">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #28a745;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <h4 class="mb-3">Yêu cầu đã được gửi thành công!</h4>
        <p class="text-muted mb-4">
          Chúng tôi đã nhận được yêu cầu nâng cấp Premium của bạn. 
          Admin sẽ xem xét và phê duyệt trong thời gian sớm nhất.
        </p>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='?p=studio'">
          Về trang chủ
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('premiumRequestForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const btn = e.target.querySelector('button[type="submit"]');
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = 'Đang gửi...';
  
  try {
    const res = await fetch('../ajax/premium_request.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'request_premium' })
    });
    
    const json = await res.json();
    
    if (json.success) {
      const modal = new bootstrap.Modal(document.getElementById('successModal'));
      modal.show();
    } else {
      alert(json.error || 'Có lỗi xảy ra. Vui lòng thử lại.');
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  } catch (err) {
    console.error(err);
    alert('Không thể kết nối server. Vui lòng thử lại.');
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
});
</script>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <h3 class="footer-logo">FUTUREFRAME</h3>
        <p>Next-gen photobooth for the digital generation.</p>
        <div class="footer-social">
          <a href="#" class="social">TW</a>
          <a href="#" class="social">IG</a>
          <a href="#" class="social">TT</a>
          <a href="#" class="social">YT</a>
        </div>
      </div>
      <div class="footer-links">
        <h4>PRODUCT</h4>
        <a href="?p=studio">Studio</a>
        <a href="?p=photobook">Photobook</a>
        <a href="?p=premium-upgrade">Premium</a>
      </div>
      <div class="footer-links">
        <h4>COMPANY</h4>
        <a href="?p=info">About</a>
        <a href="?p=service">Services</a>
        <a href="?p=contact">Contact</a>
      </div>
      <div class="footer-links">
        <h4>SUPPORT</h4>
        <a href="?p=qa">Help</a>
        <a href="?p=contact">Contact</a>
        <a href="?p=terms">Terms</a>
        <a href="?p=privacy">Privacy</a>
      </div>
      <div class="footer-newsletter">
        <h4>STAY UPDATED</h4>
        <p>Get latest frames & features</p>
        <form class="newsletter">
          <input type="email" placeholder="Your email">
          <button type="submit">→</button>
        </form>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> FutureFrame. All rights reserved.</p>
      <p>Designed with ✨ for the future</p>
    </div>
  </div>
</footer>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
// Mobile menu toggle
document.getElementById('menuToggle')?.addEventListener('click', function() {
  document.querySelector('.nav-menu').classList.toggle('active');
  this.classList.toggle('active');
});
</script>

</body>
</html>


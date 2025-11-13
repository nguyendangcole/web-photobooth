<?php
// app/premium_upgrade.php
// Trang yêu cầu nâng cấp Premium

$GUARD_PAGE = 'premium-upgrade';
require __DIR__ . '/includes/auth_guard.php';

require __DIR__ . '/header.php';

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
  font-size: 20px;
  flex-shrink: 0;
}
</style>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
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
            <h2 class="premium-gradient mb-3">Bạn đã là Premium User! ⭐</h2>
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
            <h2 class="mb-3">Yêu cầu của bạn đang được xử lý ⏳</h2>
            <p class="text-muted mb-4">
              Chúng tôi đã nhận được yêu cầu nâng cấp Premium của bạn. 
              Admin sẽ xem xét và phê duyệt trong thời gian sớm nhất.
            </p>
            <p class="text-muted">
              Bạn sẽ nhận được thông báo khi yêu cầu được phê duyệt.
            </p>
            <a href="?p=home" class="btn btn-outline-primary mt-3">Về trang chủ</a>
          </div>
        </div>
      <?php else: ?>
        <!-- Form request Premium -->
        <div class="card premium-card">
          <div class="card-body p-5">
            <div class="text-center mb-5">
              <h1 class="premium-gradient mb-3">⭐ Nâng cấp lên Premium</h1>
              <p class="lead text-muted">Mở khóa tất cả tính năng độc quyền</p>
            </div>

            <!-- Features -->
            <div class="mb-5">
              <h4 class="mb-4">Tính năng Premium bao gồm:</h4>
              
              <div class="feature-item">
                <div class="feature-icon">🖼️</div>
                <div>
                  <strong>Premium Frames độc quyền</strong>
                  <p class="mb-0 text-muted small">Truy cập vào tất cả các frame premium đẹp mắt</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">✨</div>
                <div>
                  <strong>Ưu tiên hỗ trợ</strong>
                  <p class="mb-0 text-muted small">Được hỗ trợ ưu tiên từ đội ngũ chăm sóc khách hàng</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">🚀</div>
                <div>
                  <strong>Tính năng mới sớm nhất</strong>
                  <p class="mb-0 text-muted small">Trải nghiệm các tính năng mới trước mọi người</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">🎨</div>
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
                  ⭐ Gửi yêu cầu nâng cấp Premium
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
</div>

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
        <h4 class="mb-3">Yêu cầu đã được gửi thành công! ✅</h4>
        <p class="text-muted mb-4">
          Chúng tôi đã nhận được yêu cầu nâng cấp Premium của bạn. 
          Admin sẽ xem xét và phê duyệt trong thời gian sớm nhất.
        </p>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='?p=home'">
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

<?php require __DIR__ . '/footer.php'; ?>


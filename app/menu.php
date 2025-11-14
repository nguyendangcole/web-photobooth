<!-- app/menu.php -->
<nav class="navbar px-3" style="background:#141414;">
  <!-- Trái: dropdown Menu -->
  <div class="dropdown">
    <button class="btn btn-menu dropdown-toggle no-caret"
            type="button" id="menuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      Menu
    </button>
    <ul class="dropdown-menu" aria-labelledby="menuDropdown">
      <li><a class="dropdown-item" href="index.php">🏠 Về trang chủ</a></li>
      <li><a class="dropdown-item" href="?p=studio">Studio</a></li>
      <li><a class="dropdown-item" href="?p=landing">★ Landing</a></li>
      <li><a class="dropdown-item" href="?p=photobooth">Photobooth</a></li>
      <li><a class="dropdown-item" href="?p=frame">Frame</a></li>
      <li><a class="dropdown-item" href="?p=photobook">Gallery</a></li>
    </ul>
  </div>

  <!-- Phải: AVATAR -->
  <div class="ms-auto d-flex align-items-center gap-2">
    <?php $u = current_user(); ?>
    <?php if ($u): ?>
      <div class="dropdown">
        <button class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false"
                title="<?= htmlspecialchars($u['name'] ?? 'User') ?>">
          <?php if (!empty($u['avatar_url'])): ?>
            <img src="<?= htmlspecialchars($u['avatar_url']) ?>" alt="avatar" class="nav-avatar">
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
</nav>

<?php
// app/includes/page_header.php
// Common header for frame, photobooth, photobook, and studio pages
// Parameters: $theme = 'light' or 'dark', $activePage = 'studio'|'photobooth'|'gallery'|'frame'

if (!isset($theme)) $theme = 'light';
if (!isset($activePage)) $activePage = '';

// Ensure these variables exist
if (!isset($isLoggedIn)) $isLoggedIn = false;
if (!isset($user)) $user = null;
if (!isset($userName)) $userName = 'User';
if (!isset($uid)) $uid = 0;

// Map active page to nav link text
$activeMap = [
  'studio' => 'STUDIO',
  'photobooth' => 'PHOTOBOOTH',
  'photobook' => 'GALLERY',
  'gallery' => 'GALLERY',
  'frame' => 'FRAME'
];
$activeNavText = $activeMap[$activePage] ?? '';
?>

<style>
/* Compact header - <?= $theme === 'dark' ? 'Dark' : 'Light' ?> theme */
.main-nav {
  padding: 6px 0 !important;
  background: <?= $theme === 'dark' ? '#0a0a0a' : '#ffffff' ?> !important;
  border-bottom: 1px solid <?= $theme === 'dark' ? '#c1ff72' : '#e0e0e0' ?> !important;
  <?= $theme === 'light' ? 'box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);' : '' ?>
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
  color: <?= $theme === 'dark' ? '#ffffff' : '#0a0a0a' ?> !important;
  font-weight: <?= $theme === 'dark' ? '600' : '700' ?>;
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
  font-weight: 700 !important;
  color: <?= $theme === 'dark' ? '#ffffff' : '#333333' ?> !important;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s;
  <?= $theme === 'light' ? 'text-transform: uppercase;' : '' ?>
}
.nav-link:hover {
  color: #c1ff72 !important;
  background: rgba(193, 255, 114, <?= $theme === 'dark' ? '0.1' : '0.15' ?>);
}
.nav-link.active {
  border-bottom: 2px solid #c1ff72 !important;
  padding-bottom: 2px;
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
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}
.menu-toggle span {
  width: 20px;
  height: 2px;
  background: <?= $theme === 'dark' ? '#ffffff' : '#333333' ?>;
  transition: all 0.3s;
}
.menu-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}
.menu-toggle.active span:nth-child(2) {
  opacity: 0;
}
.nav-menu {
  display: flex;
}
@media (max-width: 768px) {
  .menu-toggle {
    display: flex;
  }
  .nav-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: <?= $theme === 'dark' ? '#0a0a0a' : '#ffffff' ?>;
    flex-direction: column;
    padding: 15px;
    border-top: 1px solid <?= $theme === 'dark' ? '#c1ff72' : '#e0e0e0' ?>;
    <?= $theme === 'light' ? 'box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);' : '' ?>
    z-index: 1000;
  }
  .nav-menu.active {
    display: flex;
  }
}
</style>

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
      <a href="?p=studio" class="nav-link <?= $activeNavText === 'STUDIO' ? 'active' : '' ?>">STUDIO</a>
      <a href="?p=photobook" class="nav-link <?= $activeNavText === 'GALLERY' ? 'active' : '' ?>">GALLERY</a>
      <a href="?p=photobooth" class="nav-link <?= $activeNavText === 'PHOTOBOOTH' ? 'active' : '' ?>">PHOTOBOOTH</a>
      <a href="?p=frame" class="nav-link <?= $activeNavText === 'FRAME' ? 'active' : '' ?>">FRAME</a>
    </div>
    <div class="nav-user">
      <?php if ($isLoggedIn): ?>
        <div class="dropdown">
          <button class="btn btn-link p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php
            // Always ensure avatar URL (Gravatar if not available)
            $avatarUrl = $user['avatar_url'] ?? null;
            if (empty($avatarUrl) && !empty($user['email'])) {
              $emailHash = md5(strtolower(trim($user['email'])));
              $avatarUrl = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
            }
            
            if (!empty($avatarUrl)):
            ?>
              <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="nav-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="nav-avatar-fallback" style="display:none;"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            <?php else: ?>
              <div class="nav-avatar-fallback"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            <?php endif; ?>
          </button>
          <?php if (isset($customDropdownContent) && $customDropdownContent): ?>
            <?= $customDropdownContent ?>
          <?php else: ?>
            <?php
            // Check if user is admin for default dropdown
            $isAdmin = false;
            if (!empty($user['id'])) {
              try {
                $stmt = db()->prepare("SELECT is_admin FROM users WHERE id = ?");
                $stmt->execute([$user['id']]);
                $adminCheck = $stmt->fetch(PDO::FETCH_ASSOC);
                $isAdmin = !empty($adminCheck['is_admin']);
              } catch (Exception $e) {
                // Silent fail
              }
            }
            ?>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="?p=studio">Studio</a></li>
              <li><hr class="dropdown-divider"></li>
              <?php if ($isAdmin): ?>
                <li><a class="dropdown-item" href="../admin/index.php"><i class="bi bi-shield-check me-2"></i> Admin Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="?p=change-avatar">Change Avatar</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="?p=logout">Logout</a></li>
            </ul>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <?php if (isset($customGuestDropdown) && $customGuestDropdown): ?>
          <?= $customGuestDropdown ?>
        <?php else: ?>
          <a href="?p=login" class="nav-avatar-guest">?</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<script>
// Mobile Menu Toggle
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.querySelector('.nav-menu');

if (menuToggle && navMenu) {
  menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
  });
}
</script>


<?php
// app/change_avatar.php
require_once __DIR__ . '/config.php';

$GUARD_PAGE = 'change-avatar';
require __DIR__ . '/includes/auth_guard.php';

$user = current_user();
if (!$user) {
  redirect('?p=login');
}

// Ensure avatar_url exists (Gravatar if not available)
if (empty($user['avatar_url']) && !empty($user['email'])) {
  $emailHash = md5(strtolower(trim($user['email'])));
  $user['avatar_url'] = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
}

// Default avatars - you can add avatars to public/images/avatars/ folder
// File names: avatar-default-1.png, avatar-default-2.png, avatar-default-3.png, ...
$avatarsDir = ROOT_PATH . '/public/images/avatars/';
$defaultAvatars = [];

// Check if avatars folder exists
if (is_dir($avatarsDir)) {
  // Get all avatar files
  $files = glob($avatarsDir . 'avatar-*.{png,jpg,jpeg,gif,webp}', GLOB_BRACE);
  sort($files);
  foreach ($files as $file) {
    $filename = basename($file);
    $defaultAvatars[] = BASE_URL . 'images/avatars/' . $filename;
  }
}

// If no avatars yet, create placeholder list for you to add later
if (empty($defaultAvatars)) {
  // Create 12 placeholder avatars with gradient colors
  $colors = [
    ['#667eea', '#764ba2'], // Purple
    ['#f093fb', '#f5576c'], // Pink
    ['#4facfe', '#00f2fe'], // Blue
    ['#43e97b', '#38f9d7'], // Green
    ['#fa709a', '#fee140'], // Pink Yellow
    ['#30cfd0', '#330867'], // Cyan Purple
    ['#a8edea', '#fed6e3'], // Light
    ['#ff9a9e', '#fecfef'], // Rose
    ['#ffecd2', '#fcb69f'], // Orange
    ['#ff8a80', '#ea6100'], // Red Orange
    ['#c1ff72', '#00ff88'], // Lime
    ['#667eea', '#f093fb'], // Purple Pink
  ];
  
  // Create data URLs for placeholder (or you can use emoji/icon)
  foreach ($colors as $index => $color) {
    $defaultAvatars[] = 'data:image/svg+xml;base64,' . base64_encode(
      '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
        <defs>
          <linearGradient id="grad' . $index . '" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:' . $color[0] . ';stop-opacity:1" />
            <stop offset="100%" style="stop-color:' . $color[1] . ';stop-opacity:1" />
          </linearGradient>
        </defs>
        <circle cx="100" cy="100" r="100" fill="url(#grad' . $index . ')"/>
        <text x="100" y="120" font-size="80" font-weight="bold" text-anchor="middle" fill="white">' . ($index + 1) . '</text>
      </svg>'
    );
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/S.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Change Avatar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
  <style>
  /* Dark compact header - same as main_menu.php */
  .main-nav {
    padding: 12px 0 !important;
    background: #0a0a0a !important;
    border-bottom: 2px solid #c1ff72 !important;
  }
  .nav-wrapper {
    padding: 0 20px !important;
  }
  .logo {
    font-size: 16px !important;
  }
  .logo-icon {
    font-size: 20px !important;
    color: #c1ff72 !important;
  }
  .logo-text {
    color: #ffffff !important;
  }
  .logo-badge {
    font-size: 9px !important;
    padding: 1px 5px !important;
    background: #c1ff72 !important;
    color: #0a0a0a !important;
  }
  .nav-link {
    font-size: 13px !important;
    color: #ffffff !important;
  }
  .nav-link:hover {
    color: #c1ff72 !important;
  }
  .nav-user {
    display: flex;
    align-items: center;
    margin-left: 20px;
  }
  .nav-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #c1ff72;
  }
  .nav-avatar-fallback,
  .nav-avatar-guest {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #c1ff72;
    color: #0a0a0a;
    font-weight: 700;
    font-size: 16px;
    border: 2px solid #c1ff72;
    text-decoration: none;
  }
  .nav-avatar-guest {
    background: #999;
    color: #ffffff;
  }
  .menu-toggle span {
    background: #ffffff !important;
  }
  @media (max-width: 768px) {
    .nav-user {
      margin-left: 0;
      margin-top: 10px;
    }
    .nav-menu {
      background: #0a0a0a !important;
      border-top: 2px solid #c1ff72 !important;
    }
  }
  
  /* Page Content */
  .avatar-page {
    min-height: calc(100vh - 200px);
    padding: 40px 20px;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
  }
  
  .avatar-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    border: 3px solid #000;
  }
  
  .page-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
    text-align: center;
  }
  
  .page-subtitle {
    font-family: 'DM Mono', monospace;
    color: #666;
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .current-avatar-section {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e0e0e0;
  }
  
  .current-avatar-label {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  
  .current-avatar-display {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto;
    border: 4px solid #000;
    overflow: hidden;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 700;
    color: #000;
  }
  
  .current-avatar-display img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .avatars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 20px;
    margin-bottom: 2rem;
  }
  
  .avatar-item {
    position: relative;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid transparent;
    aspect-ratio: 1;
  }
  
  .avatar-item:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    border-color: #c1ff72;
  }
  
  .avatar-item.selected {
    border-color: #000;
    box-shadow: 0 0 0 4px #c1ff72;
  }
  
  .avatar-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }
  
  .avatar-item .avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: 700;
  }
  
  .save-btn {
    width: 100%;
    padding: 15px;
    font-size: 1.1rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: 3px solid #000;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Space Grotesk', sans-serif;
  }
  
  .save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
  }
  
  .save-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
  }
  
  .alert {
    margin-top: 1rem;
    border-radius: 12px;
    border: 2px solid;
  }
  
  @media (max-width: 768px) {
    .avatar-container {
      padding: 20px;
    }
    .page-title {
      font-size: 2rem;
    }
    .avatars-grid {
      grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
      gap: 15px;
    }
  }
  </style>
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
      <?php if ($user): ?>
        <div class="dropdown">
          <button class="btn btn-link p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (!empty($user['avatar_url'])): ?>
              <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="nav-avatar">
            <?php else: ?>
              <div class="nav-avatar-fallback"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
            <?php endif; ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="?p=studio">Studio</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=change-avatar">Change Avatar</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?p=logout">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="?p=login" class="nav-avatar-guest">?</a>
      <?php endif; ?>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<!-- Main Content -->
<section class="avatar-page">
  <div class="avatar-container">
    <h1 class="page-title">Change Avatar</h1>
    <p class="page-subtitle">Choose your favorite avatar</p>
    
    <!-- Current Avatar -->
    <div class="current-avatar-section">
      <div class="current-avatar-label">Current Avatar</div>
      <div class="current-avatar-display" id="currentAvatar">
        <?php
        // Always ensure avatar URL (Gravatar if not available)
        $displayAvatarUrl = $user['avatar_url'] ?? null;
        if (empty($displayAvatarUrl) && !empty($user['email'])) {
          $emailHash = md5(strtolower(trim($user['email'])));
          $displayAvatarUrl = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
        }
        
        if (!empty($displayAvatarUrl)):
        ?>
          <img src="<?= htmlspecialchars($displayAvatarUrl) ?>" alt="Current Avatar" onerror="this.style.display='none'; this.parentElement.innerHTML='<?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>'">
        <?php else: ?>
          <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Avatar Grid -->
    <div class="avatars-grid" id="avatarsGrid">
      <?php foreach ($defaultAvatars as $index => $avatarUrl): ?>
        <div class="avatar-item" data-avatar="<?= htmlspecialchars($avatarUrl) ?>">
          <?php if (strpos($avatarUrl, 'data:image') === 0): ?>
            <!-- Data URL (SVG placeholder) -->
            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar <?= $index + 1 ?>">
          <?php else: ?>
            <!-- Regular URL -->
            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar <?= $index + 1 ?>" onerror="this.parentElement.innerHTML='<div class=\'avatar-placeholder\'>' + (<?= $index + 1 ?>) + '</div>'">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    
    <!-- Save Button -->
    <button class="save-btn" id="saveBtn" disabled>Save Avatar</button>
    
    <!-- Alert -->
    <div id="alertContainer"></div>
  </div>
</section>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
let selectedAvatar = null;

// Avatar selection
document.querySelectorAll('.avatar-item').forEach(item => {
  item.addEventListener('click', function() {
    // Remove previous selection
    document.querySelectorAll('.avatar-item').forEach(i => i.classList.remove('selected'));
    
    // Add selection to clicked item
    this.classList.add('selected');
    selectedAvatar = this.dataset.avatar;
    
    // Update current avatar preview
    const currentAvatarEl = document.getElementById('currentAvatar');
    currentAvatarEl.innerHTML = `<img src="${selectedAvatar}" alt="Selected Avatar">`;
    
    // Enable save button
    document.getElementById('saveBtn').disabled = false;
  });
});

// Save avatar
document.getElementById('saveBtn').addEventListener('click', async function() {
  if (!selectedAvatar) return;
  
  this.disabled = true;
  this.textContent = 'Saving...';
  
  try {
    const response = await fetch('../ajax/update_avatar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ avatar_url: selectedAvatar })
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert('Avatar updated successfully!', 'success');
      setTimeout(() => {
        window.location.href = '?p=studio';
      }, 1500);
    } else {
      showAlert(result.error || 'Failed to update avatar', 'danger');
      this.disabled = false;
      this.textContent = 'Save Avatar';
    }
  } catch (error) {
    showAlert('An error occurred. Please try again.', 'danger');
    this.disabled = false;
    this.textContent = 'Save Avatar';
  }
});

function showAlert(message, type) {
  const container = document.getElementById('alertContainer');
  container.innerHTML = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
}

// Mobile menu toggle
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.querySelector('.nav-menu');
if (menuToggle) {
  menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
  });
}
</script>

</body>
</html>


<?php
// app/photobooth.php
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
$uid = $isLoggedIn ? (int)$user['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Photobooth</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="<?= asset('css/photobooth.css') ?>?v=<?= time() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
  <style>
  /* Compact header - Light theme - nhỏ gọn nhưng đầy đủ */
  .main-nav {
    padding: 6px 0 !important;
    background: #ffffff !important;
    border-bottom: 1px solid #e0e0e0 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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
    color: #0a0a0a !important;
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
    color: #333333 !important;
    text-decoration: none;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s;
  }
  .nav-link:hover {
    color: #c1ff72 !important;
    background: rgba(193, 255, 114, 0.15);
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
    background: #333333 !important;
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
      background: #ffffff !important;
      border-top: 1px solid #e0e0e0 !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
  
  /* Compact footer - Light theme - nhỏ gọn nhưng đầy đủ */
  .footer {
    background: #ffffff;
    color: #333333;
    padding: 6px 15px;
    border-top: 1px solid #e0e0e0;
    margin-top: auto;
    box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
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
    color: #666666;
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
    color: #999999;
    font-size: 8px;
    margin: 0;
  }
  .footer-copyright strong {
    color: #333333;
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
  
  /* Adjust page content for compact header */
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  
  /* Redesign Controls Card - Better spacing and layout */
  .controls-card {
    background: linear-gradient(135deg, #fff4d4 0%, #fff9e8 100%) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 16px !important;
    width: 260px !important;
    min-height: auto !important;
    max-height: none !important;
    padding: 20px !important;
    align-self: center !important;
    flex-shrink: 0 !important;
    overflow: visible !important;
  }
  
  .control-btns {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    width: 100% !important;
  }
  
  .ctrl-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    padding: 16px 20px !important;
    background: var(--white) !important;
    border: 2px solid var(--black) !important;
    border-radius: 12px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    font-family: 'Space Grotesk', sans-serif !important;
    text-align: center !important;
    width: 100% !important;
    min-height: 50px !important;
    position: relative !important;
    z-index: 1 !important;
  }
  
  .ctrl-btn:disabled {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
  }
  
  .ctrl-btn:hover:not(:disabled) {
    transform: translateY(-2px) !important;
    box-shadow: 4px 4px 0 var(--black) !important;
  }
  
  .ctrl-btn:active:not(:disabled) {
    transform: translateY(0) !important;
    box-shadow: 2px 2px 0 var(--black) !important;
  }
  
  /* Timer wrapper - ensure dropdown doesn't overlap */
  .timer-wrap {
    position: relative !important;
    width: 100% !important;
    z-index: 10 !important;
  }
  
  /* Timer button dropdown arrow */
  .timer-btn .dropdown-arrow {
    font-size: 10px !important;
    margin-left: auto !important;
    transition: transform 0.3s ease !important;
    display: inline-block !important;
  }
  
  .timer-wrap.active .timer-btn .dropdown-arrow {
    transform: rotate(180deg) !important;
  }
  
  .timer-dropdown {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    margin-top: 8px !important;
    background: var(--white) !important;
    border: 2px solid var(--black) !important;
    border-radius: 12px !important;
    padding: 8px !important;
    display: none !important;
    flex-direction: column !important;
    gap: 4px !important;
    z-index: 100 !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
  }
  
  .timer-dropdown.active {
    display: flex !important;
  }
  
  .timer-option {
    padding: 12px 16px !important;
    background: transparent !important;
    border: none !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    text-align: center !important;
    transition: all 0.2s ease !important;
  }
  
  .timer-option:hover {
    background: rgba(193, 255, 114, 0.2) !important;
  }
  
  .timer-option.active {
    background: #c1ff72 !important;
    color: var(--black) !important;
  }
  
  /* Music button */
  .music-btn {
    background: #ffd4e9 !important;
    order: 1 !important;
  }
  
  /* Timer button */
  .timer-btn {
    background: #d4ffd4 !important;
    order: 2 !important;
    justify-content: space-between !important;
  }
  
  .timer-btn .btn-icon {
    flex-shrink: 0 !important;
  }
  
  .timer-btn #timerText {
    flex: 1 !important;
    text-align: left !important;
  }
  
  /* Start/Capture button */
  .start-btn {
    background: var(--black) !important;
    color: var(--white) !important;
    order: 3 !important;
    font-size: 14px !important;
    padding: 18px 20px !important;
  }
  
  /* Export button */
  .export-btn {
    background: #f0f0f0 !important;
    order: 4 !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
  }
  
  .selection-count {
    background: var(--black) !important;
    color: var(--white) !important;
    border-radius: 50% !important;
    width: 24px !important;
    height: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    flex-shrink: 0 !important;
  }
  
  /* Ensure buttons don't overlap */
  .control-btns > * {
    margin: 0 !important;
    flex-shrink: 0 !important;
  }
  
  /* Camera Card - Resizable only (no drag) */
  .camera-card {
    position: relative !important;
    cursor: default !important;
    user-select: none !important;
  }
  
  .camera-wrapper {
    position: relative !important;
  }
  
  /* Resize handles */
  .resize-handle {
    position: absolute !important;
    background: rgba(193, 255, 114, 0.8) !important;
    border: 2px solid var(--black) !important;
    z-index: 101 !important;
    opacity: 0 !important;
    transition: opacity 0.2s ease !important;
    pointer-events: auto !important;
  }
  
  .camera-card:hover .resize-handle,
  .camera-card.resizing .resize-handle {
    opacity: 1 !important;
  }
  
  .resize-handle-nw {
    top: -6px !important;
    left: -6px !important;
    width: 16px !important;
    height: 16px !important;
    border-radius: 50% !important;
    cursor: nwse-resize !important;
  }
  
  .resize-handle-ne {
    top: -6px !important;
    right: -6px !important;
    width: 16px !important;
    height: 16px !important;
    border-radius: 50% !important;
    cursor: nesw-resize !important;
  }
  
  .resize-handle-sw {
    bottom: -6px !important;
    left: -6px !important;
    width: 16px !important;
    height: 16px !important;
    border-radius: 50% !important;
    cursor: nesw-resize !important;
  }
  
  .resize-handle-se {
    bottom: -6px !important;
    right: -6px !important;
    width: 16px !important;
    height: 16px !important;
    border-radius: 50% !important;
    cursor: nwse-resize !important;
  }
  
  .camera-card.resizing {
    cursor: nwse-resize !important;
  }
  
  /* ===== MOBILE RESPONSIVE ===== */
  @media (max-width: 768px) {
    /* Photobooth studio container */
    .photobooth-studio {
      padding: 80px 12px 20px !important;
      min-height: auto !important;
      overflow: visible !important;
    }
    
    /* Studio grid - stack vertically on mobile */
    .studio-grid {
      display: flex !important;
      flex-direction: column !important;
      gap: 16px !important;
      align-items: stretch !important;
      max-width: 100% !important;
      width: 100% !important;
      position: relative !important;
      z-index: 1 !important;
    }
    
    /* Camera card - full width, smaller height */
    .camera-card {
      width: 100% !important;
      order: 1 !important;
      position: relative !important;
      z-index: 1 !important;
      margin-bottom: 0 !important;
    }
    
    .camera-wrapper {
      max-width: 100% !important;
      max-height: 60vh !important;
      aspect-ratio: 4/3 !important;
      position: relative !important;
      z-index: 1 !important;
    }
    
    /* Controls card - full width, comes before filters on mobile */
    .controls-card {
      width: 100% !important;
      order: 2 !important;
      padding: 16px !important;
      max-height: none !important;
      position: relative !important;
      z-index: 2 !important;
      margin-bottom: 0 !important;
      overflow: visible !important;
    }
    
    /* Timer dropdown - ensure it's above controls but below filter dropdown */
    .timer-wrap {
      position: relative !important;
      z-index: 10 !important;
    }
    
    .timer-dropdown {
      position: absolute !important;
      top: 100% !important;
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      margin-top: 8px !important;
      z-index: 100 !important;
    }
    
    /* Filters card - full width, at the bottom on mobile */
    .filters-card {
      width: 100% !important;
      order: 3 !important;
      padding: 16px !important;
      max-height: none !important;
      overflow: visible !important;
      position: relative !important;
      z-index: 3 !important;
      margin-bottom: 0 !important;
    }
    
    .filter-selector {
      padding: 12px 14px !important;
      font-size: 12px !important;
      position: relative !important;
      z-index: 1 !important;
    }
    
    /* Filter dropdown - bottom sheet style on mobile */
    .filter-selector-wrap {
      position: relative !important;
      z-index: 1 !important;
      overflow: visible !important;
    }
    
    .filter-dropdown {
      position: fixed !important;
      top: auto !important;
      bottom: 0 !important;
      left: 0 !important;
      right: 0 !important;
      width: 100vw !important;
      max-width: 100vw !important;
      max-height: 70vh !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      border-radius: 24px 24px 0 0 !important;
      border-left: none !important;
      border-right: none !important;
      border-bottom: none !important;
      border-top: 3px solid var(--black) !important;
      margin: 0 !important;
      padding: 20px 16px !important;
      padding-bottom: calc(20px + env(safe-area-inset-bottom)) !important;
      box-shadow: 0 -8px 32px rgba(0,0,0,0.3) !important;
      z-index: 99999 !important;
      transform: translateY(100%) !important;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
      will-change: transform !important;
      display: flex !important;
      flex-direction: column !important;
      background: var(--white) !important;
    }
    
    .filter-dropdown.active {
      transform: translateY(0) !important;
      display: flex !important;
    }
    
    /* Overlay backdrop when dropdown is open */
    .filter-selector-wrap.active::after {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.6);
      z-index: 99998;
      display: block;
      opacity: 0;
      animation: fadeIn 0.3s ease forwards;
      pointer-events: auto;
    }
    
    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }
    
    /* Prevent body scroll when dropdown is open */
    body.filter-dropdown-open {
      overflow: hidden !important;
      position: fixed !important;
      width: 100% !important;
      height: 100% !important;
    }
    
    /* Ensure filter options are visible */
    .filter-option {
      position: relative !important;
      z-index: 1 !important;
      opacity: 1 !important;
      visibility: visible !important;
    }
    
    .control-btns {
      gap: 10px !important;
      position: relative !important;
      z-index: 1 !important;
    }
    
    .ctrl-btn {
      padding: 14px 16px !important;
      font-size: 12px !important;
      min-height: 48px !important;
      position: relative !important;
      z-index: 1 !important;
    }
    
    .start-btn {
      padding: 16px 18px !important;
      font-size: 13px !important;
    }
    
    /* Countdown - larger on mobile */
    #countdown {
      font-size: 5rem !important;
    }
    
    /* Card labels - slightly smaller */
    .card-label {
      font-size: 9px !important;
      padding: 5px 12px !important;
      margin-bottom: 12px !important;
    }
    
    /* Gallery card - if visible */
    .gallery-card {
      order: 4 !important;
      position: relative !important;
      bottom: auto !important;
      width: 100% !important;
      max-height: 200px !important;
    }
    
    /* Floating gallery - adjust for mobile */
    .floating-gallery {
      z-index: 5 !important;
    }
    
    .floating-photo {
      width: 150px !important;
      height: 150px !important;
    }
  }
  
  /* Small mobile devices */
  @media (max-width: 480px) {
    .photobooth-studio {
      padding: 70px 10px 15px !important;
    }
    
    .studio-grid {
      gap: 12px !important;
    }
    
    .camera-wrapper {
      max-height: 50vh !important;
      border-radius: 12px !important;
      border-width: 2px !important;
    }
    
    .filters-card,
    .controls-card {
      padding: 12px !important;
      border-radius: 12px !important;
    }
    
    .ctrl-btn {
      padding: 12px 14px !important;
      font-size: 11px !important;
      min-height: 44px !important;
    }
    
    .start-btn {
      padding: 14px 16px !important;
      font-size: 12px !important;
    }
    
    #countdown {
      font-size: 4rem !important;
    }
    
    .card-label {
      font-size: 8px !important;
      padding: 4px 10px !important;
    }
    
    .filter-selector {
      padding: 10px 12px !important;
      font-size: 11px !important;
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
      <?php if ($isLoggedIn): ?>
        <div class="dropdown">
          <button class="btn btn-link p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php
            // Luôn đảm bảo có avatar URL (Gravatar nếu chưa có)
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

<!-- GEN Z PHOTOBOOTH STUDIO -->
<div class="photobooth-studio">
  
  

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
    <div class="camera-card" id="cameraCard">
      <div class="camera-wrapper" id="cameraWrapper">
        <video id="video" autoplay playsinline></video>
        <div id="countdown"></div>
        <!-- Resize handles -->
        <div class="resize-handle resize-handle-nw"></div>
        <div class="resize-handle resize-handle-ne"></div>
        <div class="resize-handle resize-handle-sw"></div>
        <div class="resize-handle resize-handle-se"></div>
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
            <span class="dropdown-arrow">▼</span>
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
        
        <button id="exportBtn" class="ctrl-btn export-btn" disabled>
          <span style="display: flex; align-items: center; gap: 8px;">
            <span class="btn-icon">↓</span>
            <span>EXPORT</span>
          </span>
          <span class="selection-count" id="selectionCount">0</span>
        </button>
      </div>
    </div>

    <!-- Gallery Section (Hidden) -->
    <div class="gallery-card">
      <div class="card-label">YOUR SHOTS</div>
      <div id="captured-images" class="gallery-grid"></div>
    </div>
    
  </div>
</div>

<!-- Floating Gallery Container -->
<div class="floating-gallery" id="floatingGallery"></div>

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

<!-- Export Confirmation Modal -->
<div class="modal fade" id="exportConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border: 3px solid #000; border-radius: 16px;">
      <div class="modal-header" style="border-bottom: 2px solid #000; background: linear-gradient(135deg, #c1ff72 0%, #00f5ff 100%);">
        <h5 class="modal-title" style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: #000;">
          ⚠️ Incomplete Selection
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="font-family: 'Space Grotesk', sans-serif; padding: 2rem;">
        <p id="exportMessage" style="font-size: 1.1rem; color: #333; margin-bottom: 1.5rem;"></p>
        <p style="font-size: 0.95rem; color: #666;">Do you want to continue anyway?</p>
      </div>
      <div class="modal-footer" style="border-top: 2px solid #000; gap: 10px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                style="font-family: 'Space Grotesk', sans-serif; font-weight: 600; border: 2px solid #000; border-radius: 8px; padding: 10px 20px;">
          Cancel
        </button>
        <button type="button" class="btn btn-primary" id="confirmExportBtn"
                style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; background: #c1ff72; color: #000; border: 2px solid #000; border-radius: 8px; padding: 10px 20px;">
          Continue Anyway
        </button>
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
<script src="<?= asset('js/photobooth.js') ?>?v=<?= time() ?>"></script>

<script>
// ===== Filter Dropdown Handler =====
(function(){
  // Initialize global currentFilter if not set
  if (typeof window.currentFilter === 'undefined') {
    window.currentFilter = 'none';
  }
  
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
      window.currentFilter = 'none';
      return;
    }
    if (filter.startsWith('preset')) {
      cameraWrapper.classList.add(filter);
      video.style.filter = 'none';
    } else {
      video.style.filter = filter;
    }
    // Update currentFilter globally for capture function
    window.currentFilter = filter;
    console.log("Filter applied:", filter, "window.currentFilter:", window.currentFilter); // Debug
  }

  let isClosing = false;

  function closeFilterDropdown() {
    if (isClosing) return;
    isClosing = true;
    filterDropdown?.classList.remove('active');
    filterWrap?.classList.remove('active');
    document.body.classList.remove('filter-dropdown-open');
    setTimeout(() => { isClosing = false; }, 100);
  }

  function openFilterDropdown() {
    if (isClosing) return;
    filterDropdown?.classList.add('active');
    filterWrap?.classList.add('active');
    // Prevent body scroll on mobile when dropdown is open
    if (window.innerWidth <= 768) {
      document.body.classList.add('filter-dropdown-open');
    }
  }

  filterSelector?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    const isActive = filterDropdown?.classList.contains('active');
    if (isActive) {
      closeFilterDropdown();
    } else {
      openFilterDropdown();
    }
  });

  document.querySelectorAll('.filter-option').forEach(opt => {
    opt.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      const selectedFilter = this.dataset.filter || 'none';
      console.log("Filter option clicked:", selectedFilter); // Debug
      document.querySelectorAll('.filter-option').forEach(o => o.classList.remove('active'));
      this.classList.add('active');
      if (selectedIcon) selectedIcon.textContent = this.dataset.icon || '◉';
      if (selectedName) selectedName.textContent = this.dataset.name || 'FILTER';
      applyFilter(selectedFilter);
      closeFilterDropdown();
      // Double check that filter was set
      console.log("After applyFilter, window.currentFilter:", window.currentFilter); // Debug
    });
  });

  // Close dropdown when clicking outside (including overlay)
  // Use capture phase to handle before other click handlers
  document.addEventListener('click', (e) => {
    if (!filterWrap || !filterDropdown || isClosing) return;
    const isDropdownActive = filterDropdown.classList.contains('active');
    if (!isDropdownActive) return;
    
    // Check if click is outside filter dropdown
    const clickedInsideDropdown = filterDropdown.contains(e.target);
    const clickedOnSelector = filterSelector?.contains(e.target);
    
    // Close if clicked outside dropdown and not on selector
    if (!clickedInsideDropdown && !clickedOnSelector) {
      e.stopPropagation();
      closeFilterDropdown();
    }
  }, true);

  // Close on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && filterDropdown?.classList.contains('active')) {
      closeFilterDropdown();
    }
  });
  
  // Check if there's an active filter option on page load (after DOM is ready)
  setTimeout(() => {
    const activeFilterOption = document.querySelector('.filter-option.active');
    if (activeFilterOption) {
      const defaultFilter = activeFilterOption.dataset.filter || 'none';
      window.currentFilter = defaultFilter;
      applyFilter(defaultFilter);
    }
  }, 100);
})();

// ===== Timer Dropdown =====
const timerBtn = document.getElementById('timerBtn');
const timerDropdown = document.querySelector('.timer-dropdown');
const timerText = document.getElementById('timerText');
const timerWrap = timerBtn?.closest('.timer-wrap');

timerBtn?.addEventListener('click', (e) => {
  e.preventDefault();
  e.stopPropagation();
  timerDropdown.classList.toggle('active');
  timerWrap?.classList.toggle('active');
});

document.querySelectorAll('.timer-option').forEach(opt => {
  opt.addEventListener('click', function(e) {
    e.stopPropagation();
    document.querySelectorAll('.timer-option').forEach(o => o.classList.remove('active'));
    this.classList.add('active');
    timerText.textContent = this.dataset.time + 's';
    timerDropdown.classList.remove('active');
    timerWrap?.classList.remove('active');
    
    // Trigger original dropdown logic
    const event = new Event('click', { bubbles: true });
    const originalOption = document.querySelector('.timer-option[data-time="' + this.dataset.time + '"]');
    if (originalOption) originalOption.dispatchEvent(event);
  });
});

// Close timer dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (!timerWrap) return;
  if (!timerWrap.contains(e.target)) {
    timerDropdown?.classList.remove('active');
    timerWrap?.classList.remove('active');
  }
});

// ===== Camera Resize System (no drag) =====
(function() {
  const cameraCard = document.getElementById('cameraCard');
  const cameraWrapper = document.getElementById('cameraWrapper');
  if (!cameraCard || !cameraWrapper) return;
  
  let isResizing = false;
  let resizeHandle = null;
  let startX = 0, startY = 0;
  let startWidth = 0, startHeight = 0;
  
  // Load saved size
  const savedState = localStorage.getItem('cameraCardSize');
  if (savedState) {
    try {
      const state = JSON.parse(savedState);
      if (state.width) cameraWrapper.style.width = state.width;
      if (state.height) cameraWrapper.style.height = state.height;
      if (state.maxWidth) cameraWrapper.style.maxWidth = state.maxWidth;
      if (state.maxHeight) cameraWrapper.style.maxHeight = state.maxHeight;
    } catch(e) {}
  }
  
  // Save size to localStorage
  function saveState() {
    const state = {
      width: cameraWrapper.style.width || '',
      height: cameraWrapper.style.height || '',
      maxWidth: cameraWrapper.style.maxWidth || '',
      maxHeight: cameraWrapper.style.maxHeight || ''
    };
    localStorage.setItem('cameraCardSize', JSON.stringify(state));
  }
  
  // Resize functionality
  const resizeHandles = cameraWrapper.querySelectorAll('.resize-handle');
  
  function startResize(e, handle) {
    isResizing = true;
    resizeHandle = handle;
    cameraCard.classList.add('resizing');
    
    startX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
    startY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
    
    const rect = cameraWrapper.getBoundingClientRect();
    startWidth = rect.width;
    startHeight = rect.height;
    
    e.preventDefault();
    e.stopPropagation();
  }
  
  function doResize(e) {
    if (!isResizing || !resizeHandle) return;
    const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
    
    const deltaX = clientX - startX;
    const deltaY = clientY - startY;
    
    let newWidth = startWidth;
    let newHeight = startHeight;
    
    // Calculate new size based on handle (resize from center)
    // Use SE handle as primary, others adjust proportionally
    if (resizeHandle.classList.contains('resize-handle-se')) {
      newWidth = startWidth + deltaX;
      newHeight = startHeight + deltaY;
    } else if (resizeHandle.classList.contains('resize-handle-sw')) {
      newWidth = startWidth - deltaX;
      newHeight = startHeight + deltaY;
    } else if (resizeHandle.classList.contains('resize-handle-ne')) {
      newWidth = startWidth + deltaX;
      newHeight = startHeight - deltaY;
    } else if (resizeHandle.classList.contains('resize-handle-nw')) {
      newWidth = startWidth - deltaX;
      newHeight = startHeight - deltaY;
    }
    
    // Maintain aspect ratio
    const aspectRatio = startWidth / startHeight;
    newHeight = newWidth / aspectRatio;
    
    // Min and max constraints
    const minSize = 200;
    const maxSize = Math.min(window.innerWidth - 40, window.innerHeight - 200);
    
    newWidth = Math.max(minSize, Math.min(newWidth, maxSize));
    newHeight = Math.max(minSize, Math.min(newHeight, maxSize));
    
    cameraWrapper.style.width = newWidth + 'px';
    cameraWrapper.style.height = newHeight + 'px';
    cameraWrapper.style.maxWidth = 'none';
    cameraWrapper.style.maxHeight = 'none';
    
    e.preventDefault();
  }
  
  function endResize() {
    if (isResizing) {
      isResizing = false;
      resizeHandle = null;
      cameraCard.classList.remove('resizing');
      saveState();
    }
  }
  
  // Event listeners for resize
  resizeHandles.forEach(handle => {
    handle.addEventListener('mousedown', (e) => startResize(e, handle));
    handle.addEventListener('touchstart', (e) => startResize(e, handle), { passive: false });
  });
  
  // Global event listeners
  document.addEventListener('mousemove', doResize);
  document.addEventListener('touchmove', doResize, { passive: false });
  document.addEventListener('mouseup', endResize);
  document.addEventListener('touchend', endResize);
  
  // Double-click on camera wrapper to reset size
  cameraWrapper.addEventListener('dblclick', (e) => {
    if (e.target === cameraWrapper || e.target.closest('.resize-handle')) return;
    cameraWrapper.style.width = '';
    cameraWrapper.style.height = '';
    cameraWrapper.style.maxWidth = '';
    cameraWrapper.style.maxHeight = '';
    localStorage.removeItem('cameraCardSize');
  });
})();

// ===== Floating Gallery System =====
(function() {
  const floatingGallery = document.getElementById('floatingGallery');
  const capturedImages = [];
  const selectedPhotos = new Set();
  let draggedPhoto = null;
  let offsetX = 0, offsetY = 0;
  let isDragging = false;
  let dragStartTime = 0;
  
  // Random position generator (avoiding center where camera is)
  function getRandomPosition() {
    const margin = 200;
    const photoSize = 200;
    const centerZoneWidth = 600;
    const centerZoneHeight = 600;
    
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    const centerX = vw / 2;
    const centerY = vh / 2;
    
    let x, y;
    let attempts = 0;
    
    do {
      x = margin + Math.random() * (vw - photoSize - margin * 2);
      y = margin + Math.random() * (vh - photoSize - margin * 2);
      attempts++;
      
      // Check if position is outside center zone
      const inCenterZone = (
        x > centerX - centerZoneWidth / 2 && 
        x < centerX + centerZoneWidth / 2 && 
        y > centerY - centerZoneHeight / 2 && 
        y < centerY + centerZoneHeight / 2
      );
      
      if (!inCenterZone || attempts > 20) break;
    } while (true);
    
    return { x, y };
  }
  
  // Update export button state
  function updateExportButton() {
    const exportBtn = document.getElementById('exportBtn');
    const selectionCount = document.getElementById('selectionCount');
    
    if (exportBtn && selectionCount) {
      const count = selectedPhotos.size;
      selectionCount.textContent = count;
      exportBtn.disabled = count === 0;
    }
  }
  
  // Toggle photo selection (max 4 photos)
  function togglePhotoSelection(photoEl) {
    const imgSrc = photoEl.querySelector('img').src;
    
    if (photoEl.classList.contains('selected')) {
      // Deselect
      photoEl.classList.remove('selected');
      selectedPhotos.delete(imgSrc);
    } else {
      // Check if already at limit (4 photos)
      if (selectedPhotos.size >= 4) {
        showAlert("Bạn chỉ có thể chọn tối đa 4 ảnh!");
        return;
      }
      // Select
      photoEl.classList.add('selected');
      selectedPhotos.add(imgSrc);
    }
    
    updateExportButton();
  }
  
  // Add photo to floating gallery
  window.addFloatingPhoto = function(imageSrc) {
    const photoEl = document.createElement('div');
    photoEl.className = 'floating-photo new-photo';
    
    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = 'Captured photo';
    photoEl.appendChild(img);
    
    // Add selection checkmark
    const checkmark = document.createElement('div');
    checkmark.className = 'photo-checkmark';
    checkmark.innerHTML = '✓';
    photoEl.appendChild(checkmark);
    
    // Random position, rotation, and size for chaotic style
    const pos = getRandomPosition();
    const rotation = (Math.random() - 0.5) * 50; // -25 to 25 degrees (more chaotic)
    const scale = 0.85 + Math.random() * 0.3; // 0.85 to 1.15 (varying sizes)
    const zIndex = 10 + Math.floor(Math.random() * 20); // Random stacking
    
    photoEl.style.left = pos.x + 'px';
    photoEl.style.top = pos.y + 'px';
    photoEl.style.transform = `rotate(${rotation}deg) scale(${scale})`;
    photoEl.style.setProperty('--rotation', rotation + 'deg');
    photoEl.style.zIndex = zIndex;
    
    // Store position
    photoEl.dataset.x = pos.x;
    photoEl.dataset.y = pos.y;
    photoEl.dataset.rotation = rotation;
    
    // Double-click handler for selection
    photoEl.addEventListener('dblclick', function(e) {
      e.preventDefault();
      togglePhotoSelection(this);
    });
    
    // Drag handlers
    photoEl.addEventListener('mousedown', startDrag);
    photoEl.addEventListener('touchstart', startDrag, { passive: false });
    
    floatingGallery.appendChild(photoEl);
    capturedImages.push(photoEl);
    
    // Remove animation class after animation completes
    setTimeout(() => photoEl.classList.remove('new-photo'), 800);
  };
  
  function startDrag(e) {
    draggedPhoto = this;
    draggedPhoto.classList.add('dragging');
    draggedPhoto.style.zIndex = '1002';
    isDragging = false;
    dragStartTime = Date.now();
    
    const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
    const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
    const rect = draggedPhoto.getBoundingClientRect();
    
    offsetX = clientX - rect.left;
    offsetY = clientY - rect.top;
    
    e.preventDefault();
  }
  
  function doDrag(e) {
    if (!draggedPhoto) return;
    
    isDragging = true;
    
    const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
    
    const x = clientX - offsetX;
    const y = clientY - offsetY;
    
    draggedPhoto.style.left = x + 'px';
    draggedPhoto.style.top = y + 'px';
    draggedPhoto.dataset.x = x;
    draggedPhoto.dataset.y = y;
    
    e.preventDefault();
  }
  
  function endDrag(e) {
    if (draggedPhoto) {
      draggedPhoto.classList.remove('dragging');
      draggedPhoto.style.zIndex = '';
      draggedPhoto = null;
    }
    // Reset drag flag after a short delay
    setTimeout(() => { isDragging = false; }, 100);
  }
  
  // Global drag listeners
  document.addEventListener('mousemove', doDrag);
  document.addEventListener('touchmove', doDrag, { passive: false });
  document.addEventListener('mouseup', endDrag);
  document.addEventListener('touchend', endDrag);
  
  // Export button handler
  const exportBtn = document.getElementById('exportBtn');
  const exportMessage = document.getElementById('exportMessage');
  const confirmExportBtn = document.getElementById('confirmExportBtn');
  
  // Intercept click in capture phase to avoid old JS handler
  exportBtn?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    if (selectedPhotos.size === 0) return;
    
    // Convert selected photos to array
    const photosArray = Array.from(selectedPhotos);
    
    // Check if user has selected exactly 4 photos
    if (photosArray.length < 4) {
      const remaining = 4 - photosArray.length;
      exportMessage.textContent = 
        `You've selected ${photosArray.length} photo${photosArray.length === 1 ? '' : 's'}. ` +
        `You need ${remaining} more photo${remaining === 1 ? '' : 's'} for a complete frame.`;
      
      // Show modal (lazy load bootstrap)
      if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('exportConfirmModal'));
        modal.show();
      }
      return;
    }
    
    // If 4 photos selected, proceed directly
    proceedToFrame();
  }, true);
  
  // Confirm button in modal
  confirmExportBtn?.addEventListener('click', function() {
    if (typeof bootstrap !== 'undefined') {
      const modal = bootstrap.Modal.getInstance(document.getElementById('exportConfirmModal'));
      if (modal) modal.hide();
    }
    proceedToFrame();
  });
  
  // Function to proceed to frame page
  function proceedToFrame() {
    const photosArray = Array.from(selectedPhotos);
    // Store in localStorage for frame page to access
    localStorage.setItem('selected_photos', JSON.stringify(photosArray));
    // Navigate to frame page using router param
    window.location.href = '?p=frame';
  }
  
  // Expose functions for external use
  window.getSelectedPhotos = function() {
    return Array.from(selectedPhotos);
  };
})();

// Override the original gallery to use floating gallery
const originalCapturedImages = document.getElementById('captured-images');
if (originalCapturedImages) {
  console.log('Setting up MutationObserver for gallery');
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        console.log('Node added:', node.tagName, node);
        if (node.tagName === 'IMG' && node.src) {
          console.log('Adding floating photo:', node.src);
          if (typeof window.addFloatingPhoto === 'function') {
            window.addFloatingPhoto(node.src);
          } else {
            console.error('window.addFloatingPhoto is not defined!');
          }
        }
      });
    });
  });
  observer.observe(originalCapturedImages, { childList: true });
  console.log('MutationObserver active');
} else {
  console.error('captured-images element not found!');
}

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

<!-- Mobile Menu Toggle -->
<script>
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

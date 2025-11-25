<?php
// admin/includes/layout_header.php
// Header & Sidebar cho Admin Panel

if (session_status() === PHP_SESSION_NONE) {
  if (function_exists('init_photobooth_session')) {
    init_photobooth_session();
  } else {
    session_name('PHOTOBOOTH_SESSION');
    $scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    if (preg_match('#/(web-photobooth|Web-photobooth)(/.*)?$#i', $scriptPath, $matches)) {
      $cookiePath = '/' . $matches[1] . '/';
    } else {
      $cookiePath = rtrim($scriptPath, '/') . '/';
      if ($cookiePath === '//') $cookiePath = '/';
    }
    ini_set('session.cookie_path', $cookiePath);
    session_start();
  }
}
$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'Admin Panel' ?> - SPACE PHOTOBOOTH Admin</title>
  <link rel="icon" type="image/png" href="../public/images/S.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700;900&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --sidebar-width: 280px;
      --topbar-height: 70px;
      --black: #0a0a0a;
      --white: #ffffff;
      --lime: #c1ff72;
      --gray-light: #f5f5f5;
      --gray-mid: #666666;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Space Grotesk', sans-serif;
      background: var(--white);
      color: var(--black);
    }
    
    /* Sidebar */
    .admin-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: var(--sidebar-width);
      height: 100vh;
      background: var(--black);
      color: var(--white);
      overflow-y: auto;
      z-index: 1000;
      transition: transform 0.3s ease;
      border-right: 3px solid var(--black);
    }
    
    .admin-sidebar::-webkit-scrollbar {
      width: 8px;
    }
    
    .admin-sidebar::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.05);
    }
    
    .admin-sidebar::-webkit-scrollbar-thumb {
      background: var(--lime);
      border-radius: 4px;
    }
    
    .admin-sidebar::-webkit-scrollbar-thumb:hover {
      background: #a8e05a;
    }
    
    .sidebar-header {
      padding: 32px 24px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-logo {
      font-size: 20px;
      font-weight: 900;
      color: var(--white);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      letter-spacing: -0.5px;
    }
    
    .sidebar-logo .logo-icon {
      font-size: 28px;
      color: var(--lime);
      font-weight: normal;
    }
    
    .sidebar-nav {
      padding: 24px 0;
    }
    
    .nav-item {
      margin: 0;
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 16px 24px;
      color: rgba(255, 255, 255, 0.75);
      text-decoration: none;
      transition: all 0.2s ease;
      font-size: 15px;
      font-weight: 600;
      border-left: 3px solid transparent;
    }
    
    .nav-link:hover {
      background: rgba(193, 255, 114, 0.1);
      color: var(--lime);
      border-left-color: var(--lime);
    }
    
    .nav-link.active {
      background: rgba(193, 255, 114, 0.15);
      color: var(--lime);
      border-left-color: var(--lime);
      font-weight: 700;
    }
    
    .nav-link i {
      font-size: 20px;
      width: 24px;
      text-align: center;
    }
    
    .nav-badge {
      margin-left: auto;
      background: var(--lime);
      color: var(--black);
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      font-family: 'DM Mono', monospace;
    }
    
    .nav-section-title {
      padding: 20px 24px 12px;
      font-size: 10px;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.4);
      text-transform: uppercase;
      letter-spacing: 2px;
      font-family: 'DM Mono', monospace;
    }
    
    /* Topbar */
    .admin-topbar {
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      right: 0;
      height: var(--topbar-height);
      background: var(--white);
      border-bottom: 3px solid var(--black);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      z-index: 999;
    }
    
    .topbar-title {
      font-size: 28px;
      font-weight: 900;
      color: var(--black);
      letter-spacing: -0.5px;
      text-transform: uppercase;
    }
    
    .topbar-user {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    
    .user-avatar {
      width: 44px;
      height: 44px;
      border-radius: 8px;
      background: var(--black);
      border: 2px solid var(--black);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--lime);
      font-weight: 700;
      font-size: 18px;
    }
    
    /* Main Content */
    .admin-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      padding: 40px;
      min-height: calc(100vh - var(--topbar-height));
      background: var(--gray-light);
    }
    
    /* Cards */
    .stat-card {
      border-radius: 16px;
      border: 2px solid var(--black);
      background: var(--white);
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.15);
    }
    
    .stat-icon {
      width: 56px;
      height: 56px;
      border-radius: 12px;
      border: 2px solid var(--black);
      background: var(--lime);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: var(--black);
    }
    
    /* Card Text Styles */
    .card-body .text-muted {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-family: 'DM Mono', monospace;
      color: var(--gray-mid) !important;
    }
    
    .card-body .h4 {
      font-weight: 900 !important;
      color: var(--black) !important;
      font-size: 36px !important;
    }
    
    .card-body .small {
      font-family: 'DM Mono', monospace;
      font-weight: 500;
    }
    
    /* Tables */
    .table {
      border: 2px solid var(--black);
      border-radius: 12px;
      overflow: visible !important;
      position: relative;
    }
    
    /* Table responsive - allow dropdown to overflow */
    .table-responsive {
      overflow-x: auto !important;
      overflow-y: visible !important;
      position: relative;
    }
    
    .table thead {
      background: var(--black);
      color: var(--white);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 1px;
    }
    
    .table thead th {
      padding: 20px 12px !important;
      vertical-align: middle;
    }
    
    .table tbody tr {
      border-bottom: 1px solid #e0e0e0;
    }
    
    .table tbody td {
      padding: 24px 12px !important;
      vertical-align: middle;
      line-height: 1.6;
    }
    
    .table tbody tr:last-child {
      border-bottom: none;
    }
    
    /* Buttons */
    .btn {
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      border-radius: 8px;
      padding: 10px 20px;
      border: 2px solid var(--black);
      transition: all 0.2s ease;
    }
    
    .btn-primary {
      background: var(--black);
      color: var(--white);
      border-color: var(--black);
    }
    
    .btn-primary:hover {
      background: var(--lime);
      color: var(--black);
      border-color: var(--black);
    }
    
    .btn-outline-secondary {
      background: transparent;
      color: var(--black);
      border-color: var(--black);
    }
    
    .btn-outline-secondary:hover {
      background: var(--black);
      color: var(--white);
    }
    
    /* Dropdown - use Bootstrap's boundary feature */
    .dropdown-menu {
      border: 2px solid var(--black);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      padding: 8px;
      z-index: 1050 !important;
      background: var(--white) !important;
      min-width: 220px;
    }
    
    /* Actions cell */
    .actions-cell {
      position: relative;
    }
    
    /* Card body - allow dropdown overflow */
    .card {
      overflow: visible !important;
    }
    
    .card-body {
      overflow: visible !important;
    }
    
    .card-body .table-responsive {
      overflow-x: auto !important;
      overflow-y: visible !important;
    }
    
    /* Ensure dropdowns are not clipped by any container */
    .admin-content {
      overflow: visible !important;
    }
    
    .admin-content > * {
      overflow: visible !important;
    }
    
    .dropdown-item {
      font-weight: 600;
      border-radius: 6px;
      padding: 10px 16px;
      background: transparent;
      border: none;
      transition: all 0.2s ease;
      color: var(--black);
      font-family: 'Space Grotesk', sans-serif;
      font-size: 14px;
    }
    
    .dropdown-item:hover {
      background: var(--gray-light) !important;
      color: var(--black) !important;
    }
    
    .dropdown-item:active {
      background: var(--lime) !important;
      color: var(--black) !important;
    }
    
    /* Mobile */
    @media (max-width: 768px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }
      
      .admin-sidebar.show {
        transform: translateX(0);
      }
      
      .admin-topbar,
      .admin-content {
        margin-left: 0;
        left: 0;
      }
      
      .topbar-title {
        font-size: 20px;
      }
    }
    
    /* Animations */
    .fade-in {
      animation: fadeIn 0.4s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="admin-sidebar">
    <div class="sidebar-header">
      <a href="index.php" class="sidebar-logo">
        <span class="logo-icon">✦</span>
        <span>SPACE PHOTOBOOTH</span>
      </a>
      <p class="text-center mb-0 mt-3" style="font-size: 10px; font-weight: 700; font-family: 'DM Mono', monospace; color: var(--lime); text-transform: uppercase; letter-spacing: 2px;">ADMIN PANEL</p>
    </div>
    
    <nav class="sidebar-nav">
      <div class="nav-section-title">Dashboard</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="index.php" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
          </a>
        </li>
      </ul>
      
      <div class="nav-section-title">Manage Frames</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="frames_list.php" class="nav-link <?= ($currentPage ?? '') === 'frames_list' ? 'active' : '' ?>">
            <i class="bi bi-images"></i>
            <span>List of Frames</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="frames_add.php" class="nav-link <?= ($currentPage ?? '') === 'frames_add' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Add Frame</span>
          </a>
        </li>
      </ul>
      
      <div class="nav-section-title">Premium</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="premium_requests.php" class="nav-link <?= ($currentPage ?? '') === 'premium_requests' ? 'active' : '' ?>">
            <i class="bi bi-inbox"></i>
            <span>Premium Requests</span>
            <?php
            // Đếm pending requests
            try {
              require_once __DIR__ . '/../../config/db.php';
              $pdo = db();
              $stmt = $pdo->query("SELECT COUNT(*) FROM premium_requests WHERE status = 'pending'");
              $pendingCount = $stmt->fetchColumn();
              if ($pendingCount > 0) {
                echo '<span class="nav-badge">' . $pendingCount . '</span>';
              }
            } catch (Exception $e) {}
            ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="users_premium.php" class="nav-link <?= ($currentPage ?? '') === 'users_premium' ? 'active' : '' ?>">
            <i class="bi bi-star-fill"></i>
            <span>Premium Users</span>
          </a>
        </li>
      </ul>
      
      <div class="nav-section-title">System</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="admin_users.php" class="nav-link <?= ($currentPage ?? '') === 'admin_users' ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i>
            <span>Admin Users</span>
          </a>
        </li>
      </ul>
      
      <div class="nav-section-title">Other</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="../public/?p=studio" class="nav-link">
            <i class="bi bi-box-arrow-left"></i>
            <span>Về trang chính</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  
  <!-- Topbar -->
  <div class="admin-topbar">
    <div class="topbar-title">
      <?= $pageTitle ?? 'Dashboard' ?>
    </div>
    
    <div class="topbar-user">
      <div class="d-none d-md-block">
        <div class="small text-muted">Admin</div>
        <div class="fw-semibold"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></div>
      </div>
      <div class="user-avatar">
        <?= strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../public/?p=studio"><i class="bi bi-house me-2"></i> Studio</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../public/?p=logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="admin-content fade-in">


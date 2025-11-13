<?php
// admin/includes/layout_header.php
// Header & Sidebar cho Admin Panel

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'Admin Panel' ?> - Photobooth Admin</title>
  <link rel="icon" type="image/png" href="../public/images/S.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --sidebar-width: 260px;
      --topbar-height: 60px;
      --primary-color: #ff6b35;
      --primary-hover: #f7931e;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f8f9fa;
    }
    
    /* Sidebar */
    .admin-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: var(--sidebar-width);
      height: 100vh;
      background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
      color: white;
      overflow-y: auto;
      z-index: 1000;
      transition: transform 0.3s ease;
    }
    
    .admin-sidebar::-webkit-scrollbar {
      width: 6px;
    }
    
    .admin-sidebar::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,0.2);
      border-radius: 3px;
    }
    
    .sidebar-header {
      padding: 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      background: rgba(0,0,0,0.2);
    }
    
    .sidebar-logo {
      font-size: 24px;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-decoration: none;
    }
    
    .sidebar-nav {
      padding: 20px 0;
    }
    
    .nav-item {
      margin: 4px 12px;
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      border-radius: 8px;
      transition: all 0.2s ease;
      font-size: 14px;
    }
    
    .nav-link:hover {
      background: rgba(255,255,255,0.1);
      color: white;
      transform: translateX(4px);
    }
    
    .nav-link.active {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      color: white;
      font-weight: 600;
    }
    
    .nav-link i {
      font-size: 18px;
      width: 24px;
      text-align: center;
    }
    
    .nav-badge {
      margin-left: auto;
      background: #dc3545;
      color: white;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
    }
    
    .nav-section-title {
      padding: 8px 28px;
      font-size: 11px;
      font-weight: 600;
      color: rgba(255,255,255,0.5);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-top: 16px;
    }
    
    /* Topbar */
    .admin-topbar {
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      right: 0;
      height: var(--topbar-height);
      background: white;
      border-bottom: 1px solid #e9ecef;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      z-index: 999;
      box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    
    .topbar-title {
      font-size: 20px;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .topbar-user {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
    }
    
    /* Main Content */
    .admin-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      padding: 24px;
      min-height: calc(100vh - var(--topbar-height));
    }
    
    /* Cards */
    .stat-card {
      border-radius: 12px;
      border: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
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
    }
    
    /* Animations */
    .fade-in {
      animation: fadeIn 0.3s ease;
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
      <a href="index.php" class="sidebar-logo d-block text-center">
        <i class="bi bi-house-heart-fill"></i> Photobooth
      </a>
      <p class="text-center text-white-50 mb-0 mt-2 small">Admin Panel</p>
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
      
      <div class="nav-section-title">Quản lý Frames</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="frames_list.php" class="nav-link <?= ($currentPage ?? '') === 'frames_list' ? 'active' : '' ?>">
            <i class="bi bi-images"></i>
            <span>Danh sách Frames</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="frames_add.php" class="nav-link <?= ($currentPage ?? '') === 'frames_add' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Thêm Frame</span>
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
      
      <div class="nav-section-title">Hệ thống</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="admin_users.php" class="nav-link <?= ($currentPage ?? '') === 'admin_users' ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i>
            <span>Admin Users</span>
          </a>
        </li>
      </ul>
      
      <div class="nav-section-title">Khác</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="../public/?p=home" class="nav-link">
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
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../public/?p=home"><i class="bi bi-house me-2"></i> Trang chủ</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../public/?p=logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="admin-content fade-in">


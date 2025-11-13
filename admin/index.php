<?php
// admin/index.php
// Dashboard - Trang chủ Admin Panel

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'dashboard';
$pageTitle = 'Dashboard';

// Thống kê
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$stats['total_users'] = $stmt->fetchColumn();

// Premium users
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_premium = 1");
$stats['premium_users'] = $stmt->fetchColumn();

// Pending requests
$stmt = $pdo->query("SELECT COUNT(*) FROM premium_requests WHERE status = 'pending'");
$stats['pending_requests'] = $stmt->fetchColumn();

// Total frames
$stmt = $pdo->query("SELECT COUNT(*) FROM frames");
$stats['total_frames'] = $stmt->fetchColumn();

// Premium frames
$stmt = $pdo->query("SELECT COUNT(*) FROM frames WHERE is_premium = 1");
$stats['premium_frames'] = $stmt->fetchColumn();

// Recent requests (5 latest)
$stmt = $pdo->query("
  SELECT pr.*, u.name as user_name, u.email 
  FROM premium_requests pr
  LEFT JOIN users u ON pr.user_id = u.id
  ORDER BY pr.requested_at DESC
  LIMIT 5
");
$recentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent users (5 latest)
$stmt = $pdo->query("
  SELECT id, name, email, is_premium, created_at 
  FROM users 
  ORDER BY created_at DESC 
  LIMIT 5
");
$recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/layout_header.php';
?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="stat-icon bg-primary bg-opacity-10 text-primary">
            <i class="bi bi-people-fill"></i>
          </div>
          <div class="ms-3 flex-grow-1">
            <div class="text-muted small">Tổng Users</div>
            <div class="h4 mb-0"><?= number_format($stats['total_users']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="stat-icon bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-star-fill"></i>
          </div>
          <div class="ms-3 flex-grow-1">
            <div class="text-muted small">Premium Users</div>
            <div class="h4 mb-0"><?= number_format($stats['premium_users']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="stat-icon bg-danger bg-opacity-10 text-danger">
            <i class="bi bi-inbox-fill"></i>
          </div>
          <div class="ms-3 flex-grow-1">
            <div class="text-muted small">Pending Requests</div>
            <div class="h4 mb-0"><?= number_format($stats['pending_requests']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="stat-icon bg-success bg-opacity-10 text-success">
            <i class="bi bi-images"></i>
          </div>
          <div class="ms-3 flex-grow-1">
            <div class="text-muted small">Tổng Frames</div>
            <div class="h4 mb-0"><?= number_format($stats['total_frames']) ?></div>
            <small class="text-muted"><?= $stats['premium_frames'] ?> premium</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="row g-3">
  <!-- Recent Requests -->
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="bi bi-inbox text-warning"></i> Premium Requests gần đây
        </h5>
        <a href="premium_requests.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
      </div>
      <div class="card-body p-0">
        <?php if (empty($recentRequests)): ?>
          <div class="p-4 text-center text-muted">
            <i class="bi bi-inbox display-4 d-block mb-2"></i>
            Chưa có request nào
          </div>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($recentRequests as $req): ?>
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <strong><?= htmlspecialchars($req['user_name']) ?></strong>
                    <br>
                    <small class="text-muted"><?= htmlspecialchars($req['email']) ?></small>
                    <br>
                    <small class="text-muted">
                      <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($req['requested_at'])) ?>
                    </small>
                  </div>
                  <span class="badge <?= $req['status'] === 'pending' ? 'bg-warning' : ($req['status'] === 'approved' ? 'bg-success' : 'bg-danger') ?>">
                    <?= strtoupper($req['status']) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Recent Users -->
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="bi bi-person-plus text-primary"></i> Users mới đăng ký
        </h5>
        <a href="users_list.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
      </div>
      <div class="card-body p-0">
        <?php if (empty($recentUsers)): ?>
          <div class="p-4 text-center text-muted">
            <i class="bi bi-people display-4 d-block mb-2"></i>
            Chưa có user nào
          </div>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($recentUsers as $user): ?>
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                    <?php if ($user['is_premium']): ?>
                      <span class="badge bg-warning text-dark ms-2">⭐ PREMIUM</span>
                    <?php endif; ?>
                    <br>
                    <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                    <br>
                    <small class="text-muted">
                      <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                    </small>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-3">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0">
          <i class="bi bi-lightning-fill text-warning"></i> Quick Actions
        </h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <a href="frames_add.php" class="btn btn-outline-primary w-100 py-3">
              <i class="bi bi-plus-circle fs-4 d-block mb-2"></i>
              Thêm Frame mới
            </a>
          </div>
          <div class="col-md-3">
            <a href="premium_requests.php?status=pending" class="btn btn-outline-warning w-100 py-3">
              <i class="bi bi-inbox fs-4 d-block mb-2"></i>
              Xem Pending Requests
              <?php if ($stats['pending_requests'] > 0): ?>
                <span class="badge bg-danger ms-1"><?= $stats['pending_requests'] ?></span>
              <?php endif; ?>
            </a>
          </div>
          <div class="col-md-3">
            <a href="users_premium.php" class="btn btn-outline-success w-100 py-3">
              <i class="bi bi-star-fill fs-4 d-block mb-2"></i>
              Quản lý Premium Users
            </a>
          </div>
          <div class="col-md-3">
            <a href="users_list.php" class="btn btn-outline-info w-100 py-3">
              <i class="bi bi-people-fill fs-4 d-block mb-2"></i>
              Xem tất cả Users
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>


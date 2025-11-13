<?php
// admin/admin_users.php
// Quản lý Admin Users - Thêm/Gỡ quyền admin

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'admin_users';
$pageTitle = 'Quản lý Admin Users';

$MSG = '';
$OK = false;

// Xử lý thêm admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin_email'])) {
  $email = trim($_POST['add_admin_email']);
  
  if (empty($email)) {
    $MSG = '❌ Vui lòng nhập email!';
  } else {
    try {
      // Kiểm tra user có tồn tại không
      $stmt = $pdo->prepare("SELECT id, name, email, is_admin FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$user) {
        $MSG = '❌ Không tìm thấy user với email: ' . htmlspecialchars($email);
      } elseif ($user['is_admin']) {
        $MSG = '⚠️ User này đã là admin rồi!';
      } else {
        // Cấp quyền admin
        $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
        $stmt->execute([$email]);
        $MSG = '✅ Đã cấp quyền admin cho: ' . htmlspecialchars($user['name']) . ' (' . htmlspecialchars($email) . ')';
        $OK = true;
      }
    } catch (Exception $e) {
      $MSG = '❌ Lỗi: ' . $e->getMessage();
    }
  }
}

// Xử lý gỡ quyền admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_admin_id'])) {
  $userId = (int)$_POST['remove_admin_id'];
  
  try {
    // Không cho phép gỡ quyền chính mình
    if ($userId == $_SESSION['user']['id']) {
      $MSG = '❌ Bạn không thể gỡ quyền admin của chính mình!';
    } else {
      $stmt = $pdo->prepare("UPDATE users SET is_admin = 0 WHERE id = ?");
      $stmt->execute([$userId]);
      $MSG = '✅ Đã gỡ quyền admin!';
      $OK = true;
    }
  } catch (Exception $e) {
    $MSG = '❌ Lỗi: ' . $e->getMessage();
  }
}

// Lấy danh sách admin users
$stmt = $pdo->query("SELECT id, name, email, is_premium, is_admin FROM users WHERE is_admin = 1 ORDER BY id DESC");
$adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm tổng số admin
$totalAdmins = count($adminUsers);

require __DIR__ . '/includes/layout_header.php';
?>

<?php if ($MSG): ?>
  <div class="alert <?= $OK ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show">
    <?= htmlspecialchars($MSG) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row mb-4">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Thêm Admin User</h5>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Email của user cần cấp quyền admin:</label>
            <input type="email" name="add_admin_email" class="form-control" 
                   placeholder="user@example.com" required>
            <small class="text-muted">User phải đã đăng ký tài khoản trước đó</small>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-person-check"></i> Cấp quyền Admin
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn</h5>
      </div>
      <div class="card-body">
        <ul class="mb-0">
          <li>Nhập <strong>email</strong> của user đã đăng ký</li>
          <li>User sẽ được cấp quyền <strong>admin</strong> ngay lập tức</li>
          <li>Admin có thể truy cập tất cả trang trong Admin Panel</li>
          <li>Bạn không thể gỡ quyền admin của chính mình</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people"></i> Danh sách Admin Users</h5>
        <span class="badge bg-primary">Tổng: <?= $totalAdmins ?></span>
      </div>
      <div class="card-body p-0">
        <?php if (empty($adminUsers)): ?>
          <div class="alert alert-info m-3 mb-0">
            Chưa có admin nào.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Premium</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($adminUsers as $admin): ?>
                  <tr>
                    <td><?= $admin['id'] ?></td>
                    <td>
                      <strong><?= htmlspecialchars($admin['name']) ?></strong>
                      <?php if ($admin['id'] == $_SESSION['user']['id']): ?>
                        <span class="badge bg-info ms-2">Bạn</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($admin['email']) ?></td>
                    <td>
                      <?php if ($admin['is_premium']): ?>
                        <span class="badge bg-warning">⭐ Premium</span>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($admin['id'] == $_SESSION['user']['id']): ?>
                        <span class="text-muted small">Không thể gỡ</span>
                      <?php else: ?>
                        <form method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn gỡ quyền admin của user này?');">
                          <input type="hidden" name="remove_admin_id" value="<?= $admin['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-person-x"></i> Gỡ quyền
                          </button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>


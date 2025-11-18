<?php
// admin/admin_users.php
// Manage Admin Users - Add/Remove admin privileges

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'admin_users';
$pageTitle = 'Manage Admin Users';

$MSG = '';
$OK = false;

// Handle add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin_email'])) {
  $email = trim($_POST['add_admin_email']);
  
  if (empty($email)) {
    $MSG = '❌ Please enter email!';
  } else {
    try {
      // Check if user exists
      $stmt = $pdo->prepare("SELECT id, name, email, is_admin FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$user) {
        $MSG = '❌ User not found with email: ' . htmlspecialchars($email);
      } elseif ($user['is_admin']) {
        $MSG = '⚠️ This user is already an admin!';
      } else {
        // Grant admin privileges
        $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
        $stmt->execute([$email]);
        $MSG = '✅ Admin privileges granted to: ' . htmlspecialchars($user['name']) . ' (' . htmlspecialchars($email) . ')';
        $OK = true;
      }
    } catch (Exception $e) {
      $MSG = '❌ Error: ' . $e->getMessage();
    }
  }
}

// Handle remove admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_admin_id'])) {
  $userId = (int)$_POST['remove_admin_id'];
  
  try {
    // Don't allow removing own admin privileges
    if ($userId == $_SESSION['user']['id']) {
      $MSG = '❌ You cannot remove your own admin privileges!';
    } else {
      $stmt = $pdo->prepare("UPDATE users SET is_admin = 0 WHERE id = ?");
      $stmt->execute([$userId]);
      $MSG = '✅ Admin privileges removed!';
      $OK = true;
    }
  } catch (Exception $e) {
    $MSG = '❌ Lỗi: ' . $e->getMessage();
  }
}

// Get admin users list
$stmt = $pdo->query("SELECT id, name, email, is_premium, is_admin FROM users WHERE is_admin = 1 ORDER BY id DESC");
$adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total admins
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
        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Add Admin User</h5>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Email of user to grant admin privileges:</label>
            <input type="email" name="add_admin_email" class="form-control" 
                   placeholder="user@example.com" required>
            <small class="text-muted">User must have registered an account beforehand</small>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-person-check"></i> Grant Admin
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Instructions</h5>
      </div>
      <div class="card-body">
        <ul class="mb-0">
          <li>Enter <strong>email</strong> of registered user</li>
          <li>User will be granted <strong>admin</strong> privileges immediately</li>
          <li>Admins can access all pages in Admin Panel</li>
          <li>You cannot remove your own admin privileges</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people"></i> Admin Users List</h5>
        <span class="badge bg-primary">Total: <?= $totalAdmins ?></span>
      </div>
      <div class="card-body p-0">
        <?php if (empty($adminUsers)): ?>
          <div class="alert alert-info m-3 mb-0">
            No admins yet.
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
                        <span class="badge bg-info ms-2">You</span>
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
                        <span class="text-muted small">Cannot remove</span>
                      <?php else: ?>
                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to remove admin privileges from this user?');">
                          <input type="hidden" name="remove_admin_id" value="<?= $admin['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-person-x"></i> Remove
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


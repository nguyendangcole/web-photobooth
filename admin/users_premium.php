<?php
// admin/users_premium.php
// Manage premium users: set/unset premium status

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'users_premium';
$pageTitle = 'Manage Premium Users';

$MSG = '';
$OK = false;

// Handle upgrade/downgrade user form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = (int)($_POST['user_id'] ?? 0);
  $action = $_POST['action'] ?? '';
  $premiumMonths = (int)($_POST['premium_months'] ?? 1);

  if ($userId <= 0) {
    $MSG = '❌ Invalid User ID.';
  } else {
    if ($action === 'upgrade') {
      // Calculate expiry date
      $premiumUntil = date('Y-m-d H:i:s', strtotime("+$premiumMonths months"));
      
      $stmt = $pdo->prepare("UPDATE users SET is_premium = 1, premium_until = ? WHERE id = ?");
      $stmt->execute([$premiumUntil, $userId]);
      
      $OK = true;
      $MSG = "✅ User #$userId upgraded to Premium (expires: $premiumUntil)";
    } elseif ($action === 'downgrade') {
      $stmt = $pdo->prepare("UPDATE users SET is_premium = 0, premium_until = NULL WHERE id = ?");
      $stmt->execute([$userId]);
      
      $OK = true;
      $MSG = "✅ User #$userId downgraded to Free";
    } elseif ($action === 'extend') {
      // Extend subscription
      $stmt = $pdo->prepare("SELECT premium_until FROM users WHERE id = ?");
      $stmt->execute([$userId]);
      $currentUntil = $stmt->fetchColumn();
      
      $baseDate = $currentUntil && strtotime($currentUntil) > time() 
        ? $currentUntil 
        : date('Y-m-d H:i:s');
      
      $newUntil = date('Y-m-d H:i:s', strtotime("+$premiumMonths months", strtotime($baseDate)));
      
      $stmt = $pdo->prepare("UPDATE users SET is_premium = 1, premium_until = ? WHERE id = ?");
      $stmt->execute([$newUntil, $userId]);
      
      $OK = true;
      $MSG = "✅ User #$userId extended until $newUntil";
    }
  }
}

// Get users list
$searchQuery = $_GET['search'] ?? '';
$sql = "SELECT id, name, email, provider, is_premium, premium_until, created_at FROM users";
$params = [];

if ($searchQuery) {
  $sql .= " WHERE name LIKE ? OR email LIKE ?";
  $params = ["%$searchQuery%", "%$searchQuery%"];
}

$sql .= " ORDER BY is_premium DESC, id DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count premium users
$stmtCount = $pdo->query("SELECT COUNT(*) FROM users WHERE is_premium = 1");
$premiumCount = $stmtCount->fetchColumn();

// Count pending requests
$stmtPending = $pdo->query("SELECT COUNT(*) FROM premium_requests WHERE status = 'pending'");
$totalPending = $stmtPending->fetchColumn();

require __DIR__ . '/includes/layout_header.php';
?>

<?php if ($MSG): ?>
  <div class="alert <?= $OK ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show">
    <?= htmlspecialchars($MSG) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Search -->
<form method="get" class="mb-3">
  <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Search user by name or email..." value="<?= htmlspecialchars($searchQuery) ?>">
    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
    <?php if ($searchQuery): ?>
      <a href="users_premium.php" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Clear</a>
    <?php endif; ?>
  </div>
</form>

    <!-- Users Table -->
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Provider</th>
                <th>Status</th>
                <th>Premium Until</th>
                <th>Registered</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <?php
                  $isPremium = (bool)$u['is_premium'];
                  $premiumUntil = $u['premium_until'];
                  $isExpired = false;
                  
                  if ($isPremium && $premiumUntil) {
                    $expiryDate = new DateTime($premiumUntil);
                    $now = new DateTime();
                    $isExpired = $now > $expiryDate;
                  }
                ?>
                <tr>
                  <td><?= $u['id'] ?></td>
                  <td><?= htmlspecialchars($u['name']) ?></td>
                  <td><?= htmlspecialchars($u['email']) ?></td>
                  <td><span class="badge bg-secondary"><?= $u['provider'] ?></span></td>
                  <td>
                    <?php if ($isPremium): ?>
                      <span class="premium-badge">⭐ PREMIUM</span>
                    <?php else: ?>
                      <span class="free-badge">FREE</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($premiumUntil): ?>
                      <small class="<?= $isExpired ? 'expired' : '' ?>">
                        <?= date('d/m/Y H:i', strtotime($premiumUntil)) ?>
                        <?= $isExpired ? ' (Expired)' : '' ?>
                      </small>
                    <?php else: ?>
                      <small class="text-muted">-</small>
                    <?php endif; ?>
                  </td>
                  <td><small><?= date('d/m/Y', strtotime($u['created_at'])) ?></small></td>
                  <td class="actions-cell text-end">
                    <!-- Dropdown actions -->
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                        Actions
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (!$isPremium || $isExpired): ?>
                          <li>
                            <form method="post" style="margin: 0;">
                              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                              <input type="hidden" name="action" value="upgrade">
                              <input type="hidden" name="premium_months" value="1">
                              <button type="submit" class="dropdown-item" style="white-space: nowrap; cursor: pointer;">
                                ⬆️ Upgrade to Premium (1 month)
                              </button>
                            </form>
                          </li>
                          <li>
                            <form method="post" style="margin: 0;">
                              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                              <input type="hidden" name="action" value="upgrade">
                              <input type="hidden" name="premium_months" value="12">
                              <button type="submit" class="dropdown-item" style="white-space: nowrap; cursor: pointer;">
                                ⬆️ Upgrade to Premium (1 year)
                              </button>
                            </form>
                          </li>
                        <?php endif; ?>
                        
                        <?php if ($isPremium && !$isExpired): ?>
                          <li>
                            <form method="post" style="margin: 0;">
                              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                              <input type="hidden" name="action" value="extend">
                              <input type="hidden" name="premium_months" value="1">
                              <button type="submit" class="dropdown-item" style="white-space: nowrap; cursor: pointer;">
                                ➡️ Extend 1 month
                              </button>
                            </form>
                          </li>
                          <li>
                            <form method="post" style="margin: 0;">
                              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                              <input type="hidden" name="action" value="extend">
                              <input type="hidden" name="premium_months" value="12">
                              <button type="submit" class="dropdown-item" style="white-space: nowrap; cursor: pointer;">
                                ➡️ Extend 1 year
                              </button>
                            </form>
                          </li>
                          <li><hr class="dropdown-divider"></li>
                          <li>
                            <form method="post" style="margin: 0;" onsubmit="return confirm('Are you sure to downgrade this user?')">
                              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                              <input type="hidden" name="action" value="downgrade">
                              <button type="submit" class="dropdown-item text-danger" style="white-space: nowrap; cursor: pointer;">
                                ⬇️ Downgrade to Free
                              </button>
                            </form>
                          </li>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php if (empty($users)): ?>
      <div class="alert alert-info mt-3">
        No users found<?= $searchQuery ? ' matching keyword "' . htmlspecialchars($searchQuery) . '"' : '' ?>.
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>

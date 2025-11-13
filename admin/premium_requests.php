<?php
// admin/premium_requests.php
// Quản lý requests nâng cấp premium

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'premium_requests';
$pageTitle = 'Premium Requests';

$MSG = '';
$OK = false;

// Xử lý approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $requestId = (int)($_POST['request_id'] ?? 0);
  $action = $_POST['action'] ?? '';
  $notes = trim($_POST['notes'] ?? '');
  $premiumMonths = (int)($_POST['premium_months'] ?? 1);

  if ($requestId <= 0) {
    $MSG = '❌ Request ID không hợp lệ.';
  } else {
    // Lấy thông tin request
    $stmt = $pdo->prepare("SELECT user_id, status FROM premium_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
      $MSG = '❌ Request không tồn tại.';
    } elseif ($request['status'] !== 'pending') {
      $MSG = '❌ Request này đã được xử lý rồi.';
    } else {
      $userId = $request['user_id'];
      $now = date('Y-m-d H:i:s');

      if ($action === 'approve') {
        // Tính ngày hết hạn
        $premiumUntil = date('Y-m-d H:i:s', strtotime("+$premiumMonths months"));
        
        // Update user thành premium
        $stmt = $pdo->prepare("UPDATE users SET is_premium = 1, premium_until = ? WHERE id = ?");
        $stmt->execute([$premiumUntil, $userId]);
        
        // Update request status
        $stmt = $pdo->prepare("UPDATE premium_requests SET status = 'approved', processed_at = ?, notes = ? WHERE id = ?");
        $stmt->execute([$now, $notes, $requestId]);
        
        $OK = true;
        $MSG = "✅ Đã phê duyệt request #$requestId. User #$userId đã được nâng cấp lên Premium (hết hạn: $premiumUntil)";
      } elseif ($action === 'reject') {
        // Update request status
        $stmt = $pdo->prepare("UPDATE premium_requests SET status = 'rejected', processed_at = ?, notes = ? WHERE id = ?");
        $stmt->execute([$now, $notes, $requestId]);
        
        $OK = true;
        $MSG = "✅ Đã từ chối request #$requestId";
      }
    }
  }
}

// Lấy danh sách requests
$statusFilter = $_GET['status'] ?? 'all';
$sql = "SELECT pr.*, u.name as user_name, u.email as user_email 
        FROM premium_requests pr
        LEFT JOIN users u ON pr.user_id = u.id";
$params = [];

if ($statusFilter !== 'all') {
  $sql .= " WHERE pr.status = ?";
  $params[] = $statusFilter;
}

$sql .= " ORDER BY pr.requested_at DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm theo status
$stmtCount = $pdo->query("SELECT status, COUNT(*) as cnt FROM premium_requests GROUP BY status");
$counts = [];
while ($row = $stmtCount->fetch(PDO::FETCH_ASSOC)) {
  $counts[$row['status']] = $row['cnt'];
}
$totalPending = $counts['pending'] ?? 0;

require __DIR__ . '/includes/layout_header.php';
?>

<?php if ($MSG): ?>
  <div class="alert <?= $OK ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show">
    <?= htmlspecialchars($MSG) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="mb-3 d-flex justify-content-between align-items-center">
  <div>
    <span class="text-muted">
      Tổng số pending: <strong class="text-warning"><?= $totalPending ?></strong>
    </span>
  </div>
  <div class="btn-group" role="group">
    <a href="?status=all" class="btn btn-sm btn-outline-secondary <?= $statusFilter === 'all' ? 'active' : '' ?>">
      Tất cả
    </a>
    <a href="?status=pending" class="btn btn-sm btn-outline-warning <?= $statusFilter === 'pending' ? 'active' : '' ?>">
      Pending (<?= $counts['pending'] ?? 0 ?>)
    </a>
    <a href="?status=approved" class="btn btn-sm btn-outline-success <?= $statusFilter === 'approved' ? 'active' : '' ?>">
      Approved (<?= $counts['approved'] ?? 0 ?>)
    </a>
    <a href="?status=rejected" class="btn btn-sm btn-outline-danger <?= $statusFilter === 'rejected' ? 'active' : '' ?>">
      Rejected (<?= $counts['rejected'] ?? 0 ?>)
    </a>
  </div>
</div>

<!-- Requests List -->
<?php if (empty($requests)): ?>
  <div class="alert alert-info">
    Không có request nào<?= $statusFilter !== 'all' ? " với status '$statusFilter'" : '' ?>.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($requests as $req): ?>
      <div class="col-12">
        <div class="card request-card shadow-sm" style="border-left: 4px solid #ff6b35;">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-4">
                <h5 class="mb-1">Request #<?= $req['id'] ?></h5>
                <p class="mb-1">
                  <strong>User:</strong> <?= htmlspecialchars($req['user_name']) ?><br>
                  <small class="text-muted"><?= htmlspecialchars($req['user_email']) ?></small>
                </p>
                <small class="text-muted">
                  <i class="bi bi-clock"></i> Yêu cầu: <?= date('d/m/Y H:i', strtotime($req['requested_at'])) ?>
                </small>
              </div>
              <div class="col-md-2 text-center">
                <?php
                  $badgeClass = [
                    'pending' => 'bg-warning',
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger'
                  ][$req['status']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $badgeClass ?>">
                  <?= strtoupper($req['status']) ?>
                </span>
              </div>
              <div class="col-md-3">
                <?php if ($req['processed_at']): ?>
                  <small class="text-muted">
                    <i class="bi bi-check-circle"></i> Xử lý: <?= date('d/m/Y H:i', strtotime($req['processed_at'])) ?>
                  </small>
                  <?php if ($req['notes']): ?>
                    <br><small class="text-muted"><i class="bi bi-chat-left-text"></i> Note: <?= htmlspecialchars($req['notes']) ?></small>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
              <div class="col-md-3 text-end">
                <?php if ($req['status'] === 'pending'): ?>
                  <button type="button" class="btn btn-success btn-sm" 
                          data-bs-toggle="modal" 
                          data-bs-target="#approveModal<?= $req['id'] ?>">
                    <i class="bi bi-check-circle"></i> Approve
                  </button>
                  <button type="button" class="btn btn-danger btn-sm" 
                          data-bs-toggle="modal" 
                          data-bs-target="#rejectModal<?= $req['id'] ?>">
                    <i class="bi bi-x-circle"></i> Reject
                  </button>
                <?php else: ?>
                  <span class="text-muted small"><i class="bi bi-check-all"></i> Đã xử lý</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Approve Modal -->
      <div class="modal fade" id="approveModal<?= $req['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
              <input type="hidden" name="action" value="approve">
              <div class="modal-header">
                <h5 class="modal-title">Phê duyệt Request #<?= $req['id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p>Bạn có chắc muốn phê duyệt yêu cầu của <strong><?= htmlspecialchars($req['user_name']) ?></strong>?</p>
                <div class="mb-3">
                  <label class="form-label">Thời hạn Premium:</label>
                  <select name="premium_months" class="form-select">
                    <option value="1">1 tháng</option>
                    <option value="3">3 tháng</option>
                    <option value="6">6 tháng</option>
                    <option value="12" selected>1 năm</option>
                    <option value="24">2 năm</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Ghi chú (tùy chọn):</label>
                  <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú cho request này..."></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Phê duyệt</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Reject Modal -->
      <div class="modal fade" id="rejectModal<?= $req['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
              <input type="hidden" name="action" value="reject">
              <div class="modal-header">
                <h5 class="modal-title">Từ chối Request #<?= $req['id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p>Bạn có chắc muốn từ chối yêu cầu của <strong><?= htmlspecialchars($req['user_name']) ?></strong>?</p>
                <div class="mb-3">
                  <label class="form-label">Lý do từ chối (tùy chọn):</label>
                  <textarea name="notes" class="form-control" rows="3" placeholder="Nhập lý do từ chối..."></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Từ chối</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>

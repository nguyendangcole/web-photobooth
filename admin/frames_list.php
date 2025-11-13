<?php
// admin/frames_list.php
// Danh sách tất cả frames

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'frames_list';
$pageTitle = 'Danh sách Frames';

$MSG = '';
$OK = false;

// Xử lý xóa frame
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $deleteId = (int)$_POST['delete_id'];
  try {
    $stmt = $pdo->prepare("DELETE FROM frames WHERE id = ?");
    $stmt->execute([$deleteId]);
    $MSG = '✅ Đã xóa frame!';
    $OK = true;
  } catch (Exception $e) {
    $MSG = '❌ Lỗi: ' . $e->getMessage();
  }
}

// Xử lý cập nhật premium status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_premium_id'])) {
  $frameId = (int)$_POST['toggle_premium_id'];
  try {
    $stmt = $pdo->prepare("UPDATE frames SET is_premium = NOT is_premium WHERE id = ?");
    $stmt->execute([$frameId]);
    $MSG = '✅ Đã cập nhật premium status!';
    $OK = true;
  } catch (Exception $e) {
    $MSG = '❌ Lỗi: ' . $e->getMessage();
  }
}

// Lấy danh sách frames
$stmt = $pdo->query("SELECT id, name, src, layout, is_premium FROM frames ORDER BY is_premium DESC, id DESC");
$frames = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm số frames
$totalFrames = count($frames);
$premiumFrames = count(array_filter($frames, fn($f) => $f['is_premium']));
$freeFrames = $totalFrames - $premiumFrames;

require __DIR__ . '/includes/layout_header.php';
?>

<?php if ($MSG): ?>
  <div class="alert <?= $OK ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show">
    <?= htmlspecialchars($MSG) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row mb-3">
  <div class="col">
    <div class="d-flex gap-3">
      <span class="text-muted">Tổng: <strong><?= $totalFrames ?></strong></span>
      <span class="text-muted">Free: <strong><?= $freeFrames ?></strong></span>
      <span class="text-warning">Premium: <strong><?= $premiumFrames ?></strong></span>
    </div>
  </div>
</div>

<div class="row g-4">
  <?php foreach ($frames as $frame): ?>
    <div class="col-md-6 col-lg-4 col-xl-3">
      <div class="card shadow-sm h-100">
        <div class="position-relative">
          <img src="<?= '../' . htmlspecialchars($frame['src']) ?>" alt="<?= htmlspecialchars($frame['name']) ?>" class="card-img-top" style="height: 200px; object-fit: contain; background: #f8f9fa;">
          <?php if ($frame['is_premium']): ?>
            <span class="badge bg-warning position-absolute top-0 end-0 m-2">
              <i class="bi bi-star-fill"></i> PREMIUM
            </span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <h6 class="card-title"><?= htmlspecialchars($frame['name']) ?></h6>
          <p class="card-text small text-muted mb-2">
            Layout: <span class="badge bg-secondary"><?= htmlspecialchars($frame['layout']) ?></span>
          </p>
          <p class="card-text small text-muted mb-0">
            ID: #<?= $frame['id'] ?>
          </p>
        </div>
        <div class="card-footer bg-white border-top-0">
          <div class="btn-group w-100" role="group">
            <form method="post" class="flex-fill" onsubmit="return confirm('Toggle premium status?');">
              <input type="hidden" name="toggle_premium_id" value="<?= $frame['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                <i class="bi bi-star"></i>
              </button>
            </form>
            <form method="post" class="flex-fill" onsubmit="return confirm('Xóa frame này?');">
              <input type="hidden" name="delete_id" value="<?= $frame['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if (empty($frames)): ?>
  <div class="alert alert-info mt-3">
    Chưa có frame nào. <a href="frames_add.php">Thêm frame mới</a>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>


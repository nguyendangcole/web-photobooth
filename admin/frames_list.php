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
    $MSG = '❌ Error: ' . $e->getMessage();
  }
}

// Handle edit frame
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
  $editId = (int)$_POST['edit_id'];
  $name = trim($_POST['name'] ?? '');
  $layout = trim($_POST['layout'] ?? 'vertical');
  $src = trim($_POST['src'] ?? '');
  $isPremium = isset($_POST['is_premium']) ? 1 : 0;
  
  try {
    if (empty($name)) {
      $MSG = '❌ Frame name is required.';
    } elseif (empty($src)) {
      $MSG = '❌ Frame src is required.';
    } else {
      $stmt = $pdo->prepare("UPDATE frames SET name = ?, layout = ?, src = ?, is_premium = ? WHERE id = ?");
      $stmt->execute([$name, $layout, $src, $isPremium, $editId]);
      $MSG = '✅ Frame updated successfully!';
      $OK = true;
    }
  } catch (Exception $e) {
    $MSG = '❌ Error: ' . $e->getMessage();
  }
}

// Handle toggle premium status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_premium_id'])) {
  $frameId = (int)$_POST['toggle_premium_id'];
  try {
    $stmt = $pdo->prepare("UPDATE frames SET is_premium = NOT is_premium WHERE id = ?");
    $stmt->execute([$frameId]);
    $MSG = '✅ Premium status updated!';
    $OK = true;
  } catch (Exception $e) {
    $MSG = '❌ Error: ' . $e->getMessage();
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
            <button type="button" class="btn btn-sm btn-outline-primary flex-fill" data-bs-toggle="modal" data-bs-target="#editModal<?= $frame['id'] ?>">
              <i class="bi bi-pencil"></i>
            </button>
            <form method="post" class="flex-fill" onsubmit="return confirm('Toggle premium status?');">
              <input type="hidden" name="toggle_premium_id" value="<?= $frame['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                <i class="bi bi-star"></i>
              </button>
            </form>
            <form method="post" class="flex-fill" onsubmit="return confirm('Delete this frame?');">
              <input type="hidden" name="delete_id" value="<?= $frame['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Edit Modal -->
      <div class="modal fade" id="editModal<?= $frame['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="edit_id" value="<?= $frame['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title">Edit Frame #<?= $frame['id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Frame Name *</label>
                  <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($frame['name']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Layout</label>
                  <select name="layout" class="form-select">
                    <option value="vertical" <?= $frame['layout'] === 'vertical' ? 'selected' : '' ?>>Vertical</option>
                    <option value="horizontal" <?= $frame['layout'] === 'horizontal' ? 'selected' : '' ?>>Horizontal</option>
                    <option value="square" <?= $frame['layout'] === 'square' ? 'selected' : '' ?>>Square</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Image Source (src) *</label>
                  <input type="text" name="src" class="form-control" value="<?= htmlspecialchars($frame['src']) ?>" required>
                  <small class="text-muted">Example: public/images/frame-name.png</small>
                </div>
                <div class="mb-3">
                  <div class="form-check">
                    <input type="checkbox" name="is_premium" value="1" class="form-check-input" id="premium<?= $frame['id'] ?>" <?= $frame['is_premium'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="premium<?= $frame['id'] ?>">
                      <span class="text-warning fw-bold">⭐ Premium Frame</span>
                    </label>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Preview</label>
                  <div class="border rounded p-2" style="background: #f8f9fa;">
                    <img src="<?= '../' . htmlspecialchars($frame['src']) ?>" alt="Preview" class="img-fluid" style="max-height: 150px; object-fit: contain;">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if (empty($frames)): ?>
  <div class="alert alert-info mt-3">
    No frames yet. <a href="frames_add.php">Add new frame</a>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>


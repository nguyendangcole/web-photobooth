<?php
// admin/frames_add.php
// Thêm frame mới: upload ảnh vào public/images/ hoặc nhập src thủ công, rồi insert DB.

require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'frames_add';
$pageTitle = 'Thêm Frame';

$MSG = '';
$OK  = false;
$lastSrc = '';
$lastName = '';
$lastLayout = 'vertical';

function clean_filename($name) {
  // bỏ ký tự đặc biệt, giữ lại a-z0-9-_.
  $name = strtolower($name);
  $name = preg_replace('/[^a-z0-9\-\._]+/i', '-', $name);
  $name = preg_replace('/-+/', '-', $name);
  return trim($name, '-_.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name   = trim($_POST['name'] ?? '');
  $layout = ($_POST['layout'] ?? 'vertical') === 'square' ? 'square' : 'vertical';
  $isPremium = !empty($_POST['is_premium']) ? 1 : 0; // checkbox premium
  $lastName = $name;
  $lastLayout = $layout;

  $src = trim($_POST['src'] ?? ''); // nếu không upload, cho phép nhập src

  // Nếu có upload file
  if (!empty($_FILES['file']['name'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
      $MSG = 'Upload lỗi (mã ' . $file['error'] . ').';
    } else {
      // Kiểm tra mime & đuôi
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime  = $finfo->file($file['tmp_name']);
      $allowed = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
      ];
      if (!isset($allowed[$mime])) {
        $MSG = 'Chỉ chấp nhận PNG, JPG, WEBP.';
      } else {
        // Tạo tên file an toàn + unique
        $orig = clean_filename($file['name']);
        $ext  = pathinfo($orig, PATHINFO_EXTENSION);
        if (!$ext) $ext = $allowed[$mime];
        $basename = pathinfo($orig, PATHINFO_FILENAME);
        if ($basename === '') $basename = 'frame';

        $newName = $basename . '-' . date('Ymd-His') . '-' . substr(bin2hex(random_bytes(3)),0,6) . '.' . $ext;

        // Đường dẫn tương đối (lưu DB) & tuyệt đối (ghi file)
        $rel  = 'public/images/' . $newName;
        $abs  = dirname(__DIR__) . '/' . $rel;  // …/WEB-PHOTOBOOTH/public/images/xxx.png

        // Tạo thư mục nếu thiếu
        @mkdir(dirname($abs), 0777, true);

        if (!move_uploaded_file($file['tmp_name'], $abs)) {
          $MSG = 'Không thể lưu file lên server.';
        } else {
          $src = $rel; // dùng ảnh vừa upload
          $lastSrc = $src;
        }
      }
    }
  }

  if (!$MSG) {
    if (!$name) {
      $MSG = 'Thiếu tên frame (name).';
    } elseif (!$src) {
      $MSG = 'Bạn cần upload ảnh hoặc nhập đường dẫn src.';
    } else {
      // Chèn vào DB (bao gồm cả is_premium)
      $stmt = $pdo->prepare("INSERT INTO frames (name, src, layout, is_premium) VALUES (:n, :s, :l, :p)");
      $stmt->execute([':n' => $name, ':s' => $src, ':l' => $layout, ':p' => $isPremium]);
      $OK  = true;
      $MSG = '✅ Đã thêm frame' . ($isPremium ? ' PREMIUM' : '') . '!';
      // reset form (trừ layout cho tiện thêm tiếp)
      $lastName = '';
      // Nếu bạn muốn reset luôn layout, bỏ comment dòng dưới:
      // $lastLayout = 'vertical';
    }
  }
}

require __DIR__ . '/includes/layout_header.php';
?>

<?php if ($MSG): ?>
  <div class="alert <?= $OK ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show">
    <?= htmlspecialchars($MSG) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Thêm Frame Mới</h5>
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Tên Frame *</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($lastName) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Layout *</label>
            <select name="layout" class="form-select">
              <option value="vertical" <?= $lastLayout==='vertical'?'selected':'' ?>>Vertical (1×4)</option>
              <option value="square"   <?= $lastLayout==='square'  ?'selected':'' ?>>Square (2×2)</option>
            </select>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" name="is_premium" value="1" class="form-check-input" id="isPremium">
              <label class="form-check-label" for="isPremium">
                <span class="text-warning fw-bold">⭐ Premium Frame</span>
                <small class="d-block text-muted">Chỉ user premium mới dùng được</small>
              </label>
            </div>
          </div>

          <hr>

          <div class="mb-3">
            <label class="form-label">Upload File (PNG/JPG/WEBP)</label>
            <input type="file" name="file" class="form-control" accept="image/png,image/jpeg,image/webp">
            <small class="text-muted">Nếu không upload, hãy nhập đường dẫn src bên dưới</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Hoặc nhập Src</label>
            <input type="text" name="src" class="form-control" placeholder="public/images/ten-anh.png">
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-plus-circle"></i> Thêm Frame
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
        <ol class="mb-0">
          <li class="mb-2">Điền <strong>tên frame</strong> và chọn <strong>layout</strong></li>
          <li class="mb-2">Tick <strong>Premium Frame</strong> nếu muốn frame này chỉ dành cho premium users</li>
          <li class="mb-2">Upload ảnh <em>hoặc</em> nhập đường dẫn <code>src</code></li>
          <li class="mb-2">Click <strong>Thêm Frame</strong></li>
        </ol>

        <?php if ($OK && $lastSrc): ?>
          <div class="alert alert-success mt-3">
            <strong>Ảnh vừa upload:</strong><br>
            <img src="<?= '../' . htmlspecialchars($lastSrc) ?>" alt="" class="img-fluid mt-2 rounded" style="max-height: 200px;">
            <small class="d-block mt-2 text-muted">
              Path: <code><?= htmlspecialchars($lastSrc) ?></code>
            </small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>

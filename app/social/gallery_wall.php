<?php
require_once dirname(__DIR__) . '/config.php';
require_login_or_redirect();

$me   = current_user_or_null();
$meId = (int)($me['id'] ?? 0);

// ===== Lấy danh sách lời mời & bạn bè =====
$sqlPending = "SELECT u.id, u.name
               FROM friendships f
               JOIN users u ON u.id = f.requester_id
               WHERE f.addressee_id = :me AND f.status = 'pending'";
$st = db()->prepare($sqlPending);
$st->execute([':me' => $meId]);
$pending = $st->fetchAll(PDO::FETCH_ASSOC);

$sqlFriends = "SELECT u.id, u.name
               FROM friendships f
               JOIN users u ON u.id = CASE WHEN f.requester_id = :me1 THEN f.addressee_id ELSE f.requester_id END
               WHERE f.status='accepted' AND (f.requester_id=:me2 OR f.addressee_id=:me3)";
$st = db()->prepare($sqlFriends);
$st->execute([':me1' => $meId, ':me2' => $meId, ':me3' => $meId]);

$friends = $st->fetchAll(PDO::FETCH_ASSOC);

// ===== Lấy ảnh =====
$sqlPhotos = "SELECT p.*, u.name
              FROM shared_photos p
              JOIN users u ON u.id = p.user_id
              ORDER BY p.created_at DESC";
$photos = db()->query($sqlPhotos)->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../header.php';

?>

<main class="container-fluid mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"></h2>
    <button class="btn btn-outline-primary" id="toggleSidebarBtn">Friends</button>
  </div>

  <div id="gwMasonry">
    <?php foreach ($photos as $p): ?>
      <div class="card">
        <img src="<?= asset('uploads/' . htmlspecialchars($p['filename'])) ?>"
             alt="Photo by <?= htmlspecialchars($p['name']) ?>"
             class="card-img-top">
        <button class="delete-btn" data-id="<?= $p['id'] ?>">X</button>
        <div class="card-body">
          <strong><?= htmlspecialchars($p['name']) ?></strong><br>
          <span class="text-muted"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- 🔹 FRIEND SIDEBAR -->
<aside id="friendSidebar" class="friend-sidebar shadow">
  <div class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom">
    <h5 class="mb-0">👥 Friends & Requests</h5>
    <button class="btn-close" id="closeSidebarBtn"></button>
  </div>

  <div class="sidebar-content p-3">
    <h6>Pending Requests (<?= count($pending) ?>)</h6>
    <?php if (empty($pending)): ?>
      <p class="text-muted small">No pending requests.</p>
    <?php else: ?>
      <?php foreach ($pending as $r): ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span><?= htmlspecialchars($r['name']) ?></span>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-success js-accept" data-id="<?= $r['id'] ?>">Accept</button>
            <button class="btn btn-outline-danger js-reject" data-id="<?= $r['id'] ?>">Reject</button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <hr>
    <h6>My Friends (<?= count($friends) ?>)</h6>
    <?php if (empty($friends)): ?>
      <p class="text-muted small">You have no friends yet.</p>
    <?php else: ?>
      <ul class="list-unstyled mb-0">
        <?php foreach ($friends as $f): ?>
          <li class="mb-1">👤 <?= htmlspecialchars($f['name']) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</aside>

<link rel="stylesheet" href="<?= asset('css/gallery_instagram.css') ?>">
<link rel="stylesheet" href="<?= asset('css/gallery_sidebar.css') ?>">
<script src="<?= asset('js/gallery_delete.js') ?>?v=1"></script>
<script src="<?= asset('js/gallery_sidebar.js') ?>?v=1"></script>




<!-- Lightbox popup (vừa phải, kiểu Instagram) -->
<div class="modal fade" id="photoLightbox" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 700px;">
    <div class="modal-content bg-dark border-0 rounded-4 shadow-lg p-2 text-center">
      <img id="lightboxImg" 
           class="img-fluid rounded-3 mx-auto d-block" 
           alt="Full photo" 
           style="max-height: 85vh; object-fit: contain;">
    </div>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
  const modalEl = document.getElementById("photoLightbox");
  const imgEl   = document.getElementById("lightboxImg");

  document.querySelectorAll("#gwMasonry .card-img-top").forEach(img => {
    img.addEventListener("click", () => {
      imgEl.src = img.src;
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    });
  });
});
</script>
<link rel="stylesheet" href="<?= asset('css/gallery_background.css') ?>?v=1">
<?php require __DIR__ . '/../footer.php'; ?>

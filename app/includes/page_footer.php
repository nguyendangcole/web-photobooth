<?php
// app/includes/page_footer.php
// Common footer for frame, photobooth, photobook, and studio pages
// Parameter: $theme = 'light' or 'dark'

if (!isset($theme)) $theme = 'light';
?>

<style>
/* Compact footer - <?= $theme === 'dark' ? 'Dark' : 'Light' ?> theme */
.footer {
  background: <?= $theme === 'dark' ? '#0a0a0a' : '#ffffff' ?> !important;
  color: <?= $theme === 'dark' ? '#ffffff' : '#333333' ?> !important;
  padding: 6px 15px !important;
  border-top: 1px solid <?= $theme === 'dark' ? '#0a0a0a' : '#e0e0e0' ?> !important;
  margin-top: auto !important;
  <?= $theme === 'light' ? 'box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05) !important;' : '' ?>
}
.footer-content {
  max-width: 1400px !important;
  margin: 0 auto !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  flex-wrap: wrap !important;
  gap: 12px !important;
}
.footer-links {
  display: flex !important;
  align-items: center !important;
  gap: 1rem !important;
  flex-wrap: wrap !important;
}
.footer-links a {
  color: <?= $theme === 'dark' ? '#ffffff' : '#666666' ?> !important;
  text-decoration: none !important;
  font-size: 9px !important;
  font-weight: <?= $theme === 'dark' ? '500' : '700' ?> !important;
  opacity: 0.8 !important;
  transition: opacity 0.2s, color 0.2s !important;
  <?= $theme === 'light' ? 'text-transform: uppercase;' : '' ?>
}
.footer-links a:hover {
  opacity: 1 !important;
  color: #c1ff72 !important;
}
.footer-copyright {
  color: <?= $theme === 'dark' ? 'rgba(255, 255, 255, 0.6)' : '#999999' ?> !important;
  font-size: 8px !important;
  font-weight: <?= $theme === 'dark' ? '500' : '700' ?> !important;
  margin: 0 !important;
}
.footer-copyright strong {
  color: <?= $theme === 'dark' ? '#c1ff72' : '#333333' ?> !important;
  font-weight: 700 !important;
}
@media (max-width: 768px) {
  .footer-content {
    flex-direction: column !important;
    text-align: center !important;
    gap: 6px !important;
  }
  .footer-links {
    justify-content: center !important;
    gap: 0.75rem !important;
  }
  .footer {
    padding: 8px 15px !important;
  }
}
</style>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-links">
      <a href="?p=studio">Studio</a>
      <a href="?p=info">Info</a>
      <a href="?p=service">Service</a>
      <a href="?p=qa">Q&A</a>
      <a href="?p=contact">Contact</a>
    </div>
    <p class="footer-copyright">© <?= date('Y') ?> <strong>Space Photobooth</strong> | Show your style</p>
  </div>
</footer>

<!-- Scroll Animations CSS -->
<link rel="stylesheet" href="<?= BASE_URL ?>css/scroll-animations.css?v=<?= time() ?>">

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Scroll Animations JS -->
<script src="<?= BASE_URL ?>js/scroll-animations.js?v=<?= time() ?>"></script>


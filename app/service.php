<?php
// app/service.php
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navigation -->
<nav class="main-nav">
  <div class="nav-wrapper">
    <div class="logo">
      <span class="logo-icon">✦</span>
      <span class="logo-text">SPACE PHOTOBOOTH</span>
      <span class="logo-badge">2025</span>
    </div>
    <div class="nav-menu">
      <a href="?p=landing" class="nav-link">HOME</a>
      <a href="?p=info" class="nav-link">INFO</a>
      <a href="?p=service" class="nav-link">SERVICE</a>
      <a href="?p=qa" class="nav-link">Q&A</a>
      <a href="?p=contact" class="nav-link">CONTACT</a>
      <?php if ($isLoggedIn): ?>
        <a href="?p=studio" class="nav-btn">STUDIO</a>
      <?php else: ?>
        <a href="?p=login" class="nav-btn">LOGIN</a>
      <?php endif; ?>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<!-- Services Section -->
<section class="services-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="section-header" style="margin-bottom: 4rem;">
      <h1 class="section-title">OUR <span class="gradient">SERVICES</span></h1>
      <p class="section-sub">Everything you need for stunning visuals</p>
    </div>

    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">INSTANT</div>
        <h3 class="service-title">PHOTOBOOTH</h3>
        <p class="service-desc">Take instant photos with our fun photobooth feature. Choose from various filters and effects to make your photos stand out. Real-time preview and multiple shots in one session.</p>
        <a href="?p=photobooth" class="service-link">TRY NOW →</a>
      </div>

      <div class="service-card">
        <div class="service-icon">COMPOSE</div>
        <h3 class="service-title">FRAME COMPOSER</h3>
        <p class="service-desc">Create stunning photo frames by combining multiple images. Choose from a variety of frame templates and layouts. Upload your own photos or use captured images.</p>
        <a href="?p=frame" class="service-link">CREATE →</a>
      </div>

      <div class="service-card">
        <div class="service-icon">ORGANIZE</div>
        <h3 class="service-title">PHOTOBOOK</h3>
        <p class="service-desc">Organize and save your favorite photos in beautiful photobooks. Create albums and share your memories with friends. Easy download and export options.</p>
        <a href="?p=photobook" class="service-link">EXPLORE →</a>
      </div>

      <div class="service-card service-card-featured">
        <div class="service-icon">PREMIUM</div>
        <h3 class="service-title">PREMIUM FEATURES</h3>
        <p class="service-desc">Unlock exclusive frames and features with our premium membership. Get access to special templates, advanced editing tools, and priority support.</p>
        <a href="?p=premium-upgrade" class="service-link">UPGRADE →</a>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <h3 class="footer-logo">FUTUREFRAME</h3>
        <p>Next-gen photobooth for the digital generation.</p>
        <div class="footer-social">
          <a href="#" class="social">TW</a>
          <a href="#" class="social">IG</a>
          <a href="#" class="social">TT</a>
          <a href="#" class="social">YT</a>
        </div>
      </div>
      <div class="footer-links">
        <h4>PRODUCT</h4>
        <a href="?p=studio">Studio</a>
        <a href="?p=photobook">Photobook</a>
        <a href="?p=premium-upgrade">Premium</a>
      </div>
      <div class="footer-links">
        <h4>COMPANY</h4>
        <a href="?p=info">About</a>
        <a href="?p=service">Services</a>
        <a href="?p=contact">Contact</a>
      </div>
      <div class="footer-links">
        <h4>SUPPORT</h4>
        <a href="?p=qa">Help</a>
        <a href="?p=contact">Contact</a>
        <a href="?p=terms">Terms</a>
        <a href="?p=privacy">Privacy</a>
      </div>
      <div class="footer-newsletter">
        <h4>STAY UPDATED</h4>
        <p>Get latest frames & features</p>
        <form class="newsletter">
          <input type="email" placeholder="Your email">
          <button type="submit">→</button>
        </form>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> FutureFrame. All rights reserved.</p>
      <p>Designed with ✨ for the future</p>
    </div>
  </div>
</footer>

<style>
.services-section {
  background: var(--white);
  color: var(--black);
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.service-card {
  background: var(--white);
  border: 2px solid var(--black);
  border-radius: 16px;
  padding: 2.5rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.service-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.service-card:hover::before {
  opacity: 1;
}

.service-card-featured {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  border-color: #667eea;
}

.service-icon {
  font-size: 3rem;
  font-weight: 700;
  color: var(--black);
  margin-bottom: 1rem;
  font-family: 'Bebas Neue', sans-serif;
  letter-spacing: 2px;
}

.service-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: var(--black);
}

.service-desc {
  font-size: 1rem;
  line-height: 1.7;
  color: #555;
  margin-bottom: 1.5rem;
}

.service-link {
  display: inline-block;
  color: var(--black);
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}

.service-link:hover {
  color: var(--8c52ff);
}

@media (max-width: 768px) {
  .services-section {
    padding: 100px 20px 60px;
  }
  
  .services-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
// Mobile menu toggle
document.getElementById('menuToggle')?.addEventListener('click', function() {
  document.querySelector('.nav-menu').classList.toggle('active');
  this.classList.toggle('active');
});
</script>

</body>
</html>

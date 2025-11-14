<?php
// app/info.php
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
  <title>SPACE PHOTOBOOTH • About Us</title>
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

<!-- Info Section -->
<section class="info-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="section-header" style="margin-bottom: 4rem;">
      <h1 class="section-title">ABOUT <span class="gradient">SPACE PHOTOBOOTH</span></h1>
      <p class="section-sub">Next-gen photobooth for the digital generation</p>
    </div>

    <div class="info-content">
      <div class="info-block">
        <h2 class="info-title">OUR <span class="gradient">MISSION</span></h2>
        <p class="info-text">We're on a mission to revolutionize how you capture, create, and share moments. Space Photobooth combines cutting-edge technology with creative freedom, giving you the tools to transform ordinary photos into extraordinary memories.</p>
      </div>

      <div class="info-block">
        <h2 class="info-title">WHAT WE <span class="gradient">OFFER</span></h2>
        <div class="features-list">
          <div class="feature-item">
            <h3>INSTANT CAPTURE</h3>
            <p>Webcam support with countdown timer. Multiple shots in one session with real-time preview.</p>
          </div>
          <div class="feature-item">
            <h3>ALIEN FRAMES</h3>
            <p>100+ unique frames with sci-fi & Y2K aesthetics. Mix and match to create your perfect look.</p>
          </div>
          <div class="feature-item">
            <h3>FRAME COMPOSER</h3>
            <p>Create stunning photo frames by combining multiple images. Choose from various layouts and templates.</p>
          </div>
          <div class="feature-item">
            <h3>PHOTOBOOK</h3>
            <p>Organize and save your favorite photos in beautiful photobooks. Create albums and share memories.</p>
          </div>
          <div class="feature-item">
            <h3>PREMIUM FEATURES</h3>
            <p>Unlock exclusive frames and advanced features with premium membership. Access special templates and tools.</p>
          </div>
        </div>
      </div>

      <div class="info-block">
        <h2 class="info-title">WHY <span class="gradient">CHOOSE US</span></h2>
        <p class="info-text">Space Photobooth is built for creators who refuse to settle for ordinary. Our platform combines intuitive design with powerful features, making professional-quality photo creation accessible to everyone. Whether you're planning an event, creating content, or just having fun, we've got you covered.</p>
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
.info-section {
  background: var(--white);
  color: var(--black);
}

.info-content {
  max-width: 900px;
  margin: 0 auto;
}

.info-block {
  margin-bottom: 4rem;
}

.info-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  font-family: 'Space Grotesk', sans-serif;
}

.info-text {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #333;
  margin-bottom: 1rem;
}

.features-list {
  display: grid;
  gap: 2rem;
  margin-top: 2rem;
}

.feature-item {
  padding: 1.5rem;
  border: 2px solid var(--black);
  border-radius: 16px;
  background: var(--white);
  transition: transform 0.3s, box-shadow 0.3s;
}

.feature-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.feature-item h3 {
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  color: var(--black);
}

.feature-item p {
  font-size: 1rem;
  line-height: 1.6;
  color: #555;
  margin: 0;
}

@media (max-width: 768px) {
  .info-section {
    padding: 100px 20px 60px;
  }
  
  .info-title {
    font-size: 2rem;
  }
  
  .info-text {
    font-size: 1rem;
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

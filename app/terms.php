<?php
// app/terms.php
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
  <title>SPACE PHOTOBOOTH • Terms of Service</title>
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

<!-- Terms Section -->
<section class="terms-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="section-header" style="margin-bottom: 4rem;">
      <h1 class="section-title">TERMS OF <span class="gradient">SERVICE</span></h1>
      <p class="section-sub">Last updated: <?= date('F j, Y') ?></p>
    </div>

    <div class="terms-content" style="max-width: 900px; margin: 0 auto;">
      <div class="terms-block">
        <h2 class="terms-title">1. ACCEPTANCE OF TERMS</h2>
        <p class="terms-text">By accessing and using Space Photobooth, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these terms, please do not use our service.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">2. USE OF SERVICE</h2>
        <p class="terms-text">You agree to use Space Photobooth only for lawful purposes and in a way that does not infringe the rights of, restrict or inhibit anyone else's use and enjoyment of the service. Prohibited behavior includes harassing or causing distress or inconvenience to any person, transmitting obscene or offensive content, or disrupting the normal flow of dialogue within our service.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">3. USER ACCOUNTS</h2>
        <p class="terms-text">You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account or password. You must notify us immediately of any unauthorized use of your account.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">4. CONTENT OWNERSHIP</h2>
        <p class="terms-text">You retain all ownership rights to the content you create using Space Photobooth. By using our service, you grant us a limited, non-exclusive license to store and display your content solely for the purpose of providing the service to you.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">5. PREMIUM MEMBERSHIP</h2>
        <p class="terms-text">Premium membership grants access to exclusive features and content. Premium status is subject to approval by administrators. We reserve the right to modify, suspend, or terminate premium features at any time.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">6. LIMITATION OF LIABILITY</h2>
        <p class="terms-text">Space Photobooth shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">7. MODIFICATIONS TO TERMS</h2>
        <p class="terms-text">We reserve the right to modify these terms at any time. We will notify users of any changes by updating the "Last updated" date. Your continued use of the service after changes constitutes acceptance of the new terms.</p>
      </div>

      <div class="terms-block">
        <h2 class="terms-title">8. CONTACT</h2>
        <p class="terms-text">If you have any questions about these Terms of Service, please contact us at <a href="mailto:support@spacephotobooth.com">support@spacephotobooth.com</a>.</p>
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
.terms-section {
  background: var(--white);
  color: var(--black);
}

.terms-content {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.terms-block {
  padding-bottom: 2rem;
  border-bottom: 1px solid #e0e0e0;
}

.terms-block:last-child {
  border-bottom: none;
}

.terms-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: var(--black);
  font-family: 'Space Grotesk', sans-serif;
}

.terms-text {
  font-size: 1rem;
  line-height: 1.8;
  color: #555;
  margin: 0;
}

.terms-text a {
  color: var(--black);
  text-decoration: none;
  border-bottom: 2px solid var(--8c52ff);
  transition: color 0.2s;
}

.terms-text a:hover {
  color: var(--8c52ff);
}

@media (max-width: 768px) {
  .terms-section {
    padding: 100px 20px 60px;
  }
  
  .terms-title {
    font-size: 1.3rem;
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


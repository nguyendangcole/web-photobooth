<?php
// app/privacy.php
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
  <title>SPACE PHOTOBOOTH • Privacy Policy</title>
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

<!-- Privacy Section -->
<section class="privacy-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="section-header" style="margin-bottom: 4rem;">
      <h1 class="section-title">PRIVACY <span class="gradient">POLICY</span></h1>
      <p class="section-sub">Last updated: <?= date('F j, Y') ?></p>
    </div>

    <div class="privacy-content" style="max-width: 900px; margin: 0 auto;">
      <div class="privacy-block">
        <h2 class="privacy-title">1. INFORMATION WE COLLECT</h2>
        <p class="privacy-text">We collect information that you provide directly to us, including your name, email address, and any content you create or upload using our service. We also automatically collect certain information about your device and how you interact with our service.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">2. HOW WE USE YOUR INFORMATION</h2>
        <p class="privacy-text">We use the information we collect to provide, maintain, and improve our services, process your requests, communicate with you, and ensure the security of our platform. We do not sell your personal information to third parties.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">3. DATA STORAGE AND SECURITY</h2>
        <p class="privacy-text">Your data is stored securely using industry-standard encryption and security measures. We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">4. YOUR RIGHTS</h2>
        <p class="privacy-text">You have the right to access, update, or delete your personal information at any time. You can manage your account settings or contact us directly to exercise these rights. You also have the right to request a copy of your data.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">5. COOKIES AND TRACKING</h2>
        <p class="privacy-text">We use cookies and similar tracking technologies to enhance your experience, analyze usage patterns, and improve our service. You can control cookie preferences through your browser settings.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">6. THIRD-PARTY SERVICES</h2>
        <p class="privacy-text">Our service may contain links to third-party websites or services. We are not responsible for the privacy practices of these third parties. We encourage you to review their privacy policies before providing any information.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">7. CHILDREN'S PRIVACY</h2>
        <p class="privacy-text">Our service is not intended for children under the age of 13. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">8. CHANGES TO THIS POLICY</h2>
        <p class="privacy-text">We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date. Your continued use of the service after changes constitutes acceptance of the new policy.</p>
      </div>

      <div class="privacy-block">
        <h2 class="privacy-title">9. CONTACT US</h2>
        <p class="privacy-text">If you have any questions about this Privacy Policy, please contact us at <a href="mailto:support@spacephotobooth.com">support@spacephotobooth.com</a>.</p>
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
.privacy-section {
  background: var(--white);
  color: var(--black);
}

.privacy-content {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.privacy-block {
  padding-bottom: 2rem;
  border-bottom: 1px solid #e0e0e0;
}

.privacy-block:last-child {
  border-bottom: none;
}

.privacy-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: var(--black);
  font-family: 'Space Grotesk', sans-serif;
}

.privacy-text {
  font-size: 1rem;
  line-height: 1.8;
  color: #555;
  margin: 0;
}

.privacy-text a {
  color: var(--black);
  text-decoration: none;
  border-bottom: 2px solid var(--8c52ff);
  transition: color 0.2s;
}

.privacy-text a:hover {
  color: var(--8c52ff);
}

@media (max-width: 768px) {
  .privacy-section {
    padding: 100px 20px 60px;
  }
  
  .privacy-title {
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


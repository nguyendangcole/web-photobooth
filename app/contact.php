<?php
// app/contact.php
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');
  
  if (empty($name) || empty($email) || empty($message)) {
    $error = 'Please fill in all required fields.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } else {
    // Here you can add code to send email or save to database
    $success = 'Thank you for your message! We will get back to you soon.';
    $name = $email = $subject = $message = '';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Contact</title>
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

<!-- Contact Section -->
<section class="contact-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="section-header" style="margin-bottom: 4rem;">
      <h1 class="section-title">GET IN <span class="gradient">TOUCH</span></h1>
      <p class="section-sub">We'd love to hear from you</p>
    </div>

    <div class="contact-wrapper" style="max-width: 800px; margin: 0 auto;">
      <div class="contact-grid">
        <div class="contact-info">
          <h2 class="contact-info-title">CONTACT <span class="gradient">INFO</span></h2>
          <div class="contact-details">
            <div class="contact-detail-item">
              <h3>EMAIL</h3>
              <p><a href="mailto:support@spacephotobooth.com">support@spacephotobooth.com</a></p>
            </div>
            <div class="contact-detail-item">
              <h3>RESPONSE TIME</h3>
              <p>We typically respond within 24-48 hours</p>
            </div>
            <div class="contact-detail-item">
              <h3>SUPPORT</h3>
              <p>Check our <a href="?p=qa">Q&A page</a> for quick answers</p>
            </div>
          </div>
        </div>

        <div class="contact-form-wrapper">
          <?php if ($success): ?>
            <div class="alert alert-success" style="background: #c1ff72; color: #0a0a0a; border: 2px solid #0a0a0a; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
              <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger" style="background: #ff6b6b; color: #fff; border: 2px solid #0a0a0a; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" class="contact-form">
            <div class="form-group">
              <label for="name">NAME</label>
              <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
            </div>
            
            <div class="form-group">
              <label for="email">EMAIL</label>
              <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            
            <div class="form-group">
              <label for="subject">SUBJECT</label>
              <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>">
            </div>
            
            <div class="form-group">
              <label for="message">MESSAGE</label>
              <textarea id="message" name="message" rows="6" required><?= htmlspecialchars($message ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="contact-submit">SEND MESSAGE →</button>
          </form>
        </div>
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
.contact-section {
  background: var(--white);
  color: var(--black);
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 3rem;
}

.contact-info-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  font-family: 'Space Grotesk', sans-serif;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.contact-detail-item h3 {
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 0.5rem;
  color: #666;
  text-transform: uppercase;
}

.contact-detail-item p {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--black);
  margin: 0;
}

.contact-detail-item a {
  color: var(--black);
  text-decoration: none;
  border-bottom: 2px solid var(--8c52ff);
  transition: color 0.2s;
}

.contact-detail-item a:hover {
  color: var(--8c52ff);
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: var(--black);
  text-transform: uppercase;
}

.form-group input,
.form-group textarea {
  padding: 1rem;
  border: 2px solid var(--black);
  border-radius: 8px;
  font-size: 1rem;
  font-family: 'Space Grotesk', sans-serif;
  background: var(--white);
  color: var(--black);
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--8c52ff);
  box-shadow: 0 0 0 3px rgba(140, 82, 255, 0.1);
}

.contact-submit {
  padding: 1rem 2rem;
  background: var(--black);
  color: var(--white);
  border: 2px solid var(--black);
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  font-family: 'Space Grotesk', sans-serif;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.contact-submit:hover {
  background: var(--8c52ff);
  border-color: var(--8c52ff);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(140, 82, 255, 0.3);
}

@media (max-width: 768px) {
  .contact-section {
    padding: 100px 20px 60px;
  }
  
  .contact-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
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

<?php
// app/qa.php
require_once __DIR__ . '/config.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/S.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Q&A</title>
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

<!-- Q&A Section -->
<section class="qa-section" style="padding: 120px 20px 80px; min-height: 100vh;">
  <div class="container">
    <div class="section-header" style="margin-bottom: 4rem;">
      <h1 class="section-title">QUESTIONS & <span class="gradient">ANSWERS</span></h1>
      <p class="section-sub">Everything you need to know</p>
    </div>

    <div class="qa-list" style="max-width: 900px; margin: 0 auto;">
      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>How do I take photos in Photobooth?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Simply click on the Photobooth option from the home page, allow camera access when prompted, and click the capture button to take your photos. You can choose from various filters and effects before capturing.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>What is a Premium account?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Premium accounts give you access to exclusive frames and advanced features. You can request a premium upgrade from your account settings. Once approved by admin, you'll unlock all premium content.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>How do I save my photos?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>After creating your photos or frames, you can download them directly using the download button, or save them to your Photobook for later access. All your creations are stored in your account.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>Can I use photos from my device?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Yes! You can upload photos from your device in the Frame Composer and Photobook sections to create custom frames and albums. Supported formats include JPG, PNG, and WebP.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>Is my data secure?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Yes, we take your privacy seriously. All your photos and data are securely stored and only accessible by you. We never share your information with third parties. Your content is encrypted and protected.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>How do I create a frame?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Navigate to the Frame Composer page, upload your images, and choose a frame template. You can drag and drop images to arrange them, then download or save your creation to your Photobook.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>What browsers are supported?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Space Photobooth works on all modern browsers including Chrome, Firefox, Safari, and Edge. For the best experience, we recommend using the latest version of your browser.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>Can I use this on mobile?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>Yes! Space Photobooth is fully responsive and works on mobile devices. You can access all features including camera capture, frame creation, and photobook management from your phone or tablet.</p>
        </div>
      </div>

      <div class="qa-item">
        <button class="qa-question" type="button">
          <span>How can I print the photos?</span>
          <span class="qa-icon">+</span>
        </button>
        <div class="qa-answer">
          <p>You can print your photos at our store location or visit any nearby printing shops. Our store is located at <strong>Trường Đại học Bách khoa - Đại học Quốc gia TP.HCM</strong>. After downloading your photos from the Photobook or Frame Composer, you can bring the image files to our store or any local printing service to have them printed. You can find our exact location and directions using our <a href="?p=landing#map" style="color: var(--8c52ff); text-decoration: underline;">Google Maps link</a>.</p>
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
.qa-section {
  background: var(--white);
  color: var(--black);
}

.qa-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.qa-item {
  border: 2px solid var(--black);
  border-radius: 16px;
  overflow: hidden;
  background: var(--white);
  transition: box-shadow 0.3s;
}

.qa-item:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.qa-question {
  width: 100%;
  padding: 1.5rem 2rem;
  background: var(--white);
  border: none;
  text-align: left;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--black);
  transition: background 0.2s;
  font-family: 'Space Grotesk', sans-serif;
}

.qa-question:hover {
  background: #f5f5f5;
}

.qa-icon {
  font-size: 1.5rem;
  font-weight: 300;
  transition: transform 0.3s;
  color: var(--8c52ff);
}

.qa-item.active .qa-icon {
  transform: rotate(45deg);
}

.qa-answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease, padding 0.3s ease;
  padding: 0 2rem;
}

.qa-item.active .qa-answer {
  max-height: 500px;
  padding: 0 2rem 1.5rem;
}

.qa-answer p {
  margin: 0;
  line-height: 1.7;
  color: #555;
  font-size: 1rem;
}

@media (max-width: 768px) {
  .qa-section {
    padding: 100px 20px 60px;
  }
  
  .qa-question {
    padding: 1.25rem 1.5rem;
    font-size: 1rem;
  }
  
  .qa-answer {
    padding: 0 1.5rem;
  }
  
  .qa-item.active .qa-answer {
    padding: 0 1.5rem 1.25rem;
  }
}
</style>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
// Q&A Accordion
document.querySelectorAll('.qa-question').forEach(question => {
  question.addEventListener('click', function() {
    const item = this.parentElement;
    const isActive = item.classList.contains('active');
    
    // Close all items
    document.querySelectorAll('.qa-item').forEach(qa => {
      qa.classList.remove('active');
    });
    
    // Open clicked item if it wasn't active
    if (!isActive) {
      item.classList.add('active');
    }
  });
});

// Mobile menu toggle
document.getElementById('menuToggle')?.addEventListener('click', function() {
  document.querySelector('.nav-menu').classList.toggle('active');
  this.classList.toggle('active');
});
</script>

</body>
</html>

<?php
// app/landing.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../config/db.php';
$user = current_user();
$isLoggedIn = !empty($user);
$userName = $isLoggedIn ? ($user['name'] ?? 'User') : '';

// Get frames from database
try {
  $pdo = db();
  // Try to get sample_image if column exists, otherwise use src
  try {
    $stmt = $pdo->query("SELECT id, name, src, layout, is_premium, sample_image FROM frames ORDER BY is_premium DESC, id ASC");
  } catch (Exception $e) {
    // If sample_image column doesn't exist, use src only
    $stmt = $pdo->query("SELECT id, name, src, layout, is_premium FROM frames ORDER BY is_premium DESC, id ASC");
  }
  $frames = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // For each frame, use sample_image if available, otherwise use src
  // Also fix the path: remove 'public/' prefix if exists since BASE_URL already points to public/
  foreach ($frames as &$frame) {
    if (isset($frame['sample_image']) && !empty($frame['sample_image'])) {
      $frame['display_image'] = $frame['sample_image'];
    } else {
      $frame['display_image'] = $frame['src'];
    }
    // Remove 'public/' prefix from path if it exists
    $frame['display_image'] = preg_replace('#^public/#', '', $frame['display_image']);
  }
  unset($frame);
} catch (Exception $e) {
  $frames = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/S.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPACE PHOTOBOOTH • Capture The Cosmos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="<?= BASE_URL ?>css/landing.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
  <!-- GSAP Library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <style>
    /* Animated Background Gradients */
    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }
    
    .hero {
      animation: gradientShift 15s ease infinite;
    }
    
    /* Floating Animation */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }
    
    @keyframes floatRotate {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-30px) rotate(10deg); }
    }
    
    /* Pulse Glow */
    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 20px rgba(193, 255, 114, 0.3); }
      50% { box-shadow: 0 0 40px rgba(193, 255, 114, 0.6), 0 0 60px rgba(193, 255, 114, 0.3); }
    }
    
    /* Rainbow Border */
    @keyframes rainbowBorder {
      0% { border-color: #ff6b9d; }
      25% { border-color: #c1ff72; }
      50% { border-color: #4facfe; }
      75% { border-color: #feca57; }
      100% { border-color: #ff6b9d; }
    }
    
  </style>
  <style>
    /* Cookie Consent Banner */
    .cookie-consent-banner {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
      border-top: 3px solid #000;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
      padding: 1.5rem 2rem;
      z-index: 10000;
      display: none;
      font-family: 'Space Grotesk', sans-serif;
      animation: slideUp 0.4s ease-out;
    }
    
    .cookie-consent-banner.show {
      display: block;
    }
    
    @keyframes slideUp {
      from {
        transform: translateY(100%);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
    
    .cookie-consent-content {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 2rem;
      flex-wrap: wrap;
    }
    
    .cookie-consent-text {
      flex: 1;
      min-width: 300px;
    }
    
    .cookie-consent-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #000;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .cookie-consent-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid #000;
      object-fit: cover;
      flex-shrink: 0;
      box-shadow: 2px 2px 0px #000;
    }
    
    .cookie-consent-description {
      font-size: 0.9rem;
      color: #333;
      line-height: 1.5;
      margin: 0;
    }
    
    .cookie-consent-description a {
      color: #ff6b35;
      text-decoration: underline;
      font-weight: 600;
    }
    
    .cookie-consent-buttons {
      display: flex;
      gap: 1rem;
      flex-shrink: 0;
    }
    
    .cookie-btn {
      padding: 10px 24px;
      border: 2px solid #000;
      border-radius: 8px;
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: all 0.15s ease;
      box-shadow: 2px 2px 0px #000;
    }
    
    .cookie-btn-accept {
      background: linear-gradient(135deg, #c1ff72 0%, #a8ff5e 100%);
      color: #000;
    }
    
    .cookie-btn-accept:hover {
      transform: translate(-2px, -2px);
      box-shadow: 4px 4px 0px #000;
    }
    
    .cookie-btn-decline {
      background: #fff;
      color: #000;
    }
    
    .cookie-btn-decline:hover {
      background: #f8f9fa;
      transform: translate(-2px, -2px);
      box-shadow: 4px 4px 0px #000;
    }
    
    @media (max-width: 768px) {
      .cookie-consent-banner {
        padding: 1rem 1.5rem;
      }
      
      .cookie-consent-content {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }
      
      .cookie-consent-buttons {
        width: 100%;
        flex-direction: column;
      }
      
      .cookie-btn {
        width: 100%;
      }
      
      .cookie-consent-title {
        font-size: 1rem;
      }
      
      .cookie-consent-description {
        font-size: 0.85rem;
      }
    }
  </style>
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
      <a href="#about" class="nav-link">ABOUT</a>
      <a href="#features" class="nav-link">FEATURES</a>
      <a href="#gallery" class="nav-link">GALLERY</a>
      <a href="?p=qa" class="nav-link">HELP</a>
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

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badges">
      <span class="badge badge-pink">✦ COSMIC</span>
      <span class="badge badge-cyan">⚡ STELLAR</span>
      <span class="badge badge-yellow">★ NEBULA</span>
    </div>
    
    <h1 class="hero-title">
      <span class="title-line">CAPTURE</span>
      <span class="title-line gradient">THE</span>
      <span class="title-line">COSMOS</span>
    </h1>
    
    <p class="hero-desc">
      Journey through space with your photobooth.<br/>
      Create • Transform • Explore infinite possibilities.
    </p>
    
    <div class="hero-actions">
      <a href="?p=<?= $isLoggedIn ? 'studio' : 'register' ?>" class="btn btn-primary">
        <span>START CREATING</span>
        <span class="btn-arrow">→</span>
      </a>
      <a href="#about" class="btn btn-outline">LEARN MORE</a>
    </div>
    
    <div class="hero-stats">
      <div class="stat">
        <div class="stat-num">50K+</div>
        <div class="stat-label">PHOTOS</div>
      </div>
      <div class="stat">
        <div class="stat-num">100+</div>
        <div class="stat-label">FRAMES</div>
      </div>
      <div class="stat">
        <div class="stat-num">24/7</div>
        <div class="stat-label">ONLINE</div>
      </div>
    </div>
  </div>
  
  <div class="hero-visual">
    <div class="visual-card card-1">
      <div class="card-tag">TRENDING</div>
      <div class="card-img" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
      <div class="card-title">HOLOGRAPHIC</div>
    </div>
    <div class="visual-card card-2">
      <div class="card-tag">NEW</div>
      <div class="card-img" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
      <div class="card-title">Y2K VIBES</div>
    </div>
    <div class="visual-card card-3">
      <div class="card-tag">EXCLUSIVE</div>
      <div class="card-img" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);"></div>
      <div class="card-title">COOL</div>
    </div>
  </div>
  
  <div class="hero-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
  </div>
</section>

<!-- Marquee -->
<div class="marquee">
  <div class="marquee-content">
    <span>★ FUTURISTIC ★ GEN-Z ★ AESTHETIC ★ FASHION ★ DIGITAL ART ★ Y2K ★ NEON DREAMS ★ CYBER ★ </span>
    <span>★ FUTURISTIC ★ GEN-Z ★ AESTHETIC ★ FASHION ★ DIGITAL ART ★ Y2K ★ NEON DREAMS ★ CYBER ★ </span>
  </div>
</div>

<!-- About Section (Magazine/Newspaper Style) -->
<section class="about-section scrolly-section" id="about" data-scrolly="slide-right">
  <div class="container">
    <div class="magazine-grid">
      
      <!-- Big Title Block -->
      <div class="mag-block block-title">
        <div class="block-number">01</div>
        <h2 class="block-h2">
          ABOUT<br/>
          THE<br/>
          <span class="stroke">FUTURE</span>
        </h2>
      </div>
      
      <!-- Text Block -->
      <div class="mag-block block-text">
        <h3 class="block-h3">WHO WE ARE</h3>
        <p>Space Photobooth isn't just a tool—it's a cosmic journey. We blend stellar technology with mysterious space aesthetics and nebula-inspired filters.</p>
        <p>Born from the infinite expanse of creativity, where every photo becomes a celestial masterpiece.</p>
      </div>
      
      <!-- Visual Block 1 -->
      <div class="mag-block block-img img-1">
        <div class="img-overlay">
          <span class="img-tag">#TRENDY</span>
        </div>
      </div>
      
      <!-- Quote Block -->
      <div class="mag-block block-quote">
        <blockquote>
          "THE FUTURE IS<br/>
          <span class="quote-hl">NOW</span><br/>
          AND IT'S<br/>
          <span class="quote-hl">COLORFUL"</span>
        </blockquote>
        <cite>— FUTUREFRAME TEAM</cite>
      </div>
      
      <!-- Visual Block 2 -->
      <div class="mag-block block-img img-2">
        <div class="img-overlay">
          <span class="img-tag">#VIBES</span>
        </div>
      </div>
      
      <!-- Features Mini -->
      <div class="mag-block block-features">
        <div class="mini-feature">
          <div class="mini-icon">◉</div>
          <h4>ARTISTIC</h4>
          <p>Unlimited filters</p>
        </div>
        <div class="mini-feature">
          <div class="mini-icon">◆</div>
          <h4>INSTANT</h4>
          <p>Real-time magic</p>
        </div>
        <div class="mini-feature">
          <div class="mini-icon">★</div>
          <h4>UNIQUE</h4>
          <p>Alien frames</p>
        </div>
      </div>
      
    </div>
  </div>
</section>

<!-- Features Chaos Grid -->
<section class="features-section scrolly-section" id="features" data-scrolly="zoom-in">
  <div class="container">
    
    <div class="section-header">
      <h2 class="section-title">WHAT WE <span class="gradient">OFFER</span></h2>
      <p class="section-sub">Everything you need for stunning visuals</p>
    </div>
    
    <div class="chaos-grid">
      
      <div class="feature-card fc-large fc-purple">
        <div class="fc-num">01</div>
        <div class="fc-icon">■■○</div>
        <h3>INSTANT CAPTURE</h3>
        <p>Webcam support with countdown timer. Multiple shots in one session.</p>
        <ul class="fc-list">
          <li>✓ Webcam ready</li>
          <li>✓ Timer modes</li>
          <li>✓ Multi-shot</li>
        </ul>
      </div>
      
      <div class="feature-card fc-medium fc-cyan">
        <div class="fc-num">02</div>
        <div class="fc-icon">◆◇◆</div>
        <h3>ALIEN FRAMES</h3>
        <p>100+ unique frames with sci-fi & Y2K aesthetics</p>
      </div>
      
      <div class="feature-card fc-small fc-yellow">
        <div class="fc-icon">✦✧✦</div>
        <h3>FILTERS</h3>
        <p>Real-time effects</p>
      </div>
      
      <div class="feature-card fc-medium fc-pink">
        <div class="fc-num">03</div>
        <div class="fc-icon">▼▲▼</div>
        <h3>SAVE & SHARE</h3>
        <p>Export high-quality. Create photobooks.</p>
      </div>
      
      <div class="feature-card fc-large fc-green">
        <div class="fc-num">04</div>
        <div class="fc-icon">◉◎◉</div>
        <h3>CUSTOMIZE</h3>
        <p>Adjust brightness, contrast, saturation. Add text & stickers.</p>
        <div class="fc-visual"></div>
      </div>
      
      <a href="?p=premium-upgrade" class="feature-card fc-small fc-orange" style="text-decoration: none; color: inherit; display: block;">
        <div class="fc-icon">★☆★</div>
        <h3>PREMIUM</h3>
        <p>Unlock exclusive</p>
      </a>
      
      <div class="feature-card fc-medium fc-violet">
        <div class="fc-num">05</div>
        <div class="fc-icon">□■□</div>
        <h3>RESPONSIVE</h3>
        <p>Works everywhere. Desktop, tablet, mobile.</p>
      </div>
      
    </div>
  </div>
</section>

<!-- Frame Types Section -->
<section class="frame-types-section scrolly-section" data-scrolly="fade-up">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">CHOOSE YOUR <span class="gradient">FRAME</span></h2>
      <p class="section-sub">Two layouts, infinite memories</p>
    </div>
    
    <div class="frame-showcase">
      <!-- 1x4 Frame -->
      <div class="frame-product">
        <div class="frame-badge frame-badge-yellow">Classic layout</div>
        <div class="frame-box">
          <img src="<?= BASE_URL ?>images/tip-frame-1x4.jpg" alt="1x4 Frame Preview" class="frame-preview-img frame-preview-contain" loading="lazy">
        </div>
        <div class="frame-details">
          <h3 class="frame-name">1×4 Frame</h3>
          <p class="frame-subtitle">Strip Style</p>
        </div>
        <div class="frame-info">
          <div class="frame-spec">4 PHOTOS</div>
          <div class="frame-spec">Portrait layout</div>
        </div>
        <p class="frame-desc">
          Perfect for events and photo booths. Four vertical shots in a classic strip format. Great for storytelling.
        </p>
        <button class="frame-btn" onclick="window.location.href='?p=<?= $isLoggedIn ? 'frame' : 'register' ?>'">
          <span>Try Frame</span>
          <span class="btn-arrow">→</span>
        </button>
      </div>
      
      <!-- 2x2 Frame -->
      <div class="frame-product">
        <div class="frame-badge frame-badge-pink">Grid layout</div>
        <div class="frame-box">
          <img src="<?= BASE_URL ?>images/tip-frame-2x2.jpg" alt="2x2 Frame Preview" class="frame-preview-img" loading="lazy">
        </div>
        <div class="frame-details">
          <h3 class="frame-name">2×2 Frame</h3>
          <p class="frame-subtitle">Grid Style</p>
        </div>
        <div class="frame-info">
          <div class="frame-spec">4 PHOTOS</div>
          <div class="frame-spec">Square layout</div>
        </div>
        <p class="frame-desc">
          Balanced grid composition. Four photos in a clean 2×2 arrangement. Perfect for social media posts.
        </p>
        <button class="frame-btn" onclick="window.location.href='?p=<?= $isLoggedIn ? 'frame' : 'register' ?>'">
          <span>Try Frame</span>
          <span class="btn-arrow">→</span>
        </button>
      </div>
    </div>
  </div>
</section>

<style>
.frame-types-section {
  padding: 120px 20px;
  background: var(--white);
  position: relative;
}

.frame-showcase {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 60px;
  max-width: 1000px;
  margin: 0 auto;
  margin-top: 60px;
}

.frame-product {
  background: var(--white);
  border: 2px solid var(--black);
  border-radius: 20px;
  padding: 32px;
  position: relative;
  transition: all 0.4s ease;
}

.frame-product:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
}

.frame-badge {
  position: absolute;
  top: -12px;
  left: 32px;
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
  border: 2px solid var(--black);
  text-transform: uppercase;
  font-family: 'DM Mono', monospace;
}

.frame-badge-yellow {
  background: #fff4d4;
  color: var(--black);
}

.frame-badge-pink {
  background: #ffd4e9;
  color: var(--black);
}

.frame-box {
  width: 100%;
  aspect-ratio: 1;
  background: var(--gray-light);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid var(--black);
}

.frame-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
  display: block;
}

.frame-preview-contain {
  object-fit: contain;
}

.frame-details {
  text-align: center;
  margin-bottom: 16px;
}

.frame-name {
  font-size: 32px;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 8px;
  font-family: 'Space Grotesk', sans-serif;
}

.frame-subtitle {
  font-size: 16px;
  color: var(--gray-mid);
  font-weight: 500;
}

.frame-info {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-bottom: 20px;
}

.frame-spec {
  padding: 6px 16px;
  background: var(--black);
  color: var(--white);
  font-size: 11px;
  font-weight: 700;
  border-radius: 20px;
  letter-spacing: 0.5px;
  font-family: 'DM Mono', monospace;
}

.frame-desc {
  font-size: 15px;
  line-height: 1.6;
  color: #333;
  margin-bottom: 24px;
  text-align: center;
}

.frame-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 16px 32px;
  background: var(--black);
  color: var(--white);
  border: 2px solid var(--black);
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: 'Space Grotesk', sans-serif;
}

.frame-btn:hover {
  transform: translateY(-2px);
  box-shadow: 5px 5px 0 var(--c1ff72);
}

.frame-btn .btn-arrow {
  font-size: 20px;
  transition: transform 0.3s;
}

.frame-btn:hover .btn-arrow {
  transform: translateX(5px);
}

@media (max-width: 768px) {
  .frame-showcase {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  
  .frame-types-section {
    padding: 80px 20px;
  }
}
</style>

<!-- Chaotic Collage Section -->
<section class="collage-section scrolly-section" data-scrolly="fade-in">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">CREATIVE <span class="gradient">CHAOS</span></h2>
      <p class="section-sub">Where art meets disorder</p>
    </div>
    
    <div class="chaos-collage" id="collageContainer">
      <!-- Users can add images here - these are placeholders -->
      <div class="collage-img img-pos-1" data-id="1">
        <img src="<?= BASE_URL ?>images/collage-creative-1.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'">
      </div>
      <div class="collage-img img-pos-2" data-id="2">
        <img src="<?= BASE_URL ?>images/collage-creative-2.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'">
      </div>
      <div class="collage-img img-pos-3" data-id="3">
        <img src="<?= BASE_URL ?>images/collage-creative-3.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #fa709a 0%, #fee140 100%)'">
      </div>
      <div class="collage-img img-pos-4" data-id="4">
        <img src="<?= BASE_URL ?>images/collage-creative-4.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #30cfd0 0%, #330867 100%)'">
      </div>
      <div class="collage-img img-pos-5" data-id="5">
        <img src="<?= BASE_URL ?>images/collage-creative-5.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'">
      </div>
      <div class="collage-img img-pos-6" data-id="6">
        <img src="<?= BASE_URL ?>images/collage-creative-6.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)'">
      </div>
      <div class="collage-img img-pos-7" data-id="7">
        <img src="<?= BASE_URL ?>images/collage-creative-7.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'">
      </div>
      <div class="collage-img img-pos-8" data-id="8">
        <img src="<?= BASE_URL ?>images/collage-creative-8.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #ff6b6b 0%, #feca57 100%)'">
      </div>
      <div class="collage-img img-pos-9" data-id="9">
        <img src="<?= BASE_URL ?>images/collage-creative-9.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #48dbfb 0%, #0abde3 100%)'">
      </div>
      <div class="collage-img img-pos-10" data-id="10">
        <img src="<?= BASE_URL ?>images/collage-creative-10.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #ee5a6f 0%, #f29263 100%)'">
      </div>
      <div class="collage-img img-pos-11" data-id="11">
        <img src="<?= BASE_URL ?>images/collage-creative-11.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #5f27cd 0%, #341f97 100%)'">
      </div>
      <div class="collage-img img-pos-12" data-id="12">
        <img src="<?= BASE_URL ?>images/collage-creative-12.jpg" alt="Creative shot" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%)'">
      </div>
    </div>
  </div>
</section>

<!-- Gallery Preview -->
<section class="gallery-section scrolly-section" id="gallery" data-scrolly="fade-in">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">COMMUNITY <span class="gradient">SHOWCASE</span></h2>
      <p class="section-sub">See what others are creating</p>
    </div>
    
    <div class="gallery-grid">
      <div class="gal-item gi-1">
        <div class="gal-img" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          <img src="<?= BASE_URL ?>images/gallery-alien-theme.jpg" alt="Alien theme" loading="lazy" onerror="this.style.display='none'">
        </div>
        <div class="gal-tag">#ALIEN</div>
      </div>
      <div class="gal-item gi-2">
        <div class="gal-img" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
          <img src="<?= BASE_URL ?>images/gallery-y2k-theme.jpg" alt="Y2K theme" loading="lazy" onerror="this.style.display='none'">
        </div>
        <div class="gal-tag">#Y2K</div>
      </div>
      <div class="gal-item gi-3">
        <div class="gal-img" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
          <img src="<?= BASE_URL ?>images/gallery-neon-theme.jpg" alt="Neon theme" loading="lazy" onerror="this.style.display='none'">
        </div>
        <div class="gal-tag">#NEON</div>
      </div>
      <div class="gal-item gi-4">
        <div class="gal-img" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
          <img src="<?= BASE_URL ?>images/gallery-cyber-theme.jpg" alt="Cyber theme" loading="lazy" onerror="this.style.display='none'">
        </div>
        <div class="gal-tag">#CYBER</div>
      </div>
      <div class="gal-item gi-5">
        <div class="gal-img" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
          <img src="<?= BASE_URL ?>images/gallery-retro-theme.jpg" alt="Retro theme" loading="lazy" onerror="this.style.display='none'">
        </div>
        <div class="gal-tag">#RETRO</div>
      </div>
      <div class="gal-item gi-6">
        <div class="gal-img" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
          <img src="<?= BASE_URL ?>images/gallery-fashion-theme.jpg" alt="Fashion theme" loading="lazy" onerror="this.style.display='none'">
        </div>
        <div class="gal-tag">#FASHION</div>
      </div>
    </div>
    
    <div class="gallery-cta">
      <a href="?p=<?= $isLoggedIn ? 'home' : 'register' ?>" class="btn btn-primary btn-lg">
        <span>CREATE YOUR OWN</span>
        <span class="btn-arrow">→</span>
      </a>
    </div>
  </div>
</section>

<!-- Video Section -->
<section class="video-section scrolly-section" data-scrolly="zoom-in">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">WATCH <span class="gradient">VIDEO</span></h2>
      <p class="section-sub">See how it works</p>
    </div>
    
    <div class="video-wrapper">
      <div class="video-container">
        <!-- Replace VIDEO_ID with your YouTube video ID -->
        <!-- Example: https://www.youtube.com/watch?v=VIDEO_ID -->
        <iframe 
          id="youtube-video"
          src="https://www.youtube.com/embed/VIDEO_ID?rel=0" 
          frameborder="0" 
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
          allowfullscreen
          loading="lazy">
        </iframe>
      </div>
    </div>
  </div>
</section>

<style>
.video-section {
  padding: 4rem 0;
  background: linear-gradient(135deg, rgba(26, 26, 46, 0.05) 0%, rgba(15, 52, 96, 0.05) 100%);
}

.video-wrapper {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 20px;
}

.video-container {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
  overflow: hidden;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  background: #000;
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 16px;
}

@media (max-width: 768px) {
  .video-section {
    padding: 2rem 0;
  }
  
  .video-wrapper {
    padding: 0 15px;
  }
}
</style>

<!-- Available Frames Section -->
<?php if (!empty($frames)): ?>
<section class="frames-showcase-section scrolly-section" data-scrolly="slide-right">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">AVAILABLE <span class="gradient">FRAMES</span></h2>
      <p class="section-sub">Browse our frame collection</p>
    </div>
    
    <div class="frames-marquee">
      <div class="frames-track">
        <?php foreach ($frames as $frame): ?>
        <div class="frame-sample-card">
          <div class="frame-sample-image">
            <img src="<?= BASE_URL . htmlspecialchars($frame['display_image'] ?? $frame['src']) ?>" alt="<?= htmlspecialchars($frame['name']) ?>" loading="lazy" onerror="this.src='<?= BASE_URL ?>images/frame-normal.png'">
            <?php if ($frame['is_premium']): ?>
              <span class="frame-premium-badge">PREMIUM</span>
            <?php endif; ?>
          </div>
          <div class="frame-sample-info">
            <h3 class="frame-sample-name"><?= htmlspecialchars($frame['name']) ?></h3>
            <div class="frame-sample-layout">
              <span class="layout-badge layout-<?= htmlspecialchars($frame['layout']) ?>">
                <?= strtoupper(htmlspecialchars($frame['layout'])) ?>
              </span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <!-- Duplicate for seamless loop -->
        <?php foreach ($frames as $frame): ?>
        <div class="frame-sample-card" aria-hidden="true">
          <div class="frame-sample-image">
            <img src="<?= BASE_URL . htmlspecialchars($frame['display_image'] ?? $frame['src']) ?>" alt="<?= htmlspecialchars($frame['name']) ?>" loading="lazy" onerror="this.src='<?= BASE_URL ?>images/frame-normal.png'">
            <?php if ($frame['is_premium']): ?>
              <span class="frame-premium-badge">PREMIUM</span>
            <?php endif; ?>
          </div>
          <div class="frame-sample-info">
            <h3 class="frame-sample-name"><?= htmlspecialchars($frame['name']) ?></h3>
            <div class="frame-sample-layout">
              <span class="layout-badge layout-<?= htmlspecialchars($frame['layout']) ?>">
                <?= strtoupper(htmlspecialchars($frame['layout'])) ?>
              </span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <p class="frames-caption">Hover to pause or swipe on mobile to browse manually. 🎨</p>
  </div>
</section>
<?php endif; ?>

<!-- User Reviews Section -->
<section class="reviews-section scrolly-section" data-scrolly="slide-left">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">USER <span class="gradient">REVIEWS</span></h2>
      <p class="section-sub">What our community says</p>
    </div>
    
    <div class="reviews-marquee">
      <div class="reviews-track">
        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <span>✨</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Alex Chen</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"This photobooth is gloriously unhinged in the best way. I planned to test a frame and accidentally designed a whole galaxy party invite. Zero regrets. 🚀"</p>
          <div class="review-tag">#PREMIUM USER</div>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
              <span>🌟</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Maya Rodriguez</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"It's giving Y2K pop diva in 4K. The photobook builder keeps my chaotic selfies organized which feels like sorcery. 💫"</p>
          <div class="review-tag">#CREATOR</div>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
              <span>⚡</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Jordan Kim</h3>
              <div class="review-stars">★★★★☆</div>
            </div>
          </div>
          <p class="review-text">"Frame Composer = therapy. I drag, drop, and suddenly the collage looks like it hired an art director. Points deducted only because daylight savings still exists. 🎨"</p>
          <div class="review-tag">#PHOTOGRAPHER</div>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
              <span>💫</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Sam Taylor</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"Threw a birthday rave and the neon frames out-glowed the cake sparkler. Guests now think I own a tiny sci-fi studio. 🔮"</p>
          <div class="review-tag">#EVENT PLANNER</div>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #fcb045 0%, #fd1d1d 50%, #833ab4 100%);">
              <span>🎧</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Dex Harper</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"I DJ weddings and this booth keeps the dance floor packed even while I switch playlists. People queue just to add glitter moustaches. 🔊"</p>
          <div class="review-tag">#AFTERPARTY DJ</div>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
              <span>🌈</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Nia Solis</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"My teen art club refuses to leave meetings because we're busy rating each other's sticker chaos. This counts as community building, right? 🎨"</p>
          <div class="review-tag">#GIF QUEEN</div>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #1fa2ff 0%, #12d8fa 50%, #a6ffcb 100%);">
              <span>📎</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Priya Mehta</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"Used it for a corporate offsite. Our CFO is now a holographic llama. Productivity weirdly up 27%. Coincidence? 🦙"</p>
          <div class="review-tag">#TEAM BUILDER</div>
        </article>

        <!-- Duplicate set for seamless loop -->
        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <span>✨</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Alex Chen</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"This photobooth is gloriously unhinged in the best way. I planned to test a frame and accidentally designed a whole galaxy party invite. Zero regrets. 🚀"</p>
          <div class="review-tag">#PREMIUM USER</div>
        </article>

        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
              <span>🌟</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Maya Rodriguez</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"It's giving Y2K pop diva in 4K. The photobook builder keeps my chaotic selfies organized which feels like sorcery. 💫"</p>
          <div class="review-tag">#CREATOR</div>
        </article>

        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
              <span>⚡</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Jordan Kim</h3>
              <div class="review-stars">★★★★☆</div>
            </div>
          </div>
          <p class="review-text">"Frame Composer = therapy. I drag, drop, and suddenly the collage looks like it hired an art director. Points deducted only because daylight savings still exists. 🎨"</p>
          <div class="review-tag">#PHOTOGRAPHER</div>
        </article>

        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
              <span>💫</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Sam Taylor</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"Threw a birthday rave and the neon frames out-glowed the cake sparkler. Guests now think I own a tiny sci-fi studio. 🔮"</p>
          <div class="review-tag">#EVENT PLANNER</div>
        </article>

        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #fcb045 0%, #fd1d1d 50%, #833ab4 100%);">
              <span>🎧</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Dex Harper</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"I DJ weddings and this booth keeps the dance floor packed even while I switch playlists. People queue just to add glitter moustaches. 🔊"</p>
          <div class="review-tag">#AFTERPARTY DJ</div>
        </article>

        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
              <span>🌈</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Nia Solis</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"My teen art club refuses to leave meetings because we're busy rating each other's sticker chaos. This counts as community building, right? 🎨"</p>
          <div class="review-tag">#GIF QUEEN</div>
        </article>

        <article class="review-card" aria-hidden="true">
          <div class="review-header">
            <div class="review-avatar" style="background: linear-gradient(135deg, #1fa2ff 0%, #12d8fa 50%, #a6ffcb 100%);">
              <span>📎</span>
            </div>
            <div class="review-info">
              <h3 class="review-name">Priya Mehta</h3>
              <div class="review-stars">★★★★★</div>
            </div>
          </div>
          <p class="review-text">"Used it for a corporate offsite. Our CFO is now a holographic llama. Productivity weirdly up 27%. Coincidence? 🦙"</p>
          <div class="review-tag">#TEAM BUILDER</div>
        </article>
      </div>
    </div>
    <p class="reviews-caption">Hover to pause the gossip stream or swipe on mobile to gossip manually. 😎</p>
  </div>
</section>

<style>
/* Frames Showcase Section */
.frames-showcase-section {
  padding: 5rem 0;
  background: linear-gradient(135deg, rgba(193, 255, 114, 0.03) 0%, rgba(140, 82, 255, 0.03) 100%);
  position: relative;
  overflow: hidden;
}

.frames-showcase-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 30%, rgba(193, 255, 114, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(140, 82, 255, 0.05) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}

.frames-showcase-section .container {
  position: relative;
  z-index: 1;
}

.frames-marquee {
  margin-top: 3rem;
  overflow: hidden;
  padding: 0.5rem 0;
  mask-image: linear-gradient(90deg, transparent, rgba(0,0,0,0.85) 10%, rgba(0,0,0,0.85) 90%, transparent);
  -webkit-mask-image: linear-gradient(90deg, transparent, rgba(0,0,0,0.85) 10%, rgba(0,0,0,0.85) 90%, transparent);
}

.frames-track {
  display: flex;
  gap: 1.5rem;
  width: max-content;
  animation: frames-loop 35s linear infinite;
}

.frames-track:hover {
  animation-play-state: paused;
}

@keyframes frames-loop {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* Ensure single row layout */
.frames-marquee {
  display: flex;
  flex-direction: column;
}

.frame-sample-card {
  background: var(--white);
  border: 2px solid var(--black);
  border-radius: 16px;
  padding: 1rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  overflow: hidden;
  min-width: 220px;
  max-width: 260px;
  display: flex;
  flex-direction: column;
}

.frame-sample-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #c1ff72 0%, #8c52ff 50%, #ff6b9d 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.frame-sample-card:hover {
  transform: translateY(-8px) rotate(-0.5deg);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.frame-sample-card:hover::before {
  opacity: 1;
}

.frame-sample-image {
  position: relative;
  width: 100%;
  aspect-ratio: 4/5;
  background: #f8f9fa;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.frame-sample-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.frame-sample-card:hover .frame-sample-image img {
  transform: scale(1.05);
}

.frame-premium-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
  color: var(--black);
  font-size: 10px;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  border: 1px solid var(--black);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.frame-sample-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.frame-sample-name {
  font-size: 1rem;
  font-weight: 600;
  color: var(--black);
  margin: 0;
  font-family: 'Space Grotesk', sans-serif;
}

.frame-sample-layout {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.layout-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid var(--black);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-family: 'DM Mono', monospace;
}

.layout-badge.layout-vertical {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: var(--white);
}

.layout-badge.layout-horizontal {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: var(--white);
}

.layout-badge.layout-square {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: var(--white);
}

.frames-caption {
  text-align: center;
  margin-top: 1.5rem;
  font-size: 0.875rem;
  color: var(--gray-mid);
  font-style: italic;
}

@media (max-width: 768px) {
  .frames-showcase-section {
    padding: 3rem 0;
  }
  
  .frame-sample-card {
    min-width: 200px;
    max-width: 240px;
  }
}

/* Reviews Section */
.reviews-section {
  padding: 5rem 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
  position: relative;
  overflow: hidden;
}

.reviews-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 30%, rgba(193, 255, 114, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(140, 82, 255, 0.05) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}

.reviews-section .container {
  position: relative;
  z-index: 1;
}

.reviews-marquee {
  margin-top: 3rem;
  overflow: hidden;
  padding: 0.5rem 0;
  mask-image: linear-gradient(90deg, transparent, rgba(0,0,0,0.85) 10%, rgba(0,0,0,0.85) 90%, transparent);
  -webkit-mask-image: linear-gradient(90deg, transparent, rgba(0,0,0,0.85) 10%, rgba(0,0,0,0.85) 90%, transparent);
}

.reviews-track {
  display: flex;
  gap: 1.5rem;
  width: max-content;
  animation: reviews-loop 40s linear infinite;
}

.reviews-track:hover {
  animation-play-state: paused;
}

@keyframes reviews-loop {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.review-card {
  background: var(--white);
  border: 2px solid var(--black);
  border-radius: 16px;
  padding: 1.5rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  overflow: hidden;
  min-width: 260px;
  max-width: 320px;
}

.review-card::before {
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

.review-card:hover {
  transform: translateY(-8px) rotate(-0.5deg);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.review-card:hover::before {
  opacity: 1;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.review-avatar {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
  border: 2px solid var(--black);
  box-shadow: inset 0 0 12px rgba(255, 255, 255, 0.35);
}

.review-avatar span {
  animation: avatar-bop 6s ease-in-out infinite;
  display: inline-block;
}

@keyframes avatar-bop {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-2px) scale(1.1); }
}

.review-info {
  flex: 1;
}

.review-name {
  font-size: 1rem;
  font-weight: 600;
  color: var(--black);
  margin: 0 0 0.25rem 0;
  font-family: 'Space Grotesk', sans-serif;
}

.review-stars {
  color: #ffbd59;
  font-size: 0.9rem;
  letter-spacing: 2px;
  text-shadow: 0 0 10px rgba(255, 189, 89, 0.5);
}

.review-text {
  color: #333;
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0 0 1rem 0;
  font-family: 'Space Grotesk', sans-serif;
}

.review-tag {
  display: inline-block;
  padding: 0.35rem 0.85rem;
  background: var(--black);
  color: var(--white);
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 999px;
}

.reviews-caption {
  text-align: center;
  font-size: 0.9rem;
  margin-top: 1.5rem;
  color: rgba(0,0,0,0.6);
}
  border-radius: 20px;
  font-family: 'DM Mono', monospace;
  letter-spacing: 0.5px;
}

@media (max-width: 768px) {
  .reviews-section {
    padding: 3rem 0;
  }
  
  .reviews-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-top: 2rem;
  }
  
  .review-card {
    padding: 1.25rem;
  }
}
</style>

<!-- Pro Tips -->
<section class="tips-section scrolly-section" data-scrolly="slide-right">
  <div class="container">
    <div class="tips-grid">
      
      <div class="tips-header">
        <h2 class="section-title">PRO <span class="gradient">TIPS</span></h2>
        <p>Level up your game</p>
      </div>
      
      <div class="tip-card tc-1">
        <div class="tip-image-wrapper">
          <img src="<?= BASE_URL ?>images/tip-lighting.jpg" alt="Lighting tip" class="tip-image" loading="lazy" onerror="this.style.display='none'">
          <div class="tip-emoji"></div>
        </div>
        <h3>LIGHTING</h3>
        <p>Natural or soft LED lights work best</p>
      </div>
      
      <div class="tip-card tc-2">
        <div class="tip-image-wrapper">
          <img src="<?= BASE_URL ?>images/tip-colormatch.jpg" alt="Color match tip" class="tip-image" loading="lazy" onerror="this.style.display='none'">
          <div class="tip-emoji"></div>
        </div>
        <h3>COLOR MATCH</h3>
        <p>Coordinate outfit with frame themes</p>
      </div>
      
      <div class="tip-card tc-3">
        <div class="tip-image-wrapper">
          <img src="<?= BASE_URL ?>images/tip-composition.jpg" alt="Composition tip" class="tip-image" loading="lazy" onerror="this.style.display='none'">
          <div class="tip-emoji"></div>
        </div>
        <h3>COMPOSITION</h3>
        <p>Center or use rule of thirds</p>
      </div>
      
      <div class="tip-card tc-4">
        <div class="tip-image-wrapper">
          <img src="<?= BASE_URL ?>images/tip-experiment.jpg" alt="Experiment tip" class="tip-image" loading="lazy" onerror="this.style.display='none'">
          <div class="tip-emoji"></div>
        </div>
        <h3>EXPERIMENT</h3>
        <p>Try unexpected combinations!</p>
      </div>
      
      <div class="tip-card tc-5">
        <div class="tip-image-wrapper">
          <img src="<?= BASE_URL ?>images/tip-multiple.jpg" alt="Multiple shots tip" class="tip-image" loading="lazy" onerror="this.style.display='none'">
          <div class="tip-emoji"></div>
        </div>
        <h3>MULTIPLE</h3>
        <p>Take several shots to choose from</p>
      </div>
      
      <a href="?p=premium-upgrade" class="tip-card tc-6" style="text-decoration: none; color: inherit; display: block;">
        <div class="tip-image-wrapper">
          <img src="<?= BASE_URL ?>images/tip-premium.jpg" alt="Premium tip" class="tip-image" loading="lazy" onerror="this.style.display='none'">
          <div class="tip-emoji">★</div>
        </div>
        <h3>PREMIUM</h3>
        <p>Unlock exclusive frames & features</p>
      </a>
      
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section scrolly-section" data-scrolly="fade-up">
  <div class="cta-content">
    <h2 class="cta-title">
      READY TO CREATE<br/>
      <span class="gradient">SOMETHING ALIEN?</span>
    </h2>
    <p class="cta-text">Join thousands creating futuristic content</p>
    <div class="cta-btns">
      <?php if ($isLoggedIn): ?>
        <a href="?p=studio" class="btn btn-primary btn-xl">
          <span>GO TO STUDIO</span>
          <span class="btn-arrow">→</span>
        </a>
      <?php else: ?>
        <a href="?p=register" class="btn btn-primary btn-xl">
          <span>SIGN UP FREE</span>
          <span class="btn-arrow">→</span>
        </a>
        <a href="?p=login" class="btn btn-outline btn-xl">LOGIN</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="cta-shapes">
    <div class="cta-shape cs-1"></div>
    <div class="cta-shape cs-2"></div>
    <div class="cta-shape cs-3"></div>
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
    
    <!-- Store Location Map -->
    <div class="footer-map-section">
      <h4 class="map-title">📍 STORE LOCATION</h4>
      <div class="footer-map-wrapper">
        <iframe 
          id="storeMap"
          src="" 
          width="100%" 
          height="300" 
          style="border:0; border-radius: 12px;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"
          class="footer-map">
        </iframe>
        <div class="map-link-wrapper">
          <a href="https://maps.app.goo.gl/qosKrwj5p4HGiWvf6" target="_blank" rel="noopener noreferrer" class="map-link">
            <span>View on Google Maps</span>
            <span>→</span>
          </a>
        </div>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2025 FutureFrame. All rights reserved.</p>
      <p>Show your style</p>
    </div>
  </div>
</footer>

<script>
// Mobile menu
document.getElementById('menuToggle')?.addEventListener('click', function() {
  document.querySelector('.nav-menu').classList.toggle('active');
  this.classList.toggle('active');
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) target.scrollIntoView({ behavior: 'smooth' });
  });
});

// Parallax shapes
let ticking = false;
window.addEventListener('scroll', function() {
  if (!ticking) {
    window.requestAnimationFrame(function() {
      const scroll = window.pageYOffset;
      document.querySelectorAll('.hero-shapes .shape').forEach((s, i) => {
        const speed = 0.3 + (i * 0.15);
        s.style.transform = `translateY(${scroll * speed}px) rotate(${scroll * 0.05}deg)`;
      });
      ticking = false;
    });
    ticking = true;
  }
});

// Scrollytelling - Exhibition Tour Effect
(function() {
  const scrollySections = document.querySelectorAll('.scrolly-section');
  
  const scrollyObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const section = entry.target;
        const animation = section.dataset.scrolly || 'fade-in';
        section.classList.add('scrolly-visible', `scrolly-${animation}`);
        
        // Stagger children animations - separate handling for different elements
        const regularChildren = section.querySelectorAll('.feature-card, .step-card, .tip-card, .gal-item, .frame-product, .review-card');
        regularChildren.forEach((child, index) => {
          setTimeout(() => {
            child.style.opacity = '1';
            child.style.transform = 'none'; // Remove transform to allow hover effects
          }, index * 100);
        });
        
        // Mag-blocks - keep translateY(0) to allow CSS hover effects
        const magBlocks = section.querySelectorAll('.mag-block');
        magBlocks.forEach((block, index) => {
          setTimeout(() => {
            block.style.opacity = '1';
            block.style.transform = 'translateY(0)'; // Keep translateY(0) to allow hover effects
          }, index * 100);
        });
        
        // Collage images - preserve their rotate transforms from CSS
        const collageImgs = section.querySelectorAll('.collage-img:not(.dragging)');
        collageImgs.forEach((img, index) => {
          setTimeout(() => {
            img.style.opacity = '1';
            // Don't override transform - let CSS handle rotate transforms
            // Only set opacity, preserve existing transform from CSS classes
            if (!img.style.transform || img.style.transform === 'none') {
              // If no transform set, check computed style for rotate
              const computed = window.getComputedStyle(img);
              if (computed.transform && computed.transform !== 'none') {
                // Keep the computed transform (includes rotate)
                img.style.transform = computed.transform;
              } else {
                img.style.transform = 'scale(1)'; // Default scale
              }
            }
          }, index * 100);
        });
      }
    });
  }, { 
    threshold: 0.15, 
    rootMargin: '0px 0px -100px 0px' 
  });
  
  scrollySections.forEach(section => {
    scrollyObserver.observe(section);
  });
  
  // Original observer for non-scrolly elements
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  document.querySelectorAll('.feature-card:not(.scrolly-section .feature-card), .step-card:not(.scrolly-section .step-card), .tip-card:not(.scrolly-section .tip-card), .gal-item:not(.scrolly-section .gal-item), .mag-block:not(.scrolly-section .mag-block), .collage-img:not(.scrolly-section .collage-img)').forEach(el => {
    observer.observe(el);
  });
})();

// Auto-hide header on scroll (footer always visible)
(function() {
  let lastScrollTop = 0;
  let scrollThreshold = 50;
  let isScrolling = false;
  let scrollTimeout;
  const header = document.querySelector('.main-nav');
  
  if (!header) return;
  
  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Always show when near top
    if (scrollTop < scrollThreshold) {
      header.classList.remove('nav-hidden');
      lastScrollTop = scrollTop;
      return;
    }
    
    // Determine scroll direction
    if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
      // Scrolling down - hide
      header.classList.add('nav-hidden');
    } else if (scrollTop < lastScrollTop) {
      // Scrolling up - show
      header.classList.remove('nav-hidden');
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  }
  
  // Throttled scroll handler
  window.addEventListener('scroll', function() {
    if (!isScrolling) {
      window.requestAnimationFrame(function() {
        handleScroll();
        isScrolling = false;
      });
      isScrolling = true;
    }
    
    // Clear timeout
    clearTimeout(scrollTimeout);
    
    // Show header when scroll stops
    scrollTimeout = setTimeout(function() {
      header.classList.remove('nav-hidden');
    }, 1500);
  }, { passive: true });
})();

// ===== COOL & FUNNY INTERACTIVE ANIMATIONS =====
(function(){
  // Hero badge wiggle on load
  setTimeout(() => {
    document.querySelectorAll('.hero .badge').forEach((badge, i) => {
      setTimeout(() => {
        badge.style.animation = 'badge-wiggle 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
      }, i * 100);
    });
  }, 500);
  
  // Visual cards float animation
  document.querySelectorAll('.visual-card').forEach((card, i) => {
    setTimeout(() => {
      card.style.animation = `card-float-${i + 1} 4s ease-in-out infinite`;
    }, 800 + (i * 200));
  });
  
  // Feature cards bounce on hover
  document.querySelectorAll('.feature-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.animation = 'feature-bounce 0.5s ease';
    });
    card.addEventListener('animationend', function() {
      this.style.animation = '';
    });
  });
  
  // Frame products shake on hover
  document.querySelectorAll('.frame-product').forEach(product => {
    product.addEventListener('mouseenter', function() {
      const slots = this.querySelectorAll('.frame-slot');
      slots.forEach((slot, i) => {
        setTimeout(() => {
          slot.style.animation = 'slot-pulse 0.4s ease';
        }, i * 50);
      });
    });
  });
  
  // Tip cards shake emoji on hover
  document.querySelectorAll('.tip-card').forEach(card => {
    const emoji = card.querySelector('.tip-emoji');
    if (emoji) {
      card.addEventListener('mouseenter', () => {
        emoji.style.animation = 'emoji-dance 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
      });
      card.addEventListener('animationend', () => {
        emoji.style.animation = '';
      });
    }
  });
  
  // Gallery items flip on hover
  document.querySelectorAll('.gal-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.animation = 'gallery-wobble 0.5s ease';
    });
    item.addEventListener('animationend', function() {
      this.style.animation = '';
    });
  });
  
  // Review cards wave on scroll into view
  const reviewObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animation = 'review-pop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
      }
    });
  }, { threshold: 0.5 });
  
  document.querySelectorAll('.review-card').forEach(card => {
    reviewObserver.observe(card);
  });
  
  // Collage images random rotation on load
  setTimeout(() => {
    document.querySelectorAll('.collage-img').forEach((img, i) => {
      setTimeout(() => {
        img.style.animation = `collage-spin-in 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards`;
      }, i * 80);
    });
  }, 300);
  
  // Stats counter animation
  const statNums = document.querySelectorAll('.stat-num');
  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const text = entry.target.textContent;
        const hasPlus = text.includes('+');
        const number = parseInt(text.replace(/[^0-9]/g, ''));
        
        if (number) {
          let current = 0;
          const increment = Math.ceil(number / 30);
          const timer = setInterval(() => {
            current += increment;
            if (current >= number) {
              entry.target.textContent = text;
              clearInterval(timer);
            } else {
              entry.target.textContent = current + (hasPlus ? '+' : '');
            }
          }, 50);
        }
        statsObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.8 });
  
  statNums.forEach(stat => statsObserver.observe(stat));
  
  // Add sparkle effect on button hover
  document.querySelectorAll('.btn-primary, .frame-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function(e) {
      const sparkle = document.createElement('span');
      sparkle.className = 'btn-sparkle';
      sparkle.style.left = Math.random() * 100 + '%';
      sparkle.style.top = Math.random() * 100 + '%';
      this.appendChild(sparkle);
      
      setTimeout(() => sparkle.remove(), 1000);
    });
  });
})();
</script>

<style>
/* Auto-hide header on scroll */
.main-nav {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
              opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform, opacity;
}

.main-nav.nav-hidden {
  transform: translateY(-100%);
  opacity: 0;
  pointer-events: none;
}


/* Smooth scroll behavior */
html {
  scroll-behavior: smooth;
}

/* Scrollytelling - Exhibition Tour Styles */
.scrolly-section {
  opacity: 0;
  transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), 
              transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: opacity, transform;
}

.scrolly-section.scrolly-fade-in {
  transform: translateY(30px);
}

.scrolly-section.scrolly-fade-up {
  transform: translateY(60px);
}

.scrolly-section.scrolly-slide-left {
  transform: translateX(80px);
}

.scrolly-section.scrolly-slide-right {
  transform: translateX(-80px);
}

.scrolly-section.scrolly-zoom-in {
  transform: scale(0.95);
}

.scrolly-section.scrolly-visible {
  opacity: 1;
  transform: translateY(0) translateX(0) scale(1);
}

/* Stagger children animations - only apply initial state */
.scrolly-section:not(.scrolly-visible) .feature-card,
.scrolly-section:not(.scrolly-visible) .step-card,
.scrolly-section:not(.scrolly-visible) .tip-card,
.scrolly-section:not(.scrolly-visible) .gal-item,
.scrolly-section:not(.scrolly-visible) .mag-block,
.scrolly-section:not(.scrolly-visible) .frame-product,
.scrolly-section:not(.scrolly-visible) .review-card {
  opacity: 0;
  transform: translateY(20px);
}

/* Collage images - only animate opacity on initial reveal, preserve rotate transforms */
.scrolly-section:not(.scrolly-visible) .collage-img:not(.dragging) {
  opacity: 0;
  /* Don't override transform - preserve rotate from CSS classes like .img-pos-1, etc. */
}

/* Once visible, remove scrollytelling transforms to allow hover effects */
.scrolly-section.scrolly-visible .feature-card,
.scrolly-section.scrolly-visible .step-card,
.scrolly-section.scrolly-visible .tip-card,
.scrolly-section.scrolly-visible .gal-item,
.scrolly-section.scrolly-visible .frame-product,
.scrolly-section.scrolly-visible .review-card,
.scrolly-section.scrolly-visible .collage-img:not(.dragging) {
  opacity: 1;
  transform: none; /* Remove transform to allow hover effects */
}

/* Mag-blocks need to keep their initial transform for rotate effects */
.scrolly-section.scrolly-visible .mag-block {
  opacity: 1;
  transform: translateY(0); /* Keep translateY(0) but allow rotate in hover */
}

/* Smooth reveal transition */
.scrolly-section .feature-card,
.scrolly-section .step-card,
.scrolly-section .tip-card,
.scrolly-section .gal-item,
.scrolly-section .mag-block,
.scrolly-section .frame-product,
.scrolly-section .review-card,
.scrolly-section .collage-img:not(.dragging) {
  transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1),
              transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* When dragging, remove scrollytelling transform */
.scrolly-section .collage-img.dragging {
  transform: none !important;
  transition: none !important;
}

/* Parallax effect for sections */
.scrolly-section[data-scrolly="fade-up"] {
  transition-delay: 0.1s;
}

.scrolly-section[data-scrolly="slide-left"] {
  transition-delay: 0.15s;
}

.scrolly-section[data-scrolly="slide-right"] {
  transition-delay: 0.15s;
}

.scrolly-section[data-scrolly="zoom-in"] {
  transition-delay: 0.2s;
}

/* Drag and Drop styles for collage */
.collage-img {
  cursor: grab;
  transition: transform 0.2s, opacity 0.2s, z-index 0.2s;
}

.collage-img:active {
  cursor: grabbing;
}

.collage-img.dragging {
  opacity: 0.8 !important;
  transform: scale(1.05) !important;
  z-index: 1000 !important;
  transition: none !important;
}

.drag-placeholder {
  min-height: 100px;
  border: 2px dashed #c1ff72 !important;
  background: rgba(193, 255, 114, 0.1) !important;
  opacity: 0.3;
}

/* ===== COOL & FUNNY ANIMATION KEYFRAMES ===== */

/* Badge wiggle animation */
@keyframes badge-wiggle {
  0%, 100% { transform: rotate(0deg) scale(1); }
  25% { transform: rotate(-8deg) scale(1.05); }
  50% { transform: rotate(8deg) scale(1.1); }
  75% { transform: rotate(-5deg) scale(1.05); }
}

/* Visual cards floating */
@keyframes card-float-1 {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-15px) rotate(2deg); }
}

@keyframes card-float-2 {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(-3deg); }
}

@keyframes card-float-3 {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-12px) rotate(1deg); }
}

/* Feature card bounce */
@keyframes feature-bounce {
  0% { transform: translateY(0) scale(1); }
  30% { transform: translateY(-15px) scale(1.05); }
  50% { transform: translateY(-8px) scale(1.02); }
  70% { transform: translateY(-12px) scale(1.03); }
  100% { transform: translateY(0) scale(1); }
}

/* Frame slot pulse */
@keyframes slot-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.8; }
}

/* Emoji dance */
@keyframes emoji-dance {
  0%, 100% { transform: rotate(0deg) scale(1); }
  20% { transform: rotate(-15deg) scale(1.2); }
  40% { transform: rotate(15deg) scale(1.3); }
  60% { transform: rotate(-10deg) scale(1.2); }
  80% { transform: rotate(10deg) scale(1.1); }
}

/* Gallery wobble */
@keyframes gallery-wobble {
  0%, 100% { transform: rotate(0deg) scale(1); }
  25% { transform: rotate(3deg) scale(1.05); }
  75% { transform: rotate(-3deg) scale(1.05); }
}

/* Review pop */
@keyframes review-pop {
  0% { transform: scale(0.9) rotate(-2deg); opacity: 0; }
  50% { transform: scale(1.05) rotate(1deg); }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

/* Collage spin in */
@keyframes collage-spin-in {
  0% { 
    opacity: 0; 
    transform: scale(0.3) rotate(-180deg);
  }
  60% { 
    transform: scale(1.1) rotate(10deg);
  }
  100% { 
    opacity: 1; 
    transform: scale(1) rotate(var(--hover-rotate, 0deg));
  }
}

/* Button sparkle */
@keyframes sparkle-fade {
  0% { 
    opacity: 1; 
    transform: scale(0) rotate(0deg);
  }
  50% { 
    opacity: 1; 
    transform: scale(1) rotate(180deg);
  }
  100% { 
    opacity: 0; 
    transform: scale(0) rotate(360deg);
  }
}

.btn-sparkle {
  position: absolute;
  width: 8px;
  height: 8px;
  background: var(--c1ff72);
  border-radius: 50%;
  pointer-events: none;
  animation: sparkle-fade 1s ease-out forwards;
  box-shadow: 0 0 10px var(--c1ff72);
}

/* Add playful hover effects */
.hero-badges .badge:hover {
  animation: badge-wiggle 0.5s ease !important;
}

/* Badge text colors - COSMIC, STELLAR, and NEBULA in black */
.hero-badges .badge-pink,
.hero-badges .badge-cyan,
.hero-badges .badge-yellow {
  color: #000 !important;
}

.visual-card:hover {
  animation: feature-bounce 0.6s ease !important;
}

.tip-emoji {
  display: inline-block;
  transition: transform 0.3s;
}

.section-title:hover {
  animation: text-rainbow 2s linear infinite;
}

@keyframes text-rainbow {
  0% { filter: hue-rotate(0deg); }
  100% { filter: hue-rotate(360deg); }
}

/* Parallax floating shapes */
.hero-shapes .shape {
  animation: shape-float 8s ease-in-out infinite;
}

.shape-1 { animation-delay: 0s; }
.shape-2 { animation-delay: 2s; }
.shape-3 { animation-delay: 4s; }

@keyframes shape-float {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  25% { transform: translate(20px, -30px) rotate(90deg); }
  50% { transform: translate(-15px, -50px) rotate(180deg); }
  75% { transform: translate(30px, -20px) rotate(270deg); }
}

/* CTA shapes rotate */
.cta-shapes .cta-shape {
  animation: cta-rotate 15s linear infinite;
}

.cs-1 { animation-duration: 20s; }
.cs-2 { animation-duration: 15s; animation-direction: reverse; }
.cs-3 { animation-duration: 25s; }

@keyframes cta-rotate {
  0% { transform: rotate(0deg) scale(1); }
  50% { transform: rotate(180deg) scale(1.2); }
  100% { transform: rotate(360deg) scale(1); }
}

/* Marquee scroll faster on hover */
.marquee:hover .marquee-content {
  animation-duration: 15s !important;
}

/* Add perspective to 3D effects */
.features-section,
.gallery-section,
.frame-types-section {
  perspective: 1000px;
}

/* Accessibility: Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>

<!-- Drag and Drop for Collage Images using HTML5 Drag API -->
<script>
(function initCollageDragDrop() {
  const container = document.getElementById('collageContainer');
  if (!container) return;

  let draggedElement = null;
  let draggedOffset = { x: 0, y: 0 };
  let isDragging = false;

  container.querySelectorAll('.collage-img').forEach(item => {
    item.style.cursor = 'grab';
    item.style.userSelect = 'none';
    item.style.position = 'absolute'; // Ensure absolute positioning

    // Mouse down - start drag
    item.addEventListener('mousedown', (e) => {
      if (e.button !== 0) return; // Only left mouse button
      
      isDragging = true;
      draggedElement = item;
      
      // Add dragging class to override scrollytelling
      item.classList.add('dragging');
      
      const rect = item.getBoundingClientRect();
      const containerRect = container.getBoundingClientRect();
      
      draggedOffset.x = e.clientX - rect.left;
      draggedOffset.y = e.clientY - rect.top;
      
      item.style.cursor = 'grabbing';
      item.style.zIndex = '1000';
      item.style.opacity = '0.8';
      item.style.transition = 'none';
      item.style.transform = 'none'; // Remove scrollytelling transform
      
      e.preventDefault();
    });

    // Mouse move - update position
    document.addEventListener('mousemove', (e) => {
      if (!isDragging || !draggedElement) return;
      
      const containerRect = container.getBoundingClientRect();
      let newX = e.clientX - containerRect.left - draggedOffset.x;
      let newY = e.clientY - containerRect.top - draggedOffset.y;
      
      // Keep within container bounds
      newX = Math.max(0, Math.min(newX, containerRect.width - draggedElement.offsetWidth));
      newY = Math.max(0, Math.min(newY, containerRect.height - draggedElement.offsetHeight));
      
      draggedElement.style.left = newX + 'px';
      draggedElement.style.top = newY + 'px';
    });

    // Mouse up - end drag
    document.addEventListener('mouseup', (e) => {
      if (!isDragging || !draggedElement) return;
      
      draggedElement.classList.remove('dragging');
      draggedElement.style.cursor = 'grab';
      draggedElement.style.zIndex = '';
      draggedElement.style.opacity = '1';
      draggedElement.style.transition = '';
      
      isDragging = false;
      draggedElement = null;
    });

    // Touch support for mobile
    item.addEventListener('touchstart', (e) => {
      if (e.touches.length !== 1) return;
      
      isDragging = true;
      draggedElement = item;
      
      // Add dragging class to override scrollytelling
      item.classList.add('dragging');
      
      const touch = e.touches[0];
      const rect = item.getBoundingClientRect();
      
      draggedOffset.x = touch.clientX - rect.left;
      draggedOffset.y = touch.clientY - rect.top;
      
      item.style.zIndex = '1000';
      item.style.opacity = '0.8';
      item.style.transition = 'none';
      item.style.transform = 'none'; // Remove scrollytelling transform
      
      e.preventDefault();
    });

    document.addEventListener('touchmove', (e) => {
      if (!isDragging || !draggedElement || e.touches.length !== 1) return;
      
      const touch = e.touches[0];
      const containerRect = container.getBoundingClientRect();
      let newX = touch.clientX - containerRect.left - draggedOffset.x;
      let newY = touch.clientY - containerRect.top - draggedOffset.y;
      
      newX = Math.max(0, Math.min(newX, containerRect.width - draggedElement.offsetWidth));
      newY = Math.max(0, Math.min(newY, containerRect.height - draggedElement.offsetHeight));
      
      draggedElement.style.left = newX + 'px';
      draggedElement.style.top = newY + 'px';
      
      e.preventDefault();
    });

    document.addEventListener('touchend', (e) => {
      if (!isDragging || !draggedElement) return;
      
      draggedElement.classList.remove('dragging');
      draggedElement.style.zIndex = '';
      draggedElement.style.opacity = '1';
      draggedElement.style.transition = '';
      
      isDragging = false;
      draggedElement = null;
    });
  });
})();

// First-time visitor detection for auth button
(function() {
  const authBtn = document.getElementById('authBtn');
  if (!authBtn) return; // Only run if user is not logged in
  
  // Check if user has visited before
  const hasVisited = localStorage.getItem('hasVisitedBefore');
  
  if (!hasVisited) {
    // First-time visitor - show SIGN UP (already set in HTML)
    authBtn.textContent = 'SIGN UP';
    authBtn.href = '?p=register';
    // Mark that user has visited after they see the page
    localStorage.setItem('hasVisitedBefore', 'true');
  } else {
    // Returning visitor - show LOGIN
    authBtn.textContent = 'LOGIN';
    authBtn.href = '?p=login';
  }
})();

// Animate landing page on load
(function() {
  // Animate landing page entrance
  function animateLandingPage() {
    const tl = gsap.timeline();
    
    // Animate navigation
    tl.to('.main-nav', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      ease: 'power3.out'
    })
    // Animate hero badges
    .to('.hero-badges .badge', {
      opacity: 1,
      y: 0,
      duration: 0.5,
      stagger: 0.1,
      ease: 'back.out(1.5)'
    }, '-=0.3')
    // Animate hero title lines
    .to('.hero-title .title-line', {
      opacity: 1,
      y: 0,
      duration: 0.8,
      stagger: 0.15,
      ease: 'power4.out'
    }, '-=0.2')
    // Animate hero description
    .to('.hero-desc', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      ease: 'power3.out'
    }, '-=0.3')
    // Animate action buttons
    .to('.hero-actions .btn', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      stagger: 0.1,
      ease: 'back.out(1.2)'
    }, '-=0.2')
    // Animate stats
    .to('.hero-stats .stat', {
      opacity: 1,
      y: 0,
      duration: 0.5,
      stagger: 0.1,
      ease: 'power3.out'
    }, '-=0.3')
    // Animate visual cards
    .to('.hero-visual .visual-card', {
      opacity: 1,
      scale: 1,
      rotation: 0,
      duration: 0.8,
      stagger: 0.15,
      ease: 'back.out(1.2)',
      onComplete: addColorfulEffects
    }, '-=0.5');
  }
  
  // Add colorful continuous motion effects
  function addColorfulEffects() {
    // Floating animation for badges
    gsap.to('.hero-badges .badge', {
      y: -10,
      duration: 2,
      ease: 'sine.inOut',
      yoyo: true,
      repeat: -1,
      stagger: 0.3
    });
    
    // Floating cards with rotation
    gsap.to('.hero-visual .visual-card', {
      y: -15,
      rotation: 2,
      duration: 3,
      ease: 'sine.inOut',
      yoyo: true,
      repeat: -1,
      stagger: 0.5
    });
    
    // Pulse glow for primary button
    gsap.to('.btn-primary', {
      boxShadow: '0 0 30px rgba(193, 255, 114, 0.6), 0 0 50px rgba(193, 255, 114, 0.3)',
      duration: 2,
      ease: 'sine.inOut',
      yoyo: true,
      repeat: -1
    });
    
    // Rainbow border animation for stats
    const statElements = document.querySelectorAll('.hero-stats .stat');
    statElements.forEach((stat, i) => {
      gsap.to(stat, {
        borderColor: ['#ff6b9d', '#c1ff72', '#4facfe', '#feca57', '#ff6b9d'],
        duration: 4,
        ease: 'none',
        repeat: -1,
        delay: i * 0.5
      });
    });
    
    // Scale pulse for title
    gsap.to('.hero-title .gradient', {
      scale: 1.05,
      duration: 2,
      ease: 'sine.inOut',
      yoyo: true,
      repeat: -1
    });
    
    // Rotate arrow in button
    gsap.to('.btn-arrow', {
      x: 5,
      rotation: 45,
      duration: 1.5,
      ease: 'sine.inOut',
      yoyo: true,
      repeat: -1
    });
  }
  
  // Animate landing page on page load
  document.addEventListener('DOMContentLoaded', function() {
    animateLandingPage();
  });
})();

// Load Google Maps embed
(function() {
  const mapIframe = document.getElementById('storeMap');
  if (!mapIframe) return;
  
  // Google Maps embed URL from store location
  mapIframe.src = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.497030260064!2d106.65454717456957!3d10.77319281793749!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec3c161a3fb%3A0xef77cd47a1cc691e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIC0gxJDhuqFpIGjhu41jIFF14buRYyBnaWEgVFAuSENN!5e0!3m2!1svi!2s!4v1763749407484!5m2!1svi!2s';
})();
</script>

<!-- Cookie Consent Banner -->
<div id="cookieConsentBanner" class="cookie-consent-banner">
  <div class="cookie-consent-content">
    <div class="cookie-consent-text">
      <div class="cookie-consent-title">
        <img id="cookieConsentAvatar" class="cookie-consent-avatar" src="<?= BASE_URL ?>images/avatars/avatar-default-1.png" alt="Avatar">
        Cookie Consent
      </div>
      <p class="cookie-consent-description">
        We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. 
        By clicking "Accept", you consent to our use of cookies. 
        <a href="?p=privacy" target="_blank">Learn more</a> about our cookie policy.
      </p>
    </div>
    <div class="cookie-consent-buttons">
      <button class="cookie-btn cookie-btn-decline" id="cookieDeclineBtn">Decline</button>
      <button class="cookie-btn cookie-btn-accept" id="cookieAcceptBtn">Accept</button>
    </div>
  </div>
</div>

<script>
// Cookie Consent Banner
(function() {
  const COOKIE_CONSENT_KEY = 'cookie_consent_accepted';
  const COOKIE_CONSENT_EXPIRY_DAYS = 365; // 1 year
  
  const banner = document.getElementById('cookieConsentBanner');
  const acceptBtn = document.getElementById('cookieAcceptBtn');
  const declineBtn = document.getElementById('cookieDeclineBtn');
  const avatarImg = document.getElementById('cookieConsentAvatar');
  
  if (!banner) return;
  
  // Random avatar on load (1-5)
  if (avatarImg) {
    const randomAvatar = Math.floor(Math.random() * 5) + 1;
    avatarImg.src = '<?= BASE_URL ?>images/avatars/avatar-default-' + randomAvatar + '.png';
  }
  
  // Check if user has already made a choice
  function hasConsent() {
    return localStorage.getItem(COOKIE_CONSENT_KEY) !== null;
  }
  
  // Save consent preference
  function saveConsent(accepted) {
    const expiryDate = new Date();
    expiryDate.setTime(expiryDate.getTime() + (COOKIE_CONSENT_EXPIRY_DAYS * 24 * 60 * 60 * 1000));
    
    localStorage.setItem(COOKIE_CONSENT_KEY, JSON.stringify({
      accepted: accepted,
      timestamp: Date.now(),
      expiry: expiryDate.getTime()
    }));
    
    // Also set PHP cookie for server-side use
    document.cookie = `cookie_consent=${accepted ? 'accepted' : 'declined'}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
  }
  
  // Hide banner with animation
  function hideBanner() {
    banner.style.animation = 'slideDown 0.3s ease-out';
    setTimeout(() => {
      banner.classList.remove('show');
      banner.style.display = 'none';
    }, 300);
  }
  
  // Show banner if no consent yet
  if (!hasConsent()) {
    setTimeout(() => {
      banner.classList.add('show');
    }, 1000); // Show after 1 second
  }
  
  // Accept button handler
  acceptBtn?.addEventListener('click', () => {
    saveConsent(true);
    hideBanner();
  });
  
  // Decline button handler
  declineBtn?.addEventListener('click', () => {
    saveConsent(false);
    hideBanner();
  });
  
  // Add slideDown animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideDown {
      from {
        transform: translateY(0);
        opacity: 1;
      }
      to {
        transform: translateY(100%);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);
})();
</script>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>

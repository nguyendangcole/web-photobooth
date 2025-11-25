<?php
// app/includes/seo_helper.php
// SEO helper functions

/**
 * Generate SEO-friendly URL (relative path)
 */
function seo_url(string $page, array $params = []): string {
  $url = '';
  if (!empty($page)) {
    $url = '?p=' . urlencode($page);
    if (!empty($params)) {
      foreach ($params as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
      }
    }
  }
  return $url;
}

/**
 * Get current page canonical URL (absolute URL)
 */
function canonical_url(?string $page = null): string {
  if ($page === null) {
    $page = $_GET['p'] ?? 'studio';
  }
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
  $base = rtrim(dirname($scriptName), '/\\');
  if ($base === '/' || $base === '') {
    $base = '';
  }
  // If page is empty, return homepage
  if (empty($page) || $page === 'landing' || $page === 'home') {
    return $protocol . '://' . $host . $base . '/';
  }
  return $protocol . '://' . $host . $base . '/index.php?p=' . urlencode($page);
}

/**
 * Default SEO data per page
 */
function default_seo_data(string $page): array {
  $siteName = 'Space Photobooth';
  $siteDescription = 'Create amazing photo frames and photobooks with Space Photobooth. Professional photo editing tools for events, parties, and special moments.';
  $baseUrl = canonical_url();
  
  $defaults = [
    'title' => $siteName . ' • Home',
    'description' => $siteDescription,
    'keywords' => 'photobooth, photo frame, photo editing, event photography, party photos',
    'og_type' => 'website',
    'og_image' => $baseUrl . 'images/og-default.jpg',
    'twitter_card' => 'summary_large_image',
    'canonical' => $baseUrl,
    'robots' => 'index, follow'
  ];
  
  $pageData = [
    'studio' => [
      'title' => $siteName . ' • Studio Dashboard',
      'description' => 'Access your Space Photobooth studio dashboard. Manage your photos, create frames, and build your photobook collection.',
      'keywords' => 'photobooth studio, photo management, dashboard, photo gallery',
      'canonical' => canonical_url('studio')
    ],
    'photobooth' => [
      'title' => $siteName . ' • Photobooth',
      'description' => 'Use our interactive photobooth to capture and customize photos with stickers, filters, and fun effects. Perfect for events and parties!',
      'keywords' => 'photobooth, interactive photobooth, photo capture, event photobooth, party photobooth, photo stickers',
      'canonical' => canonical_url('photobooth')
    ],
    'frame' => [
      'title' => $siteName . ' • Frame Composer',
      'description' => 'Create stunning photo frames with our easy-to-use frame composer. Upload your photos and arrange them in beautiful layouts.',
      'keywords' => 'photo frame, frame composer, photo layout, photo collage, frame designer',
      'canonical' => canonical_url('frame')
    ],
    'photobook' => [
      'title' => $siteName . ' • Photobook Gallery',
      'description' => 'Browse your photobook collection and relive your favorite moments. Create and manage your personalized photobooks.',
      'keywords' => 'photobook, photo gallery, photo album, digital photobook, photo collection',
      'canonical' => canonical_url('photobook')
    ],
    'gallery' => [
      'title' => $siteName . ' • Gallery',
      'description' => 'View your photo gallery and photobook collection. Organize and share your favorite memories.',
      'keywords' => 'photo gallery, photobook, photo album, photo collection',
      'canonical' => canonical_url('photobook')
    ],
    'login' => [
      'title' => $siteName . ' • Login',
      'description' => 'Login to your Space Photobooth account to access your photos, frames, and photobooks.',
      'keywords' => 'login, account, sign in',
      'robots' => 'noindex, follow',
      'canonical' => canonical_url('login')
    ],
    'register' => [
      'title' => $siteName . ' • Sign Up',
      'description' => 'Create a free Space Photobooth account to start creating amazing photo frames and photobooks.',
      'keywords' => 'register, sign up, create account, free account',
      'robots' => 'noindex, follow',
      'canonical' => canonical_url('register')
    ],
    'info' => [
      'title' => $siteName . ' • About Us',
      'description' => 'Learn more about Space Photobooth and our mission to make photo creation fun and accessible for everyone.',
      'keywords' => 'about, information, company, photobooth service',
      'canonical' => canonical_url('info')
    ],
    'service' => [
      'title' => $siteName . ' • Services',
      'description' => 'Discover our photobooth services and solutions for events, parties, and special occasions.',
      'keywords' => 'services, photobooth rental, event services, party services',
      'canonical' => canonical_url('service')
    ],
    'qa' => [
      'title' => $siteName . ' • FAQ',
      'description' => 'Frequently asked questions about Space Photobooth. Find answers to common questions about our services.',
      'keywords' => 'FAQ, questions, help, support, answers',
      'canonical' => canonical_url('qa')
    ],
    'contact' => [
      'title' => $siteName . ' • Contact',
      'description' => 'Get in touch with Space Photobooth. Contact us for inquiries, support, or collaboration opportunities.',
      'keywords' => 'contact, support, inquiry, customer service',
      'canonical' => canonical_url('contact')
    ],
    'terms' => [
      'title' => $siteName . ' • Terms of Service',
      'description' => 'Read our terms of service and user agreement for Space Photobooth.',
      'keywords' => 'terms, terms of service, user agreement, legal',
      'robots' => 'noindex, follow',
      'canonical' => canonical_url('terms')
    ],
    'privacy' => [
      'title' => $siteName . ' • Privacy Policy',
      'description' => 'Our privacy policy explains how we collect, use, and protect your personal information.',
      'keywords' => 'privacy, privacy policy, data protection, security',
      'robots' => 'noindex, follow',
      'canonical' => canonical_url('privacy')
    ]
  ];
  
  $data = $pageData[$page] ?? $defaults;
  return array_merge($defaults, $data);
}

/**
 * Render SEO meta tags
 */
function render_seo_meta(array $seoData = []): void {
  if (empty($seoData)) {
  $currentPage = $_GET['p'] ?? 'studio';
  $seoData = default_seo_data($currentPage);
  }
  
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $baseUrl = $protocol . '://' . $host . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\');
  
  // Basic meta tags
  echo '<title>' . htmlspecialchars($seoData['title'] ?? 'Space Photobooth') . '</title>' . PHP_EOL;
  echo '<meta name="description" content="' . htmlspecialchars($seoData['description'] ?? '') . '">' . PHP_EOL;
  echo '<meta name="keywords" content="' . htmlspecialchars($seoData['keywords'] ?? '') . '">' . PHP_EOL;
  echo '<meta name="robots" content="' . htmlspecialchars($seoData['robots'] ?? 'index, follow') . '">' . PHP_EOL;
  echo '<meta name="author" content="Space Photobooth">' . PHP_EOL;
  
  // Canonical URL
  $canonical = $seoData['canonical'] ?? canonical_url();
  echo '<link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . PHP_EOL;
  
  // Open Graph tags
  echo '<meta property="og:type" content="' . htmlspecialchars($seoData['og_type'] ?? 'website') . '">' . PHP_EOL;
  echo '<meta property="og:title" content="' . htmlspecialchars($seoData['title'] ?? 'Space Photobooth') . '">' . PHP_EOL;
  echo '<meta property="og:description" content="' . htmlspecialchars($seoData['description'] ?? '') . '">' . PHP_EOL;
  echo '<meta property="og:url" content="' . htmlspecialchars($canonical) . '">' . PHP_EOL;
  echo '<meta property="og:site_name" content="Space Photobooth">' . PHP_EOL;
  
  // OG Image
  $ogImage = $seoData['og_image'] ?? $baseUrl . '/images/og-default.jpg';
  if (!preg_match('/^https?:\/\//', $ogImage)) {
    $ogImage = $baseUrl . '/' . ltrim($ogImage, '/');
  }
  echo '<meta property="og:image" content="' . htmlspecialchars($ogImage) . '">' . PHP_EOL;
  echo '<meta property="og:image:width" content="1200">' . PHP_EOL;
  echo '<meta property="og:image:height" content="630">' . PHP_EOL;
  echo '<meta property="og:image:alt" content="' . htmlspecialchars($seoData['title'] ?? 'Space Photobooth') . '">' . PHP_EOL;
  
  // Twitter Card tags
  echo '<meta name="twitter:card" content="' . htmlspecialchars($seoData['twitter_card'] ?? 'summary_large_image') . '">' . PHP_EOL;
  echo '<meta name="twitter:title" content="' . htmlspecialchars($seoData['title'] ?? 'Space Photobooth') . '">' . PHP_EOL;
  echo '<meta name="twitter:description" content="' . htmlspecialchars($seoData['description'] ?? '') . '">' . PHP_EOL;
  echo '<meta name="twitter:image" content="' . htmlspecialchars($ogImage) . '">' . PHP_EOL;
  
  // Additional meta tags
  echo '<meta name="theme-color" content="#c1ff72">' . PHP_EOL;
  echo '<meta name="mobile-web-app-capable" content="yes">' . PHP_EOL;
  echo '<meta name="apple-mobile-web-app-capable" content="yes">' . PHP_EOL;
  echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . PHP_EOL;
  
  // Schema.org structured data (JSON-LD)
  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => 'Space Photobooth',
    'description' => $seoData['description'] ?? '',
    'url' => $canonical,
    'applicationCategory' => 'MultimediaApplication',
    'operatingSystem' => 'Web',
    'offers' => [
      '@type' => 'Offer',
      'price' => '0',
      'priceCurrency' => 'USD'
    ]
  ];
  
  echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . PHP_EOL;
}


<?php
// public/sitemap.php
// Generate dynamic sitemap.xml

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/includes/seo_helper.php';

// Get base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $protocol . '://' . $host . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\');

// Pages to include in sitemap
$pages = [
  'studio' => ['priority' => '1.0', 'changefreq' => 'daily'],
  'photobooth' => ['priority' => '0.9', 'changefreq' => 'weekly'],
  'frame' => ['priority' => '0.9', 'changefreq' => 'weekly'],
  'photobook' => ['priority' => '0.8', 'changefreq' => 'weekly'],
  'info' => ['priority' => '0.7', 'changefreq' => 'monthly'],
  'service' => ['priority' => '0.7', 'changefreq' => 'monthly'],
  'qa' => ['priority' => '0.6', 'changefreq' => 'monthly'],
  'contact' => ['priority' => '0.6', 'changefreq' => 'monthly'],
  'terms' => ['priority' => '0.3', 'changefreq' => 'yearly'],
  'privacy' => ['priority' => '0.3', 'changefreq' => 'yearly'],
];

// Set content type
header('Content-Type: application/xml; charset=utf-8');

// Generate XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
echo '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
echo '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . PHP_EOL;
echo '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

foreach ($pages as $page => $config) {
  $url = $baseUrl . '/index.php?p=' . urlencode($page);
  $lastmod = date('Y-m-d');
  
  echo '  <url>' . PHP_EOL;
  echo '    <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
  echo '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
  echo '    <changefreq>' . $config['changefreq'] . '</changefreq>' . PHP_EOL;
  echo '    <priority>' . $config['priority'] . '</priority>' . PHP_EOL;
  echo '  </url>' . PHP_EOL;
}

// Homepage (landing)
$homeUrl = $baseUrl . '/';
echo '  <url>' . PHP_EOL;
echo '    <loc>' . htmlspecialchars($homeUrl) . '</loc>' . PHP_EOL;
echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL;
echo '    <changefreq>daily</changefreq>' . PHP_EOL;
echo '    <priority>1.0</priority>' . PHP_EOL;
echo '  </url>' . PHP_EOL;

echo '</urlset>' . PHP_EOL;


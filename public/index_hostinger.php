<?php
// index.php for Hostinger - placed in public_html/
// This file redirects to the actual project in web-photobooth/public/

// Get the base path
$projectPath = __DIR__ . '/../web-photobooth/public/index.php';

// Alternative path if folder name is different
if (!file_exists($projectPath)) {
    $projectPath = __DIR__ . '/../web-photobooth-hostinger-zip-EXTRACTED-ARCHIVE/web-photobooth/public/index.php';
}

if (file_exists($projectPath)) {
    // Set BASE_URL correctly before including
    $_SERVER['SCRIPT_NAME'] = '/web-photobooth/public/index.php';
    require $projectPath;
} else {
    // Fallback: try to find public folder
    $publicPath = __DIR__ . '/web-photobooth/public/index.php';
    if (file_exists($publicPath)) {
        $_SERVER['SCRIPT_NAME'] = '/web-photobooth/public/index.php';
        require $publicPath;
    } else {
        die('Error: Project files not found. Please check folder structure.');
    }
}


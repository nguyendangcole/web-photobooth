# Screenshot Guide for LaTeX Report

This guide shows where to place screenshots in the `report.tex` file.

## Required Screenshots

Create a `screenshots/` folder in the project root and add the following images:

### 1. Landing Page (`screenshots/landing.png`)
- **Location in report**: Section 1 (Introduction)
- **What to capture**: Main landing page/homepage showing the hero section and navigation

### 2. Responsive Design (`screenshots/responsive.png`)
- **Location in report**: Section 2 (Design & Layout)
- **What to capture**: Three views side-by-side (mobile, tablet, desktop) or use a responsive design tool screenshot

### 3. Authentication Page (`screenshots/auth.png`)
- **Location in report**: Section 3 (Key Features - User Authentication)
- **What to capture**: Login/Register page showing OAuth buttons (Google, Facebook)

### 4. Photobooth Interface (`screenshots/photobooth.png`)
- **Location in report**: Section 3 (Key Features - Photobooth Feature)
- **What to capture**: Photobooth page with webcam preview and controls

### 5. Frame Composer (`screenshots/frame.png`)
- **Location in report**: Section 3 (Key Features - Frame Composer)
- **What to capture**: Frame selection page with search functionality visible

### 6. Photobook Gallery (`screenshots/photobook.png`)
- **Location in report**: Section 3 (Key Features - Photobook Gallery)
- **What to capture**: Photobook gallery showing organized photos

### 7. SEO Implementation (`screenshots/seo.png`)
- **Location in report**: Section 4 (Technical Implementation - SEO)
- **What to capture**: View page source showing meta tags, or use browser DevTools showing meta tags

### 8. ERD Diagram (`screenshots/erd_diagram.png`)
- **Location in report**: Section 4 (Technical Implementation - Database Schema)
- **What to capture**: Entity Relationship Diagram showing all database tables and their relationships
- **How to create**: 
  - Use tools like MySQL Workbench, dbdiagram.io, or draw.io
  - Include all tables: users, frames, photobook_albums, photobook_pages, premium_requests, countries, states
  - Show foreign key relationships with connecting lines
  - Include key fields and relationships

## Screenshot Tips

1. **Image Format**: Use PNG format for best quality
2. **Image Size**: Keep images under 2MB each, resize if needed
3. **Naming**: Use exact filenames as shown above
4. **Browser**: Use Chrome or Firefox for screenshots
5. **Resolution**: 
   - Desktop: 1920x1080 or 1280x720
   - Mobile: Use browser DevTools device emulation
6. **Privacy**: Blur or remove any personal information if visible

## How to Take Screenshots

### Desktop Screenshots
- Use browser's built-in screenshot tools
- Or use tools like Snipping Tool (Windows), Screenshot (Mac), or browser extensions

### Responsive Design Screenshot
1. Open browser DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Select different device sizes
4. Take screenshots of each view
5. Combine them in an image editor or take a screenshot of all three side-by-side

### View Page Source Screenshot
1. Right-click on page → View Page Source
2. Scroll to `<head>` section
3. Take screenshot showing meta tags

## Adding Screenshots to LaTeX

The LaTeX file already has placeholders for all screenshots. Just ensure:
1. All images are in `screenshots/` folder
2. Filenames match exactly (case-sensitive)
3. Images are in PNG format

If you need to adjust image sizes in the report, modify the `width` parameter in the `\includegraphics` commands.


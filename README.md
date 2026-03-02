# 🚀 SPACE PHOTOBOOTH • CAPTURE THE COSMOS

A cosmic photobooth experience that transports your memories to another dimension. Create • Transform • Explore infinite possibilities with our space-themed photo application.

**🌌 Live Demo:** [https://mediumspringgreen-wolf-877794.hostingersite.com](https://mediumspringgreen-wolf-877794.hostingersite.com/?p=landing)

**🎥 Watch Demo:** [https://youtu.be/SWZQMlKU4vg](https://youtu.be/SWZQMlKU4vg)

## 🌟 Features

### Core Functionality
- **📸 Instant Capture**: Webcam support with countdown timer and multi-shot capabilities
- **🎨 Alien Frames**: 100+ unique frames with sci-fi & Y2K aesthetics (vertical & square layouts)
- **📸 Photobook Gallery**: Organize and manage photo collections with date-based organization
- **⭐ Premium Membership**: Exclusive frames and advanced features for premium users
- **🎭 Real-time Filters**: Live effects and transformations
- **💾 Save & Share**: Export high-quality photos and create digital photobooks
- **🎨 Customization**: Adjust brightness, contrast, saturation + text & stickers
- **📱 Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices

### User Authentication
- **🔐 Local Authentication**: Email/password registration and login
- **🌐 Social Login**: OAuth integration with Google and Facebook
- **🔑 Password Recovery**: OTP-based password reset via email
- **✉️ Email Verification**: Token-based email verification system
- **🛡️ Security**: CSRF protection, SQL injection prevention, XSS protection, secure sessions

### Technical Features
- **🔍 AJAX Search**: Real-time frame search with dropdown suggestions
- **🚀 SEO Optimized**: Meta tags, semantic HTML, sitemap, and friendly URLs
- **⚡ Performance**: Gzip compression, browser caching, optimized assets
- **🎯 Modern UI**: Clean, space-themed interface with smooth animations
- **🔧 Developer Friendly**: Well-documented code with modular architecture

## 📋 Requirements

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.3+)
- Apache/Nginx web server
- Composer (for dependency management)

### PHP Extensions
- PDO
- PDO_MySQL
- GD or Imagick (for image processing)
- OpenSSL (for OAuth)
- mbstring

### Optional
- SMTP server (for email functionality)
- Google OAuth credentials (for Google login)
- Facebook App credentials (for Facebook login)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/web-photobooth.git
cd web-photobooth
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
1. Create a MySQL database:
```sql
CREATE DATABASE photobooth_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u root -p photobooth_db < config/database_complete.sql
```

Or import via phpMyAdmin:
- Navigate to phpMyAdmin
- Select your database
- Go to Import tab
- Choose `config/database_complete.sql`
- Click Go




2. Update database credentials in `config/db.php` if not using `.env`:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'photobooth_db');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 5. File Permissions
Ensure the following directories are writable:
```bash
chmod -R 755 uploads/
chmod -R 755 public/photobook/
```

### 6. Web Server Configuration

#### Apache
Ensure mod_rewrite is enabled and create/update `.htaccess` in `public/`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?p=$1 [L,QSA]
```

#### Nginx
Add to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?p=$uri&$args;
}
```

### 7. Access the Application
Open your browser and navigate to:
```
http://localhost:8888/WEB-PHOTOBOOTH/public/
```

## 📁 Project Structure

```
web-photobooth/
├── admin/              # Admin panel for managing frames and users
│   ├── frames_add.php
│   ├── frames_list.php
│   └── includes/
├── ajax/               # AJAX endpoints for dynamic content
│   ├── frames_list.php
│   ├── photobook_add.php
│   ├── photobook_list.php
│   └── ...
├── app/                # Main application logic
│   ├── auth/          # Authentication pages
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── oauth_google.php
│   │   └── ...
│   ├── includes/      # Shared components
│   │   ├── auth_guard.php
│   │   ├── seo_helper.php
│   │   └── ...
│   ├── config.php     # Main configuration
│   ├── router.php     # URL routing
│   ├── photobooth.php # Photobooth feature
│   ├── frame.php      # Frame composer
│   └── photobook.php  # Photobook gallery
├── config/            # Database and setup files
│   ├── db.php
│   ├── database_complete.sql
│   └── ...
├── public/            # Public assets and entry point
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   ├── images/       # Image assets
│   ├── photobook/    # User-uploaded photos
│   ├── index.php     # Application entry point
│   └── sitemap.php   # Dynamic sitemap
├── uploads/           # Temporary upload directory
├── vendor/            # Composer dependencies
├── composer.json      # PHP dependencies
├── package.json       # Node.js dependencies (if any)
└── README.md          # This file
```

## 🎨 Technologies Used

### Frontend
- **🎨 HTML5**: Semantic markup with modern structure
- **💎 CSS3**: Responsive design, animations, space-themed styling
- **⚡ JavaScript (ES6+)**: Interactive features, AJAX, DOM manipulation
- **🎯 Bootstrap 5**: UI framework for responsive components

### Backend
- **🐘 PHP 7.4+**: Server-side logic with modern practices
- **🗄️ MySQL**: Database management with optimized queries
- **🔌 PDO**: Database abstraction layer for security
- **📧 PHPMailer**: Advanced email functionality

### Libraries & Tools
- **📦 Composer**: PHP dependency management
- **🔐 Google OAuth API**: Social login integration
- **📘 Facebook Login API**: Social authentication
- **🎨 Custom Frame Engine**: Dynamic frame application system

## 🔐 Security Features

- Password hashing using PHP `password_hash()` with bcrypt
- Prepared statements for all database queries (SQL injection prevention)
- XSS protection with `htmlspecialchars()` output escaping
- CSRF token validation for forms
- Secure session configuration (HttpOnly cookies, custom session names)
- Input validation and sanitization
- File upload security (type validation, secure naming)

## 🌌 Live Project

**🚀 URL:** [https://mediumspringgreen-wolf-877794.hostingersite.com](https://mediumspringgreen-wolf-877794.hostingersite.com)

**🎥 Demo Video:** [Watch on YouTube](https://youtu.be/SWZQMlKU4vg?si=KewBJJBh9HLB2BDm)

**⭐ Key Features in Production:**
- ✅ Fully functional OAuth authentication
- ✅ 100+ space-themed frames
- ✅ Real-time photo capture and processing
- ✅ Premium membership system
- ✅ Mobile-responsive design
- ✅ SEO-optimized URLs
- ✅ Email verification system

## 📱 Responsive Design

The application is fully responsive with cosmic breakpoints:
- **🖥️ Desktop**: > 768px (full cosmic layout)
- **📱 Tablet**: 481px - 768px (adjusted grid)
- **📲 Mobile**: ≤ 480px (single column, hamburger menu)

## 🔍 SEO Features

- Meta descriptions and keywords for each page
- Semantic HTML5 structure
- Open Graph tags for social sharing
- Twitter Card metadata
- Dynamic XML sitemap
- Friendly URLs
- Schema.org structured data (JSON-LD)

## 🧪 Testing

### Manual Testing Checklist
- [ ] User registration and login
- [ ] OAuth login (Google/Facebook)
- [ ] Password reset functionality
- [ ] Photobooth photo capture
- [ ] Frame composer with search
- [ ] Photobook gallery
- [ ] Responsive design on mobile/tablet
- [ ] Form validation
- [ ] Error handling

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Verify database credentials in `.env` or `config/db.php`
- Ensure MySQL service is running
- Check database exists and user has proper permissions

**Session Issues**
- Check PHP session configuration
- Verify `uploads/` directory is writable
- Clear browser cookies and try again

**OAuth Not Working**
- Verify OAuth credentials in `.env`
- Check redirect URIs match exactly
- Ensure HTTPS is used in production

**Images Not Uploading**
- Check file permissions on `uploads/` and `public/photobook/`
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Check error logs for specific issues

## 📚 Documentation

For detailed documentation on specific features:
- 🗄️ **Database schema**: See `config/database_complete.sql`
- 📧 **Email setup**: See `config/ENV_EMAIL_SETUP.md`
- 🚀 **Deployment**: See deployment guide above
- 🎥 **Video Tutorial**: [YouTube Demo](https://youtu.be/SWZQMlKU4vg?si=KewBJJBh9HLB2BDm)

---

**🌌 Created with cosmic energy for the Web Programming course.**

**👨‍🚀 Author:** Cole Nguyen - [Portfolio](https://mediumspringgreen-wolf-877794.hostingersite.com)

**🙏 Acknowledgments**
- Bootstrap team for the responsive framework
- PHPMailer contributors for email magic
- Google and Facebook for OAuth APIs
- All open-source libraries used in this cosmic journey

**⚡ Ready to create something alien?**

> "In the vast expanse of digital space, every photo tells a story of cosmic adventure."


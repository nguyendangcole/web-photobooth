# Space Photobooth - Web Programming Project

A modern, full-stack web application for creating, editing, and organizing digital photos with interactive photobooth functionality, frame composition tools, and photobook gallery system.

## 🚀 Features

### Core Functionality
- **Interactive Photobooth**: Real-time webcam capture with countdown timer and effects
- **Frame Composer**: Upload photos and apply 100+ pre-designed frames (vertical/square layouts)
- **Photobook Gallery**: Organize and manage photo collections with date-based organization
- **Premium Membership**: Exclusive frames and features for premium users

### User Authentication
- **Local Authentication**: Email/password registration and login
- **OAuth Integration**: Login with Google and Facebook
- **Password Recovery**: OTP-based password reset via email
- **Email Verification**: Token-based email verification system

### Technical Features
- **Responsive Design**: Fully responsive across desktop, tablet, and mobile devices
- **AJAX Search**: Real-time frame search with dropdown suggestions
- **SEO Optimized**: Meta tags, semantic HTML, sitemap, and friendly URLs
- **Security**: CSRF protection, SQL injection prevention, XSS protection, secure sessions

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
- **HTML5**: Semantic markup
- **CSS3**: Responsive design, animations, modern styling
- **JavaScript (ES6+)**: Interactive features, AJAX, DOM manipulation
- **Bootstrap 5**: UI framework for responsive components

### Backend
- **PHP 7.4+**: Server-side logic
- **MySQL**: Database management
- **PDO**: Database abstraction layer
- **PHPMailer**: Email functionality

### Libraries & Tools
- **Composer**: PHP dependency management
- **Google OAuth API**: Social login
- **Facebook Login API**: Social login

## 🔐 Security Features

- Password hashing using PHP `password_hash()` with bcrypt
- Prepared statements for all database queries (SQL injection prevention)
- XSS protection with `htmlspecialchars()` output escaping
- CSRF token validation for forms
- Secure session configuration (HttpOnly cookies, custom session names)
- Input validation and sanitization
- File upload security (type validation, secure naming)

## 📱 Responsive Design

The application is fully responsive with breakpoints:
- **Desktop**: > 768px (full layout)
- **Tablet**: 481px - 768px (adjusted grid)
- **Mobile**: ≤ 480px (single column, hamburger menu)

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

## 📝 License

This project is developed for educational purposes as part of a Web Programming course.

## 👤 Author

Your Name - [Your Email](mailto:your.email@example.com)

## 🙏 Acknowledgments

- Bootstrap team for the responsive framework
- PHPMailer contributors
- Google and Facebook for OAuth APIs
- All open-source libraries used in this project

## 📚 Documentation

For detailed documentation on specific features:
- Database schema: See `config/database_complete.sql`
- Email setup: See `config/ENV_EMAIL_SETUP.md`
- Deployment: See `DEPLOY_NGROK.md` (if applicable)

---

**Note**: This is a course project. For production use, additional security measures and optimizations should be implemented.


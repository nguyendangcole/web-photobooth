# Hướng dẫn cấu hình Email/SMTP để gửi OTP

## 📧 Vấn đề
Nếu bạn chưa thấy email OTP được gửi, có thể do:
1. Chưa cấu hình SMTP trong file `.env`
2. Chưa có file `.env` trong thư mục gốc project
3. PHPMailer chưa được cài đặt

## 🚀 Cách 1: Dùng Gmail (Khuyến nghị)

### Bước 1: Tạo App Password cho Gmail
1. Vào Google Account: https://myaccount.google.com/
2. Bật **2-Step Verification** (nếu chưa bật)
3. Vào **Security** → **App passwords**
4. Tạo App Password mới cho "Mail"
5. Copy password 16 ký tự (ví dụ: `abcd efgh ijkl mnop`)

### Bước 2: Tạo file `.env` trong thư mục gốc
Tạo file `.env` với nội dung:

```env
# Database (đã có sẵn)
DB_HOST=127.0.0.1
DB_NAME=myapp
DB_USER=root
DB_PASS=
DB_PORT=3306

# Email/SMTP Configuration
SMTP_ENABLED=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-16-char-app-password
SMTP_FROM=your-email@gmail.com
SMTP_FROM_NAME=PhotoBooth
```

**Lưu ý:** 
- Thay `your-email@gmail.com` bằng email Gmail của bạn
- Thay `your-16-char-app-password` bằng App Password vừa tạo (bỏ dấu cách)

### Bước 3: Cài đặt PHPMailer (nếu chưa có)
```bash
cd /Applications/MAMP/htdocs/web-photobooth
composer require phpmailer/phpmailer
```

Nếu chưa có Composer, cài đặt:
```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar require phpmailer/phpmailer
```

---

## 🚀 Cách 2: Dùng Outlook/Hotmail

```env
SMTP_ENABLED=true
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@outlook.com
SMTP_PASS=your-password
SMTP_FROM=your-email@outlook.com
SMTP_FROM_NAME=PhotoBooth
```

---

## 🚀 Cách 3: Dùng Yahoo Mail

```env
SMTP_ENABLED=true
SMTP_HOST=smtp.mail.yahoo.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@yahoo.com
SMTP_PASS=your-app-password
SMTP_FROM=your-email@yahoo.com
SMTP_FROM_NAME=PhotoBooth
```

---

## 🚀 Cách 4: Dùng Mailtrap (Testing - Không gửi email thật)

1. Đăng ký tài khoản miễn phí tại: https://mailtrap.io/
2. Vào **Inboxes** → Chọn inbox → **SMTP Settings**
3. Copy thông tin và cấu hình:

```env
SMTP_ENABLED=true
SMTP_HOST=smtp.mailtrap.io
SMTP_PORT=2525
SMTP_SECURE=tls
SMTP_USER=your-mailtrap-username
SMTP_PASS=your-mailtrap-password
SMTP_FROM=test@photobooth.com
SMTP_FROM_NAME=PhotoBooth
```

---

## 🔍 Kiểm tra cấu hình

Sau khi cấu hình, test bằng cách:
1. Vào trang forgot password
2. Nhập email
3. Kiểm tra:
   - Email inbox (hoặc Mailtrap nếu dùng Mailtrap)
   - Error log của PHP (nếu có lỗi)

---

## ⚠️ Xử lý lỗi

### Lỗi: "Class 'PHPMailer\PHPMailer\PHPMailer' not found"
**Giải pháp:** Cài đặt PHPMailer:
```bash
composer require phpmailer/phpmailer
```

### Lỗi: "SMTP connect() failed"
**Nguyên nhân:** 
- Sai username/password
- Chưa bật "Less secure app access" (Gmail cũ) hoặc chưa tạo App Password (Gmail mới)
- Firewall chặn port 587/465

**Giải pháp:**
- Kiểm tra lại username/password
- Với Gmail: Dùng App Password thay vì password thường
- Kiểm tra firewall

### Email không đến
**Kiểm tra:**
1. Spam folder
2. Error log: `/Applications/MAMP/logs/php_error.log`
3. Xem code có gọi `send_mail()` không

---

## 📝 File .env mẫu đầy đủ

```env
# Database
DB_HOST=127.0.0.1
DB_NAME=myapp
DB_USER=root
DB_PASS=
DB_PORT=3306
DB_CHARSET=utf8mb4

# App
APP_ENV=dev
APP_TZ=Asia/Ho_Chi_Minh

# SMTP (Gmail example)
SMTP_ENABLED=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
SMTP_FROM=your-email@gmail.com
SMTP_FROM_NAME=PhotoBooth
```

---

## 💡 Lưu ý

1. **File .env** nên được thêm vào `.gitignore` để không commit lên Git
2. **App Password** (Gmail) khác với password đăng nhập thường
3. Với **Gmail**, bắt buộc phải dùng App Password nếu bật 2FA
4. **Mailtrap** rất hữu ích để test email trong môi trường development


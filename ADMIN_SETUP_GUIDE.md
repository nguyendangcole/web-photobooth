# 🎨 Hướng dẫn Setup Admin Panel

## ✅ TỔNG QUAN

Admin Panel hoàn chỉnh với:
- ✅ Sidebar navigation đẹp
- ✅ Dashboard với thống kê
- ✅ Phân quyền admin/user
- ✅ Gộp tất cả trang admin vào một layout
- ✅ Responsive design

---

## 🚀 SETUP NHANH (3 bước)

### BƯỚC 1: Thêm cột `role` vào database

Mở **phpMyAdmin** → database **`myapp`** → tab **"SQL"** → Copy/paste:

```sql
-- Thêm cột role
ALTER TABLE users 
ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER is_premium;

-- Thêm index
ALTER TABLE users 
ADD INDEX idx_role (role);

-- Set email của bạn thành admin (THAY ĐỔI EMAIL)
UPDATE users SET role = 'admin' WHERE email = 'nguyenduydang225@gmail.com';

-- Kiểm tra
SELECT id, name, email, role FROM users WHERE role = 'admin';
```

**⚠️ QUAN TRỌNG**: Thay `nguyenduydang225@gmail.com` bằng email của bạn!

---

### BƯỚC 2: Kiểm tra files đã tạo

Các file sau đã được tạo:
- ✅ `admin/includes/admin_guard.php` - Bảo vệ trang admin
- ✅ `admin/includes/layout_header.php` - Header + Sidebar
- ✅ `admin/includes/layout_footer.php` - Footer
- ✅ `admin/index.php` - Dashboard
- ✅ `config/admin_setup.sql` - SQL setup

---

### BƯỚC 3: Truy cập Admin Panel

1. **Login** với email đã set admin ở Bước 1
2. Truy cập: **http://localhost:8888/Web-photobooth/admin/**
3. ✅ Bạn sẽ thấy Admin Dashboard!

---

## 📱 CÁCH SỬ DỤNG

### Thêm Admin mới

#### Cách 1: Qua SQL
```sql
-- Thêm admin theo email
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';

-- Thêm nhiều admin cùng lúc
UPDATE users SET role = 'admin' WHERE email IN (
  'admin1@example.com', 
  'admin2@example.com',
  'admin3@example.com'
);

-- Thêm admin theo user ID
UPDATE users SET role = 'admin' WHERE id = 4;
```

#### Cách 2: User register → sau đó set admin
1. User đăng ký tài khoản bình thường
2. Admin chạy SQL: `UPDATE users SET role = 'admin' WHERE email = 'email_vua_dang_ky'`
3. User login lại → vào được admin panel

---

### Xóa quyền admin

```sql
-- Hạ admin về user thường
UPDATE users SET role = 'user' WHERE email = 'admin@example.com';

-- Kiểm tra
SELECT id, name, email, role FROM users WHERE role = 'admin';
```

---

## 🎯 TÍNH NĂNG ADMIN PANEL

### Dashboard:
- ✅ Thống kê tổng users, premium users
- ✅ Thống kê pending requests
- ✅ Thống kê frames (total + premium)
- ✅ Danh sách requests gần đây
- ✅ Danh sách users mới
- ✅ Quick actions

### Sidebar Menu:
```
📊 Dashboard
  - Dashboard (tổng quan)

🖼️ Quản lý Frames
  - Danh sách Frames
  - Thêm Frame

⭐ Premium
  - Premium Requests (có badge pending count)
  - Premium Users

⚙️ Hệ thống
  - Tất cả Users
  - Cài đặt

↩️ Khác
  - Về trang chính
```

---

## 🔒 BẢO MẬT

### Admin Guard hoạt động như thế nào?

`admin/includes/admin_guard.php` kiểm tra:
1. User đã login chưa? → Chưa → Redirect về login
2. User có `role = 'admin'` không? → Không → Hiện 403 Access Denied
3. Cả 2 OK → Cho phép truy cập

### File nào cần protect?

Tất cả file trong `/admin/` nên include `admin_guard.php`:

```php
<?php
require_once __DIR__ . '/includes/admin_guard.php';
// ... rest of code
?>
```

---

## 🎨 CÁCH THÊM TRANG ADMIN MỚI

### Ví dụ: Tạo trang `admin/logs.php`

```php
<?php
// admin/logs.php
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$currentPage = 'logs';  // ← ID trang (để active sidebar)
$pageTitle = 'System Logs';  // ← Tiêu đề

// ... your code ...

require __DIR__ . '/includes/layout_header.php';
?>

<!-- Nội dung trang -->
<div class="card">
  <div class="card-header">
    <h5>System Logs</h5>
  </div>
  <div class="card-body">
    <!-- Content here -->
  </div>
</div>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
```

### Thêm vào Sidebar

Edit `admin/includes/layout_header.php`, thêm vào nav:

```php
<li class="nav-item">
  <a href="logs.php" class="nav-link <?= ($currentPage ?? '') === 'logs' ? 'active' : '' ?>">
    <i class="bi bi-file-text"></i>
    <span>System Logs</span>
  </a>
</li>
```

---

## 🧪 TESTING

### Test 1: User thường không vào được admin

1. Login với user thường (không phải admin)
2. Truy cập: http://localhost:8888/Web-photobooth/admin/
3. ✅ Hiện trang **403 Access Denied**

### Test 2: Admin vào được

1. Login với email đã set admin
2. Truy cập: http://localhost:8888/Web-photobooth/admin/
3. ✅ Thấy Dashboard với stats

### Test 3: Sidebar active đúng

1. Click vào "Premium Requests" trong sidebar
2. ✅ Link "Premium Requests" có highlight màu cam
3. Click "Dashboard"
4. ✅ Link "Dashboard" có highlight

### Test 4: Pending badge

1. Có user gửi premium request (status = pending)
2. Vào Dashboard
3. ✅ Link "Premium Requests" có badge đỏ với số lượng pending

---

## 📊 QUERIES HỮU ÍCH

### Xem tất cả admin
```sql
SELECT id, name, email, role, is_premium, created_at 
FROM users 
WHERE role = 'admin';
```

### Đếm admin
```sql
SELECT COUNT(*) as total_admins FROM users WHERE role = 'admin';
```

### Xem users + role
```sql
SELECT id, name, email, role, is_premium 
FROM users 
ORDER BY role DESC, id DESC 
LIMIT 20;
```

### Set tất cả premium users thành admin (nguy hiểm!)
```sql
-- CẢNH BÁO: Chỉ dùng khi cần
UPDATE users SET role = 'admin' WHERE is_premium = 1;
```

---

## 🎯 BEST PRACTICES

### 1. Luôn có ít nhất 2 admin
Tránh trường hợp admin duy nhất bị khóa tài khoản.

```sql
-- Set 2-3 email làm admin ngay từ đầu
UPDATE users SET role = 'admin' WHERE email IN (
  'admin1@example.com',
  'admin2@example.com'
);
```

### 2. Không nên có quá nhiều admin
Admin có quyền toàn quyền → chỉ set cho người tin tưởng.

### 3. Log admin actions (tùy chọn)
Nếu muốn track ai làm gì, tạo bảng `admin_logs`:

```sql
CREATE TABLE admin_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT UNSIGNED NOT NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES users(id)
);
```

### 4. Protect admin files
Đảm bảo tất cả file trong `/admin/` có `admin_guard.php` ở đầu.

---

## ❓ FAQ

### Q: Tôi quên email admin, làm sao tìm?
**A**: Chạy query: `SELECT email FROM users WHERE role = 'admin';`

### Q: Có thể có admin không phải premium không?
**A**: Có. `role` và `is_premium` là 2 thuộc tính riêng biệt.

### Q: Admin có thể xóa admin khác không?
**A**: Hiện tại chưa có chức năng này trong UI. Phải dùng SQL.

### Q: Làm sao để admin vào được trang user?
**A**: Click "Về trang chính" trong sidebar hoặc link logo.

### Q: Sidebar không active đúng?
**A**: Check biến `$currentPage` có đúng không. Ví dụ:
- File `premium_requests.php` → `$currentPage = 'premium_requests';`
- File `index.php` → `$currentPage = 'dashboard';`

---

## 🔧 CUSTOMIZATION

### Thay đổi màu sắc

Edit `admin/includes/layout_header.php`, tìm:

```css
:root {
  --primary-color: #ff6b35;  /* ← Đổi màu chính */
  --primary-hover: #f7931e;   /* ← Đổi màu hover */
}
```

### Thay đổi logo

Tìm trong `layout_header.php`:

```html
<a href="index.php" class="sidebar-logo">
  <i class="bi bi-house-heart-fill"></i> Photobooth  <!-- ← Đổi text/icon -->
</a>
```

### Thêm/bớt menu

Edit phần `<nav class="sidebar-nav">` trong `layout_header.php`.

---

## 🎉 KẾT QUẢ

Sau khi setup, bạn có:
- ✅ Admin Panel chuyên nghiệp
- ✅ Sidebar navigation đẹp
- ✅ Dashboard với stats realtime
- ✅ Phân quyền admin/user chặt chẽ
- ✅ Dễ dàng thêm trang mới
- ✅ Responsive mobile

**URL Admin**: http://localhost:8888/Web-photobooth/admin/

---

**✨ Chúc bạn quản trị tốt! ✨**


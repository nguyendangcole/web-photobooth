# 🔐 Hướng dẫn Cấp quyền Admin

## Cách 1: Cấp quyền Admin qua phpMyAdmin (Khuyến nghị)

### Bước 1: Import SQL setup
1. Mở **phpMyAdmin**
2. Chọn database của bạn (ví dụ: `web_photobooth`)
3. Click tab **SQL**
4. Copy và chạy nội dung file `config/admin_setup.sql`:

```sql
-- Thêm cột is_admin vào bảng users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Admin role flag' 
AFTER is_premium;

-- Thêm index
SET @exist := (SELECT COUNT(*) 
               FROM information_schema.statistics 
               WHERE table_schema = DATABASE() 
               AND table_name = 'users' 
               AND index_name = 'idx_is_admin');

SET @sqlstmt := IF(@exist > 0, 
                   'SELECT ''Index idx_is_admin already exists.''', 
                   'ALTER TABLE users ADD INDEX idx_is_admin (is_admin)');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
```

### Bước 2: Thêm Admin User

#### Cách A: Qua SQL
```sql
-- Cấp quyền admin cho user có email cụ thể
UPDATE users 
SET is_admin = 1 
WHERE email = 'your_email@example.com';

-- Kiểm tra
SELECT id, name, email, is_admin 
FROM users 
WHERE is_admin = 1;
```

#### Cách B: Qua phpMyAdmin GUI
1. Mở bảng **users**
2. Click **Browse**
3. Tìm user bạn muốn cấp quyền admin
4. Click **Edit** (icon bút)
5. Đổi giá trị cột `is_admin` từ `0` → `1`
6. Click **Go** để lưu

---

## Cách 2: Script PHP tạm thời

### Tạo file `make_admin.php` trong thư mục root:

```php
<?php
// make_admin.php - Chạy file này MỘT LẦN rồi XÓA NGAY
require __DIR__ . '/config/db.php';

// ⚠️ QUAN TRỌNG: Thay email này bằng email của bạn
$ADMIN_EMAIL = 'your_email@example.com';

try {
  $pdo = db();
  
  // Kiểm tra user có tồn tại không
  $stmt = $pdo->prepare("SELECT id, name, email, is_admin FROM users WHERE email = ?");
  $stmt->execute([$ADMIN_EMAIL]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$user) {
    die("❌ Không tìm thấy user với email: $ADMIN_EMAIL");
  }
  
  if ($user['is_admin']) {
    die("✅ User này đã là admin rồi!");
  }
  
  // Cấp quyền admin
  $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
  $stmt->execute([$ADMIN_EMAIL]);
  
  echo "✅ Đã cấp quyền admin cho:<br>";
  echo "👤 Name: " . htmlspecialchars($user['name']) . "<br>";
  echo "📧 Email: " . htmlspecialchars($user['email']) . "<br>";
  echo "<br>🔐 Bạn có thể truy cập Admin Panel tại: <a href='admin/index.php'>admin/index.php</a>";
  echo "<br><br>⚠️ <strong>QUAN TRỌNG:</strong> Hãy xóa file make_admin.php ngay!";
  
} catch (Exception $e) {
  die("❌ Lỗi: " . $e->getMessage());
}
?>
```

### Cách sử dụng:
1. Tạo file `make_admin.php` trong folder gốc (cùng cấp với `README.md`)
2. Sửa `$ADMIN_EMAIL` thành email của bạn
3. Truy cập: `http://localhost/Web-photobooth/make_admin.php`
4. **XÓA FILE NGAY** sau khi chạy xong

---

## Cách 3: Thêm nhiều Admin cùng lúc

```sql
-- Cấp quyền admin cho nhiều users
UPDATE users 
SET is_admin = 1 
WHERE email IN (
  'admin1@example.com',
  'admin2@example.com',
  'admin3@example.com'
);
```

---

## Thu hồi quyền Admin

```sql
-- Gỡ quyền admin
UPDATE users 
SET is_admin = 0 
WHERE email = 'user_email@example.com';
```

---

## Kiểm tra danh sách Admin

```sql
SELECT 
  id,
  name,
  email,
  is_admin,
  is_premium,
  created_at
FROM users 
WHERE is_admin = 1
ORDER BY created_at DESC;
```

---

## Truy cập Admin Panel

Sau khi có quyền admin, bạn có thể truy cập:

- **Admin Panel**: `/admin/index.php`
- **Hoặc từ trang web chính**: Sau khi login, trong menu dropdown sẽ có link "Admin Panel"

---

## Bảo mật

⚠️ **LƯU Ý:**
- Không bao giờ commit file `make_admin.php` vào Git
- Chỉ cấp quyền admin cho người bạn tin tưởng
- Kiểm tra định kỳ danh sách admin
- Nếu có file admin tạm thời, nhớ xóa ngay sau khi sử dụng

---

## Xử lý lỗi thường gặp

### Lỗi: "Unknown column 'is_admin'"
➜ Bạn chưa chạy file `config/admin_setup.sql`

### Lỗi: "Access Denied"
➜ User của bạn chưa được cấp quyền admin (`is_admin = 0`)

### Không thấy link "Admin Panel" trong menu
➜ Reload lại trang sau khi cấp quyền admin


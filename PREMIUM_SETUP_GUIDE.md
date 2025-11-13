# 🎨 Hướng dẫn Setup Hệ thống Premium Frames

## 📋 Tổng quan
Hệ thống Premium Frames cho phép bạn phân quyền người dùng thành 2 loại:
- **FREE users**: Chỉ xem/dùng được frame thông thường
- **PREMIUM users**: Xem/dùng được TẤT CẢ frame (bao gồm cả premium)

---

## 🔧 Bước 1: Cập nhật Database

### Cách 1: Chạy file SQL trong phpMyAdmin
1. Mở **phpMyAdmin** (http://localhost:8888/phpMyAdmin/)
2. Chọn database **`myapp`**
3. Click tab **SQL**
4. Copy toàn bộ nội dung file `/config/premium_setup.sql`
5. Paste vào và click **Go**

### Cách 2: Chạy qua Terminal
```bash
cd /Applications/MAMP/htdocs/Web-photobooth
mysql -u root -p -P 8889 myapp < config/premium_setup.sql
# Password: root
```

### Kiểm tra kết quả
Chạy các query này trong phpMyAdmin để kiểm tra:

```sql
-- Kiểm tra cột mới trong bảng users
SHOW COLUMNS FROM users;
-- Bạn sẽ thấy: is_premium, premium_until

-- Kiểm tra cột mới trong bảng frames
SHOW COLUMNS FROM frames;
-- Bạn sẽ thấy: is_premium

-- Xem users hiện tại
SELECT id, name, email, is_premium, premium_until FROM users;

-- Xem frames hiện tại
SELECT id, name, layout, is_premium FROM frames;
```

---

## 🎯 Bước 2: Thử nghiệm chức năng

### A. Upload frame Premium
1. Truy cập: `http://localhost:8888/Web-photobooth/admin/frames_add.php`
2. Điền thông tin frame:
   - **Tên**: Ví dụ "Frame VIP Gold"
   - **Layout**: Chọn vertical hoặc square
   - **✅ Tick checkbox "⭐ Premium Frame"**
   - **Upload ảnh** hoặc nhập src
3. Click **Thêm**
4. Bạn sẽ thấy thông báo "✅ Đã thêm frame PREMIUM!"

### B. Quản lý Premium Users
1. Truy cập: `http://localhost:8888/Web-photobooth/admin/users_premium.php`
2. Bạn sẽ thấy danh sách tất cả users
3. **Nâng cấp user lên Premium**:
   - Click dropdown "Actions" ở user bạn muốn nâng cấp
   - Chọn "⬆️ Nâng lên Premium (1 tháng)" hoặc "(1 năm)"
4. **Gia hạn Premium**:
   - Với user đã là Premium, chọn "🔄 Gia hạn thêm..."
5. **Hạ cấp về Free**:
   - Chọn "⬇️ Hạ về Free"

### C. Kiểm tra trên giao diện người dùng

#### Với Free User:
1. Đăng nhập bằng tài khoản free
2. Vào trang Frame: `http://localhost:8888/Web-photobooth/public/?p=frame`
3. Click **"Choose Frame"**
4. ➡️ Bạn sẽ **CHỈ THẤY** frame free (không có badge Premium)

#### Với Premium User:
1. Nâng cấp user lên premium (theo hướng dẫn B)
2. Đăng nhập lại
3. Vào trang Frame và click **"Choose Frame"**
4. ➡️ Bạn sẽ thấy **TẤT CẢ** frame, kể cả frame có badge **"⭐ PREMIUM"**
5. Frame premium có:
   - Badge cam "⭐ PREMIUM" ở góc trên
   - Viền cam nổi bật
   - Shadow effect đẹp hơn

---

## 🎨 Các file đã được cập nhật

### 1. Database
- ✅ `config/premium_setup.sql` - Script setup database

### 2. Admin Panel
- ✅ `admin/frames_add.php` - Thêm checkbox Premium khi upload frame
- ✅ `admin/users_premium.php` - Trang quản lý premium users (MỚI)

### 3. Backend (AJAX)
- ✅ `ajax/frames_list.php` - Lọc frame theo premium status của user

### 4. Frontend
- ✅ `app/frame_sidebar.php` - Hiển thị badge Premium cho frame

---

## 📊 Cấu trúc Database mới

### Bảng `users`
```sql
is_premium      TINYINT(1)  DEFAULT 0    -- 0=Free, 1=Premium
premium_until   DATETIME    NULL         -- Ngày hết hạn premium
```

### Bảng `frames`
```sql
is_premium      TINYINT(1)  DEFAULT 0    -- 0=Free frame, 1=Premium frame
```

---

## 🔍 Cách hoạt động

### Logic phân quyền
1. **User chưa đăng nhập**: Không thấy frame nào (cần đăng nhập)
2. **Free user** (`is_premium = 0`): Chỉ thấy frame free
3. **Premium user** (`is_premium = 1`):
   - Nếu `premium_until` là NULL hoặc chưa hết hạn → Thấy tất cả frame
   - Nếu `premium_until` đã qua → Tự động chuyển về chế độ Free

### Query logic (trong frames_list.php)
```php
// Nếu user không premium
if (!$isPremiumUser) {
    $where[] = "is_premium = 0";  // Chỉ lấy frame free
}
// Nếu premium user thì không thêm điều kiện → lấy tất cả
```

---

## 💡 Mở rộng trong tương lai

### 1. Tích hợp Payment Gateway
Khi muốn thêm tính năng thanh toán, bạn có thể:
- Tạo trang `app/premium_upgrade.php`
- Tích hợp Stripe, PayPal, hoặc MoMo
- Sau khi thanh toán thành công, update database:
```php
$premiumUntil = date('Y-m-d H:i:s', strtotime('+1 month'));
$pdo->prepare("UPDATE users SET is_premium = 1, premium_until = ? WHERE id = ?")
    ->execute([$premiumUntil, $userId]);
```

### 2. Tự động check hết hạn Premium
Tạo cron job chạy hàng ngày:
```php
// Check và tự động hạ cấp user hết hạn
$pdo->exec("
    UPDATE users 
    SET is_premium = 0 
    WHERE is_premium = 1 
    AND premium_until IS NOT NULL 
    AND premium_until < NOW()
");
```

### 3. Email thông báo hết hạn
Gửi email nhắc nhở trước 7 ngày:
```sql
SELECT id, name, email, premium_until 
FROM users 
WHERE is_premium = 1 
AND premium_until BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
```

### 4. Thống kê Premium
Thêm dashboard admin:
- Tổng số premium users
- Doanh thu tháng
- Conversion rate (Free → Premium)
- Frame premium phổ biến nhất

---

## ❓ Troubleshooting

### Lỗi: Column 'is_premium' not found
➡️ Bạn chưa chạy SQL setup. Hãy chạy lại file `premium_setup.sql`

### Free user vẫn thấy premium frame
➡️ Kiểm tra:
1. Cache browser - Clear cache và reload
2. Session - Đăng xuất và đăng nhập lại
3. Database - Check `users.is_premium` của user đó

### Premium frame không có badge
➡️ Kiểm tra trong database:
```sql
SELECT id, name, is_premium FROM frames WHERE id = [frame_id];
```
Nếu `is_premium = 0`, update lại:
```sql
UPDATE frames SET is_premium = 1 WHERE id = [frame_id];
```

---

## 📞 Support
Nếu có vấn đề gì, kiểm tra:
1. MySQL error log: `/Applications/MAMP/logs/mysql_error.log`
2. PHP error log: `/Applications/MAMP/logs/php_error.log`
3. Browser console (F12)

---

**✨ Chúc bạn setup thành công! ✨**


# ⭐ Hệ thống Premium Frames - Photobooth App

## 📖 Tổng quan

Hệ thống Premium Frames cho phép phân quyền người dùng và frame theo 2 cấp độ:
- **FREE**: Người dùng thường chỉ xem được frame miễn phí
- **PREMIUM**: Người dùng đã nâng cấp có thể xem/dùng tất cả frame (bao gồm cả premium)

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT NHANH

### Bước 1: Chạy SQL trong phpMyAdmin

1. Mở **phpMyAdmin**: http://localhost:8888/phpMyAdmin/
2. Chọn database **`myapp`**
3. Click tab **SQL**
4. Copy nội dung file `QUICK_SQL_SETUP.txt` và paste vào
5. Click **Go** để chạy

**Hoặc** copy các câu lệnh này:

```sql
ALTER TABLE users 
ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 AFTER email_verified,
ADD COLUMN premium_until DATETIME NULL DEFAULT NULL AFTER is_premium,
ADD INDEX idx_is_premium (is_premium);

ALTER TABLE frames 
ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 AFTER layout,
ADD INDEX idx_is_premium (is_premium),
ADD INDEX idx_layout_premium (layout, is_premium);
```

### Bước 2: Kiểm tra setup thành công

Chạy query này để kiểm tra:

```sql
SHOW COLUMNS FROM users;
SHOW COLUMNS FROM frames;
```

Bạn sẽ thấy các cột mới:
- `users.is_premium`
- `users.premium_until`
- `frames.is_premium`

---

## 📂 CÁC FILE MỚI/CẬP NHẬT

### Files mới được tạo:
- ✅ `admin/users_premium.php` - Trang quản lý premium users
- ✅ `config/premium_setup.sql` - Script SQL setup
- ✅ `QUICK_SQL_SETUP.txt` - Hướng dẫn SQL nhanh
- ✅ `PREMIUM_SETUP_GUIDE.md` - Hướng dẫn chi tiết
- ✅ `README_PREMIUM.md` - File này

### Files đã cập nhật:
- ✅ `admin/frames_add.php` - Thêm checkbox Premium khi upload frame
- ✅ `ajax/frames_list.php` - Logic lọc frame theo premium status
- ✅ `app/frame_sidebar.php` - Hiển thị badge Premium
- ✅ `app/menu.php` - Hiển thị premium status trong user menu

---

## 🎯 CÁCH SỬ DỤNG

### A. Upload Frame Premium (Admin)

1. Truy cập: http://localhost:8888/Web-photobooth/admin/frames_add.php
2. Điền thông tin:
   - Tên frame
   - Chọn layout (vertical/square)
   - **✅ Tick checkbox "⭐ Premium Frame"**
   - Upload ảnh
3. Click **Thêm**

### B. Quản lý Premium Users (Admin)

1. Truy cập: http://localhost:8888/Web-photobooth/admin/users_premium.php
2. Tìm user cần nâng cấp
3. Click dropdown **"Actions"**
4. Chọn:
   - **"⬆️ Nâng lên Premium (1 tháng/1 năm)"** - Nâng cấp user
   - **"🔄 Gia hạn"** - Gia hạn premium cho user đã là premium
   - **"⬇️ Hạ về Free"** - Hạ cấp về free user

### C. Trải nghiệm Premium (User)

#### Free User:
- Vào trang Frame → Click "Choose Frame"
- Chỉ thấy frame thường (không có badge Premium)

#### Premium User:
- Vào trang Frame → Click "Choose Frame"
- Thấy TẤT CẢ frame, bao gồm frame có badge **"⭐ PREMIUM"**
- Frame premium có viền cam và shadow đẹp hơn
- Trong menu avatar sẽ hiển thị badge **"⭐ PREMIUM"** và ngày hết hạn

---

## 🔧 CẤU TRÚC DATABASE

### Bảng `users` - Thêm 2 cột:

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `is_premium` | TINYINT(1) | 0 | 0=Free, 1=Premium |
| `premium_until` | DATETIME | NULL | Ngày hết hạn premium (NULL = vĩnh viễn) |

### Bảng `frames` - Thêm 1 cột:

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `is_premium` | TINYINT(1) | 0 | 0=Free frame, 1=Premium frame |

---

## 💻 LOGIC PHÂN QUYỀN

### Backend (PHP - ajax/frames_list.php)

```php
// Kiểm tra premium status
$isPremiumUser = false;
if (!empty($_SESSION['user']['id'])) {
    $stmt = $pdo->prepare("SELECT is_premium, premium_until FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $userInfo = $stmt->fetch();
    
    if ($userInfo && $userInfo['is_premium']) {
        // Kiểm tra còn hạn không
        if ($userInfo['premium_until']) {
            $expiryDate = new DateTime($userInfo['premium_until']);
            $now = new DateTime();
            $isPremiumUser = ($now <= $expiryDate);
        } else {
            $isPremiumUser = true; // Không có expiry = vĩnh viễn
        }
    }
}

// Lọc frame
if (!$isPremiumUser) {
    $where[] = "is_premium = 0"; // Free user: chỉ lấy frame free
}
// Premium user: không filter → lấy tất cả
```

### Frontend (JavaScript - frame_sidebar.php)

```javascript
function buildItemHTML(f) {
    const isPremium = f.is_premium == 1;
    const premiumBadge = isPremium ? '<span class="premium-badge">⭐ PREMIUM</span>' : '';
    const borderStyle = isPremium ? 'border: 2px solid #ff6b35; box-shadow: 0 4px 12px rgba(255,107,53,0.3);' : '';
    
    return `
        <div class="template" style="${borderStyle}">
            ${premiumBadge}
            <img src="${imgUrl}">
            <p>${f.name}</p>
        </div>
    `;
}
```

---

## 🧪 TESTING

### Test Case 1: Free User không thấy Premium Frame

1. Tạo frame premium trong admin
2. Đăng nhập bằng tài khoản free
3. Vào trang Frame → Click "Choose Frame"
4. ✅ **Expected**: Không thấy frame premium

### Test Case 2: Premium User thấy tất cả Frame

1. Nâng cấp user lên premium (qua admin)
2. Đăng nhập lại
3. Vào trang Frame → Click "Choose Frame"
4. ✅ **Expected**: Thấy cả frame free và premium (có badge ⭐ PREMIUM)

### Test Case 3: Premium hết hạn → tự động về Free

1. Nâng cấp user với thời hạn 1 tháng
2. Update database để set `premium_until` về quá khứ:
```sql
UPDATE users SET premium_until = '2024-01-01 00:00:00' WHERE id = [user_id];
```
3. Reload trang
4. ✅ **Expected**: User tự động về chế độ Free, không thấy frame premium

### Test Case 4: Badge hiển thị đúng

1. Đăng nhập với premium user
2. Click vào avatar ở góc phải menu
3. ✅ **Expected**: Thấy badge "⭐ PREMIUM" và ngày hết hạn

---

## 🔮 KẾ HOẠCH MỞ RỘNG

### Giai đoạn 1 (Hiện tại): ✅ HOÀN THÀNH
- ✅ Phân quyền user (Free/Premium)
- ✅ Phân loại frame (Free/Premium)
- ✅ Admin panel quản lý premium
- ✅ UI hiển thị badge premium

### Giai đoạn 2: Payment Integration
- [ ] Tạo trang Premium Upgrade
- [ ] Tích hợp Stripe/PayPal
- [ ] Tự động nâng cấp sau khi thanh toán
- [ ] Email xác nhận thanh toán

### Giai đoạn 3: Advanced Features
- [ ] Gói Premium theo tháng/năm
- [ ] Discount codes & Promotions
- [ ] Referral program (giới thiệu bạn bè)
- [ ] Premium frame analytics

### Giai đoạn 4: Automation
- [ ] Cron job: Tự động hạ cấp user hết hạn
- [ ] Email nhắc nhở trước 7 ngày hết hạn
- [ ] Email marketing cho free users
- [ ] Dashboard thống kê doanh thu

---

## 📊 QUERIES HỮU ÍCH

### Đếm số Premium Users
```sql
SELECT COUNT(*) as total_premium FROM users WHERE is_premium = 1;
```

### Lấy users sắp hết hạn (7 ngày tới)
```sql
SELECT id, name, email, premium_until 
FROM users 
WHERE is_premium = 1 
AND premium_until BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY);
```

### Lấy Premium Frames phổ biến nhất
```sql
-- (Cần thêm bảng tracking khi user apply frame)
-- Sẽ implement ở giai đoạn 3
```

### Tự động hạ cấp users hết hạn
```sql
UPDATE users 
SET is_premium = 0 
WHERE is_premium = 1 
AND premium_until IS NOT NULL 
AND premium_until < NOW();
```

### Nâng 1 user lên Premium vĩnh viễn (cho testing)
```sql
UPDATE users SET is_premium = 1, premium_until = NULL WHERE id = 1;
```

---

## ❓ FAQ

**Q: Làm sao để test nhanh premium feature?**
A: Vào `admin/users_premium.php`, nâng user của bạn lên Premium, sau đó reload trang.

**Q: Frame premium có thể set ngược về free không?**
A: Có. Update database: `UPDATE frames SET is_premium = 0 WHERE id = [frame_id];`

**Q: Premium user có thể dùng frame premium trong photobook không?**
A: Có. Nếu user đã apply được frame premium ở trang Frame, thì khi save sẽ lưu ảnh đã merge frame.

**Q: Làm sao để add nhiều user premium cùng lúc?**
A: Chạy query: 
```sql
UPDATE users SET is_premium = 1, premium_until = '2025-12-31 23:59:59' WHERE id IN (1,2,3,4,5);
```

**Q: Tại sao sau khi nâng cấp vẫn không thấy frame premium?**
A: Hãy logout và login lại để refresh session.

---

## 🐛 TROUBLESHOOTING

### Lỗi: Column 'is_premium' doesn't exist
**Nguyên nhân**: Chưa chạy SQL setup
**Giải pháp**: Chạy lại file `QUICK_SQL_SETUP.txt` trong phpMyAdmin

### Free user vẫn thấy premium frame
**Kiểm tra**:
1. Clear browser cache
2. Logout và login lại
3. Check database: `SELECT id, is_premium FROM users WHERE id = [user_id];`

### Premium frame không có badge
**Kiểm tra**:
1. Check database: `SELECT id, name, is_premium FROM frames WHERE id = [frame_id];`
2. Nếu `is_premium = 0`, update: `UPDATE frames SET is_premium = 1 WHERE id = [frame_id];`
3. Clear browser cache và reload

### Không thể access admin pages
**Nguyên nhân**: Có thể do auth guard hoặc path sai
**Giải pháp**: Check đường dẫn đầy đủ: http://localhost:8888/Web-photobooth/admin/...

---

## 📞 SUPPORT

Nếu gặp vấn đề, check logs:
- MySQL error: `/Applications/MAMP/logs/mysql_error.log`
- PHP error: `/Applications/MAMP/logs/php_error.log`
- Browser console: F12 → Console tab

---

**💡 Tip**: Đọc file `PREMIUM_SETUP_GUIDE.md` để có hướng dẫn chi tiết hơn!

**✨ Happy Coding! ✨**


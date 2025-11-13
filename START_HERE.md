# 🚀 HƯỚNG DẪN BẮT ĐẦU - PREMIUM FRAMES SETUP

## ⚡ QUICK START (3 bước nhanh nhất)

### Bước 1: Update Database ⚙️

**Chọn 1 trong 2 cách:**

#### ✅ Cách A: Import lại SQL files (KHUYẾN KHÍCH - Nhanh nhất!)

```
1. Mở phpMyAdmin: http://localhost:8888/phpMyAdmin/
2. Chọn database: myapp
3. Click tab "Import"
4. Import file: config/users.sql
5. Import file: config/frames.sql
6. DONE!
```

#### ✅ Cách B: Chạy lệnh SQL (Giữ dữ liệu hiện tại)

```
1. Mở phpMyAdmin → database myapp → tab SQL
2. Copy nội dung từ file QUICK_SQL_SETUP.txt (CÁCH 2)
3. Paste và click "Go"
4. DONE!
```

---

### Bước 2: Upload Frame Premium 🖼️

```
1. Truy cập: http://localhost:8888/Web-photobooth/admin/frames_add.php
2. Điền tên frame, chọn layout
3. ✅ TICK CHECKBOX "⭐ Premium Frame"
4. Upload ảnh
5. Click "Thêm"
```

---

### Bước 3: Nâng cấp User lên Premium 👤

```
1. Truy cập: http://localhost:8888/Web-photobooth/admin/users_premium.php
2. Tìm user muốn nâng cấp
3. Click dropdown "Actions"
4. Chọn "⬆️ Nâng lên Premium (1 tháng)" hoặc "(1 năm)"
5. DONE!
```

---

## 🎯 DEMO & TEST

### Test với Free User:
1. Đăng nhập với tài khoản free
2. Vào Frame → Click "Choose Frame"
3. ➡️ Chỉ thấy frame thường (không có badge Premium)

### Test với Premium User:
1. Nâng cấp user lên Premium (Bước 3 ở trên)
2. Logout và login lại
3. Click vào avatar → Thấy badge **"⭐ PREMIUM"**
4. Vào Frame → Click "Choose Frame"
5. ➡️ Thấy TẤT CẢ frame, kể cả premium (có badge cam ⭐)

---

## 📚 TÀI LIỆU CHI TIẾT

Đọc theo thứ tự:

1. **`QUICK_SQL_SETUP.txt`** ⚡
   - Hướng dẫn SQL nhanh nhất
   - Copy/paste commands

2. **`DATABASE_UPDATE_GUIDE.md`** 📊
   - Hướng dẫn chi tiết update database
   - 3 cách setup khác nhau
   - Troubleshooting

3. **`README_PREMIUM.md`** 📖
   - Documentation đầy đủ
   - Logic phân quyền
   - FAQ & Tips

4. **`PREMIUM_SETUP_GUIDE.md`** 🔧
   - Hướng dẫn setup từng bước
   - Kế hoạch mở rộng
   - Payment integration roadmap

---

## 📁 CÁC FILE QUAN TRỌNG

### Database Files (config/):
- ✅ `users.sql` - Đã có is_premium, premium_until
- ✅ `frames.sql` - Đã có is_premium
- ✅ `premium_setup.sql` - Backup script để update
- `photobook_pages.sql` - Photobook data
- `photobook_albums.sql` - Album data
- `countries.sql`, `states.sql` - Location data

### Admin Pages (admin/):
- ✅ `frames_add.php` - Upload frame (có checkbox Premium)
- ✅ `users_premium.php` - Quản lý premium users

### API Endpoints (ajax/):
- ✅ `frames_list.php` - Lọc frame theo premium status
- `photobook_add.php` - Add to photobook
- `photobook_list.php` - List photobook pages
- `photobook_delete.php` - Delete page

### Frontend (app/):
- ✅ `frame_sidebar.php` - Hiển thị frame với badge Premium
- ✅ `menu.php` - Hiển thị premium status trong user menu
- `frame.php` - Trang chọn frame
- `photobook.php` - Trang photobook

---

## 🎨 TÍNH NĂNG ĐÃ CÓ

### ✅ Đã triển khai:
- [x] Phân quyền user (Free/Premium)
- [x] Phân loại frame (Free/Premium)
- [x] Admin upload frame premium
- [x] Admin quản lý premium users
- [x] UI badge Premium cho frame
- [x] UI premium status trong user menu
- [x] Auto-expire premium khi hết hạn
- [x] Database với index tối ưu

### 🚧 Sẽ làm sau (khi cần):
- [ ] Trang Premium Upgrade cho users
- [ ] Payment gateway (Stripe/PayPal/MoMo)
- [ ] Email notification (sắp hết hạn)
- [ ] Cron job auto-downgrade
- [ ] Premium analytics dashboard

---

## 🔧 CONFIGURATION

### Database (config/db.php):
```php
$host = 'localhost';
$port = '8889';
$dbname = 'myapp';
$username = 'root';
$password = 'root';
```

### URLs:
- **Main App**: http://localhost:8888/Web-photobooth/public/
- **Admin Upload Frame**: http://localhost:8888/Web-photobooth/admin/frames_add.php
- **Admin Premium Users**: http://localhost:8888/Web-photobooth/admin/users_premium.php
- **phpMyAdmin**: http://localhost:8888/phpMyAdmin/

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: Tôi đã import users.sql nhưng không có cột is_premium?
**A**: File users.sql cũ chưa có. Hãy dùng file users.sql mới nhất trong folder config/ (đã được update).

### Q: Làm sao test nhanh premium feature?
**A**: 
1. Vào `admin/users_premium.php`
2. Nâng user của bạn lên Premium
3. Logout và login lại
4. Vào Frame → sẽ thấy premium frames

### Q: Free user vẫn thấy premium frame?
**A**: 
1. Clear browser cache
2. Logout và login lại
3. Check database: `SELECT is_premium FROM users WHERE id = [your_id]`

### Q: Muốn set frame cũ thành premium?
**A**: Chạy SQL:
```sql
UPDATE frames SET is_premium = 1 WHERE id IN (1, 2, 3);
```

### Q: Muốn test auto-expire premium?
**A**: 
```sql
-- Set premium hết hạn 1 ngày trước
UPDATE users 
SET is_premium = 1, premium_until = DATE_SUB(NOW(), INTERVAL 1 DAY)
WHERE id = [user_id];

-- Reload page → user tự động về Free
```

---

## 🆘 LỖI THƯỜNG GẶP

### Lỗi: Column 'is_premium' not found
➡️ Chưa update database. Chạy lại Bước 1.

### Lỗi: Invalid JSON response
➡️ Check console (F12) → Xem response text → Thường là PHP error.

### Lỗi: Cannot add to photobook
➡️ 
1. Check đã đăng nhập chưa
2. Check `ajax/photobook_add.php` có lỗi không
3. Check folder `public/photobook/` có quyền write không

### Database bị lỗi, muốn reset
➡️ 
```sql
DROP DATABASE myapp;
CREATE DATABASE myapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Rồi import lại tất cả SQL files
```

---

## 🎉 SAU KHI SETUP XONG

Bạn sẽ có:
- ✅ Hệ thống Premium đầy đủ chức năng
- ✅ Admin panel dễ quản lý
- ✅ UI/UX đẹp với animations
- ✅ Code clean, dễ mở rộng
- ✅ Ready cho payment integration

**Next steps**:
1. Upload một số frame premium đẹp
2. Test với nhiều users
3. Khi cần payment → đọc "Giai đoạn 2" trong `README_PREMIUM.md`

---

## 📞 SUPPORT

Nếu gặp vấn đề:
1. Check logs:
   - MySQL: `/Applications/MAMP/logs/mysql_error.log`
   - PHP: `/Applications/MAMP/logs/php_error.log`
2. Check browser console (F12)
3. Đọc troubleshooting trong các file .md

---

**✨ Chúc bạn thành công! ✨**

**Made with ❤️ by AI Assistant**


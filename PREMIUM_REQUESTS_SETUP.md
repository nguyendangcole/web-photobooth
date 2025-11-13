# 📋 Hướng dẫn Setup Premium Requests System

## ✅ TỔNG QUAN

Hệ thống Premium Requests cho phép:
- ✅ Free users **THẤY** frame premium nhưng **KHÔNG DÙNG ĐƯỢC**
- ✅ Khi click frame premium → hiện dialog yêu cầu nâng cấp
- ✅ User gửi request nâng cấp premium
- ✅ Admin xem và approve/reject requests

---

## 🗄️ BƯỚC 1: Tạo bảng `premium_requests`

### Cách 1: Import SQL file (KHUYẾN KHÍCH)

1. Mở **phpMyAdmin**: http://localhost:8888/phpMyAdmin/
2. Chọn database **`myapp`**
3. Click tab **"Import"**
4. Chọn file: **`config/premium_requests.sql`**
5. Click **"Go"**

✅ **DONE!**

### Cách 2: Chạy SQL thủ công

Mở phpMyAdmin → database `myapp` → tab "SQL" → Copy/paste:

```sql
CREATE TABLE IF NOT EXISTS `premium_requests` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL,
  `processed_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user id',
  `notes` text DEFAULT NULL COMMENT 'Admin notes',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_requested_at` (`requested_at`),
  CONSTRAINT `fk_premium_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🧪 BƯỚC 2: Test hệ thống

### Test 1: Free user thấy frame premium

1. **Login** với user free
2. Vào trang **Frame**: http://localhost:8888/Web-photobooth/public/?p=frame
3. Click **"Choose Frame"**
4. ✅ Bạn sẽ **THẤY** frame premium (có badge ⭐ PREMIUM)
5. ✅ Click vào frame premium → Hiện dialog yêu cầu nâng cấp!

### Test 2: Gửi request premium

1. Trong dialog, click **"⭐ Nâng cấp lên Premium"**
2. Trang Premium Upgrade sẽ mở
3. Click **"⭐ Gửi yêu cầu nâng cấp Premium"**
4. ✅ Hiện modal "Yêu cầu đã được gửi thành công!"

### Test 3: Admin xem requests

1. Truy cập: http://localhost:8888/Web-photobooth/admin/premium_requests.php
2. ✅ Bạn sẽ thấy request vừa gửi với status **"PENDING"**
3. Click **"✅ Approve"**
4. Chọn thời hạn (1 tháng, 1 năm...)
5. Click **"✅ Phê duyệt"**
6. ✅ User đã được nâng cấp lên Premium!

### Test 4: Premium user dùng frame premium

1. **Logout** và **login lại** với user vừa được approve
2. Vào Frame → Click "Choose Frame"
3. Click vào frame premium
4. ✅ Frame được apply ngay (không hiện dialog nữa)!

---

## 📁 CÁC FILE ĐÃ TẠO/CẬP NHẬT

### Files mới:
- ✅ `config/premium_requests.sql` - SQL tạo bảng
- ✅ `app/premium_upgrade.php` - Trang request premium
- ✅ `ajax/premium_request.php` - API submit request
- ✅ `admin/premium_requests.php` - Admin quản lý requests

### Files đã cập nhật:
- ✅ `app/frame_sidebar.php` - Check premium khi click frame, hiện dialog
- ✅ `ajax/frames_list.php` - Free user thấy TẤT CẢ frame
- ✅ `app/router.php` - Thêm route `premium-upgrade`
- ✅ `admin/users_premium.php` - Thêm link đến requests
- ✅ `admin/frames_add.php` - Thêm link đến requests

---

## 🎯 WORKFLOW

### User Flow:
```
1. Free User → Vào Frame → Thấy frame premium (có badge ⭐)
2. Click frame premium → Hiện dialog "Cần nâng cấp Premium"
3. Click "Nâng cấp" → Vào trang Premium Upgrade
4. Click "Gửi yêu cầu" → Request được tạo (status: pending)
5. Chờ admin approve
6. Admin approve → User tự động thành Premium
7. Login lại → Dùng được frame premium!
```

### Admin Flow:
```
1. Vào admin/premium_requests.php
2. Xem danh sách requests (filter theo status)
3. Click "✅ Approve" → Chọn thời hạn → Phê duyệt
   HOẶC
   Click "❌ Reject" → Nhập lý do → Từ chối
4. User được thông báo (qua status trong trang upgrade)
```

---

## 🔍 KIỂM TRA DATABASE

### Xem tất cả requests:
```sql
SELECT pr.*, u.name as user_name, u.email 
FROM premium_requests pr
LEFT JOIN users u ON pr.user_id = u.id
ORDER BY pr.requested_at DESC;
```

### Xem requests pending:
```sql
SELECT COUNT(*) as total_pending 
FROM premium_requests 
WHERE status = 'pending';
```

### Xem requests của 1 user:
```sql
SELECT * FROM premium_requests 
WHERE user_id = [user_id] 
ORDER BY requested_at DESC;
```

---

## 🎨 UI/UX FEATURES

### Dialog Premium Upgrade:
- ✅ Icon đẹp với gradient cam
- ✅ Message rõ ràng
- ✅ Nút "Nâng cấp" nổi bật
- ✅ Nút "Để sau" để đóng

### Trang Premium Upgrade:
- ✅ Hiển thị features premium
- ✅ Form gửi request đơn giản
- ✅ Check đã có request pending
- ✅ Check đã là premium user

### Admin Panel:
- ✅ Filter theo status (All/Pending/Approved/Rejected)
- ✅ Badge màu sắc cho từng status
- ✅ Modal approve/reject với form
- ✅ Hiển thị thông tin user đầy đủ

---

## ❓ FAQ

### Q: Free user có thể thấy bao nhiêu frame premium?
**A**: Tất cả! Free user thấy TẤT CẢ frame (free + premium), nhưng chỉ dùng được frame free.

### Q: User có thể gửi nhiều requests không?
**A**: Không. Nếu đã có request pending, hệ thống sẽ báo "Bạn đã có yêu cầu đang chờ xử lý".

### Q: Admin có thể reject request không?
**A**: Có. Admin có thể reject và nhập lý do từ chối.

### Q: Sau khi approve, user có cần login lại không?
**A**: Có. User cần logout và login lại để refresh session premium status.

### Q: Có thể set premium vĩnh viễn không?
**A**: Có. Trong admin, chọn thời hạn dài (ví dụ: 10 năm) hoặc set `premium_until = NULL` trong database.

---

## 🐛 TROUBLESHOOTING

### Lỗi: Table 'premium_requests' doesn't exist
➡️ Chưa tạo bảng. Chạy lại Bước 1.

### Lỗi: Dialog không hiện khi click frame premium
➡️ 
1. Check console (F12) xem có lỗi JavaScript không
2. Check `userIsPremium` có được set đúng không
3. Clear browser cache

### Lỗi: Không submit được request
➡️ 
1. Check `ajax/premium_request.php` có lỗi không
2. Check console (F12) xem response
3. Check user đã login chưa

### Request không hiện trong admin
➡️ 
1. Check database: `SELECT * FROM premium_requests;`
2. Check user_id có đúng không
3. Reload trang admin

---

## 🎉 KẾT QUẢ

Sau khi setup, bạn có:
- ✅ Free users thấy frame premium nhưng không dùng được
- ✅ Dialog yêu cầu nâng cấp đẹp mắt
- ✅ Trang request premium chuyên nghiệp
- ✅ Admin panel quản lý requests dễ dàng
- ✅ Workflow hoàn chỉnh từ request → approve → upgrade

**Next step**: Khi cần tích hợp payment, chỉ cần thêm payment gateway vào trang `premium_upgrade.php`!

---

**✨ Happy Coding! ✨**


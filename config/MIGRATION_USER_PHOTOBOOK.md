# 🔧 Migration: Add User Isolation to Photobook

## 📋 Vấn đề

Hiện tại tất cả users đang share chung một gallery photobook. Mỗi user cần có gallery riêng của mình.

## ✅ Giải pháp

Thêm cột `user_id` vào bảng `photobook_pages` để mỗi ảnh được link với user cụ thể.

---

## 🚀 Cách thực hiện

### **Bước 1: Backup Database (QUAN TRỌNG!)**

Trước khi chạy migration, hãy backup database:

```bash
# Vào phpMyAdmin hoặc dùng command line
mysqldump -u root -p photobooth_db > backup_before_migration.sql
```

### **Bước 2: Chạy Migration SQL**

Có 2 cách:

#### **Cách 1: Qua phpMyAdmin (Khuyến nghị)**

1. Mở phpMyAdmin: http://localhost:8888/phpMyAdmin
2. Chọn database `photobooth_db`
3. Vào tab **SQL**
4. Copy và paste nội dung từ file `config/add_user_id_to_photobook.sql`
5. Click **Go** để chạy

#### **Cách 2: Qua Command Line**

```bash
cd /Applications/MAMP/htdocs/web-photobooth
mysql -u root -p photobooth_db < config/add_user_id_to_photobook.sql
```

### **Bước 3: Xử lý dữ liệu cũ (Tùy chọn)**

Sau khi migration, các ảnh cũ sẽ có `user_id = NULL`. Bạn có 2 lựa chọn:

#### **Option A: Xóa tất cả ảnh cũ (Khuyến nghị cho development)**

```sql
DELETE FROM photobook_pages WHERE user_id IS NULL;
```

#### **Option B: Gán ảnh cũ cho user đầu tiên (Nếu cần giữ lại)**

```sql
-- Gán tất cả ảnh NULL cho user đầu tiên
UPDATE photobook_pages 
SET user_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) 
WHERE user_id IS NULL;
```

#### **Option C: Giữ nguyên (Ảnh cũ sẽ không hiển thị cho bất kỳ user nào)**

Không làm gì cả. Code đã được update để chỉ hiển thị ảnh có `user_id` matching với user hiện tại.

---

## ✅ Kiểm tra Migration

Sau khi chạy migration, kiểm tra:

```sql
-- Kiểm tra cột user_id đã được thêm
SHOW COLUMNS FROM photobook_pages LIKE 'user_id';

-- Kiểm tra foreign key
SHOW CREATE TABLE photobook_pages;
```

Bạn sẽ thấy:
- ✅ Cột `user_id` trong bảng
- ✅ Index `idx_user_id`
- ✅ Foreign key constraint `fk_photobook_pages_user`

---

## 🔄 Code đã được cập nhật

Các file sau đã được update để hỗ trợ user isolation:

1. **`ajax/photobook_add.php`**
   - ✅ Lưu `user_id` khi upload ảnh mới
   - ✅ Backward compatible (vẫn hoạt động nếu chưa có cột user_id)

2. **`ajax/photobook_list.php`**
   - ✅ Chỉ hiển thị ảnh của user hiện tại
   - ✅ Filter theo `user_id`

3. **`ajax/photobook_delete.php`**
   - ✅ Chỉ cho phép xóa ảnh của chính user đó
   - ✅ Kiểm tra ownership trước khi xóa

---

## 🧪 Test sau Migration

1. **Login với user A:**
   - Upload ảnh
   - Kiểm tra chỉ thấy ảnh của user A

2. **Logout và Login với user B:**
   - Upload ảnh
   - Kiểm tra chỉ thấy ảnh của user B (không thấy ảnh của user A)

3. **Test delete:**
   - User B không thể xóa ảnh của user A
   - User B chỉ xóa được ảnh của chính mình

---

## ⚠️ Lưu ý quan trọng

1. **Backup trước khi migration:** Luôn backup database trước khi chạy migration

2. **Dữ liệu cũ:** Ảnh cũ (không có user_id) sẽ không hiển thị cho bất kỳ user nào sau migration

3. **Foreign Key:** Nếu xóa user, tất cả ảnh của user đó sẽ tự động bị xóa (CASCADE)

4. **Backward Compatibility:** Code vẫn hoạt động nếu chưa chạy migration (sẽ hiển thị tất cả ảnh)

---

## 🐛 Troubleshooting

### Lỗi: "Duplicate column name 'user_id'"
**Giải pháp:** Cột đã tồn tại, migration đã chạy rồi. Bỏ qua bước này.

### Lỗi: "Cannot add foreign key constraint"
**Giải pháp:** 
- Kiểm tra bảng `users` có tồn tại không
- Kiểm tra có dữ liệu trong `photobook_pages` với `user_id` không hợp lệ không

### Ảnh cũ vẫn hiển thị
**Giải pháp:** 
- Kiểm tra đã chạy migration chưa
- Kiểm tra code đã được update chưa
- Clear browser cache

---

## 📝 Tóm tắt

1. ✅ Backup database
2. ✅ Chạy migration SQL
3. ✅ Xử lý dữ liệu cũ (tùy chọn)
4. ✅ Test với nhiều users
5. ✅ Mỗi user giờ có gallery riêng!


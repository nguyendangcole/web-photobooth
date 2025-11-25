# Hướng dẫn Import Database vào phpMyAdmin

## File đã tạo
- **database_myadmin.sql** - File SQL hoàn chỉnh chứa tất cả các bảng và dữ liệu

## Cách import vào phpMyAdmin

### Bước 1: Mở phpMyAdmin
1. Truy cập: `http://localhost:8889/phpMyAdmin` (hoặc port của bạn)
2. Đăng nhập với tài khoản MySQL

### Bước 2: Tạo database mới (nếu chưa có)
1. Click vào tab **"Databases"**
2. Nhập tên database: `myapp` (hoặc tên bạn muốn)
3. Chọn Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### Bước 3: Import file SQL
1. Chọn database vừa tạo (click vào tên database ở sidebar bên trái)
2. Click tab **"Import"** ở menu trên
3. Click **"Choose File"** và chọn file `database_myadmin.sql`
4. Đảm bảo các tùy chọn:
   - Format: **SQL**
   - Character set: **utf8mb4**
5. Click **"Go"** hoặc **"Import"**

### Bước 4: Kiểm tra
Sau khi import thành công, bạn sẽ thấy các bảng sau:
- ✅ `countries` - Bảng quốc gia (250 quốc gia)
- ✅ `states` - Bảng tỉnh/thành phố
- ✅ `users` - Bảng người dùng
- ✅ `frames` - Bảng khung ảnh (16 frames)
- ✅ `premium_requests` - Bảng yêu cầu nâng cấp
- ✅ `photobook_albums` - Bảng album photobook
- ✅ `photobook_pages` - Bảng trang photobook

## Lưu ý
- File `database_myadmin.sql` đã được sắp xếp theo thứ tự đúng (countries → states → users → ...)
- Tất cả foreign keys và constraints đã được bao gồm
- Dữ liệu mẫu đã được import sẵn

## Nếu gặp lỗi
1. **Lỗi foreign key**: Đảm bảo import đúng thứ tự (file đã được sắp xếp sẵn)
2. **Lỗi encoding**: Chọn charset `utf8mb4` khi import
3. **Lỗi timeout**: Tăng `max_execution_time` trong php.ini hoặc import từng phần

## Cấu trúc file SQL
File `database_myadmin.sql` bao gồm:
1. Cấu hình ban đầu (SET statements)
2. Tạo bảng `countries` + dữ liệu
3. Tạo bảng `states` + dữ liệu  
4. Tạo bảng `users` + dữ liệu mẫu
5. Tạo bảng `frames` + dữ liệu (16 frames)
6. Tạo bảng `premium_requests`
7. Tạo bảng `photobook_albums`
8. Tạo bảng `photobook_pages` + dữ liệu mẫu
9. Các indexes và foreign keys
10. Commit transaction


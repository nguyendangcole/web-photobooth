# Sửa lỗi Session Conflict giữa Photobooth và Worknest

## Vấn đề
Khi login vào web Photobooth, tự động login luôn vào web Worknest (và ngược lại) vì cả 2 web dùng chung session cookie trên cùng domain `localhost:8888`.

## Giải pháp đã áp dụng

### 1. Set Session Name riêng biệt
- Photobooth: `PHOTOBOOTH_SESSION`
- Worknest: Nên set `WORKNEST_SESSION` (hoặc tên khác)

### 2. Set Cookie Path riêng biệt
- Photobooth cookie chỉ có hiệu lực trong folder `/web-photobooth/` hoặc `/Web-photobooth/`
- Worknest cookie chỉ có hiệu lực trong folder `/Worknest/`

### 3. Các file đã được sửa
- ✅ `app/config.php` - Thêm hàm `init_photobooth_session()`
- ✅ `app/includes/auth_guard.php` - Dùng session name và path riêng
- ✅ `app/auth/login.php` - Dùng session name và path riêng
- ✅ `app/auth/register.php` - Dùng session name và path riêng
- ✅ `admin/includes/admin_guard.php` - Dùng session name và path riêng
- ✅ `admin/includes/layout_header.php` - Dùng session name và path riêng
- ✅ `ajax/premium_request.php` - Dùng session name và path riêng
- ✅ `ajax/frames_list.php` - Dùng session name và path riêng

## Cách kiểm tra

### Bước 1: Xóa cookies cũ
1. Mở Developer Tools (F12)
2. Vào tab **Application** (Chrome) hoặc **Storage** (Firefox)
3. Xóa tất cả cookies cho `localhost:8888`
4. Hoặc xóa thủ công cookie `PHPSESSID` và `PHOTOBOOTH_SESSION`

### Bước 2: Test Photobooth
1. Truy cập: `http://localhost:8888/web-photobooth/public/` (hoặc path của bạn)
2. Login vào Photobooth
3. Kiểm tra cookie trong Developer Tools:
   - Tên cookie: `PHOTOBOOTH_SESSION`
   - Path: `/web-photobooth/` (hoặc `/Web-photobooth/`)

### Bước 3: Test Worknest
1. Truy cập: `http://localhost:8888/Worknest/public/` (hoặc path của bạn)
2. Kiểm tra xem có tự động login không
3. Nếu vẫn tự động login → Worknest cũng cần sửa tương tự

## Sửa Worknest (nếu cần)

Nếu Worknest vẫn bị ảnh hưởng, cần sửa tương tự:

1. Tìm file config/session của Worknest
2. Thêm code tương tự:
```php
session_name('WORKNEST_SESSION');
$cookiePath = '/Worknest/';
ini_set('session.cookie_path', $cookiePath);
session_start();
```

## Lưu ý
- Sau khi sửa, người dùng cần **logout và login lại** để cookie mới có hiệu lực
- Cookie cũ (`PHPSESSID`) vẫn có thể tồn tại, nhưng sẽ không được sử dụng nữa
- Nên xóa cookie cũ để tránh confusion

## Kết quả mong đợi
- ✅ Login Photobooth → Chỉ Photobooth có session
- ✅ Login Worknest → Chỉ Worknest có session
- ✅ 2 web hoạt động độc lập, không ảnh hưởng lẫn nhau


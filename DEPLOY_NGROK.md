# 🚀 Hướng dẫn Deploy Web bằng NGROK (Free)

## 📋 Yêu cầu
- MAMP đã cài đặt và chạy
- Web đang chạy trên localhost (port 80 hoặc 8888)
- Tài khoản NGROK (free) - đăng ký tại: https://ngrok.com/

---

## 🔧 Bước 1: Cài đặt NGROK

### Cách 1: Download trực tiếp (Khuyến nghị)
1. Truy cập: https://ngrok.com/download
2. Download cho macOS
3. Giải nén file `ngrok`
4. Di chuyển vào thư mục `/usr/local/bin/`:
   ```bash
   sudo mv ~/Downloads/ngrok /usr/local/bin/
   sudo chmod +x /usr/local/bin/ngrok
   ```

### Cách 2: Cài bằng Homebrew
```bash
brew install ngrok/ngrok/ngrok
```

### Kiểm tra cài đặt
```bash
ngrok version
```

---

## 🔑 Bước 2: Đăng ký và lấy Auth Token

1. Đăng ký tài khoản miễn phí tại: https://dashboard.ngrok.com/signup
2. Vào Dashboard: https://dashboard.ngrok.com/get-started/your-authtoken
3. Copy **Authtoken** của bạn
4. Chạy lệnh:
   ```bash
   ngrok config add-authtoken YOUR_AUTH_TOKEN
   ```

---

## 🚀 Bước 3: Chạy NGROK

### Cách 1: Chạy thủ công
```bash
# Nếu MAMP chạy trên port 80:
ngrok http 80

# Nếu MAMP chạy trên port 8888:
ngrok http 8888
```

### Cách 2: Dùng script tự động (Khuyến nghị)
Chạy script `start_ngrok.sh` trong thư mục project:
```bash
cd /Applications/MAMP/htdocs/web-photobooth
chmod +x start_ngrok.sh
./start_ngrok.sh
```

---

## 📱 Bước 4: Truy cập Web qua NGROK

Sau khi chạy NGROK, bạn sẽ thấy:

```
Forwarding    https://xxxx-xx-xx-xx-xx.ngrok-free.app -> http://localhost:80
```

### ⚠️ QUAN TRỌNG: URL đúng phải có path đầy đủ!

**URL SAI:**
```
https://abasic-coreen-slopingly.ngrok-free.dev
```
→ Sẽ hiển thị trang welcome của MAMP

**URL ĐÚNG:**
```
https://abasic-coreen-slopingly.ngrok-free.dev/web-photobooth/public/
```
hoặc
```
https://abasic-coreen-slopingly.ngrok-free.dev/WEB-PHOTOBOOTH/public/
```

**Lưu ý:** Tên thư mục có thể là `web-photobooth` hoặc `WEB-PHOTOBOOTH` tùy vào cách bạn đặt tên.

### Kiểm tra tên thư mục:
```bash
ls /Applications/MAMP/htdocs/ | grep -i photobooth
```

---

## 🎯 URL đầy đủ để truy cập các trang:

- **Trang chủ:** `https://your-ngrok-url.ngrok-free.dev/web-photobooth/public/`
- **Login:** `https://your-ngrok-url.ngrok-free.dev/web-photobooth/public/?p=login`
- **Register:** `https://your-ngrok-url.ngrok-free.dev/web-photobooth/public/?p=register`
- **Photobook:** `https://your-ngrok-url.ngrok-free.dev/web-photobooth/public/?p=photobook`

---

## ⚙️ Bước 5: Cấu hình Web (Quan trọng!)

### 5.1. Cập nhật BASE_URL trong .env (Tùy chọn)
Nếu bạn muốn BASE_URL tự động, có thể tạo file `.env`:
```env
BASE_URL=https://your-ngrok-url.ngrok-free.dev/web-photobooth/public/
```

**Lưu ý:** Web sẽ tự động detect BASE_URL từ URL hiện tại, nên không bắt buộc phải set trong .env.

### 5.2. Cập nhật OAuth Redirect URIs
Nếu bạn dùng OAuth (Google/Facebook), cần cập nhật redirect URI:
- Google: https://console.cloud.google.com/apis/credentials
- Facebook: https://developers.facebook.com/apps

Thêm URL NGROK vào allowed redirect URIs:
```
https://your-ngrok-url.ngrok-free.dev/web-photobooth/public/index.php?p=oauth-google-callback
```

---

## 🛠️ Tùy chọn nâng cao

### 1. Dùng domain tĩnh (Free plan)
NGROK free plan cho phép dùng domain tĩnh:
```bash
ngrok http 80 --domain=your-custom-name.ngrok-free.app
```

### 2. Chạy NGROK ở background
```bash
nohup ngrok http 80 > ngrok.log 2>&1 &
```

### 3. Xem NGROK dashboard
Truy cập: http://localhost:4040 để xem:
- Request logs
- Traffic inspector
- Web interface

### 4. Forward trực tiếp đến thư mục web (Nâng cao)
Nếu muốn truy cập trực tiếp `https://your-url.ngrok-free.dev` mà không cần path, có thể dùng NGROK config file:

Tạo file `~/.ngrok2/ngrok.yml`:
```yaml
tunnels:
  photobooth:
    addr: 80
    proto: http
    host_header: rewrite
    inspect: true
```

Sau đó chạy:
```bash
ngrok start photobooth
```

**Lưu ý:** Cách này phức tạp hơn và cần cấu hình Apache virtual host.

---

## ⚠️ Lưu ý quan trọng

1. **NGROK Free Plan:**
   - URL thay đổi mỗi lần restart (trừ khi dùng domain tĩnh)
   - Giới hạn số lượng connections
   - Có thể bị rate limit
   - Có warning page khi truy cập lần đầu (click "Visit Site")

2. **Bảo mật:**
   - NGROK free plan có warning page khi truy cập lần đầu
   - Người dùng cần click "Visit Site" để tiếp tục
   - Không dùng cho production, chỉ để demo/test

3. **Performance:**
   - Tốc độ phụ thuộc vào kết nối internet của bạn
   - Có thể chậm hơn so với localhost

4. **MAMP phải đang chạy:**
   - Đảm bảo MAMP đã start Apache và MySQL
   - Web phải truy cập được trên localhost:80 hoặc localhost:8888

5. **URL Path:**
   - **QUAN TRỌNG:** Luôn nhớ thêm `/web-photobooth/public/` vào cuối URL NGROK
   - Nếu không có path, sẽ thấy trang welcome của MAMP

---

## 🐛 Xử lý lỗi

### Lỗi: "command not found: ngrok"
**Giải pháp:** Đảm bảo NGROK đã được cài đặt và có trong PATH

### Lỗi: "authtoken is required"
**Giải pháp:** Chạy `ngrok config add-authtoken YOUR_TOKEN`

### Lỗi: "port already in use"
**Giải pháp:** Kiểm tra port 80 hoặc 8888 có đang được dùng không

### Web không load được / Thấy trang welcome MAMP
**Giải pháp:**
- ✅ Đảm bảo URL có path đầy đủ: `/web-photobooth/public/`
- ✅ Kiểm tra tên thư mục đúng (có thể là `WEB-PHOTOBOOTH` hoặc `web-photobooth`)
- ✅ Kiểm tra MAMP đã start chưa
- ✅ Kiểm tra firewall không chặn

### Lỗi 404 Not Found
**Giải pháp:**
- Kiểm tra path trong URL có đúng không
- Kiểm tra file `public/index.php` có tồn tại không
- Kiểm tra quyền truy cập file

---

## 📝 Tóm tắt nhanh

1. **Cài NGROK:** `brew install ngrok/ngrok/ngrok`
2. **Auth:** `ngrok config add-authtoken YOUR_TOKEN`
3. **Chạy:** `ngrok http 80` (hoặc port 8888)
4. **Truy cập:** `https://your-url.ngrok-free.dev/web-photobooth/public/`
5. **Share:** Copy URL đầy đủ cho người khác

---

## 🎉 Hoàn thành!

Sau khi setup xong, bạn có thể:
- ✅ Share URL NGROK cho người khác (nhớ có path `/web-photobooth/public/`)
- ✅ Test web trên mobile/tablet
- ✅ Demo web cho client
- ✅ Test OAuth, email, và các tính năng cần public URL

**Lưu ý:** URL NGROK sẽ thay đổi mỗi lần restart. Nếu cần URL cố định, cân nhắc upgrade plan hoặc dùng domain tĩnh.

# 🚀 Hướng dẫn Deploy Web bằng NGROK (Free)

## 📋 Yêu cầu
- MAMP đã cài đặt và chạy
- Web đang chạy trên localhost (thường là port 8888)
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
# Mở terminal và chạy:
ngrok http 8888
```

**Lưu ý:** Port 8888 là port mặc định của MAMP. Nếu bạn dùng port khác, thay đổi số port.

### Cách 2: Dùng script tự động (Khuyến nghị)
Chạy script `start_ngrok.sh` trong thư mục project:
```bash
cd /Applications/MAMP/htdocs/web-photobooth
chmod +x start_ngrok.sh
./start_ngrok.sh
```

---

## 📱 Bước 4: Lấy URL công khai

Sau khi chạy NGROK, bạn sẽ thấy:

```
Forwarding    https://xxxx-xx-xx-xx-xx.ngrok-free.app -> http://localhost:8888
```

**URL công khai:** `https://xxxx-xx-xx-xx-xx.ngrok-free.app`

Bạn có thể:
- Copy URL này và gửi cho người khác
- Họ có thể truy cập web của bạn từ bất kỳ đâu
- URL sẽ thay đổi mỗi lần restart NGROK (trừ khi dùng plan có trả phí)

---

## ⚙️ Bước 5: Cấu hình Web (Quan trọng!)

### 5.1. Cập nhật BASE_URL trong .env
Tạo hoặc cập nhật file `.env`:
```env
BASE_URL=https://your-ngrok-url.ngrok-free.app/WEB-PHOTOBOOTH/public/
```

### 5.2. Cập nhật OAuth Redirect URIs
Nếu bạn dùng OAuth (Google/Facebook), cần cập nhật redirect URI:
- Google: https://console.cloud.google.com/apis/credentials
- Facebook: https://developers.facebook.com/apps

Thêm URL NGROK vào allowed redirect URIs:
```
https://your-ngrok-url.ngrok-free.app/WEB-PHOTOBOOTH/public/index.php?p=oauth-google-callback
```

---

## 🛠️ Tùy chọn nâng cao

### 1. Dùng domain tĩnh (Free plan)
NGROK free plan cho phép dùng domain tĩnh:
```bash
ngrok http 8888 --domain=your-custom-name.ngrok-free.app
```

### 2. Chạy NGROK ở background
```bash
nohup ngrok http 8888 > ngrok.log 2>&1 &
```

### 3. Xem NGROK dashboard
Truy cập: http://localhost:4040 để xem:
- Request logs
- Traffic inspector
- Web interface

---

## ⚠️ Lưu ý quan trọng

1. **NGROK Free Plan:**
   - URL thay đổi mỗi lần restart (trừ khi dùng domain tĩnh)
   - Giới hạn số lượng connections
   - Có thể bị rate limit

2. **Bảo mật:**
   - NGROK free plan có warning page khi truy cập lần đầu
   - Người dùng cần click "Visit Site" để tiếp tục
   - Không dùng cho production, chỉ để demo/test

3. **Performance:**
   - Tốc độ phụ thuộc vào kết nối internet của bạn
   - Có thể chậm hơn so với localhost

4. **MAMP phải đang chạy:**
   - Đảm bảo MAMP đã start Apache và MySQL
   - Web phải truy cập được trên localhost:8888

---

## 🐛 Xử lý lỗi

### Lỗi: "command not found: ngrok"
**Giải pháp:** Đảm bảo NGROK đã được cài đặt và có trong PATH

### Lỗi: "authtoken is required"
**Giải pháp:** Chạy `ngrok config add-authtoken YOUR_TOKEN`

### Lỗi: "port already in use"
**Giải pháp:** Kiểm tra port 8888 có đang được dùng không, hoặc dùng port khác

### Web không load được
**Giải pháp:**
- Kiểm tra MAMP đã start chưa
- Kiểm tra URL đúng chưa (có /WEB-PHOTOBOOTH/public/ chưa)
- Kiểm tra firewall không chặn

---

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra MAMP đang chạy
2. Kiểm tra NGROK đã authenticated
3. Xem logs tại http://localhost:4040
4. Kiểm tra file `.env` có BASE_URL đúng chưa

---

## 🎉 Hoàn thành!

Sau khi setup xong, bạn có thể:
- ✅ Share URL NGROK cho người khác
- ✅ Test web trên mobile/tablet
- ✅ Demo web cho client
- ✅ Test OAuth, email, và các tính năng cần public URL

**Lưu ý:** URL NGROK sẽ thay đổi mỗi lần restart. Nếu cần URL cố định, cân nhắc upgrade plan hoặc dùng domain tĩnh.


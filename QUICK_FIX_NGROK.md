# 🔧 Sửa lỗi ERR_NGROK_3200 - NGROK Offline

## ❌ Lỗi: ERR_NGROK_3200
**Nguyên nhân:** NGROK tunnel đã bị ngắt kết nối

## ✅ Cách sửa nhanh:

### Bước 1: Kiểm tra MAMP đang chạy
1. Mở MAMP
2. Đảm bảo **Apache** và **MySQL** đang chạy (nút Start phải xanh)
3. Nếu chưa chạy → Click **Start Servers**

### Bước 2: Khởi động lại NGROK

**Cách 1: Dùng script (Khuyến nghị)**
```bash
cd /Applications/MAMP/htdocs/web-photobooth
./start_ngrok.sh
```

**Cách 2: Chạy thủ công**
```bash
ngrok http 80
```

### Bước 3: Lấy URL mới
Sau khi chạy NGROK, bạn sẽ thấy URL mới:
```
Forwarding    https://xxxx-xx-xx-xx-xx.ngrok-free.app -> http://localhost:80
```

**Lưu ý:** URL sẽ thay đổi mỗi lần restart NGROK (trừ khi dùng domain tĩnh)

### Bước 4: Truy cập với URL mới
```
https://new-url.ngrok-free.app/web-photobooth/public/
```

---

## 🚨 Lưu ý quan trọng:

1. **NGROK phải chạy liên tục:**
   - Đừng đóng terminal khi NGROK đang chạy
   - Nếu đóng terminal → NGROK sẽ dừng → URL sẽ offline

2. **Chạy NGROK ở background (Tùy chọn):**
   ```bash
   nohup ngrok http 80 > ngrok.log 2>&1 &
   ```
   - Cách này cho phép đóng terminal mà NGROK vẫn chạy
   - Xem logs: `tail -f ngrok.log`
   - Dừng: `pkill ngrok`

3. **URL thay đổi:**
   - Mỗi lần restart NGROK, URL sẽ thay đổi
   - Nếu cần URL cố định → Dùng domain tĩnh (cần upgrade plan)

---

## 🔍 Kiểm tra NGROK đang chạy:

```bash
ps aux | grep ngrok | grep -v grep
```

Nếu có output → NGROK đang chạy
Nếu không có output → NGROK đã dừng

---

## 📞 Nếu vẫn lỗi:

1. Kiểm tra MAMP đang chạy
2. Kiểm tra port 80 có đang được dùng không
3. Kiểm tra internet connection
4. Thử restart NGROK lại


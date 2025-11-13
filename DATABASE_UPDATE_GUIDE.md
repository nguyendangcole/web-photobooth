# 📊 Hướng dẫn Update Database cho Premium Features

## ✅ TÓM TẮT

Các file SQL trong folder `/config/` **ĐÃ ĐƯỢC CẬP NHẬT** với đầy đủ cột premium:
- ✅ `users.sql` - Có sẵn `is_premium`, `premium_until`, và index
- ✅ `frames.sql` - Có sẵn `is_premium` và index
- ✅ `premium_setup.sql` - File backup để update database cũ

---

## 🚀 CÁC CÁCH SETUP

### 📦 CÁCH 1: Import lại toàn bộ (KHUYẾN KHÍCH - Nhanh nhất)

**Ưu điểm**: Nhanh, đơn giản, đảm bảo 100% đúng cấu trúc

**Nhược điểm**: Mất dữ liệu hiện tại (users và frames)

#### Các bước:

1. **Backup dữ liệu quan trọng** (nếu cần):
```sql
-- Backup users hiện tại
SELECT * FROM users INTO OUTFILE '/tmp/users_backup.csv';

-- Hoặc export qua phpMyAdmin
```

2. **Mở phpMyAdmin**: http://localhost:8888/phpMyAdmin/

3. **Chọn database `myapp`**

4. **Drop tables cũ** (nếu muốn import lại):
```sql
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS frames;
```

5. **Click tab "Import"**

6. **Import lần lượt các file**:
   - Chọn file `config/users.sql` → Click "Go"
   - Chọn file `config/frames.sql` → Click "Go"
   - (Optional) Import lại `photobook_pages.sql`, `photobook_albums.sql` nếu cần

7. **Kiểm tra**:
```sql
SHOW COLUMNS FROM users;
SHOW COLUMNS FROM frames;
```

Bạn sẽ thấy:
- `users.is_premium` (tinyint)
- `users.premium_until` (datetime)
- `frames.is_premium` (tinyint)

✅ **DONE!**

---

### 🔧 CÁCH 2: Update database hiện tại (Giữ nguyên dữ liệu)

**Ưu điểm**: Giữ nguyên users và frames hiện tại

**Nhược điểm**: Phức tạp hơn một chút

#### Các bước:

1. **Kiểm tra xem đã có cột chưa**:

Mở phpMyAdmin → Chọn database `myapp` → Tab "SQL" → Chạy:

```sql
SHOW COLUMNS FROM users WHERE Field IN ('is_premium', 'premium_until');
SHOW COLUMNS FROM frames WHERE Field = 'is_premium';
```

2. **Nếu CHƯA có kết quả** → Chạy các câu lệnh sau:

```sql
-- Thêm cột vào users
ALTER TABLE users 
ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Premium user status' AFTER email_verified;

ALTER TABLE users 
ADD COLUMN premium_until DATETIME NULL DEFAULT NULL COMMENT 'Premium expiry date' AFTER is_premium;

ALTER TABLE users 
ADD INDEX idx_is_premium (is_premium);

-- Thêm cột vào frames
ALTER TABLE frames 
ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Premium frame flag' AFTER layout;

ALTER TABLE frames 
ADD INDEX idx_is_premium (is_premium);

ALTER TABLE frames 
ADD INDEX idx_layout_premium (layout, is_premium);
```

3. **Kiểm tra lại**:

```sql
SHOW COLUMNS FROM users;
SHOW COLUMNS FROM frames;
SELECT id, name, email, is_premium, premium_until FROM users LIMIT 5;
SELECT id, name, layout, is_premium FROM frames LIMIT 5;
```

✅ **DONE!**

---

### 🤖 CÁCH 3: Chạy file premium_setup.sql (Tự động)

File này sẽ tự động kiểm tra và thêm cột nếu chưa có:

1. Mở phpMyAdmin → database `myapp` → tab "Import"
2. Chọn file `config/premium_setup.sql`
3. Click "Go"

File này sẽ:
- Kiểm tra xem cột đã tồn tại chưa
- Chỉ thêm nếu chưa có
- Không làm mất dữ liệu

✅ **DONE!**

---

## 🔍 KIỂM TRA SAU KHI SETUP

### Kiểm tra cấu trúc bảng:

```sql
-- Xem tất cả cột của users
SHOW COLUMNS FROM users;

-- Xem tất cả cột của frames  
SHOW COLUMNS FROM frames;

-- Xem indexes
SHOW INDEXES FROM users WHERE Key_name LIKE '%premium%';
SHOW INDEXES FROM frames WHERE Key_name LIKE '%premium%';
```

### Kiểm tra dữ liệu:

```sql
-- Xem users
SELECT id, name, email, is_premium, premium_until FROM users;

-- Xem frames
SELECT id, name, layout, is_premium FROM frames;
```

---

## 🎯 CẤU TRÚC DATABASE SAU KHI UPDATE

### Bảng `users`:

| Column | Type | Default | Null | Comment |
|--------|------|---------|------|---------|
| id | INT UNSIGNED | AUTO_INCREMENT | NO | Primary Key |
| name | VARCHAR(120) | - | NO | - |
| email | VARCHAR(190) | - | NO | UNIQUE |
| password_hash | VARCHAR(255) | NULL | YES | - |
| provider | ENUM | 'local' | NO | local/google/facebook |
| provider_id | VARCHAR(190) | NULL | YES | - |
| avatar_url | VARCHAR(255) | NULL | YES | - |
| email_verified | TINYINT(1) | 0 | NO | - |
| **is_premium** | **TINYINT(1)** | **0** | **NO** | **Premium status** |
| **premium_until** | **DATETIME** | **NULL** | **YES** | **Expiry date** |
| verification_token | VARCHAR(64) | NULL | YES | - |
| reset_token | VARCHAR(64) | NULL | YES | - |
| reset_expires_at | DATETIME | NULL | YES | - |
| created_at | DATETIME | CURRENT_TIMESTAMP | NO | - |
| updated_at | DATETIME | CURRENT_TIMESTAMP | NO | ON UPDATE |
| address | VARCHAR(255) | NULL | YES | - |
| country_id | INT | NULL | YES | FK to countries |
| state_id | INT | NULL | YES | FK to states |
| city_name | VARCHAR(255) | NULL | YES | - |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (email)
- KEY (provider, provider_id)
- KEY (country_id)
- KEY (state_id)
- **KEY idx_is_premium (is_premium)** ← MỚI

### Bảng `frames`:

| Column | Type | Default | Null | Comment |
|--------|------|---------|------|---------|
| id | INT | AUTO_INCREMENT | NO | Primary Key |
| name | VARCHAR(100) | - | NO | - |
| src | VARCHAR(255) | - | NO | Path to image |
| layout | VARCHAR(50) | 'square' | YES | vertical/square |
| **is_premium** | **TINYINT(1)** | **0** | **NO** | **Premium flag** |

**Indexes:**
- PRIMARY KEY (id)
- **KEY idx_is_premium (is_premium)** ← MỚI
- **KEY idx_layout_premium (layout, is_premium)** ← MỚI

---

## 🧪 TEST DATABASE

### Test 1: Kiểm tra cột tồn tại

```sql
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT, 
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'myapp' 
AND TABLE_NAME = 'users'
AND COLUMN_NAME IN ('is_premium', 'premium_until');
```

Expected: 2 rows

### Test 2: Set user thành premium

```sql
-- Nâng user id=4 lên premium (1 tháng)
UPDATE users 
SET is_premium = 1, 
    premium_until = DATE_ADD(NOW(), INTERVAL 1 MONTH)
WHERE id = 4;

-- Kiểm tra
SELECT id, name, email, is_premium, premium_until FROM users WHERE id = 4;
```

### Test 3: Set frame thành premium

```sql
-- Set frame id=1,2,3 thành premium
UPDATE frames SET is_premium = 1 WHERE id IN (1, 2, 3);

-- Kiểm tra
SELECT id, name, layout, is_premium FROM frames WHERE is_premium = 1;
```

### Test 4: Query performance (với index)

```sql
-- Query này sẽ nhanh nhờ index idx_is_premium
EXPLAIN SELECT * FROM users WHERE is_premium = 1;

-- Query này sẽ nhanh nhờ index idx_layout_premium  
EXPLAIN SELECT * FROM frames WHERE layout = 'vertical' AND is_premium = 0;
```

---

## ❓ TROUBLESHOOTING

### Lỗi: Column 'is_premium' already exists

**Nguyên nhân**: Bạn đã chạy ALTER TABLE trước đó

**Giải pháp**: Bỏ qua lỗi này, cột đã tồn tại rồi. Hoặc dùng:

```sql
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS is_premium TINYINT(1) NOT NULL DEFAULT 0;
```

### Lỗi: Duplicate key name 'idx_is_premium'

**Nguyên nhân**: Index đã tồn tại

**Giải pháp**: Kiểm tra:

```sql
SHOW INDEXES FROM users WHERE Key_name = 'idx_is_premium';
```

Nếu có rồi thì OK, không cần thêm nữa.

### Lỗi: Cannot add foreign key constraint

**Nguyên nhân**: Có thể do bảng `countries` hoặc `states` chưa có

**Giải pháp**: Import `countries.sql` và `states.sql` trước

### Database bị lỗi, muốn reset lại

**Giải pháp**: Drop và tạo lại database:

```sql
-- Backup trước!
DROP DATABASE myapp;
CREATE DATABASE myapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myapp;

-- Rồi import lại tất cả file .sql trong folder config/
```

---

## 📝 NOTES

1. **Collation**: Database dùng `utf8mb4_unicode_ci` hoặc `utf8mb4_0900_ai_ci` (MySQL 8.0)

2. **AUTO_INCREMENT**: Bắt đầu từ 1, có thể thay đổi sau khi import

3. **Foreign Keys**: 
   - `users.country_id` → `countries.id`
   - `users.state_id` → `states.id`
   - `photobook_pages.album_id` → `photobook_albums.id`

4. **Default Values**:
   - `users.is_premium` = 0 (Free user)
   - `users.premium_until` = NULL (Không giới hạn hoặc chưa set)
   - `frames.is_premium` = 0 (Free frame)

---

## 🎉 KẾT LUẬN

Sau khi setup xong, bạn có thể:
- ✅ Upload frame premium trong admin
- ✅ Nâng cấp user lên premium
- ✅ Hệ thống tự động lọc frame theo quyền
- ✅ Ready cho tích hợp payment sau này

**Next steps**: Đọc `README_PREMIUM.md` để biết cách sử dụng!


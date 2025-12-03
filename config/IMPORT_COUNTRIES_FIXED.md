# 🔧 Hướng dẫn Import Countries Data (Đã sửa)

## Vấn đề:
File `countries.sql` có nhiều cột hơn bảng `countries` trong database → lỗi "Column count doesn't match"

## Giải pháp:

### Cách 1: Import chỉ INSERT statements (Khuyến nghị)

1. Vào phpMyAdmin → chọn database
2. Tab **SQL** (không phải Import)
3. Copy và paste đoạn SQL sau (chỉ INSERT, bỏ qua các cột không có):

```sql
-- Tắt foreign key checks tạm thời
SET FOREIGN_KEY_CHECKS=0;

-- Xóa dữ liệu cũ (nếu có)
TRUNCATE TABLE `countries`;

-- Import dữ liệu (chỉ các cột có trong bảng)
-- Bạn cần extract chỉ các cột: id, name, iso3, numeric_code, iso2, phonecode, capital, currency, currency_name, currency_symbol, tld, native, region, subregion, latitude, longitude, emoji, emojiU, created_at, updated_at, flag, wikiDataId
```

### Cách 2: Sửa lại cấu trúc bảng để khớp với dữ liệu

Chạy SQL sau để thêm các cột còn thiếu:

```sql
ALTER TABLE `countries` 
ADD COLUMN `population` bigint unsigned DEFAULT NULL AFTER `native`,
ADD COLUMN `gdp` bigint unsigned DEFAULT NULL AFTER `population`,
ADD COLUMN `region_id` mediumint unsigned DEFAULT NULL AFTER `region`,
ADD COLUMN `subregion_id` mediumint unsigned DEFAULT NULL AFTER `subregion`,
ADD COLUMN `nationality` varchar(255) DEFAULT NULL AFTER `subregion_id`,
ADD COLUMN `timezones` text DEFAULT NULL AFTER `nationality`,
ADD COLUMN `translations` text DEFAULT NULL AFTER `timezones`;
```

Sau đó import file `countries.sql` bình thường.

### Cách 3: Import file countries.sql nhưng bỏ qua phần CREATE TABLE

1. Mở file `countries.sql` bằng text editor
2. Xóa phần `CREATE TABLE` (từ dòng đầu đến trước `INSERT INTO`)
3. Chỉ giữ lại phần `INSERT INTO`
4. Import file đã sửa

---

## Khuyến nghị:
**Dùng Cách 2** (thêm cột vào bảng) vì dữ liệu sẽ đầy đủ hơn.


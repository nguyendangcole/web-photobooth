# 🌍 Hướng dẫn Setup Country, State, City

## Bước 1: Import Countries vào Database

1. Mở **phpMyAdmin**
2. Chọn database của bạn (ví dụ: `myapp`)
3. Click tab **Import**
4. Click **Choose File** → chọn file `config/countries.sql`
5. Scroll xuống dưới → Click **Go**
6. Đợi import xong (có 250 countries)

**Hoặc** chạy SQL trực tiếp:
- Click tab **SQL**
- Copy toàn bộ nội dung file `config/countries.sql`
- Paste vào và click **Go**

---

## Bước 2: Import States vào Database

1. Vẫn trong phpMyAdmin
2. Click tab **Import**
3. Click **Choose File** → chọn file `config/states.sql`
4. Click **Go**
5. Đợi import xong (có ~5000 states)

**Lưu ý:** File `states.sql` có liên kết với `countries` qua `country_id`

---

## Bước 3: Cập nhật bảng Users (nếu chưa có cột)

Kiểm tra xem bảng `users` đã có các cột sau chưa:
- `country_id`
- `state_id`
- `city_name`

### Nếu chưa có, chạy SQL sau:

```sql
-- Thêm cột country_id
ALTER TABLE users 
ADD COLUMN country_id INT DEFAULT NULL 
COMMENT 'Foreign key to countries table' 
AFTER address;

-- Thêm cột state_id
ALTER TABLE users 
ADD COLUMN state_id INT DEFAULT NULL 
COMMENT 'Foreign key to states table' 
AFTER country_id;

-- Thêm cột city_name
ALTER TABLE users 
ADD COLUMN city_name VARCHAR(255) DEFAULT NULL 
COMMENT 'City name from JSON' 
AFTER state_id;

-- Thêm index
ALTER TABLE users ADD INDEX idx_country_id (country_id);
ALTER TABLE users ADD INDEX idx_state_id (state_id);
```

---

## Bước 4: File cities.json đã sẵn sàng

File `config/cities.json` chứa **148,607 cities** từ tất cả các quốc gia.

Cấu trúc JSON:
```json
{
  "id": 52,
  "name": "Ashkāsham",
  "state_id": 3901,
  "state_code": "BDS",
  "state_name": "Badakhshan",
  "country_id": 1,
  "country_code": "AF",
  "country_name": "Afghanistan",
  "latitude": "36.68333000",
  "longitude": "71.53333000"
}
```

**Không cần import vào database!** Cities sẽ được load trực tiếp từ JSON file khi user chọn State.

---

## Bước 5: Kiểm tra Data đã import

```sql
-- Kiểm tra countries
SELECT COUNT(*) as total_countries FROM countries;
-- Kết quả: 250

-- Kiểm tra states
SELECT COUNT(*) as total_states FROM states;
-- Kết quả: ~5000

-- Xem một số countries
SELECT id, name, iso2 FROM countries LIMIT 10;

-- Xem states của một country (ví dụ: Vietnam = country_id 240)
SELECT id, name, country_id FROM states WHERE country_id = 240;
```

---

## Bước 6: Cấu trúc Database

### Bảng `countries`
```
id (mediumint) - Primary Key
name (varchar) - Tên quốc gia
iso2 (char 2) - Mã ISO (VD: VN, US)
iso3 (char 3) - Mã ISO 3 ký tự
phonecode (varchar) - Mã điện thoại
```

### Bảng `states`
```
id (mediumint) - Primary Key
name (varchar) - Tên state/province
country_id (mediumint) - Foreign key → countries.id
country_code (char 2) - Mã quốc gia
```

### Bảng `users` (các cột location)
```
country_id (int) - ID quốc gia
state_id (int) - ID state/province
city_name (varchar) - Tên city (từ JSON, không lưu ID)
```

---

## Bước 7: Logic hoạt động

1. **User chọn Country** → Load danh sách từ DB `countries`
2. **User chọn State** → Load danh sách từ DB `states` WHERE `country_id = ?`
3. **User chọn City** → Load từ `cities.json`, filter theo `state_id`

---

## API Endpoints cần tạo

### 1. `/ajax/get_countries.php`
```php
SELECT id, name, iso2 FROM countries ORDER BY name ASC
```

### 2. `/ajax/get_states.php?country_id=X`
```php
SELECT id, name FROM states WHERE country_id = ? ORDER BY name ASC
```

### 3. `/ajax/get_cities.php?state_id=X`
```php
// Đọc cities.json
// Filter theo state_id
// Return JSON
```

---

## Quick Check Commands

```sql
-- Tìm Vietnam
SELECT * FROM countries WHERE name = 'Vietnam';
-- Result: id = 240

-- Lấy các tỉnh/thành phố của Vietnam
SELECT * FROM states WHERE country_id = 240 ORDER BY name;
-- Result: Hà Nội, Hồ Chí Minh, Đà Nẵng, etc.

-- Xem user có location
SELECT id, name, email, country_id, state_id, city_name 
FROM users 
WHERE country_id IS NOT NULL;
```

---

## Troubleshooting

### Lỗi: "Table 'countries' doesn't exist"
➜ Bạn chưa import file `countries.sql`

### Lỗi: "Table 'states' doesn't exist"
➜ Bạn chưa import file `states.sql`

### Lỗi: "Column 'country_id' not found in users"
➜ Chạy SQL ở Bước 3 để thêm cột

### File cities.json quá lớn, load chậm
➜ Có thể cache hoặc chỉ load cities của state đã chọn (filter server-side)

---

## Tổng kết

✅ Countries: Import từ SQL → Table `countries`  
✅ States: Import từ SQL → Table `states`  
✅ Cities: Dùng JSON file (148k cities) → `cities.json`  
✅ Users: Có 3 cột `country_id`, `state_id`, `city_name`

**Tiếp theo:** Tạo form register và các API endpoints để load data!


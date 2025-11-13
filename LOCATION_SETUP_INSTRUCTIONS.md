# 📍 Hướng dẫn Setup Country, State, City - CHI TIẾT

## 📋 TÓM TẮT

Hệ thống location gồm:
- ✅ **Countries** (250 quốc gia) → Import từ `countries.sql`
- ✅ **States** (~5,000 tỉnh/bang) → Import từ `states.sql`
- ✅ **Cities** (148,607 thành phố) → Dùng file `cities.json`

---

## 🚀 BƯỚC 1: IMPORT COUNTRIES

### Cách 1: Import bằng phpMyAdmin (Khuyến nghị)

1. Mở **phpMyAdmin** → `http://localhost/phpMyAdmin`
2. Đăng nhập (user: `root`, password: `root`)
3. Chọn database `myapp` (hoặc tên database của bạn)
4. Click tab **Import**
5. Click **Choose File** → chọn file:
   ```
   /Applications/MAMP/htdocs/Web-photobooth/config/countries.sql
   ```
6. Scroll xuống → Click **Go**
7. Đợi ~10-30 giây → Xong!

### Kiểm tra:
```sql
SELECT COUNT(*) FROM countries;
-- Kết quả: 250

SELECT * FROM countries WHERE name = 'Vietnam';
-- Kết quả: id = 240, iso2 = VN
```

---

## 🚀 BƯỚC 2: IMPORT STATES

### Import bằng phpMyAdmin

1. Vẫn trong phpMyAdmin
2. Đảm bảo đang ở database `myapp`
3. Click tab **Import**
4. Click **Choose File** → chọn file:
   ```
   /Applications/MAMP/htdocs/Web-photobooth/config/states.sql
   ```
5. Click **Go**
6. Đợi ~1-2 phút (file lớn hơn) → Xong!

### Kiểm tra:
```sql
SELECT COUNT(*) FROM states;
-- Kết quả: ~5,000

-- Xem states của Vietnam (country_id = 240)
SELECT * FROM states WHERE country_id = 240 ORDER BY name;
-- Kết quả: An Giang, Bà Rịa - Vũng Tàu, Bắc Giang, ...
```

---

## 🚀 BƯỚC 3: CẬP NHẬT BẢNG USERS

### Chạy SQL để thêm cột location

1. Trong phpMyAdmin, click tab **SQL**
2. Copy và paste nội dung file `config/location_setup.sql`:

```sql
-- Thêm cột country_id
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS country_id INT DEFAULT NULL 
COMMENT 'Foreign key to countries table' 
AFTER address;

-- Thêm cột state_id
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS state_id INT DEFAULT NULL 
COMMENT 'Foreign key to states table' 
AFTER country_id;

-- Thêm cột city_name
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS city_name VARCHAR(255) DEFAULT NULL 
COMMENT 'City name from JSON' 
AFTER state_id;

-- Thêm index
ALTER TABLE users ADD INDEX idx_country_id (country_id);
ALTER TABLE users ADD INDEX idx_state_id (state_id);
```

3. Click **Go**

### Kiểm tra:
```sql
DESCRIBE users;
-- Sẽ thấy các cột: country_id, state_id, city_name
```

---

## 🚀 BƯỚC 4: TEST CÁC API

### Test API lấy countries
```
http://localhost/Web-photobooth/ajax/get_countries.php
```

Kết quả:
```json
{
  "success": true,
  "data": [
    {"id": 1, "name": "Afghanistan", "iso2": "AF", ...},
    {"id": 2, "name": "Albania", "iso2": "AL", ...}
  ],
  "total": 250
}
```

### Test API lấy states (ví dụ Vietnam = 240)
```
http://localhost/Web-photobooth/ajax/get_states.php?country_id=240
```

Kết quả:
```json
{
  "success": true,
  "data": [
    {"id": 3776, "name": "An Giang", "country_id": 240, ...},
    {"id": 3778, "name": "Bà Rịa - Vũng Tàu", ...}
  ],
  "total": 63
}
```

### Test API lấy cities (ví dụ Hà Nội = state_id 3782)
```
http://localhost/Web-photobooth/ajax/get_cities.php?state_id=3782
```

Kết quả:
```json
{
  "success": true,
  "data": [
    {"id": 58135, "name": "Ba Vì", "state_id": 3782, ...},
    {"id": 58136, "name": "Hanoi", ...}
  ],
  "total": 30
}
```

---

## 📝 BƯỚC 5: TÍCH HỢP VÀO FORM REGISTER

Giờ bạn có thể thêm 3 dropdown vào form register:

### HTML Structure:
```html
<div class="mb-3">
  <label>Country</label>
  <select id="country" name="country_id" class="form-select">
    <option value="">-- Select Country --</option>
  </select>
</div>

<div class="mb-3">
  <label>State/Province</label>
  <select id="state" name="state_id" class="form-select" disabled>
    <option value="">-- Select State --</option>
  </select>
</div>

<div class="mb-3">
  <label>City</label>
  <select id="city" name="city_name" class="form-select" disabled>
    <option value="">-- Select City --</option>
  </select>
</div>
```

### JavaScript Logic:
```javascript
// 1. Load countries khi page load
fetch('../ajax/get_countries.php')
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      data.data.forEach(country => {
        $('#country').append(`<option value="${country.id}">${country.name}</option>`);
      });
    }
  });

// 2. Khi chọn country → load states
$('#country').change(function() {
  const countryId = $(this).val();
  $('#state').prop('disabled', !countryId).empty().append('<option value="">-- Select State --</option>');
  $('#city').prop('disabled', true).empty().append('<option value="">-- Select City --</option>');
  
  if (countryId) {
    fetch(`../ajax/get_states.php?country_id=${countryId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          data.data.forEach(state => {
            $('#state').append(`<option value="${state.id}">${state.name}</option>`);
          });
        }
      });
  }
});

// 3. Khi chọn state → load cities
$('#state').change(function() {
  const stateId = $(this).val();
  $('#city').prop('disabled', !stateId).empty().append('<option value="">-- Select City --</option>');
  
  if (stateId) {
    fetch(`../ajax/get_cities.php?state_id=${stateId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          data.data.forEach(city => {
            $('#city').append(`<option value="${city.name}">${city.name}</option>`);
          });
        }
      });
  }
});
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Import `countries.sql` vào phpMyAdmin (250 countries)
- [ ] Import `states.sql` vào phpMyAdmin (~5,000 states)
- [ ] Chạy `location_setup.sql` để thêm cột vào bảng `users`
- [ ] Test API `/ajax/get_countries.php`
- [ ] Test API `/ajax/get_states.php?country_id=240`
- [ ] Test API `/ajax/get_cities.php?state_id=3782`
- [ ] File `cities.json` đã có sẵn (không cần import DB)

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "Table 'countries' doesn't exist"
**Nguyên nhân:** Chưa import `countries.sql`  
**Giải pháp:** Làm lại Bước 1

### Lỗi: "Table 'states' doesn't exist"
**Nguyên nhân:** Chưa import `states.sql`  
**Giải pháp:** Làm lại Bước 2

### Lỗi: "Unknown column 'country_id' in users"
**Nguyên nhân:** Chưa thêm cột location vào bảng users  
**Giải pháp:** Làm lại Bước 3

### API trả về empty array
**Kiểm tra:**
1. Database có data không? → Chạy SQL kiểm tra
2. `country_id` hoặc `state_id` có đúng không?
3. Xem console log trong browser DevTools

### cities.json quá lớn, load chậm
**Giải pháp:**
- API `get_cities.php` đã filter theo `state_id`
- Chỉ load cities của state được chọn
- Khoảng 10-100 cities mỗi state → Nhanh!

---

## 🎯 KẾT QUẢ CUỐI CÙNG

Sau khi hoàn thành, user có thể:
1. Chọn **Country** từ 250 quốc gia
2. Chọn **State/Province** tương ứng với country
3. Chọn **City** tương ứng với state
4. Khi register, dữ liệu sẽ lưu vào:
   - `users.country_id` (INT)
   - `users.state_id` (INT)
   - `users.city_name` (VARCHAR)

**Sẵn sàng tích hợp vào form register!** 🚀


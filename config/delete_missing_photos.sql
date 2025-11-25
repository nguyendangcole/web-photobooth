-- Script SQL để xóa các record photobook_pages không có file tương ứng
-- Chạy script này sau khi đã kiểm tra file không tồn tại

-- Xóa các record có file không tồn tại (dựa trên ID đã kiểm tra)
-- Thay thế các ID sau bằng ID thực tế từ database của bạn

-- Ví dụ: Xóa ID 14 và 15 (file pb_20251113_*.png không tồn tại)
DELETE FROM photobook_pages WHERE id IN (14, 15);

-- Hoặc xóa tất cả record không có album_id (nếu cần)
-- DELETE FROM photobook_pages WHERE album_id IS NULL;

-- Kiểm tra lại sau khi xóa
SELECT id, image_path, created_at FROM photobook_pages ORDER BY id DESC;


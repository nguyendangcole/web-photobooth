-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:8889
-- Thời gian đã tạo: Th10 13, 2025 lúc 10:39 AM
-- Phiên bản máy phục vụ: 8.0.40
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `myapp`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `photobook_pages`
--

CREATE TABLE `photobook_pages` (
  `id` bigint UNSIGNED NOT NULL,
  `album_id` bigint UNSIGNED DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout` enum('square','vertical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'square',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `photobook_pages`
--

INSERT INTO `photobook_pages` (`id`, `album_id`, `image_path`, `layout`, `created_at`) VALUES
(14, NULL, 'public/photobook/2025/11/pb_20251113_102825_27a04648.png', 'square', '2025-11-13 10:28:25'),
(15, NULL, 'public/photobook/2025/11/pb_20251113_102907_3cec0306.png', 'vertical', '2025-11-13 10:29:07');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `photobook_pages`
--
ALTER TABLE `photobook_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pb_created_at` (`created_at`),
  ADD KEY `album_id` (`album_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `photobook_pages`
--
ALTER TABLE `photobook_pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

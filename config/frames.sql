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
-- Cấu trúc bảng cho bảng `frames`
--

CREATE TABLE `frames` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `src` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'square',
  `is_premium` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Premium frame flag'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `frames`
--

INSERT INTO `frames` (`id`, `name`, `src`, `layout`, `is_premium`) VALUES
(1, 'Normal', 'public/images/frame-normal.png', 'vertical', 0),
(2, 'Cat', 'public/images/frame-cat.png', 'vertical', 0),
(3, 'Star', 'public/images/frame-star.png', 'vertical', 0),
(4, 'Crazy(1)', 'public/images/frame-crazy-1.png', 'vertical', 0),
(5, 'Crazy(2)', 'public/images/frame-crazy-2.png', 'vertical', 0),
(6, 'Friends', 'public/images/frame-friends.png', 'vertical', 0),
(7, 'Mybeloved', 'public/images/frame-mybeloved.png', 'vertical', 0),
(8, 'QUANQUE', 'public/images/frame-quanque.png', 'vertical', 0),
(9, 'Longtimenosee', 'public/images/frame-longtimenosee.png', 'vertical', 0),
(10, 'vintage (1)', 'public/images/frame-vintage-1.png', 'vertical', 0),
(11, 'Y2K', 'public/images/frame-y2k.png', 'vertical', 0),
(12, '#Vietnamese', 'public/images/frame-vietnamese-vertical.png', 'vertical', 0),
(13, '#Vietnamese', 'public/images/frame-vietnamese-square.png', 'square', 0),
(14, '#Crazy', 'public/images/frame-crazy-square.png', 'square', 0),
(15, '#1989', 'public/images/frame-1989.png', 'square', 0),
(16, 'Papers', 'public/images/frame-papers.png', 'square', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `frames`
--
ALTER TABLE `frames`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_premium` (`is_premium`),
  ADD KEY `idx_layout_premium` (`layout`,`is_premium`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `frames`
--
ALTER TABLE `frames`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

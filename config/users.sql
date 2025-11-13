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
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `provider` enum('local','google','facebook') NOT NULL DEFAULT 'local',
  `provider_id` varchar(190) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address` varchar(255) DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `state_id` int DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT '0',
  `premium_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `provider`, `provider_id`, `avatar_url`, `email_verified`, `verification_token`, `reset_token`, `reset_expires_at`, `created_at`, `updated_at`, `address`, `country_id`, `state_id`, `city_name`, `is_premium`, `premium_until`) VALUES
(4, 'nguyen', 'nguyenduydang225@gmail.com', '$2y$10$IQ/fzuKyhGf75/S01olUSucZxHtBFq54bwd9bHDX5hP..C/s8NQ0i', 'local', NULL, NULL, 0, NULL, NULL, NULL, '2025-10-09 08:39:54', '2025-10-09 08:39:54', NULL, NULL, NULL, NULL, 0, NULL),
(5, 'nguyencolec', 'nguyen.dangcolece@hcmut.edu.vn', '$2y$10$cEpfrH0sOR3s/.IHIhBtU.UvJvmFn4TDRWNYKi/6ay2YFQijkZ1uC', 'local', NULL, NULL, 0, NULL, NULL, NULL, '2025-10-09 08:55:51', '2025-10-09 08:55:51', NULL, NULL, NULL, NULL, 0, NULL),
(6, 'Nguyên Duy', 'nguyenduydang224@gmail.com', NULL, 'facebook', '1502412914213959', 'https://platform-lookaside.fbsbx.com/platform/profilepic/?asid=1502412914213959&height=200&width=200&ext=1762620392&hash=AT87w-lv2qOlVAadDW6l4GDS', 1, NULL, NULL, NULL, '2025-10-09 23:46:33', '2025-10-09 23:46:33', NULL, NULL, NULL, NULL, 0, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `provider` (`provider`,`provider_id`),
  ADD KEY `fk_country` (`country_id`),
  ADD KEY `fk_state` (`state_id`),
  ADD KEY `idx_is_premium` (`is_premium`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `fk_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

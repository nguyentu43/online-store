-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 05, 2021 at 05:01 PM
-- Server version: 8.0.23-0ubuntu0.20.04.1
-- PHP Version: 7.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eshop`
--

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `slug`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Sony', NULL, 'sony', NULL, '2021-03-05 00:03:44', '2021-03-05 00:03:44'),
(2, 'Samsung', NULL, 'samsung', NULL, '2021-03-05 02:20:23', '2021-03-05 02:20:23');

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2021-03-05 00:02:54', '2021-03-05 00:02:54');

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `enable`, `slug`, `image`, `parent_id`, `created_at`, `updated_at`, `order`) VALUES
(1, 'Điện tử', 1, 'dien-tu', 'images/3ospPN7SkFA4Uvu3cHPGEB0s9henoNdQMYs1WhgV.jpg', NULL, '2021-03-05 00:04:08', '2021-03-05 02:15:15', NULL),
(2, 'Điện lạnh', 1, 'dien-lanh', 'images/qbzrquYY39eaz5ZKy5BFbTb77fL9C0PRdHcLjmfu.jpg', NULL, '2021-03-05 02:15:28', '2021-03-05 02:54:08', NULL),
(3, 'Máy lạnh', 1, 'may-lanh', NULL, 2, '2021-03-05 02:15:30', '2021-03-05 02:15:54', '2'),
(4, 'Tủ lạnh', 1, 'tu-lanh', 'images/atbsO5Mr9R0H0P6djOE636aJFqcgVrxSAvorlbEN.jpg', 2, '2021-03-05 02:21:05', '2021-03-05 02:23:51', '2');

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `product_sku_id`, `value`, `start_datetime`, `end_datetime`, `created_at`, `updated_at`) VALUES
(2, 4, 0.1, '2021-03-02 00:00:00', '2021-04-30 00:00:00', '2021-03-05 02:56:04', '2021-03-05 02:56:04');

--
-- Dumping data for table `login_providers`
--

INSERT INTO `login_providers` (`id`, `name`) VALUES
(1, 'email'),
(2, 'google'),
(3, 'facebook');

--
-- Dumping data for table `login_providers_users`
--

INSERT INTO `login_providers_users` (`id`, `user_id`, `login_provider_id`) VALUES
(1, 1, 1);

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `product_sku_id`, `url`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 'images/0wr0NaQlL27J76ngrIpMTfHTJ1lcl69FV8pqdNeK.jpg', NULL, '2021-03-05 01:59:54', '2021-03-05 01:59:54'),
(5, 2, 'images/5WbAIaPTe63JSgB0BkBT5RzgBKmIGpKHORLODbHo.jpg', NULL, '2021-03-05 02:54:44', '2021-03-05 02:54:44'),
(6, 3, 'images/NLjty2luKUiI1l9Gfb5IzGhde7y6SvbvrS57dFdI.jpg', NULL, '2021-03-05 02:54:53', '2021-03-05 02:54:53'),
(7, 4, 'images/LT1ZCsPNCh0ELAC3156DLxXCCVErFJD8XF5DyiRn.jpg', NULL, '2021-03-05 02:55:25', '2021-03-05 02:55:25');

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(56, '2014_10_12_000000_create_users_table', 1),
(57, '2014_10_12_100000_create_password_resets_table', 1),
(58, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(59, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(60, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(61, '2016_06_01_000004_create_oauth_clients_table', 1),
(62, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(63, '2018_08_30_155243_create_roles_table', 1),
(64, '2018_08_30_160954_create_address_books_table', 1),
(65, '2018_08_30_163659_create_categories_table', 1),
(66, '2018_08_30_164146_create_brands_table', 1),
(67, '2018_08_30_164457_create_product_types_table', 1),
(68, '2018_08_30_164844_create_products_table', 1),
(69, '2018_08_30_170309_create_product_attributes_table', 1),
(70, '2018_08_30_170457_create_product_product_attributes_table', 1),
(71, '2018_08_30_172325_create_product_skus_table', 1),
(72, '2018_08_30_172858_create_media_table', 1),
(73, '2018_08_30_173409_create_carts_table', 1),
(74, '2018_08_30_174618_create_cart_detail_table', 1),
(75, '2018_08_30_174850_create_discounts_table', 1),
(76, '2018_08_30_175404_create_orders_table', 1),
(77, '2018_08_30_175716_create_order_detail_table', 1),
(78, '2018_08_30_180250_create_campaigns_table', 1),
(79, '2018_08_30_180626_create_campaign_product_table', 1),
(80, '2018_08_30_183052_add_name_address_books_table', 1),
(81, '2018_08_30_183413_change_column_cart_table', 1),
(82, '2018_08_30_190044_add_column_slug_campaigns_table', 1),
(83, '2018_08_31_075731_change_column_parent_id_categories_table', 1),
(84, '2018_09_04_191417_create_product_product_types_table', 1),
(85, '2018_09_05_154223_add_cloumn_order_category_table', 1),
(86, '2018_09_05_193653_change_column_value_discount', 1),
(87, '2018_09_07_100429_add_column_name_product_skus_table', 1),
(88, '2018_09_07_170644_add_col_phone_number_orders_table', 1),
(89, '2018_09_07_194518_change_column_amount_orders', 1),
(90, '2018_09_07_203617_change_order_detail', 1),
(91, '2018_09_08_140304_add_column_users_table', 1),
(92, '2018_09_17_174330_change_column_media_table', 1),
(93, '2018_09_19_132714_create_table_provider_login', 1),
(94, '2018_09_19_163442_create_table_login_providers_users_table', 1),
(95, '2018_09_20_041539_change_col_users_table', 1),
(96, '2018_09_20_154242_create_payment_methods_table', 1),
(97, '2018_09_20_154629_create_order_status_table', 1),
(98, '2018_09_20_154659_create_orders_order_status', 1),
(99, '2018_09_20_165908_add_col_orders_table', 1),
(100, '2018_09_23_133945_add_col_filter_attrs_table', 1),
(101, '2018_09_26_154647_create_comments_table', 1),
(102, '2018_10_10_122720_add_col_enable_products', 1),
(103, '2018_10_10_122749_add_col_enable_users', 1),
(104, '2018_10_17_131200_add_column_order_code', 1),
(105, '2018_10_22_141921_add_column_enable', 1),
(106, '2018_10_29_081216_create_roles_users', 1),
(107, '2018_11_05_144725_create_table_rate', 1),
(108, '2018_11_21_171618_create_discount_code_table', 1),
(109, '2018_11_21_173645_add_column_discount_orders_table', 1),
(110, '2019_03_27_145845_create_jobs_table', 1);

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('1b49856ffcd8181ff5c6450063a8752f1d84833df72de87821d24466227fef1caf91777f26480851', 1, 1, 'Eshop', '[]', 0, '2021-03-05 00:03:19', '2021-03-05 00:03:19', '2022-03-05 07:03:19');

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Eshop Personal Access Client', 'BijTiz44zEDLHZyyTJCadIubDVoNQjoOIpCW9WTr', 'http://localhost', 1, 0, 0, '2021-03-05 00:03:15', '2021-03-05 00:03:15'),
(2, NULL, 'Eshop Password Grant Client', 'JoFjuY3TCjLhHMlbc718Waqk8YoVol428tjrTI3A', 'http://localhost', 0, 1, 0, '2021-03-05 00:03:15', '2021-03-05 00:03:15');

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2021-03-05 00:03:15', '2021-03-05 00:03:15');

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `address`, `city`, `amount`, `description`, `created_at`, `updated_at`, `phone`, `charge_id`, `payment_method_id`, `order_code`, `discount`) VALUES
(6, 1, 'example', '123', 'hcm', 6990000, NULL, '2021-03-05 02:46:17', '2021-03-05 02:46:18', '123456', NULL, 2, '6041FDE9F3009', NULL),
(7, 1, 'example', '123', 'hcm', 6990000, NULL, '2021-03-05 02:49:01', '2021-03-05 02:49:01', '123456', NULL, 2, '6041FE8DB4486', NULL),
(8, 1, 'example', '123', 'hcm', 6990000, NULL, '2021-03-05 02:50:53', '2021-03-05 02:50:53', '123456', NULL, 2, '6041FEFD856CF', NULL);

--
-- Dumping data for table `orders_order_status`
--

INSERT INTO `orders_order_status` (`id`, `order_id`, `status_id`, `created_at`, `updated_at`) VALUES
(6, 6, 1, '2021-03-05 02:46:17', '2021-03-05 02:46:17'),
(7, 7, 1, '2021-03-05 02:49:01', '2021-03-05 02:49:01'),
(8, 8, 1, '2021-03-05 02:50:53', '2021-03-05 02:50:53');

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `sku_id`, `quantity`, `price`, `discount`, `created_at`, `updated_at`) VALUES
(6, 6, 1, 1, 6990000, 0, NULL, NULL),
(7, 7, 1, 1, 6990000, 0, NULL, NULL),
(8, 8, 1, 1, 6990000, 0, NULL, NULL);

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Đã tiếp nhận', NULL, NULL),
(2, 'Đang xử lí', NULL, NULL),
(3, 'Đang giao', NULL, NULL),
(4, 'Đã giao', NULL, NULL),
(5, 'Hoàn trả', NULL, NULL),
(6, 'Đã huỷ', NULL, NULL);

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'COD', NULL, NULL),
(2, 'Thanh toán trực tuyến Stripe', NULL, NULL);

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `weight`, `description`, `brand_id`, `product_type_id`, `category_id`, `slug`, `created_at`, `updated_at`, `enable`) VALUES
(1, 'Sony Smart TV', '7 kg', NULL, 1, 1, 1, 'sony-smart-tv', '2021-03-05 00:05:01', '2021-03-05 02:19:47', 1),
(2, 'Android Tivi Sony 4K', '8 kg', NULL, 1, 1, 1, 'android-tivi-sony-4k', '2021-03-05 02:05:15', '2021-03-05 02:12:05', 1),
(3, 'Tủ lạnh Samsung Inverter 236 lít RT22M4032BY/SV', '25 kg', NULL, 2, 2, 4, 'tu-lanh-samsung-inverter-236-lit-rt22m4032by-sv', '2021-03-05 02:21:42', '2021-03-05 02:21:42', 1);

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `name`, `created_at`, `updated_at`, `filterable`) VALUES
(1, 'Hệ điều hành', '2021-03-05 02:16:30', '2021-03-05 02:16:30', 0),
(2, 'Dung tích', '2021-03-05 02:16:57', '2021-03-05 02:16:57', 0),
(3, 'Công suất', '2021-03-05 02:17:05', '2021-03-05 02:17:05', 0);

--
-- Dumping data for table `product_attr_product_types`
--

INSERT INTO `product_attr_product_types` (`id`, `product_type_id`, `product_attribute_id`, `created_at`, `updated_at`) VALUES
(1, 2, 2, NULL, NULL),
(2, 3, 3, NULL, NULL),
(3, 1, 1, NULL, NULL);

--
-- Dumping data for table `product_product_attributes`
--

INSERT INTO `product_product_attributes` (`id`, `product_id`, `product_attribute_id`, `value`, `created_at`, `updated_at`) VALUES
(1, 3, 2, '236 lít', NULL, NULL);

--
-- Dumping data for table `product_skus`
--

INSERT INTO `product_skus` (`id`, `product_id`, `sku`, `quantity`, `price`, `promotion`, `created_at`, `updated_at`, `name`) VALUES
(1, 1, 'SONT000001-40INCH', 93, 6990000, NULL, '2021-03-05 00:05:01', '2021-03-05 02:50:53', '40INCH'),
(2, 2, 'SONT000002-43INCH', 100, 10900000, NULL, '2021-03-05 02:05:15', '2021-03-05 02:13:53', '43INCH'),
(3, 2, 'SONT000002-49INCH', 100, 13700000, NULL, '2021-03-05 02:14:16', '2021-03-05 02:14:16', '49INCH'),
(4, 3, 'SAMTL000003-DEFAULT', 99, 6990000, NULL, '2021-03-05 02:21:42', '2021-03-05 02:31:26', 'DEFAULT');

--
-- Dumping data for table `product_types`
--

INSERT INTO `product_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Tivi', '2021-03-05 00:03:36', '2021-03-05 00:03:36'),
(2, 'Tủ lạnh', '2021-03-05 02:16:03', '2021-03-05 02:16:03'),
(3, 'Máy lạnh', '2021-03-05 02:16:09', '2021-03-05 02:16:09');

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permission`, `created_at`, `updated_at`) VALUES
(1, 'Quản trị', '[\"all.manage\"]', NULL, NULL),
(2, 'Khách hàng', '[\"product.read\", \"comment.read\", \"rate.read\", \"comment.create\", \"rate.create\", \"comment.update.owner\", \"rate.update.owner\", \"comment.delete.owner\", \"rate.delete.owner\", \"order.create\", \"order.update\", \"user.update.owner\"]', NULL, NULL);

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `gender`, `date_of_birth`, `enable`) VALUES
(1, 'admin_eshop', 'eshop@eshop.test', '$2y$10$VYA9d65aDThu5C0RCOYSLOtbF.vDABUVYaBt8KOvdVz01NpaCFnPC', NULL, '2021-03-05 00:02:53', '2021-03-05 00:02:54', NULL, NULL, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

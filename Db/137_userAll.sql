--
-- Table structure for table `usage_log`
--

CREATE TABLE `usage_log` (
  `id` bigint(20) NOT NULL,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usage_month` int(11) NOT NULL,
  `usage_year` year(4) NOT NULL,
  `usage_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `usage_log`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(99) NOT NULL,
  `email` varchar(99) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `password` varchar(99) NOT NULL,
  `address` text NOT NULL,
  `user_type` enum('Member','Admin') NOT NULL,
  `status` enum('1','0') NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `purchase_date` datetime NOT NULL,
  `last_login_at` datetime NOT NULL,
  `activation_code` varchar(20) DEFAULT NULL,
  `expired_date` datetime NOT NULL,
  `bot_status` enum('0','1') NOT NULL DEFAULT '1',
  `package_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `brand_logo` text DEFAULT NULL,
  `brand_url` text DEFAULT NULL,
  `vat_no` varchar(100) DEFAULT NULL,
  `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') NOT NULL DEFAULT 'USD',
  `time_zone` varchar(255) DEFAULT NULL,
  `company_email` varchar(200) DEFAULT NULL,
  `paypal_email` varchar(100) NOT NULL,
  `paypal_subscription_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `last_payment_method` varchar(50) NOT NULL,
  `last_login_ip` varchar(25) NOT NULL,
  `affiliate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `password`, `address`, `user_type`, `status`, `add_date`, `purchase_date`, `last_login_at`, `activation_code`, `expired_date`, `bot_status`, `package_id`, `deleted`, `brand_logo`, `brand_url`, `vat_no`, `currency`, `time_zone`, `company_email`, `paypal_email`, `paypal_subscription_enabled`, `last_payment_method`, `last_login_ip`, `affiliate_id`) VALUES
(1, 'Gaxa', 'admin@limgar.com', '+660118136665', 'e267cfcd18461ce938067eca67c59f41', 'Thailand ', 'Admin', '1', '2019-08-26 01:00:00', '0000-00-00 00:00:00', '2024-08-31 13:11:05', NULL, '0000-00-00 00:00:00', '1', 0, '0', '', NULL, NULL, 'USD', '', NULL, '', '0', '', '184.22.140.15', 0),
(2, 'gennie', 'admin@limgar.com', '', 'afdd0b4ad2ec172c586e2150770fbf9e', '', 'Admin', '0', '2021-03-22 17:24:44', '0000-00-00 00:00:00', '2021-03-27 00:43:23', '156458', '2021-03-27 00:00:00', '0', 1, '0', NULL, NULL, NULL, 'USD', NULL, NULL, '', '0', '', '', 0),
(3, 'gennie', 'limgar786@gmail.com', '0956658248', 'afdd0b4ad2ec172c586e2150770fbf9e', '', 'Admin', '1', '2021-03-22 17:24:44', '0000-00-00 00:00:00', '2024-09-07 00:32:29', '156458', '0000-00-00 00:00:00', '0', 0, '0', NULL, NULL, NULL, 'USD', NULL, NULL, '', '0', '', '184.22.143.238', 0),
(46, 'Thekee', 'thekee236@gmail.com', '', 'afdd0b4ad2ec172c586e2150770fbf9e', 'Thailand-2', 'Admin', '1', '2024-08-31 07:12:14', '0000-00-00 00:00:00', '2024-09-08 00:47:41', '147181', '2024-09-30 00:00:00', '1', 1, '0', '46.png', NULL, NULL, 'USD', 'Asia/Bangkok', NULL, '', '0', '', '184.22.143.238', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_input_custom_fields`
--

CREATE TABLE `user_input_custom_fields` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(20) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `create_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_input_custom_fields_assaign`
--

CREATE TABLE `user_input_custom_fields_assaign` (
  `id` int(11) NOT NULL,
  `subscriber_id` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `custom_field_value` text NOT NULL,
  `assaign_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_info`
--

CREATE TABLE `user_login_info` (
  `id` int(11) NOT NULL,
  `user_id` int(12) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

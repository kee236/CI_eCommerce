CREATE TABLE `wordpress_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wordpress_config_self_hosted`
--

CREATE TABLE `wordpress_config_self_hosted` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(191) NOT NULL,
  `user_key` varchar(191) NOT NULL,
  `authentication_key` varchar(191) NOT NULL,
  `blog_category` text DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wordpress_config_self_hosted`
--

INSERT INTO `wordpress_config_self_hosted` (`id`, `user_id`, `domain_name`, `user_key`, `authentication_key`, `blog_category`, `status`, `deleted`, `updated_at`, `created_at`, `error_message`) VALUES
(1, 6, 'Rtt', 'Tyg', '2c2760ff1b4a475427f7432e82a0b426', NULL, '1', '0', NULL, '2024-09-11 02:09:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wordpress_users_info`
--

CREATE TABLE `wordpress_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blog_id` varchar(200) NOT NULL,
  `blog_url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` text NOT NULL,
  `posts` int(11) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `last_update_time` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


CREATE TABLE `woocommerce_drip_campaign` (
  `id` int(11) NOT NULL,
  `campaign_name` varchar(250) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `message_content_hourly` text NOT NULL,
  `created_at` datetime NOT NULL,
  `last_sent_at` datetime NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `campaign_type` enum('messenger','email','sms') NOT NULL DEFAULT 'messenger',
  `engagement_table_id` int(11) NOT NULL,
  `between_start` varchar(50) NOT NULL DEFAULT '00:00',
  `between_end` varchar(50) NOT NULL DEFAULT '23:59',
  `timezone` varchar(250) NOT NULL,
  `message_tag` varchar(255) NOT NULL,
  `total_unsubscribed` int(11) NOT NULL,
  `last_unsubscribed_at` datetime NOT NULL,
  `external_sequence_sms_api_id` varchar(50) NOT NULL,
  `external_sequence_email_api_id` varchar(50) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_sequence_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Table structure for table `send_email_to_autoresponder_log`
--

CREATE TABLE `send_email_to_autoresponder_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mailchimp_config_id` int(11) NOT NULL,
  `settings_type` varchar(50) NOT NULL COMMENT 'admin settings, member settings',
  `status` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `auto_responder_type` varchar(30) NOT NULL COMMENT 'mailchimp,aweber etc',
  `api_name` varchar(100) NOT NULL,
  `response` text NOT NULL,
  `insert_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `sms_api_config`
--

CREATE TABLE `sms_api_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gateway_name` enum('planet','plivo','twilio','clickatell','clickatell-platform','nexmo','msg91.com','textlocal.in','sms4connect.com','telnor.com','mvaayoo.com','routesms.com','trio-mobile.com','sms40.com','africastalking.com','infobip.com','smsgatewayme','semysms.net','custom','custom_post') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `custom_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `username_auth_id` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password_auth_token` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `routesms_host_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `api_id` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `input_http_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `final_http_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `phone_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `base_url` varchar(255) NOT NULL COMMENT 'for custom post method',
  `post_data` text NOT NULL COMMENT 'post data for custom post method',
  `status` enum('0','1') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_email_contacts`
--

CREATE TABLE `sms_email_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_type_id` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `phone_number` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `notes` text NOT NULL,
  `unsubscribed` enum('0','1') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0',
  `deleted` enum('0','1') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_email_contact_group`
--

CREATE TABLE `sms_email_contact_group` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sms_email_contact_group`
--

INSERT INTO `sms_email_contact_group` (`id`, `user_id`, `type`, `created_at`, `deleted`) VALUES
(1, 1, 'Gaxa Group', '2021-07-17 03:31:29', '0');

-- --------------------------------------------------------

--
-- Table structure for table `sms_sending_campaign`
--

CREATE TABLE `sms_sending_campaign` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `api_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(255) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `label_ids` text NOT NULL,
  `excluded_label_ids` text NOT NULL,
  `label_names` text NOT NULL,
  `user_gender` varchar(20) NOT NULL,
  `user_time_zone` varchar(20) NOT NULL,
  `user_locale` varchar(50) NOT NULL,
  `contact_ids` mediumtext NOT NULL,
  `contact_type_id` mediumtext NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `campaign_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `manual_phone` varchar(255) NOT NULL,
  `posting_status` enum('0','1','2','3') NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` mediumtext NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL,
  `is_try_again` enum('0','1') NOT NULL DEFAULT '1',
  `campaign_delay` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_sending_campaign_send`
--

CREATE TABLE `sms_sending_campaign_send` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sms_api_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `contact_first_name` varchar(255) NOT NULL,
  `contact_last_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone_number` varchar(255) NOT NULL,
  `delivery_id` varchar(250) NOT NULL,
  `sent_time` datetime NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

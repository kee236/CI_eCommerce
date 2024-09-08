--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `module_name` varchar(250) DEFAULT NULL,
  `add_ons_id` int(11) NOT NULL,
  `extra_text` varchar(50) NOT NULL DEFAULT 'month',
  `limit_enabled` enum('0','1') NOT NULL DEFAULT '1',
  `bulk_limit_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES
(33, 'Social Poster - Account Import : Youtube', 0, '', '1', '0', '0'),
(65, 'Facebook Accounts', 0, '', '1', '0', '0'),
(66, 'Facebook Pages - Subscribers/Page', 0, '', '1', '0', '0'),
(78, 'Subscriber Manager : Background Lead Scan', 0, '', '0', '0', '0'),
(79, 'Conversation Promo Broadcast Send', 0, 'month', '1', '1', '0'),
(80, 'Comment Automation : Auto Reply Posts', 0, 'month', '1', '0', '0'),
(82, 'Inbox Conversation Manager', 0, '', '0', '0', '0'),
(100, 'Social Poster - Access', 0, '', '0', '0', '0'),
(101, 'Social Poster - Account Import : Pinterest', 0, '', '1', '0', '0'),
(102, 'Social Poster - Account Import : Twitter', 0, '', '1', '0', '0'),
(103, 'Social Poster - Account Import :  Linkedin', 0, '', '1', '0', '0'),
(105, 'Social Poster - Account Import : Reddit', 0, '', '1', '0', '0'),
(107, 'Social Poster - Account Import : Blogger', 0, '', '1', '0', '0'),
(108, 'Social Poster - Account Import :  WordPress', 0, '', '1', '0', '0'),
(109, 'Social Poster - Account Import :  WordPress (Self hosted) ', 0, '', '1', '0', '0'),
(110, 'Social Poster - Text Post', 0, 'month', '1', '1', '0'),
(111, 'Social Poster - Image Post', 0, 'month', '1', '1', '0'),
(112, 'Social Poster - Video Post', 0, 'month', '1', '1', '0'),
(113, 'Social Poster - Link Post', 0, 'month', '1', '1', '0'),
(114, 'Social Poster - HTML Post', 0, 'month', '1', '1', '0'),
(197, 'Messenger Bot - Persistent Menu', 0, '', '0', '0', '0'),
(198, 'Messenger Bot - Persistent Menu : Copyright Enabled', 0, '', '0', '0', '0'),
(199, 'Messenger Bot', 0, '', '0', '0', '0'),
(200, 'Facebook Pages', 0, '', '1', '0', '0'),
(220, 'Facebook Posting : CTA Post', 0, 'month', '1', '0', '0'),
(222, 'Facebook Posting : Carousel/Slider Post', 0, 'month', '1', '0', '0'),
(223, 'Facebook Posting :  Text/Image/Link/Video Post', 0, 'month', '1', '0', '0'),
(251, 'Comment Automation : Auto Comment Campaign', 0, '', '1', '0', '0'),
(256, 'RSS Auto Posting', 0, '', '1', '0', '0'),
(257, 'Messenger Bot : Export, Import & Tree View', 0, '', '1', '', '0'),
(263, 'Email Broadcast - Email Send', 0, 'month', '1', '0', '0'),
(264, 'SMS Broadcast - SMS Send', 0, 'month', '1', '0', '0'),
(265, 'Messenger Bot - Email Auto Responder', 0, '', '1', '0', '0'),
(267, 'Utility Search Tools', 0, 'month', '1', '0', '0'),
(268, 'Messenger E-commerce', 0, '', '1', '0', '0'),
(275, 'One Time Notification Send', 0, 'month', '1', '0', '0'),
(277, 'Social Poster - Account Import :  Medium', 0, '', '1', '0', '0'),
(279, 'Instagram Auto Comment Reply Enable Post', 0, 'month', '1', '0', '0'),
(296, 'Instagram Posting : Image/Video Post', 0, 'month', '1', '1', '0'),
(315, 'Visual Flow Builder', 0, '', '1', '0', '0'),
(320, 'Instagram Bot', 0, 'month', '1', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `native_api`
--

CREATE TABLE `native_api` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `api_key` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `native_api`
--

INSERT INTO `native_api` (`id`, `user_id`, `api_key`) VALUES
(1, 46, '46-I5m4GOA1725459251r6KgVzx');

-- --------------------------------------------------------

--
-- Table structure for table `otn_optin_subscriber`
--

CREATE TABLE `otn_optin_subscriber` (
  `id` int(11) NOT NULL,
  `otn_id` int(11) NOT NULL,
  `subscriber_id` varchar(255) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `otn_token` varchar(255) NOT NULL,
  `optin_time` datetime NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '0',
  `sent_time` datetime NOT NULL,
  `sent_response` text NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otn_postback`
--

CREATE TABLE `otn_postback` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `otn_postback_id` varchar(191) NOT NULL,
  `reply_postback_id` varchar(255) NOT NULL,
  `label_id` varchar(191) NOT NULL,
  `drip_campaign_id` varchar(191) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `flow_type` enum('general','flow') NOT NULL DEFAULT 'flow',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
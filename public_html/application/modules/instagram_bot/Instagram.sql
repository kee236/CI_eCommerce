CREATE TABLE `instagram_autoreply_report` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `autoreply_table_id` int(11) NOT NULL,
  `post_id` varchar(255) NOT NULL,
  `reply_type` enum('post','full','mention') NOT NULL DEFAULT 'post',
  `comment_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_text` text NOT NULL,
  `commenter_name` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `commenter_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_time` datetime NOT NULL,
  `reply_time` datetime NOT NULL,
  `comment_reply_text` text NOT NULL,
  `reply_status_comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `reply_status` text NOT NULL,
  `post_url` text NOT NULL,
  `media_url` text NOT NULL,
  `media_type` varchar(50) NOT NULL,
  `is_deleted` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  `is_hidden` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  `error_message` text NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `auto_private_reply_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instagram_reply_autoreply`
--

CREATE TABLE `instagram_reply_autoreply` (
  `id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `autoreply_type` enum('post_autoreply','account_autoreply','mentions_autoreply') NOT NULL DEFAULT 'post_autoreply',
  `post_pause_play` enum('play','pause') NOT NULL DEFAULT 'play',
  `full_pause_play` enum('play','pause') NOT NULL,
  `mentions_pause_play` enum('play','pause') NOT NULL DEFAULT 'play',
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_url` text NOT NULL,
  `media_url` text NOT NULL,
  `media_type` varchar(50) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` longtext DEFAULT NULL,
  `reply_type` varchar(200) NOT NULL,
  `report_type` enum('full','mention','post') NOT NULL DEFAULT 'post',
  `multiple_reply` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `trigger_matching_type` enum('exact','string') NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instagram_reply_template`
--

CREATE TABLE `instagram_reply_template` (
  `id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `ig_username` varchar(200) NOT NULL,
  `trigger_matching_type` enum('exact','string') NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


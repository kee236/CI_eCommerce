--
-- Table structure for table `page_response_autoreply`
--

CREATE TABLE `page_response_autoreply` (
  `id` int(11) NOT NULL,
  `page_response_user_info_id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` mediumtext DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` longtext DEFAULT NULL,
  `reply_type` varchar(200) NOT NULL,
  `auto_like_comment` enum('no','yes') NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `comment_reply_enabled` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `auto_private_reply_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `auto_private_reply_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` mediumtext NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `pause_play` enum('play','pause') NOT NULL,
  `structured_message` enum('no','yes') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_response_auto_like_report`
--

CREATE TABLE `page_response_auto_like_report` (
  `id` int(11) NOT NULL,
  `page_response_auto_like_share_report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auto_like_page_table_id` text NOT NULL,
  `status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_response_auto_like_share`
--

CREATE TABLE `page_response_auto_like_share` (
  `id` int(11) NOT NULL,
  `page_response_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text DEFAULT NULL,
  `page_id` varchar(200) NOT NULL,
  `auto_share_post` enum('0','1') NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` text NOT NULL,
  `delay_time` varchar(20) NOT NULL,
  `auto_like_post` enum('0','1') NOT NULL DEFAULT '0',
  `auto_like_this_post_by_pages` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_response_auto_like_share_report`
--

CREATE TABLE `page_response_auto_like_share_report` (
  `id` int(11) NOT NULL,
  `page_response_auto_like_share_id` int(11) NOT NULL,
  `page_response_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text DEFAULT NULL,
  `page_id` varchar(200) NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `auto_share_post` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes, 2 = processing, 3=completed',
  `share_count` int(11) NOT NULL,
  `share_done` int(11) NOT NULL,
  `share_last_tried` datetime NOT NULL,
  `auto_share_this_post_by_pages` text NOT NULL,
  `auto_share_report` longtext DEFAULT NULL,
  `delay_time` varchar(20) NOT NULL,
  `auto_like_post` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes, 2 = processing, 3=completed',
  `like_count` int(11) NOT NULL,
  `like_done` int(11) NOT NULL,
  `like_last_tried` datetime NOT NULL,
  `auto_like_this_post_by_pages` text NOT NULL,
  `auto_like_report` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_response_auto_share_report`
--

CREATE TABLE `page_response_auto_share_report` (
  `id` int(11) NOT NULL,
  `page_response_auto_like_share_report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auto_share_page_table_id` text NOT NULL,
  `status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_response_report`
--

CREATE TABLE `page_response_report` (
  `id` int(11) NOT NULL,
  `page_response_autoreply_id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` longtext DEFAULT NULL,
  `reply_type` varchar(200) NOT NULL,
  `auto_like_comment` enum('no','yes') NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `comment_reply_enabled` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `auto_private_reply_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `auto_private_reply_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `already_replied_comment_ids` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
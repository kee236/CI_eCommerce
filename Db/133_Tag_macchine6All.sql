--
-- Table structure for table `tag_machine_bulk_reply`
--

CREATE TABLE `tag_machine_bulk_reply` (
  `id` int(11) NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `reply_content` text NOT NULL,
  `uploaded_image_video` varchar(255) NOT NULL,
  `reply_multiple` enum('0','1') DEFAULT '0',
  `report` longtext NOT NULL,
  `campaign_created` datetime NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `delay_time` int(11) NOT NULL,
  `is_try_again` enum('0','1') NOT NULL DEFAULT '1',
  `total_reply` int(11) NOT NULL,
  `schedule_type` enum('now','later') NOT NULL DEFAULT 'now',
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(255) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `last_try_error_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `page_profile` text NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` text DEFAULT NULL,
  `error_message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_machine_bulk_reply_send`
--

CREATE TABLE `tag_machine_bulk_reply_send` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL COMMENT 'tag_machine_bulk_reply.id',
  `comment_id` varchar(255) NOT NULL,
  `commenter_fb_id` varchar(255) NOT NULL,
  `commenter_name` varchar(255) NOT NULL,
  `comment_time` datetime NOT NULL,
  `sent_time` datetime NOT NULL,
  `response` varchar(255) NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_machine_bulk_tag`
--

CREATE TABLE `tag_machine_bulk_tag` (
  `id` int(11) NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `tag_database` longtext NOT NULL,
  `tag_exclude` longtext NOT NULL,
  `tag_content` text NOT NULL,
  `uploaded_image_video` varchar(255) NOT NULL,
  `error_message` text NOT NULL,
  `tag_response` text NOT NULL,
  `schedule_type` enum('now','later') NOT NULL DEFAULT 'now',
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(255) NOT NULL,
  `campaign_created` datetime NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `last_updated_at` datetime NOT NULL,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `page_profile` text NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` text DEFAULT NULL,
  `commenter_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_machine_commenter_info`
--

CREATE TABLE `tag_machine_commenter_info` (
  `id` int(11) NOT NULL,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `last_comment_id` varchar(255) NOT NULL,
  `last_comment_time` varchar(255) NOT NULL,
  `commenter_fb_id` varchar(255) NOT NULL,
  `commenter_name` varchar(255) NOT NULL,
  `subscribed` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_machine_comment_info`
--

CREATE TABLE `tag_machine_comment_info` (
  `id` int(11) NOT NULL,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `comment_id` varchar(255) NOT NULL,
  `comment_text` text NOT NULL,
  `commenter_fb_id` varchar(255) NOT NULL,
  `commenter_name` varchar(255) NOT NULL,
  `comment_time` varchar(255) NOT NULL,
  `subscribed` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_machine_enabled_post_list`
--

CREATE TABLE `tag_machine_enabled_post_list` (
  `id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `page_profile` text NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` text DEFAULT NULL,
  `last_updated_at` datetime NOT NULL,
  `commenter_count` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

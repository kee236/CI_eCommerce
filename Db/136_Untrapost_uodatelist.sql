--
-- Table structure for table `ultrapost_auto_reply` คอมเม้นซ่อนเปิดปิด
--

CREATE TABLE `ultrapost_auto_reply` (
  `id` int(11) NOT NULL,
  `ultrapost_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) NOT NULL,
  `auto_like_comment` enum('no','yes') NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `comment_reply_enabled` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext NOT NULL,
  `page_ids` varchar(255) NOT NULL,
  `structured_message` enum('no','yes') NOT NULL DEFAULT 'no',
  `trigger_matching_type` enum('exact','string') NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  `page_name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `update_list`
--

CREATE TABLE `update_list` (
  `id` int(11) NOT NULL,
  `files` text NOT NULL,
  `sql_query` text NOT NULL,
  `update_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

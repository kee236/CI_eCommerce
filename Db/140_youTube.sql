--
-- Table structure for table `youtube_channel_info`
--

CREATE TABLE `youtube_channel_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `channel_id` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `last_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_channel_list`
--

CREATE TABLE `youtube_channel_list` (
  `id` int(11) NOT NULL,
  `channel_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `channel_id` varchar(200) NOT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `profile_image` text DEFAULT NULL,
  `cover_image` text DEFAULT NULL,
  `view_count` varchar(250) DEFAULT NULL,
  `video_count` varchar(250) DEFAULT NULL,
  `subscriber_count` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_video_list`
--

CREATE TABLE `youtube_video_list` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `channel_id` varchar(200) DEFAULT NULL,
  `video_id` varchar(200) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `image_link` text DEFAULT NULL,
  `publish_time` varchar(200) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `defaultLanguage` varchar(255) NOT NULL,
  `privacyStatus` varchar(255) DEFAULT NULL,
  `localizations` text DEFAULT NULL,
  `liveBroadcastContent` varchar(250) DEFAULT NULL,
  `duration` varchar(250) DEFAULT NULL,
  `dimension` varchar(200) DEFAULT NULL,
  `definition` varchar(200) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `licensedContent` text DEFAULT NULL,
  `projection` varchar(250) DEFAULT NULL,
  `viewCount` int(11) DEFAULT NULL,
  `likeCount` int(11) DEFAULT NULL,
  `dislikeCount` int(11) DEFAULT NULL,
  `favoriteCount` int(11) DEFAULT NULL,
  `commentCount` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `backlink_status` enum('0','2','1') NOT NULL DEFAULT '0' COMMENT '0 = incomplete, 2 = submitted, 1 = completed',
  `rank_status` enum('0','1') NOT NULL DEFAULT '0',
  `backlink_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
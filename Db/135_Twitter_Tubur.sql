-- --------------------------------------------------------

--
-- Table structure for table `tumblr_config`
--

CREATE TABLE `tumblr_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `consumer_id` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_config`
--

CREATE TABLE `twitter_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `consumer_id` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_users_info`
--

CREATE TABLE `twitter_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `oauth_token` varchar(200) NOT NULL,
  `oauth_token_secret` varchar(200) NOT NULL,
  `screen_name` varchar(200) NOT NULL,
  `twitter_user_id` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `profile_image` text NOT NULL,
  `followers` int(11) NOT NULL,
  `add_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

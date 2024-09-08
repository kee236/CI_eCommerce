--
-- Table structure for table `pinterest_board_info`
--

CREATE TABLE `pinterest_board_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pinterest_table_id` int(11) NOT NULL COMMENT 'rx_pinterest_autopost table id',
  `board_name` varchar(255) NOT NULL,
  `board_url` varchar(255) NOT NULL,
  `board_id` varchar(25) NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pinterest_config`
--

CREATE TABLE `pinterest_config` (
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
-- Table structure for table `pinterest_users_info`
--

CREATE TABLE `pinterest_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pinterest_user_id` varchar(30) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pins` int(11) NOT NULL,
  `boards` int(11) NOT NULL,
  `image` text NOT NULL,
  `code` varchar(255) NOT NULL,
  `add_date` date NOT NULL,
  `pinterest_config_table_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reddit_config`
--

CREATE TABLE `reddit_config` (
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
-- Table structure for table `reddit_users_info`
--

CREATE TABLE `reddit_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` varchar(250) NOT NULL,
  `token_type` varchar(200) NOT NULL,
  `username` varchar(200) DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `add_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;